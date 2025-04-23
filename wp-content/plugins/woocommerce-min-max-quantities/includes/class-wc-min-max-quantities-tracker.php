<?php
/**
 * WC_Min_Max_Quantities_Tracker class
 *
 * @package  WooCommerce Min/Max Quantities
 * @since    5.2.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Min/Max Quantities Tracker.
 *
 * @class    WC_Min_Max_Quantities_Tracker
 * @version  5.2.4
 */
class WC_Min_Max_Quantities_Tracker {

	/**
	 * Property to store and share tracking data in the class.
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * Property to store the starting time of the process.
	 *
	 * @var int
	 */
	private static $start_time = 0;

	/**
	 * Property to store how often the data will be invalidated.
	 *
	 * @var string
	 */
	private static $invalidation_interval = '-1 week';

	/**
	 * Initialize the tracker.
	 */
	public static function init() {
		if ( 'yes' === get_option( 'woocommerce_allow_tracking', 'no' ) ) {
			add_filter( 'woocommerce_tracker_data', array( __CLASS__, 'add_tracking_data' ) );

			if ( defined( 'WC_CALYPSO_BRIDGE_TRACKER_FREQUENCY' ) ) {
				add_action( 'wc_mmq_hourly', array( __CLASS__, 'maybe_calculate_tracking_data' ) );
			} else {
				add_action( 'wc_mmq_daily', array( __CLASS__, 'maybe_calculate_tracking_data' ) );
			}
		}
	}

	/**
	 * Add MMQ data to the tracked data.
	 *
	 * @param  array $data Plugin data.
	 * @return array  all the tracking data.
	 */
	public static function add_tracking_data( $data ) {
		$data['extensions']['wc_mmq'] = self::get_tracking_data();
		return $data;
	}

	/**
	 * Get all tracking data from options.
	 *
	 * @return array MMQ's tracking data.
	 */
	private static function get_tracking_data() {
		self::read_data();
		self::maybe_initialize_data();

		// If there are no data calculated, it will calculate them and then send the data.
		if ( self::has_pending_calculations() ) {
			return array();
		}

		if ( isset( self::$data['info']['started_time'] ) ) {
			unset( self::$data['info']['started_time'] );
		}

		return self::$data;
	}

	/**
	 * Calculates all tracking-related data for the previous month and year.
	 * Runs independently in a background task.
	 *
	 * @see ::maybe_calculate_tracking_data().
	 */
	private static function calculate_tracking_data() {
		self::set_start_time();
		self::calculate_product_data();
	}

	/**
	 * Maybe calculate data. Also, handles the caching strategy.
	 *
	 * @return bool Returns true if the data are re-calculated, false otherwise.
	 */
	public static function maybe_calculate_tracking_data() {

		self::read_data();
		self::maybe_initialize_data();

		// Let's check if the array has pending data to calculate.
		if ( self::has_pending_calculations() ) {

			self::calculate_tracking_data();
			self::increase_iterations();
			self::set_option_data();

			return true;
		}

		return false;
	}

	/**
	 * Calculate product aggregation data.
	 *
	 * @return void
	 */
	private static function calculate_product_data() {

		global $wpdb;

		$data = &self::$data['products'];

		// Number of products in catalog.
		if ( ! isset( $data['products_count'] ) ) {
			$data['products_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}`
				WHERE `post_type` = 'product'
					AND `post_status` = 'publish'
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of Variable Products in catalog.
		if ( ! isset( $data['product_variable_count'] ) ) {
			$data['product_variable_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->posts}` v ON v.post_parent = p.ID
				WHERE p.post_type = 'product'
					AND p.post_status = 'publish'
					AND v.post_type = 'product_variation'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations in catalog.
		if ( ! isset( $data['product_variations_count'] ) ) {
			$data['product_variations_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT ID)
				FROM `{$wpdb->posts}`
				WHERE `post_type` = 'product_variation'
					AND `post_status` = 'publish'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of Categories.
		if ( ! isset( $data['product_categories_count'] ) ) {
			$data['product_categories_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT term_id)
				FROM `{$wpdb->term_taxonomy}`
				WHERE `taxonomy` = 'product_cat'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Min Quantity in catalog.
		if ( ! isset( $data['product_with_min_quantity_count'] ) ) {
			$data['product_with_min_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = 'minimum_allowed_quantity'
					AND(postmeta.meta_value != 0
						AND postmeta.meta_value != ''
						AND postmeta.meta_value IS NOT NULL)
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Max Quantity in catalog.
		if ( ! isset( $data['product_with_max_quantity_count'] ) ) {
			$data['product_with_max_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = 'maximum_allowed_quantity'
					AND(postmeta.meta_value != 0
						AND postmeta.meta_value != ''
						AND postmeta.meta_value IS NOT NULL)
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Group of in catalog.
		if ( ! isset( $data['product_with_group_of_count'] ) ) {
			$data['product_with_group_of_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = 'group_of_quantity'
					AND(postmeta.meta_value != 0
						AND postmeta.meta_value != ''
						AND postmeta.meta_value IS NOT NULL)
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Exclude from > Order rules in catalog.
		if ( ! isset( $data['product_with_exclude_from_order_rules_count'] ) ) {
			$data['product_with_exclude_from_order_rules_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = 'minmax_cart_exclude'
					AND postmeta.meta_value = 'yes'
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Exclude from > Order rules in catalog.
		if ( ! isset( $data['product_with_exclude_from_category_rules_count'] ) ) {
			$data['product_with_exclude_from_category_rules_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(*)
				FROM `{$wpdb->posts}` AS posts
				INNER JOIN `{$wpdb->postmeta}` AS postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_type = 'product'
					AND posts.post_status = 'publish'
					AND postmeta.meta_key = 'minmax_category_group_of_exclude'
					AND postmeta.meta_value = 'yes'
			"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of products with Combine Variations in catalog.
		if ( ! isset( $data['product_variable_with_combine_variations_count'] ) ) {
			$data['product_variable_with_combine_variations_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->posts}` v ON v.post_parent = p.ID
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product'
					AND p.post_status = 'publish'
					AND v.post_type = 'product_variation'
					AND pm.meta_key = 'allow_combination'
					AND pm.meta_value = 'yes'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations with Min Quantity in catalog.
		if ( ! isset( $data['product_variation_with_min_quantity_count'] ) ) {
			$data['product_variation_with_min_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product_variation'
					AND p.post_status = 'publish'
					AND pm.meta_key = 'variation_minimum_allowed_quantity'
					AND (pm.meta_value != 0
						AND pm.meta_value != ''
						AND pm.meta_value IS NOT NULL)
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations with Max Quantity in catalog.
		if ( ! isset( $data['product_variation_with_max_quantity_count'] ) ) {
			$data['product_variation_with_max_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product_variation'
					AND p.post_status = 'publish'
					AND pm.meta_key = 'variation_maximum_allowed_quantity'
					AND (pm.meta_value != 0
						AND pm.meta_value != ''
						AND pm.meta_value IS NOT NULL)
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations with Group of Quantity in catalog.
		if ( ! isset( $data['product_variation_with_group_of_quantity_count'] ) ) {
			$data['product_variation_with_group_of_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product_variation'
					AND p.post_status = 'publish'
					AND pm.meta_key = 'variation_group_of_quantity'
					AND (pm.meta_value != 0
						AND pm.meta_value != ''
						AND pm.meta_value IS NOT NULL)
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations with Exclude from > Order rules in catalog.
		if ( ! isset( $data['product_variation_with_exclude_from_order_rules_count'] ) ) {
			$data['product_variation_with_exclude_from_order_rules_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product_variation'
					AND p.post_status = 'publish'
					AND pm.meta_key = 'variation_minmax_cart_exclude'
					AND pm.meta_value = 'yes'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of variations with Exclude from > Category rules in catalog.
		if ( ! isset( $data['product_variation_with_exclude_from_category_rules_count'] ) ) {
			$data['product_variation_with_exclude_from_category_rules_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT p.ID)
				FROM `{$wpdb->posts}` p
				INNER JOIN `{$wpdb->postmeta}` pm ON p.ID = pm.post_id
				WHERE p.post_type = 'product_variation'
					AND p.post_status = 'publish'
					AND pm.meta_key = 'variation_minmax_category_group_of_exclude'
					AND pm.meta_value = 'yes'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of Categories with Min Quantity.
		if ( ! isset( $data['category_with_min_quantity_count'] ) ) {
			$data['category_with_min_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT tt.term_id)
				FROM `{$wpdb->term_taxonomy}` tt
				INNER JOIN `{$wpdb->termmeta}` tm ON tt.term_id = tm.term_id
				WHERE tt.taxonomy = 'product_cat'
					AND tm.meta_key = 'minimum_allowed_quantity'
					AND tm.meta_value IS NOT NULL
					AND tm.meta_value != ''
					AND tm.meta_value != '0'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of Categories with Max Quantity.
		if ( ! isset( $data['category_with_max_quantity_count'] ) ) {
			$data['category_with_max_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT tt.term_id)
				FROM `{$wpdb->term_taxonomy}` tt
				INNER JOIN `{$wpdb->termmeta}` tm ON tt.term_id = tm.term_id
				WHERE tt.taxonomy = 'product_cat'
					AND tm.meta_key = 'maximum_allowed_quantity'
					AND tm.meta_value IS NOT NULL
					AND tm.meta_value != ''
					AND tm.meta_value != '0'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Number of Categories with Group of Quantity.
		if ( ! isset( $data['category_with_group_of_quantity_count'] ) ) {
			$data['category_with_group_of_quantity_count'] = (int) $wpdb->get_var(
				"
				SELECT COUNT(DISTINCT tt.term_id)
				FROM `{$wpdb->term_taxonomy}` tt
				INNER JOIN `{$wpdb->termmeta}` tm ON tt.term_id = tm.term_id
				WHERE tt.taxonomy = 'product_cat'
					AND tm.meta_key = 'group_of_quantity'
					AND tm.meta_value IS NOT NULL
					AND tm.meta_value != ''
					AND tm.meta_value != '0'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Minimum Order Quantity.
		if ( ! isset( $data['minimum_order_quantity'] ) ) {
			$data['minimum_order_quantity'] = (int) $wpdb->get_var(
				"
				SELECT option_value
				FROM `{$wpdb->options}`
				WHERE option_name = 'woocommerce_minimum_order_quantity'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Maximum Order Quantity.
		if ( ! isset( $data['maximum_order_quantity'] ) ) {
			$data['maximum_order_quantity'] = (int) $wpdb->get_var(
				"
				SELECT option_value
				FROM `{$wpdb->options}`
				WHERE option_name = 'woocommerce_maximum_order_quantity'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Minimum Order Value.
		if ( ! isset( $data['minimum_order_value'] ) ) {
			$data['minimum_order_value'] = (int) $wpdb->get_var(
				"
				SELECT option_value
				FROM `{$wpdb->options}`
				WHERE option_name = 'woocommerce_minimum_order_value'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				return;
			}
		}

		// Maximum Order Value.
		if ( ! isset( $data['maximum_order_value'] ) ) {
			$data['maximum_order_value'] = (int) $wpdb->get_var(
				"
				SELECT option_value
				FROM `{$wpdb->options}`
				WHERE option_name = 'woocommerce_maximum_order_value'
				"
			);

			if ( self::time_or_memory_exceeded() ) {
				// If we don't unset now, it would exit and would need
				// an additional run just to remove the pending flag.
				unset( $data['pending'] );
				return;
			}
		}

		unset( $data['pending'] );
	}

	/**
	 * Check if all the main aggregations have pending data.
	 *
	 * @return bool Pending status.
	 */
	private static function has_pending_calculations() {

		if ( ! isset( self::$data['products']['pending'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if execution time is high or if available memory is almost consumed.
	 *
	 * @return bool Returns true if we're about to consume our available resources.
	 */
	private static function time_or_memory_exceeded() {
		return self::time_exceeded() || self::memory_exceeded();
	}

	/**
	 * Initialize data if they are empty month/year has changed.
	 *
	 * @return void
	 */
	private static function maybe_initialize_data() {

		// Default interval is -1 week.
		if ( defined( 'WC_CALYPSO_BRIDGE_TRACKER_FREQUENCY' ) ) {
			self::$invalidation_interval = '-1 day';
		}

		if (
			empty( self::$data )
			|| ! isset( self::$data['info']['started_time'] )
			|| self::$data['info']['started_time'] <= strtotime( self::$invalidation_interval )
		) {
			self::$data = array(
				'products' => array( 'pending' => true ),
				'info'     => array(
					'iterations'   => 0,
					'started_time' => time(),
				),
			);
		}
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the batch never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	private static function time_exceeded() {
		$finish = self::$start_time + 20; // 20 seconds
		return time() >= $finish;
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the batch process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	private static function memory_exceeded() {
		$memory_limit   = self::get_memory_limit() * 0.8; // 80% of max memory
		$current_memory = memory_get_usage( true );
		return $current_memory >= $memory_limit;
	}

	/**
	 * Get memory limit.
	 *
	 * @return int
	 */
	private static function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Increase iterations.
	 *
	 * @return void
	 */
	private static function increase_iterations() {
		if ( isset( self::$data['info'] ) && isset( self::$data['info']['iterations'] ) ) {
			self::$data['info']['iterations'] += 1;
		}
	}

	/**
	 * Set starting time.
	 *
	 * @return void
	 */
	private static function set_start_time() {
		self::$start_time = time();
	}

	/**
	 * Set data from option.
	 *
	 * @return void
	 */
	private static function read_data() {
		self::$data = get_option( 'woocommerce_mmq_tracking_data' );
	}

	/**
	 * Set option with data.
	 *
	 * @return void
	 */
	private static function set_option_data() {
		update_option( 'woocommerce_mmq_tracking_data', self::$data );
	}
}

WC_Min_Max_Quantities_Tracker::init();
