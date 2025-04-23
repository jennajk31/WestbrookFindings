<?php
/**
 * WC_MMQ_Store_API class
 *
 * @package  WooCommerce Min/Max Quantities
 * @since    2.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;

/**
 * Filters the store public API.
 *
 * @version 5.2.0
 */
class WC_MMQ_Store_API {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	const IDENTIFIER = 'min_max_quantities';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public static function init() {

		// Filter minimum cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_minimum', array( __CLASS__, 'filter_min_cart_item_qty' ), 10, 3 );

		// Filter maximum cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_maximum', array( __CLASS__, 'filter_max_cart_item_qty' ), 10, 3 );

		// Filter group of cart item quantity.
		add_filter( 'woocommerce_store_api_product_quantity_multiple_of', array( __CLASS__, 'filter_multiple_of_cart_item_qty' ), 10, 3 );

		// Validate cart based on Min/Max/Group of rules and add error notices.
		add_action( 'woocommerce_store_api_cart_errors', array( __CLASS__, 'validate_cart' ), 10, 2 );

		// Prevent access to the checkout block.
		add_action( 'woocommerce_store_api_checkout_update_order_meta', array( __CLASS__, 'validate_draft_order' ) );
	}

	/**
	 * Adjust cart item quantity limits to keep min quantity limited by Min/Max Quantity restrictions.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the minimum product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the minimum product quantity must be filtered by the `wc_min_max_quantity_minimum_allowed_quantity` first.
	 *
	 * @param mixed      $value The value being filtered.
	 * @param WC_Product $product The product object.
	 * @param array|null $cart_item The cart item if the product exists in the cart, or null.
	 * @return mixed
	 */
	public static function filter_min_cart_item_qty( $value, $product, $cart_item ) {

		$parent_product = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : null;

		// If this is a variation, and the parent product allows combination, then do not automatically update the min cart item quantity, if allow combination is enabled.
		if ( $parent_product && 'yes' === $parent_product->get_meta( 'allow_combination', true ) ) {
			return $value;
		}

		[
			WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
		] = WC_Min_Max_Quantities::get_instance()->get_quantity_rules_for_product( $product );

		if ( ! is_null( $cart_item ) ) {

			/**
			 * Use this filter to filter the Minimum Quantity of a product/variation.
			 *
			 * @since 2.2.7
			 *
			 * @param  string  $quantity
			 * @param  int     $product_id
			 * @param  string  $cart_item_key
			 * @param  array   $cart_item
			 */
			$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', $minimum_quantity, $product->get_id(), $cart_item['key'], $cart_item ) );
		}

		return $minimum_quantity;
	}

	/**
	 * Adjust cart item quantity limits to keep max quantity limited by Min/Max Quantity restrictions.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the maximum product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the maximum product quantity must be filtered by the `wc_min_max_quantity_maximum_allowed_quantity` first.
	 *
	 * @param mixed      $value The value being filtered.
	 * @param WC_Product $product The product object.
	 * @param array|null $cart_item The cart item if the product exists in the cart, or null.
	 * @return mixed
	 */
	public static function filter_max_cart_item_qty( $value, $product, $cart_item ) {
		[
			WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $max_quantity,
		] = WC_Min_Max_Quantities::get_instance()->get_quantity_rules_for_product( $product );

		if ( ! is_null( $cart_item ) ) {

			/**
			 * Use this filter to filter the Maximum Quantity of a product/variation.
			 *
			 * @since 2.2.7
			 *
			 * @param  string  $quantity
			 * @param  int     $product_id
			 * @param  string  $cart_item_key
			 * @param  array   $cart_item
			 */
			$max_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', $max_quantity, $product->get_id(), $cart_item['key'], $cart_item ) );
		}

		// Avoid returning zero as the max quantity.
		return ! empty( $max_quantity ) ? $max_quantity : $value;
	}

	/**
	 * Ensure that cart item quantity can be changed in specific intervals.
	 *
	 * If the $cart_item is null, then this means that the $product has not been added to the cart yet.
	 * In this case, the group of product quantity can be read directly from the post meta.
	 * When the $cart_item exists, though, the group of product quantity must be filtered by the `wc_min_max_quantity_group_of_quantity` first.
	 *
	 * @param mixed      $value The value being filtered.
	 * @param WC_Product $product The product object.
	 * @param array|null $cart_item The cart item if the product exists in the cart, or null.
	 * @return mixed
	 */
	public static function filter_multiple_of_cart_item_qty( $value, $product, $cart_item ) {
		$parent_product = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : null;

		// If this is a variation, and the parent product allows combination, then do not automatically update the min cart item quantity, if allow combination is enabled.
		if ( $parent_product && 'yes' === $parent_product->get_meta( 'allow_combination', true ) ) {
			return $value;
		}

		[
			WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity,
		] = WC_Min_Max_Quantities::get_instance()->get_quantity_rules_for_product( $product );

		if ( ! is_null( $cart_item ) ) {

			/**
			 * Use this filter to filter the Group of quantity of a product/variation.
			 *
			 * @since 2.2.7
			 *
			 * @param  string  $quantity
			 * @param  int     $product_id
			 * @param  string  $cart_item_key
			 * @param  array   $cart_item
			 */
			$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', $group_of_quantity, $product->get_id(), $cart_item['key'], $cart_item ) );
		}

		// Avoid returning zero as the group of quantity.
		return ! empty( $group_of_quantity ) ? $group_of_quantity : $value;
	}

	/**
	 * Validate cart based on Min/Max/Group of rules and add error notices.
	 *
	 * @throws RouteException When cart validation fails.
	 *
	 * @param WP_Error $errors The WP_Error object to add errors to.
	 * @param WC_Cart  $cart The cart object to validate.
	 * @return void
	 */
	public static function validate_cart( $errors, $cart ) {

		try {
			$mmq = WC_Min_Max_Quantities::get_instance();
			$mmq->check_cart_items();
		} catch ( Exception $e ) {
			$notice = html_entity_decode( wp_strip_all_tags( $e->getMessage() ), ENT_QUOTES );
			$errors->add( 'woocommerce_store_api_invalid_min_max_quantities', $notice );
		}
	}

	/**
	 * Prevents access to the checkout block if the cart item quantities are not correctly configured.
	 *
	 * @throws RouteException When cart item quantities are invalid.
	 *
	 * @param WC_Order $order The order object to validate.
	 * @return void
	 */
	public static function validate_draft_order( $order ) {

		try {
			$mmq = WC_Min_Max_Quantities::get_instance();
			$mmq->check_cart_items();
		} catch ( Exception $e ) {
			$notice = html_entity_decode( wp_strip_all_tags( $e->getMessage() ), ENT_QUOTES );
			throw new RouteException( 'woocommerce_store_api_invalid_min_max_quantities', $notice );
		}
	}
}
