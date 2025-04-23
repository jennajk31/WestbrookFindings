<?php 
if( class_exists('WooCommerce') && !class_exists('Drile_Quickshop') && !wp_is_mobile() ){
		
	class Drile_Quickshop{
	
		public $id;
		
		function __construct(){
			add_action('init', array($this, 'init'));
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 2000);
		}
		
		function add_quickshop_button(){
			global $product;
			echo '<div class="button-in quickshop">';
			echo '<a class="quickshop" href="#" data-product_id="'.$product->get_id().'"><span class="ts-tooltip button-tooltip">'.esc_html__('Quick view', 'drile').'</span></a>';
			echo '</div>';
		}
		
		function init(){
			if( !drile_get_theme_options('ts_enable_quickshop') ){
				return;
			}
			
			add_action('wp_footer', array($this, 'add_quickshop_modal'), 999);
			
			add_action('woocommerce_after_shop_loop_item_title', array($this, 'add_quickshop_button'), 10001 );
			
			/* Register ajax */
			add_action('wp_ajax_drile_load_quickshop_content', array( $this, 'load_quickshop_content_callback') );
			add_action('wp_ajax_nopriv_drile_load_quickshop_content', array( $this, 'load_quickshop_content_callback') );		
		}
		
		function add_hooks(){
			$theme_options = drile_get_theme_options();
			
			if( $theme_options['ts_prod_title'] ){
				add_action('drile_quickshop_single_product_title', array($this, 'product_title'), 10);
			}
			if( $theme_options['ts_prod_rating'] ){
				add_action('drile_quickshop_single_product_title', 'woocommerce_template_single_rating', 1);
			}
			if( $theme_options['ts_prod_price'] ){
				add_action('drile_quickshop_single_product_summary', 'woocommerce_template_single_price', 20);
				add_action('drile_quickshop_single_product_summary', 'drile_template_single_variation_price', 21);
			}
			else{
				remove_action('woocommerce_single_variation', 'woocommerce_single_variation', 10);
			}
			if( $theme_options['ts_prod_excerpt'] ){
				add_action('drile_quickshop_single_product_summary', 'woocommerce_template_single_excerpt', 25);
			}
			if( $theme_options['ts_prod_add_to_cart'] && !$theme_options['ts_enable_catalog_mode'] ){
				add_action('drile_quickshop_single_product_summary', 'woocommerce_template_single_add_to_cart', 40); 
			}
			
			add_action('drile_quickshop_single_product_summary', 'drile_template_single_meta', 60);
		}
		
		function enqueue_scripts(){
			$theme_options = drile_get_theme_options();
			if( !empty($theme_options['ts_enable_quickshop']) ){
				wp_enqueue_script( 'wc-add-to-cart-variation' );
				if( $theme_options['ts_quickshop_image_layout'] == 'small-thumbnails' ){
					wp_enqueue_script( 'cloud-zoom' );
					if( $theme_options['ts_prod_thumbnails_style'] == 'vertical' ){
						wp_enqueue_script( 'jquery-caroufredsel' );
					}
				}
			}
		}
		
		function add_quickshop_modal(){
		?>
		<div id="ts-quickshop-modal" class="ts-popup-modal">
			<div class="overlay"></div>
			<div class="quickshop-container popup-container">
				<span class="close"><?php esc_html_e('Close ', 'drile'); ?></span>
				<div class="quickshop-content"></div>
			</div>
		</div>
		<?php
		}
		
		function product_title(){
			?>
			<h1 itemprop="name" class="product_title entry-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h1>
			<?php
		}
		
		function filter_add_to_cart_url(){
			$ref_url = wp_get_referer();
			$ref_url = remove_query_arg( array('added-to-cart','add-to-cart'), $ref_url );
			$ref_url = add_query_arg( array( 'add-to-cart' => $this->id ), $ref_url );
			return esc_url( $ref_url );
		}
		
		function filter_review_link( $review_link = '#reviews' ){
			global $product;
			$link = get_permalink( $product->get_id() );
			if( $link ){
				return trailingslashit($link) . $review_link;
			}
			else{
				return $review_link;
			}
		}
		
		function load_quickshop_content_callback(){
			global $post, $product;
			$prod_id = absint($_POST['product_id']);
			$post = get_post( $prod_id );
			$product = wc_get_product( $prod_id );

			if( $prod_id <= 0 ){
				die( esc_html__('Invalid Product', 'drile') );
			}
			if( !isset($post->post_type) || $post->post_type != 'product' ){
				die( esc_html__('Invalid Product', 'drile') );
			}
			
			$this->id = $prod_id;
			
			$this->add_hooks();
			
			drile_change_theme_options('ts_prod_sharing', 0);
			
			$image_layout = drile_get_theme_options('ts_quickshop_image_layout');
			$thumbnails_style = drile_get_theme_options('ts_prod_thumbnails_style');
			$product_title = $product->get_title();
			
			add_filter( 'woocommerce_add_to_cart_url', array($this, 'filter_add_to_cart_url'), 10 );
			add_filter( 'drile_woocommerce_review_link_filter', array($this, 'filter_review_link'), 10 );
			
			$classes = array('ts-quickshop-wrapper product');
			$classes[] = $image_layout;
			if( $image_layout == 'small-thumbnails' ){
				$classes[] = $thumbnails_style . '-thumbnail';
			}
			if( drile_get_theme_options('ts_prod_thumbnail_border') ){
				$classes[] = 'thumbnail-border';
			}
			if( !has_action('drile_quickshop_single_product_summary', 'woocommerce_template_single_add_to_cart') ){
				$classes[] = 'no-addtocart';
			}
			
			/*** Get Image IDs ***/
			$image_ids = array();
			if ( has_post_thumbnail() ){
				$image_ids[] = get_post_thumbnail_id();				
			}
			
			$attachment_ids = $product->get_gallery_image_ids();
			if( is_array($attachment_ids) ){
				$image_ids = array_merge($image_ids, $attachment_ids);
				$number_thumbnail = apply_filters('drile_quickshop_number_thumbnail', 5);
				if( count($image_ids) > $number_thumbnail ){
					$image_ids = array_slice($image_ids, 0, $number_thumbnail);
				}
			}
			
			ob_start();	
			?>
			<div class="woocommerce">
				<div itemscope itemtype="http://schema.org/Product" <?php post_class( implode(' ', $classes) ); ?>>
					
					<?php if( $image_layout == 'full-slider' ): ?>					
						<div class="images-slider-wrapper nav-middle nav-margin">
						<?php	
							if( count($image_ids) == 0 ){ /* Always show image */
								$image_ids[] = 0;
							}
							$image_sizes = wc_get_image_size( 'woocommerce_thumbnail' );
							?>
							<div class="image-items thumbnail loading" data-height="<?php echo esc_attr($image_sizes['height']); ?>" data-width="<?php echo esc_attr($image_sizes['width']); ?>">
								<?php foreach( $image_ids as $image_id ): ?>
								<?php 
									$image_info = wp_get_attachment_image_src($image_id, 'woocommerce_single');
									$image_link = isset($image_info[0])?$image_info[0]:wc_placeholder_img_src();
								?>
								<div class="image-item">
									<img src="<?php echo esc_url($image_link); ?>" alt="<?php echo esc_attr($product_title); ?>" />
								</div>
								<?php endforeach; ?>
							</div>
							
						</div>
					<?php else: /* Small Thumbnails */ ?>
						<div class="images-thumbnails">
							<?php 
							if( $thumbnails_style == 'vertical' ){
								$this->product_thumbnails_html( $image_ids, $thumbnails_style );
							}
							?>
						
							<div class="images">
							<?php
								if( has_post_thumbnail() ){
									$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
									$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
									$attributes = array(
										'alt'  						=> $product_title
										,'class' 					=> 'attachment-woocommerce_single size-woocommerce_single wp-post-image'
									);
									?>
									<div class="woocommerce-product-gallery__image">
										<a href="<?php echo esc_url($full_size_image[0]); ?>" class="woocommerce-main-image ts-qs-zoom cloud-zoom zoom on_pc" id="ts_qs_zoom" data-rel="position:'inside',showTitle:0,lensOpacity:0.5,adjustX:0,adjustY:-4">
											<?php echo wp_get_attachment_image($post_thumbnail_id, 'woocommerce_single', false, $attributes); ?>
										</a>
									</div>
									<?php
								}
								else{
									?>
									<div class="woocommerce-product-gallery__image--placeholder">
										<img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php esc_attr_e('Awaiting product image', 'drile'); ?>" class="wp-post-image" />
									</div>
									<?php
								}
							?>
							</div>
							
							<?php 
							if( $thumbnails_style == 'horizontal' ){
								$this->product_thumbnails_html( $image_ids, $thumbnails_style );
							}
							?>
						</div>
					<?php endif; ?>
					<!-- Product summary -->
					<div class="summary entry-summary">
						<?php do_action('drile_quickshop_single_product_title'); ?>
						<?php do_action('drile_quickshop_single_product_summary'); ?>
					</div>
				
				</div>
			</div>
				
			<?php
			remove_filter( 'woocommerce_add_to_cart_url', array($this, 'filter_add_to_cart_url'), 10 );
			remove_filter( 'drile_woocommerce_review_link_filter', array($this, 'filter_review_link'), 10 );

			wp_reset_postdata();
			die( ob_get_clean() );
		}
		
		function product_thumbnails_html( $image_ids, $thumbnails_style ){
			if( empty($image_ids) ){
				return;
			}
			?>
			<div class="thumbnails ts-slider loading">
				<div class="thumbnails-container">
					<ul class="product-thumbnails">
					<?php 
					foreach( $image_ids as $image_id ){
						$single_image 		= wp_get_attachment_image_src($image_id, 'woocommerce_single');
						if( $single_image ){
							$full_size_image   = wp_get_attachment_image_src($image_id, 'full');
							?>
							<li class="woocommerce-product-gallery__image">
								<a href="<?php echo esc_url($full_size_image[0]); ?>" data-rel="useZoom: 'ts_qs_zoom', smallImage: '<?php echo esc_url($single_image[0]) ?>'" class="ts-qs-zoom-gallery cloud-zoom-gallery zoom">
									<?php echo wp_get_attachment_image($image_id, 'woocommerce_thumbnail'); ?>
								</a>
							</li>
							<?php
						}
					}
					?>
					</ul>
					<?php if( $thumbnails_style == 'vertical' ): ?>
					<div class="owl-controls">
						<div class="owl-nav">
							<div class="owl-prev"></div>
							<div class="owl-next"></div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
		
	}
	new Drile_Quickshop();
}
?>