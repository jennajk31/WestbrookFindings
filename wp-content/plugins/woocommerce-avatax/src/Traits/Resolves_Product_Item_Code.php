<?php

/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\AvaTax\Traits;

defined('ABSPATH') or exit;

use WC_Product;

/**
 * A trait that helps resolve the AvaTax item code for a WooCommerce Product.
 *
 * @since 1.16.0
 */
trait Resolves_Product_Item_Code {


	/**
	 * Determines the item code to use for this product.
	 *
	 * @since 1.16.0
	 *
	 * @param WC_Product $product The product object.
	 * @return string|int The item code to be used for AvaTax.
	 */
	public function resolve_product_item_code( WC_Product $product ) {
	    $item_code = '';
	    
	    // Check if SKU is enabled as item code
	    $use_sku = 'yes' === get_option( 'wc_avatax_sku_as_item_code', 'no' );
	    
	    if ( $use_sku ) {
	        $item_code = $product->get_sku();
	    }
	    
	    // Fallback to product ID if SKU is empty or not enabled
	    if ( empty( $item_code ) ) {
	        $item_code = $product->get_id() ?? 0;
	    }
	
	    /**
	     * Filter the item code before returning.
	     *
	     * @since 2.10.0
	     *
	     * @param string|int $item_code  The resolved item code.
	     * @param WC_Product $product    The product object.
	     * @param bool       $use_sku    Whether SKU is enabled as item code.
	     * @return string|int
	     */
	    return apply_filters( 'wc_avatax_product_item_code', $item_code, $product, $use_sku );
	}


}
