<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase --- Ignore the class file name
/**
 * Base class for RoyalMail rate.
 *
 * @package WC_RoyalMail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\BoxPacker;
use WooCommerce\BoxPacker\WC_Boxpack;
use WooCommerce\BoxPacker\Package;

/**
 * RoyalMail Rate class
 */
abstract class RoyalMail_Rate {

	const MAX_LARGE_LETTER_WEIGHT = 750;

	const MAX_LETTER_WEIGHT = 100;

	/**
	 * List of country codes under EUR Zone 1.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Corsica as it's part of France.
	 *
	 * @var array EUR Zone 1 country codes
	 */
	protected $europe_zone_1 = array(
		'IE',
		'DE',
		'DK',
		'FR',
		'MC',
	);

	/**
	 * List of country codes under EUR Zone 2.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Azores, Madeira as it's part of Portugal.
	 * Excluding Balaeric Island, and Canary Island as it's part of Spain.
	 *
	 * @var array EUR Zone 2 country codes
	 */
	protected $europe_zone_2 = array(
		'AT',
		'BE',
		'BG',
		'HR',
		'CY',
		'CZ',
		'EE',
		'FI',
		'GR',
		'HU',
		'IT',
		'LV',
		'LT',
		'LU',
		'MT',
		'NL',
		'PL',
		'PT',
		'RO',
		'SK',
		'SI',
		'ES',
		'SE',
	);

	/**
	 * List of country codes under EUR Zone 3.
	 *
	 * See https://www.royalmail.com/sites/royalmail.com/files/2023-09/our-prices-october-2023-ta.pdf
	 * Page 10.
	 *
	 * Excluding Kosovo as it's not part of ISO Countries.
	 *
	 * @var array EUR Zone 3 country codes
	 */
	protected $europe_zone_3 = array(
		'AL',
		'AD',
		'AM',
		'AZ',
		'BY',
		'BA',
		'FO',
		'GE',
		'GI',
		'GL',
		'IS',
		'KZ',
		'KG',
		'LI',
		'MD',
		'ME',
		'MK',
		'NO',
		'RU',
		'SM',
		'RS',
		'CH',
		'TJ',
		'TR',
		'TM',
		'UA',
		'UZ',
		'VA',
	);

	/**
	 * List of country codes under World Zone 2.
	 *
	 * @var array World Zone country codes
	 */
	protected $world_zone_2 = array(
		'AU',
		'PW',
		'IO',
		'CX',
		'CC',
		'CK',
		'FJ',
		'PF',
		'TF',
		'KI',
		'MO',
		'NR',
		'NC',
		'NZ',
		'PG',
		'NU',
		'NF',
		'LA',
		'PN',
		'TO',
		'TV',
		'WS',
		'AS',
		'SG',
		'SB',
		'TK',
	);

	/**
	 * List of country codes under World Zone 3.
	 *
	 * @var array World Zone country codes
	 */
	protected $world_zone_3 = array(
		'US',
	);

	/**
	 * List of country codes where Printed Papers max weight
	 * is limited to 2kg instead of 5kg.
	 *
	 * @var array Country codes
	 */
	protected $printed_papers_2kg_limited_countries = array(
		'CA',
		'KH',
	);

	/**
	 * Name of the rate (e.g. 'special_delivery_1pm').
	 *
	 * @var string ID/Name of rate
	 */
	protected $rate_id = '';

	/**
	 * Rate price type.
	 *
	 * @var string
	 */
	public $rate_type;

	/**
	 * Bands is an array of pricing bands where key is coverage / compensation
	 * for loss or damange (this will numeric value), or size (e.g. 'letter');
	 * value is an key-value array where key is package weight and value is the
	 * price. Data is nested within top-level array where key is the is the
	 * calendar year during which the pricing bands became active.
	 *
	 * @var array Pricing bands
	 */
	protected $bands = array();

	/**
	 * Shipping boxes.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array();

	/**
	 * Array of years and the dates on which the pricing becomes effective for
	 * that year (in YYYY-MM-DD format).
	 *
	 * @var array Rate year data
	 */
	protected $rate_year_starts = array(
		'2024' => '2024-10-07',
		'2025' => '2025-04-07',
	);

	/**
	 * Refer to https://www.royalmail.com/sites/royalmail.com/files/2021-03/royal-mail-our-prices-april-2021.pdf page 9.
	 *
	 * @var array
	 */
	protected $international_default_box = array(
		Packaging::LETTER        => array(
			'length' => 240, // Max L in mm.
			'width'  => 165, // Max W in mm.
			'height' => 5,   // Max H in mm.
			'weight' => self::MAX_LETTER_WEIGHT, // Max Weight in grams.
		),
		Packaging::LARGE_LETTER  => array(
			'length' => 353,
			'width'  => 250,
			'height' => 25,
			'weight' => self::MAX_LARGE_LETTER_WEIGHT,
		),
		'long-parcel'            => array(
			'length' => 600,
			'width'  => 150,
			'height' => 150,
			'weight' => 2000,
		),
		'square-parcel'          => array(
			'length' => 300,
			'width'  => 300,
			'height' => 300,
			'weight' => 2000,
		),
		'parcel'                 => array(
			'length' => 450,
			'width'  => 225,
			'height' => 225,
			'weight' => 2000,
		),
		Packaging::MEDIUM_PARCEL => array(
			'length' => 610,
			'width'  => 460,
			'height' => 460,
			'weight' => 20000,
		),
	);

	/**
	 * Additional rates for printed papers.
	 *
	 * @var array.
	 */
	protected $additional_rates;

	/**
	 * Box packer library to use.
	 *
	 * @var string
	 */
	protected $box_packer_library;

	/**
	 * Class constructor.
	 *
	 * @param string $rate_type          Rate type.
	 * @param string $box_packer_library Box packer library.
	 */
	public function __construct( $rate_type, $box_packer_library ) {
		$this->rate_id            = $this->get_rate_id();
		$this->rate_type          = $rate_type;
		$this->box_packer_library = $box_packer_library;
		$this->setup();
	}

	/**
	 * Set up the rate.
	 */
	public function setup() {
		// Set up the rate.
	}

	/**
	 * Name of the rate (e.g. 'special_delivery_1pm').
	 *
	 * @return string
	 */
	public function get_rate_id() {
		$rate_slug = $this->get_rate_slug();

		return str_replace( '-', '_', $rate_slug );
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	abstract public function get_rate_slug();

	/**
	 * Get calendar year for currently effective rates.
	 *
	 * @return string Calendar year (e.g. '2018').
	 */
	public function get_rate_year() {
		// Get first year in list and use it as a default.
		reset( $this->rate_year_starts );
		$current_rate_year = key( $this->rate_year_starts );

		$current_time = gmdate( 'U' );
		foreach ( $this->rate_year_starts as $year => $start ) {
			if ( $current_time > strtotime( $start ) ) {
				$current_rate_year = $year;
			}
		}
		return $current_rate_year;
	}

	/**
	 * Get this rates pricing Bands.
	 *
	 * @param mixed $band Coverage or compensation for loss / damage, or size.
	 *                    If not specified all prices are returned.
	 *
	 * @return array
	 */
	public function get_rate_bands( $band = false ) {
		$rate_year = $this->get_rate_year();

		// Get the price data for the rate year, or the last defined year if not defined.
		if ( isset( $this->bands[ $rate_year ] ) ) {
			$current_bands = $this->bands[ $this->get_rate_year() ];
		} else {
			$current_bands = end( $this->bands );
		}

		/**
		 * Allow third party to modify the boxes.
		 *
		 * @param array Current bands.
		 *
		 * @since 2.2.2
		 */
		$bands = apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_bands', $current_bands );
		if ( $band ) {
			return isset( $bands[ $band ] ) ? $bands[ $band ] : array();
		} else {
			return $bands;
		}
	}

	/**
	 * Add additional rates for printed papers.
	 */
	public function apply_additional_rates() {
		$rate_year = $this->get_rate_year();

		if ( empty( $this->additional_rates ) ) {
			return;
		}

		// Get the price data for the rate year, or the last defined year if not defined.
		$used_rates = isset( $this->additional_rates[ $rate_year ] ) ? $this->additional_rates[ $rate_year ] : end( $this->additional_rates );

		if ( isset( $this->additional_rates[ $rate_year ] ) ) {
			$used_rates     = $this->additional_rates[ $rate_year ];
			$used_rate_year = $rate_year;
		} else {
			$used_rates     = end( $this->additional_rates );
			$used_rate_year = array_key_last( $this->additional_rates );
		}

		foreach ( $used_rates as $key => $values ) {
			for ( $a = 2250; $a <= 5000; $a += 250 ) {
				$previous_rate = $this->bands[ $used_rate_year ][ $key ][ ( $a - 250 ) ];

				if ( is_array( $values ) ) {
					foreach ( $values as $val_idx => $val_rate ) {
						$this->bands[ $used_rate_year ][ $key ][ $a ][ $val_idx ] = ( $previous_rate[ $val_idx ] + $values[ $val_idx ] );
					}
				} else {
					$this->bands[ $used_rate_year ][ $key ][ $a ] = ( $previous_rate + $values );
				}
			}
		}
	}

	/**
	 * Get signed cost based on year.
	 *
	 * @param string $cost_var Which cost value to return signed_for_cost or signed_for_package_cost.
	 * @return int
	 */
	protected function get_signed_cost( $cost_var ) {
		$rate_year = $this->get_rate_year();

		// Get the price data for the rate year, or the last defined year if not defined.
		if ( ! isset( $this->{$cost_var} ) ) {
			$cost = 0;
		} elseif ( isset( $this->{$cost_var}[ $rate_year ] ) ) {
			$cost = $this->{$cost_var}[ $rate_year ];
		} else {
			$cost = end( $this->{$cost_var} );
		}

		return $cost;
	}

	/**
	 * Get this rates boxes.
	 *
	 * @return array
	 */
	public function get_rate_boxes() {
		/**
		 * Allow third party to modify the boxes.
		 *
		 * @param array Boxes.
		 *
		 * @since 2.2.2
		 */
		return apply_filters( 'woocommerce_shipping_royalmail_' . $this->rate_id . '_rate_boxes', $this->boxes );
	}

	/**
	 * Get all European countries. Combination of Europe zone 1, 2, and 3.
	 *
	 * @return array
	 */
	public function get_all_european_countries() {
		return array_merge( $this->europe_zone_1, $this->europe_zone_2, $this->europe_zone_3 );
	}

	/**
	 * Get the zone for the package.
	 *
	 * @param string $country_code $country_code Two-letter country code.
	 *
	 * @return string Zone.
	 */
	public function get_zone( string $country_code ): string {
		if ( 'GB' === $country_code ) {
			return Shipping_Zones::ZONE_UK;
		} elseif ( in_array( $country_code, $this->europe_zone_1, true ) ) {
			return Shipping_Zones::ZONE_EUR_1;
		} elseif ( in_array( $country_code, $this->europe_zone_2, true ) ) {
			return Shipping_Zones::ZONE_EUR_2;
		} elseif ( in_array( $country_code, $this->europe_zone_3, true ) ) {
			return Shipping_Zones::ZONE_EUR_3;
		} elseif ( in_array( $country_code, WC()->countries->get_european_union_countries(), true ) ) {
			return Shipping_Zones::ZONE_EU;
		} elseif ( in_array( $country_code, $this->world_zone_2, true ) ) {
			return Shipping_Zones::ZONE_2;
		} elseif ( in_array( $country_code, $this->world_zone_3, true ) ) {
			return Shipping_Zones::ZONE_3;
		} else {
			return Shipping_Zones::ZONE_1;
		}
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_letter( $box ) {
		if ( $box->get_weight() > self::MAX_LETTER_WEIGHT ) {
			return false;
		}
		if ( $box->get_length() > 240 ) {
			return false;
		}
		if ( $box->get_width() > 165 ) {
			return false;
		}
		if ( $box->get_height() > 5 ) {
			return false;
		}
		return true;
	}

	/**
	 * See if box could be a letter.
	 *
	 * @param  object $box Box.
	 * @return bool
	 */
	public function box_is_large_letter( $box ) {
		if ( $box->get_weight() > self::MAX_LARGE_LETTER_WEIGHT ) {
			return false;
		}
		if ( $box->get_length() > 353 ) {
			return false;
		}
		if ( $box->get_width() > 250 ) {
			return false;
		}
		if ( $box->get_height() > 25 ) {
			return false;
		}
		return true;
	}

	/**
	 * Pack items into boxes and return results.
	 *
	 * @param array  $items Items to pack.
	 * @param string $method Method to pack items (e.g. 'Pack items individually').
	 * @param string $country_code The two-letter country code of the destination.
	 * @param bool   $printed_papers If this is for Printed Papers rates.
	 * @param bool   $books If this is for Printed Papers rates only for books.
	 * @param bool   $tube If this is for tube rates.
	 *
	 * @return array Packed items.
	 * @since 1.0.0
	 * @version 2.5.3
	 */
	public function get_packages( $items, $method, $country_code = '', $printed_papers = false, $books = false, $tube = false ) {
		$packages  = array();
		$boxpacker = $this->get_boxpack( $country_code, $printed_papers, $books, $tube );

		if ( empty( $items ) ) {
			return $packages;
		}

		if ( 'per_item' === $method ) {
			$packages = $this->get_packages_using_per_item_method( $items, $boxpacker );
		} else {
			$packages = $this->get_packages_using_box_packing_method( $items, $boxpacker );
		}

		return $packages;
	}

	/**
	 * Get box packer instance populated with defined boxes.
	 *
	 * @param string $country_code The two-letter country code of the destination.
	 * @param bool   $printed_papers If this is for Printed Papers rates.
	 * @param bool   $books If this is for Printed Papers rates only for books.
	 * @param bool   $tube If this is for tube rates.
	 *
	 * @return BoxPacker\Abstract_Packer Box packer.
	 * @since 2.5.3
	 * @version 3.5.0
	 */
	protected function get_boxpack( $country_code = '', $printed_papers = false, $books = false, $tube = false ) {
		$boxpack = ( new WC_Boxpack( 'mm', 'g', $this->box_packer_library ) )->get_packer();

		// Define boxes.
		foreach ( $this->get_rate_boxes() as $box_id => $box ) {

			// Medium parcel does not apply for printed papers.
			if ( true === $printed_papers && Packaging::MEDIUM_PARCEL === $box_id ) {
				continue;
			}

			if ( true !== $tube && Packaging::TUBE === $box_id ) {
				continue;
			}

			$newbox = $boxpack->add_box(
				$box['length'],
				$box['width'],
				$box['height'],
				isset( $box['box_weight'] ) ? $box['box_weight'] : 0
			);

			if ( is_numeric( $box_id ) && $this->box_is_letter( $newbox ) ) {
				$box_id = Packaging::LETTER;
				$newbox->set_type( 'envelope' );
			} elseif ( is_numeric( $box_id ) && $this->box_is_large_letter( $newbox ) ) {
				$box_id = Packaging::LARGE_LETTER;
				$newbox->set_type( 'envelope' );
			} elseif ( strstr( $box_id, Packaging::PACKET ) || strstr( $box_id, 'parcel' ) ) {
				$newbox->set_type( Packaging::PACKET );
			} elseif ( strstr( $box_id, Packaging::TUBE ) ) {
				$newbox->set_type( Packaging::TUBE );
			}

			$newbox->set_id( $box_id );

			if ( ! empty( $box['weight'] ) ) {
				$newbox->set_max_weight( $box['weight'] );
			}

			// Printed Papers max weight adjustments.
			if ( $printed_papers ) {
				if ( in_array( $country_code, $this->printed_papers_2kg_limited_countries, true ) ) {
					$newbox->set_max_weight( 2000 );
				} elseif ( 'IE' === $country_code ) {
					$max_weight = ( $books ) ? 5000 : 2000;
					$newbox->set_max_weight( $max_weight );
				} else {
					$newbox->set_max_weight( 5000 );
				}
			}
		}

		return $boxpack;
	}

	/**
	 * Get packages using per item method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array                     $items   Items to pack.
	 * @param BoxPacker\Abstract_Packer $boxpack Box packer instance.
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_per_item_method( $items, $boxpack ) {
		$packages = array();
		foreach ( $items as $item ) {
			$boxpack->clear_items();
			$boxpack->add_item(
				$item->length,
				$item->width,
				$item->height,
				$item->weight,
				$item->value
			);

			$boxpack->pack();
			$item_packages = $boxpack->get_packages();

			for ( $i = 0; $i < $item->qty; $i++ ) {
				$packages = array_merge( $packages, $item_packages );
			}
		}

		return $packages;
	}

	/**
	 * Get packages using box packing method.
	 *
	 * @since 2.5.3
	 * @version 2.5.3
	 *
	 * @param array                     $items   Items to pack.
	 * @param BoxPacker\Abstract_Packer $boxpack Box packer instance.
	 *
	 * @return array Packages.
	 */
	protected function get_packages_using_box_packing_method( $items, $boxpack ) {
		foreach ( $items as $item ) {
			$boxpack->add_item(
				$item->length,
				$item->width,
				$item->height,
				$item->weight,
				$item->value,
				array(),
				$item->qty
			);
		}

		// Pack it.
		$boxpack->pack();

		return $boxpack->get_packages();
	}

	/**
	 * Gets the instance options.
	 *
	 * @since 2.5.7
	 * @param int $instance_id The ID of the shipping instance.
	 * @return string $option_value
	 */
	public function get_instance_options( $instance_id = '' ) {
		if ( empty( $instance_id ) ) {
			return array();
		}

		return get_option( 'woocommerce_royal_mail_' . $instance_id . '_settings', array() );
	}

	/**
	 * Validates package id and weights
	 *
	 * @param Package $package Package object.
	 */
	protected function validate_package( Package $package ) {

		if ( Packaging::LETTER !== $package->id && Packaging::LARGE_LETTER !== $package->id && Packaging::PRINTED_PAPERS !== $package->id && Packaging::MEDIUM_PARCEL !== $package->id ) {
			$package->id = Packaging::PACKET;
		}

		if ( Packaging::LARGE_LETTER === $package->id && $package->weight > self::MAX_LARGE_LETTER_WEIGHT ) {
			$package->id = Packaging::PACKET;
		}

		if ( Packaging::LETTER === $package->id && $package->weight > self::MAX_LETTER_WEIGHT ) {
			$package->id = Packaging::PACKET;
		}
	}

	/**
	 * Separate all qualifying Printed Papers items, get them packed
	 * according to type and weight, and return the packages for
	 * quoting.
	 *
	 * @param array  $items $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 * @param string $packing_method Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Printed Papers packages.
	 */
	protected function get_printed_papers_packages( &$items, $country_code, $packing_method ) {

		/**
		 * Separate printed papers and standard items
		 * before sending to the box packer.
		 */
		$printed_papers = $this->get_qualifying_printed_papers_items( $items, $country_code );

		/**
		 * If there are printed papers items and the destination is
		 * the Republic of Ireland, separate the books from the rest
		 * of the Printed Papers items.
		 */
		if ( ! empty( $printed_papers ) && 'IE' === $country_code ) {
			$printed_papers_books         = $this->get_printed_papers_books( $printed_papers );
			$printed_paper_books_packages = $this->get_packages( $printed_papers_books, $packing_method, $country_code, true, true );
		}

		// Get Printed Papers packages.
		$printed_paper_packages = $this->get_packages( $printed_papers, $packing_method, $country_code, true );

		/**
		 * If books were separated, combine them now with the rest
		 * of the printed papers items.
		 */
		if ( ! empty( $printed_paper_books_packages ) ) {
			$printed_paper_packages = array_merge( $printed_paper_packages, $printed_paper_books_packages );
		}

		// Set each printed papers package ID to 'printed-papers'.
		foreach ( $printed_paper_packages as $idx => $package ) {
			$package->id = Packaging::PRINTED_PAPERS;
		}

		return $printed_paper_packages;
	}

	/**
	 * Separate all qualifying Tube items, get them packed
	 * according to type and weight, and return the packages for
	 * quoting.
	 *
	 * @param array  $items $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 * @param string $packing_method Method to pack items (e.g. 'Pack items individually').
	 *
	 * @return array Tube packages.
	 */
	protected function get_tube_packages( &$items, $country_code, $packing_method ) {

		/**
		 * Separate tubes and standard items
		 * before sending to the box packer.
		 */
		$tubes = $this->get_qualifying_tube_items( $items, $country_code );

		if ( empty( $tubes ) ) {
			return array();
		}

		$tubes_packages = $this->get_packages( $tubes, $packing_method, $country_code, false, false, true );

		// Set each tube package ID to 'tube'.
		foreach ( $tubes_packages as $idx => $package ) {
			$package->id = Packaging::TUBE;
		}

		return $tubes_packages;
	}

	/**
	 * Find and separate printed papers from the standard items
	 * so we can handle differently.
	 *
	 * Any qualifying Printed Papers items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @see https://www.royalmail.com/price-finder/printed-papers
	 *
	 * @param array  $items Items to pack.
	 * @param string $country_code Two-letter country code.
	 *
	 * @return array Printed Papers items.
	 */
	protected function get_qualifying_printed_papers_items( &$items, $country_code ) {
		$printed_papers = array();
		foreach ( $items as $key => $item ) {
			if ( $item->printed_papers ) {
				// Items over 5kg don't qualify for Printed Papers.
				if ( $item->weight > 5000 ) {
					continue;
				}

				/**
				 * If the destination country limits Printed Papers to 2kg max
				 * and this item is over 2kg, don't ship it with Printed Papers
				 * rates.
				 */
				if ( $item->weight > 2000 && in_array( $country_code, $this->printed_papers_2kg_limited_countries, true ) ) {
					continue;
				}

				/**
				 * Only books can be over 2kg and be shipped with the Printed Papers
				 * service to the Republic of Ireland. Other bound papers/pamphlets
				 * cannot exceed 2kg or they don't qualify for Printed Papers.
				 */
				if ( $item->weight > 2000 && ! $item->book && 'IE' === $country_code ) {
					continue;
				}

				$printed_papers[] = $item;
				unset( $items[ $key ] );
			}
		}

		return $printed_papers;
	}

	/**
	 * Find and separate printed papers books from other
	 * printed papers items for separate calculation if necessary.
	 * Example: Republic of Ireland only allows books to be over
	 * 2kg and up to 5kg. Other printed papers items must be 2kg
	 * or less. So we need to split books from other printed papers
	 * items for separate handling/rules in that case.
	 *
	 * Any qualifying Printed Papers book items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @param array $printed_papers_items Printed Papers items.
	 *
	 * @return array Printed Papers books.
	 */
	protected function get_printed_papers_books( &$printed_papers_items ) {
		$books = array();
		foreach ( $printed_papers_items as $key => $item ) {
			if ( $item->book ) {

				$books[] = $item;
				unset( $printed_papers_items[ $key ] );
			}
		}

		return $books;
	}

	/**
	 * Find and separate tubes from the standard items
	 * so we can handle differently.
	 *
	 * Any qualifying Tube items will be returned in an
	 * array and will also be unset from the $items array that is
	 * passed by reference.
	 *
	 * @param array $items Items to pack.
	 *
	 * @return array Tube items.
	 */
	protected function get_qualifying_tube_items( &$items ) {
		$tubes = array();
		foreach ( $items as $key => $item ) {
			if ( ! $item->tube ) {
				continue;
			}

			// Item's width and height should be the same.
			if ( $item->width !== $item->height ) {
				continue;
			}

			// Tube item's length should not exceed 90cm.
			if ( $item->length >= 900 ) {
				continue;
			}

			// Item's length and 2xdiameter should not exceed 104cm.
			if ( ( $item->length + $item->width + $item->height ) >= 1040 ) {
				continue;
			}

			$tubes[] = $item;
			unset( $items[ $key ] );
		}

		return $tubes;
	}
}
