<?php
/**
 * WC_PRL_Amplifier_Generator class
 *
 * @package  Woo Product Recommendations
 * @since    4.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A singleton controller for handling the amplifiers' cache tasks.
 *
 * @class    WC_PRL_Amplifier_Generator
 * @version  4.1.0
 */
final class WC_PRL_Amplifier_Generator {

	/**
	 * The instance.
	 *
	 * @var WC_PRL_Amplifier_Generator
	 */
	private static $instance;

	/**
	 * The generator instance.
	 *
	 * @var WC_PRL_Amplifier_Generator_Queue
	 */
	private $queue;

	/**
	 * Supported callibration types.
	 *
	 * @var array
	 */
	const CALIBRATION_TYPES = array(
		'default', // Used in engines (backward compatibility.)
		'low',
		'medium',
		'high',
	);

	/**
	 * Default callibration type.
	 *
	 * @var string
	 */
	const DEFAULT_CALIBRATION_TYPE = 'default';

	/**
	 * Constructor.
	 */
	protected function __construct() {
		$this->queue = new WC_PRL_Amplifier_Generator_Queue();
	}

	/**
	 * Get the instance.
	 *
	 * @return WC_PRL_Amplifier_Generator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.1.0' );
	}

	/**
	 * Schedule a task.
	 *
	 * @param  int    $product_id Product ID.
	 * @param  string $name       Amplifier ID.
	 * @param  string $type       Calibration Type. (Optional)
	 * @return bool  True if the task is in queue.
	 */
	public function schedule_task( $product_id, $name, $type = 'default' ) {

		// Validate type.
		if ( ! in_array( $type, self::CALIBRATION_TYPES, true ) ) {
			$type = self::DEFAULT_CALIBRATION_TYPE;
		}

		$task_data = array(
			'id'   => $product_id,
			'name' => $name,
			'type' => $type,
		);

		if ( $this->queue->is_task_in_queue( $task_data ) ) {
			return true;
		}

		$this->queue->add( $task_data );
		return (bool) $this->queue->save();
	}

	/**
	 * Check if the cache products have expired.
	 *
	 * @since 4.1.0
	 *
	 * @param  int                     $product_id
	 * @param  string|WC_PRL_Amplifier $amplifier
	 * @param  string                  $type Calibration Type. (Optional)
	 * @return bool   Whether cached products are outdated. false if there are no cached products or the cache is still valid.
	 */
	public function has_expired_cache_products( $product_id, $amplifier, $type = 'default' ) {

		if ( ! $amplifier instanceof WC_PRL_Amplifier ) {
			$amplifier = WC_PRL()->amplifiers->get_amplifier( $amplifier );
			if ( ! $amplifier instanceof WC_PRL_Amplifier ) {
				return false;
			}
		}

		$products = $amplifier->get_cached_products( $product_id, $type );
		if ( empty( $products ) ) {
			return false;
		}

		/**
		 * Determine whether or not to use the cache.
		 *
		 * 'woocommerce_prl_amplifier_cache_regeneration_seconds' filter. This defaults to the value of 'Cache regeneration period (hours)' in Settings > Recommendations.
		 *
		 * @since 4.1.0
		 *
		 * @param  int     $interval_in_seconds
		 * @return int     $product_id
		 * @return string  $amplifier_id
		 * @return string  $type
		 */
		$refresh_interval = (int) apply_filters( 'woocommerce_prl_amplifier_cache_regeneration_seconds', wc_prl_get_cache_regeneration_threshold(), $product_id, $amplifier->get_id(), $type );
		$created_at       = (int) $products['created_at'];
		if ( $created_at + $refresh_interval < time() ) {
			return true;
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Amplifiers results generation methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Generates amplifier cache per product.
	 *
	 * Notes:
	 * - It runs recurring until all steps are completed.
	 * - Each time the return value is passed to the next step.
	 *
	 * @see WC_PRL_Amplifier_Generator_Queue::task()
	 *
	 * @param  array $args  {
	 *    @type int    $id                    Product ID.
	 *    @type string $name                  Amplifier ID.
	 *    @type string $type                  Calibration Type. (Optional)
	 *    @type int    $step                  Step number. (Optional)
	 *    @type array  $carried_return_values Carried return values. (Internal use only)
	 * }
	 * @param bool  $force Force run even if cache is available (Optional)
	 * @return mixed
	 */
	public function run( $args, $force = false ) {

		//
		// Setup.
		//
		$product_id = isset( $args['id'] ) ? absint( $args['id'] ) : 0;
		if ( ! $product_id ) {
			// Nothing to do.
			return false;
		}
		$name      = ! empty( $args['name'] ) ? $args['name'] : '';
		$amplifier = WC_PRL()->amplifiers->get_amplifier( $name );
		if ( ! $amplifier instanceof WC_PRL_Amplifier || $amplifier->get_steps_count() < 2 ) {
			// Nothing to do.
			return false;
		}
		$type = ! empty( $args['type'] ) && in_array( $args['type'], self::CALIBRATION_TYPES, true ) ? $args['type'] : 'default';
		// Careful, the first step is 1, not 0.
		$step      = isset( $args['step'] ) ? absint( $args['step'] ) : 1;
		$max_steps = $amplifier->get_steps_count();
		if ( $step > $max_steps ) {
			// Nothing to do.
			return false;
		}

		// Keep return values of each step on a zero-indexed array.
		$args['step_results'] = ! empty( $args['step_results'] ) ? $args['step_results'] : array();

		//
		// Run step.
		//
		$source_args = array(
			'product_id'       => $product_id,
			'calibration_type' => $type,
		);
		$step_result = $amplifier->run_step( $step, $source_args, $args['step_results'] );

		if ( ! is_null( $step_result ) ) {
			$args['step_results'][ $step ] = $step_result;
			$args['step']                  = $step + 1;

			if ( $args['step'] <= $max_steps ) {

				//
				// Run next step.
				//
				return $args;
			}
		}

		// Hint: null return during any step means empty results.
		$is_finished = count( $args['step_results'] ) === $max_steps;
		if ( is_null( $step_result ) || $is_finished ) {

			//
			// Save results.
			//
			$cached_products = $is_finished ? (array) $args['step_results'][ $max_steps ] : array();
			$amplifier->save_products( $product_id, $cached_products, $type );
		}

		//
		// Terminate.
		//
		return false;
	}
}
