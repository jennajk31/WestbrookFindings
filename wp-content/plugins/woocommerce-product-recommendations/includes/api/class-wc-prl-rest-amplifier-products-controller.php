<?php
/**
 * WC_PRL_Rest_Amplifier_Products_V1_Controller class
 *
 * @package  Woo Product Recommendations
 * @since    4.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The REST controller for the Amplifier Products.
 *
 * @class    WC_PRL_Rest_Amplifier_Products_V1_Controller
 * @version  4.1.0
 */
class WC_PRL_Rest_Amplifier_Products_V1_Controller extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc-prl-amplifier-provider/v1';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// noop.
	}

	/**
	 * Register routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/status',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_status' ),
					'permission_callback' => array( $this, 'permissions_check' ),
				),
			)
		);

		// TEst wp-admin ajax.
		add_action( 'wp_ajax_prl_check_status', array( $this, 'maybe_handle' ) );
		add_action( 'wp_ajax_nopriv_prl_check_status', array( $this, 'maybe_handle' ) );
	}

	public function maybe_handle() {
		$generator = WC_PRL_Amplifier_Generator::get_instance();
		$generator->schedule_task( 639, 'frequently_bought_together' );
	}

	/**
	 * Check on the cache status of a given request.
	 * If the cache is not available or has expired data, it will schedule a new cache generation task.
	 *
	 * @see WC_PRL_AS_Generator::handle
	 * @see WC_PRL_Amplifier_Generator_Queue::run_step
	 *
	 * Possible responses:
	 * - AVAILABLE: The cache is available.
	 * - IN_QUEUE: The cache is being generated.
	 * - NOT_AVAILABLE: The cache is not available.
	 *
	 * @param $request WP_REST_Request
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_status( $request ) {

		$response = array(
			'not-available' => 'NOT_AVAILABLE',
			'in-queue'      => 'IN_QUEUE',
			'available'     => 'AVAILABLE',
		);

		// Get the product ID.
		$product_id = $request->get_param( 'product_id' );
		if ( ! $product_id ) {
			return new WP_Error( 'woocommerce_prl_invalid_product_id', 'Invalid Product ID.', array( 'status' => 400 ) );
		}

		// Product must exist.
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return new WP_Error( 'woocommerce_prl_invalid_product', 'Invalid Product.', array( 'status' => 400 ) );
		}

		// Get the amplifier name.
		$amplifier_name = $request->get_param( 'name' );
		if ( ! $amplifier_name ) {
			return new WP_Error( 'woocommerce_prl_invalid_amplifier_name', 'Invalid Amplifier Name.', array( 'status' => 400 ) );
		}

		$amplifier = WC_PRL()->amplifiers->get_amplifier( $amplifier_name );
		if ( ! $amplifier instanceof WC_PRL_Amplifier ) {
			return new WP_Error( 'woocommerce_prl_invalid_amplifier', 'Invalid Amplifier.', array( 'status' => 400 ) );
		}

		// Get the type or default.
		$type = $request->get_param( 'type' ) ? $request->get_param( 'type' ) : 'default';
		if ( ! in_array( $type, WC_PRL_Amplifier_Generator::CALIBRATION_TYPES, true ) ) {
			return new WP_Error( 'woocommerce_prl_invalid_amplifier_type', 'Invalid Amplifier Type.', array( 'status' => 400 ) );
		}

		// Check if the cache is available.
		$generator = WC_PRL_Amplifier_Generator::get_instance();
		$products  = $amplifier->get_cached_products( $product_id, $type );
		if ( ! empty( $products ) ) {

			// If products are outdated, schedule a new cache generation task and return the products.
			if ( $generator->has_expired_cache_products( $product_id, $amplifier, $type ) ) {
				$generator->schedule_task( $product_id, $amplifier->get_id(), $type );
			}

			return rest_ensure_response( $response['available'] );
		}

		$generator = WC_PRL_Amplifier_Generator::get_instance();
		$in_queue  = $generator->schedule_task( $product_id, $amplifier_name, $type );

		if ( $in_queue ) {
			return rest_ensure_response( $response['in-queue'] );
		}

		// The cache is not available.
		return rest_ensure_response( $response['not-available'] );
	}

	/**
	 * Check permissions for the request.
	 *
	 * @param $request
	 * @return boolean|WP_Error
	 */
	public function permissions_check( $request ) {
		if ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' ) ) {
			return true;
		}

		return new WP_Error(
			'woocommerce_prl_unauthorized',
			'You do not have permission to access this resource.',
			array( 'status' => is_user_logged_in() ? 403 : 401 )
		);
	}
}
