<?php
/**
 * The "Recently Viewed Products" Collection Controller.
 *
 * @package  WooCommerce Product Recommendations
 *
 * @since    4.1.0
 * @version  4.1.0
 */
class WC_PRL_Blocks_Collection_Recently_Viewed extends WC_PRL_Blocks_Product_Collection {

	/**
	 * The collection name.
	 *
	 * @var string
	 */
	protected $collection_name = 'woocommerce-product-recommendations/product-collection/recently-viewed';

	/**
	 * Construct.
	 */
	public function __construct() {
		parent::__construct();

		// Apply this collection to the front-end query args.
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

		$viewed_products = wc_prl_get_recently_viewed_cookie_products();
		if ( empty( $viewed_products ) ) {
			$this->disable_rendering();
			return $query;
		}

		if ( empty( $query['post__in'] ) || ! is_array( $query['post__in'] ) ) {
			$query['post__in'] = array();
		}

		// Sort by recent views.
		$viewed_products = array_reverse( $viewed_products );

		if ( ! empty( $query['post__in'] ) ) {
			$query['post__in'] = array_intersect( $query['post__in'], $viewed_products );
		} else {
			$query['post__in'] = $viewed_products;
		}

		// Order by post__in.
		$query['orderby'] = 'post__in';

		return $query;
	}
}
