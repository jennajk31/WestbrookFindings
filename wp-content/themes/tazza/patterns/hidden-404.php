<?php
/**
 * Title: 404 content
 * Slug: tazza/hidden-404
 * Categories: featured
 */
?>

<!-- wp:heading {"style":{"typography":{"fontSize":"clamp(4rem, 40vw, 20rem)","fontWeight":"200","lineHeight":"1"}},"className":"has-text-align-center"} -->
<h2 class="has-text-align-center" style="font-size:clamp(4rem, 40vw, 20rem);font-weight:200;line-height:1"><?php echo esc_html( _x( '404', 'Error code for a webpage that is not found.', 'tazza' ) ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php echo esc_html__( 'This page could not be found. Maybe try a search?', 'tazza' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_html_x( 'Search', 'tazza' ); ?>","showLabel":false,"width":50,"widthUnit":"%","buttonText":"<?php echo esc_html__( 'Search', 'tazza' ); ?>","buttonUseIcon":true,"align":"center"} /-->

<!-- wp:spacer {"height":48} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
