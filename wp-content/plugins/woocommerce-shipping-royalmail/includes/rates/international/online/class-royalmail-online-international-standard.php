<?php
/**
 * International Standard rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Shipping_Zones;

/**
 * RoyalMail_Online_International_Standard class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See International Standard page 17 - 18.
 */
class RoyalMail_Online_International_Standard extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Pricing bands
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Online_International_Standard rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
	}

	/**
	 * Initializes Pricing bands - Europe 1, Europe 2, Europe 3, World Zone 1, World Zone 2, World Zone 3 (previously Zone 1)
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER       => array(
					100 => array( 280, 280, 280, 280, 280, 280 ),
				),
				Packaging::LARGE_LETTER => array(
					100 => array( 325, 325, 325, 420, 420, 420 ),
					250 => array( 475, 475, 475, 635, 755, 650 ),
					500 => array( 585, 585, 585, 885, 1090, 920 ),
					750 => array( 695, 695, 695, 1180, 1500, 1230 ),
				),
				Packaging::PACKET       => array(
					100  => array( 555, 605, 645, 745, 850, 820 ),
					250  => array( 555, 605, 645, 900, 960, 985 ),
					500  => array( 735, 815, 890, 1310, 1480, 1580 ),
					750  => array( 860, 950, 1060, 1615, 1820, 1870 ),
					1000 => array( 990, 1075, 1230, 1915, 2180, 2260 ),
					1250 => array( 1035, 1230, 1360, 2135, 2510, 2615 ),
					1500 => array( 1035, 1230, 1570, 2330, 2850, 2895 ),
					2000 => array( 1200, 1400, 1825, 2450, 3035, 3025 ),
				),
			),
			'2025' => array(
				Packaging::LETTER       => array(
					100 => array( 320, 320, 320, 320, 320, 320 ),
				),
				Packaging::LARGE_LETTER => array(
					100 => array( 340, 340, 340, 420, 420, 420 ),
					250 => array( 505, 505, 505, 695, 825, 720 ),
					500 => array( 645, 645, 645, 1000, 1230, 1070 ),
					750 => array( 870, 870, 870, 1510, 1710, 1620 ),
				),
				Packaging::PACKET       => array(
					100  => array( 580, 630, 675, 780, 890, 1175 ),
					250  => array( 580, 630, 675, 940, 1005, 1175 ),
					500  => array( 770, 850, 930, 1370, 1545, 1685 ),
					750  => array( 900, 995, 1110, 1690, 1900, 1990 ),
					1000 => array( 1035, 1125, 1285, 2000, 2280, 2285 ),
					1250 => array( 1080, 1285, 1420, 2230, 2625, 2675 ),
					1500 => array( 1080, 1285, 1640, 2435, 2980, 3040 ),
					2000 => array( 1255, 1465, 1905, 2560, 3170, 3175 ),
				),
			),
		);
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::INTERNATIONAL_STANDARD;
	}

	/**
	 * Get quotes for this rate
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 * @param array  $boxes User-defined boxes.
	 * @param int    $instance_id Instance ID.
	 *
	 * @return array{ 'international-standard': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$standard_quote = false;

		if ( ! empty( $boxes ) ) {
			$this->boxes = array();

			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
					'weight'     => 2000,
				);
			}
		} else {
			$this->boxes = $this->international_default_box;
		}

		$zone                    = $this->get_zone( $destination );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );
		$packages                = $this->get_packages( $items, $packing_method );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 20.
				}

				$this->validate_package( $package );

				if ( in_array( $package->id, array( Packaging::PACKET, Packaging::PRINTED_PAPERS ), true ) && 900 < ( $package->length + $package->width + $package->height ) ) {
					return false; // Exceeding parcels requirement, unpacked.
				}

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$bands   = $this->get_rate_bands( $package->id );
				$quote   = 0;
				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( $package->weight <= $band ) {
						switch ( $zone ) {
							case Shipping_Zones::ZONE_EUR_1:
								$quote += $value[0];
								break;
							case Shipping_Zones::ZONE_EUR_2:
								$quote += $value[1];
								break;
							case Shipping_Zones::ZONE_EUR_3:
							case Shipping_Zones::ZONE_EU:
								$quote += $value[2];
								break;
							case Shipping_Zones::ZONE_1:
								$quote += $value[3];
								break;
							case Shipping_Zones::ZONE_2:
								$quote += $value[4];
								break;
							case Shipping_Zones::ZONE_3:
								// Fallback to zone 1 for older prices.
								$quote += isset( $value[5] ) ? $value[5] : $value[3];
								break;
						}
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return;
				}

				$standard_quote += $quote;
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $standard_quote / 100;

		return $quotes;
	}
}
