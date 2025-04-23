<?php
/**
 * WC_PRL_Amplifier_Generator_Queue class
 *
 * @package  Woo Product Recommendations
 * @since    3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
	include_once WC_ABSPATH . 'includes/libraries/wp-async-request.php';
}

/**
 * Generator Queue class for handling amplifier cache generation.
 *
 * @class    WC_PRL_Amplifier_Generator_Queue
 * @version  4.1.0
 */
class WC_PRL_Amplifier_Generator_Queue extends WC_PRL_Background_Queue {

	/**
	 * Action
	 *
	 * @var string
	 */
	protected $action = 'wc_prl_amplifier_generator';

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
	 * Generate key
	 *
	 * Generates a unique key based on microtime. Queue items are
	 * given a unique key so that they can be merged upon save.
	 *
	 * @param array $data Data.
	 *
	 * @return string
	 */
	public function generate_key( $data ) {
		return sprintf( '%s_item_%s_%s_%s', $this->identifier, $data['id'], $data['name'], $data['type'] );
	}

	/**
	 * Run step.
	 *
	 * This is a recurring task that will be called by the action scheduler. It will be called on repeat until it returns false.
	 *
	 * @param array $data Data.
	 * @return mixed
	 */
	protected function run_step( $data ) {
		return WC_PRL_Amplifier_Generator::get_instance()->run( $data );
	}

	/**
	 * Get job data.
	 *
	 * @param array $data Data. The `id` and `name` key are required.
	 * @return array|false The job data.
	 */
	protected function get_job_data( $data ) {

		if ( ! isset( $data['id'], $data['name'] ) ) {
			return false;
		}

		$job_data = array(
			'id'   => absint( $data['id'] ),
			'name' => $data['name'],
			'type' => $data['type'] ?? 'default',
		);
		return $job_data;
	}
}
