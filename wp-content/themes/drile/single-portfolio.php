<?php 
get_header();

global $post;

setup_postdata( $post );

wp_enqueue_script( 'prettyphoto' );

$theme_options = drile_get_theme_options();

$show_breadcrumb = apply_filters('drile_show_breadcrumb_on_single_portfolio', true);

$container_classes = array();
if( $show_breadcrumb ){
	$container_classes[] = 'show_breadcrumb_' . $theme_options['ts_breadcrumb_layout'];
}

$video_url = get_post_meta($post->ID, 'ts_video_url', true);

$thumbnail_style = $theme_options['ts_portfolio_thumbnail_style'];

$classes = array();
$classes[] = $thumbnail_style;

if( $thumbnail_style == 'gallery' ){
	wp_enqueue_script( 'isotope' );
}

$is_slider = false;
if( $thumbnail_style == 'slider' && empty($video_url) ){
	$is_slider = true;
}

drile_breadcrumbs_title($show_breadcrumb, false, '');
?>
<div id="content" class="page-container container-post <?php echo esc_attr(implode(' ', $container_classes)) ?>">
	
	<!-- main-content -->
	<div id="main-content" class="ts-col-24">
		<article class="single single-post single-portfolio <?php echo esc_attr(implode(' ', $classes)) ?>">
			
			<?php if( $theme_options['ts_portfolio_details_next_prev_navigation'] ): ?>
				<div class="navigation-top clearfix">
					<!-- Next Prev Blog -->
					<div class="single-navigation-1">
					<?php previous_post_link('%link', esc_html__('Previous Post', 'drile')); ?>
					</div>
					
					<!-- Next Prev Blog -->
					<div class="single-navigation-2">
					<?php next_post_link('%link', esc_html__('Next Post', 'drile')); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="entry-header">
			
				<div class="entry-title-left">

					<header>
						<!-- Portfolio Title -->
						<?php if( $theme_options['ts_portfolio_title'] ): ?>
							<h3 class="entry-title"><?php the_title() ?></h3>
						<?php endif; ?>
					</header>
					
					<div class="entry-meta-middle">						
						
						<!-- Portfolio Client -->
						<?php $client = get_post_meta($post->ID, 'ts_client', true); ?>
						<?php if( $theme_options['ts_portfolio_client'] && $client ): ?>
							<div class="portfolio-info">
								<span><?php esc_html_e('Client:', 'drile') ?></span>
								<span class="client"><?php echo esc_html($client); ?></span>
							</div>
						<?php endif; ?>
						
						<!-- Portfolio Year -->
						<?php $year = get_post_meta($post->ID, 'ts_year', true); ?>
						<?php if( $theme_options['ts_portfolio_year'] && $year ): ?>
							<div class="portfolio-info">
								<span><?php esc_html_e('Year:', 'drile') ?></span>
								<span class="year"><?php echo esc_html($year); ?></span>
							</div>
						<?php endif; ?>
						
						<!-- Portfolio Categories -->
						<?php
						$categories_list = get_the_term_list($post->ID, 'ts_portfolio_cat', '', ', ', '');
						if ( $categories_list && $theme_options['ts_portfolio_categories'] ):
						?>
							<div class="portfolio-info">
								<span><?php esc_html_e('Categories:', 'drile'); ?></span>
								<span class="cat-links"><?php echo wp_kses_post($categories_list); ?></span>
							</div>
						<?php endif; ?>
						
						<!-- Portfolio Custom Field -->
						<?php if( $theme_options['ts_portfolio_custom_field'] ): ?>
							<div class="portfolio-info">
								<span><?php echo esc_html($theme_options['ts_portfolio_custom_field_title']); ?>:</span>
								<div class="custom-field">
									<?php echo do_shortcode( stripslashes( wp_specialchars_decode( $theme_options['ts_portfolio_custom_field_content'] ) ) ) ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Portfolio Likes -->
						<?php if( $theme_options['ts_portfolio_likes'] ): ?>
							<div class="portfolio-info like-button">
							<?php
								global $ts_portfolios;
								$like_num = 0;
								$already_like = false;
								if( is_a($ts_portfolios, 'TS_Portfolios') && method_exists($ts_portfolios, 'get_like') ){
									$like_num = $ts_portfolios->get_like($post->ID);
									$already_like = $ts_portfolios->user_already_like($post->ID);
								}
								?>
								<div class="portfolio-like">
									<span class="ic-like <?php echo esc_attr($already_like?'already-like':''); ?>" data-post_id="<?php echo esc_attr($post->ID) ?>"></span>
									<span class="like-num" data-single="<?php esc_attr_e('Like', 'drile'); ?>" data-plural="<?php esc_attr_e('Likes', 'drile'); ?>">
										<?php echo esc_html( sprintf( _n( '%s Like', '%s Likes', $like_num, 'drile' ), $like_num ) ); ?>
									</span>
								</div>
							</div>
						<?php endif; ?>						

					</div>
					
				</div>
				
				<!-- Blog Thumbnail -->
				<?php if( $theme_options['ts_portfolio_thumbnail'] ): ?>
				<div class="entry-format <?php echo esc_attr($is_slider?'nav-middle nav-margin':''); ?>">
					<div class="thumbnail <?php echo esc_attr($is_slider?'gallery loading':''); ?>">
						<?php if( empty($video_url) ): ?>
							<figure>
								<?php
								$gallery = get_post_meta($post->ID, 'ts_gallery', true);
								if( $gallery ){
									$gallery_ids = explode(',', $gallery);
								}
								else{
									$gallery_ids = array();
								}
								
								if( is_array($gallery_ids) && has_post_thumbnail() ){
									array_unshift($gallery_ids, get_post_thumbnail_id());
								}
								foreach( $gallery_ids as $gallery_id ){
									$image_url = '';
									$image_src = wp_get_attachment_image_src($gallery_id, 'full');
									if( $image_src ){
										$image_url = $image_src[0];
									}
										
									echo '<a href="'.$image_url.'" rel="prettyPhoto[portfolio-gallery]">';
									echo wp_get_attachment_image( $gallery_id, 'full' );
									echo '</a>';
								}						
								?>
							</figure>
						<?php 
						else:
							echo do_shortcode('[ts_video src="'.esc_url($video_url).'"]');
						endif;
						?>
					</div>
				</div>
				<?php endif; ?>
			</div>
			
			<div class="entry-content">	
				
				<!-- Portfolio Content -->
				<?php if( $theme_options['ts_portfolio_content'] ): ?>
					<div class="portfolio-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
				
				<div class="meta-content">
				
					<!-- Portfolio URL -->
					<?php if( $theme_options['ts_portfolio_url'] ): ?>
						<?php 
						$portfolio_url = get_post_meta($post->ID, 'ts_portfolio_url', true);
						if( $portfolio_url == '' ){
							$portfolio_url = get_the_permalink();
						}
						?>
						<div class="portfolio-info">
							<span><?php esc_html_e('Link:', 'drile') ?></span>
							<a href="<?php echo esc_url($portfolio_url); ?>" class="portfolio-url"><?php echo esc_url($portfolio_url); ?></a>
						</div>
					<?php endif; ?>
					
					<!-- Portfolio Sharing -->
					<?php if( $theme_options['ts_portfolio_sharing'] && function_exists('ts_template_social_sharing') ): ?>
						<div class="portfolio-info">
							<div class="social-sharing">
								<span><?php esc_html_e('Share:', 'drile'); ?></span>
								<?php ts_template_social_sharing(); ?>
							</div>
						</div>
					<?php endif; ?>
					
				</div>
				
			</div>
			
			<!-- Related Posts-->
			<?php 
			if( $theme_options['ts_portfolio_related_posts'] ){
				get_template_part('templates/related-portfolios');
			}
			?>
		
		</article>
	</div><!-- end main-content -->
	
</div>
<?php get_footer(); ?>