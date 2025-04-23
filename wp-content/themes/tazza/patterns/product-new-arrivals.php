<?php
/**
 * Title: Product New Arrivals
 * Slug: tazza/product-new-arrivals
 * Categories: featured
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"bottom":"5vw","top":"5vw"},"margin":{"top":"0px","bottom":"0px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0px;margin-bottom:0px;padding-top:5vw;padding-bottom:5vw"><!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|60"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"textAlign":"left","level":3,"align":"wide","style":{"typography":{"fontSize":"32px","fontStyle":"normal","fontWeight":"300"},"spacing":{"margin":{"bottom":"0","top":"0"}}}} -->
<h3 class="alignwide has-text-align-left" id="new-arrivals" style="margin-top:0;margin-bottom:0;font-size:32px;font-style:normal;font-weight:300"><?php _e( 'Shop New Arrivals', 'tazza' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"400","textTransform":"uppercase"}},"fontSize":"x-small"} -->
<p class="has-text-align-center has-x-small-font-size" style="font-style:normal;font-weight:400;text-transform:uppercase"><a href="/product-tag/new-arrivals/"><?php _e( 'Shop all new arrivals', 'tazza' ); ?></a></p>
<!-- /wp:paragraph -->
</div><!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0px"}}} -->
<div class="wp-block-group alignwide"><!-- wp:woocommerce/product-new {"rows":1,"alignButtons":true,"contentVisibility":{"image":true,"title":true,"price":true,"rating":false,"button":false},"align":"wide"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->