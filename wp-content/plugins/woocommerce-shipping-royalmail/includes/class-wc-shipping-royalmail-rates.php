<?php
/**
 * RoyalMail rates.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Shipping_Royalmail_Rates class.
 */
class WC_Shipping_Royalmail_Rates {

	/**
	 * Box packer library.
	 *
	 * @var string
	 */
	public $box_packer_library;

	/**
	 * Packing method that is being used.
	 *
	 * @var string
	 */
	public $packing_method = '';

	/**
	 * Shipping method instance id.
	 *
	 * @var string
	 */
	public $instance_id = 0;

	/**
	 * Rate price type.
	 *
	 * @var string
	 */
	public $rate_type;

	/**
	 * List of items to ship.
	 *
	 * @var array
	 */
	public $items;

	/**
	 * List of packages to ship.
	 *
	 * @var array
	 */
	public $packages;

	/**
	 * Destination.
	 *
	 * @var array
	 */
	private $destination;

	/**
	 * User-defined boxes.
	 *
	 * @var array
	 */
	private $boxes;

	/**
	 * Available services for RoyalMail.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * Available online services for RoyalMail.
	 *
	 * @var array
	 */
	private $online_services;

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger_Interface
	 */
	private $logger;

	/**
	 * Constructor.
	 *
	 * @param array               $package            Package to ship.
	 * @param string              $box_packer_library Box packer library.
	 * @param string              $packing_method     Packing method.
	 * @param array               $boxes              User-defined boxes.
	 * @param int                 $instance_id        Instance ID.
	 * @param string              $rate_type          Whether using regular price or online price.
	 * @param WC_Logger_Interface $logger             Logger instance.
	 */
	public function __construct( $package, $box_packer_library, $packing_method, $boxes = array(), $instance_id = '', $rate_type = 'regular', $logger = null ) {
		if ( ! class_exists( 'RoyalMail_Rate' ) ) {
			include_once 'rates/abstract-class-royalmail-rate.php';
		}

		if ( ! class_exists( 'Parcelforce_Rate' ) ) {
			include_once 'rates/abstract-class-parcelforce-rate.php';
		}

		$this->services        = WC_RoyalMail::get_regular_services();
		$this->online_services = WC_RoyalMail::get_online_services();

		$this->logger             = $logger;
		$this->items              = $this->get_items( $package );
		$this->destination        = $package['destination']['country'];
		$this->box_packer_library = $box_packer_library;
		$this->packing_method     = $packing_method;
		$this->boxes              = $boxes;
		$this->instance_id        = $instance_id;
		$this->rate_type          = ( 'online' === $rate_type ) ? 'online' : 'regular';
	}

	/**
	 * Output a message.
	 *
	 * @param string $message Message to display.
	 * @param array  $data    Additional contextual data to pass.
	 *
	 * @return void;
	 */
	public function debug( $message, $data = array() ) {
		if ( ! $this->logger instanceof WC_Logger_Interface ) {
			return;
		}

		$this->logger->debug( $message, $data );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __DIR__ ) );
	}

	/**
	 * Get quotes.
	 *
	 * @since 1.0.0
	 * @version 2.5.3
	 *
	 * @return array Quotes.
	 */
	public function get_quotes() {
		if ( empty( $this->items ) ) {
			return;
		}

		$quotes                 = array();
		$services_without_quote = array();

		foreach ( $this->get_services() as $service ) {
			$class = $this->get_service_class_name( $service );
			$file  = $this->get_service_class_path( $service );

			if ( file_exists( $file ) ) {
				include_once $file;
			}

			if ( ! class_exists( $class ) ) {
				continue;
			}

			$service_class = new $class( $this->get_rate_type(), $this->box_packer_library );
			$quote         = (array) $service_class->get_quotes(
				$this->items,
				$this->packing_method,
				$this->destination,
				$this->boxes,
				$this->instance_id
			);

			$this->packages = $service_class->get_packages( $this->items, $this->packing_method );

			if ( empty( $quote ) ) {
				$services_without_quote[] = $service;
				continue;
			}

			$quotes = array_merge( $quotes, $quote );
		}

		if ( ! empty( $services_without_quote ) ) {
			$this->debug(
				sprintf( // translators: %s is services.
					__( 'Did not find any corresponding chargeable weight OR valued package is greater than maximum total cover for services "%s"', 'woocommerce-shipping-royalmail' ),
					implode( ', ', $services_without_quote )
				)
			);
		}

		return array_filter( $quotes );
	}

	/**
	 * Get services from a given destination.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @return array Services.
	 */
	public function get_services() {
		$service = ( WC_RoyalMail::ONLINE_SERVICE === $this->get_rate_type() ) ? $this->online_services : $this->services;

		return $service[ $this->get_service_type() ];
	}

	/**
	 * Get rate price based on the settings.
	 *
	 * @since 2.9.0
	 * @version 2.9.0
	 *
	 * @return string Rate price type.
	 */
	public function get_rate_type() {
		return $this->rate_type;
	}

	/**
	 * Get rate type name.
	 *
	 * @since 2.9.0
	 * @version 2.9.0
	 *
	 * @return string Rate type name.
	 */
	public function get_rate_type_name() {
		if ( WC_RoyalMail::ONLINE_SERVICE === $this->rate_type ) {
			return 'Online';
		}

		return 'Regular'; // Default rate price name.
	}
	/**
	 * Get service type.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @return string Service type ('uk' or 'international').
	 */
	public function get_service_type() {
		$domestic_destinations = array(
			'GB', // Great Britain.
			'GG', // Guernsey.
			'IM', // Isle of Man.
			'JE', // Jersey.
		);
		return in_array( $this->destination, $domestic_destinations, true ) ? WC_RoyalMail::UK_SERVICE : WC_RoyalMail::INTERNATIONAL_SERVICE;
	}

	/**
	 * Get class path of a given service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $service Service slug. See `self::services`.
	 *
	 * @return string Class filepath
	 */
	public function get_service_class_path( $service ) {
		return sprintf(
			'%1$s/includes/rates/%2$s/%3$s/class-royalmail-%3$s-%4$s.php',
			$this->plugin_path(),
			$this->get_service_type(),
			$this->get_rate_type(),
			str_replace( '_', '-', $service )
		);
	}

	/**
	 * Get class name of a given service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $service Service slug. See `self::services`.
	 *
	 * @return string Class name
	 */
	public function get_service_class_name( $service ) {
		return 'RoyalMail_' . $this->get_rate_type_name() . '_' . str_replace( '-', '_', ucwords( $service, '-' ) );
	}

	/**
	 * Get items from a given package.
	 *
	 * @param mixed $package Package to ship.
	 * @return array Items.
	 */
	private function get_items( $package ) {
		$requests = array();

		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! ( $values['data'] instanceof WC_Product ) ) {
				/* translators: product item ID */
				$this->debug( sprintf( __( 'Item #%d is not a product. Skipping.', 'woocommerce-shipping-royalmail' ), $item_id ) );
				continue;
			}

			if ( ! $values['data']->needs_shipping() ) {
				/* translators: product item ID */
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-royalmail' ), $item_id ) );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				/* translators: product item ID */
				$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-royalmail' ), $item_id ) );
				return;
			}

			$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

			sort( $dimensions );

			$item         = new stdClass();
			$item->weight = wc_get_weight( floatval( $values['data']->get_weight() ), 'g' );
			$item->length = wc_get_dimension( floatval( $dimensions[2] ), 'mm' );
			$item->width  = wc_get_dimension( floatval( $dimensions[1] ), 'mm' );
			$item->height = wc_get_dimension( floatval( $dimensions[0] ), 'mm' );
			$item->qty    = intval( $values['quantity'] );
			$item->value  = $values['data']->get_price();
			$item->id     = $values['data']->get_id();

			$item->printed_papers = 'yes' === $values['data']->get_meta( WC_Shipping_Royalmail_Admin::META_KEY_PRINTED_PAPERS );
			$item->book           = 'yes' === $values['data']->get_meta( WC_Shipping_Royalmail_Admin::META_KEY_BOOK );
			$item->tube           = 'yes' === $values['data']->get_meta( WC_Shipping_Royalmail_Admin::META_KEY_TUBE );

			$requests[] = $item;
		}

		return $requests;
	}
}
