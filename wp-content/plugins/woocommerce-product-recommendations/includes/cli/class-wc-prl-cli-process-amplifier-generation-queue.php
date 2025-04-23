<?php
/**
 * WC_PRL_CLI_Process_Amplifier_Generation_Queue class
 *
 * @package  Woo Product Recommendations
 * @since    3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allows processing the amplifiergeneration queue via WP-CLI.
 *
 * @class    WC_PRL_CLI_Process_Amplifier_Generation_Queue
 * @version  4.1.0
 */
class WC_PRL_CLI_Process_Amplifier_Generation_Queue {

	/**
	 * Registers the update command.
	 */
	public static function register_command() {

		$args = array(
			'shortdesc' => 'Runs through the queue and generates product recommendations.',
		);

		WP_CLI::add_command(
			'wc prl process-amplifier-generation-queue',
			array( 'WC_PRL_CLI_Process_Amplifier_Generation_Queue', 'process' ),
			$args
		);
	}

	/**
	 * Runs through the queue and generates product recommendations.
	 */
	public static function process( $args, $assoc_args ) {

		$assoc_args = wp_parse_args(
			$assoc_args,
			array(
				'max-iterations' => 0,
			)
		);

		require_once WC_PRL_ABSPATH . 'includes/class-wc-prl-amplifier-generator.php';
		require_once WC_PRL_ABSPATH . 'includes/class-wc-prl-amplifier-generator-queue.php';

		$queue        = new WC_PRL_Amplifier_Generator_Queue();
		$handler_args = array(
			'cli_max_iterations' => absint( $assoc_args['max-iterations'] ),
		);
		$queue->handle( null, $handler_args );
	}
}
