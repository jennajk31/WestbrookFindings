<?php
/**
 * Package Size as class constants.
 *
 * @package WC_RoyalMail/Rate
 */

namespace WooCommerce\RoyalMail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Packaging class.
 */
class Packaging {
	/**
	 * Represents a standard letter size.
	 *
	 * @var string
	 */
	const LETTER = 'letter';

	/**
	 * Represents a large letter size
	 *
	 * @var string
	 */
	const LARGE_LETTER = 'large-letter';

	/**
	 * Represents a small parcel size.
	 *
	 * @var string
	 */
	const SMALL_PARCEL = 'small-parcel';

	/**
	 * Represents a tube size
	 *
	 * @var string
	 */
	const TUBE = 'tube';

	/**
	 * Represents a medium parcel
	 *
	 * @var string
	 */
	const MEDIUM_PARCEL = 'medium-parcel';

	/**
	 * Represents a packet or small parcel
	 *
	 * @var string
	 */
	const PACKET = 'packet';

	/**
	 * Represents printed papers
	 *
	 * @var string
	 */
	const PRINTED_PAPERS = 'printed-papers';
}
