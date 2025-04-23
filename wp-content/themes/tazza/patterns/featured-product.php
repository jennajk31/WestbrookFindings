<?php
/**
 * Title: Featured Product
 * Slug: tazza/featured-product
 * Categories: featured
 */
?>

<!-- wp:group {"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"padding":{"right":"var:preset|spacing|70"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--80);margin-bottom:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--70)"><!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="has-x-large-font-size"><?php _e( 'Rooibos Loose Leaf Tea', 'tazza' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"color":{"text":"#343230a6"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#343230a6"><?php echo esc_html__( 'Try this naturally caffeine-free tea any time of the day or night. It&rsquo;s loaded with antioxidants and minerals to boot!', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"foreground","style":{"typography":{"textTransform":"uppercase","letterSpacing":"1px"}},"fontSize":"x-small"} -->
<div class="wp-block-button has-custom-font-size has-x-small-font-size" style="letter-spacing:1px;text-transform:uppercase"><a class="wp-block-button__link has-foreground-background-color has-background wp-element-button"><?php _e( 'Add to Cart', 'tazza' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"id":74,"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/teacora-rooibos-w0BdR1eXlSk-unsplash.jpg" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->