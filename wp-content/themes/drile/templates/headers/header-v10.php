<?php
$drile_theme_options = drile_get_theme_options();

$header_classes = array();
if( $drile_theme_options['ts_enable_sticky_header'] ){
	$header_classes[] = 'has-sticky';
}

if( !$drile_theme_options['ts_enable_tiny_shopping_cart'] ){
	$header_classes[] = 'hidden-cart';
}

if( !$drile_theme_options['ts_enable_tiny_wishlist'] || !class_exists('WooCommerce') || !class_exists('YITH_WCWL') ){
	$header_classes[] = 'hidden-wishlist';
}

if( !$drile_theme_options['ts_enable_search'] ){
	$header_classes[] = 'hidden-search';
}

?>
<header class="ts-header <?php echo esc_attr(implode(' ', $header_classes)); ?>">
	<div class="header-container">
		<div class="header-template">
		
			<div class="header-middle header-sticky">
				<div class="container">

					<div class="logo-wrapper"><?php echo drile_theme_logo(); ?></div>
					
					<div class="header-right">
					
						<div class="ts-group-meta-icon-toggle">
							<span class="icon ">
								<span></span>
								<span></span>
								<span></span>
							</span>
						</div>
						
						<?php if( $drile_theme_options['ts_enable_search'] ): ?>
						<div class="search-button hidden-ipad">
							<span class="icon"><?php esc_html_e('Search', 'drile'); ?></span>
						</div>
						<?php endif; ?>
						
						<?php if( $drile_theme_options['ts_enable_tiny_account'] ): ?>
						<div class="my-account-wrapper">
							<?php echo drile_tiny_account(); ?>
						</div>
						<?php endif; ?>	
						
						<?php if( $drile_theme_options['ts_enable_tiny_shopping_cart'] ): ?>
						<div class="shopping-cart-wrapper">
							<?php echo drile_tiny_cart(); ?>
						</div>
						<?php endif; ?>
						
						<?php if( class_exists('YITH_WCWL') && $drile_theme_options['ts_enable_tiny_wishlist'] ): ?>
							<div class="my-wishlist-wrapper"><?php echo drile_tini_wishlist(); ?></div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</div>	
	</div>
</header>