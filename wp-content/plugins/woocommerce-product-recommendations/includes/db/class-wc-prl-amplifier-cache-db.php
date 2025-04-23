<?php
/**
 * WC_PRL_Amplifier_Cache_DB class
 *
 * @package  Woo Product Recommendations
 * @since    4.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Amplifier Cache DB API class.
 *
 * @class    WC_PRL_Amplifier_Cache_DB
 * @version  4.1.0
 */
class WC_PRL_Amplifier_Cache_DB {

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
	 * Constructor.
	 */
	public function __construct() {
		// ...
	}

	/**
	 * Get cached results for given product.
	 *
	 * @param  int   $product_id
	 * @param  array $args
	 * @return array
	 */
	public function get( $product_id, $args ) {

		if ( ! isset( $args['name'] ) ) {
			return array();
		}

		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'type' => 'default',
			)
		);

		$table = $wpdb->prefix . 'woocommerce_prl_amplifier_cache';
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql     = "SELECT * FROM `{$table}` WHERE `source` = %d AND `name` = %s AND `type` = %s LIMIT 1";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, $product_id, $args['name'], $args['type'] ) );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( empty( $results ) ) {
			return array();
		}

		$a = array();
		foreach ( $results as $result ) {
			$result->data = json_decode( $result->data, true, 512, JSON_OBJECT_AS_ARRAY );
			$a[]          = (array) $result;
		}

		return $a;
	}

	/**
	 * Set cached results for given product.
	 *
	 * @param  int   $product_id
	 * @param  array $products
	 * @param  array $args {
	 *    @type  string  $name    Amplifier type.
	 *    @type  string  $type  Preset name.
	 * }
	 *
	 * @return bool
	 */
	public function set( $product_id, $products, $args = array() ) {
		// adds or updates a record.
		if ( empty( $product_id ) || empty( $args['name'] ) ) {
			return;
		}

		$args = wp_parse_args(
			$args,
			array(
				'type' => 'default',
			)
		);

		if ( $this->get( $product_id, $args ) ) {
			return $this->update( $product_id, $products, $args );
		} else {
			return false !== $this->add( $product_id, $products, $args );
		}
	}

	/**
	 * Update cached results for given product.
	 *
	 * @param  int   $product_id
	 * @param  array $products
	 * @param  array $args {
	 *    @type  string  $name  The calculation name. Usually the Amplifier ID.
	 *    @type  string  $type  Calibration type.
	 * }
	 *
	 * @return bool
	 */
	public function update( $product_id, $products, $args ) {

		if ( empty( $product_id ) || empty( $args['name'] ) ) {
			throw new Exception( __( 'Missing attributes.', 'woocommerce-product-recommendations' ) );
		}

		$args = wp_parse_args(
			$args,
			array(
				'type' => 'default',
			)
		);

		global $wpdb;
		$table = $wpdb->prefix . 'woocommerce_prl_amplifier_cache';
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$affected_rows = $wpdb->query(
			$wpdb->prepare(
				"UPDATE `{$table}`
		    SET `data` = %s, `created_at` = %d
			  WHERE `source` = %d AND `name` = %s AND `type` = %s
			  LIMIT 1",
				array( wp_json_encode( $products ), time(), $product_id, $args['name'], $args['type'] )
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $affected_rows > 0 ? true : false;
	}

	/**
	 * Add a new record.
	 *
	 * @param  int   $product_id
	 * @param  array $products
	 * @param  array $args {
	 *    @type  string  $name  The calculation name. Usually the Amplifier ID.
	 *    @type  string  $type  Calibration type.
	 * }
	 * @return false|int
	 */
	public function add( $product_id, $products, $args ) {

		if ( empty( $product_id ) || empty( $args['name'] ) ) {
			throw new Exception( __( 'Missing attributes.', 'woocommerce-product-recommendations' ) );
		}

		$args = wp_parse_args(
			$args,
			array(
				'type' => 'default',
			)
		);

		global $wpdb;
		$table = $wpdb->prefix . 'woocommerce_prl_amplifier_cache';
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO `{$table}`
				( source, name, type, data, created_at )
			  VALUES
				( %d, %s, %s, %s, %d )",
				array( $product_id, $args['name'], $args['type'], wp_json_encode( $products ), time() )
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return ( $wpdb->insert_id ) ? $wpdb->insert_id : false;
	}
}
