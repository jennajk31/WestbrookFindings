<?php 
/************************************
*** Custom Post Type Shortcodes
*************************************/
/*** Shortcode Team memmber ***/
function ts_team_members_shortcode($atts){
	extract(shortcode_atts(array(						
						'limit'				=> 6
						,'ids'				=> ''
						,'columns'			=> 3
						,'target'			=> '_blank'	
						,'is_slider'		=> 0						
						,'show_nav'			=> 1				
						,'auto_play'		=> 1		
						,'margin'			=> 0		
					), $atts ));
	
	$columns = absint($columns);
	if( !in_array($columns, array(1,2,3,4,5,6)) || ($is_slider == 0 && $columns == 5) ){
		$columns = 3;
	}
	
	ob_start();
	global $post, $ts_team_members;
	$thumb_size_name = isset($ts_team_members->thumb_size_name)?$ts_team_members->thumb_size_name:'ts_team_thumb';
	
	$args = array(
				'post_type'				=> 'ts_team'
				,'post_status'			=> 'publish'
				,'posts_per_page'		=> $limit
			);

	if( $ids ){
		$args['post__in'] = array_map('trim', explode(',', $ids));
		$args['orderby'] = 'post__in';
	}
	
	$team = new WP_Query($args);
	if( $team->have_posts() ){
		$classes = array();
		$classes[] = 'ts-team-members ts-shortcode';
		$item_class = '';
		$item_extra_class = '';
		if( $is_slider ){
			$classes[] = 'ts-slider';
			if( $show_nav ){
				$classes[] = 'show-nav';
				$classes[] = 'nav-middle';
				$classes[] = 'middle-thumbnail';
			}
		}
		else{
			$item_class = 'ts-col-' . (24/$columns);
		}
		
		$data_attr = array();
		if( $is_slider ){
			$data_attr[] = 'data-nav="'.$show_nav.'"';
			$data_attr[] = 'data-auto_play="'.$auto_play.'"';
			$data_attr[] = 'data-columns="'.$columns.'"';
			$data_attr[] = 'data-margin="'.$margin.'"';
		}
		$key = -1;
		?>
		<div class="<?php echo esc_attr( implode(' ', $classes) ) ?>" <?php echo implode(' ', $data_attr); ?>>
			<div class="items <?php echo $is_slider?'loading':''; ?>">
		<?php
		while( $team->have_posts() ){
			$key ++;
			if( $key == 0 ){
				$item_extra_class = 'first';
			}
			else{
				$item_extra_class = ($key % $columns == 0)?'first':(($key % $columns == $columns - 1)?'last':'');
			}
			$team->the_post();
			$profile_link = get_post_meta($post->ID, 'ts_profile_link', true);
			if( $profile_link == '' ){
				$profile_link = '#';
			}
			$name = get_the_title($post->ID);
			$role = get_post_meta($post->ID, 'ts_role', true);
			
			$facebook_link = get_post_meta($post->ID, 'ts_facebook_link', true);
			$twitter_link = get_post_meta($post->ID, 'ts_twitter_link', true);
			$linkedin_link = get_post_meta($post->ID, 'ts_linkedin_link', true);
			$rss_link = get_post_meta($post->ID, 'ts_rss_link', true);
			$dribbble_link = get_post_meta($post->ID, 'ts_dribbble_link', true);
			$pinterest_link = get_post_meta($post->ID, 'ts_pinterest_link', true);
			$instagram_link = get_post_meta($post->ID, 'ts_instagram_link', true);
			$custom_link = get_post_meta($post->ID, 'ts_custom_link', true);
			$custom_link_icon_class = get_post_meta($post->ID, 'ts_custom_link_icon_class', true);
			
			$social_content = '';
			
			if( $facebook_link ){
				$social_content .= '<a class="facebook" href="'.esc_url($facebook_link).'" target="'.$target.'"><i class="fab fa-facebook-f"></i></a>';
			}
			if( $twitter_link ){
				$social_content .= '<a class="twitter" href="'.esc_url($twitter_link).'" target="'.$target.'"><i class="fab fa-twitter"></i></a>';
			}
			if( $linkedin_link ){
				$social_content .= '<a class="linked" href="'.esc_url($linkedin_link).'" target="'.$target.'"><i class="fab fa-linkedin-in"></i></a>';
			}
			if( $rss_link ){
				$social_content .= '<a class="rss" href="'.esc_url($rss_link).'" target="'.$target.'"><i class="fas fa-rss"></i></a>';
			}
			if( $dribbble_link ){
				$social_content .= '<a class="dribbble" href="'.esc_url($dribbble_link).'" target="'.$target.'"><i class="fab fa-dribbble"></i></a>';
			}
			if( $pinterest_link ){
				$social_content .= '<a class="pinterest" href="'.esc_url($pinterest_link).'" target="'.$target.'"><i class="fab fa-pinterest-p"></i></a>';
			}
			if( $instagram_link ){
				$social_content .= '<a class="instagram" href="'.esc_url($instagram_link).'" target="'.$target.'"><i class="fab fa-instagram"></i></a>';
			}
			if( $custom_link ){
				$social_content .= '<a class="custom" href="'.esc_url($custom_link).'" target="'.$target.'"><i class="'.esc_attr($custom_link_icon_class).'"></i></a>';
			}
			
			?>
			<div class="item <?php echo $item_class ?> <?php echo (has_post_thumbnail())?'has-thumbnail':'' ?> <?php echo $item_extra_class ?>">
				<div class="team-content">
					<?php if( has_post_thumbnail() ): ?>
						<div class="image-thumbnail">
						
							<div class="image-content">
								<figure>
								<a href="<?php echo esc_url($profile_link); ?>" target="<?php echo esc_attr($target) ?>"><?php the_post_thumbnail($thumb_size_name); ?></a>
								</figure>
								
							</div>
							
						</div>
					<?php endif; ?>
					
					<div class="team-info">
					
						<header>
						
							<h3 class="name semibold"><a href="<?php echo esc_url($profile_link); ?>" target="<?php echo esc_attr($target) ?>"><?php echo esc_html($name); ?></a></h3>
							
							<span class="member-role"><?php echo esc_html($role); ?></span>
							
						</header>
						
						<?php if( $social_content): ?>
						<span class="member-social"><?php echo $social_content; ?></span>
						<?php endif; ?>
					
					</div>
				</div>
				
			</div>
			<?php
		}
		?>
			</div>
		</div>
		<?php
	}
	
	wp_reset_postdata();
	
	return ob_get_clean();
}
add_shortcode('ts_team_members', 'ts_team_members_shortcode');

/*** Shortcode Image Box ***/
function ts_image_box_shortcode( $atts ){
	extract(shortcode_atts(array(
						'img_id'					=> ''
						,'img_url'					=> ''
						,'img_size'					=> ''
						,'image_position'			=> 'image-left'
						,'title'					=> ''
						,'description'				=> ''	
						,'button_text'				=> 'shop now'
						,'link' 					=> '#'		
						,'target' 					=> '_blank'
						,'extra_class'				=> ''
					), $atts ));
	
	ob_start();
	$classes = array();
	$classes[] = 'ts-image-box';
	$classes[] = $image_position;
	
	?>
	<div class="<?php echo esc_attr( implode(' ', $classes) ) ?>">
	
		<?php if( $image_position == 'image-right' ): ?>

		<div class="box-header">
			
			<?php if( strlen($title) > 0 ): ?>
			<h2 class="feature-title heading-title">
				<?php echo esc_html($title); ?>
			</h2>
			<?php endif; ?>
			
			<?php if( strlen($description) > 0 ): ?>
			<div class="box-description">
				<?php echo esc_html($description) ?>
			</div>
			<?php endif; ?>
		
			<?php if( strlen($button_text) > 0): ?>
			<a target="<?php echo esc_attr($target); ?>" href="<?php echo ($link)?esc_url($link):'javascript: void(0)' ?>" class="button see-more"><?php echo esc_html($button_text); ?></a>
			<?php endif; ?>
		
		</div>
		
		<div class="image-thumbnail">
			
			<a target="<?php echo esc_attr($target); ?>" href="<?php echo ($link)?esc_url($link):'javascript: void(0)' ?>" class="thumbnail">
				<?php 
				if( $img_url ){
				?>
					<img src="<?php echo esc_url($img_url); ?>">
				<?php
				}
				else{
					echo wp_get_attachment_image($img_id, 'full', 0, array('class'=>''));
				}
				?>
			</a>
			
		</div>
		
		<?php else: ?>
		
		<div class="image-thumbnail">
		
			<a target="<?php echo esc_attr($target); ?>" href="<?php echo ($link)?esc_url($link):'javascript: void(0)' ?>" class="thumbnail">
				<?php 
				if( $img_url ){
				?>
					<img src="<?php echo esc_url($img_url); ?>">
				<?php
				}
				else{
					echo wp_get_attachment_image($img_id, 'full', 0, array('class'=>''));
				}
				?>
			</a>
		
		</div>

		<div class="box-header">
		
			<?php if( strlen($title) > 0 ): ?>
			<h2 class="feature-title heading-title">
				<?php echo esc_html($title); ?>
			</h2>
			<?php endif; ?>
			
			<?php if( strlen($description) > 0 ): ?>
			<div class="box-description">
				<?php echo esc_attr($description) ?>
			</div>
			<?php endif; ?>
		
			<?php if( strlen($button_text) > 0): ?>
			<a target="<?php echo esc_attr($target); ?>" href="<?php echo ($link)?esc_url($link):'javascript: void(0)' ?>" class="button see-more"><?php echo esc_html($button_text); ?></a>
			<?php endif; ?>
		
		</div>
		
		<?php endif; ?>
		
	</div>
	<?php
	
	return ob_get_clean();
}
add_shortcode('ts_image_box', 'ts_image_box_shortcode');

/*** Shortcode Feature ***/
function ts_feature_shortcode( $atts ){
	extract(shortcode_atts(array(
						'style'						=> 'vertical-icon'
						,'icon_type' 				=> 'fontawesome'
						,'icon_color' 				=> '#666666'
						,'icon_fontawesome' 		=> 'fa fa-laptop'
						,'icon_openiconic'			=> 'vc-oi vc-oi-dial'
						,'icon_typicons'			=> 'typcn typcn-adjust-brightness'
						,'icon_entypo'				=> 'entypo-icon entypo-icon-note'
						,'icon_linecons'			=> 'vc_li vc_li-heart'
						,'icon_material' 			=> 'vc-material vc-material-cake'
						,'icon_linear' 				=> 'lnr lnr-heart'
						,'title' 					=> ''
						,'img_id'					=> ''
						,'img_url'					=> ''
						,'excerpt' 					=> ''
						,'link' 					=> ''		
						,'target' 					=> '_blank'
						,'text_style'				=> 'text-default'
						,'extra_class'				=> ''
					), $atts ));
	
	ob_start();
	
	$icon = $icon_fontawesome;
	if( $icon_type == 'openiconic' && function_exists('vc_icon_element_fonts_enqueue') ){
		$icon = $icon_openiconic;
		vc_icon_element_fonts_enqueue( 'openiconic' );
	}
	elseif( $icon_type == 'typicons' && function_exists('vc_icon_element_fonts_enqueue') ){
		$icon = $icon_typicons;
		vc_icon_element_fonts_enqueue( 'typicons' );
	}
	elseif( $icon_type == 'entypo' && function_exists('vc_icon_element_fonts_enqueue') ){
		$icon = $icon_entypo;
		vc_icon_element_fonts_enqueue( 'entypo' );
	}
	elseif( $icon_type == 'linecons' && function_exists('vc_icon_element_fonts_enqueue') ){
		$icon = $icon_linecons;
		vc_icon_element_fonts_enqueue( 'linecons' );
	}
	elseif( $icon_type == 'material' && function_exists('vc_icon_element_fonts_enqueue') ){
		$icon = $icon_material;
		vc_icon_element_fonts_enqueue( 'material' );
	}
	elseif( $icon_type == 'linear' ){
		$icon = $icon_linear;
	}
	
	$classes = array();
	$classes[] = 'ts-feature-wrapper';
	$classes[] = $extra_class;
	$classes[] = $style;
	$classes[] = $text_style;
	?>
	<div class="<?php echo esc_attr(implode(' ', $classes)) ?>">
		
		<div class="feature-content">
			
			<?php if( $style == 'horizontal-icon' || $style == 'vertical-icon' ): ?>
			<a style="color: <?php echo esc_attr($icon_color); ?>" target="<?php echo esc_attr($target); ?>" class="feature-icon" href="<?php echo ($link != '')?esc_url($link):'javascript: void(0)' ?>">
				<i class="<?php echo esc_attr($icon); ?>"></i>
			</a>
			<?php else: ?>
			<a target="<?php echo esc_attr($target); ?>" class="feature-icon" href="<?php echo ($link != '')?esc_url($link):'javascript: void(0)' ?>">
				<?php 
				if( $img_url != '' ){
				?>
					<img src="<?php echo esc_url($img_url); ?>">
				<?php
				}
				else{
					echo wp_get_attachment_image($img_id, 'full', 0, array('class'=>''));
				}
				?>
			</a>
			<?php endif; ?>
			
			<div class="feature-header">
				
				<?php if( strlen($title) > 0 ): ?>
				<h4 class="feature-title heading-title entry-title">
					<a target="<?php echo esc_attr($target); ?>" href="<?php echo ($link != '')?esc_url($link):'javascript: void(0)' ?>"><?php echo esc_html($title); ?></a>
				</h4>
				<?php endif; ?>
			
				<?php if( strlen($excerpt) > 0 ): ?>
				<div class="feature-excerpt">
					<?php echo esc_html($excerpt); ?>
				</div>
				<?php endif; ?>
					
			</div>
		</div>
	</div>
	<?php
	
	return ob_get_clean();
}
add_shortcode('ts_feature', 'ts_feature_shortcode');

/*** Shortcode Price Table ***/
function ts_price_table_shortcode( $atts ){
	extract(shortcode_atts(array(
						'active_table' 					=> 0
						,'style'						=> 'style-1'
						,'color_scheme'					=> '#f1a671'
						,'title'						=> ''
						,'price' 						=> ''
						,'currency' 					=> ''
						,'during_price' 				=> ''
						,'description'					=> ''
						,'button_text'					=> ''
						,'link'							=> '#'
					), $atts ));
	
	static $ts_price_table_counter = 1;
	$unique_class = 'ts-price-table-' . $ts_price_table_counter;
	$selector = '.' . $unique_class;
	$ts_price_table_counter++;
	
	$inline_style = '<div class="ts-shortcode-custom-style hidden">';
	if( $style == 'style-1'){
		$inline_style .= $selector.':before{border-color:'.$color_scheme.';}';
		$inline_style .= $selector.':hover:before{border-color:'.$color_scheme.';}';
		$inline_style .= $selector.' .table-price,'.$selector. '.during-price{color:'.$color_scheme.';}';
		$inline_style .= $selector.' .button-price-table:hover{background:'.$color_scheme.';border-color:'.$color_scheme.';}';
	}
	if($style == 'style-2'){
		$inline_style .= $selector.' header{background:'.$color_scheme.';}';
		if($active_table){
			$inline_style .= $selector.'{border-color:'.$color_scheme.';}';
		}
	}
	if($style == 'style-3'){
		$inline_style .= $selector.' .group-price > span{background:'.$color_scheme.';}';
		$inline_style .= $selector.' .button-price-table:hover{background:'.$color_scheme.';border-color:'.$color_scheme.';}';
		$inline_style .= $selector.' .group-price > span:before,'.$selector.' .group-price > span:after,'.$selector.' .group-price:before,'.$selector.' .group-price:after{border-bottom-color:'.$color_scheme.';border-top-color:'.$color_scheme.';}';
	}
	$inline_style .= '</div>';
	ob_start();
	?>
	<div class="ts-price-table <?php echo esc_attr($unique_class) ?> <?php echo esc_attr($style); ?> <?php echo ($active_table)?'active-table':'' ?>">
		<?php echo trim( $inline_style ); ?>

		<header>
		
			<?php if( strlen($title) > 0 && $style == 'style-3' ): ?>
				<h3 class="table-title"><?php echo esc_html($title) ?></h3>
			<?php endif; ?>
			
			<?php if( $style == 'style-3' ){ echo '<div class="group-price"><span>'; }?>
				<span class="table-price"><span><?php echo esc_html($currency) ?></span><?php echo esc_html($price) ?></span>
				<?php if( $during_price ): ?>
				<span class="during-price"><?php echo esc_html($during_price) ?></span>
				<?php endif;?>
			<?php if( $style == 'style-3' ){ echo '</span></div>'; }?>
			
			<?php if( strlen($title) > 0 && $style != 'style-3' ): ?>
				<h3 class="table-title"><?php echo esc_html($title) ?></h3>
			<?php endif; ?>
			
			<?php if( $style == 'style-2' && $button_text ): ?>
			<a class="button button-price-table" href="<?php echo esc_url($link) ?>"><?php echo esc_html($button_text) ?></a>
			<?php endif; ?>
			
		</header>
		
		<?php if( $description ): ?>
		<div class="table-description">
			<?php echo strip_tags($description, '<ul></ul><li></li><b></b><strong></strong><i></i>'); ?>
		</div>
		<?php endif; ?>
		
		<?php if( $style != 'style-2' && $button_text ): ?>
		<div class="table-button">
			<a class="button button-price-table" href="<?php echo esc_url($link) ?>"><?php echo esc_html($button_text) ?></a>
		</div>
		<?php endif; ?>
		
	</div>
	<?php
	
	return ob_get_clean();
}
add_shortcode('ts_price_table', 'ts_price_table_shortcode');

/*** Shortcode Testimonial ***/
function ts_testimonial_shortcode($atts){
	extract(shortcode_atts(array(
						'style'					=> 'dots-horizontal'
						,'title'				=> ''
						,'categories'			=> ''
						,'per_page'				=> 4
						,'text_color_style'		=> 'text-default'
						,'ids'					=> ''
						,'show_avatar'			=> 0
						,'show_name'			=> 1
						,'show_byline'			=> 1
						,'show_rating'			=> 0
						,'excerpt_words'		=> 40
						,'extra_class'			=> ''
						,'is_slider'			=> 1
						,'show_nav'				=> 0
						,'show_dots'			=> 0
						,'auto_play'			=> 1
					), $atts ));
	
	if( !is_numeric($excerpt_words) ){
		$excerpt_words = 50;
	}
	
	$classes = array();
	$classes[] = $style;
	$classes[] = $text_color_style;
	$classes[] = $extra_class;
	if($is_slider){
		$classes[] = 'ts-slider';
		if( $show_dots ){
			$show_nav = 0;
			$classes[] = 'show-dots';
		}
		if( $show_nav ){
			$classes[] = 'show-nav nav-middle';
		}
	}
	
	$data_attr = array();
	if( $is_slider ){
		$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
		$data_attr[] = 'data-dots="'.esc_attr($show_dots).'"';
		$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
	}

	global $post, $ts_testimonials;
	
	$args = array(
			'post_type'				=> 'ts_testimonial'
			,'post_status'			=> 'publish'
			,'ignore_sticky_posts'	=> true
			,'posts_per_page' 		=> $per_page
			,'orderby' 				=> 'date'
			,'order' 				=> 'desc'
		);
		
	$categories = str_replace(' ', '', $categories);
	if( strlen($categories) > 0 ){
		$categories = explode(',', $categories);
	}
	
	if( is_array($categories) && count($categories) > 0 ){
		$field_name = is_numeric($categories[0])?'term_id':'slug';
		$args['tax_query'] = array(
								array(
									'taxonomy' => 'ts_testimonial_cat',
									'terms' => $categories,
									'field' => $field_name,
									'include_children' => false
								)
							);
	}
	
	if( strlen(trim($ids)) > 0 ){
		$ids = array_map('trim', explode(',', $ids));
		if( is_array($ids) && count($ids) > 0 ){
			$args['post__in'] = $ids;
			$args['orderby'] = 'post__in';
		}
	}
	
	$testimonials = new WP_Query($args);
	
	ob_start();
	if( $testimonials->have_posts() ){
		if( isset($testimonials->post_count) && $testimonials->post_count <= 1 ){
			$is_slider = false;
		}
		?>
		<div class="ts-testimonial-wrapper ts-shortcode <?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr); ?>>
	
			<div class="items <?php echo ($is_slider)?'loading':'' ?>">
			<?php
			while( $testimonials->have_posts() ){
				$testimonials->the_post();
				if( function_exists('drile_the_excerpt_max_words') ){
					$content = drile_the_excerpt_max_words($excerpt_words, $post, true, '', false);
				}
				else{
					$content = substr(wp_strip_all_tags($post->post_content), 0, 300);
				}
				$byline = get_post_meta($post->ID, 'ts_byline', true);
				$url = get_post_meta($post->ID, 'ts_url', true);
				if( $url == '' ){
					$url = '#';
				}
				$rating = get_post_meta($post->ID, 'ts_rating', true);
				$rating_percent = '0';
				if( $rating != '-1' && $rating != '' ){
					$rating_percent = $rating * 100 / 5;
				}
				
				$show_item_avatar = $show_avatar;
				if( $show_item_avatar ){
					$gravatar_email = get_post_meta($post->ID, 'ts_gravatar_email', true);
					if( !has_post_thumbnail() && ($gravatar_email == '' || !is_email($gravatar_email)) ){
						$show_item_avatar = false;
					}
				}
				?>
				<div class="item">
					<blockquote class="<?php echo ($style == 'dots-horizontal')?'style-2':'' ?>">

						<div class="content">
							<?php echo esc_html($content); ?>
						</div>
						
						<div class="author-role">
						
							<?php if( $show_item_avatar ): ?>
							<div class="image">
								<?php echo $ts_testimonials->get_image($post->ID); ?>
							</div>
							<?php endif; ?>
							
							<?php if( $show_name ): ?>
							<span class="author">
								<a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo get_the_title($post->ID); ?></a>
							</span>
							<?php endif; ?>
							
							<?php if( $show_byline ): ?>
							<span class="role"><?php echo esc_html($byline); ?></span>
							<?php endif; ?>
							
							<?php if( $show_rating && $rating != '-1' && $rating != ''): ?>
							<div class="rating" title="<?php printf(esc_html__('Rated %s out of 5', 'themesky'), $rating); ?>">
								<span style="width: <?php echo $rating_percent.'%'; ?>"><?php printf(esc_html__('Rated %s out of 5', 'themesky'), $rating); ?></span>
							</div>
							<?php endif; ?>
						</div>
					
					</blockquote>
				</div>
				<?php
			}
			?>
			</div>
		</div>
		<?php
	}
	
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode('ts_testimonial', 'ts_testimonial_shortcode');

/*** Shortcode Portfolio ***/
if( !function_exists('ts_portfolio_shortcode') ){
	function ts_portfolio_shortcode( $atts ){
		extract(shortcode_atts(array(
							'title'				=> ''
							,'columns'			=> 2
							,'per_page'			=> 8
							,'categories'		=> ''
							,'orderby'			=> 'none'
							,'order'			=> 'DESC'
							,'style'			=> ''
							,'show_filter_bar'	=> 1
							,'show_load_more'	=> 1
							,'load_more_text'	=> 'LOAD MORE'
							,'show_title'		=> 1
							,'show_categories'	=> 1
							,'show_like_icon'	=> 1
							,'show_link_icon'	=> 1
							,'is_slider'		=> 0
							,'show_nav'			=> 1
							,'nav_position'		=> 'nav-middle'
							,'show_dots'		=> 0
							,'auto_play'		=> 1
							,'include'			=> '' // Used for related portfolio
						), $atts ));
						
		if( $is_slider ){
			$show_filter_bar = 0;
			$show_load_more = 0;
		}
		else{
			wp_enqueue_script( 'isotope' );
		}
		
		$args = array(
			'post_type'				=> 'ts_portfolio'
			,'posts_per_page'		=> $per_page
			,'post_status'			=> 'publish'
			,'ignore_sticky_posts'	=> 1
			,'orderby'				=> $orderby
			,'order'				=> $order
		);	
		
		if( $include ){
			$args['post__in'] = array_map('trim', explode(',', $include));
		}
		
		$categories = str_replace(' ', '', $categories);
		if( strlen($categories) > 0 ){
			$ar_categories = explode(',', $categories);
			if( is_array($ar_categories) && count($ar_categories) > 0 ){
				$field_name = is_numeric($ar_categories[0])?'term_id':'slug';
				$args['tax_query']	= array(
							array(
								'taxonomy'	=> 'ts_portfolio_cat'
								,'field'	=> $field_name
								,'terms'	=> $ar_categories
							)
						);
			}
		}
		ob_start();
		global $post, $wp_query, $ts_portfolios;
		$margin = 0;
		$classes = array();
		$classes[] = 'ts-portfolio-wrapper ts-shortcode loading';
		$classes[] = $style;
		if( $is_slider ){
			$classes[] = 'ts-slider';
		}
		else{
			$classes[] = 'ts-masonry columns-' . $columns;
		}
		
		$classes[] = $nav_position;
		
		$posts = new WP_Query( $args );
		if( $posts->have_posts() ){
			if( $posts->max_num_pages == 1 ){
				$show_load_more = 0;
			}
			
			$atts = compact('columns', 'per_page', 'categories', 'orderby', 'order', 'show_filter_bar', 'show_title','show_categories', 'show_like_icon', 'show_link_icon', 'margin', 'is_slider', 'show_nav', 'auto_play');
			?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>">
			
			<?php if( strlen($title) > 0 && $is_slider ): ?>
			<header class="shortcode-heading-wrapper">
				<h2 class="shortcode-title">
					<?php echo esc_html($title); ?>
				</h2>
			</header>
			<?php endif; ?>
			
			<?php
			/* Get filter bar */
			if( $show_filter_bar ){
				$terms = array();
				foreach( $posts->posts as $p ){
					$post_terms = wp_get_post_terms($p->ID, 'ts_portfolio_cat');
					if( is_array($post_terms) ){
						foreach( $post_terms as $term ){
							$terms[$term->slug] = $term->name;
						}
					}
				}
				
				if( !empty($terms) ){
					?>
					<ul class="filter-bar">
						<li data-filter="*" class="current"><?php esc_html_e('All', 'themesky'); ?></li>
						<?php
						foreach( $terms as $slug => $name ){
						?>
						<li data-filter="<?php echo '.'.$slug; ?>"><?php echo esc_attr($name) ?></li>
						<?php
						}
						?>
					</ul>
					<?php
				}
			}
			?>
				<div class="portfolio-inner items">
				<?php
					ts_get_portfolio_items_content_shortcode($atts, $posts);
				?>
				</div>
				
				<?php if( $show_load_more ){ ?>
				<div class="load-more-wrapper">
					<a href="#" class="load-more" data-total_pages="<?php echo $posts->max_num_pages; ?>" data-paged="2"><?php echo esc_html($load_more_text) ?></a>
				</div>
				<?php } ?>
			</div>
			
			<?php
		}
		
		wp_reset_postdata();
		return ob_get_clean();
	}
}
add_shortcode('ts_portfolio', 'ts_portfolio_shortcode');

add_action('wp_ajax_ts_portfolio_load_items', 'ts_get_portfolio_items_content_shortcode');
add_action('wp_ajax_nopriv_ts_portfolio_load_items', 'ts_get_portfolio_items_content_shortcode');
if( !function_exists('ts_get_portfolio_items_content_shortcode') ){
	function ts_get_portfolio_items_content_shortcode($atts, $posts = null){
		
		global $post, $ts_portfolios;
		
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			if( !isset($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$paged = isset($_POST['paged'])?absint($_POST['paged']):1;
			
			extract($atts);
			
			$args = array(
				'post_type'				=> 'ts_portfolio'
				,'posts_per_page'		=> $per_page
				,'post_status'			=> 'publish'
				,'ignore_sticky_posts'	=> 1
				,'paged' 				=> $paged
				,'orderby'				=> $orderby
				,'order'				=> $order
			);	
			$categories = str_replace(' ', '', $categories);
			if( strlen($categories) > 0 ){
				$categories = explode(',', $categories);
				if( is_array($categories) ){
					$field_name = is_numeric($categories[0])?'term_id':'slug';
					$args['tax_query']	= array(
								array(
									'taxonomy'	=> 'ts_portfolio_cat'
									,'field'	=> $field_name
									,'terms'	=> $categories
								)
							);
				}
			}
			$posts = new WP_Query( $args );
			ob_start();
		}
		
		extract($atts);
		
		if( $posts->have_posts() ):
			while( $posts->have_posts() ): $posts->the_post();
				$classes = '';
				$post_terms = wp_get_post_terms($post->ID, 'ts_portfolio_cat');
				if( is_array($post_terms) ){
					foreach( $post_terms as $term ){
						$classes .= $term->slug . ' ';
					}
				}
				
				$link = esc_url(get_post_meta($post->ID, 'ts_portfolio_url', true));
				if( $link == '' ){
					$link = get_permalink();
				}
				
				/* Get Like */
				$like_num = 0;
				$user_already_like = false;
				if( is_a($ts_portfolios, 'TS_Portfolios') ){
					$like_num = $ts_portfolios->get_like( $post->ID );
					$user_already_like = $ts_portfolios->user_already_like( $post->ID );
				}
				?>
				<div class="item <?php echo esc_attr($classes) ?>">
					<div class="item-wrapper">
						<div class="portfolio-thumbnail">
							<figure>								
								<?php 
								if( has_post_thumbnail() ){
									the_post_thumbnail('ts_portfolio_thumb');
								}
								?>								
								<figcaption>
									<div class="portfolio-meta">
									
										<?php if( $show_title ): ?>
											<h4 class="heading-title semibold"><a href="<?php echo esc_url($link); ?>"><?php echo get_the_title(); ?></a></h4>
										<?php endif; ?>
										
										<div class="portfolio-meta-bottom">
											<?php $categories_list = get_the_term_list($post->ID, 'ts_portfolio_cat', '', ', ', ''); ?>
											<?php if ( $show_categories && $categories_list ): ?>
												<div class="cats-portfolio">
													<?php esc_html_e('in ', 'themesky') ?><?php echo $categories_list; ?>
												</div>
											<?php endif; ?>
											
											<div class="icon-group">
												<?php if( $show_like_icon ){ ?>
													<a href="#" class="like <?php echo ($user_already_like)?'already-like':'' ?>" 
														data-post_id="<?php echo esc_attr($post->ID) ?>" title="<?php echo ($user_already_like)?esc_html__('You liked it', 'themesky'):esc_html__('Like it', 'themesky') ?>"
														data-liked-title="<?php esc_html_e('You liked it', 'themesky') ?>" data-like-title="<?php esc_html_e('Like it', 'themesky') ?>">
													</a>
												<?php } ?>
											</div>
										
										</div>
									</div>
									<a href="<?php echo esc_url($link); ?>"></a>
								</figcaption>
							</figure>
						</div>
					</div>
				</div>
			<?php
			endwhile;
		endif;
		
		wp_reset_postdata();
		
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			die(ob_get_clean());
		}
		
	}
}

/*** Shortcode Banner ***/
function ts_banner_shortcode( $atts ){
	extract(shortcode_atts(array(
						'banner_style'							=> 'style-default'
						,'bg_id'								=> ''
						,'bg_url'								=> ''
						,'bg_color'								=> '#ffffff'
						,'heading_title'						=> ''
						,'heading_text_color'					=> '#202020'
						,'heading_text_hover_color'				=> '#ffffff'
						,'sub_heading_title'					=> ''
						,'sub_heading_text_color'				=> '#202020'
						,'description'							=> ''
						,'description_text_color'				=> '#848484'
						,'content_background'					=> ''
						,'use_theme_fonts'						=> 0
						,'google_fonts'							=> 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal'
						,'sub_heading_title_font_size'			=> ''
						,'sub_heading_title_font_size_device' 	=> ''
						,'discount'								=> ''
						,'discount_color'						=> '#ed9fa6'
						,'show_button'							=> 0
						,'button_text'							=> 'Shop Now'
						,'content_position'						=> 'left-top'
						,'link' 								=> ''
						,'link_title' 							=> ''
						,'target' 								=> '_blank'
						,'style_effect'							=> 'eff-image-opacity'
						,'effect_color'							=> '#ffffff'
						,'extra_class'							=> ''
						,'enable_mobile'						=> 1
						,'custom_bg_color'						=> 0
						,'mobile_bg_id'							=> ''
						,'mobile_bg_url'						=> ''
						,'mobile_bg_color'						=> '#ffffff'
					), $atts ));

	static $ts_banner_counter = 1;
	$unique_class = 'ts-banner-'.$ts_banner_counter;
	$classes = array();
	$selector = '.' . $unique_class;
	$ts_banner_counter++;
	
	$classes[] = $banner_style;
	$classes[] = $style_effect;
	$classes[] = $content_position;
	$classes[] = $unique_class;
	$classes[] = $extra_class;
	
	if($content_background && $content_background != ''){
		$classes[] = 'content-has-background';
	}
	
	if( $bg_id || $bg_url ){
		$classes[] = 'has-background-image';
	}
	
	if( $discount ){
		$classes[] = 'has-discount';
	}
	
	$style = '<div class="ts-shortcode-custom-style hidden">';
	
	$style .= $selector.':hover .overlay{background-color:'. $effect_color .'}';

	if( $heading_text_color ){
		$style .= $selector.' .banner-wrapper h2{color:'. $heading_text_color .'}';
	}
	
	if( $banner_style != 'style-category' && $sub_heading_text_color ){
		$style .= $selector.' .banner-wrapper h6{color:'. $sub_heading_text_color .'}';
	}
	
	if( $description_text_color ){
		$style .= $selector.' .banner-wrapper .description{color:'. $description_text_color .'}';
	}
	
	if( $content_background ){
		$style .= $selector.' .banner-wrapper .box-content header{background-color:'. $content_background .'}';
	}
	
	if( $banner_style == 'style-text-center' && $discount_color ){
		$style .= $selector.' .banner-wrapper .discount{color:'. $discount_color .'}';
	}
	
	if( $banner_style == 'style-category' && $heading_text_hover_color ){
		$style .= $selector.':hover .banner-wrapper h2, '.$selector.':hover .banner-wrapper .banner-bg:after{color:'. $heading_text_hover_color .'}';
	}
	
	if( $bg_color ){
		$style .= $selector.' .banner-wrapper{background-color:'. $bg_color .'}';
	}
	
	if( $style_effect == 'eff-image-shadow' ){
		$style .= $selector.':hover .bg-content:before{box-shadow: inset 0 0 0 15px '. $effect_color .'}';
	}
	
	if( $sub_heading_title_font_size ){
		$style .= $selector.' .box-content h6{font-size:'. $sub_heading_title_font_size .'px}';
	}
	
	if( $sub_heading_title_font_size_device ){
		$style .= '@media screen and (max-width: 1279px){';
		$style .= $selector.' .box-content h6{font-size:'. $sub_heading_title_font_size_device .'px}';
		$style .= '}';
	}
	
	// Google fonts
	if( $use_theme_fonts ){
		
		$google_fonts_data = array();
		
		if( function_exists('vc_parse_multi_attribute') ){
			$google_fonts_data = vc_parse_multi_attribute( $google_fonts);
		}
		
		$settings = get_option( 'wpb_js_google_fonts_subsets' );
		if ( is_array( $settings ) && ! empty( $settings ) ) {
			$subsets = '&subset=' . implode( ',', $settings );
		} else {
			$subsets = '';
		}
		
		if ( isset( $google_fonts_data['font_family'] ) ) {
			if( function_exists('vc_build_safe_css_class') ){
				wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['font_family'] ), 'https://fonts.googleapis.com/css?family=' . $google_fonts_data['font_family'] . $subsets, [], WPB_VC_VERSION );
			}
			if( isset( $google_fonts_data['font_style'] ) ){
				$google_fonts_family = explode( ':', $google_fonts_data['font_family'] );
				$google_fonts_styles = explode( ':', $google_fonts_data['font_style'] );
				
				$style .= $selector.' .box-content header h6{font-family:'.$google_fonts_family[0].';font-weight:'.$google_fonts_styles[1].';font-style:'.$google_fonts_styles[2].'}';
			}
		}
	}
	
	if( $enable_mobile && $banner_style != 'style-category' ){
		$classes[] = 'enable-mobile-version';
		if ( $custom_bg_color && $mobile_bg_color ){
			$style .= '@media screen and (max-width: 767px){';
			$style .= $selector.' .banner-wrapper{background-color:'. $mobile_bg_color .'}';
			$style .= '}';
		}
	}

	$style .= '</div>';
	
	ob_start();
	
	
	?>
	<div class="ts-banner ts-effect-image <?php echo esc_attr( implode(' ', $classes) ); ?>">
		<?php echo trim($style); ?>
		
		<div class="banner-wrapper">
		
			<?php if( $link && !$show_button ): ?>
			<a title="<?php echo esc_attr($link_title) ?>" target="<?php echo esc_attr($target); ?>" class="banner-link" href="<?php echo esc_url($link) ?>" ></a>
			<?php endif;?>
				
			<div class="banner-bg <?php echo ($enable_mobile && ($mobile_bg_id || $mobile_bg_url))?'hidden-phone':'' ?>">
				<div class="bg-content">				
				<?php if( $bg_url ): ?>
					<img alt="<?php echo esc_attr($link_title) ?>" title="<?php echo esc_attr($link_title) ?>" class="img" src="<?php echo esc_url($bg_url); ?>">
				<?php else:
					echo wp_get_attachment_image($bg_id, 'full', 0, array('class'=>'img'));
				endif; ?>			
				</div>
				
				<span class="overlay"></span>
			</div>
						
			<div class="box-content">
				<header>
					<?php if( ( $banner_style == 'style-text-center' ) && $discount ): ?>				
						<div class="discount"><?php echo esc_html($discount) ?></div>
					<?php endif; ?>
					
					<?php if( $sub_heading_title ): ?>				
						<h6><?php echo esc_html($sub_heading_title) ?></h6>
					<?php endif; ?>
					
					<?php if( $heading_title ): ?>
						<h2 class="h1"><?php echo wp_kses($heading_title, array('br' => array())) ?></h2>
					<?php endif; ?>
					
					<?php if( $description ): ?>
						<div class="description"><?php echo wp_kses($description, array('br' => array())) ?></div>
					<?php endif; ?>
								
					<?php if( $show_button ):?>
						<div class="ts-banner-button">
							<a title="<?php echo esc_attr($link_title) ?>" target="<?php echo esc_attr($target); ?>" href="<?php echo esc_url($link) ?>"><?php echo esc_html($button_text) ?></a>
						</div>
					<?php endif; ?>
				</header>
			</div>
			
			<?php if( $enable_mobile && ($mobile_bg_id || $mobile_bg_url) ): ?>
				<div class="banner-bg mobile-version visible-phone">
					<div class="bg-content">
					
					<?php if( $mobile_bg_url ): ?>
						<img alt="<?php echo esc_attr($link_title) ?>" title="<?php echo esc_attr($link_title) ?>" class="img" src="<?php echo esc_url($mobile_bg_url); ?>">
					<?php else:
						echo wp_get_attachment_image($mobile_bg_id, 'full', 0, array('class'=>'img'));
					endif; ?>
					
					</div>
				</div>
			<?php endif; ?>
			
		</div>
	</div>
	<?php
	
	return ob_get_clean();
}
add_shortcode('ts_banner', 'ts_banner_shortcode');

/*** Shortcode Single Image ***/
if( !function_exists('ts_single_image_shortcode') ){
	function ts_single_image_shortcode( $atts ){
		extract(shortcode_atts(array(
							'img_id'			=> ''
							,'img_url'			=> ''
							,'img_size'			=> ''
							,'style_effect'		=> 'eff-image-opacity'
							,'effect_color'		=> '#ffffff'
							,'link' 			=> ''
							,'link_title' 		=> ''						
							,'alignment' 		=> ''						
							,'target' 			=> '_blank'
							,'extra_class' 		=> ''
						), $atts ));
						
		if( $img_size == '' ){
			$img_size = 'full';
		}
		
		
		
		static $ts_image_counter = 1;
		$unique_class = 'ts-single-image-'.$ts_image_counter;
		$classes = array();
		$selector = '.' . $unique_class;
		$ts_image_counter++;
		
		$classes[] = $alignment;
		$classes[] = $style_effect;
		$classes[] = $unique_class;
		$classes[] = $extra_class;
		
		$style = '<div class="ts-shortcode-custom-style hidden">';
		if( $effect_color ){
			$style .= $selector.':hover .overlay{background-color:'. $effect_color .'}';
		}
		if( $style_effect == 'eff-image-shadow' ){
			$style .= $selector.':hover .image-link:before{box-shadow: inset 0 0 0 15px '. $effect_color .'}';
		}
		$style .= '</div>';
		
		ob_start();
		?>
		<div class="ts-single-image ts-effect-image <?php echo esc_attr( implode(' ', $classes) ); ?>">
			<?php echo trim($style); ?>
			<a title="<?php echo esc_attr($link_title) ?>" target="<?php echo esc_attr($target); ?>" class="image-link" href="<?php echo esc_url($link) ?>" >
				<?php 
				if( $img_url != '' ){
				?>
					<img alt="<?php echo esc_attr($link_title) ?>" title="<?php echo esc_attr($link_title) ?>" class="img" src="<?php echo esc_url($img_url); ?>" loading="lazy" />
				<?php
				}
				else{
					echo wp_get_attachment_image($img_id, $img_size, 0, array('class'=>'img'));
				}
				?>
				<span class="overlay"></span>
			</a>
		</div>
		<?php
		
		return ob_get_clean();
	}
}
add_shortcode('ts_single_image', 'ts_single_image_shortcode');

/*** Shortcode Banner Image ***/
if( !function_exists('ts_banner_image_shortcode') ){
	function ts_banner_image_shortcode( $atts ){
		extract(shortcode_atts(array(
							'img_bg_id'				=> ''
							,'img_bg_url'			=> ''
							,'img_text_id'			=> ''
							,'img_text_url'			=> ''
							,'img_size'				=> ''
							,'style_effect'			=> 'eff-image-opacity'
							,'img_text_position'	=> 'left-top'
							,'effect_color'			=> '#ffffff'
							,'link' 				=> ''
							,'link_title' 			=> ''						
							,'target' 				=> '_blank'
							,'fix_width'			=> 0
							,'image_radius'			=> 0
							,'extra_class'			=> ''
						), $atts ));
						
		if( $img_size == '' ){
			$img_size = 'full';
		}
		
		static $ts_banner_image_counter = 1;
		$unique_class = 'ts-banner-image-'.$ts_banner_image_counter;
		$selector = '.' . $unique_class;
		$ts_banner_image_counter++;
		
		$classes = array();
		$classes[] = $image_radius?'image-radius':'';
		$classes[] = $style_effect;
		$classes[] = $unique_class;
		$classes[] = $img_text_position;
		$classes[] = $fix_width?'fix-width':'';
		$classes[] = $extra_class;
		
		$style = '<div class="ts-shortcode-custom-style hidden">';
		if( $effect_color ){
			$style .= $selector.':hover .overlay{background-color:'. $effect_color .'}';
		}
		if( $style_effect == 'eff-image-shadow' ){
			$style .= $selector.':hover .image-link:before{box-shadow: inset 0 0 0 15px '. $effect_color .'}';
		}
		$style .= '</div>';
		
		ob_start();
		?>
		<div class="ts-banner-image ts-effect-image <?php echo esc_attr( implode(' ', $classes) ); ?>">
			<?php echo trim($style); ?>
			<a title="<?php echo esc_attr($link_title) ?>" target="<?php echo esc_attr($target); ?>" class="image-link" href="<?php echo esc_url($link) ?>" >
				<?php 
				if( $img_bg_url != '' ){
				?>
					<img alt="<?php echo esc_attr($link_title) ?>" title="<?php echo esc_attr($link_title) ?>" class="img bg-image" src="<?php echo esc_url($img_bg_url); ?>">
				<?php
				}
				else{
					echo wp_get_attachment_image($img_bg_id, $img_size, 0, array('class'=>'img bg-image'));
				}
				if( $img_text_url != '' ){
				?>
					<img alt="<?php echo esc_attr($link_title) ?>" title="<?php echo esc_attr($link_title) ?>" class="img text-image" src="<?php echo esc_url($img_text_url); ?>">
				<?php
				}
				else{
					echo wp_get_attachment_image($img_text_id, $img_size, 0, array('class'=>'img text-image'));
				}
				?>
				<span class="overlay"></span>
			</a>
		</div>
		<?php
		
		return ob_get_clean();
	}
}
add_shortcode('ts_banner_image', 'ts_banner_image_shortcode');


/*** Shortcode Logo ***/
if( !function_exists('ts_logos_slider_shortcode') ){
	function ts_logos_slider_shortcode( $atts, $content = null ){
		extract(shortcode_atts(array(
					'categories' 		=> ''
					,'style_nav'		=> 'text-default'
					,'per_page' 		=> 7
					,'rows' 			=> 1
					,'active_link'		=> 1
					,'show_nav' 		=> 1
					,'auto_play' 		=> 1
					,'margin'			=> 0
					), $atts));
		if( !class_exists('TS_Logos') ){
			return;
		}
		
		$args = array(
			'post_type'				=> 'ts_logo'
			,'post_status'			=> 'publish'
			,'ignore_sticky_posts'	=> 1
			,'posts_per_page' 		=> $per_page
			,'orderby' 				=> 'date'
			,'order' 				=> 'desc'
		);
		
		$categories = str_replace(' ', '', $categories);
		if( strlen($categories) > 0 ){
			$categories = explode(',', $categories);
		}
		if( is_array($categories) && count($categories) > 0 ){
			$field_name = is_numeric($categories[0])?'term_id':'slug';
			$args['tax_query'] = array(
									array(
										'taxonomy' => 'ts_logo_cat'
										,'terms' => $categories
										,'field' => $field_name
										,'include_children' => false
									)
								);
		}
		
		$logos = new WP_Query($args);
		
		global $post;
		ob_start();
		if( $logos->have_posts() ):
			$count_posts = $logos->post_count;
			
			$classes = array();
			$classes[] = 'ts-logo-slider-wrapper ts-slider ts-shortcode';
			$classes[] = $style_nav;
			
			if( $count_posts > 1 && $count_posts > $rows ){
				$classes[] = 'loading';
			}
			if( $show_nav ){
				$classes[] = 'show-nav nav-middle';
			}
			
			$settings_option = get_option('ts_logo_setting', array());
			$data_break_point = isset($settings_option['responsive']['break_point'])?$settings_option['responsive']['break_point']:array();
			$data_item = isset($settings_option['responsive']['item'])?$settings_option['responsive']['item']:array();
			
			$data_attr = array();
			$data_attr[] = 'data-margin="'.absint($margin).'"';
			$data_attr[] = 'data-nav="'.$show_nav.'"';
			$data_attr[] = 'data-auto_play="'.$auto_play.'"';
			$data_attr[] = 'data-rows="'.absint($rows).'"';
			$data_attr[] = 'data-break_point="'.htmlentities(json_encode( $data_break_point )).'"';
			$data_attr[] = 'data-item="'.htmlentities(json_encode( $data_item )).'"';
			?>
			<div class="<?php echo esc_attr( implode(' ', $classes) ); ?>" <?php echo implode(' ', $data_attr); ?>>
				<div class="content-wrapper">
					<div class="items">
					<?php 
					$count = 0;
					while( $logos->have_posts() ): $logos->the_post(); 
						if( $rows > 1 && $count % $rows == 0 ){
							echo '<div class="logo-group">';
						}
					?>
						<div class="item">
							<?php if( $active_link ):
							$logo_url = get_post_meta($post->ID, 'ts_logo_url', true);
							$logo_target = get_post_meta($post->ID, 'ts_logo_target', true);
							?>
								<a href="<?php echo esc_url($logo_url); ?>" target="<?php echo esc_attr($logo_target); ?>">
							<?php endif; ?>
								<?php 
								if( has_post_thumbnail() ){
									the_post_thumbnail('ts_logo_thumb');
								}
								?>
							<?php if( $active_link ): ?>
								</a>
							<?php endif; ?>
						</div>
					<?php 
						if( $rows > 1 && ($count % $rows == $rows - 1 || $count == $count_posts - 1) ){
							echo '</div>';
						}
						$count++;
					endwhile; 
					?>
					</div>
				</div>
			</div>
		<?php
		endif;
		wp_reset_postdata();
		return ob_get_clean();
	}
}
add_shortcode('ts_logos_slider', 'ts_logos_slider_shortcode');

/************************************
*** Element Shortcodes
*************************************/

/*** Shortcode Button ***/
function ts_button_shortcode($atts, $content=null){
	extract(shortcode_atts(array(	
					'link'					=> '#'
					,'text_color'			=> '#202020'
					,'text_color_hover'		=> '#ffffff'
					,'bg_color'				=> '#ffffff'
					,'bg_color_hover'		=> '#202020'
					,'border_color'			=> '#202020'
					,'border_color_hover'	=> '#202020'
					,'border_width'			=> '1'
					,'border_radius'		=> '0'
					,'add_icon'				=> 0
					,'icon_type'			=> 'fontawesome'
					,'font_icon'			=> ''
					,'icon_linear'			=> 'lnr lnr-arrow-right'
					,'icon_alignment'		=> 'icon-left'
					,'target'				=> '_self' /* _self, _blank */
					,'size'					=> 'medium' /* small, medium, large, x-large */
					,'alignment'			=> 'btn-inline'
					,'extra_class'			=> ''
					), $atts));
	static $ts_button_counter = 1;		
	$style_css = '';
	$style_hover_css = '';
	$classes = array();
	$classes[] = $alignment;
	$classes[] = $extra_class;
	
	if( $border_width ){
		$classes[] = 'has-border';
	}
	$selector = '.ts-button-wrapper a.ts-button-'.$ts_button_counter;
	
	if( $bg_color ){
		$style_css .= 'background:'.$bg_color.';';
	}else{
		$style_css .= 'background:transparent;';
	}
	if( $border_color ){
		$style_css .= 'border-color:'.$border_color.';';
	}
	if( $border_width != '' ){
		$style_css .= 'border-width:'.absint($border_width).'px ;';
	}
	if( $border_radius != '' ){
		$style_css .= 'border-radius:'.absint($border_radius).'px ;';
	}
	if( $text_color ){
		$style_css .= 'color:'.$text_color.';';
	}
		
	if( $bg_color_hover ){
		$style_hover_css .= 'background:'.$bg_color_hover.';';
	}else{
		$style_hover_css .= 'background:transparent;';
	}
	if( $border_color_hover ){
		$style_hover_css .= 'border-color:'.$border_color_hover.';';
	}
	if( $text_color_hover ){
		$style_hover_css .= 'color:'.$text_color_hover.';';
	}
	
	$html = '<div class="ts-button-wrapper '.implode(' ', $classes).'">';
	$html .= '<div class="ts-shortcode-custom-style hidden">';
	$html .= $selector.'{';
	$html .= $style_css;
	$html .= '}';
	
	$html .= $selector.':hover{';
	$html .= $style_hover_css;
	$html .= '}';
	$html .= '</div>';
	
	$icon = '';
	if ( $add_icon ){
		if ( $icon_type == 'fontawesome' ){
			$icon = ($font_icon)?'<i class="'.$font_icon.'"></i>':'';
		}else{
			$icon = ($icon_linear)?'<i class="'.$icon_linear.'"></i>':'';
		}
	}
	
	if( $icon_alignment == 'icon-left' ){
		$html .= '<a href="'.esc_url($link).'" target="'.$target.'" class="ts-button ts-button-'.$ts_button_counter.' '.$size.'">'. $icon .'<span>'. do_shortcode($content) .'</span></a>';
	}else{
		$html .= '<a href="'.esc_url($link).'" target="'.$target.'" class="ts-button ts-button-'.$ts_button_counter.' '.$size.'"><span>'. do_shortcode($content) .'</span>'. $icon .'</a>';
	}
	
	$html .= '</div>';
	
	$ts_button_counter++;
	return $html;
}
add_shortcode('ts_button', 'ts_button_shortcode');

/*** Shortcode MailChimp ***/
if( !function_exists('ts_mailchimp_subscription_shortcode') ){
	function ts_mailchimp_subscription_shortcode( $atts ){
		extract(shortcode_atts(array(	
					'style'				=> ''
					,'title'			=> ''
					,'intro_text'		=> ''
					,'form'				=> ''
					,'text_style'		=> 'text-default'
					,'extra_class'		=> ''
					), $atts));
					
		if( !class_exists('TS_Mailchimp_Subscription_Widget') ){
			return;
		}
		
		$intro_html = '';
		if( $intro_text ){
			$intro_html = '<div class="newsletter"><p>'.esc_html($intro_text).'</p></div>';
			$intro_text = '';
		}
		
		$args = array(
			'before_widget' => '<section class="widget-container %s">'
			,'after_widget' => '</section>'
			,'before_title' => '<div class="widget-title-wrapper"><h3 class="widget-title heading-title">'
			,'after_title'  => '</h3>'.$intro_html.'</div>'
		);
		
		$instance = compact('title', 'intro_text', 'form');
		
		ob_start();
		
		$classes = array();
		$classes[] = $style;
		$classes[] = $text_style;
		$classes[] = $extra_class;
		
		echo '<div class="ts-mailchimp-subscription-shortcode '.implode(' ', $classes).'" >';
		
		the_widget('TS_Mailchimp_Subscription_Widget', $instance, $args);
		
		echo '</div>';
		
		return ob_get_clean();
	}
}
add_shortcode('ts_mailchimp_subscription', 'ts_mailchimp_subscription_shortcode');

/*** Shortcode Social ***/
if( !function_exists('ts_social_shortcode') ){
	function ts_social_shortcode( $atts ){
		extract(shortcode_atts(array(	
					'social_style'		=> 'style-icon'
					,'title' 			=> ''
					,'facebook_url' 	=> ''
					,'twitter_url' 		=> ''
					,'flickr_url' 		=> ''
					,'vimeo_url' 		=> ''
					,'youtube_url' 		=> ''
					,'viber_number' 	=> ''
					,'skype_username' 	=> ''
					,'instagram_url' 	=> ''
					,'pinterest_url' 	=> ''
					,'custom_link' 		=> ''
					,'custom_text' 		=> ''
					,'custom_font' 		=> ''
					,'show_tooltip' 	=> 1					
					,'extra_class' 		=> ''					
					), $atts));
					
		if( !class_exists('TS_Social_Icons_Widget') ){
			return;
		}
		
		$args = array(
			'before_widget' => '<section class="widget-container '.$extra_class.' %s">'
			,'after_widget' => '</section>'
			,'before_title' => '<div class="widget-title-wrapper"><h3 class="widget-title heading-title">'
			,'after_title'  => '</h3></div>'
		);
		
		$instance = compact('title', 'facebook_url', 'twitter_url', 'flickr_url', 'vimeo_url', 'youtube_url', 'viber_number', 'skype_username', 'instagram_url', 'pinterest_url', 'custom_link', 'custom_text', 'custom_font', 'show_tooltip', 'social_style');
		
		ob_start();	
		
		the_widget('TS_Social_Icons_Widget', $instance, $args);
			
		return ob_get_clean();
	}
}
add_shortcode('ts_social', 'ts_social_shortcode');

/*** Shortcode Dropcap ***/
function ts_dropcap_shortcode($atts, $content=null){
	extract(shortcode_atts(array(	
					'style'					=> '1'
					), $atts));
	return '<span class="ts-dropcap'.' style-'.$style.'">' .do_shortcode($content). '</span>';
}
add_shortcode('ts_dropcap', 'ts_dropcap_shortcode');

/*** Shortcode Quote ***/
function ts_quote_shortcode($atts, $content = null){
	extract(shortcode_atts(array(
			'style' 			=> 'default'
			,'author' 			=> ''
			,'role' 			=> ''
		), $atts));
	ob_start();
	?>
	<blockquote class="<?php echo esc_attr($style) ?>">
		&ldquo;<?php echo do_shortcode($content); ?>&rdquo;
		<?php if( $author || $role ): ?>
		<p class="author-role">
			<?php if( $author ): ?>
			<span class="author"><?php echo esc_html($author) ?></span>
			<?php endif; ?>
			
			<?php if( $role ): ?>
			<span class="role"><?php echo esc_html($role) ?></span>
			<?php endif; ?>
		</p>
		<?php endif; ?>
	</blockquote>
	<?php
	return ob_get_clean();
}
add_shortcode('ts_quote', 'ts_quote_shortcode');

/*** Shortcode Heading ***/
if( !function_exists('ts_heading_shortcode') ){
	function ts_heading_shortcode($atts, $content = null){
		extract(shortcode_atts(array(
			'alignment' 			=> 'heading-center'
			,'style' 				=> 'style-default'
			,'size' 				=> '1'
			,'text' 				=> ''
			,'text2' 				=> ''
			,'text_style'			=> 'text-default'
			,'extra_class' 			=> ''
		), $atts));
		ob_start();
		$classes = array();
		$classes[] = 'ts-heading';
		$classes[] = 'heading-' . $size;
		$classes[] = $style;
		$classes[] = $alignment;
		$classes[] = $text_style;
		$classes[] = $extra_class;
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
			<h<?php echo esc_attr($size) ?> class="heading"><?php echo do_shortcode($text); ?><?php echo ($text2 && $style == 'style-multiple-heading') ? '<span class="heading-2">'.do_shortcode($text2).'</span>' : ''; ?></h<?php echo esc_attr($size) ?>>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('ts_heading', 'ts_heading_shortcode');

/*** Shortcode Blog ***/
if( !function_exists('ts_blogs_shortcode') ){
	function ts_blogs_shortcode( $atts, $content = null){
		extract(shortcode_atts(array(
					'style'				=> ''
					,'title'			=> ''
					,'layout'			=> 'grid'
					,'item_layout'		=> 'grid' /* grid, list */
					,'columns'			=> 3
					,'categories'		=> ''
					,'per_page'			=> 5
					,'orderby'			=> 'none'
					,'order'			=> 'DESC'
					,'show_title'		=> 1
					,'show_thumbnail'	=> 1
					,'show_author'		=> 0
					,'show_categories'	=> 1
					,'show_date'		=> 1
					,'show_comment'		=> 1
					,'show_excerpt'		=> 1
					,'show_readmore'	=> 1
					,'excerpt_words'	=> 20
					,'show_nav'			=> 1
					,'auto_play'		=> 1
					,'margin'			=> 0
					,'show_load_more'	=> 0
					,'load_more_text'	=> 'LOAD MORE'
					,'extra_class'		=> ''
				), $atts));
		
		if( !is_numeric($excerpt_words) ){
			$excerpt_words = 20;
		}
		
		$is_slider = 0;
		$is_masonry = 0;
		if( $layout == 'slider' ){
			$is_slider = 1;
		}
		if( $layout == 'masonry' ){
			wp_enqueue_script( 'isotope' );
			$is_masonry = 1;
		}
		
		$columns = absint($columns);
		if( !in_array($columns, array(1, 2, 3, 4)) ){
			$columns = 2;
		}
		
		$args = array(
			'post_type' 			=> 'post'
			,'post_status' 			=> 'publish'
			,'ignore_sticky_posts' 	=> 1
			,'posts_per_page'		=> $per_page
			,'orderby'				=> $orderby
			,'order'				=> $order
			,'tax_query'			=> array()
		);
		
		$categories = str_replace(' ', '', $categories);
		if( strlen($categories) > 0 ){
			$ar_categories = explode(',', $categories);
			if( is_array($ar_categories) && count($ar_categories) > 0 ){
				$field_name = is_numeric($ar_categories[0])?'term_id':'slug';
				$args['tax_query'][] = array(
											'taxonomy' => 'category'
											,'terms' => $ar_categories
											,'field' => $field_name
											,'include_children' => false
										);
			}
		}
		
		if( $item_layout == 'background' ){ // only load the standard posts
			$args['tax_query'][] = array(
					'taxonomy'	=> 'post_format'
					,'field'	=> 'slug'
					,'terms'    => array( 'post-format-audio', 'post-format-gallery', 'post-format-quote', 'post-format-video' )
					,'operator'	=> 'NOT IN'
				);
				
			$show_excerpt = 0;
		}
		
		global $post;
		$posts = new WP_Query($args);
		
		ob_start();
		if( $posts->have_posts() ):
			if( $posts->post_count <= 1 ){
				$is_slider = 0;
			}
			if( $is_slider || $posts->max_num_pages == 1 ){
				$show_load_more = 0;
			}
			
			$classes = array();
			$classes[] = 'ts-blogs-wrapper ts-shortcode ts-blogs heading-center';
			if( $is_slider ){
				$classes[] = 'ts-slider loading';
				if( $show_nav ){
					$classes[] = 'show-nav';
					$classes[] = 'nav-middle';
				}
			}
			if( $is_masonry ){
				$classes[] = 'ts-masonry loading';
			}
			$classes[] = $item_layout;
			$classes[] = 'columns-'.$columns;
			
			if( $style == 'partial-view'){
				if( $is_slider && $columns % 2 == 0 && $item_layout == 'grid' ){
					$classes[] = $style;
				}
			}else{
				$classes[] = $style;
			}
				
			$classes[] = $extra_class;
			
			$atts = compact('layout', 'columns', 'categories', 'per_page', 'orderby', 'order'
							,'item_layout', 'show_title', 'show_thumbnail', 'show_author', 'show_categories'
							,'show_date', 'show_comment', 'show_excerpt', 'show_readmore', 'excerpt_words'
							,'is_slider', 'show_nav', 'auto_play', 'margin', 'is_masonry', 'show_load_more');
			?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>">
			
				<?php if( $title ): ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
				<?php endif; ?>
				
				<div class="content-wrapper">
					<div class="blogs items">
						<?php ts_get_blog_items_content_shortcode($atts, $posts); ?>
					</div>
					<?php if( $show_load_more ): ?>
					<div class="load-more-wrapper">
						<a href="#" class="load-more" data-total_pages="<?php echo $posts->max_num_pages; ?>" data-paged="2"><?php echo esc_html($load_more_text) ?></a>
					</div>
					<?php endif; ?>
				</div>
			</div>
		<?php
		endif;
		wp_reset_postdata();
		return ob_get_clean();
	}	
}
add_shortcode('ts_blogs', 'ts_blogs_shortcode');

add_action('wp_ajax_ts_blogs_load_items', 'ts_get_blog_items_content_shortcode');
add_action('wp_ajax_nopriv_ts_blogs_load_items', 'ts_get_blog_items_content_shortcode');
if( !function_exists('ts_get_blog_items_content_shortcode') ){
	function ts_get_blog_items_content_shortcode($atts, $posts = null){
		
		global $post;
		
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			if( !isset($_POST['atts']) ){
				die('0');
			}
			$atts = $_POST['atts'];
			$paged = isset($_POST['paged'])?absint($_POST['paged']):1;
			
			extract($atts);
			
			$args = array(
				'post_type' 			=> 'post'
				,'post_status' 			=> 'publish'
				,'ignore_sticky_posts' 	=> 1
				,'posts_per_page'		=> $per_page
				,'orderby'				=> $orderby
				,'order'				=> $order
				,'paged'				=> $paged
				,'tax_query'			=> array()
			);
			
			$categories = str_replace(' ', '', $categories);
			if( strlen($categories) > 0 ){
				$categories = explode(',', $categories);
			}
			if( is_array($categories) && count($categories) > 0 ){
				$field_name = is_numeric($categories[0])?'term_id':'slug';
				$args['tax_query'][] = array(
											'taxonomy' => 'category'
											,'terms' => $categories
											,'field' => $field_name
											,'include_children' => false
										);
			}
			
			if( $item_layout == 'background' ){ // only load the standard posts
				$args['tax_query'][] = array(
						'taxonomy'	=> 'post_format'
						,'field'	=> 'slug'
						,'terms'    => array( 'post-format-audio', 'post-format-gallery', 'post-format-quote', 'post-format-video' )
						,'operator'	=> 'NOT IN'
					);
			}
			
			$posts = new WP_Query($args);
			ob_start();
		}
		
		extract($atts);
		
		$blog_thumb_size = 'drile_blog_thumb';
		if( $item_layout == 'list' ){
			$blog_thumb_size = 'drile_blog_list_thumb';
		}else{
			if( $layout == 'masonry' ){
				$blog_thumb_size = 'full';
			}
		}
		
		if( $posts->have_posts() ):
			$item_class = '';
			if( !$is_slider ){
				$item_class = 24/(int)$columns;
				$item_class = 'ts-col-'.$item_class;
			}
			$key = -1;
			$show_thumbnail_old = $show_thumbnail;
			while( $posts->have_posts() ): $posts->the_post();
				$show_thumbnail = $show_thumbnail_old;
			
				$post_format = get_post_format(); /* Video, Audio, Gallery, Quote */
				if( $is_slider && $post_format == 'gallery' ){ /* Remove Slider in Slider */
					$post_format = false;
				}
				
				$key++;
				$item_extra_class = ($key % $columns == 0)?'first':(($key % $columns == $columns - 1)?'last':'');
				?>
				<article class="item <?php echo ( $post_format == 'gallery' )?'nav-middle nav-margin ':'' ?><?php echo esc_attr($post_format); ?> <?php echo esc_attr($item_class.' '.$item_extra_class) ?>">
					<div class="article-content">
					<?php if( $show_thumbnail && $post_format != 'quote' ){ ?>
						<div class="thumbnail-content">
							<?php 
							if( $post_format == 'gallery' || $post_format === false || $post_format == 'standard' ){
							?>
								<a class="thumbnail <?php echo esc_attr($post_format); ?> <?php echo ($post_format == 'gallery')?'loading':''; ?>" href="<?php echo ($post_format == 'gallery')?'javascript: void(0)':get_permalink() ?>">
									<figure>
									<?php 
									
									if( $post_format == 'gallery' ){
										$gallery = get_post_meta($post->ID, 'ts_gallery', true);
										$gallery_ids = explode(',', $gallery);
										if( is_array($gallery_ids) && has_post_thumbnail() ){
											array_unshift($gallery_ids, get_post_thumbnail_id());
										}
										foreach( $gallery_ids as $gallery_id ){
											echo wp_get_attachment_image( $gallery_id, $blog_thumb_size );
										}
										
										if( empty($gallery_ids) ){
											$show_thumbnail = false;
										}
									}
									
									if( $post_format === false || $post_format == 'standard' ){
										if( has_post_thumbnail() ){
											the_post_thumbnail( $blog_thumb_size ); 
										}
										else{
											$show_thumbnail = false;
										}
									}
									
									?>
									</figure>
									<div class="effect-thumbnail"></div>
								</a>
								
								
							<?php 
							}
							
							if( $post_format == 'video' ){
								$video_url = get_post_meta($post->ID, 'ts_video_url', true);
								echo do_shortcode('[ts_video src="'.$video_url.'"]');
								$show_thumbnail = false;
							}
							
							if( $post_format == 'audio' ){
								$audio_url = get_post_meta($post->ID, 'ts_audio_url', true);
								$show_thumbnail = false;
								if( strlen($audio_url) > 4 ){
									$file_format = substr($audio_url, -3, 3);
									if( in_array($file_format, array('mp3', 'ogg', 'wav')) ){
										echo do_shortcode('[audio '.$file_format.'="'.$audio_url.'"]');
									}
									else{
										echo do_shortcode('[ts_soundcloud url="'.$audio_url.'" width="100%" height="122"]');
									}
								}
							}
						?>
						
						</div>
					<?php } ?>
					
					<?php if( $post_format != 'quote' ): ?>
						
						<div class="entry-content">
							
							<?php if( $show_date || $show_comment ) : ?>
							
								<div class="entry-meta-top">
								
									<!-- Blog Date Time -->
									<?php if( $show_date ) : ?>
									<span class="date-time">
										<?php echo get_the_time( get_option('date_format') ); ?>
									</span>
									<?php endif; ?>									
									
									<!-- Blog Comment -->
									<?php if( $show_comment ): ?>
									<span class="comment-count">
										<?php
										$comment_count = drile_get_post_comment_count();
										echo sprintf( _n('%d comment', '%d comments', $comment_count, 'themesky'), $comment_count );
										?>
									</span>
									<?php endif; ?>

								</div>
								
							<?php endif; ?>
							
							<?php if( $show_title ): ?>
							<header>
								<h4 class="heading-title entry-title">
									<a class="post-title heading-title" href="<?php the_permalink() ; ?>"><?php the_title(); ?></a>
								</h4>
							</header>
							<?php endif; ?>
							
							<div class="entry-meta-middle">
								<!-- Blog Author -->
								<?php if( $show_author ): ?>
								<span class="vcard author"><?php esc_html_e('Post by ', 'themesky'); ?><?php the_author_posts_link(); ?></span>
								<?php endif; ?>
								
								<!-- Blog Categories -->
								<?php if( $show_categories ): ?>
								<div class="cats-link">
									<?php esc_html_e('in ', 'themesky'); ?><?php echo get_the_category_list(', '); ?>
								</div>
								<?php endif; ?>
							</div>
							
							<?php if( $show_excerpt && function_exists('drile_the_excerpt_max_words') ): ?>
							<div class="excerpt"><?php drile_the_excerpt_max_words($excerpt_words, '', true, '', true); ?></div>
							<?php endif; ?>
							
							<?php if( $show_readmore ): ?>
							<div class="entry-meta-bottom">
								<!-- Blog Read More Button -->
								<a class="button-readmore button-text" href="<?php the_permalink() ; ?>"><?php esc_html_e('Read More', 'themesky'); ?></a>
								
							</div>
							<?php endif; ?>
						</div>
							
						<?php else: /* Post format is quote */ ?>
							<div class="quote-wrapper">
								<blockquote>&ldquo; 
									<?php 
									$quote_content = get_the_excerpt();
									if( !$quote_content ){
										$quote_content = get_the_content();
									}
									echo do_shortcode($quote_content);
									?>
								&rdquo;</blockquote>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php 
			endwhile;
		endif;
		
		wp_reset_postdata();
		
		if( defined( 'DOING_AJAX' ) && DOING_AJAX ){
			die(ob_get_clean());
		}
		
	}
}

/* TS Google Map shortcode */
if( !function_exists('ts_google_map_shortcode') ){
	function ts_google_map_shortcode($atts, $content = ''){
		extract(shortcode_atts(array(
						'address'			=> ''
						,'height'			=> 360
						,'zoom'				=> 12
						,'map_type'			=> 'ROADMAP'
						,'grayscale'		=> 1
						,'title'			=> ''
						,'extra_class'		=> ''
					), $atts));
		$gmap_api_key = function_exists('drile_get_theme_options')?drile_get_theme_options('ts_gmap_api_key'):'';

		if( $gmap_api_key ){
			wp_enqueue_script('gmap-api');
		}
		
		ob_start();
		if( $gmap_api_key ){
			$classes = array();
			$classes[] = $extra_class;
			if( $grayscale ){
				$classes[] = 'grayscale';
			}
		?>
		<div class="google-map-container <?php echo esc_attr(implode(' ', $classes)) ?>" style="height:<?php echo esc_attr($height); ?>px" 
			data-address="<?php echo esc_attr($address) ?>" data-zoom="<?php echo esc_attr($zoom) ?>" data-map_type="<?php echo esc_attr($map_type) ?>" data-title="<?php echo esc_attr($title) ?>">
			<div style="height:<?php echo esc_attr($height); ?>px" class="map-content"></div>
			<?php if( $content ): ?>
			<div class="information">
				<?php echo apply_filters('the_content', $content); ?>
			</div>
			<?php endif; ?>
		</div>
		<?php
		}
		else{
			$params = array(
				rawurlencode( $address )
				,absint( $zoom )
			);
			$url = 'https://maps.google.com/maps?q=%1$s&amp;t=m&amp;z=%2$d&amp;output=embed&amp;iwloc=near';
			?>
			<div class="google-map-embed">
				<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
						src="<?php echo esc_url( vsprintf( $url, $params ) ); ?>"
						height="<?php echo esc_attr($height); ?>"
						width="100%"
						title="<?php echo esc_attr( $address ); ?>"
						aria-label="<?php echo esc_attr( $address ); ?>"
				></iframe>
			</div>
			<?php
		}
		return ob_get_clean();
	}
}
add_shortcode('ts_google_map', 'ts_google_map_shortcode');

/* Shortcode Video - Support Youtube and Vimeo video */
if( !function_exists('ts_video_shortcode') ){
	function ts_video_shortcode($atts){
		extract( shortcode_atts(array(
				'src' 		=> '',
				'height' 	=> '450',
				'width' 	=> '800'
			), $atts
		));
	if( $src == '' ){
		return;
	}
	
	$extra_class = '';
	if( !isset($atts['height']) || !isset($atts['width']) ){
		$extra_class = 'auto-size';
	}
	
	$src = ts_parse_video_link($src);
    ob_start();
	?>
		<div class="ts-video <?php echo esc_attr($extra_class); ?>" style="width:<?php echo esc_attr($width) ?>px; height:<?php echo esc_attr($height) ?>px;">
			<iframe width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($src); ?>" allowfullscreen></iframe>
		</div>
	<?php
	return ob_get_clean();
	}
}
add_shortcode('ts_video', 'ts_video_shortcode');

/* Shortcode Video width Placeholder image */
if( !function_exists('ts_video_2_shortcode') ){
	function ts_video_2_shortcode($atts){
		extract( shortcode_atts(array(
				'video_url' 				=> ''
				,'placeholder_image_id' 	=> ''
				,'placeholder_image_url' 	=> ''
				,'extra_class' 				=> ''
			), $atts
		));
		if( $video_url == '' ){
			return;
		}
		
		ob_start();
		if( !$placeholder_image_id && !$placeholder_image_url ){
			echo do_shortcode('[ts_video src="'.$video_url.'"]');
		}
		else{
		?>
		<div class="ts-video-2 <?php echo esc_attr($extra_class); ?>">
			<a href="#">
				<?php 
				if( $placeholder_image_id ){
					echo wp_get_attachment_image($placeholder_image_id, 'full');
				}
				else{
				?>
				<img src="<?php echo esc_url($placeholder_image_url); ?>" alt="<?php esc_attr_e('Video Placeholder Image', 'themesky'); ?>" />
				<?php } ?>
			</a>
			<div class="ts-popup-modal ts-video-modal">
				<div class="overlay"></div>
				<div class="video-container popup-container">
					<span class="close"><?php esc_html_e('Close ', 'themesky'); ?></span>
					<div class="video-content">
					<?php echo do_shortcode('[ts_video src="'.$video_url.'"]'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		return ob_get_clean();
	}
}
add_shortcode('ts_video_2', 'ts_video_2_shortcode');

if( !function_exists('ts_parse_video_link') ){
	function ts_parse_video_link( $video_url ){
		if( strstr($video_url, 'youtube.com') || strstr($video_url, 'youtu.be') ){
			preg_match('%(?:youtube\.com/(?:user/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match);
			if( count($match) >= 2 ){
				return '//www.youtube.com/embed/' . $match[1];
			}
		}
		elseif( strstr($video_url, 'vimeo.com') ){
			preg_match('~^http://(?:www\.)?vimeo\.com/(?:clip:)?(\d+)~', $video_url, $match);
			if( count($match) >= 2 ){
				return '//player.vimeo.com/video/' . $match[1];
			}
			else{
				$video_id = explode('/', $video_url);
				if( is_array($video_id) && !empty($video_id) ){
					$video_id = $video_id[count($video_id) - 1];
					return '//player.vimeo.com/video/' . $video_id;
				}
			}
		}
		return $video_url;
	}
}

/* Shortcode SoundCloud */
if( !function_exists('ts_soundcloud_shortocde') ){
	function ts_soundcloud_shortocde( $atts, $content ){
		extract(shortcode_atts(array(
			'params'		=> "color=ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false"
			,'url'			=> ''
			,'width'		=> '100%'
			,'height'		=> '166'
			,'iframe'		=> 1
		),$atts));
		
		$atts = compact( 'params', 'url', 'width', 'height', 'iframe' );
		
		if( $iframe ){
			return ts_soundcloud_iframe_widget( $atts );
		}
		else{ 
			return ts_soundcloud_flash_widget( $atts );
		}
	}
}
add_shortcode('ts_soundcloud','ts_soundcloud_shortocde');


function ts_soundcloud_iframe_widget($options) {
	$url = 'https://w.soundcloud.com/player/?url=' . $options['url'] . '&' . $options['params'];
	$unique_class = 'ts-soundcloud-'.rand();
	$style = '.'.$unique_class.' iframe{width: '.$options['width'].'; height:'.$options['height'].'px;}';
	$style = '<style type="text/css" scoped>'.$style.'</style>';
	return '<div class="ts-soundcloud '.$unique_class.'">'.$style.'<iframe src="'.esc_url( $url ).'"></iframe></div>';
}

function ts_soundcloud_flash_widget( $options ){
	$url = 'https://player.soundcloud.com/player.swf?url=' . $options['url'] . '&' . $options['params'];
	
	return preg_replace('/\s\s+/', '', sprintf('<div class="ts-soundcloud"><object width="%s" height="%s">
							<param name="movie" value="%s"></param>
							<param name="allowscriptaccess" value="always"></param>
							<embed width="%s" height="%s" src="%s" allowscriptaccess="always" type="application/x-shockwave-flash"></embed>
						  </object></div>', $options['width'], $options['height'], esc_url( $url ), $options['width'], $options['height'], esc_url( $url )));
}

/* Milestone shortcode */
if( !function_exists('ts_milestone_shortcode') ){
	function ts_milestone_shortcode( $atts ){
		extract( shortcode_atts(array(
				'style'				=> 'style-default'
				,'plus_icon'		=> 0
				,'number'			=> 0
				,'subject'			=> ''
				,'text_color_style'	=> 'text-default'
				,'extra_class'		=> ''
			), $atts)
		);
		
		wp_enqueue_script( 'jquery-waypoints' );
		wp_enqueue_script( 'jquery-countto' );
		
		if( !is_numeric($number) ){
			$number = 0;
		}
		
		$classes = array();
		$classes[] = $style;
		$classes[] = $text_color_style;
		$classes[] = $extra_class;
		
		ob_start();
		?>
		<div class="ts-milestone <?php echo esc_attr(implode(' ', $classes)); ?>" data-number="<?php echo esc_attr($number); ?>">
			<span class="number">
				<span class="count"><?php echo esc_html($number); ?></span><?php if( $plus_icon ): ?><span class="icon-plus">+</span><?php endif; ?>
			</span>
			<h3 class="subject">
				<?php echo esc_html($subject); ?>
			</h3>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('ts_milestone', 'ts_milestone_shortcode');

/* Countdown shortcode */
if( !function_exists('ts_countdown_shortcode') ){
	function ts_countdown_shortcode( $atts ){
		extract( shortcode_atts(array(
				'style'				=> ''
				,'day'				=> ''
				,'month'			=> ''
				,'year'				=> ''
				,'text_color_style'	=> 'text-default'
				,'extra_class'		=> ''
				,'seconds'			=> 0 /* Used for product deals */
			), $atts)
		);
		
		if( !$seconds ){
			if( empty($month) || empty($day) || empty($year) ){
				return;
			}
			
			if( !checkdate($month, $day, $year) ){
				return;
			}
			
			$date = mktime(0, 0, 0, $month, $day, $year);
			$current_time = current_time('timestamp');
			$delta = $date - $current_time;
			
			if( $delta <= 0 ){
				return;
			}
		}
		else{
			$delta = $seconds;
		}
		
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
		
		$classes = array();
		$classes[] = $style;
		$classes[] = $text_color_style;
		$classes[] = $extra_class;
		
		ob_start();
		?>
		<div class="ts-countdown <?php echo esc_attr(implode(' ', $classes)); ?>">
			<div class="counter-wrapper days-<?php echo strlen($day); ?>">
				<div class="days">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($day); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php esc_html_e('Days', 'themesky'); ?>
					</div>
				</div>
				<div class="hours">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($hour); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php esc_html_e('Hours', 'themesky'); ?>
					</div>
				</div>
				<div class="minutes">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($minute); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php esc_html_e('Mins', 'themesky'); ?>
					</div>
				</div>
				<div class="seconds">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($second); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php esc_html_e('Secs', 'themesky'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('ts_countdown', 'ts_countdown_shortcode');

/* Image Gallery */
if( !function_exists('ts_image_gallery_shortcode') ){
	function ts_image_gallery_shortcode( $atts ){
		extract( shortcode_atts(array(
				'images' 				=> ''
				,'image_size'			=> 'thumbnail'
				,'is_slider' 			=> 0
				,'columns' 				=> 4
				,'on_click'				=> 'none' /* none, prettyphoto, custom_link */
				,'custom_links' 		=> ''
				,'custom_link_target' 	=> '_self' /* _self, _blank */
				,'margin_class' 		=> ''
				,'extra_class' 			=> ''
				,'show_nav' 			=> 1
				,'show_dots' 			=> 0
				,'dots_position' 		=> 'dots-horizontal'
				,'auto_play' 			=> 1
				,'responsive_items' 	=> 1
				,'margin' 				=> 0
			), $atts)
		);
		
		$images = str_replace(' ', '', $images);
		if( $images == '' ){
			return;
		}
		$images = explode(',', $images);
		
		if( !$image_size ){
			$image_size = 'full';
		}
		
		if( $custom_links != '' ){
			$custom_links = array_map('trim', explode(',', $custom_links));
		}
		else{
			$custom_links = array();
		}
		
		$columns = absint($columns);
		
		if( $on_click == 'prettyphoto' ){
			wp_enqueue_script( 'prettyphoto' );
			$rel_id = 'ts-gallery-'.mt_rand();
		}
		
		ob_start();
		$classes = array();
		$classes[] = 'ts-image-gallery-wrapper ts-shortcode';
		$classes[] = $is_slider?'ts-slider':'';
		$classes[] = 'columns-'.$columns;
		$classes[] = $margin_class;
		$classes[] = $extra_class;
		if( $show_nav ){
			$classes[] = 'show-nav nav-middle';
		}
		if( $show_dots ){
			$show_nav = 0;
			$classes[] = 'show-dots';
			$classes[] = $dots_position;
		}
		
		$data_attr = array();
		if( $is_slider ){
			$data_attr[] = 'data-nav="'.$show_nav.'"';
			$data_attr[] = 'data-dots="'.$show_dots.'"';
			$data_attr[] = 'data-autoplay="'.$auto_play.'"';
			$data_attr[] = 'data-columns="'.$columns.'"';
			$data_attr[] = 'data-margin="'.absint($margin).'"';
			$data_attr[] = 'data-responsive="'.absint($responsive_items).'"';
		}
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr); ?>>
			<div class="images items <?php echo ($is_slider)?'loading':''; ?>">
				<?php 
				foreach( $images as $index => $image ): 
				$item_classes = array();
				if( !$is_slider){
					if( $columns > 1 ){
						if( $index % $columns == 0 ){
							$item_classes[] = 'first';
						}
						if( $index % $columns == $columns - 1 || $index == count($images) - 1 ){
							$item_classes[] = 'last';
						}
					}
				}
				?>
				<div class="item <?php echo implode(' ', $item_classes); ?>">
					<?php 
					if( $on_click == 'prettyphoto' || $on_click == 'custom_link' ){
						if( $on_click == 'prettyphoto' ){
							$href = wp_get_attachment_url($image);
							$data_rel = 'data-rel="prettyPhoto['.$rel_id.']"';
							$target = '';
						}
						else{
							$href = isset($custom_links[$index])?$custom_links[$index]:'#';
							$data_rel = '';
							$target = 'target="'.$custom_link_target.'"';
						}
						echo '<a class="'.$on_click.'" href="'.esc_url($href).'" '.$data_rel.' '.$target.'>';
					}
					echo wp_get_attachment_image($image, $image_size);
					if( $on_click == 'prettyphoto' || $on_click == 'custom_link' ){
						echo '</a>';
					}
					?>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('ts_image_gallery', 'ts_image_gallery_shortcode');

/* Instagram */
if( !function_exists('ts_instagram_shortcode') ){
	function ts_instagram_shortcode( $atts ){
		extract( shortcode_atts(array(
				'title'			=> ''
				,'style'		=> ''
				,'username' 	=> ''
				,'access_token' => ''
				,'number'		=> 9
				,'column' 		=> 5
				,'column_gap' 	=> ''
				,'size' 		=> 'large'
				,'target'		=> '_self'
				,'cache_time' 	=> 12
				,'is_slider'	=> 0
				,'show_nav' 	=> 1
				,'auto_play' 	=> 1
				,'margin_item' 	=> 0
				,'margin' 		=> 0
			), $atts)
		);
		
		if( !class_exists('TS_Instagram_Widget') ){
			return;
		}
		
		$classes = array();
		$classes[] = 'ts-instagram-shortcode ts-shortcode heading-center';
		$classes[] = $style;
		$classes[] = $column_gap;
		if( $is_slider ){
			$classes[] = 'ts-slider';
			if( $show_nav ){
				$classes[] = 'nav-middle';
			}
		}
		
		$instance = compact('title', 'username', 'access_token', 'number', 'column', 'size', 'target', 'cache_time', 'is_slider', 'show_nav', 'auto_play', 'margin');
		
		$args = array(
			'before_widget' => '<section class="widget-container %s">'
			,'after_widget' => '</section>'
			,'before_title' => '<header class="shortcode-heading-wrapper"><h2 class="shortcode-title">'
			,'after_title'  => '</h2></header>'
		);
		
		ob_start();
		
		echo '<div class="'.implode(' ', $classes).'">';
		
		the_widget('TS_Instagram_Widget', $instance, $args);
		
		echo '</div>';
		
		return ob_get_clean();
	}
}
add_shortcode('ts_instagram', 'ts_instagram_shortcode');
?>