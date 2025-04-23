<?php
/**
 * USPS Services and subservices
 *
 * @package WC_Shipping_USPS
 */

/**
 * Filter to modify the USPS services list.
 *
 * @var array List of services.
 *
 * @since 4.4.2
 */
return apply_filters(
	'wc_usps_services',
	array(
		// Domestic.
		'D_FIRST_CLASS'      => array(
			// Name of the service shown to the user.
			'name'     => 'First-Class Mail&#0174;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'0'  => array(
					'first-class-mail-large-envelope'         => 'First-Class Mail&#0174; Large Envelope',
					'first-class-mail-large-envelope-presort' => 'First-Class Mail&#0174; Large Envelope Presort',
					'first-class-mail-postcards'              => 'First-Class Mail&#0174; Postcards',
					'first-class-mail-postcards-5-digit'      => 'First-Class Mail&#0174; Postcards 5-Digit',
					'first-class-mail-postcards-aadc'         => 'First-Class Mail&#0174; Postcards AADC',
					'first-class-mail-postcards-mixed-aadc'   => 'First-Class Mail&#0174; Postcards Mixed AADC',
					'first-class-mail-postcards-presort'      => 'First-Class Mail&#0174; Postcards Presort',
					'first-class-mail-stamped-letter'         => 'First-Class Mail&#0174; Stamped Letter',
				),
				'12' => 'First-Class&#8482; Postcard Stamped',
				'15' => 'First-Class&#8482; Large Postcards',
				'19' => 'First-Class&#8482; Keys and IDs',
				'78' => 'First-Class Mail&#0174; Metered Letter',
			),
		),
		'D_GROUND_ADVANTAGE' => array(
			// Name of the service shown to the user.
			'name'     => 'Ground Advantage&#8482;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'1058' => 'Ground Advantage&#8482;',
			),
		),
		'D_EXPRESS_MAIL'     => array(
			// Name of the service shown to the user.
			'name'     => 'Priority Mail Express&#8482;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'3'  => 'Priority Mail Express&#8482;',
				'23' => 'Priority Mail Express&#8482; Sunday/Holiday',
			),
		),
		'D_MEDIA_MAIL'       => array(
			// Name of the service shown to the user.
			'name'     => 'Media Mail',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'6' => 'Media Mail',
			),
		),
		'D_LIBRARY_MAIL'     => array(
			// Name of the service shown to the user.
			'name'     => 'Library Mail',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'7' => 'Library Mail',
			),
		),
		'D_PRIORITY_MAIL'    => array(
			// Name of the service shown to the user.
			'name'       => 'Priority Mail&#0174;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services'   => array(
				'1'  => 'Priority Mail&#0174;',
				'18' => 'Priority Mail&#0174; Keys and IDs',
			),

			// Service IDs which are only available for commercial rates.
			'commercial' => array( 47, 49 ),
		),
		// International.
		'I_EXPRESS_MAIL'     => array(
			// Name of the service shown to the user.
			'name'     => 'Priority Mail Express International&#8482;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'1' => 'Priority Mail Express International&#8482;',
			),
		),
		'I_PRIORITY_MAIL'    => array(
			// Name of the service shown to the user.
			'name'     => 'Priority Mail International&#0174;',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'2' => 'Priority Mail International&#0174;',
			),
		),
		'I_GLOBAL_EXPRESS'   => array(
			// Name of the service shown to the user.
			'name'     => 'Global Express Guaranteed&#0174; (GXG)',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'4'  => 'Global Express Guaranteed&#0174; (GXG)',
				'5'  => 'Global Express Guaranteed&#0174; Document',
				'6'  => 'Global Express Guaranteed&#0174; Non-Document Rectangular',
				'7'  => 'Global Express Guaranteed&#0174; Non-Document Non-Rectangular',
				'12' => 'USPS GXG&#8482; Envelope',
			),
		),
		'I_FIRST_CLASS'      => array(
			// Name of the service shown to the user.
			'name'     => 'First Class Mail&#0174; International',
			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'15' => 'First Class Package International Service&#8482;',
			),
		),
		'I_POSTCARDS'        => array(
			// Name of the service shown to the user.
			'name'     => 'International Postcards',

			// Services which costs are merged if returned (cheapest is used). This gives us the best possible rate.
			'services' => array(
				'21' => 'International Postcards',
			),
		),
	)
);
