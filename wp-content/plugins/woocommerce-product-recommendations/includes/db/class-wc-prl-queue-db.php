<?php
/**
 * WC_PRL_Queue_DB class
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queue DB API class. Used for background generator queue.
 *
 * @class    WC_PRL_Queue_DB
 * @version  4.0.0
 */
class WC_PRL_Queue_DB {

	/**
	 * Queue db table name
	 *
	 * @var int
	 */
	protected $queue_table_name = 'woocommerce_prl_generator_queue';

	/**
	 * Cache group.
	 */
	protected $cache_group = 'wc_prl_background_queue_query';

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.0.0' );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// ...
	}

	/**
	 * Add queue item(s).
	 *
	 * @param array {
	 *     @type string $key       The main identifier (Required).
	 *     @type int    $source_id The source ID.
	 *     @type array  $data      Data.
	 *     @type bool   $force     Whether to force add the item.
	 * } $args It can also contain an array of data.
	 *
	 * @return int|false The number of items added, or false on failure.
	 */
	public function add( $args ) {

		if ( empty( $args ) ) {
			return false;
		}

		// Has multiple items?
		$has_multiple     = isset( $args[0] ) && is_array( $args[0] );
		$sql_values       = array();
		$sql_placeholders = array();

		if ( ! $has_multiple ) {
			$args = array( $args );
		}

		$now = time();
		foreach ( $args as $index => $item ) {

			// Make added time distinct for every queue item.
			$now += $index;
			$key  = $item['key'] ?? '';
			if ( ! $key ) {
				continue;
			}

			// Grab necessary data to save in the table.
			$data = $item['data'] ?? array();

			// Construct INSERT values.
			// Hint: Deployment ID column is used as a source ID.
			$sql_values         = array_merge( $sql_values, array( $key, absint( $item['source_id'] ), maybe_serialize( $data ), $now, 0 ) );
			$sql_placeholders[] = '(%s, %d, %s, %d, %d)';
		}

		if ( empty( $sql_values ) ) {
			return false;
		}

		WC_Cache_Helper::invalidate_cache_group( $this->cache_group );

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		global $wpdb;
		$table = $wpdb->prefix . $this->queue_table_name;
		$query = sprintf( "INSERT IGNORE INTO {$table}(`item_key`,`deployment_id`,`data`,`added_time`,`iterations`) VALUES %s", implode( ', ', $sql_placeholders ) );
		return $wpdb->query( $wpdb->prepare( $query, $sql_values ) );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Query queue items.
	 *
	 * @param array $args {
	 *      @type int    $key       The main identifier (Required).
	 *      @type int    $source_id The source ID.
	 *      @type array  $order_by  An array of column names and sort orders.
	 *      @type bool   $stale     Whether to query stale items.
	 *      @type int    $limit     The number of items to return.
	 *      @type int    $offset    The offset.
	 *      @type bool   $count     Whether to count the results.
	 * }
	 *
	 * @return bool
	 */
	public function query( $args ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'key'       => '',
				'count'     => false,
				'stale'     => null, // Possible values: null, false, true.
				'source_id' => null,
				'order_by'  => array( 'added_time' => 'ASC' ),
				'limit'     => -1,
				'offset'    => -1,
			)
		);

		// Key is required.
		if ( empty( $args['key'] ) ) {
			return false;
		}

		$key         = $args['key'];
		$is_like     = false !== strpos( $key, '%' );
		$table       = $wpdb->prefix . $this->queue_table_name;
		$is_counting = true === $args['count'];

		// Avoid caching specific keys to protect from clog up the cache.
		$is_using_cache = $is_like;
		$max_iterations = 10;

		// Build the query.
		$sql      = $is_counting ? "SELECT COUNT(*) FROM {$table}" : "SELECT * FROM {$table}";
		$join     = '';
		$where    = '';
		$order_by = '';

		$where_clauses = $is_like ? array( '`item_key` LIKE %s' ) : array( '`item_key` = %s' );
		$where_values  = array( $key );

		if ( true === $args['stale'] ) {
			$where_clauses[] = '`iterations` >= %d';
			$where_values[]  = $max_iterations;
		} elseif ( false === $args['stale'] ) {
			$where_clauses[] = '`iterations` < %d';
			$where_values[]  = $max_iterations;
		}

		// Source ID (e.g., deployment ID).
		if ( $args['source_id'] ) {
			$where_clauses[] = '`deployment_id` = %d';
			$where_values[]  = absint( $args['source_id'] );
		}

		// Order by clauses.
		$order_by_clauses = array();
		if ( $args['order_by'] && is_array( $args['order_by'] ) ) {
			foreach ( $args['order_by'] as $what => $how ) {
				$order_by_clauses[] = $table . '.' . esc_sql( strval( $what ) ) . ' ' . esc_sql( strval( $how ) );
			}
		}

		$order_by_clauses = empty( $order_by_clauses ) ? array( $table . '.id, ASC' ) : $order_by_clauses;

		$where    = ' WHERE ' . implode( ' AND ', $where_clauses );
		$order_by = ' ORDER BY ' . implode( ', ', $order_by_clauses );
		$limit    = $args['limit'] > 0 ? ' LIMIT ' . absint( $args['limit'] ) : '';
		$offset   = $args['offset'] > 0 ? ' OFFSET ' . absint( $args['offset'] ) : '';

		$sql .= $join . $where . $order_by . $limit . $offset;

		/**
		 * Check cache data.
		 */
		if ( $is_using_cache ) {
			$query_key  = md5( $sql );
			$params_key = md5( serialize( $args ) );
			$cache_key  = WC_Cache_Helper::get_cache_prefix( $this->cache_group ) . $query_key . $params_key;
			$data       = wp_cache_get( $cache_key, $this->cache_group, false, $found );
			if ( true === $found ) {
				return $data;
			}
		}

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$results = $is_counting ? $wpdb->get_var( $wpdb->prepare( $sql, $where_values ) ) : $wpdb->get_results( $wpdb->prepare( $sql, $where_values ), ARRAY_A );
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		if ( $is_using_cache ) {
			// Store to runtime cache.
			wp_cache_set( $cache_key, $results, $this->cache_group, DAY_IN_SECONDS );
		}

		return $results;
	}

	/**
	 * Update queue item.
	 *
	 * @param string $key Key.
	 * @param array  $data Data.
	 *
	 * @return int
	 */
	public function update( $key, $data ) {
		if ( ! empty( $data ) ) {

			global $wpdb;
			$table = $wpdb->prefix . $this->queue_table_name;
			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$updated = $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET `data` = %s WHERE `item_key` = %s LIMIT 1", maybe_serialize( $data ), $key ) );
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			WC_Cache_Helper::invalidate_cache_group( $this->cache_group );

			return $updated;
		}
	}

	/**
	 * Delete queue item.
	 *
	 * @param string $key Key.
	 *
	 * @return bool
	 */
	public function delete( $key ) {

		global $wpdb;
		$table = $wpdb->prefix . $this->queue_table_name;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE `item_key` = %s LIMIT 1", $key ) );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		WC_Cache_Helper::invalidate_cache_group( $this->cache_group );

		return $deleted;
	}

	/**
	 * Increment number of execution iterations.
	 *
	 * In order to prevent the loss of this number, we have implemented a mandatory update of the attempts at the beginning. Loss may occur due to script timeouts or memory overflow.
	 *
	 * @param string $key Key.
	 *
	 * @return bool
	 */
	public function increment_iterations( $key ) {

		global $wpdb;
		$table = $wpdb->prefix . $this->queue_table_name;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$increased = $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET `iterations` = `iterations` + 1 WHERE `item_key` = %s LIMIT 1", $key ) );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		WC_Cache_Helper::invalidate_cache_group( $this->cache_group );

		return $increased;
	}

	/**
	 * Force set a number of iterations in a queue item.
	 *
	 * @param string $key Key.
	 * @param int    $number The number of iterations to set.
	 *
	 * @return bool
	 */
	public function set_number_of_iterations( $key, $number ) {

		global $wpdb;
		$table = $wpdb->prefix . $this->queue_table_name;
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$set = $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET `iterations` = %d WHERE `item_key` = %s LIMIT 1", $number, $key ) );
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		WC_Cache_Helper::invalidate_cache_group( $this->cache_group );

		return $set;
	}
}
