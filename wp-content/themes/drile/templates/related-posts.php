<?php 
global $post;
$theme_options = drile_get_theme_options();
$blog_thumb_size = 'drile_blog_thumb';

$cat_list = get_the_category($post->ID);
$cat_ids = array();
foreach( $cat_list as $cat ){
	$cat_ids[] = $cat->term_id;
}
$cat_ids = implode(',', $cat_ids);

if( strlen($cat_ids) > 0 ){
	$args = array(
		'post_type' => $post->post_type
		,'cat' => $cat_ids
		,'post__not_in' => array($post->ID)
		,'posts_per_page'	=> 5
	);
}
else{
	$args = array(
		'post_type' => $post->post_type
		,'post__not_in' => array($post->ID)
		,'posts_per_page'	=> 5
	);
}

/* Remove the quote post format */
$args['tax_query'] = array(
	array(
		'taxonomy'	=> 'post_format'
		,'field'	=> 'slug'
		,'terms'    => array( 'post-format-quote' )
		,'operator'	=> 'NOT IN'
	)
);

$related_posts = new WP_Query($args);
	
if( $related_posts->have_posts() ){
?>
	<div class="ts-blogs ts-shortcode related-posts related nav-top ts-slider loading">
		<header class="theme-title">
			<h3 class="heading-title"><?php esc_html_e('Related Posts', 'drile'); ?></h3>
		</header>
		<div class="content-wrapper">
			<div class="blogs items">
				<?php 
				while( $related_posts->have_posts() ): $related_posts->the_post();
				
				$post_format = get_post_format(); /* Video, Audio, Gallery, Quote */
				if( $post_format == 'gallery' ){ /* Remove Slider in Slider */
					$post_format = false;
				}
				$show_thumbnail = in_array($post_format, array('gallery', 'standard', 'video', 'audio', 'quote', false));
				?>
				<article class="item <?php echo esc_attr($post_format); ?>">
					<div class="article-content">
						<?php if( $show_thumbnail ): ?>
						<div class="thumbnail-content">
						
							<?php if( $post_format == 'gallery' || $post_format === false || $post_format == 'standard' ){ ?>
								<a class="thumbnail <?php echo esc_attr($post_format); ?> <?php echo ('gallery' == $post_format)?'loading':''; ?>" href="<?php echo ('gallery' == $post_format)?'javascript: void(0)':get_permalink() ?>">
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
											the_post_thumbnail($blog_thumb_size); 
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
								if( $video_url ){
									echo do_shortcode('[ts_video src="'.$video_url.'"]');
								}
								else{
									$show_thumbnail = false;
								}
							}
							
							if( $post_format == 'audio' ){
								$audio_url = get_post_meta($post->ID, 'ts_audio_url', true);
								if( strlen($audio_url) > 4 ){
									$file_format = substr($audio_url, -3, 3);
									if( in_array($file_format, array('mp3', 'ogg', 'wav')) ){
										echo do_shortcode('[audio '.$file_format.'="'.$audio_url.'"]');
									}
									else{
										echo do_shortcode('[ts_soundcloud url="'.$audio_url.'" width="100%" height="122"]');
									}
								}
								else{
									$show_thumbnail = false;
								}
							}
							?>
							
						</div>
						<?php endif; ?>
					
						<div class="entry-content <?php echo !$show_thumbnail?'no-featured-image':'' ?>">
						
							<div class="entry-meta-top">
					
								<!-- Blog Date Time -->
								<span class="date-time">
									<?php echo get_the_time( get_option('date_format') ); ?>
								</span>
								
								<!-- Blog Comment -->
								<?php if( $theme_options['ts_blog_comment'] ): ?>
								<span class="comment-count">
									<?php
									$comment_count = drile_get_post_comment_count();
									echo sprintf( _n('%d comment', '%d comments', $comment_count, 'drile'), $comment_count );
									?>
								</span>
								<?php endif; ?>
								
							</div>
							
							<header>
								<h5 class="heading-title entry-title">
									<a class="post-title heading-title" href="<?php the_permalink() ; ?>"><?php the_title(); ?></a>
								</h5>
								
							</header>
							
							<div class="entry-meta-middle">
								<!-- Blog Categories -->
								<?php if( $theme_options['ts_blog_categories'] ): ?>
								<div class="cats-link">
									<?php esc_html_e('in ', 'drile'); ?><?php echo get_the_category_list(', '); ?>
								</div>
								<?php endif; ?>
							</div>
							
							<div class="excerpt"><?php drile_the_excerpt_max_words(24, '', true, '', true); ?></div>
							
							<div class="entry-meta-bottom">
								<!-- Blog Read More Button -->
								<a class="button-readmore button-text" href="<?php the_permalink() ; ?>"><?php esc_html_e('Read more', 'drile'); ?></a>
								
							</div>
							
						</div>
					</div>
					
				</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
	
<?php 
}
wp_reset_postdata();
?>