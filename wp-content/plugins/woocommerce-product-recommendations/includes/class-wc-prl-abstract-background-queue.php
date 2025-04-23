<?php
/**
 * WC_PRL_Background_Queue class
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once WC_ABSPATH . 'includes/libraries/wp-async-request.php';
}

/**
 * Generator Queue class.
 *
 * @class    WC_PRL_Background_Queue
 * @version  4.1.3
 */
abstract class WC_PRL_Background_Queue extends WP_Async_Request {

	/**
	 * Action
	 *
	 * @var string
	 */
	protected $action = '';

	/**
	 * Start time of current process.
	 *
	 * (default value: 0)
	 *
	 * @var int
	 * @access protected
	 */
	protected $start_time = 0;

	/**
	 * How many seconds will the method is_running will return true counting from the start.
	 * Throttle queue processing every 50 seconds (in seconds.)
	 *
	 * The repeating interval has been set to 1 minute, thus a lower number would be preferable.
	 *
	 * @var int
	 */
	protected $queue_lock_time = 50;

	/**
	 * Property to set when dispatching a single queue item.
	 *
	 * @var string
	 */
	protected $run_for_key;

	/**
	 * Max size of the queue. After that number, the system no longer saves the task request.
	 *
	 * @var int
	 */
	protected $max_queue_allowed;

	/**
	 * Queue db table name
	 *
	 * @var int
	 */
	protected $queue_table_name = 'woocommerce_prl_generator_queue';

	/**
	 * Generate a key for the queue items.
	 *
	 * @var string
	 */
	abstract public function generate_key( $data );

	/**
	 * Run step.
	 *
	 * This is a recurring task that will be called by the action scheduler. It will be called on repeat until it returns false.
	 *
	 * @param array $data Data.
	 *
	 * @return mixed
	 */
	abstract protected function run_step( $data );

	/**
	 * Get job data.
	 *
	 * Parses the queue's runtime $data property into a format that the background job can accept in the run_step method.
	 *
	 * @return array|false The job data. Return array should contain the 'id' key to used as the source ID of each queue item. It can be a Product ID or a Deployment ID.
	 */
	abstract protected function get_job_data( $data );

	/**
	 * Initiate new background process
	 */
	public function __construct() {

		// Uses unique prefix per blog so each blog has its own queue.
		$this->prefix = 'wp_' . get_current_blog_id();

		parent::__construct();

		// Determine queue.
		$max_queue_allowed     = 20;
		$is_handled_via_wp_cli = $this->is_handled_via_wp_cli();
		if ( $is_handled_via_wp_cli ) {
			$max_queue_allowed = 2000;
		}

		/**
		 * Use this filter to change the maximum number of queue items allowed.
		 *
		 * @since 4.0.0
		 *
		 * @param int $max_queue_allowed The maximum number of queue items allowed.
		 * @param bool $is_handled_via_wp_cli Whether the queue is handled via WP-CLI.
		 * @return int
		 */
		$this->max_queue_allowed = (int) apply_filters( $this->identifier . '_queue_max_size', $max_queue_allowed, $is_handled_via_wp_cli );
	}

	/**
	 * Is queue processing handled via wp-cli.
	 */
	public function is_handled_via_wp_cli() {
		if ( WC_PRL_AS_Generator::is_handled_via_wp_cli() ) {
			return true;
		}

		$is_via_wp_cli_local = (bool) apply_filters( $this->identifier . '_queue_via_wp_cli', false );
		if ( $is_via_wp_cli_local ) {
			return true;
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Cron Management.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Handle Action Scheduler recurring request.
	 *
	 * Acts as a cron job.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function handle_AS_request() {
		$is_wp_cli = defined( 'WP_CLI' ) && WP_CLI;

		if ( $this->is_process_running() ) {
			// Background process already running.
			if ( $is_wp_cli ) {
				WP_CLI::success( 'Process already running. Exiting.' );
			}
			$this->maybe_wp_die();
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			if ( $is_wp_cli ) {
				WP_CLI::success( 'Queue is empty.' );
			}
			$this->maybe_wp_die();
		}

		$handle_args = array(
			/**
			 * The maximum number of iterations to run.
			 * If set to 0, the queue will run until it's empty.
			 *
			 * @since 4.1.3
			 *
			 * @param int $max_iterations The maximum number of iterations to run.
			 * @return int
			 */
			'max_iterations' => (int) apply_filters( 'wc_prl_as_max_iterations_per_task', 100 ),
			'avoid_dispatch' => true,
		);

		$this->handle( null, $handle_args );
		$this->maybe_wp_die();
	}

	/*
	|--------------------------------------------------------------------------
	| Queue management.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get number of queue items.
	 *
	 * @param array $args
	 * @return int
	 */
	public function count( $args = array() ) {
		if ( ! is_array( $args ) ) {
			$args = array();
		}

		$count = $this->query(
			array(
				'count' => true,
			)
		);

		return $count;
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	public function is_queue_empty() {
		$count = $this->query(
			array(
				'count' => true,
				'stale' => false,
			)
		);

		return ! ( $count > 0 );
	}

	/**
	 * Checks if the queue is full.
	 *
	 * @return bool
	 */
	public function is_queue_full() {
		$count = $this->query(
			array(
				'count' => true,
				'stale' => false,
			)
		);

		return $count > $this->max_queue_allowed;
	}

	/**
	 * Does the task exist the queue?
	 *
	 * @param  array $data
	 * @return boolean
	 */
	public function is_task_in_queue( $data ) {
		$key = $this->generate_key( $this->get_job_data( $data ) );
		return WC_PRL()->db->queue->query(
			array(
				'key'   => $key,
				'count' => true,
			)
		) > 0;
	}

	/**
	 * Query queue items.
	 *
	 * @param array $args
	 * @return array
	 */
	public function query( $args = array() ) {
		$args['key'] = $this->identifier . '_item_%';
		return WC_PRL()->db->queue->query( $args );
	}

	/**
	 * Get item.
	 *
	 * @param string $key The key of the queue item to get.
	 * @return array Return an array of stdClass objects from the queue
	 */
	protected function get_item( $key ) {
		$results = WC_PRL()->db->queue->query(
			array(
				'key'   => $key,
				'stale' => false,
			)
		);

		if ( empty( $results ) ) {
			return false;
		}

		$row              = $results[0];
		$item             = new stdClass();
		$item->key        = $row['item_key'];
		$item->source_id  = absint( $row['deployment_id'] );
		$item->data       = maybe_unserialize( $row['data'] );
		$item->iterations = absint( $row['iterations'] );
		return $item;
	}

	/**
	 * Get batch
	 *
	 * Hint: The "deployment_id" column is used as a source ID. It's here for backward compatibility.
	 *
	 * @param string $key_prefix The key prefix to use when fetching the next item.
	 * @return array Return an array of stdClass objects from the queue
	 */
	protected function get_next_item( $key_prefix = '' ) {
		$key     = ! empty( $key_prefix ) ? $key_prefix . '%' : $this->identifier . '_item_%';
		$results = WC_PRL()->db->queue->query(
			array(
				'key'   => $key,
				'stale' => false,
			)
		);

		if ( empty( $results ) ) {
			return false;
		}

		$row              = $results[0];
		$item             = new stdClass();
		$item->key        = $row['item_key'];
		$item->source_id  = absint( $row['deployment_id'] );
		$item->data       = maybe_unserialize( $row['data'] );
		$item->iterations = absint( $row['iterations'] );
		return $item;
	}

	/**
	 * Saves local data to queue.
	 *
	 * @return int|bool The number of items saved or false if no data.
	 */
	public function save() {

		if ( empty( $this->data ) || $this->is_queue_full() ) {
			// Drop requests if limits are reached.
			return;
		}

		$args = array();
		foreach ( $this->data as $data ) {

			// Validate.
			if ( false === $this->get_job_data( $data ) ) {
				continue;
			}

			// Generate task key.
			$key = $this->generate_key( $data );
			// Decorate data with item key.
			$job_data = array_merge( $data, array( 'item_key' => $key ) );
			// Prepare data store arguments.
			$args[] = array(
				'key'       => $key,
				'source_id' => absint( $data['id'] ),
				'data'      => $job_data,
			);
		}

		if ( ! empty( $args ) ) {
			return WC_PRL()->db->queue->add( $args );
		}

		return false;
	}

	/**
	 * Add to queue.
	 *
	 * @param mixed $data Data.
	 *
	 * @return string|false The key to be added. False if data is invalid.
	 */
	public function add( $data ) {
		$data = $this->get_job_data( $data );
		if ( false === $data ) {
			return false;
		}

		$this->data[] = $data;
		return $this->generate_key( $data );
	}


	/**
	 * Update queue item.
	 *
	 * @param string $key Key.
	 * @param array  $data Data.
	 *
	 * @return $this
	 */
	public function update( $key, $data ) {
		WC_PRL()->db->queue->update( $key, $data );
		return $this;
	}

	/**
	 * Delete queue item.
	 *
	 * @param string $key Key.
	 *
	 * @return $this
	 */
	public function delete( $key ) {
		WC_PRL()->db->queue->delete( $key );
		return $this;
	}

	/**
	 * Increment number of execution iterations.
	 *
	 * In order to prevent the loss of this number, we have implemented a mandatory update of the attempts at the beginning. Loss may occur due to script timeouts or memory overflow.
	 *
	 * @param string $key Key.
	 *
	 * @return $this
	 */
	public function increment_iterations( $key ) {
		WC_PRL()->db->queue->increment_iterations( $key );
		return $this;
	}

	/**
	 * Force set a number of iterations in a queue item.
	 *
	 * @param string $key Key.
	 * @param int    $number The number of iterations to set.
	 *
	 * @return $this
	 */
	public function set_number_of_iterations( $key, $number ) {
		WC_PRL()->db->queue->set_number_of_iterations( $key, $number );
		return $this;
	}

	/**
	 * Get the maximum number of iterations allowed before dropping.
	 *
	 * @param string $key Key.
	 *
	 * @return $this
	 */
	public function get_max_iterations_per_item() {
		return (int) apply_filters( $this->identifier . 'max_iterations_per_item', 10 );
	}

	/*
	|--------------------------------------------------------------------------
	| Handling the queue.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Maybe process queue. This is the WP_Async_Request AJAX's action callback.
	 *
	 * Checks whether data exists within the queue and that
	 * the process is not already running.
	 */
	public function maybe_handle() {
		// Don't lock up other requests while processing
		session_write_close();

		if ( $this->is_process_running() ) {
			// Background process already running.
			$this->maybe_wp_die();
		}

		if ( $this->is_queue_empty() && ! isset( $_GET['item_key'] ) ) {
			// No data to process.
			$this->maybe_wp_die();
		}

		check_ajax_referer( $this->identifier, 'nonce' );
		// Check for single dispatch.
		$key = null;
		if ( isset( $_GET['item_key'] ) ) {
			$key = wc_clean( wp_unslash( $_GET['item_key'] ) );
		}

		$this->handle( $key, array( 'max_iterations' => is_null( $key ) ? 0 : $this->get_max_iterations_per_item() ) );
		$this->maybe_wp_die();
	}

	/**
	 * Handle
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 *
	 * @since 4.0.0 Added the $args parameter.
	 *
	 * @param string   $key The key from the queue item to dispatch.
	 * @param array  {
	 *     @type string $key_prefix The key prefix to use when fetching the next item.
	 *     @type int $max_iterations The maximum number of iterations to run.
	 *     @type int $cli_max_iterations The maximum number of iterations to run in CLI mode.
	 *     @type bool $is_wp_cli Whether the queue is handled via WP-CLI.
	 *     @type bool $avoid_dispatch Whether to avoid dispatching the next batch async.
	 * } $args Additional arguments.
	 * @return void
	 */
	public function handle( $key = null, $args = array() ) {

		$args      = wp_parse_args(
			$args,
			array(
				'key_prefix'         => $this->identifier . '_item_',
				'max_iterations'     => 0,
				'cli_max_iterations' => 0,
				'is_wp_cli'          => defined( 'WP_CLI' ) && WP_CLI,
				'avoid_dispatch'     => false,
			)
		);
		$is_wp_cli = (bool) $args['is_wp_cli'];

		if ( is_null( $key ) && $this->is_queue_empty() ) {
			if ( $is_wp_cli ) {
				WP_CLI::success( 'Queue is empty.' );
			}

			return;
		}

		$this->lock_process();

		$count      = 0;
		$is_stopped = false;
		do {
			$processing_item = false;
			$item            = is_null( $key ) ? $this->get_next_item( $args['key_prefix'] ) : $this->get_item( $key );
			if ( false === $item ) {
				$processing_item = false;
				break;
			}

			$results = $this->run_step( $item->data );
			if ( false !== $results ) {
				$processing_item = true;
				$this->update( $item->key, $results );
			} else {
				$this->delete( $item->key );
			}

			$count = $count + 1;
			if ( $is_wp_cli && ( $args['cli_max_iterations'] > 0 && $count >= $args['cli_max_iterations'] ) ) {
				WP_CLI::log( 'Max iterations reached. Exiting.' );
				break;
			}
			if ( ! $is_wp_cli && $args['max_iterations'] > 0 && $count >= $args['max_iterations'] ) {
				$is_stopped = true;
				break;
			}
		} while ( ( $is_wp_cli || ( ! $this->time_exceeded() && ! $this->memory_exceeded() ) ) && ( ( ! is_null( $key ) && $processing_item ) || ! $this->is_queue_empty() ) );

		$this->unlock_process();

		if ( $is_wp_cli ) {
			WP_CLI::success( 'Queue processed.' );
		}

		// Start next batch or complete process.
		if ( ! $args['avoid_dispatch'] && is_null( $key ) && ! $is_wp_cli && ! $is_stopped && ! $this->is_queue_empty() ) {
			// Continue processing.
			$this->dispatch();
		} elseif ( ! is_null( $key ) && $processing_item && ! $is_wp_cli ) {
			$this->dispatch_single_item( $key );
		}

		$this->maybe_wp_die();
	}

	/**
	 * Dispatch for specific key.
	 *
	 * @param string $key The key from the queue item to dispatch.
	 * @return bool
	 */
	public function dispatch_single_item( $key ) {
		$this->run_for_key = $key;
		parent::dispatch();
		// Reset single mark.
		$this->run_for_key = null;
	}

	/**
	 * Get query args
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$args = parent::get_query_args();
		if ( ! empty( $this->run_for_key ) ) {
			$args['item_key'] = $this->run_for_key;
		}

		return $args;
	}

	/*
	|--------------------------------------------------------------------------
	| Process locking utilities.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	public function is_process_running() {
		if ( get_site_transient( $this->identifier . '_process_lock' ) ) {
			// Process already running.
			return true;
		}

		return false;
	}

	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 */
	protected function lock_process() {
		$this->start_time = time(); // Set start time of current process.

		$lock_duration = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : 60; // 1 minute
		$lock_duration = apply_filters( $this->identifier . '_queue_lock_time', $lock_duration );

		set_site_transient( $this->identifier . '_process_lock', microtime(), $lock_duration );
	}

	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @return $this
	 */
	protected function unlock_process() {
		delete_site_transient( $this->identifier . '_process_lock' );
		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Keeping execution limits.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_memory_exceeded', $return );
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || '-1' === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + apply_filters( $this->identifier . '_default_time_limit', 20 ); // 20 seconds
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( $this->identifier . '_time_exceeded', $return );
	}

	/**
	 * Should the process exit with wp_die?
	 *
	 * @since 3.0.4
	 *
	 * @param mixed $return What to return if filter says don't die, default is null.
	 *
	 * @return void|mixed
	 */
	protected function maybe_wp_die( $return = null ) {

		/**
		 * Should wp_die be used?
		 *
		 * @return bool
		 */
		if ( apply_filters( $this->identifier . '_wp_die', false ) ) {
			wp_die();
		}

		return $return;
	}

	/**
	 * Handle cron healthcheck
	 *
	 * @deprecated 4.0.0
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		_deprecated_function( __METHOD__, '4.0.0', 'WC_PRL_Generator_Queue::handle_AS_request()' );
	}

	/**
	 * Schedule cron healthcheck
	 *
	 * @deprecated
	 *
	 * @param mixed $schedules Schedules.
	 * @return mixed
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		_deprecated_function( __METHOD__, '4.0.0' );
	}
}
