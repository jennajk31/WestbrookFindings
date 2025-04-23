<?php 
add_action( 'vc_before_init', 'drile_integrate_with_vc' );
function drile_integrate_with_vc() {
	
	if( !function_exists('vc_map') ){
		return;
	}

	/********************** Content Shortcodes ***************************/
	/*** TS Heading ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Heading', 'drile' ),
		'base' 		=> 'ts_heading',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Default', 'drile')						=>  'style-default'
							,esc_html__('Multiple Heading', 'drile')			=>  'style-multiple-heading'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Heading Size', 'drile' )
				,'param_name' 	=> 'size'
				,'admin_label' 	=> true
				,'value' 		=> array(
						'1'				=> '1'
						,'2'			=> '2'
						,'3'			=> '3'
						,'4'			=> '4'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Alignment', 'drile' )
				,'param_name' 	=> 'alignment'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Center', 'drile')						=>  'heading-center'
							,esc_html__('Left', 'drile')						=>  'heading-left'
							,esc_html__('Right', 'drile')						=>  'heading-right'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Heading', 'drile' )
				,'param_name' 	=> 'text'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Heading 2', 'drile' )
				,'param_name' 	=> 'text2'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency' => array('element' => 'style', 'value' => array('style-multiple-heading'))
				
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Style', 'drile' )
				,'param_name' 	=> 'text_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')			=> 'text-default'
							,esc_html__('Light', 'drile')			=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Button ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Button', 'drile' ),
		'base' 		=> 'ts_button',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Text', 'drile' )
				,'param_name' 	=> 'content'
				,'admin_label' 	=> true
				,'value' 		=> 'Button text'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> '#'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Self', 'drile')					=> '_self'
						,esc_html__('New Window Tab', 'drile')		=> '_blank'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Add icon', 'drile' )
				,'param_name' 	=> 'add_icon'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Icon library', 'drile' )
				,'param_name' 	=> 'icon_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Font Awesome 5 Free', 'drile')		=>  'fontawesome'
						,esc_html__('Linear', 'drile')					=>  'linear'
						)
				,'description' 	=> ''
				,'dependency' => array('element' => 'add_icon', 'value' => '1')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Font icon', 'drile' )
				,'param_name' 	=> 'font_icon'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'settings' 	=> array(
					'emptyIcon' 	=> true /* default true, display an "EMPTY" icon? */
					,'iconsPerPage' => 4000 /* default 100, how many icons per/page to display */
				)
				,'description' 	=> esc_html__('Add an icon before the text. Ex: fas fa-lock', 'drile')
				,'dependency' => array('element' => 'icon_type', 'value' => 'fontawesome')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_linear'
				,'admin_label' 	=> false
				,'value' 		=> 'lnr lnr-arrow-right'
				,'description' 	=> esc_html__( 'https://linearicons.com/free', 'drile' )
				,'dependency' => array('element' => 'icon_type', 'value' => 'linear')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Icon Alignment', 'drile' )
				,'param_name' 	=> 'icon_alignment'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Left', 'drile')			=> 'icon-left'
						,esc_html__('Right', 'drile')		=> 'icon-right'
						)
				,'description' 	=> ''
				,'dependency' => array('element' => 'add_icon', 'value' => '1')
			)			
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Text color', 'drile' )
				,'param_name' 	=> 'text_color'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Text color hover', 'drile' )
				,'param_name' 	=> 'text_color_hover'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Background color', 'drile' )
				,'param_name' 	=> 'bg_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Background color hover', 'drile' )
				,'param_name' 	=> 'bg_color_hover'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Border color', 'drile' )
				,'param_name' 	=> 'border_color'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Border color hover', 'drile' )
				,'param_name' 	=> 'border_color_hover'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Border width', 'drile' )
				,'param_name' 	=> 'border_width'
				,'admin_label' 	=> false
				,'value' 		=> '1'
				,'description' 	=> esc_html__('In pixels. Ex: 1', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Border Radius', 'drile' )
				,'param_name' 	=> 'border_radius'
				,'admin_label' 	=> false
				,'value' 		=> '0'
				,'description' 	=> esc_html__('In pixels. Ex: 5', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Button Size', 'drile' )
				,'param_name' 	=> 'size'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Small', 'drile')		=> 'small'
						,esc_html__('Medium', 'drile')		=> 'medium'
						,esc_html__('Large', 'drile')		=> 'large'
						,esc_html__('X-Large', 'drile')		=> 'x-large'
						)
				,'std' 			=> 'medium'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Button Alignment', 'drile' )
				,'param_name' 	=> 'alignment'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Default', 'drile')		=> 'btn-inline'
						,esc_html__('Left', 'drile')		=> 'ts-alignleft'
						,esc_html__('Center', 'drile')		=> 'ts-aligncenter'
						,esc_html__('Right', 'drile')		=> 'ts-alignright'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Features ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Feature', 'drile' ),
		'base' 		=> 'ts_feature',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Vertical Icon', 'drile')				=>  'vertical-icon'
						,esc_html__('Vertical Image', 'drile')				=>  'vertical-image'
						,esc_html__('Horizontal Icon', 'drile')				=>  'horizontal-icon'
						,esc_html__('Horizontal Image', 'drile')			=>  'horizontal-image'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Icon library', 'drile' )
				,'param_name' 	=> 'icon_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Font Awesome 5 Free', 'drile')		=>  'fontawesome'
						,esc_html__('Open Iconic', 'drile')				=>  'openiconic'
						,esc_html__('Typicons', 'drile')				=>  'typicons'
						,esc_html__('Entypo', 'drile')					=>  'entypo'
						,esc_html__('Linecons', 'drile')				=>  'linecons'
						,esc_html__('Material', 'drile')				=>  'material'
						,esc_html__('Linear', 'drile')					=>  'linear'
						)
				,'description' 	=> ''
				,'dependency' => array('element' => 'style', 'value' => array('vertical-icon', 'horizontal-icon'))
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_fontawesome'
				,'admin_label' 	=> false
				,'value' 		=> 'fa fa-laptop'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'fontawesome')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_openiconic'
				,'admin_label' 	=> false
				,'value' 		=> 'vc-oi vc-oi-dial'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'type' 		=> 'openiconic'
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'openiconic')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_typicons'
				,'admin_label' 	=> false
				,'value' 		=> 'typcn typcn-adjust-brightness'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'type' 		=> 'typicons'
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'typicons')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_entypo'
				,'admin_label' 	=> false
				,'value' 		=> 'entypo-icon entypo-icon-note'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'type' 		=> 'entypo'
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'entypo')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_linecons'
				,'admin_label' 	=> false
				,'value' 		=> 'vc_li vc_li-heart'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'type' 		=> 'linecons'
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'linecons')
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_material'
				,'admin_label' 	=> false
				,'value' 		=> 'vc-material vc-material-cake'
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'type' 		=> 'material'
					,'iconsPerPage' => 4000
				)
				,'dependency' => array('element' => 'icon_type', 'value' => 'material')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Icon', 'drile' )
				,'param_name' 	=> 'icon_linear'
				,'admin_label' 	=> false
				,'value' 		=> 'lnr lnr-heart'
				,'description' 	=> esc_html__( 'https://linearicons.com/free', 'drile' )
				,'dependency' => array('element' => 'icon_type', 'value' => 'linear')
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Icon Color', 'drile' )
				,'param_name' 	=> 'icon_color'
				,'admin_label' 	=> false
				,'value' 		=> '#666666'
				,'description' 	=> ''
				,'dependency' => array('element' => 'style', 'value' => array('horizontal-icon', 'vertical-icon'))
			)
			,array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Image', 'drile' )
				,'param_name' 	=> 'img_id'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency' => array('element' => 'style', 'value' => array('vertical-image', 'horizontal-image'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Image URL', 'drile' )
				,'param_name' 	=> 'img_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
				,'dependency' => array('element' => 'style', 'value' => array('vertical-image', 'horizontal-image'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Feature title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Short description', 'drile' )
				,'param_name' 	=> 'excerpt'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('New Window Tab', 'drile')		=>  '_blank'
						,esc_html__('Self', 'drile')				=>  '_self'	
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Style', 'drile' )
				,'param_name' 	=> 'text_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')			=> 'text-default'
							,esc_html__('Light', 'drile')			=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Image Box ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Image Box', 'drile' ),
		'base' 		=> 'ts_image_box',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Image', 'drile' )
				,'param_name' 	=> 'img_id'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Image URL', 'drile' )
				,'param_name' 	=> 'img_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image Position', 'drile' )
				,'param_name' 	=> 'image_position'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Left', 'drile')			=> 'image-left'
							,esc_html__('Right', 'drile')			=> 'image-right'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Short description', 'drile' )
				,'param_name' 	=> 'description'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Button text', 'drile' )
				,'param_name' 	=> 'button_text'
				,'admin_label' 	=> true
				,'value' 		=> 'shop now'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('New Window Tab', 'drile')	=>  '_blank'
						,esc_html__('Self', 'drile')				=>  '_self'	
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Social Icons ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Social Icons', 'drile' ),
		'base' 		=> 'ts_social',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'social_style'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Icon', 'drile')						=> 'style-icon'
							,esc_html__('Square', 'drile')					=> 'style-square'
							,esc_html__('Vertical', 'drile')				=> 'style-vertical'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Facebook URL', 'drile' )
				,'param_name' 	=> 'facebook_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Twitter URL', 'drile' )
				,'param_name' 	=> 'twitter_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)			
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Flickr URL', 'drile' )
				,'param_name' 	=> 'flickr_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Vimeo URL', 'drile' )
				,'param_name' 	=> 'vimeo_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Youtube URL', 'drile' )
				,'param_name' 	=> 'youtube_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Viber Number', 'drile' )
				,'param_name' 	=> 'viber_number'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Skype Username', 'drile' )
				,'param_name' 	=> 'skype_username'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Instagram URL', 'drile' )
				,'param_name' 	=> 'instagram_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Pinterest URL', 'drile' )
				,'param_name' 	=> 'pinterest_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Custom Link', 'drile' )
				,'param_name' 	=> 'custom_link'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Custom Text', 'drile' )
				,'param_name' 	=> 'custom_text'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'iconpicker'
				,'heading' 		=> esc_html__( 'Custom Icon', 'drile' )
				,'param_name' 	=> 'custom_font'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'settings' 	=> array(
					'emptyIcon' 	=> true
					,'iconsPerPage' => 4000
				)
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show Tooltip', 'drile' )
				,'param_name' 	=> 'show_tooltip'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Yes', 'drile')		=> 1
							,esc_html__('No', 'drile')		=> 0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Mailchimp Subscription ***/
	$mc_forms = drile_get_mailchimp_forms();
	$mc_form_option = array('' => '');
	foreach( $mc_forms as $mc_form ){
		$mc_form_option[$mc_form['title']] = $mc_form['id'];
	}
	vc_map( array(
		'name' 		=> esc_html__( 'TS Mailchimp Subscription', 'drile' ),
		'base' 		=> 'ts_mailchimp_subscription',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> ''
							,esc_html__('Style 2', 'drile')		=> 'style-2'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Form', 'drile' )
				,'param_name' 	=> 'form'
				,'admin_label' 	=> true
				,'value' 		=> $mc_form_option
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Intro Text', 'drile' )
				,'param_name' 	=> 'intro_text'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Style', 'drile' )
				,'param_name' 	=> 'text_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> 'text-default'
							,esc_html__('Light', 'drile')		=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Instagram ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Instagram', 'drile' ),
		'base' 		=> 'ts_instagram',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Layout', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Grid', 'drile')				=>  ''
							,esc_html__('Masonry', 'drile')				=>  'style-masonry'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Username', 'drile' )
				,'param_name' 	=> 'username'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Access Token', 'drile' )
				,'param_name' 	=> 'access_token'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number of photos', 'drile' )
				,'param_name' 	=> 'number'
				,'admin_label' 	=> true
				,'value' 		=> '9'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'column'
				,'admin_label' 	=> true
				,'value' 		=> array(
							2	=> 2
							,3	=> 3
							,4	=> 4
							,5	=> 5
							,6	=> 6
							,6	=> 7
							)
				,'description' 	=> ''
				,'std'			=> 5
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns Gap', 'drile' )
				,'param_name' 	=> 'column_gap'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('30px', 'drile')			=> ''
							,esc_html__('20px', 'drile')		=> 'gap-10'
							,esc_html__('10px', 'drile')		=> 'gap-5'
							)
				,'description' 	=> ''
				,'std'			=> 5
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Size', 'drile' )
				,'param_name' 	=> 'size'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Thumbnail', 'drile')		=> 'thumbnail'
							,esc_html__('Small', 'drile')		=> 'small'
							,esc_html__('Large', 'drile')		=> 'large'
							,esc_html__('Original', 'drile')		=> 'original'
							)
				,'description' 	=> ''
				,'std'			=> 'large'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Self', 'drile')				=> '_self'
							,esc_html__('New window tab', 'drile')	=> '_blank'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Cache time (hours)', 'drile' )
				,'param_name' 	=> 'cache_time'
				,'admin_label' 	=> false
				,'value' 		=> '12'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Testimonial ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Testimonial', 'drile' ),
		'base' 		=> 'ts_testimonial',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Horizontal', 'drile')	=>  'dots-horizontal'
							,esc_html__('Verticle', 'drile')	=>  'dots-verticle'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'ts_category'
				,'heading' 		=> esc_html__( 'Categories', 'drile' )
				,'param_name' 	=> 'categories'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'class'		=> 'ts_testimonial'
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Testimonial IDs', 'drile' )
				,'param_name' 	=> 'ids'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('A comma separated list of testimonial ids', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> '4'
				,'description' 	=> esc_html__('Number of Posts', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show avatar', 'drile' )
				,'param_name' 	=> 'show_avatar'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show name', 'drile' )
				,'param_name' 	=> 'show_name'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show Byline', 'drile' )
				,'param_name' 	=> 'show_byline'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number of words in excerpt', 'drile' )
				,'param_name' 	=> 'excerpt_words'
				,'admin_label' 	=> true
				,'value' 		=> '40'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Color Style', 'drile' )
				,'param_name' 	=> 'text_color_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> 'text-default'
							,esc_html__('Light', 'drile')		=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show dots navigation', 'drile' )
				,'param_name' 	=> 'show_dots'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')		=> 1
							,esc_html__('No', 'drile')		=> 0
							)
				,'description' 	=> esc_html__('Show dots navigation at the bottom. If it is enabled, the navigation buttons will be removed', 'drile')
				,'group'		=> esc_html__('Slider Options', 'drile')
			)			
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Single Image ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Single Image', 'drile' ),
		'base' 		=> 'ts_single_image',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Image', 'drile' )
				,'param_name' 	=> 'img_id'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Image Size', 'drile' )
				,'param_name' 	=> 'img_size'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'Ex: thumbnail, medium, large or full', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Image URL', 'drile' )
				,'param_name' 	=> 'img_url'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> '#'
				,'description' 	=> ''
			)			
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link Title', 'drile' )
				,'param_name' 	=> 'link_title'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('New Window Tab', 'drile')	=> '_blank'
						,esc_html__('Self', 'drile')				=> '_self'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Alignment', 'drile' )
				,'param_name' 	=> 'alignment'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Default', 'drile')				=> ''
						,esc_html__('Left', 'drile')				=> 'ts-alignleft'
						,esc_html__('Center', 'drile')				=> 'ts-aligncenter'
						,esc_html__('Right', 'drile')				=> 'ts-alignright'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Hover Effect', 'drile' )
				,'param_name' 	=> 'style_effect'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Opacity', 'drile')					=> 'eff-image-opacity'
						,esc_html__('Zoom In', 'drile')					=> 'eff-image-scale'
						,esc_html__('Zoom Out', 'drile')				=> 'eff-image-zoom-out'
						,esc_html__('Inner Shadow', 'drile')			=> 'eff-image-shadow'
						,esc_html__('None', 'drile')					=> 'no-eff'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Effect Color', 'drile' )
				,'param_name' 	=> 'effect_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'description' 	=> ''
				,'dependency' 	=> array('element' => 'style_effect', 'value' => array('eff-image-opacity', 'eff-image-shadow'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Image Gallery ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Image Gallery', 'drile' ),
		'base' 		=> 'ts_image_gallery',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'attach_images'
				,'heading' 		=> esc_html__( 'Images', 'drile' )
				,'param_name' 	=> 'images'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image size', 'drile' )
				,'param_name' 	=> 'image_size'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Thumbnail', 'drile')		=> 'thumbnail'
							,esc_html__('Medium', 'drile')		=> 'medium'
							,esc_html__('Large', 'drile')		=> 'large'
							,esc_html__('Full', 'drile')			=> 'full'
						)
				,'description' 	=> esc_html__('You go to Settings > Media to change image size', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> false
				,'value' 		=> array(
							1 	=> 1
							,2 	=> 2
							,3 	=> 3
							,4 	=> 4
							,5 	=> 5
							,6 	=> 6
							,7 	=> 7
							)
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
				,'std'			=> 4
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'On click', 'drile' )
				,'param_name' 	=> 'on_click'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('None', 'drile')						=> 'none'
							,esc_html__('Open prettyPhoto', 'drile')		=> 'prettyphoto'
							,esc_html__('Open custom links', 'drile')		=> 'custom_link'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Custom links', 'drile' )
				,'param_name' 	=> 'custom_links'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('A comma separated list of links. Ex: if you have 3 images, the value of this field should be "link1, link2, link3"', 'drile')
				,'dependency'	=> array( 'element' => 'on_click', 'value' => array('custom_link') )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Custom link target', 'drile' )
				,'param_name' 	=> 'custom_link_target'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Self', 'drile')				=> '_self'
							,esc_html__('New Window Tab', 'drile')		=> '_blank'
						)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'on_click', 'value' => array('custom_link') )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Margin', 'drile' )
				,'param_name' 	=> 'margin_class'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('0px', 'drile')			=> ''
							,esc_html__('30px', 'drile')		=> 'has-margin'
							,esc_html__('10px', 'drile')		=> 'has-margin margin-10'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show dots navigation', 'drile' )
				,'param_name' 	=> 'show_dots'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__('Show dots navigation at the bottom. If it is enabled, the navigation buttons will be removed', 'drile')
				,'group'		=> esc_html__('Slider Options', 'drile')
			)	
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Dots Position', 'drile' )
				,'param_name' 	=> 'dots_position'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Horizontal', 'drile')	=>  'dots-horizontal'
							,esc_html__('Verticle', 'drile')	=>  'dots-verticle'
						)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'show_dots', 'value' => array('1') )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Responsive Items', 'drile' )
				,'param_name' 	=> 'responsive_items'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Logos Slider ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Logos Slider', 'drile' ),
		'base' 		=> 'ts_logos_slider',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> '7'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Rows', 'drile' )
				,'param_name' 	=> 'rows'
				,'admin_label' 	=> true
				,'value' 		=> 1
				,'description' 	=> esc_html__( 'Number of Rows', 'drile' )
			)
			,array(
				'type' 			=> 'ts_category'
				,'heading' 		=> esc_html__( 'Categories', 'drile' )
				,'param_name' 	=> 'categories'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'class'		=> 'ts_logo'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Activate link', 'drile' )
				,'param_name' 	=> 'active_link'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style navigation', 'drile' )
				,'param_name' 	=> 'style_nav'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> 'text-default'
							,esc_html__('Light', 'drile')		=> 'text-light'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Banner ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Banner', 'drile' ),
		'base' 		=> 'ts_banner',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'banner_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')					=>  'style-default'
							,esc_html__('Discount', 'drile')				=>  'style-text-center'
							,esc_html__('Category', 'drile')				=>  'style-category'
							,esc_html__('Has Description', 'drile')			=>  'has-description'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Background Image', 'drile' )
				,'param_name' 	=> 'bg_id'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Background Image Url', 'drile' )
				,'param_name' 	=> 'bg_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Background Color', 'drile' )
				,'param_name' 	=> 'bg_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default', 'style-text-center') )
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Heading Text', 'drile' )
				,'param_name' 	=> 'heading_title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Heading Text Color', 'drile' )
				,'param_name' 	=> 'heading_text_color'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Heading Text Hover Color', 'drile' )
				,'param_name' 	=> 'heading_text_hover_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-category') )
			)			
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Sub Heading Text', 'drile' )
				,'param_name' 	=> 'sub_heading_title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default', 'style-text-center', 'has-description') )
			)			
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Sub Heading Text Color', 'drile' )
				,'param_name' 	=> 'sub_heading_text_color'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default', 'style-text-center', 'has-description') )
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Description Text', 'drile' )
				,'param_name' 	=> 'description'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('has-description') )
			)			
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Description Text Color', 'drile' )
				,'param_name' 	=> 'description_text_color'
				,'admin_label' 	=> false
				,'value' 		=> '#848484'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('has-description') )
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Content Background Color', 'drile' )
				,'param_name' 	=> 'content_background'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('has-description') )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Discount', 'drile' )
				,'param_name' 	=> 'discount'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-text-center') )
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Discount Color', 'drile' )
				,'param_name' 	=> 'discount_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ed9fa6'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-text-center') )
			)			
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Use custom font family for Sub Heading Text', 'drile' )
				,'param_name' 	=> 'use_theme_fonts'
				,'admin_label' 	=> true
				,'value' 		=> array(
								esc_html__('No', 'drile')		=> 0
								,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default') )
			)
			,array(
				'type' => 'google_fonts'
				,'param_name' => 'google_fonts'
				,'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal'
				,'settings' => array(
					'fields' => array(
						'font_family_description' => esc_html__( 'Select font family.', 'drile' )
						,'font_style_description' => esc_html__( 'Select font styling.', 'drile' )
					)
				)
				,'dependency' => array('element' => 'use_theme_fonts', 'value' => array('1'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Custom Font Size for Sub Heading Text', 'drile' )
				,'param_name' 	=> 'sub_heading_title_font_size'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'In px', 'drile' )
				,'dependency' => array('element' => 'use_theme_fonts', 'value' => array('1'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Custom Font Size for Sub Heading Text on Device', 'drile' )
				,'param_name' 	=> 'sub_heading_title_font_size_device'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'In px', 'drile' )
				,'dependency' => array('element' => 'use_theme_fonts', 'value' => array('1'))
			)			
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show Button', 'drile' )
				,'param_name' 	=> 'show_button'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=>  0
							,esc_html__('Yes', 'drile')		=>  1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Button Text', 'drile' )
				,'param_name' 	=> 'button_text'
				,'admin_label' 	=> false
				,'value' 		=> 'Shop Now'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'show_button', 'value' => array('1') )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Banner Text Position', 'drile' )
				,'param_name' 	=> 'content_position'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Left Top', 'drile')			=>  'left-top'
						,esc_html__('Left Bottom', 'drile')		=>  'left-bottom'
						,esc_html__('Left Center', 'drile')		=>  'left-center'
						,esc_html__('Right Top', 'drile')		=>  'right-top'
						,esc_html__('Right Bottom', 'drile')	=>  'right-bottom'
						,esc_html__('Right Center', 'drile')	=>  'right-center'
						,esc_html__('Center Top', 'drile')		=>  'center-top'
						,esc_html__('Center Bottom', 'drile')	=>  'center-bottom'
						,esc_html__('Center Center', 'drile')	=>  'center-center'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link Title', 'drile' )
				,'param_name' 	=> 'link_title'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('New Window Tab', 'drile')	=>  '_blank'
							,esc_html__('Self', 'drile')				=>  '_self'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Hover Effect', 'drile' )
				,'param_name' 	=> 'style_effect'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Opacity', 'drile')					=> 'eff-image-opacity'
						,esc_html__('Zoom In', 'drile')					=> 'eff-image-scale'
						,esc_html__('Zoom Out', 'drile')				=> 'eff-image-zoom-out'
						,esc_html__('Inner Shadow', 'drile')			=> 'eff-image-shadow'
						,esc_html__('None', 'drile')					=> 'no-eff'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Effect Color', 'drile' )
				,'param_name' 	=> 'effect_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'description' 	=> ''
				,'dependency' 	=> array('element' => 'style_effect', 'value' => array('eff-image-opacity', 'eff-image-shadow'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Enable Mobile Version', 'drile' )
				,'param_name' 	=> 'enable_mobile'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Yes', 'drile')					=>  1
						,esc_html__('No', 'drile')					=>	0
						)
				,'description' 	=> esc_html__( 'Show text above the image. If it is enabled, Banner Text Position will be disabled', 'drile' )
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default', 'style-text-center', 'has-description') )
				,'group'		=> esc_html__('Mobile', 'drile')
			)
			,array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Mobile Background Image', 'drile' )
				,'param_name' 	=> 'mobile_bg_id'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'enable_mobile', 'value' => array('1') )
				,'group'		=> esc_html__('Mobile', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Mobile Background Image Url', 'drile' )
				,'param_name' 	=> 'mobile_bg_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
				,'dependency'	=> array( 'element' => 'enable_mobile', 'value' => array('1') )
				,'group'		=> esc_html__('Mobile', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Custom Background Color', 'drile' )
				,'param_name' 	=> 'custom_bg_color'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('No', 'drile')					=>  0
						,esc_html__('Yes', 'drile')					=>	1
						)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'banner_style', 'value' => array('style-default', 'style-text-center', 'has-description') )
				,'group'		=> esc_html__('Mobile', 'drile')
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Mobile Background Color', 'drile' )
				,'param_name' 	=> 'mobile_bg_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'custom_bg_color', 'value' => array('1') )
				,'group'		=> esc_html__('Mobile', 'drile')
			)
		)
	) );
	
	/*** TS Banner 2 ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Banner 2', 'drile' ),
		'base' 		=> 'ts_banner_image',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Background Image', 'drile' )
				,'param_name' 	=> 'img_bg_id'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Background Image URL', 'drile' )
				,'param_name' 	=> 'img_bg_url'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Background Image Size', 'drile' )
				,'param_name' 	=> 'img_size'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'Ex: thumbnail, medium, large or full', 'drile' )
			)
			,array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Image Text', 'drile' )
				,'param_name' 	=> 'img_text_id'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Display this image before, after or over the main image', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Image Text URL', 'drile' )
				,'param_name' 	=> 'img_text_url'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image Text Position', 'drile' )
				,'param_name' 	=> 'img_text_position'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Left Top', 'drile')			=>  'left-top'
						,esc_html__('Left Bottom', 'drile')		=>  'left-bottom'
						,esc_html__('Left Center', 'drile')		=>  'left-center'
						,esc_html__('Right Top', 'drile')		=>  'right-top'
						,esc_html__('Right Bottom', 'drile')		=>  'right-bottom'
						,esc_html__('Right Center', 'drile')		=>  'right-center'
						,esc_html__('Center Top', 'drile')		=>  'center-top'
						,esc_html__('Center Bottom', 'drile')	=>  'center-bottom'
						,esc_html__('Center Center', 'drile')	=>  'center-center'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> true
				,'value' 		=> '#'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link Title', 'drile' )
				,'param_name' 	=> 'link_title'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('New Window Tab', 'drile')	=> '_blank'
						,esc_html__('Self', 'drile')			=> '_self'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Hover Effect', 'drile' )
				,'param_name' 	=> 'style_effect'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Opacity', 'drile')					=> 'eff-image-opacity'
						,esc_html__('Zoom In', 'drile')					=> 'eff-image-scale'
						,esc_html__('Zoom Out', 'drile')				=> 'eff-image-zoom-out'
						,esc_html__('Inner Shadow', 'drile')			=> 'eff-image-shadow'
						,esc_html__('None', 'drile')					=> 'no-eff'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Effect Color', 'drile' )
				,'param_name' 	=> 'effect_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ffffff'
				,'description' 	=> ''
				,'description' 	=> ''
				,'dependency' 	=> array('element' => 'style_effect', 'value' => array('eff-image-opacity', 'eff-image-shadow'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Blogs ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Blogs', 'drile' ),
		'base' 		=> 'ts_blogs',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Default', 'drile')			=> ''
							,esc_html__('Partial view', 'drile')	=> 'partial-view'
						)
				,'description' 	=> esc_html__( 'Style Partial view only available if number of columns equal to 2 or 4, layout is slider and item layout is grid.', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Layout', 'drile' )
				,'param_name' 	=> 'layout'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Grid', 'drile')			=> 'grid'
							,esc_html__('Slider', 'drile')		=> 'slider'
							,esc_html__('Masonry', 'drile')		=> 'masonry'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Item Layout', 'drile' )
				,'param_name' 	=> 'item_layout'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Grid', 'drile')			=> 'grid'
							,esc_html__('List', 'drile')			=> 'list'
						)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'layout', 'value' => array('grid', 'slider', 'masonry') )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> array(
							'1'				=> '1'
							,'2'			=> '2'
							,'3'			=> '3'
							,'4'			=> '4'
							)
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
				,'std'			=> '3'
				,'dependency'	=> array( 'element' => 'layout', 'value' => array('grid', 'slider', 'masonry') )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 5
				,'description' 	=> esc_html__( 'Number of Posts', 'drile' )
				,'dependency'	=> array( 'element' => 'layout', 'value' => array('grid', 'slider', 'masonry') )
			)
			,array(
				'type' 			=> 'ts_category'
				,'heading' 		=> esc_html__( 'Categories', 'drile' )
				,'param_name' 	=> 'categories'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'class'		=> 'post_cat'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Order by', 'drile' )
				,'param_name' 	=> 'orderby'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('None', 'drile')		=> 'none'
						,esc_html__('ID', 'drile')		=> 'ID'
						,esc_html__('Date', 'drile')		=> 'date'
						,esc_html__('Name', 'drile')		=> 'name'
						,esc_html__('Title', 'drile')	=> 'title'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Order', 'drile' )
				,'param_name' 	=> 'order'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Descending', 'drile')	=> 'DESC'
						,esc_html__('Ascending', 'drile')	=> 'ASC'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show post title', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show thumbnail', 'drile' )
				,'param_name' 	=> 'show_thumbnail'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show author', 'drile' )
				,'param_name' 	=> 'show_author'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show Categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show comment', 'drile' )
				,'param_name' 	=> 'show_comment'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show date', 'drile' )
				,'param_name' 	=> 'show_date'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show post excerpt', 'drile' )
				,'param_name' 	=> 'show_excerpt'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show read more button', 'drile' )
				,'param_name' 	=> 'show_readmore'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number of words in excerpt', 'drile' )
				,'param_name' 	=> 'excerpt_words'
				,'admin_label' 	=> false
				,'value' 		=> 20
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show load more button', 'drile' )
				,'param_name' 	=> 'show_load_more'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'layout', 'value' => array('grid', 'masonry') )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Load more button text', 'drile' )
				,'param_name' 	=> 'load_more_text'
				,'admin_label' 	=> false
				,'value' 		=> 'LOAD MORE'
				,'description' 	=> ''
				,'dependency'	=> array( 'element' => 'layout', 'value' => array('grid', 'masonry') )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Video ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Video', 'drile' ),
		'base' 		=> 'ts_video_2',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Video Url', 'drile' )
				,'param_name' 	=> 'video_url'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Enter a Youtube, Vimeo or hosted video url', 'drile')
			)
			,array(
				'type' 			=> 'attach_image'
				,'heading' 		=> esc_html__( 'Placeholder Image', 'drile' )
				,'param_name' 	=> 'placeholder_image_id'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Placeholder Image Url', 'drile' )
				,'param_name' 	=> 'placeholder_image_url'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Input external URL instead of image from library', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Rxtra_class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Portfolio ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Portfolio', 'drile' ),
		'base' 		=> 'ts_portfolio',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> array(
							'2'		=> '2'
							,'3'	=> '3'
							,'4'	=> '4'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 8
				,'description' 	=> esc_html__('Number of Posts', 'drile')
			)
			,array(
				'type' 			=> 'ts_category'
				,'heading' 		=> esc_html__( 'Categories', 'drile' )
				,'param_name' 	=> 'categories'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'class'		=> 'ts_portfolio'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Order by', 'drile' )
				,'param_name' 	=> 'orderby'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('None', 'drile')		=> 'none'
							,esc_html__('ID', 'drile')		=> 'ID'
							,esc_html__('Date', 'drile')		=> 'date'
							,esc_html__('Name', 'drile')		=> 'name'
							,esc_html__('Title', 'drile')	=> 'title'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Order', 'drile' )
				,'param_name' 	=> 'order'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Descending', 'drile')	=> 'DESC'
							,esc_html__('Ascending', 'drile')	=> 'ASC'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show portfolio title', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show like icon', 'drile' )
				,'param_name' 	=> 'show_like_icon'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show link icon', 'drile' )
				,'param_name' 	=> 'show_link_icon'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show filter bar', 'drile' )
				,'param_name' 	=> 'show_filter_bar'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show load more button', 'drile' )
				,'param_name' 	=> 'show_load_more'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Load more button text', 'drile' )
				,'param_name' 	=> 'load_more_text'
				,'admin_label' 	=> false
				,'value' 		=> 'LOAD MORE'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')	=>  0
							,esc_html__('Yes', 'drile')	=>  1
						)
				,'description' 	=> esc_html__('If slider is enabled, the filter bar and load more button will be removed', 'drile')
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=>  1
							,esc_html__('No', 'drile')	=>  0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Price Table ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Price Table', 'drile' ),
		'base' 		=> 'ts_price_table',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Style 1', 'drile')		=> 'style-1'
							,esc_html__('Style 2', 'drile')		=> 'style-2'
							,esc_html__('Style 3', 'drile')		=> 'style-3'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title Table', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Color Scheme', 'drile' )
				,'param_name' 	=> 'color_scheme'
				,'admin_label' 	=> false
				,'value' 		=> '#f1a671'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Price', 'drile' )
				,'param_name' 	=> 'price'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Currency', 'drile' )
				,'param_name' 	=> 'currency'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'During Price', 'drile' )
				,'param_name' 	=> 'during_price'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('Ex: /day, /mon, /year', 'drile')
			)
			,array(
				'type' 			=> 'textarea'
				,'heading' 		=> esc_html__( 'Description', 'drile' )
				,'param_name' 	=> 'description'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Button text', 'drile' )
				,'param_name' 	=> 'button_text'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Link', 'drile' )
				,'param_name' 	=> 'link'
				,'admin_label' 	=> false
				,'value' 		=> '#'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Active Table', 'drile' )
				,'param_name' 	=> 'active_table'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')	=> 1
						)
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Team Members ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Team Members', 'drile' ),
		'base' 		=> 'ts_team_members',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number of members', 'drile' )
				,'param_name' 	=> 'limit'
				,'admin_label' 	=> true
				,'value' 		=> 6
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Include these members', 'drile' )
				,'param_name' 	=> 'ids'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> false
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> array(
							'1'				=> '1'
							,'2'			=> '2'
							,'3'			=> '3'
							,'4'			=> '4'
							,'5'			=> '5'
							,'6'			=> '6'
							)
				,'description' 	=> esc_html__( 'Number of Columns. 5 columns is not available on the Grid layout', 'drile' )
				,'std'			=> '3'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Target', 'drile' )
				,'param_name' 	=> 'target'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('New Window Tab', 'drile')	=> '_blank'
						,esc_html__('Self', 'drile')			=> '_self'	
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=>  0
							,esc_html__('Yes', 'drile')		=>  1
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Milestone ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Milestone', 'drile' ),
		'base' 		=> 'ts_milestone',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number', 'drile' )
				,'param_name' 	=> 'number'
				,'admin_label' 	=> true
				,'value' 		=> '0'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Plus Icon', 'drile' )
				,'param_name' 	=> 'plus_icon'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Subject', 'drile' )
				,'param_name' 	=> 'subject'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Color Style', 'drile' )
				,'param_name' 	=> 'text_color_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> 'text-default'
							,esc_html__('Light', 'drile')		=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Countdown ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Countdown', 'drile' ),
		'base' 		=> 'ts_countdown',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> ''
							,esc_html__('Style 2', 'drile')		=> 'style-2'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Day', 'drile' )
				,'param_name' 	=> 'day'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Month', 'drile' )
				,'param_name' 	=> 'month'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Year', 'drile' )
				,'param_name' 	=> 'year'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Text Color Style', 'drile' )
				,'param_name' 	=> 'text_color_style'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Default', 'drile')		=> 'text-default'
							,esc_html__('Light', 'drile')		=> 'text-light'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Google Map ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Google Map', 'drile' ),
		'base' 		=> 'ts_google_map',
		'icon' 		=> 'ts_icon_vc',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Address', 'drile' )
				,'param_name' 	=> 'address'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> esc_html__('You have to input your API Key in Appearance > Theme Options > General tab', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Height', 'drile' )
				,'param_name' 	=> 'height'
				,'admin_label' 	=> true
				,'value' 		=> 360
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Zoom', 'drile' )
				,'param_name' 	=> 'zoom'
				,'admin_label' 	=> true
				,'value' 		=> 12
				,'description' 	=> esc_html__('Input a number between 0 and 22', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Map Type', 'drile' )
				,'param_name' 	=> 'map_type'
				,'admin_label' 	=> true
				,'value' 		=> array(
								esc_html__('ROADMAP', 'drile')		=> 'ROADMAP'
								,esc_html__('SATELLITE', 'drile')	=> 'SATELLITE'
								,esc_html__('HYBRID', 'drile')		=> 'HYBRID'
								,esc_html__('TERRAIN', 'drile')		=> 'TERRAIN'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Grayscale', 'drile' )
				,'param_name' 	=> 'grayscale'
				,'admin_label' 	=> true
				,'value' 		=> array(
								esc_html__('Yes', 'drile')		=> 1
								,esc_html__('No', 'drile')		=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textarea_html'
				,'heading' 		=> esc_html__( 'Information', 'drile' )
				,'param_name' 	=> 'content'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Display some information over map', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
	
	/********************** TS Product Shortcodes ************************/

	/*** TS Products ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Products', 'drile' ),
		'base' 		=> 'ts_products',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Product type', 'drile' )
				,'param_name' 	=> 'product_type'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'description' 	=> esc_html__( 'Select type of product', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Products', 'drile' )
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product IDs', 'drile' )
				,'param_name' 	=> 'ids'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> esc_html__('Enter product name or slug to search', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product image', 'drile' )
				,'param_name' 	=> 'show_image'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image border', 'drile' )
				,'param_name' 	=> 'image_border'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')			=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product name', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product SKU', 'drile' )
				,'param_name' 	=> 'show_sku'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product price', 'drile' )
				,'param_name' 	=> 'show_price'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product short description', 'drile' )
				,'param_name' 	=> 'show_short_desc'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product label', 'drile' )
				,'param_name' 	=> 'show_label'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show add to cart button', 'drile' )
				,'param_name' 	=> 'show_add_to_cart'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show color swatches', 'drile' )
				,'param_name' 	=> 'show_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show the color attribute of variations. The slug of the color attribute has to be "color"', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Number of color swatches', 'drile' )
				,'param_name' 	=> 'number_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							2		=> 2
							,3		=> 3
							,4		=> 4
							,5		=> 5
							,6		=> 6
							)
				,'description' 	=> ''
				,'std'			=> 3
				,'dependency' 	=> array('element' => 'show_color_swatch', 'value' => array('1'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more button text', 'drile' )
				,'param_name' 	=> 'shop_more_text'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more link', 'drile' )
				,'param_name' 	=> 'shop_more_link'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Only show slider on mobile', 'drile' )
				,'param_name' 	=> 'only_slider_mobile'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show Grid on desktop and only enable Slider on mobile', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Row', 'drile' )
				,'param_name' 	=> 'rows'
				,'admin_label' 	=> false
				,'value' 		=> array(
								1 	=> 1
								,2 	=> 2
								,3 	=> 3
							)
				,'description' 	=> esc_html__( 'Number of Rows for slider', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Disable slider responsive', 'drile' )
				,'param_name' 	=> 'disable_slider_responsive'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__('You should only enable this option when Columns is 1 or 2', 'drile')
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Product Deals ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Product Deals', 'drile' ),
		'base' 		=> 'ts_product_deals',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Layout', 'drile' )
				,'param_name' 	=> 'layout'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Slider', 'drile')	=>  'slider'
							,esc_html__('Grid', 'drile')		=>  'grid'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Product type', 'drile' )
				,'param_name' 	=> 'product_type'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'description' 	=> esc_html__( 'Select type of product', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> false
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 5
				,'description' 	=> esc_html__( 'Number of Products', 'drile' )
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product IDs', 'drile' )
				,'param_name' 	=> 'ids'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> false
					,'unique_values' 	=> true
				)
				,'description' 	=> esc_html__('Enter product name or slug to search', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show counter', 'drile' )
				,'param_name' 	=> 'show_counter'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> esc_html__( 'Show counter on each product', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product image', 'drile' )
				,'param_name' 	=> 'show_image'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image border', 'drile' )
				,'param_name' 	=> 'image_border'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')			=> 0
							,esc_html__('Yes', 'drile')			=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product name', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product SKU', 'drile' )
				,'param_name' 	=> 'show_sku'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product price', 'drile' )
				,'param_name' 	=> 'show_price'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product short description', 'drile' )
				,'param_name' 	=> 'show_short_desc'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Number of words in short description', 'drile' )
				,'param_name' 	=> 'short_desc_words'
				,'admin_label' 	=> false
				,'value' 		=> 8
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')		=> 1
							,esc_html__('No', 'drile')		=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product label', 'drile' )
				,'param_name' 	=> 'show_label'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show add to cart button', 'drile' )
				,'param_name' 	=> 'show_add_to_cart'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more button text', 'drile' )
				,'param_name' 	=> 'shop_more_text'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more link', 'drile' )
				,'param_name' 	=> 'shop_more_link'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Products In Category Tabs ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Products In Category Tabs', 'drile' ),
		'base' 		=> 'ts_products_in_category_tabs',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Style', 'drile' )
				,'param_name' 	=> 'style'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Horizontal', 'drile')			=> 'style-horizontal'
						,esc_html__('Horizontal Icons', 'drile')	=> 'style-horizontal-icons'
						,esc_html__('Vertical Banner', 'drile')		=> 'style-verticle'
						,esc_html__('Vertical Icons', 'drile')		=> 'style-verticle-icons'
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'attach_images'
				,'heading' 		=> esc_html__( 'Banners', 'drile' )
				,'param_name' 	=> 'banners'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'dependency'	=> array( 'element' => 'style', 'value' => array('style-verticle') )
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'attach_images'
				,'heading' 		=> esc_html__( 'Icons', 'drile' )
				,'param_name' 	=> 'icons'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'dependency'	=> array( 'element' => 'style', 'value' => array('style-verticle', 'style-horizontal-icons', 'style-verticle-icons') )
				,'description' 	=> esc_html__('Select icons which show on the left side of category name', 'drile')
			)
			,array(
				'type' 			=> 'attach_images'
				,'heading' 		=> esc_html__( 'Hover Icons', 'drile' )
				,'param_name' 	=> 'hover_icons'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'dependency'	=> array( 'element' => 'style', 'value' => array('style-verticle', 'style-verticle-icons') )
				,'description' 	=> esc_html__('Select icons which show on the left side of category name when hovering', 'drile')
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Icons Background Color', 'drile' )
				,'param_name' 	=> 'bg_color'
				,'admin_label' 	=> false
				,'value' 		=> '#ebebeb'
				,'dependency'	=> array( 'element' => 'style', 'value' => array('style-verticle', 'style-horizontal-icons', 'style-verticle-icons') )
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'colorpicker'
				,'heading' 		=> esc_html__( 'Icons Background Color Hover', 'drile' )
				,'param_name' 	=> 'bg_color_hover'
				,'admin_label' 	=> false
				,'value' 		=> '#202020'
				,'dependency'	=> array( 'element' => 'style', 'value' => array('style-verticle', 'style-horizontal-icons', 'style-verticle-icons') )
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product count', 'drile' )
				,'param_name' 	=> 'show_product_count'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Product type', 'drile' )
				,'param_name' 	=> 'product_type'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')			=> 'sale'
						,esc_html__('Featured', 'drile')		=> 'featured'
						,esc_html__('Best Selling', 'drile')	=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'description' 	=> esc_html__( 'Select type of product', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 8
				,'description' 	=> esc_html__( 'Number of Products', 'drile' )
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Parent Category', 'drile' )
				,'param_name' 	=> 'parent_cat'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> false
					,'sortable' 		=> false
					,'unique_values' 	=> true
				)
				,'description' 	=> esc_html__('Each tab will be a sub category of this category. This option is available when the Product Categories option is empty', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Include children', 'drile' )
				,'param_name' 	=> 'include_children'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('No', 'drile')		=> 0
						,esc_html__('Yes', 'drile')		=> 1
						)
				,'description' 	=> esc_html__( 'Load the products of sub categories in each tab', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show general tab', 'drile' )
				,'param_name' 	=> 'show_general_tab'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__('Get products from all categories or sub categories', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'General tab heading', 'drile' )
				,'param_name' 	=> 'general_tab_heading'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'dependency'	=> array( 'element' => 'show_general_tab', 'value' => array('1') )
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Product type of general tab', 'drile' )
				,'param_name' 	=> 'product_type_general_tab'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						)
				,'dependency'	=> array( 'element' => 'show_general_tab', 'value' => array('1') )
				,'description' 	=> esc_html__( 'Select type of product', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product image', 'drile' )
				,'param_name' 	=> 'show_image'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image border', 'drile' )
				,'param_name' 	=> 'image_border'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product name', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product SKU', 'drile' )
				,'param_name' 	=> 'show_sku'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product price', 'drile' )
				,'param_name' 	=> 'show_price'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product short description', 'drile' )
				,'param_name' 	=> 'show_short_desc'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product label', 'drile' )
				,'param_name' 	=> 'show_label'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show add to cart button', 'drile' )
				,'param_name' 	=> 'show_add_to_cart'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show color swatches', 'drile' )
				,'param_name' 	=> 'show_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show the color attribute of variations. The slug of the color attribute has to be "color"', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Number of color swatches', 'drile' )
				,'param_name' 	=> 'number_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							2		=> 2
							,3		=> 3
							,4		=> 4
							,5		=> 5
							,6		=> 6
							)
				,'description' 	=> ''
				,'std'			=> 3
				,'dependency' 	=> array('element' => 'show_color_swatch', 'value' => array('1'))
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show shop more button', 'drile' )
				,'param_name' 	=> 'show_shop_more_button'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show shop more button in general tab', 'drile' )
				,'param_name' 	=> 'show_shop_more_general_tab'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
						)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more button label', 'drile' )
				,'param_name' 	=> 'shop_more_button_text'
				,'admin_label' 	=> true
				,'value' 		=> 'Shop more'
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Only show slider on mobile', 'drile' )
				,'param_name' 	=> 'only_slider_mobile'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show Grid on desktop and only enable Slider on mobile', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Rows', 'drile' )
				,'param_name' 	=> 'rows'
				,'admin_label' 	=> true
				,'value' 		=> array(
						'1'			=> '1'
						,'2'		=> '2'
						)
				,'description' 	=> esc_html__( 'Number of Rows in slider', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show dot navigation', 'drile' )
				,'param_name' 	=> 'show_dots'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__('Show dot navigation at the bottom. If it is enabled, the navigation buttons will be removed', 'drile')
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Products In Product Type Tabs ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Products In Product Type Tabs', 'drile' ),
		'base' 		=> 'ts_products_in_product_type_tabs',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 1', 'drile' )
				,'param_name' 	=> 'tab_1'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Tab 1 heading', 'drile' )
				,'param_name' 	=> 'tab_1_heading'
				,'admin_label' 	=> false
				,'value' 		=> 'Featured'
				,'description' 	=> ''
				,'dependency' => array('element' => 'tab_1', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 1 product type', 'drile' )
				,'param_name' 	=> 'tab_1_product_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'std'			=> 'featured'
				,'dependency' 	=> array('element' => 'tab_1', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 2', 'drile' )
				,'param_name' 	=> 'tab_2'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Tab 2 heading', 'drile' )
				,'param_name' 	=> 'tab_2_heading'
				,'admin_label' 	=> false
				,'value' 		=> 'Best Selling'
				,'description' 	=> ''
				,'dependency' => array('element' => 'tab_2', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 2 product type', 'drile' )
				,'param_name' 	=> 'tab_2_product_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'std'			=> 'best_selling'
				,'dependency' 	=> array('element' => 'tab_2', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 3', 'drile' )
				,'param_name' 	=> 'tab_3'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Tab 3 heading', 'drile' )
				,'param_name' 	=> 'tab_3_heading'
				,'admin_label' 	=> false
				,'value' 		=> 'On Sale'
				,'description' 	=> ''
				,'dependency' => array('element' => 'tab_3', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 3 product type', 'drile' )
				,'param_name' 	=> 'tab_3_product_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'std'			=> 'sale'
				,'dependency' 	=> array('element' => 'tab_3', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 4', 'drile' )
				,'param_name' 	=> 'tab_4'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Tab 4 heading', 'drile' )
				,'param_name' 	=> 'tab_4_heading'
				,'admin_label' 	=> false
				,'value' 		=> 'Top Rated'
				,'description' 	=> ''
				,'dependency' => array('element' => 'tab_4', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 4 product type', 'drile' )
				,'param_name' 	=> 'tab_4_product_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'std'			=> 'top_rated'
				,'dependency' 	=> array('element' => 'tab_4', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 5', 'drile' )
				,'param_name' 	=> 'tab_5'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Tab 5 heading', 'drile' )
				,'param_name' 	=> 'tab_5_heading'
				,'admin_label' 	=> false
				,'value' 		=> 'Recent'
				,'description' 	=> ''
				,'dependency' => array('element' => 'tab_5', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Tab 5 product type', 'drile' )
				,'param_name' 	=> 'tab_5_product_type'
				,'admin_label' 	=> false
				,'value' 		=> array(
						esc_html__('Recent', 'drile')			=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')		=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')		=> 'mixed_order'
						)
				,'std'			=> 'recent'
				,'dependency' 	=> array('element' => 'tab_5', 'value' => '1')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Active tab', 'drile' )
				,'param_name' 	=> 'active_tab'
				,'admin_label' 	=> false
				,'value' 		=> array(
						1		=> 1
						,2		=> 2
						,3		=> 3
						,4		=> 4
						,5		=> 5
						)
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Item layout', 'drile' )
				,'param_name' 	=> 'item_layout'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Grid', 'drile')	=> 'grid'
							,esc_html__('List', 'drile')	=> 'list'
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 6
				,'description' 	=> esc_html__( 'Number of Products', 'drile' )
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product image', 'drile' )
				,'param_name' 	=> 'show_image'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image border', 'drile' )
				,'param_name' 	=> 'image_border'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product name', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product SKU', 'drile' )
				,'param_name' 	=> 'show_sku'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product price', 'drile' )
				,'param_name' 	=> 'show_price'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product short description', 'drile' )
				,'param_name' 	=> 'show_short_desc'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product label', 'drile' )
				,'param_name' 	=> 'show_label'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show add to cart button', 'drile' )
				,'param_name' 	=> 'show_add_to_cart'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show color swatches', 'drile' )
				,'param_name' 	=> 'show_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show the color attribute of variations. The slug of the color attribute has to be "color"', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Number of color swatches', 'drile' )
				,'param_name' 	=> 'number_color_swatch'
				,'admin_label' 	=> false
				,'value' 		=> array(
							2		=> 2
							,3		=> 3
							,4		=> 4
							,5		=> 5
							,6		=> 6
							)
				,'description' 	=> ''
				,'std'			=> 3
				,'dependency' 	=> array('element' => 'show_color_swatch', 'value' => array('1'))
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Only show slider on mobile', 'drile' )
				,'param_name' 	=> 'only_slider_mobile'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> esc_html__( 'Show Grid on desktop and only enable Slider on mobile', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Rows', 'drile' )
				,'param_name' 	=> 'rows'
				,'admin_label' 	=> true
				,'value' 		=> array(
						'1'			=> '1'
						,'2'		=> '2'
						)
				,'description' 	=> esc_html__( 'Number of Rows in slider', 'drile' )
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button as text', 'drile' )
				,'param_name' 	=> 'show_nav_as_text'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
						)
				,'description' 	=> ''
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Previous Text', 'drile' )
				,'param_name' 	=> 'prev_text'
				,'admin_label' 	=> true
				,'value' 		=> 'Back'
				,'description' 	=> ''
				,'dependency' 	=> array('element' => 'show_nav_as_text', 'value' => array('1'))
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Next Text', 'drile' )
				,'param_name' 	=> 'next_text'
				,'admin_label' 	=> true
				,'value' 		=> 'Next'
				,'description' 	=> ''
				,'dependency' 	=> array('element' => 'show_nav_as_text', 'value' => array('1'))
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__( 'Slider Options', 'drile' )
			)
		)
	) );
	
	/*** TS Products Widget ***/
	vc_map( array(
		'name' 			=> esc_html__( 'TS Products Widget', 'drile' ),
		'base' 			=> 'ts_products_widget',
		'icon' 			=> 'ts_icon_vc_shop',
		'class' 		=> '',
		'description' 	=> '',
		'category' 		=> esc_html__('Theme-Sky', 'drile'),
		'params' 		=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Product type', 'drile' )
				,'param_name' 	=> 'product_type'
				,'admin_label' 	=> true
				,'value' 		=> array(
						esc_html__('Recent', 'drile')				=> 'recent'
						,esc_html__('Sale', 'drile')				=> 'sale'
						,esc_html__('Featured', 'drile')			=> 'featured'
						,esc_html__('Best Selling', 'drile')		=> 'best_selling'
						,esc_html__('Top Rated', 'drile')			=> 'top_rated'
						,esc_html__('Mixed Order', 'drile')			=> 'mixed_order'
						)
				,'description' 	=> esc_html__( 'Select type of product', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 6
				,'description' 	=> esc_html__( 'Number of Products', 'drile' )
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product image', 'drile' )
				,'param_name' 	=> 'show_image'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Image border', 'drile' )
				,'param_name' 	=> 'image_border'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product name', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product price', 'drile' )
				,'param_name' 	=> 'show_price'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product rating', 'drile' )
				,'param_name' 	=> 'show_rating'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product categories', 'drile' )
				,'param_name' 	=> 'show_categories'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Row', 'drile' )
				,'param_name' 	=> 'rows'
				,'admin_label' 	=> false
				,'value' 		=> 3
				,'description' 	=> esc_html__( 'Number of Rows for slider', 'drile' )
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Product Brands ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Product Brands Slider', 'drile' ),
		'base' 		=> 'ts_product_brands',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Use logo\'s settings', 'drile' )
				,'param_name' 	=> 'use_logo_setting'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> esc_html__( 'If enabled, you go to Logos > Settings to configure image size and slider responsive', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> false
				,'value' 		=> 5
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
				,'dependency' 	=> array('element' => 'use_logo_setting', 'value' => array('0'))
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 6
				,'description' 	=> esc_html__( 'Number of Product Brands', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Only display the first level', 'drile' )
				,'param_name' 	=> 'first_level'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Hide empty product brands', 'drile' )
				,'param_name' 	=> 'hide_empty'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product brand title', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product count', 'drile' )
				,'param_name' 	=> 'show_product_count'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
			)
		)
	) );
	
	/*** TS Product Categories ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Product Categories', 'drile' ),
		'base' 		=> 'ts_product_categories',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Columns', 'drile' )
				,'param_name' 	=> 'columns'
				,'admin_label' 	=> true
				,'value' 		=> 4
				,'description' 	=> esc_html__( 'Number of Columns', 'drile' )
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Limit', 'drile' )
				,'param_name' 	=> 'per_page'
				,'admin_label' 	=> true
				,'value' 		=> 5
				,'description' 	=> esc_html__( 'Number of Product Categories', 'drile' )
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Only display the first level', 'drile' )
				,'param_name' 	=> 'first_level'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Parent', 'drile' )
				,'param_name' 	=> 'parent'
				,'admin_label' 	=> true
				,'settings' => array(
					'multiple' 			=> false
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'Select a category. Get direct children of this category', 'drile' )
				,'dependency' 	=> array('element' => 'first_level', 'value' => array('0'))
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Child Of', 'drile' )
				,'param_name' 	=> 'child_of'
				,'admin_label' 	=> true
				,'settings' => array(
					'multiple' 			=> false
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'value' 		=> ''
				,'description' 	=> esc_html__( 'Select a category. Get all descendents of this category', 'drile' )
				,'dependency' 	=> array('element' => 'first_level', 'value' => array('0'))
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'ids'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> esc_html__('Include these categories', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Hide empty product categories', 'drile' )
				,'param_name' 	=> 'hide_empty'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product category title', 'drile' )
				,'param_name' 	=> 'show_title'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product category icon', 'drile' )
				,'param_name' 	=> 'show_icon'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')	=> 0
							,esc_html__('Yes', 'drile')	=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product count', 'drile' )
				,'param_name' 	=> 'show_product_count'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Shop more button text', 'drile' )
				,'param_name' 	=> 'view_shop_button_text'
				,'admin_label' 	=> false
				,'description' 	=> ''
				,'value' 		=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Reverse Effect', 'drile' )
				,'param_name' 	=> 'reverse_effect'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show in a carousel slider', 'drile' )
				,'param_name' 	=> 'is_slider'
				,'admin_label' 	=> true
				,'value' 		=> array(
							esc_html__('No', 'drile')		=> 0
							,esc_html__('Yes', 'drile')		=> 1
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show navigation button', 'drile' )
				,'param_name' 	=> 'show_nav'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Auto play', 'drile' )
				,'param_name' 	=> 'auto_play'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
						)
				,'description' 	=> ''
				,'group'		=> esc_html__('Slider Options', 'drile')
			)
		)
	) );
	
	/*** TS Product Category Banners ***/
	vc_map( array(
		'name' 		=> esc_html__( 'TS Product Category Banners', 'drile' ),
		'base' 		=> 'ts_product_category_banners',
		'icon' 		=> 'ts_icon_vc_shop',
		'class' 	=> '',
		'category' 	=> esc_html__('Theme-Sky', 'drile'),
		'params' 	=> array(
			array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Title', 'drile' )
				,'param_name' 	=> 'title'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'autocomplete'
				,'heading' 		=> esc_html__( 'Product Categories', 'drile' )
				,'param_name' 	=> 'product_cats'
				,'admin_label' 	=> true
				,'value' 		=> ''
				,'settings' => array(
					'multiple' 			=> true
					,'sortable' 		=> true
					,'unique_values' 	=> true
				)
				,'description' 	=> esc_html__('Select your product categories', 'drile')
			)
			,array(
				'type' 			=> 'attach_images'
				,'heading' 		=> esc_html__( 'Banners', 'drile' )
				,'param_name' 	=> 'banners'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> esc_html__('Select banners for each product category. Leave blank to use product category thumbnail.', 'drile')
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Banner size', 'drile' )
				,'param_name' 	=> 'banner_size'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Medium', 'drile')	=> 'medium'
							,esc_html__('Large', 'drile')	=> 'large'
							,esc_html__('Full', 'drile')	=> 'full'
						)
				,'description' 	=> ''
				,'std' 			=> 'full'
			)
			,array(
				'type' 			=> 'dropdown'
				,'heading' 		=> esc_html__( 'Show product count', 'drile' )
				,'param_name' 	=> 'show_product_count'
				,'admin_label' 	=> false
				,'value' 		=> array(
							esc_html__('Yes', 'drile')	=> 1
							,esc_html__('No', 'drile')	=> 0
							)
				,'description' 	=> ''
			)
			,array(
				'type' 			=> 'textfield'
				,'heading' 		=> esc_html__( 'Extra Class', 'drile' )
				,'param_name' 	=> 'extra_class'
				,'admin_label' 	=> false
				,'value' 		=> ''
				,'description' 	=> ''
			)
		)
	) );
}

/*** Add Shortcode Param ***/
WpbakeryShortcodeParams::addField('ts_category', 'drile_product_catgories_shortcode_param');
if( !function_exists('drile_product_catgories_shortcode_param') ){
	function drile_product_catgories_shortcode_param($settings, $value){
		$categories = drile_get_list_categories_shortcode_param(0, $settings);
		$arr_value = explode(',', $value);
		ob_start();
		?>
		<input type="hidden" class="wpb_vc_param_value wpb-textinput product_cats textfield ts-hidden-selected-categories" name="<?php echo esc_attr($settings['param_name']); ?>" value="<?php echo esc_attr($value); ?>" />
		<div class="categorydiv">
			<div class="tabs-panel">
				<ul class="categorychecklist">
					<?php foreach($categories as $cat){ ?>
					<li>
						<label>
							<input type="checkbox" class="checkbox ts-select-category" value="<?php echo esc_attr($cat->term_id); ?>" <?php echo (in_array($cat->term_id, $arr_value))?'checked':''; ?> />
							<?php echo esc_html($cat->name); ?>
						</label>
						<?php drile_get_list_sub_categories_shortcode_param($cat->term_id, $arr_value, $settings); ?>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('.ts-select-category').bind('change', function(){
				"use strict";
				
				var selected = jQuery('.ts-select-category:checked');
				jQuery('.ts-hidden-selected-categories').val('');
				var selected_id = new Array();
				selected.each(function(index, ele){
					selected_id.push(jQuery(ele).val());
				});
				selected_id = selected_id.join(',');
				jQuery('.ts-hidden-selected-categories').val(selected_id);
			});
		</script>
		<?php
		return ob_get_clean();
	}
}

if( !function_exists('drile_get_list_categories_shortcode_param') ){
	function drile_get_list_categories_shortcode_param( $cat_parent_id, $settings ){
		$taxonomy = 'product_cat';
		if( isset($settings['class']) ){
			if( $settings['class'] == 'post_cat' ){
				$taxonomy = 'category';
			}
			if( $settings['class'] == 'ts_testimonial' ){
				$taxonomy = 'ts_testimonial_cat';
			}
			if( $settings['class'] == 'ts_portfolio' ){
				$taxonomy = 'ts_portfolio_cat';
			}
			if( $settings['class'] == 'ts_logo' ){
				$taxonomy = 'ts_logo_cat';
			}
			if( $settings['class'] == 'tribe_events_cat' ){
				$taxonomy = 'tribe_events_cat';
			}
		}
		
		$args = array(
				'taxonomy' 			=> $taxonomy
				,'hierarchical'		=> 1
				,'hide_empty'		=> 0
				,'parent'			=> $cat_parent_id
				,'title_li'			=> ''
				,'child_of'			=> 0
			);
		$cats = get_categories($args);
		return $cats;
	}
}

if( !function_exists('drile_get_list_sub_categories_shortcode_param') ){
	function drile_get_list_sub_categories_shortcode_param( $cat_parent_id, $arr_value, $settings ){
		$sub_categories = drile_get_list_categories_shortcode_param($cat_parent_id, $settings); 
		if( count($sub_categories) > 0){
		?>
			<ul class="children">
				<?php foreach( $sub_categories as $sub_cat ){ ?>
					<li>
						<label>
							<input type="checkbox" class="checkbox ts-select-category" value="<?php echo esc_attr($sub_cat->term_id); ?>" <?php echo (in_array($sub_cat->term_id, $arr_value))?'checked':''; ?> />
							<?php echo esc_html($sub_cat->name); ?>
						</label>
						<?php drile_get_list_sub_categories_shortcode_param($sub_cat->term_id, $arr_value, $settings); ?>
					</li>
				<?php } ?>
			</ul>
		<?php }
	}
}

function drile_team_member_autocomplete_suggester( $query ){
	$args = array(
			'post_type'				=> 'ts_team'
			,'post_status'			=> 'publish'
			,'posts_per_page'		=> -1
			,'s'					=> $query
			);
	$results = array();
	$teams = new WP_Query($args);
	if( !empty( $teams->posts ) && is_array( $teams->posts ) ){
		foreach( $teams->posts as $p ){
			$data = array();
			$data['value'] = $p->ID;
			$data['label'] = esc_html__( 'ID', 'drile' ) . ': ' . $p->ID . ( ( strlen( $p->post_title ) > 0 ) ? ' - ' . esc_html__( 'Name', 'drile' ) . ': ' . $p->post_title : '' );
			$results[] = $data;
		}
	}
	return $results;
}

function drile_team_member_autocomplete_render( $query ){
	$query = trim( $query['value'] );
	if ( ! empty( $query ) ) {
		
		$args = array(
			'post_type'				=> 'ts_team'
			,'post_status'			=> 'publish'
			,'posts_per_page'		=> 1
			,'p'					=> (int) $query
			);
		$teams = new WP_Query($args);
		if( isset($teams->post) ){
			$team = $teams->post;
			
			$team_id_display = esc_html__( 'ID', 'drile' ) . ': ' . $team->ID;
			$team_title_display = '';
			if ( ! empty( $team->post_title ) ) {
				$team_title_display = ' - ' . esc_html__( 'Name', 'drile' ) . ': ' . $team->post_title;
			}
			
			$data = array();
			$data['value'] = $team->ID;
			$data['label'] = $team_id_display . $team_title_display;

			wp_reset_postdata();
			
			return $data;
		}
		return false;
	}
	return false;
}

if( class_exists('Vc_Vendor_Woocommerce') ){
	$vc_woo_vendor = new Vc_Vendor_Woocommerce();

	/* autocomplete callback */
	add_filter( 'vc_autocomplete_ts_products_ids_callback', array($vc_woo_vendor, 'productIdAutocompleteSuggester') );
	add_filter( 'vc_autocomplete_ts_products_ids_render', array($vc_woo_vendor, 'productIdAutocompleteRender') );
	
	add_filter( 'vc_autocomplete_ts_product_deals_ids_callback', array($vc_woo_vendor, 'productIdAutocompleteSuggester') );
	add_filter( 'vc_autocomplete_ts_product_deals_ids_render', array($vc_woo_vendor, 'productIdAutocompleteRender') );
	
	add_filter( 'vc_autocomplete_ts_team_members_ids_callback', 'drile_team_member_autocomplete_suggester' );
	add_filter( 'vc_autocomplete_ts_team_members_ids_render', 'drile_team_member_autocomplete_render' );
	
	$shortcode_field_cats = array();
	$shortcode_field_cats[] = array('ts_products', 'product_cats');
	$shortcode_field_cats[] = array('ts_products_widget', 'product_cats');
	$shortcode_field_cats[] = array('ts_product_deals', 'product_cats');
	$shortcode_field_cats[] = array('ts_products_in_category_tabs', 'product_cats');
	$shortcode_field_cats[] = array('ts_products_in_category_tabs', 'parent_cat');
	$shortcode_field_cats[] = array('ts_products_in_product_type_tabs', 'product_cats');
	$shortcode_field_cats[] = array('ts_product_categories', 'parent');
	$shortcode_field_cats[] = array('ts_product_categories', 'child_of');
	$shortcode_field_cats[] = array('ts_product_categories', 'ids');
	$shortcode_field_cats[] = array('ts_product_category_banners', 'product_cats');
		
	foreach( $shortcode_field_cats as $shortcode_field ){
		add_filter( 'vc_autocomplete_'.$shortcode_field[0].'_'.$shortcode_field[1].'_callback', array($vc_woo_vendor, 'productCategoryCategoryAutocompleteSuggester') );
		add_filter( 'vc_autocomplete_'.$shortcode_field[0].'_'.$shortcode_field[1].'_render', array($vc_woo_vendor, 'productCategoryCategoryRenderByIdExact') );
	}
}
?>