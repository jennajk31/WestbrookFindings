<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php 
	$drile_theme_options = drile_get_theme_options();
	?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<?php if( $drile_theme_options['ts_responsive'] ): ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
	<?php endif; ?>

	<link rel="profile" href="//gmpg.org/xfn/11" />
	<?php 
	drile_theme_favicon();
	wp_head(); 
	?>
</head>
<body <?php body_class(); ?>>
<?php
if( function_exists('wp_body_open') ){
	wp_body_open();
}
?>

<div id="page" class="hfeed site">

	<?php if( !is_page_template('page-templates/blank-page-template.php') ): ?>
		<!-- Page Slider -->
		<?php if( is_page() ): ?>
			<?php if( drile_get_page_options('ts_page_slider') && drile_get_page_options('ts_page_slider_position') == 'before_header' ): ?>
			<div class="top-slideshow">
				<div class="top-slideshow-wrapper">
					<?php drile_show_page_slider(); ?>
				</div>
			</div>
			<?php endif; ?>
		<?php endif; ?>
		
		<!-- Search -->
		<?php if( $drile_theme_options['ts_enable_search'] ): ?>
		
			<?php 
				$enable_search_sidebar = false;
				$search_default_class = $drile_theme_options['ts_search_style'];
			
				if( $drile_theme_options['ts_search_style'] != 'search-default' ){
					$enable_search_sidebar = true;
				}else{
					if( !in_array( $drile_theme_options['ts_header_layout'], array('v1','v3') ) ){
						$enable_search_sidebar = true;
						$search_default_class = 'search-fullscreen';
					}
				}
			?>

			<?php if( $enable_search_sidebar ): ?>
			
				<div id="ts-search-sidebar" class="ts-floating-sidebar <?php echo esc_attr($search_default_class); ?>">
					
					<?php if( $search_default_class != 'search-fullscreen' ): ?>
					<div class="overlay"></div>
					<?php endif; ?>
				
					<div class="ts-sidebar-content">
					
						<span class="close"></span>
						
						<?php if( $search_default_class == 'search-fullscreen' ): ?>
						<div class="overlay"></div>
						<?php endif; ?>
						
						<div class="ts-search-by-category woocommerce">
							<h2 class="title"><?php esc_html_e('Search ', 'drile'); ?></h2>
							<?php get_search_form(); ?>
							<div class="ts-search-result-container"></div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		
		<?php endif; ?>
		
		<!-- Group Header Button -->
		<div id="group-icon-header" class="ts-floating-sidebar">
		
			<div class="ts-sidebar-content">
				
				<?php if( $drile_theme_options['ts_enable_search'] ): ?>
				<div class="ts-search-by-category"><?php get_search_form(); ?></div>
				<?php endif; ?>
				
				<?php if( in_array( $drile_theme_options['ts_header_layout'], array('v2', 'v9', 'v10') ) ): ?>
				
					<div class="main-menu-sidebar-wrapper ts-menu hidden-ipad">
						<div class="main-menu-sidebar">
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
				
				<?php endif; ?>
			
				<div class="mobile-menu-wrapper ts-menu visible-ipad">
					<div class="menu-main-mobile">
						<?php 
						if( has_nav_menu( 'mobile' ) ){
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'mobile', 'walker' => new Drile_Walker_Nav_Menu() ) );
						}else if( has_nav_menu( 'primary' ) ){
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu', 'theme_location' => 'primary', 'walker' => new Drile_Walker_Nav_Menu() ) );
						}
						else{
							wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'mobile-menu' ) );
						}
						?>
					</div>
					
				</div>
				
				<div class="group-button-header">
					
					<?php if( $drile_theme_options['ts_header_currency'] ): ?>
					<div class="header-currency"><h6 class="title"><?php esc_html_e('Currency ', 'drile'); ?></h6><?php drile_woocommerce_multilingual_currency_switcher(); ?></div>
					<?php endif; ?>
					
					<?php if( $drile_theme_options['ts_header_language'] ): ?>
					<div class="header-language"><h6 class="title"><?php esc_html_e('Language ', 'drile'); ?></h6><?php drile_wpml_language_selector(); ?></div>
					<?php endif; ?>											
					
					<?php if( in_array( $drile_theme_options['ts_header_layout'], array('v1', 'v3', 'v4', 'v7') )): ?>
					
						<?php if( $drile_theme_options['ts_header_contact_information'] ): ?>
							<div class="info-desc"><?php echo do_shortcode(stripslashes($drile_theme_options['ts_header_contact_information'])); ?></div>
						<?php endif; ?>
						
						<?php if( function_exists('ts_header_social_icons') && $drile_theme_options['ts_enable_header_social_icons'] ): ?>
							<?php ts_header_social_icons(); ?>
						<?php endif; ?>
					
					<?php endif; ?>
				</div>
				
			</div>
			

		</div>
		
		
		<!-- Shopping Cart Floating Sidebar -->
		<?php if( class_exists('WooCommerce') && $drile_theme_options['ts_enable_tiny_shopping_cart'] && $drile_theme_options['ts_shopping_cart_sidebar'] && !is_cart() && !is_checkout() ): ?>
		<div id="ts-shopping-cart-sidebar" class="ts-floating-sidebar">
			<div class="overlay"></div>
			<div class="ts-sidebar-content">
				<span class="close"></span>
				<div class="ts-tiny-cart-wrapper"></div>
			</div>
		</div>
		<?php endif; ?>
		
		<?php drile_get_header_template(); ?>
		
	<?php endif; ?>
	
	<?php do_action('drile_before_main_content'); ?>

	<div id="main" class="wrapper">