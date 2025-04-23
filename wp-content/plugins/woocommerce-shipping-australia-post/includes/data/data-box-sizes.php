<?php
/**
 * Default Box Sizes.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	array(
		'name'         => 'Small Satchel',
		'id'           => 'AUS_PARCEL_REGULAR_SATCHEL_SMALL',
		'max_weight'   => 5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 35.5, // In cm.
		'outer_width'  => 22, // In Cm.
		'outer_height' => 7, // In Cm.
		'inner_length' => 35.5, // In cm.
		'inner_width'  => 22, // In Cm.
		'inner_height' => 7, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Medium Satchel',
		'id'           => 'AUS_PARCEL_EXPRESS_SATCHEL_MEDIUM',
		'max_weight'   => 5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 38.5, // In cm.
		'outer_width'  => 26.5, // In Cm.
		'outer_height' => 9.5, // In Cm.
		'inner_length' => 38.5, // In cm.
		'inner_width'  => 26.5, // In Cm.
		'inner_height' => 9.5, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Large Satchel',
		'id'           => 'AUS_PARCEL_EXPRESS_SATCHEL_LARGE',
		'max_weight'   => 5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 40.5, // In cm.
		'outer_width'  => 31, // In Cm.
		'outer_height' => 11, // In Cm.
		'inner_length' => 40.5, // In cm.
		'inner_width'  => 31, // In Cm.
		'inner_height' => 11, // In Cm.
		'type'         => 'packet',
	),
	array(
		'name'         => 'Extra Large Satchel',
		'id'           => 'AUS_PARCEL_EXPRESS_SATCHEL_EXTRA_LARGE',
		'max_weight'   => 5, // In kg.
		'box_weight'   => 0,
		'outer_length' => 51, // In cm.
		'outer_width'  => 43.5, // In Cm.
		'outer_height' => 16, // In Cm.
		'inner_length' => 51, // In cm.
		'inner_width'  => 43.5, // In Cm.
		'inner_height' => 16, // In Cm.
		'type'         => 'packet',
	),
);
