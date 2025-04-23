<?php
/**
 * Base for Product Collection blocks.
 *
 * @package  WooCommerce Product Recommendations
 *
 * @since    4.1.0
 * @version  4.1.0
 */
abstract class WC_PRL_Blocks_Product_Collection {

	/**
	 * The collection name (including namespace.)
	 *
	 * @var string
	 */
	protected $collection_name;

	/**
	 * Runtime cache for the WP_Query's query arguments.
	 *
	 * @var array|null
	 */
	private $should_render = true;

	/**
	 * Construct.
	 */
	public function __construct() {
		add_filter( 'render_block_woocommerce/product-collection', array( $this, 'handle_product_collection_rendering' ), PHP_INT_MAX );
	}

	/**
	 * Disable rendering of the block.
	 *
	 * @return void
	 */
	protected function disable_rendering() {
		$this->should_render = false;
	}

	/**
	 * Prevent rendering empty results.
	 *
	 * @param string   $block_content The block content about to be rendered.
	 * @param array    $block The block being rendered.
	 * @param WP_Block $block The block instance being rendered.
	 *
	 * @return string
	 */
	public function handle_product_collection_rendering( $block_content ) {

		if ( ! $this->should_render ) {
			// Prevent rendering of the block. Print an empty div instead.
			return '';
		}

		// Reset.
		$this->should_render = true;

		return $block_content;
	}

	/**
	 * Check if the Product Collection block is the current variation.
	 *
	 * @param WP_Block $block The block being rendered.
	 * @return bool
	 */
	protected function is_valid_block( $block ) {

		if ( ! is_array( $block->context ) ) {
			return false;
		}

		// If not in context of product collection block, return the query as is.
		$is_product_collection_block = $block->context['query']['isProductCollectionBlock'] ?? false;
		if ( ! $is_product_collection_block ) {
			return false;
		}

		// The collection key isn't set on the default collection type (custom query), or other child components.
		if ( ! isset( $block->context['collection'] ) ) {
			return false;
		}

		if ( $this->collection_name !== $block->context['collection'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the Product Collection block is the current variation.
	 *
	 * @param WP_REST_Request $request The request.
	 * @return bool
	 */
	protected function is_valid_block_request( $request ) {

		$is_product_collection_block = $request->get_param( 'isProductCollectionBlock' );
		if ( ! $is_product_collection_block ) {
			return false;
		}

		$query_context = $request->get_param( 'productCollectionQueryContext' );
		if ( ! is_array( $query_context ) || ! isset( $query_context['collection'] ) ) {
			return false;
		}

		$is_preview = isset( $query_context['previewState'], $query_context['previewState']['isPreview'] ) && $query_context['previewState']['isPreview'];
		if ( $is_preview ) {
			return false;
		}

		if ( $this->collection_name !== $query_context['collection'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Parse the order from the block context.
	 *
	 * @param WP_Block $block The block being rendered.
	 * @return WC_Order|false
	 */
	protected function get_viewed_order( $block ) {
		$location = $block->context['productCollectionLocation'] ?? false;
		if ( ! $location || ! is_array( $location ) || ! isset( $location['type'] ) || 'order' !== $location['type'] || ! isset( $location['sourceData'] ) || empty( $location['sourceData']['orderId'] ) ) {
			return false;
		}

		$order = wc_get_order( $location['sourceData']['orderId'] );
		if ( ! ( $order instanceof WC_Order ) ) {
			return false;
		}

		return $order;
	}

	/**
	 * Get the product IDs from the block.
	 *
	 * @param WP_Block $block The block being rendered.
	 * @return WC_Product|false
	 */
	protected function get_viewed_product( $block ) {
		$location = $block->context['productCollectionLocation'] ?? false;
		if ( ! $location || ! is_array( $location ) || ! isset( $location['type'] ) || 'product' !== $location['type'] || ! isset( $location['sourceData'] ) || empty( $location['sourceData']['productId'] ) ) {
			return false;
		}

		$product = wc_get_product( $location['sourceData']['productId'] );
		if ( ! ( $product instanceof WC_Product ) ) {
			return false;
		}

		return $product;
	}
}
