<?php 
/*** Include files in includes folder ***/
require_once get_template_directory().'/framework/includes/class-tgm-plugin-activation.php';

/*** Include files in framework folder ***/
require_once get_template_directory().'/framework/theme_functions.php';
require_once get_template_directory().'/framework/woo_functions.php';
require_once get_template_directory().'/framework/woo_hooks.php';
require_once get_template_directory().'/framework/quickshop.php';
require_once get_template_directory().'/framework/theme_control.php';
require_once get_template_directory().'/framework/theme_filter.php';
require_once get_template_directory().'/framework/mega_menu.php';

/*** Visual Composer plugin ***/
if( class_exists('Vc_Manager') && class_exists('WPBakeryVisualComposerAbstract') ){
	require_once get_template_directory().'/framework/vc_extension/vc_map.php';
	require_once get_template_directory().'/framework/vc_extension/update_param.php';
	
	vc_set_shortcodes_templates_dir(get_template_directory() . '/framework/vc_extension/templates');
}
?>