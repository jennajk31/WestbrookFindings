<div class="ts-social-sharing">
	<span><?php esc_html_e('Share:', 'themesky'); ?></span>
	<ul>
		<li class="facebook">
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_url(get_permalink()); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
		</li>
	
		<li class="twitter">
			<a href="https://twitter.com/intent/tweet?text=<?php echo esc_url(get_permalink()); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
		</li>
	
		<li class="pinterest">
			<?php $image_link  = wp_get_attachment_url( get_post_thumbnail_id() );?>
			<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_url(get_permalink()); ?>&amp;media=<?php echo esc_url($image_link);?>" target="_blank"><i class="fab fa-pinterest-p"></i></a>
		</li>
	
		<li class="linkedin">
			<a href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_url(get_permalink()); ?>&amp;title=<?php echo esc_attr(sanitize_title(get_the_title())); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
		</li>
	
		<li class="reddit">
			<a href="http://www.reddit.com/submit?url=<?php echo esc_url(get_permalink()); ?>&amp;title=<?php echo esc_attr(sanitize_title(get_the_title())); ?>" target="_blank"><i class="fab fa-reddit-alien"></i></a>
		</li>
	</ul>
</div>