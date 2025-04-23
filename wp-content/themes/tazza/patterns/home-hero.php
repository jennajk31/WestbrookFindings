<?php
/**
 * Title: Homepage Hero
 * Slug: tazza/home-hero
 * Categories: featured
 */
?>


<!-- wp:media-text {"align":"full","mediaType":"image","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"backgroundColor":"tertiary"} -->
<div class="wp-block-media-text alignfull is-stacked-on-mobile has-tertiary-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0">
	<figure class="wp-block-media-text__media"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/teacora-rooibos-cOWE5cctcZI-unsplash.jpg" alt=""/></figure>

	<div class="wp-block-media-text__content">
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">

			<!-- wp:heading --><h2><?php _e( 'Our collection of fall tea flavors for autumn', 'tazza' ); ?></h2><!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"color":{"text":"#343230a6"}},"fontSize":"small"} -->
			<p class="has-text-color has-small-font-size" style="color:#343230a6"><?php echo esc_html__( 'There&rsquo;s no better way to relax than to sit down with a cozy blanket and a mug of hot tea. Check our collection for new flavors to try this fall.', 'tazza' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"backgroundColor":"foreground","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px"}}} -->
				<div class="wp-block-button" style="letter-spacing:1px;text-transform:uppercase"><a class="wp-block-button__link has-foreground-background-color has-background wp-element-button"><?php _e( 'Shop Now', 'tazza' ); ?></a>
				</div><!-- /wp:button -->
			</div><!-- /wp:buttons -->
		</div><!-- /wp:group -->
	</div>
</div>
<!-- /wp:media-text -->