<?php 
get_header();

$image_404 = drile_get_theme_options('ts_image_not_found');
$image_404 = !empty($image_404['url'])?$image_404['url']:'';
?>
	<div class="fullwidth-template">
		<div id="main-content">	
			<div id="primary" class="site-content">
				<article>
					<h1 class="heading-1 special-font"><?php esc_html_e('404', 'drile'); ?></h1>
					
					<?php if( $image_404 ): ?>
						<img src="<?php echo esc_url($image_404); ?>" alt="<?php esc_attr_e('404 image', 'drile'); ?>" />
					<?php endif; ?>
					
					<h5 class="heading-2 heading-body"><?php esc_html_e('The page you are looking for does not exist', 'drile'); ?></h5>
					<p class="ts-description-2 primary-text"><?php esc_html_e('Try to search again or use the go back button below', 'drile'); ?></p>
					<a href="<?php echo esc_url( home_url('/') ) ?>" class="button"><?php esc_html_e('Back To Home', 'drile'); ?></a>
				</article>
			</div>
		</div>
	</div>
<?php
get_footer();