<?php
/**
 * Title: Shop Call to Action
 * Slug: tazza/shop-cta
 * Categories: featured
 */
?>

<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/content-pixie-m-gqDRzbJLQ-unsplash.jpg","dimRatio":20,"overlayColor":"foreground","focalPoint":{"x":0.33,"y":0.84},"minHeight":50,"minHeightUnit":"vw","contentPosition":"center center","isDark":false,"align":"full","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}}}} -->
<div class="wp-block-cover alignfull is-light" style="margin-top:0px;margin-bottom:0px;min-height:50vw"><span aria-hidden="true" class="wp-block-cover__background has-foreground-background-color has-background-dim-20 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/content-pixie-m-gqDRzbJLQ-unsplash.jpg" style="object-position:33% 84%" data-object-fit="cover" data-object-position="33% 84%"/><div class="wp-block-cover__inner-container">
	<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"300","fontSize":"40px"},"spacing":{"margin":{"top":"6px","bottom":"8px"}}},"textColor":"background"} -->
	<h2 class="has-text-align-center has-background-color has-text-color" id="keplar-collection" style="margin-top:6px;margin-bottom:8px;font-size:40px;font-style:normal;font-weight:300">
		<?php echo wp_kses_post( 'Everything you need <br>to make a perfect cuppa.', 'tazza' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"24px"}}}} -->
	<div class="wp-block-buttons" style="margin-top:24px">
		<!-- wp:button {"backgroundColor":"background","textColor":"foreground","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px"}},"className":"is-style-fill","fontFamily":"system-font"} -->
			<div class="wp-block-button is-style-fill has-system-font-font-family" style="letter-spacing:1px;text-transform:uppercase">
				<a class="wp-block-button__link has-foreground-color has-background-background-color has-text-color has-background wp-element-button"><?php _e( 'Shop accessories', 'tazza' ); ?></a>
			</div><!-- /wp:button -->
	</div><!-- /wp:buttons -->
</div></div>
<!-- /wp:cover -->