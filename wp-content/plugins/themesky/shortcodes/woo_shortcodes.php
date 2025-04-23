<?php
function ts_remove_product_hooks_shortcode( $options = array() ){
	if( isset($options['show_label']) && !$options['show_label'] ){
		remove_action('woocommerce_after_shop_loop_item_title', 'drile_template_loop_product_label', 1);
	}
	if( isset($options['show_image']) && !$options['show_image'] ){
		remove_action('woocommerce_before_shop_loop_item_title', 'drile_template_loop_product_thumbnail', 10);
	}
	
	if( isset($options['show_categories']) && !$options['show_categories'] ){
		remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_categories', 10);
	}
	if( isset($options['show_sku']) && !$options['show_sku'] ){
		remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_sku', 20);
	}
	if( isset($options['show_title']) && !$options['show_title'] ){
		remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_title', 30);
	}
	if( isset($options['show_price']) && !$options['show_price'] ){
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 40);
	}
	if( isset($options['show_rating']) && !$options['show_rating'] ){
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 45);
	}
	if( isset($options['show_short_desc']) && !$options['show_short_desc'] ){
		remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_short_description', 60);
	}
	if( isset($options['show_add_to_cart']) && !$options['show_add_to_cart'] ){
		remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_add_to_cart', 70);
		remove_action('woocommerce_after_shop_loop_item_title', 'drile_template_loop_add_to_cart', 10004 );
	}
	if( isset($options['show_color_swatch']) && $options['show_color_swatch'] && function_exists('drile_template_loop_product_variable_color') ){
		add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_variable_color', 50);
		if( isset($options['number_color_swatch']) ){
			$number_color_swatch = absint($options['number_color_swatch']);
			add_filter('drile_loop_product_variable_color_number', function() use ($number_color_swatch){
				return $number_color_swatch;
			});
		}
	}
	wc_set_loop_prop( 'is_shortcode', true );
}

function ts_restore_product_hooks_shortcode(){
	add_action('woocommerce_after_shop_loop_item_title', 'drile_template_loop_product_label', 1);
	add_action('woocommerce_before_shop_loop_item_title', 'drile_template_loop_product_thumbnail', 10);
	
	add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_categories', 10);
	add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_sku', 20);
	add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_title', 30);
	add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 40);
	add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 45);
	add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_short_description', 60); 
	add_action('woocommerce_after_shop_loop_item', 'drile_template_loop_add_to_cart', 70); 
	add_action('woocommerce_after_shop_loop_item_title', 'drile_template_loop_add_to_cart', 10004 );
	remove_action('woocommerce_after_shop_loop_item', 'drile_template_loop_product_variable_color', 50);
	remove_all_filters('drile_loop_product_variable_color_number');
	wc_set_loop_prop( 'is_shortcode', false );
}

function ts_filter_product_by_product_type( &$args = array(), $product_type = 'recent' ){
	switch( $product_type ){
		case 'sale':
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		break;
		case 'featured':
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			);
		break;
		case 'best_selling':
			$args['meta_key'] 	= 'total_sales';
			$args['orderby'] 	= 'meta_value_num';
			$args['order'] 		= 'desc';
		break;
		case 'top_rated':
			$args['meta_key'] 	= '_wc_average_rating';
			$args['orderby'] 	= 'meta_value_num';
			$args['order'] 		= 'desc';
		break;
		case 'mixed_order':
			$args['orderby'] 	= 'rand';
		break;
		default: /* Recent */
			$args['orderby'] 	= 'date';
			$args['order'] 		= 'desc';
		break;
	}
}

function ts_get_product_deals_transient(){
	$key = 'all';
	if( defined('ICL_LANGUAGE_CODE') ){
		$key .= '-' . ICL_LANGUAGE_CODE;
	}
	$transient = get_transient('ts_product_deals_ids');
	if( $transient && isset($transient[$key]) && is_array($transient[$key]) ){
		return $transient[$key];
	}
	return false;
}

function ts_set_product_deals_transient( $value = array() ){
	$key = 'all';
	if( defined('ICL_LANGUAGE_CODE') ){
		$key .= '-' . ICL_LANGUAGE_CODE;
	}
	$transient = get_transient('ts_product_deals_ids');
	if( is_array($transient) ){
		$transient[$key] = $value;
	}
	else{
		$transient = array( $key => $value );
	}
	set_transient( 'ts_product_deals_ids', $transient, MONTH_IN_SECONDS );
}

add_action('wc_after_products_starting_sales', 'ts_delete_product_deals_transient');
add_action('wc_after_products_ending_sales', 'ts_delete_product_deals_transient');
add_action('woocommerce_delete_product_transients', 'ts_delete_product_deals_transient');
function ts_delete_product_deals_transient(){
	set_transient( 'ts_product_deals_ids', false, MONTH_IN_SECONDS );
}

function ts_get_product_deals_ids(){
	$product_ids = ts_get_product_deals_transient();
	if( !is_array($product_ids) ){
		global $post;
		$product_ids = array();
		$args = array(
			'post_type'				=> array('product', 'product_variation')
			,'post_status' 			=> 'publish'
			,'posts_per_page' 		=> -1
			,'meta_query' => array(
				array(
					'key'		=> '_sale_price_dates_to'
					,'value'	=> current_time( 'timestamp', true )
					,'compare'	=> '>'
					,'type'		=> 'numeric'
				)
				,array(
					'key'		=> '_sale_price_dates_from'
					,'value'	=> current_time( 'timestamp', true )
					,'compare'	=> '<'
					,'type'		=> 'numeric'
				)
			)
			,'tax_query'			=> array()
		);
		
		$products = new WP_Query( $args );
		
		if( $products->have_posts() ){
			while( $products->have_posts() ){
				$products->the_post();
				if( $post->post_type == 'product' ){
					$product_ids[] = $post->ID;
				}
				else{ /* Variation product */
					$product_ids[] = $post->post_parent;
				}
			}
		}
		$product_ids = array_unique($product_ids);
		ts_set_product_deals_transient($product_ids);
		wp_reset_postdata();
	}
	
	return $product_ids;
}

/*** Products Shortcode ***/

if( !function_exists('ts_products_shortcode') ){

	function ts_products_shortcode($atts, $content){

		extract(shortcode_atts(array(
				'title'						=> ''
				,'product_type'				=> 'recent'
				,'item_layout' 				=> 'grid'
				,'columns' 					=> 4
				,'per_page' 				=> 4
				,'product_cats'				=> ''
				,'ids'						=> ''
				,'image_border'				=> 0
				,'show_image' 				=> 1
				,'show_title' 				=> 1
				,'show_sku' 				=> 0
				,'show_price' 				=> 1
				,'show_short_desc'  		=> 0
				,'show_rating' 				=> 0
				,'show_label' 				=> 1	
				,'show_categories'			=> 0	
				,'show_add_to_cart' 		=> 1
				,'show_color_swatch'		=> 0
				,'number_color_swatch'		=> 3
				,'shop_more_text'			=> ''
				,'shop_more_link'			=> ''
				,'is_slider'				=> 0
				,'only_slider_mobile'		=> 0
				,'rows' 					=> 1
				,'show_nav'					=> 1
				,'auto_play'				=> 0
				,'margin'					=> 0
				,'disable_slider_responsive'=> 0
			), $atts));
			if ( !class_exists('WooCommerce') ){
				return;
			}
			
			if( $only_slider_mobile && !wp_is_mobile() ){
				$is_slider = 0;
			}
			
			$options = array(
					'show_image'			=> $show_image
					,'show_label'			=> $show_label
					,'show_title'			=> $show_title
					,'show_sku'				=> $show_sku
					,'show_price'			=> $show_price
					,'show_short_desc'		=> $show_short_desc
					,'show_categories'		=> $show_categories
					,'show_rating'			=> $show_rating
					,'show_add_to_cart'		=> $show_add_to_cart
					,'show_color_swatch'	=> $show_color_swatch
					,'number_color_swatch'	=> $number_color_swatch
				);
			ts_remove_product_hooks_shortcode( $options );
			
			$args = array(
				'post_type'				=> 'product'
				,'post_status' 			=> 'publish'
				,'ignore_sticky_posts'	=> 1
				,'posts_per_page' 		=> $per_page
				,'orderby' 				=> 'date'
				,'order' 				=> 'desc'
				,'meta_query' 			=> WC()->query->get_meta_query()
				,'tax_query'           	=> WC()->query->get_tax_query()
			);
			
			ts_filter_product_by_product_type($args, $product_type);

			$product_cats = str_replace(' ', '', $product_cats);
			if( strlen($product_cats) > 0 ){
				$product_cats = explode(',', $product_cats);
			}
			if( is_array($product_cats) && count($product_cats) > 0 ){
				$field_name = is_numeric($product_cats[0])?'term_id':'slug';
				$args['tax_query'][] = array(
											'taxonomy' => 'product_cat'
											,'terms' => $product_cats
											,'field' => $field_name
											,'include_children' => false
										);
			}
			
			$ids = str_replace(' ', '', $ids);
			if( strlen($ids) > 0 ){
				$ids = explode(',', $ids);
				if( is_array($ids) && count($ids) > 0 ){
					$args['post__in'] = $ids;
					$args['orderby'] = 'post__in';
					if( count($ids) == 1 ){
						$columns = 1;
					}
				}
			}
			
			ob_start();
			global $post;
			if( (int)$columns <= 0 ){
				$columns = 5;
			}
			
			$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
			wc_set_loop_prop('columns', $columns);

			$products = new WP_Query( $args );
			
			$classes = array();
			$classes[] = 'ts-product-wrapper ts-shortcode ts-product heading-center';
			$classes[] = $product_type;
			$classes[] = 'item-'.$item_layout;
			if( $per_page == 7 && ( $columns == 3 || $columns == 4 ) && !$is_slider ){
				$classes[] = 'special-columns';
			}
			if( $show_color_swatch ){
				$classes[] = 'show-color-swatch';
			}
			if( $image_border ){
				$classes[] = 'image-border';
			}
			if( $is_slider ){
				$classes[] = 'ts-slider';
				$classes[] = 'rows-'.$rows;
				if( $show_nav ){
					$classes[] = 'show-nav nav-middle middle-thumbnail';
				}
			}
			
			$data_attr = array();
			if( $is_slider ){
				$data_attr[] = 'data-nav="'.$show_nav.'"';
				$data_attr[] = 'data-autoplay="'.$auto_play.'"';
				$data_attr[] = 'data-margin="'.absint($margin).'"';
				$data_attr[] = 'data-columns="'.$columns.'"';
				$data_attr[] = 'data-disable_responsive="'.$disable_slider_responsive.'"';
			}
			
			if( $products->have_posts() ): 
			?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr) ?>>
			
				<?php if( strlen($title) > 0 ): ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>					
				</header>
				<?php endif; ?>
				
				<div class="content-wrapper <?php echo ($is_slider)?'loading':'' ?>">
					<?php
					$count = 0;
					woocommerce_product_loop_start();
					
					while( $products->have_posts() ){ 
						$products->the_post();	
						if( $is_slider && $rows > 1 && $count % $rows == 0 ){
							echo '<div class="product-group">';
						}
						wc_get_template_part( 'content', 'product' );
						if( $is_slider && $rows > 1 && ($count % $rows == $rows - 1 || $count == $products->post_count - 1) ){
							echo '</div>';
						}
						$count++;
					}

					woocommerce_product_loop_end();
					?>
				</div>
				
				<?php if( $shop_more_text && $shop_more_link ): ?>
				<div class="shop-more">
					<a class="button button-text" href="<?php echo esc_url($shop_more_link); ?>"><?php echo esc_html($shop_more_text) ?></a>
				</div>
				<?php endif; ?>
				
			</div>
			<?php
			endif;
			
			wp_reset_postdata();

			/* restore hooks */
			ts_restore_product_hooks_shortcode();

			wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
			return '<div class="woocommerce columns-'.$columns.'">' . ob_get_clean() . '</div>';
	}	
}
add_shortcode('ts_products', 'ts_products_shortcode');

/*** Products Widget ***/
if( !function_exists('ts_products_widget_shortcode') ){
	function ts_products_widget_shortcode($atts, $content){
	
		if( !class_exists('TS_Products_Widget') ){
			return;
		}
	
		extract(shortcode_atts(array(
				'product_type'			=> 'recent'
				,'rows' 				=> 3
				,'per_page' 			=> 6
				,'product_cats'			=> ''
				,'title' 				=> ''
				,'title_style' 			=> ''
				,'show_image' 			=> 1
				,'show_title' 			=> 1
				,'show_price' 			=> 1
				,'show_rating' 			=> 0
				,'show_categories'		=> 0
				,'image_border'			=> 0
				,'image_radius'			=> 0
				,'is_slider'			=> 0
				,'show_nav'				=> 1
				,'auto_play'			=> 1
			), $atts));	
		if( trim($product_cats) != '' ){
			$product_cats = array_map('trim', explode(',', $product_cats));
		}
		
		$instance = array(
			'title'					=> $title
			,'title_style'			=> $title_style
			,'product_type'			=> $product_type
			,'product_cats'			=> $product_cats
			,'row'					=> $rows
			,'limit'				=> $per_page
			,'show_thumbnail' 		=> $show_image
			,'show_categories' 		=> $show_categories
			,'show_product_title' 	=> $show_title
			,'show_price' 			=> $show_price
			,'show_rating' 			=> $show_rating
			,'image_border'			=> $image_border
			,'image_radius'			=> $image_radius
			,'is_slider'			=> $is_slider
			,'show_nav' 			=> $show_nav
			,'auto_play' 			=> $auto_play
		);
		
		ob_start();
		the_widget('TS_Products_Widget', $instance);
		return ob_get_clean();
	}
}
add_shortcode('ts_products_widget', 'ts_products_widget_shortcode');

/* Product Category Slider */

if( !function_exists('ts_product_categories_shortcode') ){
	function ts_product_categories_shortcode($atts, $content){
		extract(shortcode_atts(array(
			'title'						=> ''
			,'is_slider'				=> 0
			,'per_page' 				=> 5
			,'columns' 					=> 4
			,'first_level' 				=> 0
			,'parent' 					=> ''
			,'child_of' 				=> 0
			,'ids'	 					=> ''
			,'hide_empty'				=> 1
			,'show_icon'				=> 0
			,'show_title'				=> 1
			,'show_product_count'		=> 0
			,'reverse_effect'			=> 0
			,'view_shop_button_text'	=> ''
			,'extra_class'				=> ''
			,'show_nav' 				=> 1
			,'auto_play' 				=> 1
			,'margin'					=> 0
		),$atts));

		if ( !class_exists('WooCommerce') ){
			return;
		}
		
		remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail');
		add_action('woocommerce_before_subcategory_title', 'ts_woocommerce_subcategory_thumbnail');
		
		if( $first_level ){
			$parent = $child_of = 0;
		}

		$args = array(
			'taxonomy'	  => 'product_cat'
			,'orderby'    => 'name'
			,'order'      => 'ASC'
			,'hide_empty' => $hide_empty
			,'include'    => array_map('trim', explode(',', $ids))
			,'pad_counts' => true
			,'parent'     => $parent
			,'child_of'   => $child_of
			,'number'     => $per_page
		);
		if( $ids ){
			$args['orderby'] = 'include';
		}
		$product_categories = get_terms($args);
		
		$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
		wc_set_loop_prop('columns', $columns);
		
		ob_start();
		
		if( count($product_categories) > 0 ):
			$classes = array();
			$classes[] = 'ts-product-category-wrapper ts-product ts-shortcode heading-center';
			$classes[] = $is_slider?'ts-slider':'grid';
			$classes[] = $extra_class;
			
			if( $reverse_effect ){
				$classes[] = 'reverse-effect';
			}
			if( $is_slider && $show_nav ){
				$classes[] = 'show-nav';
				$classes[] = 'nav-middle';
			}
			if( $view_shop_button_text ){
				$classes[] = 'show-button';
			}
		
			$data_attr = array();
			if( $is_slider ){
				$data_attr[] = 'data-nav="'.$show_nav.'"';
				$data_attr[] = 'data-autoplay="'.$auto_play.'"';
				$data_attr[] = 'data-margin="'.$margin.'"';
				$data_attr[] = 'data-columns="'.$columns.'"';
			}
			
		?>
			<div class="<?php echo esc_attr(implode(' ', $classes)) ?>" <?php echo implode(' ', $data_attr); ?>>
			
				<?php if( strlen($title) > 0 ): ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
				<?php endif; ?>
				
				<div class="content-wrapper <?php echo $is_slider?'loading':''; ?>">
					<?php 
					woocommerce_product_loop_start();
					foreach ( $product_categories as $category ) {
						wc_get_template( 'content-product-cat.php', array(
							'category' 					=> $category
							,'show_icon' 				=> $show_icon
							,'show_title' 				=> $show_title
							,'show_product_count' 		=> $show_product_count
							,'view_shop_button_text' 	=> $view_shop_button_text
						) );
					}
					woocommerce_product_loop_end();
					?>
				</div>
			</div>
		<?php
		endif;
		
		wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
		
		add_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail');
		remove_action('woocommerce_before_subcategory_title', 'ts_woocommerce_subcategory_thumbnail');
		
		return '<div class="woocommerce columns-'.$columns.'">' . ob_get_clean() . '</div>';			
	}
}
add_shortcode('ts_product_categories', 'ts_product_categories_shortcode');

if( !function_exists('ts_woocommerce_subcategory_thumbnail') ){
	function ts_woocommerce_subcategory_thumbnail( $category ){
		$thumbnail_size = apply_filters('subcategory_archive_thumbnail_size', 'woocommerce_thumbnail');
		$thumbnail_id   = get_term_meta($category->term_id, 'thumbnail_id', true);
		if( $thumbnail_id ){
			echo wp_get_attachment_image( $thumbnail_id, $thumbnail_size );
		}
		else{
			echo wc_placeholder_img( $thumbnail_size );
		}
	}
}

/* Product Category Banners */
if( !function_exists('ts_product_category_banners_shortcode') ){
	function ts_product_category_banners_shortcode($atts, $content){
		extract(shortcode_atts(array(
			'title'							=> ''
			,'product_cats'					=> ''
			,'banners'						=> ''
			,'banner_size'					=> 'full'
			,'show_product_count'			=> 1
			,'extra_class'					=> ''
		),$atts));

		if ( !class_exists('WooCommerce') || !$product_cats ){
			return;
		}
		
		$product_cats = array_map('trim', explode(',', $product_cats));
		$banners = array_map('trim', explode(',', $banners));

		$rand_id = 'ts-product-category-banner-wrapper-' . mt_rand(0, 1000);
		$selector = '#' . $rand_id;

		ob_start();
		?>
		<div class="ts-product-category-banner-wrapper ts-shortcode <?php echo (strlen($title) > 0)?'has-title':''; ?> <?php echo $extra_class; ?>" id="<?php echo esc_attr($rand_id); ?>">
		
			<?php
			
				$product_cats_even = array_filter($product_cats, function($k){
					return !($k & 1);
				}, ARRAY_FILTER_USE_KEY);
				
				$product_cats_odd = array_filter($product_cats, function($k){
					return $k & 1;
				}, ARRAY_FILTER_USE_KEY);
				
			?>
			<div class="category-column column-left">
				<?php
					foreach( $product_cats_even as $k => $product_cat_even ){
						
						if( isset($product_cat_even) ){
							$category = get_term($product_cat_even, 'product_cat');
							
							if( !isset($category->name) ){
								continue;
							}
							
							$cat_link = get_term_link((int)$product_cat_even, 'product_cat');
							
							echo '<div class="category-item">';
							
								if( isset($banners[$k]) && $banners[$k] != '' ){
									echo '<a href="'.esc_url($cat_link).'">'.wp_get_attachment_image($banners[$k], $banner_size).'</a>';
								}else{
									do_action( 'woocommerce_before_subcategory_title', $category );
								}
								
								echo '<div class="category-name">';
									echo '<h3><a href="'.esc_url($cat_link).'">'.esc_html($category->name).'</a></h3>';
									if( $show_product_count ){
										echo '<span class="category-count">'.sprintf( _n('%d Product', '%d Products', $category->count, 'themesky'), $category->count ).'</span>';
									}
								echo '</div>';
								
							echo '</div>';
						}
					}
				?>
			</div>
			
			<?php if( strlen($title) > 0 ): ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title"><?php echo esc_html($title); ?></h2>
				</header>
			<?php endif; ?>
			
			<div class="category-column column-right">
				<?php
					foreach( $product_cats_odd as $k => $product_cat_odd ){
						
						if( isset($product_cat_odd) ){
							$category = get_term($product_cat_odd, 'product_cat');
							
							if( !isset($category->name) ){
								continue;
							}
							
							$cat_link = get_term_link((int)$product_cat_odd, 'product_cat');
						
							echo '<div class="category-item">';
							
								if( isset($banners[$k]) && $banners[$k] != '' ){
									echo '<a href="'.esc_url($cat_link).'">'.wp_get_attachment_image($banners[$k], $banner_size).'</a>';
								}else{
									do_action( 'woocommerce_before_subcategory_title', $category );
								}
								
								echo '<div class="category-name">';
									echo '<h3><a href="'.esc_url($cat_link).'">'.esc_html($category->name).'</a></h3>';
									if( $show_product_count ){
										echo '<span class="category-count">'.sprintf( _n('%d Product', '%d Products', $category->count, 'themesky'), $category->count ).'</span>';
									}
								echo '</div>';
								
							echo '</div>';
						}
					}
				?>
			</div>
		</div>
		<?php
		
		return ob_get_clean();		
	}
}
add_shortcode('ts_product_category_banners', 'ts_product_category_banners_shortcode');

/* Product Brands */
if( !function_exists('ts_product_brands_shortcode') ){
	function ts_product_brands_shortcode($atts, $content){
		extract(shortcode_atts(array(
			'title'					=> ''
			,'title_style'			=> ''
			,'use_logo_setting'		=> 1
			,'per_page' 			=> 6
			,'columns' 				=> 5
			,'first_level' 			=> 0
			,'hide_empty'			=> 1
			,'show_title'			=> 1
			,'show_product_count'	=> 0
			,'show_nav' 			=> 1
			,'auto_play' 			=> 1
			,'margin'				=> 0
		),$atts));

		if ( !class_exists('WooCommerce') ){
			return;
		}

		$args = array(
			'taxonomy'	  => 'ts_product_brand'
			,'orderby'    => 'name'
			,'order'      => 'ASC'
			,'hide_empty' => $hide_empty
			,'pad_counts' => true
			,'number'     => $per_page
		);
		if( $first_level ){
			$args['parent'] = 0;
		}
		$product_brands = get_terms($args);
		
		ob_start();
		
		if( count($product_brands) > 0 ):
			$classes = array();
			$classes[] = 'ts-product-brand-wrapper ts-product ts-shortcode ts-slider';
			$classes[] = $title_style;
			$classes[] = $use_logo_setting?'use-logo-setting':'';
			if( $show_nav ){
				$classes[] = 'show-nav';
				$classes[] = 'nav-middle';
			}
		
			$data_attr = array();
			$data_attr[] = 'data-nav="'.$show_nav.'"';
			$data_attr[] = 'data-autoplay="'.$auto_play.'"';
			$data_attr[] = 'data-margin="'.$margin.'"';
			$data_attr[] = 'data-columns="'.$columns.'"';
			
			if( $use_logo_setting ){
				$settings_option = get_option('ts_logo_setting', array());
				$data_break_point = isset($settings_option['responsive']['break_point'])?$settings_option['responsive']['break_point']:array();
				$data_item = isset($settings_option['responsive']['item'])?$settings_option['responsive']['item']:array();
				
				$data_attr[] = 'data-break_point="'.htmlentities(json_encode( $data_break_point )).'"';
				$data_attr[] = 'data-item="'.htmlentities(json_encode( $data_item )).'"';
			}
		?>
			<div class="<?php echo esc_attr(implode(' ', $classes)) ?>" <?php echo implode(' ', $data_attr); ?>>
				<?php if( $title ): ?>
					<header class="shortcode-heading-wrapper">
						<h2 class="shortcode-title"><?php echo esc_html($title); ?></h2>
					</header>
				<?php endif; ?>
				<div class="content-wrapper loading items">
					<?php 
					foreach( $product_brands as $brand ){
						$brand_link = get_term_link($brand, 'ts_product_brand');
						$thumbnail_id = absint(get_term_meta( $brand->term_id, 'thumbnail_id', true ));
						$image_size = $use_logo_setting?'ts_logo_thumb':'woocommerce_thumbnail';
						?>
						<div class="item">
							<a href="<?php echo esc_url( $brand_link ) ?>">
							<?php
							if( $thumbnail_id ){
								echo wp_get_attachment_image($thumbnail_id, $image_size);
							}
							else{
								echo wc_placeholder_img();
							}
							?>
							</a>
							<div class="meta-wrapper">
								<?php if( $show_title ): ?>
								<h3 class="heading-title">
									<a href="<?php echo esc_url($brand_link); ?>"><?php echo $brand->name; ?></a>
								</h3>
								<?php endif; ?>
								<?php if( $show_product_count ): ?>
								<div class="count"><?php echo sprintf( _n( '%s Product', '%s Products', $brand->count, 'themesky' ), $brand->count ); ?></div>
								<?php endif; ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		<?php
		endif;
		
		return '<div class="woocommerce columns-'.$columns.'">' . ob_get_clean() . '</div>';			
	}
}
add_shortcode('ts_product_brands', 'ts_product_brands_shortcode');

/* TS Product Deals */
if( !function_exists('ts_product_deals_shortcode') ){
	function ts_product_deals_shortcode($atts, $content = null){

		extract(shortcode_atts(array(
				'title'					=> ''
				,'title_style' 			=> 'title-center'
				,'layout' 				=> 'slider'
				,'product_type'			=> 'recent'
				,'columns' 				=> 4
				,'per_page' 			=> 5
				,'product_cats'			=> ''
				,'ids'					=> ''
				,'show_counter'			=> 1
				,'show_image' 			=> 1
				,'show_title' 			=> 1
				,'show_sku' 			=> 0
				,'show_price' 			=> 1
				,'show_short_desc'  	=> 0
				,'short_desc_words'  	=> 8
				,'show_rating' 			=> 1
				,'show_label' 			=> 1	
				,'show_categories'		=> 0	
				,'show_add_to_cart' 	=> 1
				,'shop_more_text'		=> ''
				,'shop_more_link'		=> ''
				,'image_border'			=> 0
				,'text_align'			=> ''
				,'show_nav'				=> 1
				,'auto_play'			=> 1
				,'margin'				=> 0
			), $atts));			

			if( !class_exists('WooCommerce') ){
				return;
			}
			
			$product_ids_on_sale = ts_get_product_deals_ids();
			
			if( $ids ){
				$ids = array_map('trim', explode(',', $ids));
				$product_ids_on_sale = array_intersect($product_ids_on_sale, $ids);
			}
			
			if( !$product_ids_on_sale ){
				return;
			}
			
			$per_page = absint($per_page);
			
			add_filter('drile_product_short_desc_limit_words', function() use ($short_desc_words){
				return absint($short_desc_words);
			});
			
			if( $show_counter ){
				add_action('woocommerce_after_shop_loop_item', 'ts_template_loop_time_deals', 65);
			}
			
			/* Remove hook */
			$options = array(
					'show_image'		=> $show_image
					,'show_label'		=> $show_label
					,'show_title'		=> $show_title
					,'show_sku'			=> $show_sku
					,'show_price'		=> $show_price
					,'show_short_desc'	=> $show_short_desc
					,'show_categories'	=> $show_categories
					,'show_rating'		=> $show_rating
					,'show_add_to_cart'	=> $show_add_to_cart
				);
			ts_remove_product_hooks_shortcode( $options );

			global $post, $product;
			if( (int)$columns <= 0 ){
				$columns = 5;
			}
			
			$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
			wc_set_loop_prop('columns', $columns);
			
			$args = array(
				'post_type'				=> 'product'
				,'post_status' 			=> 'publish'
				,'posts_per_page' 		=> $per_page
				,'orderby' 				=> 'date'
				,'order' 				=> 'desc'
				,'post__in'				=> $product_ids_on_sale
				,'meta_query' 			=> WC()->query->get_meta_query()
				,'tax_query'           	=> WC()->query->get_tax_query()
			);
			
			ts_filter_product_by_product_type($args, $product_type);
			
			if( $product_cats ){
				$product_cats = array_map('trim', explode(',', $product_cats));
				$args['tax_query'][] = array(
								'taxonomy' 	=> 'product_cat'
								,'terms' 	=> $product_cats
								,'field' 	=> 'term_id'
							);
			}
			
			$products = new WP_Query($args);
			
			ob_start();
			
			if( $products->have_posts() ): 
				$classes = array();
				$classes[] = 'ts-product-deals-wrapper ts-shortcode ts-product heading-center';
				$classes[] = $show_image?'':'no-thumbnail';
				$classes[] = $text_align;
				$classes[] = 'layout-' . $layout;
				$classes[] = $title_style;
				if( $image_border ){
					$classes[] = 'image-border';
				}
				if( $layout == 'slider' ){
					$classes[] = 'ts-slider';
					$classes[] = 'nav-middle middle-thumbnail';
				}
				$classes = array_filter($classes);
				
				$data_attr = array();
				if( $layout == 'slider' ){
					$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
					$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
					$data_attr[] = 'data-margin="'.esc_attr($margin).'"';
					$data_attr[] = 'data-columns="'.esc_attr($columns).'"';
				}
				?>
				<div class="<?php echo esc_attr( implode(' ', $classes) ); ?>" <?php echo implode(' ', $data_attr); ?>>
				
					<?php if( strlen($title) > 0 ): ?>
					<header class="shortcode-heading-wrapper">
					
						<?php if( strlen($title) > 0 ): ?>
						<h2 class="shortcode-title">
							<?php echo esc_html($title); ?>
						</h2>
						<?php endif; ?>
						
					</header>
					<?php endif; ?>
					
					<div class="content-wrapper <?php echo ($layout == 'slider')?'loading':''; ?>">
						<?php woocommerce_product_loop_start(); ?>				

						<?php while( $products->have_posts() ): $products->the_post(); ?>
							<?php wc_get_template_part( 'content', 'product' ); ?>							
						<?php endwhile; ?>			

						<?php woocommerce_product_loop_end(); ?>
					</div>
					
					<?php if( strlen($shop_more_text) > 0 ): ?>
					<div class="shop-more">
						<a class="button button-text" href="<?php echo esc_url($shop_more_link); ?>"><?php echo esc_html($shop_more_text) ?></a>
					</div>
					<?php endif; ?>
					
				</div>
				<?php
			endif;
			
			wp_reset_postdata();
			
			/* restore hooks */
			if( $show_counter ){
				remove_action('woocommerce_after_shop_loop_item', 'ts_template_loop_time_deals', 65);
			}
			
			remove_all_filters('drile_product_short_desc_limit_words');

			ts_restore_product_hooks_shortcode();

			wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
			
			return '<div class="woocommerce columns-'.$columns.'">' . ob_get_clean() . '</div>';
	}
}
add_shortcode('ts_product_deals', 'ts_product_deals_shortcode');

if( !function_exists('ts_product_availability_bar') ){
	function ts_product_availability_bar(){
		global $product;
		$total_sales = $product->get_total_sales();
		$stock_quantity = $product->get_stock_quantity();
		if( $stock_quantity ){
			$total = $total_sales + $stock_quantity;
			$percent = $stock_quantity * 100 / $total;
		?>
		<div class="availability-bar">
			<span class="available"><?php esc_html_e('Available:', 'themesky') ?> <?php echo esc_html($stock_quantity) ?></span>
			<span class="sold"><?php esc_html_e('Already Sold:', 'themesky') ?> <?php echo esc_html($total_sales) ?></span>
			<div class="progress-bar">
				<span style="width:<?php echo number_format($percent, 2) ?>%"></span>
			</div>
		</div>
		<?php
		}
	}
}

if( !function_exists('ts_template_loop_time_deals') ){
	function ts_template_loop_time_deals(){
		global $product;
		$date_to = '';
		$date_from = '';
		if( $product->get_type() == 'variable' ){
			$children = $product->get_children();
			if( is_array($children) && count($children) > 0 ){
				foreach( $children as $children_id ){
					$date_to = get_post_meta($children_id, '_sale_price_dates_to', true);
					$date_from = get_post_meta($children_id, '_sale_price_dates_from', true);
					if( $date_to != '' ){
						break;
					}
				}
			}
		}
		else{
			$date_to = get_post_meta($product->get_id(), '_sale_price_dates_to', true);
			$date_from = get_post_meta($product->get_id(), '_sale_price_dates_from', true);
		}
		
		$current_time = current_time('timestamp', true);
		
		if( $date_to == '' || $date_from == '' || $date_from > $current_time || $date_to < $current_time ){
			return;
		}
		
		$delta = $date_to - $current_time;
		
		$time_day = 60 * 60 * 24;
		$time_hour = 60 * 60;
		$time_minute = 60;
		
		$day = floor( $delta / $time_day );
		$delta -= $day * $time_day;
		
		$hour = floor( $delta / $time_hour );
		$delta -= $hour * $time_hour;
		
		$minute = floor( $delta / $time_minute );
		$delta -= $minute * $time_minute;
		
		if( $delta > 0 ){
			$second = $delta;
		}
		else{
			$second = 0;
		}
		
		$day = zeroise($day, 2);
		$hour = zeroise($hour, 2);
		$minute = zeroise($minute, 2);
		$second = zeroise($second, 2);

		?>
		<div class="counter-wrapper days-<?php echo strlen($day); ?>">
			<div class="days">
				<div class="number-wrapper">
					<span class="number"><?php echo esc_html($day); ?></span>
				</div>
				<div class="ref-wrapper">
					<?php esc_html_e('days', 'themesky'); ?>
				</div>
			</div>
			<div class="hours">
				<div class="number-wrapper">
					<span class="number"><?php echo esc_html($hour); ?></span>
				</div>
				<div class="ref-wrapper">
					<?php esc_html_e('hours', 'themesky'); ?>
				</div>
			</div>
			<div class="minutes">
				<div class="number-wrapper">
					<span class="number"><?php echo esc_html($minute); ?></span>
				</div>
				<div class="ref-wrapper">
					<?php esc_html_e('mins', 'themesky'); ?>
				</div>
			</div>
			<div class="seconds">
				<div class="number-wrapper">
					<span class="number"><?php echo esc_html($second); ?></span>
				</div>
				<div class="ref-wrapper">
					<?php esc_html_e('secs', 'themesky'); ?>
				</div>
			</div>
		</div>
		<?php
	}
}

/* Product in category tabs */
if( !function_exists('ts_products_in_category_tabs_shortcode') ){
	function ts_products_in_category_tabs_shortcode($atts, $content){

		extract(shortcode_atts(array(
			'title'							=> ''
			,'style'						=> 'style-horizontal'
			,'banners'						=> ''
			,'icons'						=> ''
			,'hover_icons'					=> ''
			,'bg_color'						=> '#ebebeb'
			,'bg_color_hover'				=> '#202020'
			,'product_type'					=> 'recent'
			,'show_product_count' 			=> 0
			,'columns' 						=> 4
			,'per_page' 					=> 8
			,'product_cats'					=> ''
			,'parent_cat' 					=> ''
			,'include_children' 			=> 0
			,'show_general_tab' 			=> 0
			,'general_tab_heading' 			=> ''
			,'product_type_general_tab' 	=> 'recent'
			,'show_image' 					=> 1
			,'show_title' 					=> 1
			,'show_sku' 					=> 0
			,'show_price' 					=> 1
			,'show_short_desc'  			=> 0
			,'show_rating' 					=> 0
			,'show_label' 					=> 1
			,'show_categories'				=> 0	
			,'show_add_to_cart' 			=> 1
			,'show_color_swatch' 			=> 0
			,'number_color_swatch' 			=> 3
			,'show_shop_more_button' 		=> 0
			,'show_shop_more_general_tab' 	=> 0
			,'shop_more_button_text' 		=> 'Shop more'
			,'extra_class' 					=> ''
			,'text_align'					=> ''
			,'image_border'					=> 0
			,'is_slider' 					=> 0
			,'only_slider_mobile'			=> 0
			,'rows' 						=> 1
			,'show_nav' 					=> 1
			,'show_dots' 					=> 0
			,'auto_play' 					=> 1
			,'margin'						=> 0
		), $atts));
		if ( !class_exists('WooCommerce') ){
			return;
		}
				
		if( !$product_cats && !$parent_cat ){
			return;
		}
		
		if( !$product_cats ){
			$sub_cats = get_terms(array('taxonomy' => 'product_cat', 'parent' => $parent_cat, 'fields' => 'ids', 'orderby' => 'none'));
			if( is_array($sub_cats) && !empty($sub_cats) ){
				$product_cats = implode(',', $sub_cats);
			}
			else{
				return;
			}
		}
		else{
			$parent_cat = '';
		}
		
		if( $only_slider_mobile && !wp_is_mobile() ){
			$is_slider = 0;
		}
		
		if( $banners ){
			$banners = array_map('trim', explode(',', $banners));
		}
		
		$lazy_load_icon = false;
		if( $icons ){
			$icons = array_map('trim', explode(',', $icons));
			$lazy_load_icon = apply_filters('ts_lazy_load_icon_products_in_category_tabs', true);
		}
		
		if( $hover_icons ){
			$hover_icons = array_map('trim', explode(',', $hover_icons));
		}
		
		if( $show_dots ){
			$show_nav = 0;
		}
		
		$atts = compact('product_type', 'columns', 'rows', 'per_page' ,'product_cats', 'include_children'
						,'show_image', 'show_title', 'show_sku', 'show_price', 'show_short_desc', 'show_rating', 'show_label' ,'show_categories', 'show_add_to_cart', 'show_color_swatch', 'number_color_swatch'
						,'show_shop_more_button', 'show_shop_more_general_tab', 'show_general_tab', 'product_type_general_tab', 'is_slider', 'show_nav', 'show_dots', 'auto_play', 'margin');
		
		$classes = array();
		$classes[] = 'ts-product-in-category-tab-wrapper ts-shortcode ts-product heading-center';
		$classes[] = $product_type;
		$classes[] = $style;
		$classes[] = $text_align;
		$classes[] = $extra_class;
		if( $style == 'style-verticle' && $banners ){
			$classes[] = 'has-banner';
		}
		if( $show_color_swatch ){
			$classes[] = 'show-color-swatch';
		}
		if( $image_border ){
			$classes[] = 'image-border';
		}
		if( $show_dots ){
			$classes[] = 'show-dots';
		}
		if( $show_shop_more_button ){
			$classes[] = 'has-shop-more-button';
		}
		else{
			$classes[] = 'no-shop-more-button';
		}
		
		if( $is_slider ){
			$classes[] = 'ts-slider';
			$classes[] = 'rows-'.$rows;
			if( $show_nav ){
				$classes[] = 'show-nav nav-middle';
			}
		}
		$current_cat = '';
		$is_general_tab = false;
		$shop_more_link = '#';
		
		$rand_id = 'ts-product-in-category-tab-'.mt_rand(0, 1000);
		
		$inline_style = '';
		if( $style == 'style-horizontal-icons' || $style == 'style-verticle-icons' || $style == 'style-verticle' ){
			$selector = '#' . $rand_id . ' .list-categories ul';
			$inline_style = '<div class="ts-shortcode-custom-style hidden">';
				if( $bg_color ){
					$inline_style .= $selector.' li{background:'.$bg_color.';}';
				}
				if( $bg_color_hover ){
					$inline_style .= $selector.' li:hover, '.$selector.' li.current{background:'.$bg_color_hover.';}';
				}
			$inline_style .= '</div>';
		}
		
		ob_start();
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" id="<?php echo esc_attr($rand_id) ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>">
			<?php echo trim( $inline_style ); ?>
			<?php 
				$title = ($title) ? '<header class="heading-tab"><h2 class="heading-title">'.esc_html($title).'</h2></header>' : ''; 
				if( $style == 'style-horizontal' ){
					echo '<div class="column-tabs">';
					echo $title;
				}else{
					echo $title;
					echo '<div class="column-tabs">';
				}
			?>
		
				<div class="list-categories">
					<ul class="tabs <?php echo $style == 'style-horizontal-icons'?'loading':''; ?>">
					<?php 
					if( $lazy_load_icon ){
						add_filter('wp_calculate_image_srcset_meta', '__return_empty_array', 999999);
						add_filter('wp_get_attachment_image_attributes', function($attr){
							$src = $attr['src'];
							$attr['src'] = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201%201'%3E%3C/svg%3E";
							$attr['data-src'] = $src;
							$attr['class'] .= ' ts-lazy-load';
							return $attr;
						}, 999999);
					}
					
					if( $show_general_tab ){
						if( $parent_cat ){
							$current_cat = $parent_cat;
							$shop_more_link = get_term_link((int)$parent_cat, 'product_cat');
							if( is_wp_error($shop_more_link) ){
								$shop_more_link = wc_get_page_permalink('shop');
							}
						}
						else{
							$current_cat = $product_cats;
							$shop_more_link = wc_get_page_permalink('shop');
						}
						$is_general_tab = true;
					?>
						<li class="tab-item general-tab current" data-product_cat="<?php echo $current_cat; ?>" data-link="<?php echo esc_url($shop_more_link) ?>">
							<?php 
							if( isset($icons[0]) ){
								$icon_class = isset($hover_icons[0])?'has-hover-icon':'';
								echo '<span class="icon '.$icon_class.'">';
									echo wp_get_attachment_image($icons[0], 'thumbnail');
									if( isset($hover_icons[0]) ){
										echo wp_get_attachment_image($hover_icons[0], 'thumbnail');
									}
								echo '</span>';
							}
							?>
							<span class="category-name"><?php echo esc_html($general_tab_heading) ?></span>
						</li>
					<?php
					}
					
					$product_cats = array_map('trim', explode(',', $product_cats));
					foreach( $product_cats as $k => $product_cat ):
						$i = $k;
						$term = get_term_by( 'term_id', $product_cat, 'product_cat');
						if( !isset($term->name) ){
							continue;
						}
						$current_tab = false;
						if( $current_cat == '' ){
							$current_tab = true;
							$current_cat = $product_cat;
							$shop_more_link = get_term_link($term, 'product_cat');
						}
					?>
						<li class="tab-item <?php echo ($current_tab)?'current':''; ?>" data-product_cat="<?php echo esc_attr($product_cat) ?>" data-link="<?php echo esc_url(get_term_link($term, 'product_cat')) ?>">
							<?php
							if( $show_general_tab ){
								$k++;
							}
							if( isset($icons[$k]) ){
								$icon_class = isset($hover_icons[$k])?'has-hover-icon':'';
								echo '<span class="icon '.$icon_class.'">';
									echo wp_get_attachment_image($icons[$k], 'thumbnail');
									if( isset($hover_icons[$k]) ){
										echo wp_get_attachment_image($hover_icons[$k], 'thumbnail');
									}
								echo '</span>';
							}
							?>
							<span>
								<span class="category-name"><?php echo esc_html($term->name) ?></span>
								<?php if( $show_product_count ): ?>
									<span class="category-count"><?php echo sprintf( _n('%d Product', '%d Products', $term->count, 'themesky'), $term->count ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php
					endforeach;
					
					if( $lazy_load_icon ){
						remove_all_filters('wp_calculate_image_srcset_meta', 999999);
						remove_all_filters('wp_get_attachment_image_attributes', 999999);
					}
					?>
					</ul>
				</div>
				
				<?php
					if( $style == 'style-verticle' && $banners ){
						$banner_urls = array();
						foreach( $banners as $banner ){
							$banner_urls[] = wp_get_attachment_image_url($banner, 'full');
						}
						echo '<a href="'.esc_url($shop_more_link).'" class="banners" style="background-image:url('.$banner_urls[0].');" data-banner_urls="'.implode(',', $banner_urls).'"></a>';
					}
				?>
			</div>
			
			<div class="column-content">
				<div class="column-products loading woocommerce columns-<?php echo esc_attr($columns) ?>">
					<?php echo ts_get_product_content_in_category_tab($atts, $current_cat, $is_general_tab); ?>
				</div>
				
				<?php if( $show_shop_more_button ): ?>
				<div class="shop-more">
					<a class="button shop-more-button" href="<?php echo esc_url($shop_more_link) ?>"><?php echo esc_html($shop_more_button_text) ?></a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
		
		return ob_get_clean();
	}	
}
add_shortcode('ts_products_in_category_tabs', 'ts_products_in_category_tabs_shortcode');

add_action('wp_ajax_ts_get_product_content_in_category_tab', 'ts_get_product_content_in_category_tab');
add_action('wp_ajax_nopriv_ts_get_product_content_in_category_tab', 'ts_get_product_content_in_category_tab');
if( !function_exists('ts_get_product_content_in_category_tab') ){
	function ts_get_product_content_in_category_tab( $atts = array(), $product_cat = '', $is_general_tab = false ){
		if( wp_doing_ajax() ){
			if( empty($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$product_cat = isset($_POST['product_cat'])?$_POST['product_cat']:'';
			$is_general_tab = (isset($_POST['is_general_tab']) && $_POST['is_general_tab'])?true:false;
		}
		
		if( $is_general_tab ){
			$atts['product_type'] = $atts['product_type_general_tab'];
		}
		
		ob_start();
		extract($atts);
		
		$options = array(
				'show_image'			=> $show_image
				,'show_label'			=> $show_label
				,'show_title'			=> $show_title
				,'show_sku'				=> $show_sku
				,'show_price'			=> $show_price
				,'show_short_desc'		=> $show_short_desc
				,'show_categories'		=> $show_categories
				,'show_rating'			=> $show_rating
				,'show_add_to_cart'		=> $show_add_to_cart
				,'show_color_swatch'	=> $show_color_swatch
				,'number_color_swatch'	=> $number_color_swatch
			);
		ts_remove_product_hooks_shortcode( $options );
		
		$args = array(
			'post_type'				=> 'product'
			,'post_status' 			=> 'publish'
			,'ignore_sticky_posts'	=> 1
			,'posts_per_page' 		=> $per_page
			,'orderby' 				=> 'date'
			,'order' 				=> 'desc'
			,'meta_query' 			=> WC()->query->get_meta_query()
			,'tax_query'           	=> WC()->query->get_tax_query()
		);

		ts_filter_product_by_product_type($args, $product_type);
		
		if( $product_cat ){
			$args['tax_query'][] = array(
									'taxonomy' => 'product_cat'
									,'terms' => array_map('trim', explode(',', $product_cat))
									,'field' => 'term_id'
									,'include_children' => $include_children
									);
		}
		
		if( (int)$columns <= 0 ){
			$columns = 3;
		}
		
		$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
		wc_set_loop_prop('columns', $columns);

		$products = new WP_Query( $args );
		
		$count = 0;
		
		woocommerce_product_loop_start();
		if( $products->have_posts() ){	

			if( isset($show_shop_more_button, $products->found_posts, $products->post_count) && $products->found_posts == $products->post_count ){
				echo '<div class="hidden hide-shop-more"></div>';
			}

			while( $products->have_posts() ){ 
				$products->the_post();
				
				if( $is_slider && $rows > 1 && $count % $rows == 0 ){
					echo '<div class="product-group">';
				}
				
				wc_get_template_part( 'content', 'product' );
				
				if( $is_slider && $rows > 1 && ($count % $rows == $rows - 1 || $count == $products->post_count - 1) ){
					echo '</div>';
				}
				$count++;
			}

		}
		woocommerce_product_loop_end();
		
		wp_reset_postdata();

		/* restore hooks */
		ts_restore_product_hooks_shortcode();

		wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
		
		if( wp_doing_ajax() ){
			die(ob_get_clean());
		}
		else{
			return ob_get_clean();
		}
	}
}

/* Product in product type tabs */
if( !function_exists('ts_products_in_product_type_tabs_shortcode') ){
	function ts_products_in_product_type_tabs_shortcode($atts, $content){

		extract(shortcode_atts(array(
			'title'							=> ''
			,'tab_1'					    => 1
			,'tab_1_heading'				=> 'Featured'
			,'tab_1_product_type'			=> 'featured'
			,'tab_2'					    => 1
			,'tab_2_heading'				=> 'Best Selling'
			,'tab_2_product_type'			=> 'best_selling'
			,'tab_3'					    => 1
			,'tab_3_heading'				=> 'On Sale'
			,'tab_3_product_type'			=> 'sale'
			,'tab_4'					    => 1
			,'tab_4_heading'				=> 'Top Rated'
			,'tab_4_product_type'			=> 'top_rated'
			,'tab_5'					    => 1
			,'tab_5_heading'				=> 'Recent'
			,'tab_5_product_type'			=> 'recent'
			,'color'						=> '#27af7d'
			,'active_tab'					=> 1
			,'columns' 						=> 4
			,'per_page' 					=> 6
			,'item_layout' 					=> 'grid'
			,'product_cats'					=> ''
			,'include_children' 			=> 1
			,'text_align'					=> ''
			,'image_border'					=> 0
			,'show_image' 					=> 1
			,'show_title' 					=> 1
			,'show_sku' 					=> 0
			,'show_price' 					=> 1
			,'show_short_desc'  			=> 0
			,'show_rating' 					=> 0
			,'show_label' 					=> 1
			,'show_categories'				=> 0	
			,'show_add_to_cart' 			=> 1
			,'show_color_swatch' 			=> 0
			,'number_color_swatch' 			=> 3
			,'is_slider' 					=> 1
			,'only_slider_mobile'			=> 0
			,'rows' 						=> 1
			,'show_nav' 					=> 1
			,'show_nav_as_text' 			=> 0
			,'prev_text' 					=> 'Back'
			,'next_text' 					=> 'Next'
			,'auto_play' 					=> 1
			,'margin'						=> 0
		), $atts));
		if ( !class_exists('WooCommerce') ){
			return;
		}
		
		if( !$tab_1 && !$tab_2 && !$tab_3 && !$tab_4 && !$tab_5 ){
			return;
		}
		
		if( $only_slider_mobile && !wp_is_mobile() ){
			$is_slider = 0;
		}
		
		$tabs = array();
		for( $i = 1; $i <= 5; $i++ ){
			if( ${'tab_' . $i} ){
				$tabs[] = array(
					'heading'		=> ${'tab_' . $i . '_heading'}
					,'product_type'	=> ${'tab_' . $i . '_product_type'}
				);
			}
		}
		
		if( $active_tab > count($tabs) ){
			$active_tab = 1;
		}
		
		if( !$product_cats ){
			$show_list_categories = 0;
		}
		
		$product_type = $tabs[$active_tab-1]['product_type'];
		
		$atts = compact('columns', 'rows', 'per_page', 'product_cats', 'include_children', 'product_type'
						,'show_image', 'show_title', 'show_sku', 'show_price', 'show_short_desc', 'show_rating', 'show_label'
						,'show_categories', 'show_add_to_cart', 'show_color_swatch', 'number_color_swatch', 'is_slider', 'show_nav', 'prev_text', 'next_text', 'auto_play', 'margin');
		
		$classes = array();
		$classes[] = 'ts-product-in-product-type-tab-wrapper ts-shortcode ts-product heading-center';
		$classes[] = $text_align;
		$classes[] = 'item-'.$item_layout;
		if( $image_border ){
			$classes[] = 'image-border';
		}
		if( $show_color_swatch ){
			$classes[] = 'show-color-swatch';
		}
		if( $is_slider ){
			$classes[] = 'ts-slider';
			$classes[] = 'rows-'.$rows;
			if( $show_nav ){
				$classes[] = 'show-nav nav-middle';
			}
			if( $show_nav_as_text ){
				$classes[] = 'show-nav-as-text';
			}
		}
		
		$classes = array_filter($classes);
		
		$rand_id = 'ts-product-in-product-type-tab-'.mt_rand(0, 1000);
		
		ob_start();
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" id="<?php echo esc_attr($rand_id) ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>">
			<div class="column-tabs">
			
				<?php if( strlen($title) > 0 ): ?>
				<header class="heading-tab">
					<h2 class="heading-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
				<?php endif; ?>
				
				<ul class="tabs">
				<?php
				foreach( $tabs as $i => $tab ):
				?>
					<li class="tab-item <?php echo ($active_tab == $i + 1)?'active current':''; ?>" data-product_type="<?php echo esc_attr($tab['product_type']) ?>"><?php echo esc_html($tab['heading']) ?></li>
				<?php
				endforeach;
				?>
				</ul>
			</div>
			
			<div class="column-content">
			
				<div class="column-products loading woocommerce columns-<?php echo esc_attr($columns) ?> <?php echo $product_type; ?>">
					<?php echo ts_get_product_content_in_category_tab($atts, $product_cats); ?>
				</div>
				
			</div>
		</div>
		<?php
		
		return ob_get_clean();
	}	
}
add_shortcode('ts_products_in_product_type_tabs', 'ts_products_in_product_type_tabs_shortcode');
?>