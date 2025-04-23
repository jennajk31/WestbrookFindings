<?php
/**
 * WC_Min_Max_Quantities_Quantity_Rules class
 *
 * @package  WooCommerce Min/Max Quantities
 * @since    5.2.0
 */

declare( strict_types=1 );

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Container class for a product or variations quantity rules
 *
 * @class    WC_Min_Max_Quantities_Quantity_Rules
 * @version  5.2.0
 */
class WC_Min_Max_Quantities_Quantity_Rules {
	/**
	 * Group of quantity magic string
	 *
	 * @var string
	 */
	public const GROUP_OF = 'group_of';
	/**
	 * Maximum magic string
	 *
	 * @var string
	 */
	public const MAXIMUM = 'maximum';
	/**
	 * Minimum magic string
	 *
	 * @var string
	 */
	public const MINIMUM = 'minimum';
	/**
	 * Transient group name.
	 *
	 * @var string
	 */
	public const TRANSIENT_GROUP = 'wc_min_max_quantity';

	/**
	 * Quantity rules that are allowed.
	 *
	 * @var string[]
	 */
	protected const QUANTITY_RULES = array( self::MINIMUM, self::MAXIMUM, self::GROUP_OF );
	/**
	 * Maps quantity rules to product meta keys.
	 *
	 * @var array<string, string>
	 */
	protected const PRODUCT_META_KEYS = array(
		self::MINIMUM  => 'minimum_allowed_quantity',
		self::MAXIMUM  => 'maximum_allowed_quantity',
		self::GROUP_OF => 'group_of_quantity',
	);
	/**
	 * Maps quantity rules to variation meta keys.
	 *
	 * @var array<string, string>
	 */
	protected const VARIATION_META_KEYS = array(
		self::MINIMUM  => 'variation_minimum_allowed_quantity',
		self::MAXIMUM  => 'variation_maximum_allowed_quantity',
		self::GROUP_OF => 'variation_group_of_quantity',
	);
	/**
	 * Transient name prefix.
	 */
	protected const TRANSIENT_NAME_PREFIX = 'wc_min_max_quantity_rules_';

	/**
	 * The product for this quantity rule.
	 *
	 * @var WC_Product
	 */
	protected WC_Product $product;

	/**
	 * Constructor.
	 *
	 * @param WC_Product|int $product The product for this quantity rule.
	 * @throws InvalidArgumentException If the product argument is invalid.
	 */
	public function __construct( $product ) {
		if ( is_int( $product ) ) {
			$product = wc_get_product( $product );
		}
		if ( ! $product instanceof WC_Product ) {
			throw new InvalidArgumentException( 'Product argument should be a WC_Product instance, or valid product ID (as an int)' );
		}

		$this->product = $product;
	}

	/**
	 * Retrieves the transient value for the product.
	 *
	 * @return array<string, int>|null
	 */
	protected function get_transient() {
		$transient_name    = self::TRANSIENT_NAME_PREFIX . $this->product->get_id();
		$transient_version = WC_Cache_Helper::get_transient_version( self::TRANSIENT_GROUP );
		$transient_value   = get_transient( $transient_name );

		if (
			isset( $transient_value['values'], $transient_value['version'] )
			&& $transient_value['version'] === $transient_version
			&& is_array( $transient_value['values'] )
		) {
			return $transient_value['values'];
		}

		return null;
	}

	/**
	 * Sets the transient value for the product.
	 *
	 * @param array $rules Array of quantity values.
	 * @return void
	 */
	protected function set_transient( array $rules ): void {
		$transient_name    = self::TRANSIENT_NAME_PREFIX . $this->product->get_id();
		$transient_version = WC_Cache_Helper::get_transient_version( self::TRANSIENT_GROUP );

		$transient_value = array(
			'version' => $transient_version,
			'values'  => $rules,
		);

		set_transient( $transient_name, $transient_value, DAY_IN_SECONDS * 30 );
	}

	/**
	 * Reads the correct meta from the given product.
	 *
	 * @param WC_Product $product The product to read metadata from.
	 *
	 * @return array<string, int>
	 */
	protected function read_rules_from_product_meta( WC_Product $product ): array {
		$meta_keys = $product->is_type( 'variation' ) ? static::VARIATION_META_KEYS : static::PRODUCT_META_KEYS;
		$rules     = array_fill_keys( static::QUANTITY_RULES, 0 );

		foreach ( static::QUANTITY_RULES as $quantity_rule ) {
			$meta_key = $meta_keys[ $quantity_rule ];

			$rules[ $quantity_rule ] = absint( $product->get_meta( $meta_key, true ) );
		}

		return $rules;
	}

	/**
	 * This gets the rules from the products categories.
	 *
	 * This method iterates over all categories for the product, it then reads the quantity rule for each category and
	 * calculates the correct value for each quantity rule. Specifically, the lowest minimum and group of value, and
	 * the highest maximum value.
	 *
	 * If the product is set to exclude category rules, this method will return early.
	 *
	 * @return array<string, int>|null
	 */
	protected function get_rules_for_categories(): ?array {
		// Since only parent products can have categories, we can only look at the parent product (if needed).
		$product = $this->product->is_type( 'variation' ) ? wc_get_product( $this->product->get_parent_id() ) : $this->product;

		// Should only happen where a product is a variation, but that variation doesn't have a valid parent.
		if ( ! $product instanceof WC_Product ) {
			return null;
		}

		$exclude_category = 'yes' === $product->get_meta( 'minmax_category_group_of_exclude', true );

		// If the product is set to exclude category rules, we return early since no category rules should be used.
		if ( $exclude_category ) {
			return null;
		}

		$categories = $product->get_category_ids();
		if ( ! $categories || empty( $categories ) || is_wp_error( $categories ) ) {
			return null;
		}

		$rules = array();
		// Iterate over all the quantity rules, and calculate the correct value for each.
		foreach ( static::QUANTITY_RULES as $quantity_rule ) {
			$meta_key = static::PRODUCT_META_KEYS[ $quantity_rule ];

			$found_rules = array();

			foreach ( $categories as $category ) {
				$found_rules[] = absint( get_term_meta( $category, $meta_key, true ) );
			}

			$found_rules = array_filter( $found_rules );

			if ( ! empty( $found_rules ) ) {
				// Maximum maximum, minimum minimum, and minimum group of.
				$rules[ $quantity_rule ] = self::MAXIMUM === $quantity_rule ? max( $found_rules ) : min( $found_rules );
			}
		}

		return $rules;
	}

	/**
	 * This gets the rules for the parent product.
	 *
	 * @return array<string, int>|null
	 */
	protected function get_rules_for_product(): ?array {
		$product = $this->product->is_type( 'variation' ) ? wc_get_product( $this->product->get_parent_id() ) : $this->product;

		// Should only happen where a product is a variation, but that variation doesn't have a valid parent.
		if ( ! $product instanceof WC_Product ) {
			return null;
		}

		return $this->read_rules_from_product_meta( $product );
	}

	/**
	 * This gets the rules for the product if it's a variation.
	 *
	 * @return array<string, int>|null
	 */
	protected function get_rules_for_variation(): ?array {
		// Bail early if the product is not a variation.
		if ( ! $this->product->is_type( 'variation' ) ) {
			return null;
		}

		$parent_product = wc_get_product( $this->product->get_parent_id() );

		// Should only happen where a product is a variation, but that variation doesn't have a valid parent.
		if ( ! $parent_product instanceof WC_Product ) {
			return null;
		}

		$allow_combination = 'yes' === $parent_product->get_meta( 'allow_combination', true );
		$min_max_rules     = 'yes' === $this->product->get_meta( 'min_max_rules', true );

		// Return early if we don't allow variation specific rules.
		if ( $allow_combination || ! $min_max_rules ) {
			return null;
		}

		return $this->read_rules_from_product_meta( $this->product );
	}

	/**
	 * Get quantity rules for the product.
	 *
	 * This method will calculate the quantity rules for the product, based on the product's categories and any overiden
	 * values on the product.
	 *
	 * The following rules are used to calculate the quantity rules:
	 *  - If `minmax_category_group_of_exclude` is set to `yes`, the category rules are ignored.
	 *  - If not, then the lowest minimum and group of value, and the highest maximum value from all the product categories are used.
	 *  - The product's own quantity rules are then merged with the category rules. The product's own rules take precedence.
	 *    If the product is a variation, the parent product's rules are used.
	 *  - If the product is a variation, and the variation has specific rules, these are merged with the rules.
	 *    The variation rules take precedence. If`allow_combination` is set on the parent product, then variation rules are ignored.
	 *
	 * @since 5.2.0
	 * @return array<string, int>
	 */
	public function get(): array {
		$rules = $this->get_transient();

		if ( ! empty( $rules ) ) {
			return $rules;
		}

		$rules = array_fill_keys( static::QUANTITY_RULES, 0 );
		// @see https://www.php.net/manual/en/function.array-merge.php
		$rules = array_merge(
			$rules,
			array_filter( (array) $this->get_rules_for_categories() ),
			array_filter( (array) $this->get_rules_for_product() ),
			array_filter( (array) $this->get_rules_for_variation() )
		);

		// Ensure that the chosen values are valid.
		// And so we adjust them if necessary using the same technique as used when saving the values.
		$rules[ self::MINIMUM ] = WC_Min_Max_Quantities::adjust_min_quantity( $rules[ self::MINIMUM ], $rules[ self::GROUP_OF ] );
		$rules[ self::MAXIMUM ] = WC_Min_Max_Quantities::adjust_max_quantity( $rules[ self::MAXIMUM ], $rules[ self::GROUP_OF ], $rules[ self::MINIMUM ] );

		array_walk( $rules, 'absint' );

		$this->set_transient( $rules );

		return $rules;
	}
}
