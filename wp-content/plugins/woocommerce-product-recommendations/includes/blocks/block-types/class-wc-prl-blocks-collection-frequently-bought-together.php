<?php
/**
 * The "Frequently Bought Together" Collection Controller.
 *
 * @package  WooCommerce Product Recommendations
 *
 * @since    4.1.0
 * @version  4.1.0
 */
class WC_PRL_Blocks_Collection_Fequently_Bought_Together extends WC_PRL_Blocks_Product_Collection {

	/**
	 * The collection name.
	 *
	 * @var string
	 */
	protected $collection_name = 'woocommerce-product-recommendations/product-collection/frequently-bought-together';

	/**
	 * Runtime cache for the products.
	 */
	private $products = array();

	/**
	 * Construct.
	 */
	public function __construct() {
		parent::__construct();

		// Apply this collection to the front-end query args.
		add_filter( 'query_loop_block_query_vars', array( $this, 'handle_query_vars' ), 100, 3 );

		// Reset the order clauses.
		add_filter(
			'render_block_woocommerce/product-collection',
			function ( $content ) {

				if ( ! empty( $this->products ) ) {
					remove_filter( 'posts_clauses', array( $this, 'add_order_clauses' ) );
					$this->products = array();
				}

				return $content;
			},
			100,
			3
		);
	}

	public function add_order_clauses( $args ) {
		if ( empty( $this->products ) ) {
			return $args;
		}

		$args['orderby'] = 'FIELD(ID,' . implode( ',', $this->products ) . ')';

		return $args;
	}

	/**
	 * Filter the query in the front-end.
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

		$product = $this->get_viewed_product( $block );
		if ( ! $product ) {
			$this->disable_rendering();
			return $query;
		}

		// Handle the relevance score.
		$relevance_score = $block->context['query']['woocommercePrlRelevanceScore'] ?? 'low';
		if ( ! in_array( $relevance_score, array( 'low', 'medium', 'high' ), true ) ) {
			$relevance_score = 'low';
		}

		// Fetch the amplifier.
		$amplifier = WC_PRL()->amplifiers->get_amplifier( 'frequently_bought_together' );
		if ( ! $amplifier ) {
			$this->disable_rendering();
			return $query;
		}

		// Get cached products if exist.
		$product_id   = $product->get_id();
		$generator    = WC_PRL_Amplifier_Generator::get_instance();
		$is_throttled = WC_PRL()->deployments->is_generator_throttled();
		$products     = $amplifier->get_cached_products( $product_id, $relevance_score );
		if ( ! empty( $products ) ) {

			// If products are outdated, schedule a new cache generation task and return the products.
			if ( ! $is_throttled && $generator->has_expired_cache_products( $product_id, $amplifier, $relevance_score ) ) {
				$generator->schedule_task( $product_id, $amplifier->get_id(), $relevance_score );
			}
		} elseif ( ! $is_throttled ) {
			// Schedule a new cache generation task.
			$generator->schedule_task( $product_id, $amplifier->get_id(), $relevance_score );
			$this->disable_rendering();
			return $query;
		}

		$related_products   = $products['data'] ?? array();
		$should_fill_blanks = (bool) ( $block->context['query']['woocommercePrlShouldFillBlanks'] ?? false );
		if ( $should_fill_blanks ) {
			$random_products_result = get_posts(
				array(
					'posts_per_page' => 10, // Fetch 10 products randomly to fix the blanks.
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'post__not_in'   => $related_products,
					'orderby'        => 'rand',
				)
			);
			$random_products_ids    = array();
			foreach ( $random_products_result as $random_product ) {
				$random_products_ids[] = $random_product->ID;
			}
			$related_products = array_merge( $related_products, $random_products_ids );
		}

		if ( empty( $related_products ) ) {
			$this->disable_rendering();
			return $query;
		}

		$query['post__in'] = empty( $query['post__in'] ) ? $related_products : array_intersect( $query['post__in'], $related_products );

		// Handle default sorting.
		$this->products = $related_products;
		add_filter( 'posts_clauses', array( $this, 'add_order_clauses' ) );

		return $query;
	}
}
