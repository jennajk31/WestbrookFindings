<?php
/**
 * The "Viewed but not Purchased Products" Collection Controller.
 *
 * @package  WooCommerce Product Recommendations
 *
 * @since    4.1.0
 * @version  4.1.0
 */
class WC_PRL_Blocks_Collection_Viewed_Not_Purchased extends WC_PRL_Blocks_Product_Collection {

	/**
	 * The collection name.
	 *
	 * @var string
	 */
	protected $collection_name = 'woocommerce-product-recommendations/product-collection/viewed-not-purchased';

	/**
	 * Construct.
	 */
	public function __construct() {
		parent::__construct();

		// Apply this collection to the query args.
		add_filter( 'query_loop_block_query_vars', array( $this, 'handle_query_vars' ), 100, 3 );
	}

	/**
	 * Inject cookie data into the query.
	 *
	 * @param array    $query The WordPress WP_Query arguments.
	 * @param WP_Block $block The block being rendered.
	 * @param int      $page  The page number.
	 *
	 * @return array The modified query.
	 */
	public function handle_query_vars( $query, $block, $page ) {
		if ( ! $this->is_valid_block( $block ) ) {
			return $query;
		}

		// Get the viewed products.
		$viewed_products = wc_prl_get_recently_viewed_cookie_products();
		if ( empty( $viewed_products ) ) {
			$this->disable_rendering();
			return $query;
		}

		// Get current order.
		$order = $this->get_viewed_order( $block );
		if ( ! $order ) {
			$this->disable_rendering();
			return $query;
		}

		// Grap purchased product Ids.
		$purchased_products = array();
		$items              = $order->get_items();
		foreach ( $items as $item_id => $item ) {
			if ( ! ( $item instanceof WC_Order_Item_Product ) ) {
				continue;
			}

			$purchased_products[] = $item->get_product_id();
		}

		if ( empty( $purchased_products ) ) {
			$this->disable_rendering();
			return $query;
		}

		// Inject args.
		if ( empty( $query['post__in'] ) || ! is_array( $query['post__in'] ) ) {
			$query['post__in'] = array();
		}

		$viewed_products = array_reverse( $viewed_products );
		$products        = array_values( array_diff( $viewed_products, $purchased_products ) );

		if ( ! empty( $query['post__in'] ) ) {
			$query['post__in'] = array_intersect( $query['post__in'], $products );
		} else {
			$query['post__in'] = $products;
		}

		// Order by post__in.
		$query['orderby'] = 'post__in';

		return $query;
	}
}
