<?php 
$theme_options = drile_get_theme_options();
$facebook_url = $theme_options['ts_facebook_url'];
$twitter_url = $theme_options['ts_twitter_url'];
$youtube_url = $theme_options['ts_youtube_url'];
$instagram_url = $theme_options['ts_instagram_url'];
$linkedin_url = $theme_options['ts_linkedin_url'];
$custom_url = $theme_options['ts_custom_social_url'];
$custom_class = $theme_options['ts_custom_social_class'];
?>
<div class="ts-header-social-icons">
	<ul>
			
		<?php if( $facebook_url != '' ): ?>
		<li class="facebook">
			<a href="<?php echo esc_url($facebook_url); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
		</li>
		<?php endif; ?>

		<?php if( $twitter_url != '' ): ?>
		<li class="twitter">
			<a href="<?php echo esc_url($twitter_url); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
		</li>
		<?php endif; ?>
		
		<?php if( $youtube_url != '' ): ?>
		<li class="youtube">
			<a href="<?php echo esc_url($youtube_url); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
		</li>
		<?php endif; ?>
		
		<?php if( $instagram_url != '' ): ?>
		<li class="instagram">
			<a href="<?php echo esc_url($instagram_url); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
		</li>
		<?php endif; ?>
		
		<?php if( $linkedin_url != '' ): ?>
		<li class="linkedin">
			<a href="<?php echo esc_url($linkedin_url); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
		</li>
		<?php endif; ?>
		
		<?php if( $custom_url != '' ): ?>
		<li class="custom">
			<a href="<?php echo esc_url($custom_url); ?>" target="_blank"><i class="<?php echo esc_attr($custom_class) ?>"></i></a>
		</li>
		<?php endif; ?>

	</ul>
</div>