<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

$attribute_keys = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

$attr_dropdown = drile_get_theme_options('ts_prod_attr_dropdown');
$attr_color_text = drile_get_theme_options('ts_prod_attr_color_text');
$select_class = '';
if( !$attr_dropdown ){
	$select_class = 'hidden';
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; /* WPCS: XSS ok. */ ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'drile' ); ?></p>
	<?php else : ?>
		<div class="variations">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<div class="attribute">
					<div class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></label></div>
					<div class="value">
						<?php if( !$attr_dropdown && is_array( $options ) ): ?>
								<div class="ts-product-attribute <?php echo sanitize_title( $attribute_name ) ?>">
									<?php 
									if ( isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) {
										$selected_value = $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ];
									} elseif ( isset( $selected_attributes[ sanitize_title( $attribute_name ) ] ) ) {
										$selected_value = $selected_attributes[ sanitize_title( $attribute_name ) ];
									} else {
										$selected_value = '';
									}
									
									// Get terms if this is a taxonomy - ordered
									if ( taxonomy_exists( $attribute_name ) ) {
										
										$class = 'option';
										$is_attr_color = false;
										$is_attr_material = false;
										$attribute_color = wc_sanitize_taxonomy_name( 'color' );
										if( $attribute_name == wc_attribute_taxonomy_name( $attribute_color ) ){
											if( !$attr_color_text ){
												$is_attr_color = true;
												$class .= ' color';
											}
											else{
												$class .= ' text';
											}
										}
										else{
											$attribute_material = wc_sanitize_taxonomy_name( 'material' );
											if( $attribute_name == wc_attribute_taxonomy_name( $attribute_material ) ){
												$is_attr_material = true;
												$class .= ' material';
											}
										}
										
										$terms = wc_get_product_terms( $post->ID, $attribute_name, array( 'fields' => 'all' ) );

										foreach ( $terms as $term ) {
											if ( ! in_array( $term->slug, $options ) ) {
												continue;
											}
											$term_class = '';
											$term_name = apply_filters( 'woocommerce_variation_option_name', $term->name );
											
											if( $is_attr_color ){
												$datas = get_term_meta( $term->term_id, 'ts_product_color_config', true );
												if( strlen( $datas ) > 0 ){
													$datas = unserialize( $datas );	
												}else{
													$datas = array(
																'ts_color_color' 				=> "#ffffff"
																,'ts_color_image' 				=> 0
															);
											
												}
											}
											else if( $is_attr_material ){
												$material_thumb_id = get_term_meta( $term->term_id, 'ts_material_thumb_id', true );
												if( $material_thumb_id ){
													$term_class = 'color';
												}
											}
											
											$selected_class = sanitize_title( $selected_value ) == sanitize_title( $term->slug ) ? 'selected' : '';
											
											echo '<div data-value="' . esc_attr( $term->slug ) . '" class="'. $class .' '. $selected_class .' '. $term_class .'">';
											
											if( $is_attr_color ){
												if( absint($datas['ts_color_image']) > 0 ){
													echo '<a href="#">' . wp_get_attachment_image( absint($datas['ts_color_image']), 'ts_prod_color_thumb', true, array('alt'=>$term_name) ) . '<span class="ts-tooltip button-tooltip">' . $term_name . '</span></a>';
												}
												else{
													echo '<a href="#"><span style="background-color:' . $datas['ts_color_color'] . '"></span><span class="ts-tooltip button-tooltip">' . $term_name . '</span></a>';
												}
											}
											else if( $is_attr_material && $material_thumb_id ){
												echo '<a href="#">' . wp_get_attachment_image( absint($material_thumb_id), 'ts_prod_material_thumb', true, array('alt'=>$term_name) ) . '<span class="ts-tooltip button-tooltip">' . $term_name . '</span></a>';
											}
											else{
												echo '<a href="#">' . $term_name . '</a>';
											}
											
											echo '</div>';
										}

									} else {

										foreach ( $options as $option ) {
											$class = 'option';
											$class .= sanitize_title( $selected_value ) == sanitize_title( $option ) ? ' selected' : '';
											echo '<div data-value="' . esc_attr( $option ) . '" class="' . $class . '"><a href="#">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</a></div>';
										}

									}
									?>
								</div>
							<?php 
							endif;
							
							wc_dropdown_variation_attribute_options( array( 
								'options' => $options, 
								'attribute' => $attribute_name, 
								'product' => $product, 
								'class' => $select_class 
							) );
						?>
					</div>
				</div>
				<?php echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#" aria-label="' . esc_attr__( 'Clear options', 'drile' ) . '">' . esc_html__( 'Clear', 'drile' ) . '</a>' ) ) : ''; ?>
			<?php endforeach;?>
			
		</div>

		<div class="single_variation_wrap">
			<?php
				/**
				 * woocommerce_before_single_variation Hook.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php
do_action( 'woocommerce_after_add_to_cart_form' );
