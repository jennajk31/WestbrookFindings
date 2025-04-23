<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     9.5.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $product;

$attachment_ids = $product->get_gallery_image_ids();

if ( $attachment_ids ) {
	$vertical_thumbnail = drile_get_theme_options('ts_prod_thumbnails_style') == 'vertical';
	$thumbnail_layout = drile_get_theme_options('ts_prod_thumbnail_layout');

	if( has_post_thumbnail() && ( $thumbnail_layout != 'default' || ( $thumbnail_layout == 'default' && drile_get_theme_options('ts_prod_cloudzoom') ) ) ){
		array_unshift($attachment_ids, get_post_thumbnail_id());
	}
	$number_thumbnail = count($attachment_ids);
	$thumbnails_extra_classes = apply_filters('drile_single_product_thumbnails_class', array());
	if( $thumbnail_layout == 'grid' && $number_thumbnail == 3 ){
		$thumbnails_extra_classes[] = 'three-thumbnail';
	}
	if( $thumbnail_layout == 'grid' && $number_thumbnail > 3 && $number_thumbnail % 2 == 1 ){
		$last_item_start = $number_thumbnail - 3;
	}
	
	if( $thumbnail_layout != 'default' && drile_get_theme_options('ts_prod_label') ){
		drile_template_loop_product_label();
	}
	?>
	<div class="thumbnails ts-slider <?php echo esc_attr(implode(' ', $thumbnails_extra_classes)) ?>">
		<div class="thumbnails-container <?php echo (1 < $number_thumbnail && $thumbnail_layout != 'grid')?'loading':''; ?>">
			<ul class="product-thumbnails">
			<?php
			$loop = 0;
			$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
			
			$start_index = 1;
			if( $thumbnail_layout != 'default' ){
				$start_index = 0;
			}

			$thumbnail_size = apply_filters('drile_single_product_shop_thumbnail_size', 'woocommerce_thumbnail');
			foreach ( $attachment_ids as $i => $attachment_id ) {
				$item_class = isset($last_item_start) && $i >= $last_item_start?'last-item':'';

				$classes = array( 'zoom' );

				if ( $loop === 0 || $loop % $columns === 0 ) {
					$classes[] = 'first';
				}

				if ( ( $loop + 1 ) % $columns === 0 ) {
					$classes[] = 'last';
				}
				$image_class 	= esc_attr( implode( ' ', $classes ) );
				
				$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
				$thumbnail        = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
				
				if( drile_get_theme_options('ts_prod_cloudzoom') ){
					$single_thumbnail = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
					$image_class 	.= ' cloud-zoom-gallery';
					$data_rel = 'useZoom: \'product_zoom\', smallImage: \''.esc_url( $single_thumbnail[0] ).'\'';
					if( $thumbnail_layout == 'grid' ){
						$data_rel = 'position:\'inside\',showTitle:0,titleOpacity:0.5,lensOpacity:0.5,fixWidth:362,fixThumbWidth:72,fixThumbHeight:72,adjustX: 0, adjustY:'.(wp_is_mobile()?'0':'-4');
						$image_class 	.= ' cloud-zoom';
					}
					$attributes = array(
						'title'                   => get_post_field( 'post_title', $attachment_id )
					);
					$html  = '<li data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image '.$item_class.'"><a href="' . esc_url( $full_size_image[0] ) . '" class="'.esc_attr($image_class).'" data-rel="'.$data_rel.'">';
					$html .= wp_get_attachment_image( $attachment_id, $thumbnail_size, false, $attributes );
					$html .= '</a></li>';
				}
				else{
					$attributes = array(
						'title'                   => get_post_field( 'post_title', $attachment_id ),
						'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
						'data-src'                => $full_size_image[0],
						'data-large_image'        => $full_size_image[0],
						'data-large_image_width'  => $full_size_image[1],
						'data-large_image_height' => $full_size_image[2],
						'data-index' 			  => $loop + $start_index,
					);
					$html  = '<li data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image '.$item_class.'"><a href="' . esc_url( $full_size_image[0] ) . '" class="'.esc_attr($image_class).'">';
					$html .= wp_get_attachment_image( $attachment_id, $thumbnail_size, false, $attributes );
					$html .= '</a></li>';
				}
				
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );

				$loop++;
			}
		?>
			</ul>
			
			<?php if( $vertical_thumbnail ): ?>
			<div class="owl-controls">
				<div class="owl-nav">
					<div class="owl-prev"></div>
					<div class="owl-next"></div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<?php do_action('drile_after_single_product_thumbnails'); ?>
	</div>
	<?php
}