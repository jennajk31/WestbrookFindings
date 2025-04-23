<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase --- Ignore the class file name
/**
 * Base class Parcelforce rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Shipping_Zones;

/**
 * International Parcelforce rate
 *
 * @since 2.5.3
 * @version 2.5.3
 */
abstract class Parcelforce_Rate extends RoyalMail_Rate {
	/**
	 * List of countries categorized as far east.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var array
	 */
	protected $far_east = array(
		'CN',
		'HK',
		'MO',
		'JP',
		'MN',
		'KP',
		'KR',
		'TW',
		'BN',
		'KH',
		'TL',
		'ID',
		'LA',
		'MY',
		'MM',
		'PH',
		'SG',
		'TH',
		'VN',
		'RU',
	);

	/**
	 * List of countries categorized as Australasia.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var array
	 */
	protected $australasia = array(
		'AU',
		'PF',
		'NU',
		'TO',
		'CX',
		'KI',
		'PG',
		'TV',
		'CC',
		'NR',
		'PN',
		'VU',
		'CK',
		'NC',
		'SB',
		'WF',
		'FJ',
		'NZ',
		'TK',
		'WS',
	);

	/**
	 * List of European countries that is not part of European Union.
	 *
	 * @version 2.5.36
	 * @since 2.5.36
	 *
	 * @var array
	 */
	protected $europe_non_eu = array(
		'AL',
		'AD',
		'AM',
		'AZ',
		'BY',
		'BA',
		'GE',
		'IS',
		'LI',
		'MD',
		'MC',
		'ME',
		'MK',
		'NO',
		'RU',
		'SM',
		'RS',
		'CH',
		'TR',
		'UA',
		'GB',
		'VA',
	);

	/**
	 * List of countries that have a Country-specific prices.
	 *
	 * @since 3.4.5
	 *
	 * @var array
	 */
	protected $custom_priced_countries = array(
		'FR',
		'ES',
		'CN',
		'IN',
		'ZA',
		'KR',
	);

	/**
	 * List of services that have a Country-specific prices.
	 *
	 * @since 3.4.5
	 *
	 * @var array
	 */
	protected $services_has_country_specific_prices = array(
		Services::PARCELFORCE_GLOBALPRIORITY,
		Services::PARCELFORCE_GLOBALVALUE,
	);

	/**
	 * Maximum inclusive compensation.
	 *
	 * All of our express services include compensation cover ranging from £100
	 * to £200, except globaleconomy.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_inclusive_compensation;

	/**
	 * Maximum total cover.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_total_cover;

	/**
	 * Get the international zone for the package.
	 *
	 * Sending within UK will be handled by RoyalMail rates.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param string $country_code Two-letter country code.
	 *
	 * @return string Zone.
	 */
	public function get_zone( string $country_code ): string {
		if ( $this->has_custom_price_for_country( $country_code ) ) {
			return Shipping_Zones::country_specific_pricing_zone( $country_code );
		}

		if ( in_array( $country_code, array( 'JE', 'GG', 'IM' ), true ) ) {
			return Shipping_Zones::ZONE_4;
		} elseif ( 'IE' === $country_code ) {
			return Shipping_Zones::ZONE_5;
		} elseif ( in_array( $country_code, array( 'BE', 'NL', 'LU' ), true ) ) {
			return Shipping_Zones::ZONE_6;
		} elseif ( in_array( $country_code, array( 'FR', 'DE', 'DK' ), true ) ) {
			return Shipping_Zones::ZONE_7;
		} elseif ( in_array( $country_code, array( 'IT', 'ES', 'PT', 'GR' ), true ) ) {
			return Shipping_Zones::ZONE_8;
		} elseif ( in_array( $country_code, WC()->countries->get_european_union_countries(), true ) ) {
			return Shipping_Zones::ZONE_9;
		} elseif ( in_array( $country_code, $this->europe_non_eu, true ) ) {
			return Shipping_Zones::ZONE_9_NON_EU;
		} elseif ( in_array( $country_code, array( 'US', 'CA' ), true ) ) {
			return Shipping_Zones::ZONE_10;
		} elseif ( in_array( $country_code, $this->far_east, true ) ) {
			return Shipping_Zones::ZONE_11;
		} elseif ( in_array( $country_code, $this->australasia, true ) ) {
			return Shipping_Zones::ZONE_11;
		} else {
			return Shipping_Zones::ZONE_12;
		}
	}

	/**
	 * Get volumetric weight.
	 *
	 * Since WC_Shipping_Royalmail_Rates::get_items converts the dimensions to
	 * mm, this is calculated in mm instead of cm.
	 *
	 * @see http://www.parcelforce.com/help-and-advice/sending/volumetric-charging.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param float|int $l Length.
	 * @param float|int $w Width.
	 * @param float|int $h Height.
	 *
	 * @return float Calculated weight in gram.
	 */
	public function get_volumetric_weight( $l, $w, $h ) {
		return ( $l * $w * $h ) / 5000;
	}

	/**
	 * Get quotes for this service.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array  $items          Items to be shipped.
	 * @param string $packing_method Selected packing method.
	 * @param string $country_code   Two-letter country code.
	 * @param array  $boxes          User-defined boxes.
	 * @param int    $instance_id    Shipping instance ID.
	 *
	 * @return array Quotes.
	 */
	public function get_quotes( $items, $packing_method, $country_code, $boxes = array(), $instance_id = '' ) {

		$this->set_boxes( $boxes );

		// By default, Parcelforce doesn't define boxes to ship internationally.
		// Max weight is 30kg. Max dimensions of 1.5m length and 3m length and
		// girth combined. With boxes empty, packing method is ignored at all.
		if ( empty( $this->boxes ) ) {
			return $this->get_quotes_based_on_items( $items, $country_code, $instance_id );
		}

		// Box packer will be used for 'per_item' or 'box_packing'. When 'per_item',
		// each box only packs one item.
		return $this->get_quotes_based_on_packages( $items, $packing_method, $country_code, $instance_id );
	}

	/**
	 * Set boxes from user-defined (in setting) boxes.
	 *
	 * @since 2.5.3
	 * @version 2.5.5
	 *
	 * @param array $boxes User-defined boxes.
	 */
	protected function set_boxes( $boxes = array() ) {
		if ( ! empty( $boxes ) ) {
			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
				);
			}
		}
	}

	/**
	 * Get quotes based on items.
	 *
	 * Each item in items is considered packaged already in a box. Which means
	 * dimensions stored in product property will be used to calculate the
	 * cost.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array  $items        Items to ship.
	 * @param string $country_code Two-letter country code.
	 * @param int    $instance_id  Shipping instance ID.
	 *
	 * @return mixed Quotes.
	 */
	protected function get_quotes_based_on_items( $items, $country_code, $instance_id = '' ) {
		$total_actual_weight     = 0;
		$total_volumetric_weight = 0;
		$total_valued_items      = 0;

		foreach ( $items as $item ) {
			for ( $i = 0; $i < $item->qty; $i++ ) {
				$total_actual_weight     += $item->weight;
				$total_volumetric_weight += $this->get_volumetric_weight(
					$item->length,
					$item->width,
					$item->height
				);

				$total_valued_items += $item->value;
			}
		}

		$chargeable_weight = ( $total_actual_weight > $total_volumetric_weight )
			? $total_actual_weight
			: $total_volumetric_weight;

		$zone             = $this->get_zone( $country_code );
		$bands            = $this->get_rate_bands( $zone );
		$options          = $this->get_instance_options( $instance_id );
		$quote            = 0;
		$ignore_max_cover = ( isset( $options['ignore_max_total_cover'] ) && 'yes' === $options['ignore_max_total_cover'] );

		foreach ( $bands as $max_weight => $price ) {
			if ( $chargeable_weight <= $max_weight ) {
				$quote = $price;
				break;
			}
		}

		// Don't return any quotes if there was no corresponding chargeable weight.
		if ( empty( $quote ) ) {
			return false;
		}

		// Don't return the quote if valued items is greater than maximum total
		// cover of the service.
		if ( ! $ignore_max_cover && $this->maximum_total_cover > 0 && $total_valued_items > $this->maximum_total_cover ) {
			return false;
		}

		// Additional compensation cost.
		$quote += $this->get_additional_compensation_cost( $total_valued_items );

		// Rate includes VAT.
		$quote     = $quote / 1.2;
		$rate_slug = $this->get_rate_slug();
		return array(
			$rate_slug => $quote / 100,
		);
	}

	/**
	 * Get quotes based on packages.
	 *
	 * This method will be used if user-defined boxes are not empty.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $country_code Two-letter country code.
	 * @param int    $instance_id Shipping instance ID.
	 *
	 * @return mixed Quote.
	 */
	protected function get_quotes_based_on_packages( $items, $packing_method, $country_code, $instance_id = '' ) {
		$zone        = $this->get_zone( $country_code );
		$packages    = $this->get_packages( $items, $packing_method );
		$class_quote = false;

		if ( $packages ) {
			foreach ( $packages as $package ) {
				$volumetric_weight = $this->get_volumetric_weight(
					$package->length,
					$package->width,
					$package->height
				);

				$chargeable_weight = ( $package->weight > $volumetric_weight )
					? $package->weight
					: $volumetric_weight;

				$bands            = $this->get_rate_bands( $zone );
				$options          = $this->get_instance_options( $instance_id );
				$quote            = 0;
				$ignore_max_cover = ( isset( $options['ignore_max_total_cover'] ) && 'yes' === $options['ignore_max_total_cover'] );

				foreach ( $bands as $max_weight => $price ) {
					if ( $chargeable_weight <= $max_weight ) {
						$quote = $price;
						break;
					}
				}

				// Don't return any quotes if there was no corresponding chargeable weight.
				if ( empty( $quote ) ) {
					return false;
				}

				// Don't return the quote if valued package is greater than maximum total
				// cover of the service.
				if ( ! $ignore_max_cover && $this->maximum_total_cover > 0 && $package->value > $this->maximum_total_cover ) {
					return false;
				}

				// Additional compensation cost.
				$quote += $this->get_additional_compensation_cost( $package->value );

				// Rate includes VAT.
				$quote = $quote / 1.2;

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes = array();
		$quotes[ str_replace( '_', '-', $this->rate_id ) ] = $class_quote / 100;

		return $quotes;
	}

	/**
	 * Get additional compensation cost.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @see http://www.parcelforce.com/help-and-advice/sending/enhanced-compensation.
	 *
	 * @param int $valued_item Valued item (product's price).
	 *
	 * @return int|double Additional compensation.
	 */
	public function get_additional_compensation_cost( $valued_item ) {
		// No compensation included for globaleconomy service and if it's under
		// max. inc. compensation there's no extra cost.
		if ( ! $this->maximum_inclusive_compensation || $valued_item <= $this->maximum_inclusive_compensation ) {
			return 0;
		}

		// £1.80 including VAT for the first extra £100 cover. The additional
		// cost is in pence since it will be added before converting back to £.
		$cost  = 180;
		$extra = ( $valued_item - $this->maximum_inclusive_compensation ) - 100;

		if ( 0 >= $extra ) {
			return $cost;
		}

		// Making sure the compensation cost does not take more than maximum total cover.
		if ( $valued_item > $this->maximum_total_cover ) {
			$extra = $this->maximum_total_cover - $this->maximum_inclusive_compensation;
		}

		// £4.50 including VAT for every subsequent £100. The additional cost
		// is in pence since it will be added before converting back to £.
		$cost += ceil( $extra / 100 ) * 450;

		/**
		 * Allow third party to modify the additional compensation value.
		 *
		 * @param float Additional compensation cost.
		 *
		 * @since 2.5.0
		 */
		return apply_filters(
			'woocommerce_shipping_royalmail_parcelforce_additional_compensation',
			$cost
		);
	}

	/**
	 * Check if the destination has a specific price or not.
	 *
	 * @param string $country_code Two-letter country code.
	 *
	 * @return bool
	 */
	public function has_custom_price_for_country( string $country_code ): bool {
		// Only some of online rates has a Country-specific prices.
		if ( WC_RoyalMail::ONLINE_SERVICE !== $this->rate_type ) {
			return false;
		}

		if ( ! in_array( $this->get_rate_slug(), $this->services_has_country_specific_prices, true ) ) {
			return false;
		}

		if ( in_array( $country_code, $this->custom_priced_countries, true ) ) {
			return true;
		}

		return false;
	}
}
