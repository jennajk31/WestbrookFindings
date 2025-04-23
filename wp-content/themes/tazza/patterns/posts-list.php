<?php
/**
 * Title: Posts list
 * Slug: tazza/posts-list
 * Categories: featured
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0px","bottom":"0px"}}},"layout":{"inherit":true}} -->
<div class="wp-block-group alignfull" style="margin-top:0px;margin-bottom:0px">
	<!-- wp:query {"query":{"pages":0,"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"align":"wide","layout":{"inherit":true}} -->
	<div class="wp-block-query alignwide">
		<!-- wp:post-template {"align":"wide"} -->
			<!-- wp:post-title {"isLink":true,"align":"wide","fontSize":"var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"} /-->

			<!-- wp:post-featured-image {"isLink":true,"align":"wide","style":{"spacing":{"margin":{"top":"calc(1.75 * var(--wp--style--block-gap))"}}}} /-->

			<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"bottom":"var(--wp--custom--spacing--small, 6rem)"}}}} -->
			<div class="wp-block-columns alignwide" style="margin-bottom:var(--wp--custom--spacing--small, 6rem)">
				<!-- wp:column {"width":"650px"} -->
				<div class="wp-block-column" style="flex-basis:650px">
					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"isLink":true,"format":"F j, Y","style":{"typography":{"fontStyle":"italic","fontWeight":"400"}},"fontSize":"small"} /-->
				</div>
				<!-- /wp:column -->

				<!-- wp:column {"width":""} -->
				<div class="wp-block-column"></div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:spacer {"height":3} -->
			<div style="height:3px" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:separator {"opacity":"css","align":"wide","className":"is-style-wide"} -->
			<hr class="wp-block-separator alignwide is-style-wide has-css-opacity"/>
			<!-- /wp:separator -->

		<!-- /wp:post-template -->

		<!-- wp:query-pagination {"paginationArrow":"arrow","align":"wide","layout":{"type":"flex","justifyContent":"space-between"}} -->
			<!-- wp:query-pagination-previous {"fontSize":"small"} /-->

			<!-- wp:query-pagination-numbers /-->

			<!-- wp:query-pagination-next {"fontSize":"small"} /-->
		<!-- /wp:query-pagination -->
	</div>
	<!-- /wp:query -->
</div>
<!-- /wp:group -->
