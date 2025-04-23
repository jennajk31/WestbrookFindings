<?php
$redux_url = '';
if( class_exists('ReduxFramework') ){
	$redux_url = ReduxFramework::$_url;
}

$logo_url 					= get_template_directory_uri() . '/images/logo.png'; 
$favicon_url 				= get_template_directory_uri() . '/images/favicon.ico';

$color_image_folder = get_template_directory_uri() . '/admin/assets/images/colors/';
$list_colors = array('default');
$preset_colors_options = array();
foreach( $list_colors as $color ){
	$preset_colors_options[$color] = array(
					'alt'      => $color
					,'img'     => $color_image_folder . $color . '.jpg'
					,'presets' => drile_get_preset_color_options( $color )
	);
}

$family_fonts = array(
	"Arial, Helvetica, sans-serif"                          => "Arial, Helvetica, sans-serif"
	,"'Arial Black', Gadget, sans-serif"                    => "'Arial Black', Gadget, sans-serif"
	,"'Bookman Old Style', serif"                           => "'Bookman Old Style', serif"
	,"'Comic Sans MS', cursive"                             => "'Comic Sans MS', cursive"
	,"Courier, monospace"                                   => "Courier, monospace"
	,"Garamond, serif"                                      => "Garamond, serif"
	,"Georgia, serif"                                       => "Georgia, serif"
	,"Impact, Charcoal, sans-serif"                         => "Impact, Charcoal, sans-serif"
	,"'Lucida Console', Monaco, monospace"                  => "'Lucida Console', Monaco, monospace"
	,"'Lucida Sans Unicode', 'Lucida Grande', sans-serif"   => "'Lucida Sans Unicode', 'Lucida Grande', sans-serif"
	,"'MS Sans Serif', Geneva, sans-serif"                  => "'MS Sans Serif', Geneva, sans-serif"
	,"'MS Serif', 'New York', sans-serif"                   => "'MS Serif', 'New York', sans-serif"
	,"'Palatino Linotype', 'Book Antiqua', Palatino, serif" => "'Palatino Linotype', 'Book Antiqua', Palatino, serif"
	,"Tahoma,Geneva, sans-serif"                            => "Tahoma, Geneva, sans-serif"
	,"'Times New Roman', Times,serif"                       => "'Times New Roman', Times, serif"
	,"'Trebuchet MS', Helvetica, sans-serif"                => "'Trebuchet MS', Helvetica, sans-serif"
	,"Verdana, Geneva, sans-serif"                          => "Verdana, Geneva, sans-serif"
	,"CustomFont"                          					=> "CustomFont"
);

$header_layout_options = array();
$header_image_folder = get_template_directory_uri() . '/admin/assets/images/headers/';
for( $i = 1; $i <= 10; $i++ ){
	$header_layout_options['v' . $i] = array(
		'alt'  => sprintf(esc_html__('Header Layout %s', 'drile'), $i)
		,'img' => $header_image_folder . 'header_v'.$i.'.jpg'
	);
}

$loading_screen_options = array();
$loading_image_folder = get_template_directory_uri() . '/images/loading/';
for( $i = 1; $i <= 10; $i++ ){
	$loading_screen_options[$i] = array(
		'alt'  => sprintf(esc_html__('Loading Image %s', 'drile'), $i)
		,'img' => $loading_image_folder . 'loading_'.$i.'.svg'
	);
}

$footer_block_options = drile_get_footer_block_options();

$breadcrumb_layout_options = array();
$breadcrumb_image_folder = get_template_directory_uri() . '/admin/assets/images/breadcrumbs/';
for( $i = 1; $i <= 3; $i++ ){
	$breadcrumb_layout_options['v' . $i] = array(
		'alt'  => sprintf(esc_html__('Breadcrumb Layout %s', 'drile'), $i)
		,'img' => $breadcrumb_image_folder . 'breadcrumb_v'.$i.'.jpg'
	);
}

$sidebar_options = array();
$default_sidebars = drile_get_list_sidebars();
if( is_array($default_sidebars) ){
	foreach( $default_sidebars as $key => $_sidebar ){
		$sidebar_options[$_sidebar['id']] = $_sidebar['name'];
	}
}

$product_loading_image = get_template_directory_uri() . '/images/prod_loading.gif';

$option_fields = array();

/*** General Tab ***/
$option_fields['general'] = array(
	array(
		'id'        => 'section-logo-favicon'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Logo - Favicon', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_logo'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Logo', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Select an image file for the main logo', 'drile' )
		,'readonly' => false
		,'default'  => array( 'url' => $logo_url )
	)
	,array(
		'id'        => 'ts_logo_mobile'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Logo On Mobile', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Leave blank to display the main logo on mobile', 'drile' )
		,'readonly' => false
		,'default'  => array( 'url' => '' )
	)
	,array(
		'id'        => 'ts_logo_sticky'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Sticky Logo', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Display this logo on sticky header', 'drile' )
		,'readonly' => false
		,'default'  => array( 'url' => '' )
	)
	,array(
		'id'        => 'ts_logo_width'
		,'type'     => 'text'
		,'url'      => true
		,'title'    => esc_html__( 'Logo Width', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Set width for logo (in pixels)', 'drile' )
		,'default'  => '110'
	)
	,array(
		'id'        => 'ts_device_logo_width'
		,'type'     => 'text'
		,'url'      => true
		,'title'    => esc_html__( 'Logo Width on Device', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Set width for logo (in pixels)', 'drile' )
		,'default'  => '96'
	)
	,array(
		'id'        => 'ts_favicon'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Favicon', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Select a PNG, GIF or ICO image', 'drile' )
		,'readonly' => false
		,'default'  => array( 'url' => $favicon_url )
	)
	,array(
		'id'        => 'ts_text_logo'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Text Logo', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Drile'
	)
	
	,array(
		'id'        => 'section-layout-style'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Layout Style', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'        => 'ts_header_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Header Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'required'	=> array( 'ts_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_main_content_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Main Content Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'required'	=> array( 'ts_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_footer_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Footer Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'required'	=> array( 'ts_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'       	=> 'ts_layout_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Layout Style', 'drile' )
		,'subtitle' => esc_html__( 'You can override this option for the individual page', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'wide' 		=> 'Wide'
			,'boxed' 	=> 'Boxed'
		)
		,'default'  => 'wide'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_layout_fullwidth', 'equals', '0' )
	)
	
	,array(
		'id'        => 'section-rtl'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Right To Left', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_enable_rtl'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Right To Left', 'drile' )
		,'subtitle' => ''
		,'default'  => false
	)
	
	,array(
		'id'        => 'section-responsive'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Responsive', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_responsive'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Responsive', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	
	,array(
		'id'        => 'section-smooth-scroll'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Smooth Scroll', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_smooth_scroll'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Smooth Scroll', 'drile' )
		,'subtitle' => ''
		,'default'  => false
	)
	
	,array(
		'id'        => 'section-back-to-top-button'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Back To Top Button', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_back_to_top_button'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Back To Top Button', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'        => 'ts_back_to_top_button_on_mobile'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Back To Top Button On Mobile', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	
	,array(
		'id'        => 'section-image-not-found'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Image in 404 page', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_image_not_found'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( '404 Image', 'drile' )
		,'desc'     => ''
		,'subtitle' => ''
		,'readonly' => false
		,'default'  => array( 'url' => '' )
	)
	
	,array(
		'id'        => 'section-loading-screen'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Loading Screen', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_loading_screen'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Loading Screen', 'drile' )
		,'subtitle' => ''
		,'default'  => false
	)
	,array(
		'id'        => 'ts_loading_image'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Loading Image', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $loading_screen_options
		,'default'  => '1'
	)
	,array(
		'id'        => 'ts_custom_loading_image'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Custom Loading Image', 'drile' )
		,'desc'     => ''
		,'subtitle' => ''
		,'readonly' => false
		,'default'  => array( 'url' => '' )
	)
	,array(
		'id'       	=> 'ts_display_loading_screen_in'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Display Loading Screen In', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'all-pages' 		=> esc_html__( 'All Pages', 'drile' )
			,'homepage-only' 	=> esc_html__( 'Homepage Only', 'drile' )
			,'specific-pages' 	=> esc_html__( 'Specific Pages', 'drile' )
		)
		,'default'  => 'all-pages'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_loading_screen_exclude_pages'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Exclude Pages', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'data'     => 'pages'
		,'multi'    => true
		,'default'	=> ''
		,'required'	=> array( 'ts_display_loading_screen_in', 'equals', 'all-pages' )
	)
	,array(
		'id'       	=> 'ts_loading_screen_specific_pages'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Specific Pages', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'data'     => 'pages'
		,'multi'    => true
		,'default'	=> ''
		,'required'	=> array( 'ts_display_loading_screen_in', 'equals', 'specific-pages' )
	)
	
	,array(
		'id'        => 'section-google-map-api'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Google Map API Key', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_gmap_api_key'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Enter Your API Key', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
	)
);

/*** Color Scheme Tab ***/
$option_fields['color-scheme'] = array(
	array(
		'id'          => 'ts_color_scheme'
		,'type'       => 'image_select'
		,'presets'    => true
		,'full_width' => false
		,'title'      => esc_html__( 'Select Color Scheme of Theme', 'drile' )
		,'subtitle'   => ''
		,'desc'       => ''
		,'options'    => $preset_colors_options
		,'default'    => 'default'
		,'class'      => 'hidden'
	)
	,array(
		'id'        => 'section-general-colors'
		,'type'     => 'section'
		,'title'    => esc_html__( 'General Colors', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'      => 'info-primary-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Primary Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_primary_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Primary Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_text_color_in_bg_primary'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Text Color In Background Primary Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-main-content-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Main Content Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_main_content_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Main Content Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_text_bold_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Text Bold Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_heading_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Heading Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_link_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Link Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_link_color_hover'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Link Color Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#e8b294'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_tag_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Tag Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_tag_background'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Tag Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#d7d7d7'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_border_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Border Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#d7d7d7'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_input_border_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Input Border Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#a3a3a3'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-button-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Button Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_button_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Button Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_button_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Button Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_button_text_hover'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Button Text Color Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_button_hover_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Button Background Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'        => 'section-header-colors'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Header Colors', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       => 'ts_top_header_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Top Header Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_top_header_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Top Header Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_top_header_border_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Top Header Border Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#e9e9e9'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_middle_header_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Middle Header Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_middle_header_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Middle Header Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_middle_header_border_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Middle Header Border Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#e9e9e9'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_mobile_header_icon_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Mobile Header Transparent Icon Color', 'drile' )
		,'subtitle' => esc_html__( 'The Mobile Header Icon Color option is only used for Header Layout 2', 'drile' )
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-breadcrumb-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Breadcrumb Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_breadcrumb_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Breadcrumb Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_breadcrumb_heading_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Breadcrumb Heading Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_breadcrumb_link_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Breadcrumb Link Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-main-menu-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Menu Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_menu_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Menu Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)	
	,array(
		'id'       => 'ts_sub_menu_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sub Menu Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_sub_menu_heading_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sub Menu Heading Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_sidebar_menu_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sidebar Menu Background Color', 'drile' )
		,'subtitle' => esc_html__( 'The Sidebar Menu Background Color option is only used for Header Layout 8', 'drile' )
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_sidebar_menu_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sidebar Menu Color', 'drile' )
		,'subtitle' => esc_html__( 'The Sidebar Menu Color option is only used for Header Layout 8', 'drile' )
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'        => 'section-footer-colors'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Footer Colors', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       => 'ts_footer_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_footer_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_footer_text_hover_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer Text Color Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_footer_end_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer End Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_footer_end_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer End Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#707070'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_footer_end_text_hover_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Footer End Text Color Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'        => 'section-product-colors'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Colors', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       => 'ts_product_del_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Product Sale Price Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#bdbdbd'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_rating_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Product Rating Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#848484'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_rating_fill_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Product Rating Fill Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-product-button-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Thumbnail Product Button Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Thumbnail Button Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Thumbnail Button Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_hover_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Thumbnail Button Text Color Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_hover_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Thumbnail Button Background Hover', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_tooltip_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Tooltip Thumbnail Button Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_thumbnail_tooltip_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Tooltip Thumbnail Button Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_button_device_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Product Buttons Color on Device', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)	
	,array(
		'id'      => 'info-product-label-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Product Label Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_product_sale_label_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sale Label Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_sale_label_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Sale Label Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#9e0b0f'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_new_label_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'New Label Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_new_label_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'New Label Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#202020'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_feature_label_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Feature Label Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_feature_label_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Feature Label Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#f7941d'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_outstock_label_text_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'OutStock Label Text Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#ffffff'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_product_outstock_label_background_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'OutStock Label Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#989898'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'      => 'info-product-message-colors'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Add To Cart Message Colors', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       => 'ts_add_to_cart_message_background'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Add To Cart Message Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#42924d'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_add_to_cart_message_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Add To Cart Message Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#f3f3f3'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_add_to_cart_message_error_background'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Add To Cart Message Error Background Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#e5534c'
			,'alpha'	=> 1
		)
	)
	,array(
		'id'       => 'ts_add_to_cart_message_error_color'
		,'type'     => 'color_rgba'
		,'title'    => esc_html__( 'Add To Cart Message Error Color', 'drile' )
		,'subtitle' => ''
		,'default'  => array(
			'color' 	=> '#f3f3f3'
			,'alpha'	=> 1
		)
	)	
);

/*** Typography Tab ***/
$option_fields['typography'] = array(
	array(
		'id'        => 'section-fonts'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Fonts', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       			=> 'ts_body_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Body Font', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-style'   	=> true
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing' 	=> true
		,'preview'			=> array('always_display' => true)
		,'default'  		=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '500'
			,'font-size'   		=> '14px'
			,'line-height' 		=> '26px'
			,'letter-spacing' 	=> '0.5px'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_body_font_bold'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Body Font Bold', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-style'   	=> true
		,'text-align'   	=> false
		,'line-height'  	=> false
		,'font-size'  		=> false
		,'letter-spacing' 	=> false
		,'color'   			=> false
		,'preview'			=> array('always_display' => true)
		,'default'  		=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '600'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_heading_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Heading Font', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-size'    	=> false
		,'font-style'   	=> true
		,'text-align'   	=> false
		,'line-height'  	=> false
		,'color'   			=> false
		,'letter-spacing' 	=> false
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '700'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_heading_font_thin'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Heading Font Light', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-style'   	=> true
		,'font-size'    	=> false
		,'text-align'   	=> false
		,'line-height'  	=> false
		,'color'   			=> false
		,'letter-spacing' 	=> false
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '200'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_menu_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Menu Font', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-style'   	=> true
		,'text-align'   	=> false
		,'color'   			=> false
		,'line-height'   	=> false
		,'letter-spacing' 	=> true
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '500'
			,'font-size'   		=> '14px'
			,'letter-spacing' 	=> '0.5px'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_sub_menu_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Sub Menu Font', 'drile' )
		,'subtitle' 		=> ''
		,'google'   		=> true
		,'font-style'   	=> true
		,'text-align'   	=> false
		,'color'   			=> false
		,'line-height'   	=> false
		,'letter-spacing' 	=> true
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '500'
			,'font-size'   		=> '14px'
			,'letter-spacing' 	=> '0.5px'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_menu_special_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Menu Special Font', 'drile' )
		,'subtitle' 		=> esc_html__( 'The Menu Special Font option is only used for Header Layout 2', 'drile' )
		,'google'   		=> true
		,'font-style'   	=> true
		,'font-size'    	=> true
		,'line-height'  	=> true
		,'text-align'   	=> false
		,'color'   			=> false
		,'line-height'   	=> false
		,'letter-spacing' 	=> true
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Frank Ruhl Libre'
			,'font-weight' 		=> '700'
			,'font-size'   		=> '30px'
			,'letter-spacing' 	=> '0.5px'
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'       			=> 'ts_sidebar_menu_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Sidebar Menu Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> true
		,'font-style'   	=> true
		,'font-size'    	=> true
		,'line-height'  	=> true
		,'text-align'   	=> false
		,'color'   			=> false
		,'line-height'   	=> false
		,'letter-spacing' 	=> false
		,'preview'			=> array('always_display' => true)
		,'default'  			=> array(
			'font-family'  		=> 'Montserrat'
			,'font-weight' 		=> '400'
			,'font-size'   		=> '18px'
			,'letter-spacing' 	=> ''
			,'font-style'   	=> ''
			,'google'	   		=> true
		)
		,'fonts'	=> $family_fonts
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 20)
	)
	,array(
		'id'        => 'section-custom-font'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Custom Font', 'drile' )
		,'subtitle' => esc_html__( 'If you get the error message \'Sorry, this file type is not permitted for security reasons\', you can add this line define(\'ALLOW_UNFILTERED_UPLOADS\', true); to the wp-config.php file', 'drile' )
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_custom_font_ttf'
		,'type'     => 'media'
		,'url'      => true
		,'preview'  => false
		,'title'    => esc_html__( 'Custom Font ttf', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Upload the .ttf font file. To use it, you select CustomFont in the Standard Fonts group', 'drile' )
		,'default'  => array( 'url' => '' )
		,'mode'		=> 'application'
	)
	
	,array(
		'id'        => 'section-font-sizes'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Font Sizes', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'      => 'info-font-size-pc'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Font size on PC', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       			=> 'ts_h1_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H1 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '48px'
			,'line-height' => '50px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_h2_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H2 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '36px'
			,'line-height' => '42px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_h3_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H3 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '24px'
			,'line-height' => '30px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_h4_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H4 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '20px'
			,'line-height' => '30px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_h5_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H5 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  		=> ''
			,'font-weight'		=> ''
			,'font-size'   		=> '18px'
			,'line-height' 		=> '26px'
			,'google'	   		=> false
		)
	)
	,array(
		'id'       			=> 'ts_h6_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'H6 Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '16px'
			,'line-height' => '26px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_button_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Button Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'line-height'   	=> true
		,'color'   			=> false
		,'letter-spacing'  	=> true
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  		=> ''
			,'font-weight' 		=> ''
			,'font-size'   		=> '14px'
			,'line-height' 		=> '26px'
			,'letter-spacing'   => '0.5px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_heading_spacing'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Special Heading Letter Spacing', 'drile' )
		,'subtitle' 		=> esc_html__( 'Apply to h1, h2, h3', 'drile' )
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight' 	 	=> false
		,'font-size'   		=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'line-height'  	=> false
		,'letter-spacing'  	=> true
		,'color'   			=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  		=> ''
			,'font-weight' 		=> ''
			,'font-size'   		=> ''
			,'letter-spacing' 	=> '1px'
			,'google'	   => false
		)
	)
	,array(
		'id'      => 'info-font-size-ipad'
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Font size on Device', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'       		=> 'ts_h1_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H1 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '30px'
			,'line-height' => '36px'
			,'google'	   => false
		)
	)
	,array(
		'id'       		=> 'ts_h2_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H2 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '24px'
			,'line-height' => '30px'
			,'google'	   => false
		)
	)
	,array(
		'id'       		=> 'ts_h3_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H3 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '20px'
			,'line-height' => '26px'
			,'google'	   => false
		)
	)
	,array(
		'id'       		=> 'ts_h4_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H4 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '18px'
			,'line-height' => '26px'
			,'google'	   => false
		)
	)
	,array(
		'id'       		=> 'ts_h5_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H5 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '16px'
			,'line-height' => '24px'
			,'google'	   => false
		)
	)
	,array(
		'id'       		=> 'ts_h6_ipad_font'
		,'type'     	=> 'typography'
		,'title'    	=> esc_html__( 'H6 Font Size', 'drile' )
		,'subtitle' 	=> ''
		,'class' 		=> 'typography-no-preview'
		,'google'   	=> false
		,'font-family'  => false
		,'font-weight'  => false
		,'font-style'   => false
		,'text-align'   => false
		,'color'   		=> false
		,'preview'		=> array('always_display' => false)
		,'default'  	=> array(
			'font-family'  => ''
			,'font-weight' => ''
			,'font-size'   => '14px'
			,'line-height' => '20px'
			,'google'	   => false
		)
	)
	,array(
		'id'       			=> 'ts_button_ipad_font'
		,'type'     		=> 'typography'
		,'title'    		=> esc_html__( 'Button Font Size', 'drile' )
		,'subtitle' 		=> ''
		,'class' 			=> 'typography-no-preview'
		,'google'   		=> false
		,'font-family'  	=> false
		,'font-weight'  	=> false
		,'font-style'   	=> false
		,'text-align'   	=> false
		,'line-height'   	=> true
		,'color'   			=> false
		,'letter-spacing'  	=> false
		,'preview'			=> array('always_display' => false)
		,'default'  		=> array(
			'font-family'  		=> ''
			,'font-weight' 		=> ''
			,'font-size'   		=> '12px'
			,'line-height' 		=> '24px'
			,'letter-spacing'   => ''
			,'google'	   => false
		)
	)
);

/*** Header Tab ***/
$option_fields['header'] = array(
	array(
		'id'        => 'section-header-options'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Header Options', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_header_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Header Layout', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $header_layout_options
		,'default'  => 'v1'
	)
	,array(
		'id'        => 'ts_enable_sticky_header'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Sticky Header', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_enable_toggle_header_top'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Top Header Toggle', 'drile' )
		,'subtitle' => esc_html__( 'Only available on some header layouts', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_header_contact_information'
		,'type'     => 'textarea'
		,'title'    => esc_html__( 'Header Contact Information', 'drile' )
		,'subtitle' => esc_html__( 'You can add welcome text, email, phone number...', 'drile' )
		,'desc'     => ''
		,'validate' => 'html'
		,'default'  => ''
	)
	,array(
		'id'        => 'ts_enable_tiny_wishlist'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Wishlist', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_header_currency'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Header Currency', 'drile' )
		,'subtitle' => esc_html__( 'If you don\'t install WooCommerce Multilingual plugin, it will display demo html', 'drile' )
		,'default'  => false
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_header_language'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Header Language', 'drile' )
		,'subtitle' => esc_html__( 'If you don\'t install WPML plugin, it will display demo html', 'drile' )
		,'default'  => false
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_enable_tiny_account'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'My Account', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_enable_search'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Search Bar', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'       	=> 'ts_search_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Search Style', 'drile' )
		,'subtitle' => esc_html__( 'The Overlap Menu option is only used for Header Layout 1, 3', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'search-default'			=> esc_html__( 'Overlap Menu', 'drile' )
			,'search-sidebar'			=> esc_html__( 'Sidebar', 'drile' )
			,'search-fullwidth'			=> esc_html__( 'Fullwidth', 'drile' )
			,'search-fullscreen'		=> esc_html__( 'Fullscreen', 'drile' )
			
		)
		,'default'  => 'search-fullwidth'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_enable_search', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_enable_tiny_shopping_cart'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Shopping Cart', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_shopping_cart_sidebar'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Shopping Cart Sidebar', 'drile' )
		,'subtitle' => esc_html__( 'Show shopping cart in sidebar instead of dropdown. You need to update cart after changing', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
		,'required'	=> array( 'ts_enable_tiny_shopping_cart', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_show_shopping_cart_after_adding'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Show Shopping Cart After Adding Product To Cart', 'drile' )
		,'subtitle' => esc_html__( 'You need to enable Ajax add to cart in WooCommerce > Settings > Products', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
		,'required'	=> array( 'ts_shopping_cart_sidebar', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_add_to_cart_effect'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Add To Cart Effect', 'drile' )
		,'subtitle' => esc_html__( 'You need to enable Ajax add to cart in WooCommerce > Settings > Products. If "Show Shopping Cart After Adding Product To Cart" is enabled, this option will be disabled', 'drile' )
		,'options'  => array(
			''				=> esc_html__( 'None', 'drile' )
			,'fly_to_cart'	=> esc_html__( 'Fly To Cart', 'drile' )
			,'show_popup'	=> esc_html__( 'Show Popup', 'drile' )
		)
		,'default'  => ''
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	
	,array(
		'id'      => ''
		,'type'   => 'info'
		,'notice' => false
		,'title'  => esc_html__( 'Header Social Icons', 'drile' )
		,'desc'   => ''
	)
	,array(
		'id'        => 'ts_enable_header_social_icons'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Header Social Icons', 'drile' )
		,'subtitle' => esc_html__( 'Some header layouts don\'t include the social icons', 'drile' )
		,'default'  => false
		,'on'		=> esc_html__( 'Enable', 'drile' )
		,'off'		=> esc_html__( 'Disable', 'drile' )
	)
	,array(
		'id'        => 'ts_facebook_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Facebook URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_twitter_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Twitter URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_youtube_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Youtube URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_instagram_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Instagram URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_linkedin_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'LinkedIn URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_custom_social_url'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Custom Icon URL', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_custom_social_class'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Custom Icon Class', 'drile' )
		,'subtitle' => esc_html__( 'Use Font Awesome 5 Free. Ex: fab fa-facebook-f', 'drile' )
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_enable_header_social_icons', 'equals', '1' )
	)
	
	,array(
		'id'        => 'section-breadcrumb-options'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Breadcrumb Options', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_breadcrumb_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Breadcrumb Layout', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $breadcrumb_layout_options
		,'default'  => 'v2'
	)
	,array(
		'id'        => 'ts_enable_breadcrumb_background_image'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Breadcrumbs Background Image', 'drile' )
		,'subtitle' => esc_html__( 'You can set background color by going to Color Scheme tab > Breadcrumb Colors section', 'drile' )
		,'default'  => true
	)
	,array(
		'id'        => 'ts_bg_breadcrumbs'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Breadcrumbs Background Image', 'drile' )
		,'desc'     => ''
		,'subtitle' => esc_html__( 'Select a new image to override the default background image', 'drile' )
		,'readonly' => false
		,'default'  => array( 'url' => '' )
	)
	,array(
		'id'        => 'ts_breadcrumb_bg_parallax'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Breadcrumbs Background Parallax', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
);

/*** Footer Tab ***/
$option_fields['footer'] = array(
	array(
		'id'       	=> 'ts_first_footer_area'
		,'type'     => 'select'
		,'title'    => esc_html__( 'First Footer Area', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $footer_block_options
		,'default'  => '0'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_second_footer_area'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Second Footer Area', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $footer_block_options
		,'default'  => '0'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
);

/*** Menu Tab ***/
$option_fields['menu'] = array(
	array(
		'id'             => 'ts_menu_num_widget'
		,'type'          => 'slider'
		,'title'         => esc_html__( 'Mega Menu Widget Area', 'drile' )
		,'subtitle'      => esc_html__( 'Number Of Widget Areas Available', 'drile' )
		,'desc'          => esc_html__( 'Min: 1, max: 30, step: 1, default value: 6', 'drile' )
		,'default'       => 12
		,'min'           => 1
		,'step'          => 1
		,'max'           => 30
		,'display_value' => 'text'
	)
	,array(
		'id'             => 'ts_menu_thumb_width'
		,'type'          => 'slider'
		,'title'         => esc_html__( 'Menu Thumbnail Width', 'drile' )
		,'subtitle'      => ''
		,'desc'          => esc_html__( 'Min: 5, max: 50, step: 1, default value: 46', 'drile' )
		,'default'       => 46
		,'min'           => 5
		,'step'          => 1
		,'max'           => 50
		,'display_value' => 'text'
	)
	,array(
		'id'             => 'ts_menu_thumb_height'
		,'type'          => 'slider'
		,'title'         => esc_html__( 'Menu Thumbnail Height', 'drile' )
		,'subtitle'      => ''
		,'desc'          => esc_html__( 'Min: 5, max: 50, step: 1, default value: 46', 'drile' )
		,'default'       => 46
		,'min'           => 5
		,'step'          => 1
		,'max'           => 50
		,'display_value' => 'text'
	)
);

/*** Blog Tab ***/
$option_fields['blog'] = array(
	array(
		'id'        => 'section-blog'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Blog', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_blog_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Blog Layout', 'drile' )
		,'subtitle' => esc_html__( 'This option is available when Front page displays the latest posts', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'0-1-0' => array(
				'alt'  => esc_html__('Fullwidth', 'drile')
				,'img' => $redux_url . 'assets/img/1col.png'
			)
			,'1-1-0' => array(
				'alt'  => esc_html__('Left Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cl.png'
			)
			,'0-1-1' => array(
				'alt'  => esc_html__('Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cr.png'
			)
			,'1-1-1' => array(
				'alt'  => esc_html__('Left & Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/3cm.png'
			)
		)
		,'default'  => '0-1-0'
	)
	,array(
		'id'       	=> 'ts_blog_left_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Left Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'blog-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_blog_right_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Right Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'blog-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_blog_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Blog Style', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'0'			=> esc_html__( 'Default', 'drile' )
			,'list'		=> esc_html__( 'List', 'drile' )
		)
		,'default'  => ''
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_blog_thumbnail'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Thumbnail', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_date'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Date', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_title'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Title', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_author'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Author', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_comment'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Comment', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_read_more'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Read More Button', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_categories'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Categories', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_excerpt'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Excerpt', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_excerpt_strip_tags'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Excerpt Strip All Tags', 'drile' )
		,'subtitle' => esc_html__( 'Strip all html tags in Excerpt', 'drile' )
		,'default'  => false
	)
	,array(
		'id'        => 'ts_blog_excerpt_max_words'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Blog Excerpt Max Words', 'drile' )
		,'subtitle' => esc_html__( 'Input -1 to show full excerpt', 'drile' )
		,'desc'     => ''
		,'default'  => '-1'
	)
	
	,array(
		'id'        => 'section-blog-details'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Blog Details', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_blog_details_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Blog Details Layout', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'0-1-0' => array(
				'alt'  => esc_html__('Fullwidth', 'drile')
				,'img' => $redux_url . 'assets/img/1col.png'
			)
			,'1-1-0' => array(
				'alt'  => esc_html__('Left Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cl.png'
			)
			,'0-1-1' => array(
				'alt'  => esc_html__('Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cr.png'
			)
			,'1-1-1' => array(
				'alt'  => esc_html__('Left & Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/3cm.png'
			)
		)
		,'default'  => '0-1-0'
	)
	,array(
		'id'       	=> 'ts_blog_details_left_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Left Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'blog-detail-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_blog_details_right_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Right Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'blog-detail-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_blog_details_breadcrumb'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Breadcrumb', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_next_prev_navigation'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Next/Prev Navigation', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_thumbnail'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Thumbnail', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_date'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Date', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_title'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Title', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_author'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Author', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_comment'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Comment', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_content'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Content', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_tags'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Tags', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_categories'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Categories', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_sharing'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Sharing', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_sharing_sharethis'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Sharing - Use ShareThis', 'drile' )
		,'subtitle' => esc_html__( 'Use share buttons from sharethis.com. You need to add key below', 'drile')
		,'default'  => true
		,'required'	=> array( 'ts_blog_details_sharing', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_blog_details_sharing_sharethis_key'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Blog Sharing - ShareThis Key', 'drile' )
		,'subtitle' => esc_html__( 'You get it from script code. It is the value of "property" attribute', 'drile' )
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_blog_details_sharing', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_blog_details_author_box'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Author Box', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_related_posts'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Related Posts', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_blog_details_comment_form'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Blog Comment Form', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
);

/*** Portfolio Details Tab ***/
$option_fields['portfolio-details'] = array(
	array(
		'id'       	=> 'ts_portfolio_page'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Portfolio Page', 'drile' )
		,'subtitle' => esc_html__( 'Select the page which displays the list of portfolios. You also need to add our portfolio shortcode to that page', 'drile' )
		,'desc'     => ''
		,'data'     => 'pages'
		,'default'	=> ''
	)	
	,array(
		'id'       	=> 'ts_portfolio_thumbnail_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Thumbnail Style', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'slider'	=> esc_html__( 'Slider', 'drile' )
			,'gallery'	=> esc_html__( 'Gallery', 'drile' )
		)
		,'default'	=> 'slider'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_portfolio_details_next_prev_navigation'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Next/Prev Navigation', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_thumbnail'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Thumbnail', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_title'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Title', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_likes'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Likes', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_content'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Content', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_client'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Client', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_year'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Year', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_url'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio URL', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_categories'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Categories', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_sharing'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Sharing', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_related_posts'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Related Posts', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_custom_field'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Portfolio Custom Field', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_portfolio_custom_field_title'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Portfolio Custom Field Title', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Custom Field'
		,'required'	=> array( 'ts_portfolio_custom_field', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_portfolio_custom_field_content'
		,'type'     => 'editor'
		,'title'    => esc_html__( 'Portfolio Custom Field Content', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Custom content goes here'
		,'args'     => array(
			'wpautop'        => false
			,'media_buttons' => false
			,'textarea_rows' => 5
			,'teeny'         => false
			,'quicktags'     => false
		)
		,'required'	=> array( 'ts_portfolio_custom_field', 'equals', '1' )
	)
);

/*** WooCommerce Tab ***/
$option_fields['woocommerce'] = array(
	array(
		'id'        => 'section-product-label'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Label', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       	=> 'ts_product_label_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Label Style', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'circle' 		=> esc_html__( 'Circle', 'drile' )
			,'square' 		=> esc_html__( 'Square', 'drile' )
			,'rectangle' 	=> esc_html__( 'Rectangle', 'drile' )
		)
		,'default'  => 'rectangle'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_product_show_new_label'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product New Label', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_product_new_label_text'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product New Label Text', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'New'
		,'required'	=> array( 'ts_product_show_new_label', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_product_show_new_label_time'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product New Label Time', 'drile' )
		,'subtitle' => esc_html__( 'Number of days which you want to show New label since product is published', 'drile' )
		,'desc'     => ''
		,'default'  => '30'
		,'required'	=> array( 'ts_product_show_new_label', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_product_sale_label_text'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Sale Label Text', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Sale'
	)
	,array(
		'id'        => 'ts_product_feature_label_text'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Feature Label Text', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Hot'
	)
	,array(
		'id'        => 'ts_product_out_of_stock_label_text'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Out Of Stock Label Text', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Sold out'
	)
	,array(
		'id'       	=> 'ts_show_sale_label_as'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Show Sale Label As', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'text' 		=> esc_html__( 'Text', 'drile' )
			,'number' 	=> esc_html__( 'Number', 'drile' )
			,'percent' 	=> esc_html__( 'Percent', 'drile' )
		)
		,'default'  => 'text'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	
	,array(
		'id'        => 'section-product-rating'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Rating', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       	=> 'ts_product_rating_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Rating Style', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'border' 		=> esc_html__( 'Border', 'drile' )
			,'fill' 		=> esc_html__( 'Fill', 'drile' )
		)
		,'default'  => 'border'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	
	,array(
		'id'        => 'section-product-hover'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Hover', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       	=> 'ts_product_hover_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Hover Style', 'drile' )
		,'subtitle' => esc_html__( 'Select the style of buttons/icons when hovering on product', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'style-1' 		=> esc_html__( 'Horizontal Style', 'drile' )
			,'style-2' 		=> esc_html__( 'Vertical Style', 'drile' )
			,'style-3' 		=> esc_html__( 'Push Style', 'drile' )
		)
		,'default'  => 'style-2'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_effect_product'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Back Product Image', 'drile' )
		,'subtitle' => esc_html__( 'Show another product image on hover. It will show an image from Product Gallery', 'drile' )
		,'default'  => true
	)
	,array(
		'id'        => 'ts_product_tooltip'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Tooltip', 'drile' )
		,'subtitle' => esc_html__( 'Show tooltip when hovering on buttons/icons on product', 'drile' )
		,'default'  => true
	)
	
	,array(
		'id'        => 'section-lazy-load'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Lazy Load', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_prod_lazy_load'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Activate Lazy Load', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'        => 'ts_prod_placeholder_img'
		,'type'     => 'media'
		,'url'      => true
		,'title'    => esc_html__( 'Placeholder Image', 'drile' )
		,'desc'     => ''
		,'subtitle' => ''
		,'readonly' => false
		,'default'  => array( 'url' => $product_loading_image )
	)
	
	,array(
		'id'        => 'section-quickshop'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Quickshop', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_enable_quickshop'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Activate Quickshop', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'       	=> 'ts_quickshop_image_layout'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Image Layout', 'drile' )
		,'subtitle' => esc_html__( 'If you select Small Thumbnails, it will use the Thumbnails Style option of Product Details', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'full-slider' 		=> esc_html__( 'Full Slider', 'drile' )
			,'small-thumbnails' => esc_html__( 'Small Thumbnails', 'drile' )
		)
		,'default'  => 'full-slider'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_enable_quickshop', 'equals', '1' )
	)
	
	,array(
		'id'        => 'section-catalog-mode'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Catalog Mode', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_enable_catalog_mode'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Catalog Mode', 'drile' )
		,'subtitle' => esc_html__( 'Hide all Add To Cart buttons on your site. You can also hide Shopping cart by going to Header tab > turn Shopping Cart option off', 'drile' )
		,'default'  => false
	)
	
	,array(
		'id'        => 'section-ajax-search'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Ajax Search', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_ajax_search'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Enable Ajax Search', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'        => 'ts_ajax_search_number_result'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Number Of Results', 'drile' )
		,'subtitle' => esc_html__( 'Input -1 to show all results', 'drile' )
		,'desc'     => ''
		,'default'  => '4'
	)
);

/*** Shop/Product Category Tab ***/
$option_fields['shop-product-category'] = array(
	array(
		'id'        => 'ts_prod_cat_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Shop/Product Category Layout', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'0-1-0' => array(
				'alt'  => esc_html__('Fullwidth', 'drile')
				,'img' => $redux_url . 'assets/img/1col.png'
			)
			,'1-1-0' => array(
				'alt'  => esc_html__('Left Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cl.png'
			)
			,'0-1-1' => array(
				'alt'  => esc_html__('Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cr.png'
			)
			,'1-1-1' => array(
				'alt'  => esc_html__('Left & Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/3cm.png'
			)
		)
		,'default'  => '0-1-0'
	)
	,array(
		'id'       	=> 'ts_prod_cat_left_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Left Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'product-category-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_prod_cat_right_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Right Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'product-category-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_prod_cat_columns'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Columns', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			3	=> 3
			,4	=> 4
			,5	=> 5
			,6	=> 6
		)
		,'default'  => '4'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_prod_cat_per_page'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Products Per Page', 'drile' )
		,'subtitle' => esc_html__( 'Number of products per page', 'drile' )
		,'desc'     => ''
		,'default'  => '16'
	)
	,array(
		'id'       	=> 'ts_prod_cat_loading_type'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Loading Type', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'default'	=> esc_html__( 'Default', 'drile' )
			,'infinity-scroll'	=> esc_html__( 'Infinity Scroll', 'drile' )
			,'load-more-button'	=> esc_html__( 'Load More Button', 'drile' )
			,'ajax-pagination'	=> esc_html__( 'Ajax Pagination', 'drile' )
		)
		,'default'  => 'load-more-button'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_prod_cat_per_page_dropdown'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Products Per Page Dropdown', 'drile' )
		,'subtitle' => esc_html__( 'Allow users to select number of products per page', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_zoom_in_out'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Zoom In/Out', 'drile' )
		,'subtitle' => esc_html__( 'Allow users to zoom in/out the product container', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_filter_widget_area'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Filter Widget Area', 'drile' )
		,'subtitle' => esc_html__( 'Shows widgets in Filter Widget Area', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'       	=> 'ts_filter_widget_area_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Filter Widget Area Style', 'drile' )
		,'subtitle' => esc_html__( 'If you choose Sidebar, it will not show the sidebar of the shop/product category page', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'default'	=> esc_html__( 'Default', 'drile' )
			,'dropdown'	=> esc_html__( 'Dropdown', 'drile' )
			,'sidebar'	=> esc_html__( 'Sidebar', 'drile' )
			,'bottom'	=> esc_html__( 'Bottom', 'drile' )
		)
		,'default'  => 'default'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_filter_widget_area', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_cat_thumbnail'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Thumbnail', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_thumbnail_border'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Thumbnail Border', 'drile' )
		,'subtitle' => esc_html__( 'Add border to product thumbnail', 'drile' )
		,'default'  => false
		,'required'	=> array( 'ts_prod_cat_thumbnail', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_cat_label'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Label', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_brand'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Brands', 'drile' )
		,'subtitle' => esc_html__( 'Add brands to product list on all pages', 'drile' )
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_cat'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Categories', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_title'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Title', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_sku'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product SKU', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_rating'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Rating', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_price'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Price', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_add_to_cart'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Add To Cart Button', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_desc'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Short Description', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat_desc_words'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Short Description - Limit Words', 'drile' )
		,'subtitle' => esc_html__( 'It is also used for product shortcode', 'drile' )
		,'desc'     => ''
		,'default'  => '8'
	)
	,array(
		'id'        => 'ts_prod_cat_color_swatch'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Color Swatches', 'drile' )
		,'subtitle' => esc_html__( 'Show the color attribute of variations. The slug of the color attribute has to be "color"', 'drile' )
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'       	=> 'ts_prod_cat_number_color_swatch'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Number Of Color Swatches', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			2	=> 2
			,3	=> 3
			,4	=> 4
			,5	=> 5
			,6	=> 6
		)
		,'default'  => '3'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_prod_cat_color_swatch', 'equals', '1' )
	)
);

/*** Product Details Tab ***/
$option_fields['product-details'] = array(
	array(
		'id'        => 'ts_prod_layout'
		,'type'     => 'image_select'
		,'title'    => esc_html__( 'Product Layout', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'0-1-0' => array(
				'alt'  => esc_html__('Fullwidth', 'drile')
				,'img' => $redux_url . 'assets/img/1col.png'
			)
			,'1-1-0' => array(
				'alt'  => esc_html__('Left Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cl.png'
			)
			,'0-1-1' => array(
				'alt'  => esc_html__('Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/2cr.png'
			)
			,'1-1-1' => array(
				'alt'  => esc_html__('Left & Right Sidebar', 'drile')
				,'img' => $redux_url . 'assets/img/3cm.png'
			)
		)
		,'default'  => '0-1-0'
	)
	,array(
		'id'       	=> 'ts_prod_left_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Left Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'product-detail-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_prod_right_sidebar'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Right Sidebar', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => $sidebar_options
		,'default'  => 'product-detail-sidebar'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_prod_layout_fullwidth'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Layout Fullwidth', 'drile' )
		,'subtitle' => esc_html__( 'Override the Layout Fullwidth option in the General tab', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'default'	=> esc_html__( 'Default', 'drile' )
			,'0'		=> esc_html__( 'No', 'drile' )
			,'1'		=> esc_html__( 'Yes', 'drile' )
		)
		,'default'  => '1'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_prod_header_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Header Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'required'	=> array( 'ts_prod_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_main_content_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Main Content Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'required'	=> array( 'ts_prod_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_footer_layout_fullwidth'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Footer Layout Fullwidth', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'required'	=> array( 'ts_prod_layout_fullwidth', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_breadcrumb'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Breadcrumb', 'drile' )
		,'subtitle' => ''
		,'default'  => true
	)
	,array(
		'id'       	=> 'ts_prod_thumbnail_layout'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Thumbnail Layout', 'drile' )
		,'subtitle' => esc_html__( 'The Grid, Slider layouts do not support cloudzoom', 'drile' )
		,'desc'     => ''
		,'options'  => array(
			'default'	=> esc_html__( 'Default', 'drile' )
			,'grid'		=> esc_html__( 'Grid', 'drile' )
			,'slider'	=> esc_html__( 'Slider', 'drile' )
		)
		,'default'  => 'default'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_prod_thumbnail_slider_layout_on_mobile'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Thumbnail Slider Layout On Mobile/Tablet', 'drile' )
		,'subtitle' => esc_html__( 'Change Grid to Slider layout on Mobile/Tablet', 'drile' )
		,'default'  => true
		,'required'	=> array( 'ts_prod_thumbnail_layout', 'equals', 'grid' )
	)
	,array(
		'id'        => 'ts_prod_summary_scrolling'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Summary Scrolling', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'required'	=> array( 'ts_prod_thumbnail_layout', 'equals', 'grid' )
	)
	,array(
		'id'        => 'ts_prod_cloudzoom'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Cloud Zoom', 'drile' )
		,'subtitle' => esc_html__( 'If you turn it off, product gallery images will open in a lightbox', 'drile' )
		,'default'  => true
	)
	,array(
		'id'        => 'ts_prod_attr_dropdown'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Attribute Dropdown', 'drile' )
		,'subtitle' => esc_html__( 'If you turn it off, the dropdown will be replaced by image or text label', 'drile' )
		,'default'  => false
	)
	,array(
		'id'        => 'ts_prod_attr_color_text'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Attribute Color Text', 'drile' )
		,'subtitle' => esc_html__( 'Show text for the Color attribute instead of color/color image', 'drile' )
		,'default'  => false
		,'required'	=> array( 'ts_prod_attr_dropdown', 'equals', '0' )
	)
	,array(
		'id'        => 'ts_prod_next_prev_navigation'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Next/Prev Product Navigation', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_thumbnail'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Thumbnail', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_thumbnail_border'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Thumbnail Border', 'drile' )
		,'subtitle' => esc_html__( 'Add border to main thumbnail and gallery', 'drile' )
		,'default'  => false
		,'required'	=> array( 'ts_prod_thumbnail', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_label'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Label', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_title'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Title', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_title_in_content'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Title In Content', 'drile' )
		,'subtitle' => esc_html__( 'Display the product title in the page content instead of above the breadcrumbs', 'drile' )
		,'default'  => true
	)
	,array(
		'id'        => 'ts_prod_rating'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Rating', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_sku'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product SKU', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_availability'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Availability', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_excerpt'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Excerpt', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_count_down'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Count Down', 'drile' )
		,'subtitle' => esc_html__( 'You have to activate ThemeSky plugin', 'drile' )
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_price'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Price', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_add_to_cart'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Add To Cart Button', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_ajax_add_to_cart'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Ajax Add To Cart', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'required'	=> array( 'ts_prod_add_to_cart', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_brand'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Brands', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_cat'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Categories', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_tag'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Tags', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_more_less_content'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product More/Less Content', 'drile' )
		,'subtitle' => esc_html__( 'Show more/less content in the Description tab', 'drile' )
		,'default'  => true
	)
	,array(
		'id'        => 'ts_prod_sharing'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Sharing', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_sharing_sharethis'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Sharing - Use ShareThis', 'drile' )
		,'subtitle' => esc_html__( 'Use share buttons from sharethis.com. You need to add key below', 'drile' )
		,'default'  => false
		,'required'	=> array( 'ts_prod_sharing', 'equals', '1' )
	)
	,array(
		'id'        => 'ts_prod_sharing_sharethis_key'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Sharing - ShareThis Key', 'drile' )
		,'subtitle' => esc_html__( 'You get it from script code. It is the value of "property" attribute', 'drile' )
		,'desc'     => ''
		,'default'  => ''
		,'required'	=> array( 'ts_prod_sharing', 'equals', '1' )
	)
	
	,array(
		'id'        => 'section-product-thumbnails'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Thumbnails', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'       	=> 'ts_prod_thumbnails_style'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Thumbnails Style', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'vertical'		=> esc_html__( 'Vertical', 'drile' )
			,'horizontal'	=> esc_html__( 'Horizontal', 'drile' )
		)
		,'default'  => 'vertical'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'       	=> 'ts_prod_thumbnails_position'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Thumbnails Position', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'left'		=> esc_html__( 'Left', 'drile' )
			,'right'	=> esc_html__( 'Right', 'drile' )
		)
		,'default'  => 'left'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
		,'required'	=> array( 'ts_prod_thumbnails_style', 'equals', 'vertical' )
	)
	
	,array(
		'id'        => 'section-product-tabs'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Product Tabs', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_prod_tabs'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Tabs', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_accordion_tabs'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Tabs As Accordion', 'drile' )
		,'subtitle' => ''
		,'default'  => false
	)
	,array(
		'id'       	=> 'ts_prod_tabs_position'
		,'type'     => 'select'
		,'title'    => esc_html__( 'Product Tabs Position', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'options'  => array(
			'after_summary'		=> esc_html__( 'After Summary', 'drile' )
			,'inside_summary'	=> esc_html__( 'Inside Summary', 'drile' )
		)
		,'default'  => 'after_summary'
		,'select2'	=> array('allowClear' => false, 'minimumResultsForSearch' => 'Infinity')
	)
	,array(
		'id'        => 'ts_prod_custom_tab'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Product Custom Tab', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_custom_tab_title'
		,'type'     => 'text'
		,'title'    => esc_html__( 'Product Custom Tab Title', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => 'Custom tab'
	)
	,array(
		'id'        => 'ts_prod_custom_tab_content'
		,'type'     => 'editor'
		,'title'    => esc_html__( 'Product Custom Tab Content', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => esc_html__( 'Your custom content goes here. You can add the content for individual product', 'drile' )
		,'args'     => array(
			'wpautop'        => false
			,'media_buttons' => false
			,'textarea_rows' => 5
			,'teeny'         => false
			,'quicktags'     => false
		)
	)
	
	,array(
		'id'        => 'section-ads-banner'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Ads Banner', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_prod_ads_banner'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Ads Banner', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_ads_banner_content'
		,'type'     => 'editor'
		,'title'    => esc_html__( 'Ads Banner Content', 'drile' )
		,'subtitle' => ''
		,'desc'     => ''
		,'default'  => ''
		,'args'     => array(
			'wpautop'        => false
			,'media_buttons' => false
			,'textarea_rows' => 5
			,'teeny'         => false
			,'quicktags'     => false
		)
	)
	
	,array(
		'id'        => 'section-related-up-sell-products'
		,'type'     => 'section'
		,'title'    => esc_html__( 'Related - Up-Sell Products', 'drile' )
		,'subtitle' => ''
		,'indent'   => false
	)
	,array(
		'id'        => 'ts_prod_upsells'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Up-Sell Products', 'drile' )
		,'subtitle' => ''
		,'default'  => true
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
	,array(
		'id'        => 'ts_prod_related'
		,'type'     => 'switch'
		,'title'    => esc_html__( 'Related Products', 'drile' )
		,'subtitle' => ''
		,'default'  => false
		,'on'		=> esc_html__( 'Show', 'drile' )
		,'off'		=> esc_html__( 'Hide', 'drile' )
	)
);

/*** Custom Code Tab ***/
$option_fields['custom-code'] = array(
	array(
		'id'        => 'ts_custom_css_code'
		,'type'     => 'ace_editor'
		,'title'    => esc_html__( 'Custom CSS Code', 'drile' )
		,'subtitle' => ''
		,'mode'     => 'css'
		,'theme'    => 'monokai'
		,'desc'     => ''
		,'default'  => ''
	)
	,array(
		'id'        => 'ts_custom_javascript_code'
		,'type'     => 'ace_editor'
		,'title'    => esc_html__( 'Custom Javascript Code', 'drile' )
		,'subtitle' => ''
		,'mode'     => 'javascript'
		,'theme'    => 'monokai'
		,'desc'     => ''
		,'default'  => ''
	)
);