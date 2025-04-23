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

$header_top_classes = ( $drile_theme_options['ts_enable_toggle_header_top'] ) ? '' : 'active';
?>
<header class="ts-header <?php echo esc_attr(implode(' ', $header_classes)); ?>">
	<div class="header-container">
		<div class="header-template">
		
			<?php if( $drile_theme_options['ts_header_contact_information'] || $drile_theme_options['ts_header_currency'] || $drile_theme_options['ts_header_language'] || ( function_exists('ts_header_social_icons') && $drile_theme_options['ts_enable_header_social_icons'] ) ): ?>
			
				<div class="header-top <?php echo esc_attr($header_top_classes); ?>">
					<div class="container">
					
						<div class="header-left">
							<?php if( $drile_theme_options['ts_header_contact_information'] ): ?>
								<div class="info-desc"><?php echo do_shortcode(stripslashes($drile_theme_options['ts_header_contact_information'])); ?></div>
							<?php endif; ?>
							
						</div>
						
						<div class="header-right">
						
							<?php ts_header_social_icons(); ?>
						
							<?php if( $drile_theme_options['ts_header_currency'] ): ?>
							<div class="header-currency hidden-phone"><?php drile_woocommerce_multilingual_currency_switcher(); ?></div>
							<?php endif; ?>
							
							<?php if( $drile_theme_options['ts_header_language'] ): ?>
							<div class="header-language hidden-phone"><?php drile_wpml_language_selector(); ?></div>
							<?php endif; ?>
							
						</div>
					
					</div>
				</div>
				
			<?php endif; ?>	
			
			<div class="header-middle header-sticky">
				<div class="container">

					<div class="ts-group-meta-icon-toggle visible-ipad">
						<span class="icon ">
							<span></span>
							<span></span>
							<span></span>
						</span>
					</div>
					
					<div class="menu-wrapper hidden-ipad">
					
						<div class="ts-menu">
							<?php 
								if ( has_nav_menu( 'primary' ) ) {
									wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper','theme_location' => 'primary','walker' => new Drile_Walker_Nav_Menu() ) );
								}
								else{
									wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'main-menu pc-menu ts-mega-menu-wrapper' ) );
								}
							?>
						</div>
						
					</div>
					
					<div class="logo-wrapper logo-center"><?php echo drile_theme_logo(); ?></div>
					
					<div class="header-right">
					
						<?php if( $drile_theme_options['ts_enable_toggle_header_top'] && ( $drile_theme_options['ts_header_contact_information'] || $drile_theme_options['ts_header_currency'] || $drile_theme_options['ts_header_language'] || ( function_exists('ts_header_social_icons') && $drile_theme_options['ts_enable_header_social_icons'] ) ) ): ?>
							<div class="ts-icon-toggle-header-top hidden-ipad">
								<span></span>
								<span></span>
								<span></span>
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
						
						<?php if( $drile_theme_options['ts_enable_tiny_account'] ): ?>
						<div class="my-account-wrapper">
							<?php echo drile_tiny_account(); ?>
						</div>
						<?php endif; ?>
						
						<?php if( $drile_theme_options['ts_enable_search'] ): ?>
						<div class="search-button hidden-ipad">
							<span class="icon"><?php esc_html_e('Search', 'drile'); ?></span>
						</div>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</div>	
	</div>
</header>