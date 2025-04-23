<?php
if( !isset($data) ){
	$data = drile_get_theme_options();
}

update_option('ts_load_dynamic_style', 0);

$default_options = array(
				'ts_responsive'										=> 1
				,'ts_enable_rtl'									=> 0
				,'ts_layout_fullwidth'								=> 0
				,'ts_enable_search'									=> 1
				,'ts_search_style' 									=> 'search-default'
				,'ts_logo_width'									=> "154"
				,'ts_device_logo_width'								=> "126"
				,'ts_product_rating_style'							=> 'border'
				,'ts_custom_font_ttf'								=> array( 'url' => '' )
		);
		
foreach( $default_options as $option_name => $value ){
	if( isset($data[$option_name]) ){
		$default_options[$option_name] = $data[$option_name];
	}
}

extract($default_options);
		
$default_colors = array(

				'ts_main_content_background_color'							=> "#ffffff"
				,'ts_text_color'											=> "#707070"
				,'ts_text_bold_color'										=> "#202020"
				,'ts_heading_color'											=> "#202020"
				,'ts_primary_color'											=> "#202020"
				,'ts_text_color_in_bg_primary'								=> "#ffffff"
				,'ts_link_color'											=> "#202020"
				,'ts_link_color_hover'										=> "#e2b79f"
				,'ts_tag_background'										=> "#d7d7d7"
				,'ts_tag_color'												=> "#202020"
				,'ts_border_color'											=> "#d7d7d7"
				,'ts_input_border_color'									=> "#a3a3a3"
				
				// BUTTON
				,'ts_button_background_color'								=> "#202020"
				,'ts_button_text_color'										=> "#ffffff"
				,'ts_button_hover_background_color'							=> "#ffffff"
				,'ts_button_hover_text_color'								=> "#202020"
				
				// HEADER
				,'ts_top_header_background_color'							=> "#ffffff"
				,'ts_top_header_text_color'									=> "#202020"
				,'ts_top_header_border_color'								=> "#e9e9e9"
				,'ts_middle_header_background_color'						=> "#ffffff"
				,'ts_middle_header_text_color'								=> "#202020"
				,'ts_middle_header_border_color'							=> "#e9e9e9"
				,'ts_mobile_header_icon_color'								=> "#ffffff"
				
				// BREADCRUMB
				,'ts_breadcrumb_text_color'									=> "#202020"
				,'ts_breadcrumb_heading_color'								=> "#202020"
				,'ts_breadcrumb_link_color'									=> "#707070"
				
				// MENU
				,'ts_menu_text_color'										=> "#707070"
				,'ts_sub_menu_text_color'									=> "#707070"
				,'ts_sub_menu_heading_color'								=> "#202020"
				,'ts_sidebar_menu_color'									=> "#ffffff"
				,'ts_sidebar_menu_background_color'							=> "#202020"
				
				// FOOTER
				,'ts_footer_background_color'								=> "#ffffff"
				,'ts_footer_text_color'										=> "#707070"
				,'ts_footer_text_hover_color'								=> "#202020"
				,'ts_footer_end_background_color'							=> "#ffffff"
				,'ts_footer_end_text_color'									=> "#707070"
				,'ts_footer_end_text_hover_color'							=> "#202020"

				// PRODUCT
				,'ts_product_del_color'										=> "#202020"
				,'ts_product_button_thumbnail_color'						=> "#202020"
				,'ts_product_button_thumbnail_background_color'				=> "#ffffff"
				,'ts_product_button_thumbnail_hover_color'					=> "#ffffff"
				,'ts_product_button_thumbnail_hover_background_color'		=> "#202020"
				,'ts_product_button_device_color'							=> "#202020"
				,'ts_product_button_thumbnail_tooltip_color'				=> "#202020"
				,'ts_product_button_thumbnail_tooltip_background_color'		=> "#ffffff"
				,'ts_rating_color'											=> "#848484"
				,'ts_rating_fill_color'										=> "#202020"
				,'ts_product_sale_label_text_color'							=> "#ffffff"
				,'ts_product_sale_label_background_color'					=> "#9e0b0f"
				,'ts_product_new_label_text_color'							=> "#ffffff"
				,'ts_product_new_label_background_color'					=> "#202020"
				,'ts_product_feature_label_text_color'						=> "#ffffff"
				,'ts_product_feature_label_background_color'				=> "#f7941d"
				,'ts_product_outstock_label_text_color'						=> "#ffffff"
				,'ts_product_outstock_label_background_color'				=> "#989898"
				,'ts_add_to_cart_message_background'						=> "#42924d"
				,'ts_add_to_cart_message_color'								=> "#f3f3f3"
				,'ts_add_to_cart_message_error_background'					=> "#e5534c"
				,'ts_add_to_cart_message_error_color'						=> "#f3f3f3"
				
);

$data = apply_filters('drile_custom_style_data', $data);

foreach( $default_colors as $option_name => $default_color ){
	if( isset($data[$option_name]['rgba']) ){
		$default_colors[$option_name] = $data[$option_name]['rgba'];
	}
	else if( isset($data[$option_name]['color']) ){
		$default_colors[$option_name] = $data[$option_name]['color'];
	}
}

extract( $default_colors );

/* Parse font option. Ex: if option name is ts_body_font, we will have variables below:
* ts_body_font (font-family)
* ts_body_font_weight
* ts_body_font_style
* ts_body_font_size
* ts_body_font_line_height
* ts_body_font_letter_spacing
*/
$font_option_names = array(
							'ts_body_font',
							'ts_body_font_bold',
							'ts_heading_font',
							'ts_heading_font_thin',
							'ts_menu_special_font',
							'ts_menu_font',
							'ts_sub_menu_font',
							'ts_sidebar_menu_font',
							);
$font_size_option_names = array( 
							'ts_h1_font', 
							'ts_h2_font', 
							'ts_h3_font', 
							'ts_h4_font', 
							'ts_h5_font', 
							'ts_h6_font',
							'ts_button_font',
							'ts_heading_spacing',
							'ts_h1_ipad_font', 
							'ts_h2_ipad_font', 
							'ts_h3_ipad_font', 
							'ts_h4_ipad_font',
							'ts_h5_ipad_font',
							'ts_h6_ipad_font',
							'ts_button_ipad_font',
							);
$font_option_names = array_merge($font_option_names, $font_size_option_names);
foreach( $font_option_names as $option_name ){
	$default = array(
		$option_name 						=> 'inherit'
		,$option_name . '_weight' 			=> 'normal'
		,$option_name . '_style' 			=> 'normal'
		,$option_name . '_size' 			=> 'inherit'
		,$option_name . '_line_height' 		=> 'inherit'
		,$option_name . '_letter_spacing' 	=> 'inherit'
	);
	if( is_array($data[$option_name]) ){
		if( !empty($data[$option_name]['font-family']) ){
			$default[$option_name] = $data[$option_name]['font-family'];
		}
		if( !empty($data[$option_name]['font-weight']) ){
			$default[$option_name . '_weight'] = $data[$option_name]['font-weight'];
		}
		if( !empty($data[$option_name]['font-style']) ){
			$default[$option_name . '_style'] = $data[$option_name]['font-style'];
		}
		if( !empty($data[$option_name]['font-size']) ){
			$default[$option_name . '_size'] = $data[$option_name]['font-size'];
		}
		if( !empty($data[$option_name]['line-height']) ){
			$default[$option_name . '_line_height'] = $data[$option_name]['line-height'];
		}
		if( !empty($data[$option_name]['letter-spacing']) ){
			$default[$option_name . '_letter_spacing'] = $data[$option_name]['letter-spacing'];
		}
	}
	extract( $default );
}
?>	
	
	/*
	1. CUSTOM FONT FAMILY
	2. CUSTOM FONT SIZE
	3. CUSTOM COLOR
	*/
	header .logo img,
	header .logo-header img{
		width: <?php echo absint($ts_logo_width); ?>px;
	}
	header .logo-wrapper{
		width: <?php echo absint($ts_logo_width) + 60; ?>px;
	}
	.sticky-wrapper.is-sticky .logo img,
	.sticky-wrapper.is-sticky .logo-header img{
		width: <?php echo absint($ts_device_logo_width); ?>px;
	}
	@media only screen and (max-width: 1279px){
		header .logo img,
		header .logo-header img{
			width: <?php echo absint($ts_device_logo_width); ?>px;
		}
		header .logo-wrapper{
			width: <?php echo absint($ts_device_logo_width) + 40; ?>px;
		}
	}
	@media only screen and (max-width: 767px){
		header .logo-wrapper{
			width: auto;
		}
	}
	
	<?php if( isset($ts_custom_font_ttf) && $ts_custom_font_ttf['url'] ):?>
	/*** Custom Font ***/
	@font-face {
		font-family: 'CustomFont';
		src:url('<?php echo esc_url($ts_custom_font_ttf['url']); ?>') format('truetype');
		font-weight: normal;
		font-style: normal;
	}
	<?php endif; ?>
	
	<?php if( isset($ts_product_rating_style) && $ts_product_rating_style == 'fill' ): ?>
	
	/*** Star Rating Style Fill ***/
	.woocommerce .star-rating span:before, 
	.woocommerce .star-rating:before, 
	.woocommerce p.stars a::before, 
	.ts-testimonial-wrapper .rating:before, 
	.ts-testimonial-wrapper .rating span:before, 
	blockquote .rating:before, blockquote .rating span:before{
		font-family: "Font Awesome 5 Free";
		font-weight: 400;
		font-size: 13px;
		letter-spacing: 1px;
	}
	.woocommerce p.stars a::before,
	.woocommerce .woocommerce-product-rating .star-rating:before,
	.woocommerce .woocommerce-product-rating .star-rating span:before{
		font-size: 20px;
		letter-spacing: 1px;
	}
	.woocommerce .star-rating span:before,
	.woocommerce .star-rating:before,
	.woocommerce p.stars a::before,
	.ts-testimonial-wrapper .rating:before, 
	.ts-testimonial-wrapper .rating span:before,
	blockquote .rating:before, 
	blockquote .rating span:before{
		content: "\f005\f005\f005\f005\f005";
	}
	.woocommerce p.stars a::before,
	.woocommerce p.stars:hover a::before,
	.woocommerce p.stars a:hover~a::before,
	.woocommerce p.stars.selected a.active::before,
	.woocommerce p.stars.selected a.active~a::before,
	.woocommerce p.stars.selected a:not(.active)::before{
		content: "\f005";
	}
	.woocommerce .star-rating span:before,
	.woocommerce p.stars:hover a::before,
	.woocommerce p.stars.selected a::before,
	.ts-testimonial-wrapper .rating span:before,
	blockquote .rating span:before{
		font-weight: 900;
	}
	.woocommerce p.stars a:hover~a::before,
	.woocommerce p.stars.selected a.active~a::before{
		font-weight: 400;
	}
	<?php endif; ?>
	
	/*------------------------------------------------------
		1. CUSTOM FONT FAMILY
	-------------------------------------------------------*/
	html,
	body,
	label,
	input,
	textarea,
	keygen,
	select,
	button,
	form table label,
	.ts-button.fa,
	li.fa,
	.font-body,
	/*** Blog ***/
	.avatar-name a,
	.breadcrumb-title-wrapper .breadcrumbs,
	article.single-post .entry-format blockquote,
	/*** Product ***/
	.product-name,
	.woocommerce h3.product-name,
	.woocommerce ul.cart_list h3.product-name a,
	.product-group-button .button-tooltip,
	.woocommerce ul.product_list_widget li a,
	.column-tabs ul.tabs li span.count,
	.widget_shopping_cart_content p.total strong,
	.woocommerce div.product .entry-title,
	.woocommerce span.onsale,
	.woocommerce div.product .images .product-label span.onsale, 
	.woocommerce div.product .images .product-label span.new, 
	.woocommerce div.product .images .product-label span.featured, 
	.woocommerce div.product .images .product-label span.out-of-stock, 
	.woocommerce div.product .images .product-label span,
	.woocommerce .product .product-label .onsale, 
	.woocommerce .product .product-label .new, 
	.woocommerce .product .product-label .featured, 
	.woocommerce .product .product-label .out-of-stock,
	.woocommerce div.product .woocommerce-tabs ul.tabs li a,
	/*** Menu ***/
	.menu-wrapper nav > ul.menu li .menu-desc,
	body.header-v9 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li:after,
	body.header-v10 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li:after,
	body.header-v2 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li:after,
	/*** Cart/checkout ***/
	.cart_totals table th,
	.widget_display_stats > dl dt,
	.rating-wrapper strong.rating,
	#ship-to-different-address label,
	.woocommerce-checkout .checkout .create-account label,
	.woocommerce #order_review table.shop_table tfoot td,
	.woocommerce table.shop_table_responsive tr td:before, 
	.woocommerce-page table.shop_table_responsive tr td:before,
	/*** Shortcode/Widget/Visual ***/
	.mc4wp-form-fields label,
	.ts-milestone h3.subject,
	.ts-twitter-slider.twitter-content h4.name > a,
	.vc_tta-accordion .vc_tta-panel .vc_tta-panel-title,
	body.wpb-js-composer .vc_tta-accordion .vc_tta-panel .vc_tta-panel-title > a,
	body.wpb-js-composer .vc_general.vc_tta-tabs .vc_tta-tab > a,
	body.wpb-js-composer .vc_tta.vc_general .vc_tta-panel-title .vc_tta-controls-icon.vc_tta-controls-icon-plus:before,
	body .theme-default .nivo-caption,
	.comment_list_widget blockquote.comment-body,
	.ts-banner.style-button-show h3,
	.ts-banner.has-description .box-content header h6,
	/*** Plugin ***/	
	body #cboxClose,
	body .dataTables_wrapper,
	body .compare-list,
	body .pp_nav .currentTextHolder,
	ul.wishlist_table .item-details .product-name h3,
	/*** Dokan ***/
	.dokan-category-menu .sub-block h3{
		font-family: <?php echo esc_html($ts_body_font); ?>;
		font-style: <?php echo esc_html($ts_body_font_style); ?>;
		font-weight: <?php echo esc_html($ts_body_font_weight); ?>;
		letter-spacing: <?php echo esc_html($ts_body_font_letter_spacing); ?>;
	}
	.woocommerce div.product p.price ins, 
	.woocommerce div.product span.price ins{
		font-weight: <?php echo esc_html($ts_body_font_weight); ?>;
	}
	.header-currency > .title, 
	.header-language > .title,
	.group-button-header h6.title{
		font-family: <?php echo esc_html($ts_body_font); ?>;
		font-style: <?php echo esc_html($ts_body_font_style); ?>;
		letter-spacing: <?php echo esc_html($ts_body_font_letter_spacing); ?>;
	}
	strong,
	b,
	dt,
	table th,
	body blockquote,
	.semibold,
	.ts-heading.semibold .heading,
	/*** Blog ***/
	.cats-link > a,
	.author > a,
	.comment-meta .edit,
	.comment-meta .reply,
	.single-navigation-1,
	.single-navigation-2,
	.single-portfolio .portfolio-info > span:first-child,
	/*** Product ***/
	.filter-widget-area-button a,
	.woocommerce .product .product-label .onsale,
	.woocommerce .product .product-label .new,
	.woocommerce .product .product-label .featured,
	.woocommerce .product .product-label .out-of-stock,
	div.product .single-navigation > a > span,
	.woocommerce div.product form.cart .reset_variations,
	.woocommerce div.product form.cart .variations label,
	/*** Cart/checkout ***/
	.cart-collaterals .cart_totals table.shop_table tr.cart-subtotal td, 
	.cart-collaterals .cart_totals table.shop_table tr.order-total td,
	.woocommerce table.shop_attributes th,
	.comment-respond #reply-title,
	#order_review table .product-total .amount,
	.woocommerce-checkout-review-order .cart-subtotal .woocommerce-Price-amount,
	.woocommerce table.woocommerce-table--order-details tfoot th,
	.woocommerce table.woocommerce-table--order-details tfoot td,
	/*** Shortcode/Widget/Visual ***/
	.ts-banner h6,
	.list-categories ul.tabs li span.category-name,
	.ts-product-in-product-type-tab-wrapper ul.tabs li,
	.counter-wrapper > div .number,
	.ts-feature-wrapper.vertical-icon .feature-title,
	.ts-feature-wrapper.vertical-image .feature-title,
	body.wpb-js-composer .vc_toggle_default .vc_toggle_title h4,
	#tab-seller .store-name span:first-child,
	#tab-seller .store-address span:first-child,
	#tab-seller .seller-name span:first-child,
	.header-v8 .ts-menu nav .widgettitle,
	.header-v8 .widget_nav_menu .widgettitle,
	.header-v8 .list-link .widgettitle,
	.header-v8 .ts-header .menu-wrapper ul > li > a:hover,
	.header-v8 .menu-wrapper nav > ul.menu > li.current-menu-item > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current_page_parent > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current-menu-parent > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current_page_item > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current-menu-ancestor > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current-page-ancestor > a, 
	.header-v8 .menu-wrapper nav > ul.menu > li.current-product_cat-ancestor > a,
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li > a:hover,
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current-menu-item > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current_page_parent > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current-menu-parent > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current_page_item > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current-menu-ancestor > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current-page-ancestor > a, 
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li.current-product_cat-ancestor > a,
	.header-v8 .ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a:hover{
		font-family: <?php echo esc_html($ts_body_font_bold); ?>;
		font-weight: <?php echo esc_html($ts_body_font_bold_weight); ?>;
	}
	@media screen and (min-width: 1279px){
		.header-v8 .menu-wrapper nav > ul.menu > li > a:hover,
		.header-v8 .menu-wrapper nav > ul > li > a:hover,
		.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li > a:hover,
		.header-v8 .menu-wrapper nav div.list-link li > a:hover,
		.header-v8 .menu-wrapper nav > ul.menu li.widget_nav_menu li > a:hover,		
		.header-v8 .widget_nav_menu .menu > li > a:hover,
		.header-v8 .menu div.list-link li > a:hover,
		.header-v8 .ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a:hover,
		.header-v8 .ts-menu .group-meta a:hover,
		.header-v8 .ts-menu .group-meta ul li a:hover{
			font-family: <?php echo esc_html($ts_body_font_bold); ?>;
			font-weight: <?php echo esc_html($ts_body_font_bold_weight); ?>;
		}
	}
	h1, h2, h3, h4, h5, h6, 
	.h1, .h2, .h3, .h4, .h5, .h6, 
	.ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a,
	.woocommerce > form > fieldset legend,
	.woocommerce-cart .cart-collaterals .cart_totals > h2,
	.ts-active-filters .widget_layered_nav_filters .widgettitle,
	.woocommerce-cart .cart-count strong,
	.ts-testimonial-wrapper.style-horizontal .author a,
	.search-no-results .blog-template .alert,
	body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-left .vc_tta-tab a,
	body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-right .vc_tta-tab a{
		font-family: <?php echo esc_html($ts_heading_font); ?>;
		font-style: <?php echo esc_html($ts_heading_font_style); ?>;
		font-weight: <?php echo esc_html($ts_heading_font_weight); ?>;
	}
	.font-thin,
	.ts-heading.font-thin .heading{
		font-family: <?php echo esc_html($ts_heading_font_thin); ?>;
		font-weight: <?php echo esc_html($ts_heading_font_thin_weight); ?>;
	}
	.ts-menu ul li a{
		font-family: <?php echo esc_html($ts_menu_font); ?>;
		font-style: <?php echo esc_html($ts_menu_font_style); ?>;
		font-weight: <?php echo esc_html($ts_menu_font_weight); ?>;
		letter-spacing: <?php echo esc_html($ts_menu_font_letter_spacing); ?>;
	}
	.ts-menu ul .sub-menu li a,
	.ts-menu div.list-link li a,
	.ts-menu .widget_nav_menu .menu li a{
		font-family: <?php echo esc_html($ts_sub_menu_font); ?>;
		font-style: <?php echo esc_html($ts_sub_menu_font_style); ?>;
		font-weight: <?php echo esc_html($ts_sub_menu_font_weight); ?>;
		letter-spacing: <?php echo esc_html($ts_sub_menu_font_letter_spacing); ?>;
	}
	.ts-menu .widgettitle,
	.ts-menu .widget_nav_menu .widgettitle,
	.ts-menu .list-link .widgettitle{
		font-family: <?php echo esc_html($ts_sub_menu_font); ?>;
		font-style: <?php echo esc_html($ts_sub_menu_font_style); ?>;
		letter-spacing: <?php echo esc_html($ts_sub_menu_font_letter_spacing); ?>;
	}
	.special-font,
	.special-font h1, .special-font .h1,
	.special-font h2, .special-font .h2,
	.special-font h3, .special-font .h3,
	.special-font h4, .special-font .h4,
	.special-font h5, .special-font .h5,
	body.header-v9 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li > a,
	body.header-v10 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li > a,
	body.header-v2 .ts-floating-sidebar .main-menu-sidebar-wrapper nav > ul > li > a{
		font-family: <?php echo esc_html($ts_menu_special_font); ?>;
		font-size: <?php echo esc_html($ts_menu_special_font_size); ?>;
		font-weight: <?php echo esc_html($ts_menu_special_font_weight); ?>;
		font-style: <?php echo esc_html($ts_menu_special_font_style); ?>;
		letter-spacing: <?php echo esc_html($ts_menu_special_font_letter_spacing); ?>;
	}
	
	/*------------------------------------------------------
		2. CUSTOM FONT SIZE
	-------------------------------------------------------*/
	html,
	body,
	keygen,
	select option,
	h3 > label,
	/*** Blog ***/
	.comment-text,
	.comment_list_widget .comment-body,
	.post_list_widget .excerpt,
	.single-portfolio .single-navigation > div a,
	.post_list_widget blockquote,
	article.single-post .entry-format blockquote,
	#ts-search-result-container ul li a,
	#ts-search-result-container .view-all-wrapper a,
	.rating-wrapper strong.rating,
	.woocommerce #reviews #comments ol.commentlist li .comment-text p.meta,
	#comment-wrapper .heading-wrapper small,
	/*** Product ***/
	.product-name,
	.product-title,
	.products .product .product-name,
	.products .product .price,
	.woocommerce div.product p.price,
	.woocommerce div.product span.price,
	.products .product .product-sku,
	.products .product .product-brands,
	.products .product .product-categories,
	.products .product .short-description,
	.single-navigation .product-info > div > span:first-child, 
	ul.wishlist_table .item-details .product-name h3
	.single-navigation .product-info > div > span:first-child,
	.woocommerce div.product .woocommerce-tabs .panel,
	/*** Cart/checkout ***/	
	.woocommerce .order_details li,
	table.woocommerce-checkout-review-order-table th,
	.woocommerce-cart .cart-collaterals .cart_totals > h2,
	/*** Shortcode/Widget/Visual ***/
	.shopping-cart-wrapper .ts-tiny-cart-wrapper,
	.ts-tiny-cart-wrapper .form-content > label,
	.ts-tiny-account-wrapper .dropdown-container,
	.mc4wp-form-fields label,
	.mailchimp-subscription .mc4wp-alert,
	.vc_progress_bar .vc_single_bar .vc_label,
	body.wpb-js-composer .vc_tta.vc_general,
	.ts-team-members .team-info,
	.ts-feature-wrapper.horizontal-icon .feature-title,
	.ts-feature-wrapper.horizontal-image .feature-title,
	.group-button-header h6.title,
	.ts-banner.has-description .box-content header h6,
	/*** Plugin ***/
	body table.compare-list,
	.woocommerce table.wishlist_table,
	ul.wishlist_table .item-details .product-name h3,
	body div.wishlist-title h2,
	.yith-wcwl-share h4.yith-wcwl-share-title,
	body .wpml-ls-legacy-list-vertical a,
	body .wpml-ls-legacy-list-horizontal ul li a{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
		line-height: <?php echo esc_html($ts_body_font_line_height); ?>;
	}
	input,
	textarea,
	select,
	body .select2-container--default .select2-selection--single .select2-selection__rendered,
	.woocommerce form .form-row input.input-text,
	.woocommerce form .form-row textarea,
	#add_payment_method table.cart td.actions .coupon .input-text,
	.woocommerce-cart table.cart td.actions .coupon .input-text,
	.woocommerce-checkout table.cart td.actions .coupon .input-text,
	.ts-pagination ul li a.prev:before,
	.ts-pagination ul li a.next:before,
	.woocommerce nav.woocommerce-pagination ul li a.next:before,
	.woocommerce nav.woocommerce-pagination ul li a.prev:before,
	.woocommerce table.my_account_orders td,
	.woocommerce table.shop_table.my_account_orders,
	.vc_toggle_default .vc_toggle_title h4,
	header .wpml-ls-legacy-dropdown a.wpml-ls-item-toggle span,
	header .wpml-ls-legacy-dropdown-click a.wpml-ls-item-toggle span,
	.ts-active-filters .widget_layered_nav_filters .widgettitle{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
	}
	::-webkit-input-placeholder{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
	}
	:-moz-placeholder{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
	}
	::-moz-placeholder{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
	}
	:-ms-input-placeholder{
		font-size: <?php echo esc_html($ts_body_font_size); ?>;
	}
	.tagcloud a,
	.tags-link a{
		font-size: <?php echo esc_html($ts_body_font_size); ?> !important;
	}
	/*** MENU ***/
	.menu-wrapper nav > ul.menu > li > a,
	.menu-wrapper nav > ul.menu > li:before,
	.mobile-menu-wrapper .mobile-menu > ul > li > a,
	.mobile-menu-wrapper nav > ul > li:before,
	.mobile-menu span.ts-menu-drop-icon{
		font-size: <?php echo esc_html($ts_menu_font_size); ?>;
	}
	.menu-wrapper nav > ul.menu ul.sub-menu > li > a,
	.menu-wrapper nav > ul.menu ul.sub-menu > li:before,
	.menu-wrapper nav div.list-link li > a,
	.menu-wrapper nav > ul.menu li.widget_nav_menu li > a,
	.ts-menu nav .widgettitle,
	.widget_nav_menu .widgettitle,
	.list-link .widgettitle,
	.widget_nav_menu .menu > li > a,
	.menu div.list-link li > a,
	ul li .ts-megamenu-container{
		font-size: <?php echo esc_html($ts_sub_menu_font_size); ?>;		
	}
	.header-v8 .menu-wrapper nav > ul.menu > li > a,
	.header-v8 .menu-wrapper nav > ul > li > a,
	.header-v8 .menu-wrapper nav > ul.menu ul.sub-menu > li > a,
	.header-v8 .menu-wrapper nav div.list-link li > a,
	.header-v8 .menu-wrapper nav > ul.menu li.widget_nav_menu li > a,		
	.header-v8 .widget_nav_menu .menu > li > a,
	.header-v8 .menu div.list-link li > a,
	.header-v8 ul li .ts-megamenu-container,
	.header-v8 .ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a{
		font-family: <?php echo esc_html($ts_sidebar_menu_font); ?>;
		font-weight: <?php echo esc_html($ts_sidebar_menu_font_weight); ?>;
		font-size: <?php echo esc_html($ts_sidebar_menu_font_size); ?>;
	}
	.header-v8 .ts-menu .group-meta ul li a,
	.header-v8 .ts-menu .group-meta{
		font-family: <?php echo esc_html($ts_sidebar_menu_font); ?>;
		font-weight: <?php echo esc_html($ts_sidebar_menu_font_weight); ?>;
	}
	/*** HEADING ***/
	h1,
	.h1,
	.ts-megamenu-static-html-container > .tab-heading > span:last-child{
		font-size: <?php echo esc_html($ts_h1_font_size); ?>;
		line-height: <?php echo esc_html($ts_h1_font_line_height); ?>;
	}
	h2,
	.h2,
	.breadcrumb-title-wrapper .breadcrumb-title h1,
	.woocommerce div.product .entry-title,
	.single .entry-title-left header .entry-title,
	.ts-home-tabs > .tab-items > li > a,
	.header-v8 .ts-menu nav .ts-megamenu-fullwidth .ts-megamenu-widgets-container .widgettitle,
	.header-v8 .ts-megamenu-fullwidth .ts-megamenu-widgets-container .widget_nav_menu .widgettitle,
	.header-v8 .ts-megamenu-fullwidth .ts-megamenu-widgets-container .list-link .widgettitle{
		font-size: <?php echo esc_html($ts_h2_font_size); ?>;
		line-height: <?php echo esc_html($ts_h2_font_line_height); ?>;
	}
	h3,
	.h3,
	.search-fullwidth .ts-sidebar-content h2,
	.search-fullscreen .ts-sidebar-content h2,
	.breadcrumb-title-wrapper.breadcrumb-v1 .breadcrumb-title h1,
	.theme-title .heading-title, 
	.comments-title .heading-title, 
	#comment-wrapper .heading-title,
	.ts-shortcode .heading-tab .heading-title,
	footer .widget .widgettitle, 
	footer .widget-title,
	.ts-banner.has-description .box-content header h2,
	.vc_col-sm-12 .ts-mailchimp-subscription-shortcode .widget-title,
	#main > .ts-product-category-wrapper .product-category .category-name h3,
	.vc_row[data-vc-stretch-content] .product-category .category-name h3,
	.ts-product-in-product-type-tab-wrapper.show-nav-as-text .column-tabs ul.tabs li,
	.ts-shortcode .shortcode-heading-wrapper h2{
		font-size: <?php echo esc_html($ts_h3_font_size); ?>;
		line-height: <?php echo esc_html($ts_h3_font_line_height); ?>;
	}
	h4,
	.h4,
	.woocommerce .cross-sells > h2, 
	.woocommerce .up-sells > h2, 
	.woocommerce .related > h2,
	.cart-collaterals .cart_totals > h2,
	.ts-shortcode.nav-top .owl-nav > div:before,
	.product-category .category-name h3,
	.ts-portfolio-wrapper.ts-shortcode .shortcode-heading-wrapper > h2,
	body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-left .vc_tta-tab a,
	body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-right .vc_tta-tab a{
		font-size: <?php echo esc_html($ts_h4_font_size); ?>;
		line-height: <?php echo esc_html($ts_h4_font_line_height); ?>;
	}
	h5,
	.h5,
	#customer_login h2,
	.dropdown-container .cart-number,
	.woocommerce div.product .summary > .price,
	div.product .summary > .ts-variation-price .amount,
	.woocommerce div.product .single_variation_wrap .woocommerce-variation-price .price,
	.woocommerce-account .addresses .title h3, 
	.woocommerce-account .addresses h2, 
	.woocommerce-customer-details .addresses h2,
	.woocommerce div.product .woocommerce-tabs ul.tabs li > a,
	.ts-sidebar-content h2,
	body h4.wpb_pie_chart_heading,
	h3.wpb_heading,
	.ts-feature-wrapper .feature-title,
	.ts-product-category-banner-wrapper .category-item h3,
	.search-no-results .blog-template .alert,
	.mc4wp-form-fields > h2.title,
	html body > h1{
		font-size: <?php echo esc_html($ts_h5_font_size); ?>;
		line-height: <?php echo esc_html($ts_h5_font_line_height); ?>;
	}
	h6,.h6,
	.widget-title,
	.ts-products-widget > h2,
	.woocommerce div.product form.cart .variations label,
	.woocommerce div.product.tabs-in-summary .woocommerce-tabs ul.tabs li > a,
	#order_review_heading,
	.woocommerce-account .page-container div.woocommerce > h2,
	.account-content h2,
	.woocommerce-MyAccount-content > h2,
	.woocommerce-customer-details > h2,
	.woocommerce-order-details > h2,
	.woocommerce-billing-fields > h3,
	.woocommerce-additional-fields > h3,
	header.woocommerce-Address-title > h3,
	.ts-portfolio-wrapper .filter-bar li,
	.ts-blogs-wrapper .blogs article.quote blockquote,
	.ts-team-members h3,
	.widget-container .post_list_widget header h5,
	.single-portfolio .single-navigation > div a:first-child,
	.style-verticle .list-categories ul.tabs li span.category-name,
	.ts-product-brand-wrapper .meta-wrapper .heading-title,
	body.wpb-js-composer .vc_tta.vc_general .vc_tta-panel-title,
	body.wpb-js-composer .vc_toggle .vc_toggle_title h4{
		font-size: <?php echo esc_html($ts_h6_font_size); ?>;
		line-height: <?php echo esc_html($ts_h6_font_line_height); ?>;
	}
	h1,.h1,
	h2,.h2,
	h3,.h3,
	.theme-title .heading-title, 
	.comments-title .heading-title, 
	#comment-wrapper .heading-title{
		letter-spacing: <?php echo esc_html($ts_heading_spacing_letter_spacing); ?>;
	}
	h4,.h4,
	h5,.h5,
	h6,.h6,
	.clear-spacing,
	.ts-heading.clear-spacing .heading,
	.ts-team-members h3,
	.woocommerce-cart .cart-collaterals .cart_totals > h2,
	body div.wishlist-title h2,
	.ts-active-filters .widget_layered_nav_filters .widgettitle,
	.woocommerce-cart .cart-count strong{
		letter-spacing: <?php echo esc_html($ts_body_font_letter_spacing); ?>;
	}
	/*** BUTTON ***/
	a.ts-button,
	a.button,
	.ts-banner-button a,
	a.button-readmore,
	button, 
	input[type^="submit"],
	.woocommerce a.button, 
	.woocommerce button.button, 
	.woocommerce input.button,  
	.woocommerce a.button.disabled, 
	.woocommerce a.button:disabled, 
	.woocommerce a.button:disabled[disabled], 
	.woocommerce button.button.disabled, 
	.woocommerce button.button:disabled, 
	.woocommerce button.button:disabled[disabled], 
	.woocommerce input.button.disabled, 
	.woocommerce input.button:disabled, 
	.woocommerce input.button:disabled[disabled],
	.woocommerce #respond input#submit,
	.woocommerce a.button.alt, 
	.woocommerce button.button.alt, 
	.woocommerce input.button.alt,  
	.woocommerce a.button.alt.disabled, 
	.woocommerce a.button.alt:disabled, 
	.woocommerce a.button.alt:disabled[disabled], 
	.woocommerce button.button.alt.disabled, 
	.woocommerce button.button.alt:disabled, 
	.woocommerce button.button.alt:disabled[disabled], 
	.woocommerce input.button.alt.disabled, 
	.woocommerce input.button.alt:disabled, 
	.woocommerce input.button.alt:disabled[disabled],
	.woocommerce a.button.added:before,
	.woocommerce button.button.added:before,
	.woocommerce input.button.added:before,
	.more-less-buttons a,
	.ts-shop-load-more .load-more,
	.ts-shortcode .load-more-wrapper .load-more,
	.filter-widget-area .widget-title,
	.woocommerce .woocommerce-ordering ul.orderby .orderby-current,
	.filter-widget-area-button a,
	.shopping-cart p.buttons a,
	html body.woocommerce table.compare-list tr.add-to-cart td a:before,
	html body table.compare-list tr.add-to-cart td a:before,
	#ts-search-sidebar .ts-search-result-container .view-all-wrapper a,
	.ts-milestone h3.subject,
	body table.compare-list .add-to-cart td a,
	body .yith-woocompare-widget a.compare,
	body table.compare-list .add-to-cart td a:not(.unstyled_button),
	.woocommerce .hidden-title-form a.hide-title-form,
	.woocommerce-account .woocommerce-MyAccount-navigation li,
	/* Dokan */
	input[type="submit"].dokan-btn,
	a.dokan-btn,
	.dokan-btn,
	body .product-edit-new-container .dokan-btn-lg{
		font-size: <?php echo esc_html($ts_button_font_size); ?>;
		line-height: <?php echo esc_html($ts_button_font_line_height); ?>;
		letter-spacing: <?php echo esc_html($ts_button_font_letter_spacing); ?>;
	}
	input,
	select,
	body .select2-container--default .select2-selection--single .select2-selection__rendered,
	textarea,
	select,
	html input[type^="search"],
	html input[type^="text"], 
	html input[type^="email"],
	html input[type^="password"],
	html input[type^="number"],
	html input[type^="tel"],
	.woocommerce .woocommerce-result-count,
	.woocommerce .woocommerce-ordering .orderby li,
	.product-per-page-form ul.perpage li,
	.zoom-in-out-button a,
	.woocommerce form .form-row input.input-text,
	.woocommerce form .form-row textarea,
	#add_payment_method table.cart td.actions .coupon .input-text,
	.woocommerce-cart table.cart td.actions .coupon .input-text,
	.woocommerce-checkout table.cart td.actions .coupon .input-text,
	.chosen-container a.chosen-single,
	.woocommerce-checkout .form-row .chosen-container-single .chosen-single,
	#add_payment_method table.cart td.actions .coupon .input-text, 
	.woocommerce-cart table.cart td.actions .coupon .input-text, 
	.woocommerce-checkout table.cart td.actions .coupon .input-text, 
	.woocommerce-page table.cart td.actions .coupon .input-text,
	body .select2-container--default .select2-selection--single .select2-selection__rendered,
	.ts-mailchimp-subscription-shortcode.style-2 .vertical-button-icon .subscribe-email .button {
		line-height: <?php echo esc_html($ts_button_font_line_height); ?> !important;
	}
	textarea::-webkit-input-placeholder{ /* WebKit browsers */
		line-height: <?php echo esc_html($ts_button_font_line_height); ?> !important;
	}
	textarea:-moz-placeholder{ /* Mozilla Firefox 4 to 18 */
		line-height: <?php echo esc_html($ts_button_font_line_height); ?> !important;
	}
	textarea::-moz-placeholder{ /* Mozilla Firefox 19+ */
		line-height: <?php echo esc_html($ts_button_font_line_height); ?> !important;
	}
	textarea:-ms-input-placeholder{ /* Internet Explorer 10+ */
		line-height: <?php echo esc_html($ts_button_font_line_height); ?> !important;
	}
	@media screen and (min-width: 1400px){
		.ts-heading.style-multiple-heading .heading{
			font-size: <?php echo esc_html($ts_h1_font_size); ?>;
			line-height: <?php echo esc_html($ts_h1_font_line_height); ?>;
		}
	}
	@media screen and (max-width: 1279px){
		h1,
		.h1,
		.ts-heading.style-multiple-heading h2,
		.ts-megamenu-static-html-container > .tab-heading > span:last-child{
			font-size: <?php echo esc_html($ts_h1_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h1_ipad_font_line_height); ?>;
		}
		h2,
		.h2,
		.breadcrumb-title-wrapper.breadcrumb-v3 .breadcrumb-title h1,
		.woocommerce div.product .entry-title,
		.single .entry-title-left header .entry-title{
			font-size: <?php echo esc_html($ts_h2_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h2_ipad_font_line_height); ?>;
		}
		h3,
		.h3,
		.search-fullwidth .ts-sidebar-content h2,
		.search-fullscreen .ts-sidebar-content h2,
		.breadcrumb-title-wrapper .breadcrumb-title h1,
		.theme-title .heading-title, 
		.comments-title .heading-title, 
		#comment-wrapper .heading-title,
		.ts-shortcode .heading-tab .heading-title,
		footer .widget .widgettitle, 
		footer .widget-title,
		.ts-banner.has-description .box-content header h2,
		.vc_col-sm-12 .ts-mailchimp-subscription-shortcode .widget-title,
		#main > .ts-product-category-wrapper .product-category .category-name h3,
		.vc_row[data-vc-stretch-content] .product-category .category-name h3,
		.ts-product-in-product-type-tab-wrapper.show-nav-as-text .column-tabs ul.tabs li,
		.ts-shortcode .shortcode-heading-wrapper h2,
		.ts-home-tabs > .tab-items > li > a{
			font-size: <?php echo esc_html($ts_h3_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h3_ipad_font_line_height); ?>;
		}
		h4,
		.h4,
		.woocommerce .cross-sells > h2, 
		.woocommerce .up-sells > h2, 
		.woocommerce .related > h2,
		.cart-collaterals .cart_totals > h2,
		.ts-shortcode.nav-top .owl-nav > div:before,
		.product-category .category-name h3,
		.ts-portfolio-wrapper.ts-shortcode .shortcode-heading-wrapper > h2,
		body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-left .vc_tta-tab a,
		body.wpb-js-composer .vc_general.vc_tta-tabs.vc_tta-tabs-position-right .vc_tta-tab a{
			font-size: <?php echo esc_html($ts_h4_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h4_ipad_font_line_height); ?>;
		}
		h5,
		.h5,
		#customer_login h2,
		.dropdown-container .cart-number,
		.woocommerce div.product .summary > .price,
		div.product .summary > .ts-variation-price .amount,
		.woocommerce div.product .single_variation_wrap .woocommerce-variation-price .price,
		.woocommerce-account .addresses .title h3, 
		.woocommerce-account .addresses h2, 
		.woocommerce-customer-details .addresses h2,
		.woocommerce div.product .woocommerce-tabs ul.tabs li > a,
		.ts-sidebar-content h2,
		body h4.wpb_pie_chart_heading,
		h3.wpb_heading,
		.ts-feature-wrapper .feature-title,
		.ts-product-category-banner-wrapper .category-item h3,
		.search-no-results .blog-template .alert,
		.mc4wp-form-fields > h2.title,
		html body > h1{
			font-size: <?php echo esc_html($ts_h5_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h5_ipad_font_line_height); ?>;
		}
		h6,.h6,
		.widget-title,
		.ts-products-widget > h2,
		.woocommerce div.product form.cart .variations label,
		.woocommerce div.product.tabs-in-summary .woocommerce-tabs ul.tabs li > a,
		#order_review_heading,
		.woocommerce-account .page-container div.woocommerce > h2,
		.account-content h2,
		.woocommerce-MyAccount-content > h2,
		.woocommerce-customer-details > h2,
		.woocommerce-order-details > h2,
		.woocommerce-billing-fields > h3,
		.woocommerce-additional-fields > h3,
		header.woocommerce-Address-title > h3,
		.ts-portfolio-wrapper .filter-bar li,
		.ts-blogs-wrapper .blogs article.quote blockquote,
		.ts-team-members h3,
		.widget-container .post_list_widget header h5,
		.single-portfolio .single-navigation > div a:first-child,
		.style-verticle .list-categories ul.tabs li span.category-name,
		.ts-product-brand-wrapper .meta-wrapper .heading-title,
		body.wpb-js-composer .vc_tta.vc_general .vc_tta-panel-title,
		body.wpb-js-composer .vc_toggle .vc_toggle_title h4{
			font-size: <?php echo esc_html($ts_h6_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_h6_ipad_font_line_height); ?>;
		}
		h1,.h1,
		h2,.h2,
		h3,.h3,
		.theme-title .heading-title, 
		.comments-title .heading-title, 
		#comment-wrapper .heading-title{
			letter-spacing: <?php echo esc_html($ts_body_font_letter_spacing); ?>;
		}
		/*** BUTTON ***/		
		a.ts-button,
		a.button,
		.ts-banner-button a,
		a.button-readmore,
		button, 
		input[type^="submit"],
		.woocommerce a.button, 
		.woocommerce button.button, 
		.woocommerce input.button,  
		.woocommerce a.button.disabled, 
		.woocommerce a.button:disabled, 
		.woocommerce a.button:disabled[disabled], 
		.woocommerce button.button.disabled, 
		.woocommerce button.button:disabled, 
		.woocommerce button.button:disabled[disabled], 
		.woocommerce input.button.disabled, 
		.woocommerce input.button:disabled, 
		.woocommerce input.button:disabled[disabled],
		.woocommerce #respond input#submit,
		.woocommerce a.button.alt, 
		.woocommerce button.button.alt, 
		.woocommerce input.button.alt,  
		.woocommerce a.button.alt.disabled, 
		.woocommerce a.button.alt:disabled, 
		.woocommerce a.button.alt:disabled[disabled], 
		.woocommerce button.button.alt.disabled, 
		.woocommerce button.button.alt:disabled, 
		.woocommerce button.button.alt:disabled[disabled], 
		.woocommerce input.button.alt.disabled, 
		.woocommerce input.button.alt:disabled, 
		.woocommerce input.button.alt:disabled[disabled],
		.woocommerce a.button.added:before,
		.woocommerce button.button.added:before,
		.woocommerce input.button.added:before,
		.more-less-buttons a,
		.ts-shop-load-more .load-more,
		.ts-shortcode .load-more-wrapper .load-more,
		.filter-widget-area .widget-title,
		.woocommerce .woocommerce-ordering ul.orderby .orderby-current,
		.filter-widget-area-button a,
		.shopping-cart p.buttons a,
		.woocommerce div.product .summary > form.cart .button,
		html body.woocommerce table.compare-list tr.add-to-cart td a:before,
		html body table.compare-list tr.add-to-cart td a:before,
		#ts-search-sidebar .ts-search-result-container .view-all-wrapper a,
		.ts-milestone h3.subject,
		body table.compare-list .add-to-cart td a,
		body .yith-woocompare-widget a.compare,
		.woocommerce .hidden-title-form a.hide-title-form,
		.woocommerce-account .woocommerce-MyAccount-navigation li,
		body table.compare-list .add-to-cart td a:not(.unstyled_button),
		/* Dokan */
		input[type="submit"].dokan-btn,
		a.dokan-btn,
		.dokan-btn,
		body .product-edit-new-container .dokan-btn-lg{
			font-size: <?php echo esc_html($ts_button_ipad_font_size); ?>;
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?>;
			letter-spacing: <?php echo esc_html($ts_body_font_letter_spacing); ?>;
		}
		input,
		textarea,
		select,
		body .select2-container--default .select2-selection--single .select2-selection__rendered,
		textarea,
		select,
		html input[type^="search"],
		html input[type^="text"], 
		html input[type^="email"],
		html input[type^="password"],
		html input[type^="number"],
		html input[type^="tel"],
		.woocommerce .woocommerce-result-count,
		.woocommerce .woocommerce-ordering .orderby li,
		.product-per-page-form ul.perpage li,
		.zoom-in-out-button a,
		.woocommerce form .form-row input.input-text,
		.woocommerce form .form-row textarea,
		#add_payment_method table.cart td.actions .coupon .input-text,
		.woocommerce-cart table.cart td.actions .coupon .input-text,
		.woocommerce-checkout table.cart td.actions .coupon .input-text,
		.chosen-container a.chosen-single,
		.woocommerce-checkout .form-row .chosen-container-single .chosen-single,
		#add_payment_method table.cart td.actions .coupon .input-text, 
		.woocommerce-cart table.cart td.actions .coupon .input-text, 
		.woocommerce-checkout table.cart td.actions .coupon .input-text, 
		.woocommerce-page table.cart td.actions .coupon .input-text,
		body .select2-container--default .select2-selection--single .select2-selection__rendered,
		.ts-mailchimp-subscription-shortcode.style-2 .vertical-button-icon .subscribe-email .button{
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?> !important;
		}
		.quantity ::-webkit-input-placeholder,
		td.product-quantity ::-webkit-input-placeholder{
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?>;
		}
		.quantity :-moz-placeholder,
		td.product-quantity :-moz-placeholder{
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?>;
		}
		.quantity ::-moz-placeholder,
		td.product-quantity ::-moz-placeholder{
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?>;
		}
		.quantity :-ms-input-placeholder,
		td.product-quantity :-ms-input-placeholder{
			line-height: <?php echo esc_html($ts_button_ipad_font_line_height); ?>;
		}
	}

	/*------------------------------------------------------
		3. CUSTOM COLOR
	-------------------------------------------------------*/
	/*** MAIN ***/
	body,
	blockquote .author,
	.entry-meta-top span,
	.tagcloud a,
	.comment_list_widget .comment-body,
	.list-categories li > a,
	.ts-pagination ul li a,
	/*** Product ***/
	.woocommerce p.stars a,
	.woocommerce-product-rating .woocommerce-review-link,
	.woocommerce .woocommerce-ordering ul li a, 
	.product-per-page-form ul.perpage ul li a,
	.woocommerce div.product p.stock span,
	.woocommerce div.product .summary .woocommerce-product-details__short-description,
	.brands-link span:not(.brand-links),
	.cats-link span:not(.cat-links),
	.tags-link span:not(.tag-links),
	ul.product_list_widget li .ts-wg-meta .product-categories a,
	.list-cats li a,
	.product-categories .count,
	.woocommerce div.product p.availability.stock label,
	.quantity .number-button:before,
	.quantity .number-button:after,
	div.product .ts-product-video-button, 
	div.product .ts-product-360-button, 
	.woocommerce nav.woocommerce-pagination ul li a,
	.woocommerce nav.woocommerce-pagination ul li span,
	/*** Cart/Checkout/Account ***/
	.woocommerce table.shop_attributes td,
	.woocommerce table.shop_attributes th,
	#add_payment_method table.cart td.actions .coupon .input-text,
	.woocommerce-cart table.cart td.actions .coupon .input-text,
	.woocommerce-checkout table.cart td.actions .coupon .input-text,
	.my-account-wrapper .forgot-pass a, 
	body .my-account-wrapper .form-content a.sign-up,
	/*** Shortcode/Widget/Visual ***/
	.ts-tiny-cart-wrapper .subtotal > span:first-child,
	.ts-tiny-cart-wrapper ul li div.blockUI.blockOverlay:after,
	.widget_shopping_cart ul li div.blockUI.blockOverlay:after,
	.ts-testimonial-wrapper.style-horizontal blockquote .content,
	.ts-testimonial-wrapper .author-role .role,
	.widget_recent_entries ul li > a,
	.widget_recent_comments ul li > a,
	.widget_rss cite,
	.widget_categories > ul li > a,
	.widget-container.ts-social-icons ul li > a,
	.widget-container ul li > a,
	.ts-product-categories-widget ul.product-categories li > a,
	.ts-product-categories-widget ul.product-categories li span.icon-toggle,
	.woocommerce .product-filter-by-brand ul li label,
	.ts-heading.style-multiple-heading .heading-2,
	.ts-banner.has-description .box-content header h6,
	/*** Plugin ***/
	.dataTables_wrapper,
	.wishlist_table .product-name .variation,
	.wishlist_table .product-name .variation dl,
	.wishlist_table .product-name .variation dt,
	.wishlist_table.images_grid li .item-details table.item-details-table td.label, 
	.wishlist_table.mobile li .item-details table.item-details-table td.label, 
	.wishlist_table.mobile li table.additional-info td.label, 
	.wishlist_table.modern_grid li .item-details table.item-details-table td.label,
	/*** Dokan ***/
	.dokan-widget-area .widget ul li > a,
	.dokan-orders-content .dokan-orders-area ul.order-statuses-filter li a,
	.dokan-pagination-container .dokan-pagination li a{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	::-webkit-input-placeholder{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	:-moz-placeholder{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	::-moz-placeholder{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	:-ms-input-placeholder{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	/*** Text Bold ***/
	strong,
	b,
	dt,
	table th,
	.ts-dropcap,
	.ol-style li:before,
	.ul-style li:before,
	/*** Blog ***/
	.single-portfolio .portfolio-info > span:first-child,
	.ts-sidebar-content .info-desc,
	.single-portfolio .portfolio-like,
	.ts-portfolio-wrapper .filter-bar li,
	.comment-respond #reply-title,
	/*** Product ***/
	.filter-widget-area-button a,
	.ts-tiny-cart-wrapper .total,
	.widget_shopping_cart .total,
	.zoom-in-out-button a:hover,
	.product-per-page-form ul.perpage .perpage-current:hover,
	.ts-active-filters .widget_layered_nav_filters ul li a,
	.woocommerce table.shop_attributes td,
	.woocommerce div.product form.cart .variations label,
	div.product .single-navigation > a > span,
	.ts-shop-result-count,
	.woocommerce div.product .woocommerce-tabs ul.tabs li,
	/*** Cart/Checkout ***/
	#order_review,
	.woocommerce-cart .cart-count,
	.woocommerce-cart table.cart,
	.woocommerce .woocommerce-ordering ul li a:hover,
	.cart-collaterals .cart_totals table.shop_table tbody td:before,
	.woocommerce ul#shipping_method .amount,
	.cart-collaterals .woocommerce-shipping-destination,
	.cart-collaterals .cart_totals .cart-subtotal td,
	.woocommerce-cart .cart-collaterals .cart_totals > h2,
	.woocommerce table.woocommerce-table--order-details tfoot,
	.woocommerce > form.checkout,
	/*** Shortcode/Widget/Visual ***/
	.list-categories ul.tabs li span.category-name,
	.ts-product-in-product-type-tab-wrapper ul.tabs li,
	.counter-wrapper > div .number,
	.widget_archive > ul li a:hover,
	.wp-block-archives li a:hover,
	.widget_categories > ul li a:hover,
	body.wpb-js-composer .vc_toggle .vc_toggle_icon:before,
	body.wpb-js-composer .vc_toggle .vc_toggle_icon:after,
	body.wpb-js-composer .vc_general.vc_tta-tabs .vc_tta-tab,
	.ts-product-in-product-type-tab-wrapper.show-nav-as-text.nav-middle .owl-nav > div:hover,
	/*** Plugin ***/
	#tab-seller .store-name span:first-child,
	#tab-seller .store-address span:first-child,
	#tab-seller .seller-name span:first-child,
	#yith-wcwl-popup-message,
	.woocommerce .wishlist_table{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}	
	html body > h1,
	.widget_calendar caption,
	.woocommerce .widget_price_filter .ui-slider-horizontal .ui-slider-range:before{
		background-color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.woocommerce .widget_price_filter .ui-slider .ui-slider-handle{
		border-color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.products .product.product-category .product-wrapper > a{
		background-color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.filter-widget-area ul li a:hover,
	.widget-container.ts-product-categories-widget ul > li.current > a, 
	.widget-container.widget_product_categories ul > li.current-cat a, 
	.widget-container.product-filter-by-brand ul > li.selected label,
	.widget-container.product-filter-by-availability ul li:hover label,
	.ol-style.primary-color li:before,
	.ul-style.primary-color li:before,
	.vertical-button-icon .subscribe-email .button,
	.woocommerce form .show-password-input.display-password::after, 
	.woocommerce-page form .show-password-input.display-password::after,
	/*** Widget ***/
	.widget_categories > ul li a:hover,
	.widget_pages > ul li a:hover,
	.widget_nav_menu .menu-menu-main-container > ul li a:hover,
	.widget-container ul.product-categories li a:hover,
	.widget-container.widget_categories > ul li a:hover,
	.widget_layered_nav > ul > li a:hover,
	.widget-container.product-filter-by-price .product-filter-by-price-wrapper > ul > li label:hover,
	.widget-container.product-filter-by-color ul li.chosen a:hover .color-name,
	.woocommerce .widget_rating_filter > ul li.wc-layered-nav-rating.chosen a,
	.widget-container.product-filter-by-brand ul > li.selected label,
	.widget-container.product-filter-by-availability ul li input[checked] + label,
	.widget-container.product-filter-by-color ul li.chosen a .color-name,
	.widget-container.product-filter-by-price ul li.chosen label,
	.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item--chosen a,
	.ts-social-icons .social-icons:not(.style-vertical) li.custom .ts-tooltip:before,
	.ts-social-icons .social-icons li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons li.custom a:hover,
	.widget_recent_comments ul li .comment-author-link,
	.ts-milestone .number,
	/*** Close ***/
	html body > h1 a.close,
	.ts-popup-modal .close:after,
	.ts-floating-sidebar .close:after,
	body .yith-woocompare-widget ul.products-list a.remove:before,
	/*** Remove ***/
	.woocommerce table.shop_table .product-remove a:before,
	.cart_list li .cart-item-wrapper a.remove:before,
	.woocommerce .widget_shopping_cart .cart_list li a.remove:before,
	.woocommerce.widget_shopping_cart .cart_list li a.remove:before,
	body table.compare-list tr.remove td > a .remove:before{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.owl-dots > div > span,
	body .flex-control-paging li a,
	body .theme-default .nivo-controlNav a,
	body .theme-default .nivo-controlNav a.active,
	.ts-social-icons .social-icons.style-square li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons.style-square li.custom a:hover,
	.ts-social-icons .social-icons.style-circle li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons.style-circle li.custom a:hover,
	.ts-social-icons .style-vertical-multicolor li.custom a:hover i,
	.ts-social-icons .style-circle-multicolor li.custom a,
	footer#colophon .ts-social-icons .style-circle-multicolor li.custom a,
	.ts-social-icons .style-vertical-multicolor li.custom a i{
		border-color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.owl-dots > div:hover > span,
	.owl-dots > div.active > span,
	.dots-verticle .owl-dots > div:hover > span,
	.dots-verticle .owl-dots > div.active > span,
	.ts-social-icons .social-icons.style-square li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons.style-square li.custom a:hover,
	.ts-social-icons .social-icons.style-circle li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons.style-circle li.custom a:hover,
	.ts-social-icons .social-icons.style-circle-opacity li.custom a:hover,
	footer#colophon .ts-social-icons .social-icons.style-circle-opacity li.custom a:hover,
	.ts-social-icons .social-icons:not(.style-vertical) li.custom .ts-tooltip,
	.ts-social-icons .style-circle-multicolor li.custom a,
	footer#colophon .ts-social-icons .style-circle-multicolor li.custom a,
	.ts-social-icons .style-vertical-multicolor li.custom a i{
		background: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	table.woocommerce-checkout-review-order-table tbody > tr:last-child > td,
	table.woocommerce-table--order-details tbody > tr:last-child > td{
		border-color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	/*** Main background ***/
	body #main,
	body.dokan-store #main:before,
	#cboxLoadedContent,
	.ts-floating-sidebar.search-fullscreen.active,	
	form.checkout div.create-account,
	#main > .page-container,
	body .flexslider .slides,
	body .wpb_gallery_slides.wpb_slider_nivo,
	.ts-floating-sidebar .ts-sidebar-content,
	.ts-popup-modal .popup-container,
	body #ts-search-result-container:before,
	.active-table.style-2 .group-price,
	body .select2-container--default .select2-selection--single .select2-selection__rendered,
	#yith-wcwl-popup-message,
	.dataTables_wrapper,
	body > .compare-list,
	.single-navigation > div .product-info:before,
	.woocommerce .woocommerce-ordering .orderby ul:before,
	.product-per-page-form ul.perpage ul:before,
	.single-navigation .product-info:before,
	.google-map-container .information,
	.ts-blogs .article-content,
	.list-posts > article,
	div.product .single-navigation a .product-info,
	.ts-floating-sidebar.search-fullscreen .ts-sidebar-content,
	.ts-header .menu-wrapper nav > ul.menu li ul.sub-menu:before,
	.ts-search-result-container,
	.filter-widget-area.style-default > .widget-container > :not(.widget-title-wrapper):before{
		background-color: <?php echo esc_html($ts_main_content_background_color); ?>;
	}	
	.tagcloud a,
	.tags-link a{
		background: <?php echo esc_html($ts_tag_background); ?>;
		color: <?php echo esc_html($ts_tag_color); ?>;
	}
	@-moz-keyframes fade{
		to{
			background: <?php echo esc_html($ts_main_content_background_color); ?>;
		}
	}
	@-webkit-keyframes fade{
		to{
			background: <?php echo esc_html($ts_main_content_background_color); ?>;
		}
	}
	@keyframes fade{
		to{
			background: <?php echo esc_html($ts_main_content_background_color); ?>;
		}
	}
	<?php if( strpos($ts_main_content_background_color, 'rgba') !== false ): ?>
	.dropdown-container ul.cart_list li.loading:before,
	.dropdown-container ul.cart_list li div.blockUI.blockOverlay:before{
		background-color: <?php echo esc_html(str_replace('1)', '0.9)', esc_html($ts_main_content_background_color))); ?>;
	}
	#yith-wcwl-form .blockOverlay{
		background-color: <?php echo esc_html(str_replace('1)', '0.9)', esc_html($ts_main_content_background_color))); ?> !important;
	}
	.product-content.show-more-less:before{
		background-image: -webkit-gradient(linear,left top,left bottom,color-stop(0,<?php echo esc_html(str_replace('1)', '0)', esc_html($ts_main_content_background_color))); ?>),to(<?php echo esc_html($ts_main_content_background_color); ?>));
		background-image: linear-gradient(to bottom,<?php echo esc_html(str_replace('1)', '0)', esc_html($ts_main_content_background_color))); ?> 0,<?php echo esc_html($ts_main_content_background_color); ?> 100%);
	}
	<?php endif; ?>
	/*** LOADING ***/
	.woocommerce a.button.loading:before,
	.woocommerce button.button.loading:before,
	.woocommerce input.button.loading:before,
	div.blockUI.blockOverlay:before,
	.woocommerce .blockUI.blockOverlay:before,
	.dropdown-container ul.cart_list li.loading:before,
	.woocommerce .add_to_wishlist.loading:before,
	.wishlist_table .product-add-to-cart > a.loading:before,
	div.product .summary .yith-wcwl-add-to-wishlist a.loading:before,
	.woocommerce table.compare-list a.button.loading:before,
	.archive.ajax-pagination .woocommerce > .products.loading:after{
		background: <?php echo esc_html($ts_main_content_background_color); ?>;
	}
	.vc_row.loading:after,
	div.wpcf7 .ajax-loader:after,
	.thumbnails.loading:after,
	.thumbnails-container.loading:after,
	article .thumbnail.loading:after,
	figure.gallery.loading:after,
	.ts-blogs-wrapper.loading .content-wrapper:after,
	.column-products.loading:after,
	.ts-product-category-wrapper .content-wrapper.loading:after,
	.ts-product-in-category-tab-wrapper ul.tabs.loading:after,
	.ts-logo-slider-wrapper.loading .content-wrapper:after,
	.woocommerce a.button.loading:after,
	.woocommerce button.button.loading:after,
	.woocommerce input.button.loading:after,
	div.blockUI.blockOverlay:after,
	.woocommerce div.blockUI.blockOverlay:after,
	.dropdown-container ul.cart_list li.loading:after,
	.ts-tiny-cart-wrapper ul li div.blockUI.blockOverlay:after,
	.widget_shopping_cart ul li div.blockUI.blockOverlay:after,
	.archive.ajax-pagination .woocommerce > .products.loading:before{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	/*** PRIMARY COLOR ***/
	.ts-shortcode .load-more-wrapper .load-more:before, 
	.ts-shop-load-more .load-more:before,
	.ts-product-attribute > div.option:not(.color).selected a:after,
	.widget_archive ul li:before{
		background-color: <?php echo esc_html($ts_primary_color); ?>;
	}
	.ts-dropcap.style-2,
	header .header-middle .header-right .my-wishlist-wrapper > a .wishlist-number,
	.shopping-cart-wrapper .cart-control .cart-number{
		background-color: <?php echo esc_html($ts_primary_color); ?>;
		color: <?php echo esc_html($ts_text_color_in_bg_primary); ?>;
	}
	.products .product.product-category .product-wrapper .meta-wrapper{
		color: <?php echo esc_html($ts_text_color_in_bg_primary); ?>;
	}
	.list-posts article.sticky,
	.woocommerce div.product .summary a.compare:hover, 
	.woocommerce div.product .summary .yith-wcwl-add-to-wishlist a:hover{
		border-color: <?php echo esc_html($ts_primary_color); ?>;
	}
	.search-button:hover .icon:before,
	.shopping-cart-wrapper:hover a > .ic-cart:before,
	.ts-tiny-account-wrapper:hover .account-control:before,
	ul.info-content.icon-primary li:before,
	.my-wishlist-wrapper:hover a:before{
		color: <?php echo esc_html($ts_primary_color); ?>;
	}
	.ts-group-meta-icon-toggle:hover .icon span, 
	.ts-icon-toggle-header-top:hover span{
		background-color: <?php echo esc_html($ts_primary_color); ?>;
	}
	@media screen and (max-width: 767px){
		.header-v6 .header-left .my-wishlist-wrapper > a .wishlist-number{
			background-color: <?php echo esc_html($ts_primary_color); ?>;
			color: <?php echo esc_html($ts_text_color_in_bg_primary); ?>;
		}
	}

	/*** HEADING ***/
	h1,h2,h3,h4,h5,h6,
	.h1,.h2,.h3,.h4,.h5,.h6,
	.widget-container .widget-title-wrapper a.block-control:hover,
	fieldset legend,
	body div.wishlist-title a.button,
	body div.wishlist-title a.button:hover,
	.ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a{
		color: <?php echo esc_html($ts_heading_color); ?>;
	}
	/*** LINK ***/
	a,
	.author a,
	.author a:hover,
	.ts-shortcode .load-more-wrapper .load-more,
	.ts-shop-load-more .load-more,
	.ts-shortcode .load-more-wrapper .load-more:hover,
	.ts-shop-load-more .load-more:hover,
	.ts-button-wrapper.text-only a.ts-button,
	.ts-button-wrapper.text-only a.ts-button:hover,
	.wc-proceed-to-checkout .button.continue-shopping:hover,
	a.shipping-calculator-button:hover,
	.woocommerce-review__author,
	.entry-meta-top .comment-count:before,
	.cats-link a,
	.cats-link a:hover,
	.ts-pagination ul li a:focus, 
	.ts-pagination ul li a:hover, 
	.ts-pagination ul li span.current, 
	.woocommerce nav.woocommerce-pagination ul li a:focus, 
	.woocommerce nav.woocommerce-pagination ul li a:hover, 
	.woocommerce nav.woocommerce-pagination ul li span.current,
	.vc_row.row-slider .widget-container.ts-social-icons ul li > a i,
	body blockquote,
	.ts-social-sharing li a,
	.ts-social-sharing span,
	.single-navigation-1 a,
	.single-navigation-2 a,
	.comment-meta a,
	.widget_recent_entries ul li a,
	.widget_rss ul li > a,
	.ts-testimonial-wrapper.show-nav.nav-middle .items .owl-nav > div,
	.ts-product-in-category-tab-wrapper.style-verticle.nav-middle .products .owl-nav > div,
	.style-horizontal-icons .list-categories ul.tabs.owl-carousel .owl-nav > div,
	.woocommerce .checkout-login-coupon-wrapper .lost_password a{
		color: <?php echo esc_html($ts_link_color); ?>;
	}
	a:hover,
	.hightlight,
	.logged-in .my-account-wrapper .dropdown-container a:hover,
	.products .product .product-brands a:hover,
	.products .product .product-categories a:hover,
	.widget-container ul.product_list_widget li .ts-wg-meta > a:hover, 
	ul.product_list_widget li .ts-wg-meta .product-categories a:hover,
	ul.product_list_widget li .ts-wg-meta > a:hover, 
	.woocommerce .widget-container ul.product_list_widget li .ts-wg-meta > a:hover, 
	.woocommerce ul.cart_list li .product-name a:hover, 
	.woocommerce ul.product_list_widget li .product-name a:hover,
	.woocommerce-product-rating .woocommerce-review-link:hover,
	.single-portfolio .portfolio-like:hover .ic-like,
	.ts-portfolio-wrapper .item .portfolio-meta .icon-group a:hover,
	.header-language:hover .wpml-ls-legacy-dropdown > ul > li > a:hover,
	.header-language:hover .wpml-ls-legacy-dropdown-click > ul > li > a:hover,
	.header-currency a:hover,
	.header-language a:hover,
	.ts-floating-sidebar .header-currency a:hover,
	.ts-floating-sidebar .header-language a:hover,
	.ts-floating-sidebar .close:hover:after,
	.woocommerce table.shop_table .product-remove:hover a:before,
	.cart_list li .cart-item-wrapper a.remove:hover:before,
	.woocommerce .widget_shopping_cart .cart_list li a.remove:hover:before,
	.woocommerce.widget_shopping_cart .cart_list li a.remove:hover:before,
	body table.compare-list tr.remove td > a:hover .remove:before,
	.filter-widget-area-button a.active,
	.single-post .no-featured-image .entry-meta-middle .author a,
	.single-post .no-featured-image .entry-meta-middle .cats-link a{
		color: <?php echo esc_html($ts_link_color_hover); ?>;
	}
	.woocommerce .widget_layered_nav_filters ul li a:hover:after,
	.woocommerce .widget_rating_filter > ul li.wc-layered-nav-rating.chosen a:hover:after,
	.widget-container.product-filter-by-brand ul > li.selected label:hover:after,
	.widget-container.product-filter-by-availability ul li input[checked] + label:hover:after,
	.widget-container.product-filter-by-color ul li.chosen a .color-name:hover:after,
	.widget-container.product-filter-by-price ul li.chosen label:hover:after,
	.woocommerce .woocommerce-widget-layered-nav-list .woocommerce-widget-layered-nav-list__item--chosen a:hover:after{
		color: <?php echo esc_html($ts_link_color_hover); ?> !important;
	}
	/*** HEADER ***/
	.header-top{
		background: <?php echo esc_html($ts_top_header_background_color); ?>;
		color: <?php echo esc_html($ts_top_header_text_color); ?>;
	}
	.header-top > .container:before{
		border-color: <?php echo esc_html($ts_top_header_border_color); ?>;
	}
	.header-middle{
		background: <?php echo esc_html($ts_middle_header_background_color); ?>;
		color: <?php echo esc_html($ts_middle_header_text_color); ?>;
	}
	div#main > *:first-child:before{
		border-color: <?php echo esc_html($ts_middle_header_border_color); ?>;
	}
	.shopping-cart-wrapper .dropdown-container:before, 
	.my-account-wrapper .dropdown-container:before, 
	header .wcml_currency_switcher > ul:before, 
	header .wpml-ls-legacy-dropdown ul.wpml-ls-sub-menu:before,
	header .wpml-ls-legacy-dropdown-click ul.wpml-ls-sub-menu:before{
		background: <?php echo esc_html($ts_middle_header_background_color); ?>;
	}
	header .header-top .wcml_currency_switcher > ul:before, 
	header .header-top .wpml-ls-legacy-dropdown ul.wpml-ls-sub-menu:before,
	header .header-top .wpml-ls-legacy-dropdown-click ul.wpml-ls-sub-menu:before{
		background: <?php echo esc_html($ts_top_header_background_color); ?>;
	}	
	.search-button .icon:before, 
	.ts-search-by-category .search-button:before,
	.shopping-cart-wrapper a > .ic-cart:before, 
	.ts-tiny-account-wrapper .account-control:before, 
	.logged-in .my-account-wrapper .dropdown-container a,
	.my-wishlist-wrapper > a:before{
		color: <?php echo esc_html($ts_middle_header_text_color); ?>;
	}
	.ts-group-meta-icon-toggle .icon span, 
	.ts-icon-toggle-header-top span{
		background-color: <?php echo esc_html($ts_middle_header_text_color); ?>;
	}
	.header-top .search-button .icon:before, 
	.header-top .ts-search-by-category .search-button:before,
	.header-top .shopping-cart-wrapper a > .ic-cart:before, 
	.header-top .ts-tiny-account-wrapper .account-control:before, 
	.logged-in .header-top .my-account-wrapper .dropdown-container a, 
	.header-top .my-wishlist-wrapper > a:before{
		color: <?php echo esc_html($ts_top_header_text_color); ?>;
	}

	/*** Dropdown color ***/
	.header-top .wpml-ls-legacy-dropdown .wpml-ls-sub-menu, 
	.header-top .wpml-ls-legacy-dropdown-click .wpml-ls-sub-menu, 
	.header-top .header-currency ul{
		color: <?php echo esc_html($ts_top_header_text_color); ?>;
	}
	.header-middle .shopping-cart-wrapper .dropdown-container, 
	.header-middle .my-account-wrapper .dropdown-container, 
	.header-middle .wpml-ls-legacy-dropdown .wpml-ls-sub-menu, 
	.header-middle .wpml-ls-legacy-dropdown-click .wpml-ls-sub-menu, 
	.header-middle .header-currency ul{
		color: <?php echo esc_html($ts_middle_header_text_color); ?>;
	}
	/*** BREADCRUMB ***/
	.breadcrumb-title-wrapper .breadcrumb-title h1{
		color: <?php echo esc_html($ts_breadcrumb_heading_color); ?>;
	}
	.breadcrumb-title-wrapper .breadcrumbs,
	.breadcrumb-title-wrapper .breadcrumbs span,
	.breadcrumb-title-wrapper .breadcrumbs a:hover{
		color: <?php echo esc_html($ts_breadcrumb_text_color); ?>;
	}
	.breadcrumb-title-wrapper .breadcrumbs a{
		color: <?php echo esc_html($ts_breadcrumb_link_color); ?>;
	}
	/*** MENU ***/
	.ts-menu ul li a,
	.ts-menu ul li .ts-menu-drop-icon,
	.header-v5 .header-currency,
	.header-v5 .header-language{
		color: <?php echo esc_html($ts_menu_text_color); ?>;
	}
	.ts-menu ul .sub-menu li a,
	.ts-menu ul .sub-menu li .ts-menu-drop-icon,
	.ts-menu div.list-link li a,
	.ts-menu .widget_nav_menu .menu li a{
		color: <?php echo esc_html($ts_sub_menu_text_color); ?>;
	}
	.ts-menu .widgettitle,
	.ts-menu .widget_nav_menu .widgettitle,
	.ts-menu .list-link .widgettitle{
		color: <?php echo esc_html($ts_sub_menu_heading_color); ?>;
	}
	nav > ul.menu li > a .menu-label:before,
	nav > ul.menu ul.sub-menu li a:hover,
	.ts-menu ul li.current-menu-item > a, 
	.ts-menu ul li.current_page_parent > a, 
	.ts-menu ul li.current-menu-parent > a, 
	.ts-menu ul li.current_page_item > a, 
	.ts-menu ul li.current-menu-ancestor > a, 
	.ts-menu ul li.current-page-ancestor > a, 
	.ts-menu ul li.current-product_cat-ancestor > a,
	.ts-menu ul li.current-menu-item .ts-menu-drop-icon, 
	.ts-menu ul li.current_page_parent .ts-menu-drop-icon, 
	.ts-menu ul li.current-menu-parent .ts-menu-drop-icon, 
	.ts-menu ul li.current_page_item .ts-menu-drop-icon, 
	.ts-menu ul li.current-menu-ancestor .ts-menu-drop-icon, 
	.ts-menu ul li.current-page-ancestor .ts-menu-drop-icon, 
	.ts-menu ul li.current-product_cat-ancestor .ts-menu-drop-icon,
	.ts-menu ul .sub-menu li.current-menu-item > a, 
	.ts-menu ul .sub-menu li.current_page_parent > a, 
	.ts-menu ul .sub-menu li.current-menu-parent > a, 
	.ts-menu ul .sub-menu li.current_page_item > a, 
	.ts-menu ul .sub-menu li.current-menu-ancestor > a, 
	.ts-menu ul .sub-menu li.current-page-ancestor > a, 
	.ts-menu ul .sub-menu li.current-product_cat-ancestor > a,
	.ts-menu ul .sub-menu li.current-menu-item .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current_page_parent .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current-menu-parent .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current_page_item .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current-menu-ancestor .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current-page-ancestor .ts-menu-drop-icon, 
	.ts-menu ul .sub-menu li.current-product_cat-ancestor .ts-menu-drop-icon,
	.ts-floating-sidebar a:hover{
		color: <?php echo esc_html($ts_primary_color); ?>;
	}
	.ts-floating-sidebar a,
	.ts-floating-sidebar .ts-menu ul li a,
	.ts-floating-sidebar .ts-menu ul li .ts-menu-drop-icon,
	.ts-floating-sidebar .ts-menu ul .sub-menu li a,
	.ts-floating-sidebar .ts-menu ul .sub-menu li .ts-menu-drop-icon,
	.ts-floating-sidebar .ts-menu div.list-link li a,
	.ts-floating-sidebar .ts-menu .widget_nav_menu .menu li a,
	.ts-floating-sidebar .ts-menu .widgettitle,
	.ts-floating-sidebar .ts-menu .widget_nav_menu .widgettitle,
	.ts-floating-sidebar .ts-menu .list-link .widgettitle{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.mobile-menu-wrapper ul li.current-menu-item > a, 
	.mobile-menu-wrapper ul li.current-menu-parent > a, 
	.mobile-menu-wrapper ul li.current_page_item > a, 
	.mobile-menu-wrapper ul li.current-menu-ancestor > a, 
	.mobile-menu-wrapper ul li.current-page-ancestor > a, 
	.mobile-menu-wrapper ul li.current-product_cat-ancestor > a, 
	.mobile-menu-wrapper ul li.current-menu-item .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current_page_parent .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current-menu-parent .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current_page_item .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current-menu-ancestor .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current-page-ancestor .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul li.current-product_cat-ancestor .mobile-menu-wrapper-drop-icon,
	.mobile-menu-wrapper ul .sub-menu li.current-menu-item > a, 
	.mobile-menu-wrapper ul .sub-menu li.current-menu-parent > a, 
	.mobile-menu-wrapper ul .sub-menu li.current_page_item > a, 
	.mobile-menu-wrapper ul .sub-menu li.current-menu-ancestor > a, 
	.mobile-menu-wrapper ul .sub-menu li.current-page-ancestor > a, 
	.mobile-menu-wrapper ul .sub-menu li.current-product_cat-ancestor > a, 
	.mobile-menu-wrapper ul .sub-menu li.current-menu-item .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current_page_parent .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current-menu-parent .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current_page_item .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current-menu-ancestor .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current-page-ancestor .mobile-menu-wrapper-drop-icon, 
	.mobile-menu-wrapper ul .sub-menu li.current-product_cat-ancestor .mobile-menu-wrapper-drop-icon,
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li a:hover, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul ul.sub-menu li a:hover, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-item > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-parent > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current_page_item > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-ancestor > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-page-ancestor > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-product_cat-ancestor > a, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-item .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current_page_parent .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-parent .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current_page_item .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-menu-ancestor .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-page-ancestor .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon, 
	.ts-floating-sidebar .main-menu-sidebar-wrapper ul li.current-product_cat-ancestor .ts-floating-sidebar .main-menu-sidebar-wrapper-drop-icon{
		color: <?php echo esc_html($ts_link_color_hover); ?>;
	}	
	.header-v8 .ts-header .menu-wrapper .ts-menu,
	.header-v8 .ts-header .menu-wrapper nav > ul.menu li ul.sub-menu:before{
		background: <?php echo esc_html($ts_sidebar_menu_background_color); ?>;
	}	
	.header-v8 header .wcml_currency_switcher > ul:before, 
	.header-v8 header .wpml-ls-legacy-dropdown ul.wpml-ls-sub-menu:before,
	.header-v8 .header-middle .header-currency ul:before{
		background: <?php echo esc_html($ts_main_content_background_color); ?>;
	}
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li .ts-menu-drop-icon,
	.header-v8 .ts-header .menu-wrapper nav > ul.menu ul.sub-menu li a:hover,
	.header-v8 .ts-header .menu-wrapper .ts-menu ul ul li.current-menu-item > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current_page_parent > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-menu-parent > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current_page_item > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-menu-ancestor > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-page-ancestor > a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-product_cat-ancestor > a,
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-menu-item .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current_page_parent .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-menu-parent .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current_page_item .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-menu-ancestor .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-page-ancestor .ts-menu-drop-icon, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul li.current-product_cat-ancestor .ts-menu-drop-icon,
	.header-v8 .ts-header .menu-wrapper .ts-menu ul .sub-menu li a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu ul .sub-menu li .ts-menu-drop-icon,
	.header-v8 .ts-header .menu-wrapper nav > ul.menu li:before,
	.header-v8 .ts-header .menu-wrapper .ts-menu div.list-link li a, 
	.header-v8 .ts-header .menu-wrapper .ts-menu .widget_nav_menu .menu li a,
	.header-v8 .ts-header .menu-wrapper .ts-menu nav .widgettitle,
	.header-v8 .ts-header .menu-wrapper .widget_nav_menu .widgettitle,
	.header-v8 .ts-header .menu-wrapper .list-link .widgettitle,
	.header-v8 .ts-header .menu-wrapper .ts-menu ul .sub-menu .ts-home-tabs > .tab-items > li > a,
	.header-v8 .ts-header .menu-wrapper .header-currency, 
	.header-v8 .ts-header .menu-wrapper .header-language{
		color: <?php echo esc_html($ts_sidebar_menu_color); ?>;
	}
	.header-v8 .ts-header .header-middle .wpml-ls-legacy-dropdown .wpml-ls-sub-menu, 
	.header-v8 .ts-header .header-middle .wpml-ls-legacy-dropdown-click .wpml-ls-sub-menu, 
	.header-v8 .ts-header .header-middle .header-currency ul,
	.header-v8 .ts-header .header-middle .header-language ul.wpml-ls-sub-menu li a, 
	.header-v8 .ts-header .header-middle .header-currency ul li a{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	@media screen and (min-width: 1279px){
		.header-v8.menu-header-active .ts-group-meta-icon-toggle .icon span{
			background: <?php echo esc_html($ts_sidebar_menu_color); ?>;
		}
	}
	/*** FOOTER ***/
	.first-footer-area{
		background: <?php echo esc_html($ts_footer_background_color); ?>;
		color: <?php echo esc_html($ts_footer_text_color); ?>;
	}
	.end-footer{
		background: <?php echo esc_html($ts_footer_end_background_color); ?>;
		color: <?php echo esc_html($ts_footer_end_text_color); ?>;
	}
	.first-footer-area a:hover,
	.first-footer-area .vc_wp_custommenu .current-menu-item > a{
		color: <?php echo esc_html($ts_footer_text_hover_color); ?>;
	}
	.end-footer a:hover,
	.end-footer .vc_wp_custommenu .current-menu-item > a{
		color: <?php echo esc_html($ts_footer_end_text_hover_color); ?>;
	}
	/*** BUTTON ***/
	a.ts-button,
	a.button,
	.ts-banner-button a,
	a.button-readmore:hover,
	button, 
	input[type^="submit"],
	.woocommerce a.button, 
	.woocommerce button.button, 
	.woocommerce input.button,  
	.woocommerce a.button.alt, 
	.woocommerce button.button.alt, 
	.woocommerce input.button.alt,  
	.woocommerce a.button.alt.disabled, 
	.woocommerce a.button.alt.disabled:hover, 
	.woocommerce a.button.alt:disabled, 
	.woocommerce a.button.alt:disabled:hover, 
	.woocommerce a.button.alt:disabled[disabled], 
	.woocommerce a.button.alt:disabled[disabled]:hover, 
	.woocommerce button.button.alt.disabled, 
	.woocommerce button.button.alt.disabled:hover, 
	.woocommerce button.button.alt:disabled, 
	.woocommerce button.button.alt:disabled:hover, 
	.woocommerce button.button.alt:disabled[disabled], 
	.woocommerce button.button.alt:disabled[disabled]:hover, 
	.woocommerce input.button.alt.disabled, 
	.woocommerce input.button.alt.disabled:hover, 
	.woocommerce input.button.alt:disabled, 
	.woocommerce input.button.alt:disabled:hover, 
	.woocommerce input.button.alt:disabled[disabled], 
	.woocommerce input.button.alt:disabled[disabled]:hover,
	.woocommerce #respond input#submit,
	.more-less-buttons a,
	.woocommerce .woocommerce-ordering ul.orderby .orderby-current:hover,
	.woocommerce .widget_price_filter .price_slider_amount .button:hover,
	.shopping-cart p.buttons a,
	.woocommerce-wishlist .yith-wcwl-form .hidden-title-form a.button:hover,
	.ts-tiny-cart-wrapper .dropdown-footer .button.view-cart:hover,
	.woocommerce-cart table.cart td.actions .coupon button:hover,
	.woocommerce-cart button.button[name="update_cart"],
	.woocommerce-cart button.button:disabled[name="update_cart"],
	.woocommerce-cart button.button[name="ts_empty_cart"]:hover,
	.ts-product-in-category-tab-wrapper .shop-more a.button:hover,
	.woocommerce-account .woocommerce-MyAccount-navigation li:hover,
	.woocommerce-account .woocommerce-MyAccount-navigation li.is-active,
	.wishlist_table .product-add-to-cart > a,
	/* Compare */
	body table.compare-list .add-to-cart td a,
	body .yith-woocompare-widget a.compare,
	body table.compare-list .add-to-cart td a:not(.unstyled_button),
	/* Dokan */
	input[type="submit"].dokan-btn,
	a.dokan-btn,
	.dokan-btn,
	body .product-edit-new-container .dokan-btn-lg,
	.woocommerce div.product div.summary form.cart table.group_table td.woocommerce-grouped-product-list-item__quantity .button:hover{
		background-color: <?php echo esc_html($ts_button_background_color); ?>;
		color: <?php echo esc_html($ts_button_text_color); ?>;
		border-color: <?php echo esc_html($ts_button_background_color); ?>;
	}
	a.ts-button:hover,
	a.button:hover,
	.ts-banner-button a:hover,
	a.button-readmore,
	button:hover, 
	input[type^="submit"]:hover,
	button:hover,
	.woocommerce a.button:hover, 
	.woocommerce button.button:hover, 
	.woocommerce input.button:hover,  
	.woocommerce a.button.alt:hover, 
	.woocommerce button.button.alt:hover, 
	.woocommerce input.button.alt:hover,  
	.woocommerce #respond input#submit:hover, 
	.woocommerce div.product .summary a.compare,
	.woocommerce div.product .summary .yith-wcwl-add-to-wishlist a,
	.more-less-buttons a:hover,
	.woocommerce .woocommerce-ordering ul.orderby .orderby-current,
	.woocommerce .widget_price_filter .price_slider_amount .button,
	.shopping-cart p.buttons a:hover,
	#ts-search-sidebar .ts-search-result-container .view-all-wrapper a:hover,
	.woocommerce-wishlist .yith-wcwl-form .hidden-title-form a.button,
	.ts-tiny-cart-wrapper .dropdown-footer .button.view-cart,
	.woocommerce-cart table.cart td.actions .coupon button,
	.woocommerce-cart button.button[name="update_cart"]:hover,
	.woocommerce-cart button.button:disabled[name="update_cart"]:hover,
	.woocommerce-cart button.button[name="ts_empty_cart"],
	.ts-product-in-category-tab-wrapper .shop-more a.button,
	.woocommerce-account .woocommerce-MyAccount-navigation li,
	.wishlist_table .product-add-to-cart > a:hover,
	/* Compare */
	body table.compare-list .add-to-cart td a:hover,
	body .yith-woocompare-widget a.compare:hover,
	body table.compare-list .add-to-cart td a:not(.unstyled_button):hover,
	/* Dokan */
	input[type="submit"].dokan-btn:hover,
	a.dokan-btn:hover,
	.dokan-btn:hover,
	body .product-edit-new-container .dokan-btn-lg:hover,
	.woocommerce div.product div.summary form.cart table.group_table td.woocommerce-grouped-product-list-item__quantity .button{
		background-color: <?php echo esc_html($ts_button_hover_background_color); ?>;
		color: <?php echo esc_html($ts_button_hover_text_color); ?>;
		border-color: <?php echo esc_html($ts_button_hover_text_color); ?>;
	}
	.ts-mailchimp-subscription-shortcode.style-2 .vertical-button-icon .subscribe-email .button{
		background-color: <?php echo esc_html($ts_button_background_color); ?> !important;
		color: <?php echo esc_html($ts_button_text_color); ?>;
		border-color: <?php echo esc_html($ts_button_background_color); ?>;
	}
	.ts-mailchimp-subscription-shortcode.style-2 .vertical-button-icon .subscribe-email .button:hover{
		background-color: <?php echo esc_html($ts_button_hover_background_color); ?> !important;
		color: <?php echo esc_html($ts_button_hover_text_color); ?> !important;
		border-color: <?php echo esc_html($ts_button_hover_text_color); ?>;
	}
	.wc-proceed-to-checkout .button.continue-shopping{
		color: <?php echo esc_html($ts_button_hover_text_color); ?>;
	}
	/*** BORDER ***/
	*,
	.woocommerce-checkout #payment:before,
	.image-border .thumbnail-wrapper > a img,
	.woocommerce div.product div.summary form.cart table.group_table th,
	.woocommerce div.product div.summary form.cart table.group_table td,
	.woocommerce-cart table.cart td.actions .coupon .input-text,
	.woocommerce div.product div.summary form.cart table.group_table td.woocommerce-grouped-product-list-item__quantity .button,
	body #yith-woocompare table.compare-list th,
	body #yith-woocompare table.compare-list td,
	.quantity .number-button,
	.image-border a.ts-wg-thumbnail img,
	.woocommerce-cart-form table thead th,
	.woocommerce table.shop_table th,
	.woocommerce .cart-collaterals table.shop_table tbody tr.cart-subtotal th,
	.woocommerce .cart-collaterals table.shop_table tbody tr.cart-subtotal td,
	.widget_archive ul li > a:after,
	.wp-block-archives li > a:after,
	.woocommerce table.shop_table.wishlist_table tr th,
	.woocommerce table.shop_table.wishlist_table tr td,
	.woocommerce div.product .summary a.compare, 
	.woocommerce div.product .summary .yith-wcwl-add-to-wishlist a{
		border-color: <?php echo esc_html($ts_border_color); ?>;
	}
	textarea,
	select,
	html input[type^="search"],
	html input[type^="text"], 
	html input[type^="email"],
	html input[type^="password"],
	html input[type^="number"],
	html input[type^="tel"],
	#comment-wrapper input,
	#comment-wrapper textarea,
	.ts-product-attribute > div.option:not(.color) a:before,
	.chosen-container a.chosen-single,
	.woocommerce form .form-row .input-text, 
	.woocommerce form .form-row select,
	body .select2-container .select2-dropdown, 
	body .select2-container .select2-selection,
	.woocommerce-checkout .form-row .chosen-container-single .chosen-single,
	.select2-container--default .select2-selection--single,
	body .select2-container--default .select2-selection--single .select2-selection__rendered{
		border-color: <?php echo esc_html($ts_input_border_color); ?>;
	}
	textarea:hover,
	select:hover,
	html input[type^="search"]:hover,
	html input[type^="text"]:hover, 
	html input[type^="email"]:hover,
	html input[type^="password"]:hover,
	html input[type^="number"]:hover,
	html input[type^="tel"]:hover,
	#comment-wrapper input:hover,
	#comment-wrapper textarea:hover,
	.woocommerce form .form-row .input-text:hover, 
	.woocommerce form .form-row select:hover,
	.ts-product-attribute > div.option:not(.color):hover a:before,
	.chosen-container a.chosen-single:hover,
	.woocommerce-checkout .form-row .chosen-container-single .chosen-single:hover,
	.select2-container--default .select2-selection--single:hover,
	body .select2-container--default .select2-selection--single .select2-selection__rendered:hover{
		border-color: <?php echo esc_html($ts_primary_color); ?>;
	}
	.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content{
		background: <?php echo esc_html($ts_border_color); ?>;
	}
	.no-featured-image .entry-meta-top .date-time:not(:last-child):after{
		color: <?php echo esc_html($ts_border_color); ?>;
	}

	/*** PRODUCT ***/
	.product-title,
	.product-name,
	body .yith-woocompare-widget ul.products-list li .title,
	.price,
	.woocommerce-Price-amount,
	ul.cart_list .price,
	ul.product_list_widget .price,
	.woocommerce div.product p.price, 
	.woocommerce div.product span.price,
	.woocommerce div.product .summary > .price,
	table.group_table td.woocommerce-grouped-product-list-item__price,
	.quantity .qty,
	.quantity input,
	ul.product_list_widget li .ts-wg-meta a{
		color: <?php echo esc_html($ts_text_bold_color); ?>;
	}
	.products .product .product-sku,
	.products .product .product-brands,
	.products .product .product-brands a,
	.products .product .product-categories,
	.products .product .product-categories a,
	.products .product .short-description{
		color: <?php echo esc_html($ts_text_color); ?>;
	}
	.price del,
	.woocommerce-grouped-product-list-item__price del,
	.woocommerce div.product p.price del, 
	.woocommerce div.product span.price del{
		color: <?php echo esc_html($ts_product_del_color); ?>;
	}
	.product-group-button > div{
		color: <?php echo esc_html($ts_product_button_thumbnail_color); ?>;
		background: <?php echo esc_html($ts_product_button_thumbnail_background_color); ?>;
		border-color: <?php echo esc_html($ts_product_button_thumbnail_color); ?>;
	}
	.product-group-button > div:hover{
		color: <?php echo esc_html($ts_product_button_thumbnail_hover_color); ?>;
		background: <?php echo esc_html($ts_product_button_thumbnail_hover_background_color); ?>;
		border-color: <?php echo esc_html($ts_product_button_thumbnail_hover_background_color); ?>;
	}
	.product-group-button .button-tooltip, 
	.ts-product-attribute .button-tooltip{
		color: <?php echo esc_html($ts_product_button_thumbnail_tooltip_color); ?>;
	}
	.product-group-button .button-tooltip:before, 
	.ts-product-attribute .button-tooltip:before{
		background: <?php echo esc_html($ts_product_button_thumbnail_tooltip_background_color); ?>;
	}
	.product-style-2 .product-group-button .button-tooltip, 
	.product-style-2 .ts-product-attribute .button-tooltip,
	.product-style-2 .product-group-button > div:hover div.blockUI.blockOverlay:after,
	.woocommerce.product-style-2 .product-group-button > div:hover .blockUI.blockOverlay:after,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover a.button.loading:after,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover button.button.loading:after,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover input.button.loading:after,
	.product-style-2 .product-group-button > div .button.loading:after,
	.product-style-2 .product-group-button > div:hover .button.added:before{
		color: <?php echo esc_html($ts_product_button_thumbnail_hover_color); ?>;
	}
	.product-style-2 .product-group-button .button-tooltip:before, 
	.product-style-2 .ts-product-attribute .button-tooltip:before,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover a.button.loading:before,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover button.button.loading:before,
	.product-style-2 .thumbnail-wrapper .product-group-button > div:hover input.button.loading:before,
	.product-style-2 .product-group-button > div .button.loading:before,
	.product-style-2 .product-group-button > div:hover div.blockUI.blockOverlay:before,
	.woocommerce.product-style-2 .product-group-button > div:hover .blockUI.blockOverlay:before{
		background: <?php echo esc_html($ts_product_button_thumbnail_hover_background_color); ?>;
	}
	.product-style-2 .product-group-button .button-tooltip:after, 
	.product-style-2 .ts-product-attribute .button-tooltip:after{
		border-top-color: <?php echo esc_html($ts_product_button_thumbnail_hover_background_color); ?>;
	}
	/*** Device group button ***/
	.product-group-button-meta{
		color: <?php echo esc_html($ts_product_button_device_color); ?>;
	}
	/*** Product Label ***/
	.woocommerce .product .product-label .onsale{
		color: <?php echo esc_html($ts_product_sale_label_text_color); ?>;
		background: <?php echo esc_html($ts_product_sale_label_background_color); ?>;
	}
	.woocommerce .product .product-label .new{
		color: <?php echo esc_html($ts_product_new_label_text_color); ?>;
		background: <?php echo esc_html($ts_product_new_label_background_color); ?>;
	}
	.woocommerce .product .product-label .featured{
		color: <?php echo esc_html($ts_product_feature_label_text_color); ?>;
		background: <?php echo esc_html($ts_product_feature_label_background_color); ?>;
	}
	.woocommerce .product .product-label .out-of-stock{
		color: <?php echo esc_html($ts_product_outstock_label_text_color); ?>;
		background: <?php echo esc_html($ts_product_outstock_label_background_color); ?>;
	}
	/*** Product Rating ***/
	.woocommerce p.stars a,
	.woocommerce p.stars a:hover ~ a,
	.woocommerce p.stars.selected a.active ~ a,
	.woocommerce .star-rating:before,
	.ts-testimonial-wrapper .rating:before, 
	blockquote .rating:before{
		color: <?php echo esc_html($ts_rating_color); ?>;
	}
	.woocommerce p.stars:hover a,
	.woocommerce p.stars.selected a,
	.woocommerce .star-rating span:before,
	.ts-testimonial-wrapper .rating span:before, 
	blockquote .rating span:before{
		color: <?php echo esc_html($ts_rating_fill_color); ?>;
	}
	/*** Add to cart message ***/
	#ts-ajax-add-to-cart-message {
		background: <?php echo esc_html($ts_add_to_cart_message_background); ?>;
		color: <?php echo esc_html($ts_add_to_cart_message_color); ?>;
	}
	#ts-ajax-add-to-cart-message.error {
		background: <?php echo esc_html($ts_add_to_cart_message_error_background); ?>;
		color: <?php echo esc_html($ts_add_to_cart_message_error_color); ?>;
	}
	/*** Header icon Mobile for header-v2 ***/
	@media only screen and (max-width: 767px){
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .ts-menu > nav.main-menu > ul.menu > li > .ts-menu-drop-icon,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li > a,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .header-language .wpml-ls > ul > li > a,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .header-currency .wcml_currency_switcher > a,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .search-button .icon:before,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .my-account-wrapper .account-control:before,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .my-wishlist-wrapper a:before,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .shopping-cart-wrapper a > .ic-cart:before,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .ts-group-meta-icon-toggle .icon,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .ts-menu > nav.main-menu > ul.menu > li > .ts-menu-drop-icon,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li > a,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .header-language .wpml-ls > ul > li > a,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .header-currency .wcml_currency_switcher > a,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .search-button .icon:before,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .my-account-wrapper .account-control:before,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .my-wishlist-wrapper a:before,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .shopping-cart-wrapper a > .ic-cart:before,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .ts-group-meta-icon-toggle .icon{
			color: <?php echo esc_html($ts_mobile_header_icon_color); ?>;
		}
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li > a .menu-label:before,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-item > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_parent > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-parent > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_item > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-ancestor > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-page-ancestor > a, 
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-product_cat-ancestor > a,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li > a .menu-label:before,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-item > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_parent > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-parent > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_item > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-ancestor > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-page-ancestor > a, 
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-product_cat-ancestor > a,
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li > a .menu-label:before,
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-item > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_parent > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-parent > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current_page_item > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-menu-ancestor > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-page-ancestor > a, 
		.header-v10.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .header-middle .menu-wrapper nav > ul.menu > li.current-product_cat-ancestor > a{
			color: #cccccc;
		}
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .my-wishlist-wrapper > a .wishlist-number,
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .shopping-cart-wrapper .cart-control .cart-number,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .my-wishlist-wrapper > a .wishlist-number,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .shopping-cart-wrapper .cart-control .cart-number{
			background-color: <?php echo esc_html($ts_mobile_header_icon_color); ?>;
			color: <?php echo esc_html($ts_primary_color); ?>;
		}
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .ts-group-meta-icon-toggle .icon span,  
		.header-v9.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .ts-icon-toggle-header-top span,
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .ts-group-meta-icon-toggle .icon span,  
		.header-v2.header-transparent:not(.menu-header-active) .header-template > div:not(.is-sticky) .ts-icon-toggle-header-top span{
			background-color: <?php echo esc_html($ts_mobile_header_icon_color); ?>;
		}
	}
	
	/******* 4. RESPONSIVE *******/
	<?php if( isset($data['ts_responsive']) && $data['ts_responsive'] == 1 ): ?>
		@media only screen and (max-width: 767px){
			body #yith-woocompare table.compare-list tr th:first-child{
				display: none;
			}
			body #yith-woocompare table.compare-list tbody tr td:first-of-type {
				border-left-width: 1px;
			}
		}
	<?php else: ?>
		/* VISUAL COMPOSER */
		.vc_col-xs-1, .vc_col-sm-1, .vc_col-md-1, .vc_col-lg-1, .vc_col-xs-2, .vc_col-sm-2, .vc_col-md-2, .vc_col-lg-2, .vc_col-xs-3, .vc_col-sm-3, .vc_col-md-3, .vc_col-lg-3, .vc_col-xs-4, .vc_col-sm-4, .vc_col-md-4, .vc_col-lg-4, .vc_col-xs-5, .vc_col-sm-5, .vc_col-md-5, .vc_col-lg-5, .vc_col-xs-6, .vc_col-sm-6, .vc_col-md-6, .vc_col-lg-6, .vc_col-xs-7, .vc_col-sm-7, .vc_col-md-7, .vc_col-lg-7, .vc_col-xs-8, .vc_col-sm-8, .vc_col-md-8, .vc_col-lg-8, .vc_col-xs-9, .vc_col-sm-9, .vc_col-md-9, .vc_col-lg-9, .vc_col-xs-10, .vc_col-sm-10, .vc_col-md-10, .vc_col-lg-10, .vc_col-xs-11, .vc_col-sm-11, .vc_col-md-11, .vc_col-lg-11, .vc_col-xs-12, .vc_col-sm-12, .vc_col-md-12, .vc_col-lg-12{
			padding-left: 0;
			padding-right: 0;
		}
		.vc_column-gap-default > .vc_col-xs-1,.vc_column-gap-default > .vc_col-sm-1,.vc_column-gap-default > .vc_col-md-1,.vc_column-gap-default > .vc_col-lg-1,.vc_column-gap-default > .vc_col-xs-2,.vc_column-gap-default > .vc_col-sm-2,.vc_column-gap-default > .vc_col-md-2,.vc_column-gap-default > .vc_col-lg-2,.vc_column-gap-default > .vc_col-xs-3,.vc_column-gap-default > .vc_col-sm-3,.vc_column-gap-default > .vc_col-md-3,.vc_column-gap-default > .vc_col-lg-3,.vc_column-gap-default > .vc_col-xs-4,.vc_column-gap-default > .vc_col-sm-4,.vc_column-gap-default > .vc_col-md-4,.vc_column-gap-default > .vc_col-lg-4,.vc_column-gap-default > .vc_col-xs-5,.vc_column-gap-default > .vc_col-sm-5,.vc_column-gap-default > .vc_col-md-5,.vc_column-gap-default > .vc_col-lg-5,.vc_column-gap-default > .vc_col-xs-6,.vc_column-gap-default > .vc_col-sm-6,.vc_column-gap-default > .vc_col-md-6,.vc_column-gap-default > .vc_col-lg-6,.vc_column-gap-default > .vc_col-xs-7,.vc_column-gap-default > .vc_col-sm-7,.vc_column-gap-default > .vc_col-md-7,.vc_column-gap-default > .vc_col-lg-7,.vc_column-gap-default > .vc_col-xs-8,.vc_column-gap-default > .vc_col-sm-8,.vc_column-gap-default > .vc_col-md-8,.vc_column-gap-default > .vc_col-lg-8,.vc_column-gap-default > .vc_col-xs-9,.vc_column-gap-default > .vc_col-sm-9,.vc_column-gap-default > .vc_col-md-9,.vc_column-gap-default > .vc_col-lg-9,.vc_column-gap-default > .vc_col-xs-10,.vc_column-gap-default > .vc_col-sm-10,.vc_column-gap-default > .vc_col-md-10,.vc_column-gap-default > .vc_col-lg-10,.vc_column-gap-default > .vc_col-xs-11,.vc_column-gap-default > .vc_col-sm-11,.vc_column-gap-default > .vc_col-md-11,.vc_column-gap-default > .vc_col-lg-11,.vc_column-gap-default > .vc_col-xs-12,.vc_column-gap-default > .vc_col-sm-12,.vc_column-gap-default > .vc_col-md-12,.vc_column-gap-default > .vc_col-lg-12{
			padding-left: 15px;
			padding-right: 15px;
		}
		.ts-col-1, .ts-col-2, .ts-col-3, .ts-col-4, .ts-col-5, .ts-col-6, .ts-col-7, .ts-col-8, .ts-col-9, .ts-col-10, .ts-col-11, .ts-col-12, .ts-col-13, .ts-col-14, .ts-col-15, .ts-col-16, .ts-col-17, .ts-col-18, .ts-col-19, .ts-col-20, .ts-col-21, .ts-col-22, .ts-col-23, .ts-col-24{
			float: left;
		}
		.ts-col-24{
			width: 100%;
		}
		.ts-col-23{
			width: 95.83333333%;
		}
		.ts-col-22{
			width: 91.66666667%;
		}
		.ts-col-21{
			width: 87.5%;
		}
		.ts-col-20{
			width: 83.33333333%;
		}
		.ts-col-19{
			width: 79.16666667%;
		}
		.ts-col-18{
			width: 75%;
		}
		.ts-col-17{
			width: 70.83333333%;
		}
		.ts-col-16{
			width: 66.66666667%;
		}
		.ts-col-15{
			width: 62.5%;
		}
		.ts-col-14{
			width: 58.33333333%;
		}
		.ts-col-13{
			width: 54.16666667%;
		}
		.ts-col-12{
			width: 50%;
		}
		.ts-col-11{
			width: 45.83333333%;
		}
		.ts-col-10{
			width: 41.66666667%;
		}
		.ts-col-9{
			width: 37.5%;
		}
		.ts-col-8{
			width: 33.33333333%;
		}
		.ts-col-7{
			width: 29.16666667%;
		}
		.ts-col-6{
			width: 25%;
		}
		.ts-col-5{
			width: 20.83333333%;
		}
		.ts-col-4{
			width: 16.66666667%;
		}
		.ts-col-3{
			width: 12.5%;
		}
		.ts-col-2{
			width: 8.33333333%;
		}
		.ts-col-1{
			width: 4.16666667%;
		}
		.ts-col-44per{
			width: 44%;
		}
		.ts-col-56per{
			width: 56%;
		}
		@media only screen and (max-width: 991px){
			body.boxed #page,
			.page-container,
			.container{
				width: 760px;
				max-width: 100%;
			}
			.ts-banner-image.fix-width,
			.ts-banner.fix-width,
			.ts-blog-videos-wrapper,
			.dokan-store #page > #main,
			.breadcrumb-title-wrapper .breadcrumb-content,
			body.boxed header.ts-header .header-sticky{
				max-width: 760px;
				width: 100%;
			}
		}
	<?php endif; ?>
	
<?php update_option('ts_load_dynamic_style', 1); // uncomment after finished this file ?>	