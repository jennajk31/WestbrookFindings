<?php
/**
 * Title: Footer
 * Slug: tazza/footer
 * Categories: featured
 */
?>

<!-- wp:group {"tagName":"footer","align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"4.5vw","bottom":"4.5vw"},"margin":{"top":"0px","bottom":"0px"}}},"backgroundColor":"secondary","textColor":"background","layout":{"inherit":false,"contentSize":"1000px","type":"constrained"}} -->
<footer class="wp-block-group alignfull has-background-color has-secondary-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:4.5vw;padding-bottom:4.5vw"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:site-title {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px"}}} /-->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo wp_kses_post( __( '123 Example St, San Francisco,<br>CA 12345-6789', 'tazza' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><a href="#"><?php echo esc_html__( 'contact@example.com', 'tazza' ); ?></a><br><?php echo esc_html__( '0123-456-7890', 'tazza' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"8px"}}} -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"bottom":"24px"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-bottom:24px;font-style:normal;font-weight:700;text-transform:uppercase"><?php _e( 'Quick Links', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'About Us', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Contact', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Shipping', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Returns', 'tazza' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"8px"}}} -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"bottom":"24px"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-bottom:24px;font-style:normal;font-weight:700;text-transform:uppercase"><?php _e( 'Shop', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'New Arrivals', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Bestsellers', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Collections', 'tazza' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"8px"}}} -->
<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"bottom":"24px"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-bottom:24px;font-style:normal;font-weight:700;text-transform:uppercase"><?php _e( 'Follow Us', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Facebook', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Instagram', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php _e( 'Pinterest', 'tazza' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|tertiary"}}},"spacing":{"padding":{"top":"24px","bottom":"24px"},"blockGap":"0px","margin":{"top":"0px","bottom":"0px"}}},"backgroundColor":"secondary","textColor":"tertiary","layout":{"inherit":false,"contentSize":"1000px","type":"constrained"}} -->
<div class="wp-block-group alignfull has-tertiary-color has-secondary-background-color has-text-color has-background has-link-color" style="margin-top:0px;margin-bottom:0px;padding-top:24px;padding-bottom:24px"><!-- wp:group {"align":"wide","layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-group alignwide">
	<!-- wp:site-title {"level":0,"style":{"typography":{"lineHeight":"1","fontStyle":"normal","fontWeight":"400"}},"fontSize":"small"} /-->

	<!-- wp:paragraph {"className":"has-x-small-font-size","fontSize":"small"} -->
		<p class="has-small-font-size">
			<?php
			printf(
				/* Translators: WordPress.com link. */
				esc_html__( 'Designed with %s', 'tazza' ),
				'<a href="' . esc_url( __( 'https://wordpress.com', 'tazza' ) ) . '" rel="nofollow">WordPress.com</a>'
				);
			?>
		</p>
		<!-- /wp:paragraph -->
	</div><!-- /wp:group -->
</footer><!-- /wp:group -->