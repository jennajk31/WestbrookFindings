<?php 
/**
 * Plugin Name: ThemeSky
 * Plugin URI: http://theme-sky.com
 * Description: Add shortcodes and custom post types for the Drile Theme
 * Version: 1.2.7
 * Author: ThemeSky Team
 * Author URI: http://theme-sky.com
 */
if( !defined('THEMESKY_VERSION') ){
	define('THEMESKY_VERSION', '1.2.7');
}

class ThemeSky_Plugin{

	function __construct(){
		$this->load_language_file();
		$this->include_files();
		$this->register_widgets();
		
		/* Allow HTML in Category Descriptions */
		remove_filter('pre_term_description', 'wp_filter_kses');
		remove_filter('pre_link_description', 'wp_filter_kses');
		remove_filter('pre_link_notes', 'wp_filter_kses');
		remove_filter('term_description', 'wp_kses_data');
		
		/* Dont support custom header */
		add_action('after_setup_theme', array($this, 'remove_theme_support_custom_header'), 99 );
		
		/* Template redirect */
		add_action('template_redirect', array($this, 'template_redirect'));
	}
	
	function load_language_file(){
		load_plugin_textdomain('themesky', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	function include_files(){
		require_once('functions.php');
		require_once('register_post_type.php');
		require_once('class-shortcodes.php');
		require_once('woo_term.php');
		require_once('woo_filter_by_color.php');
		require_once('woo_filter_by_material.php');
		require_once('ads-banner.php');
		require_once('metaboxes/metaboxes.php');
		require_once('importer/importer.php');
	}
	
	function register_widgets(){
		$file_names = array('single_image', 'mailchimp_subscription', 'product_filter_by_color', 'product_filter_by_availability'
							, 'product_filter_by_brand', 'product_filter_by_price', 'social_icons', 'products', 'blogs', 'recent_comments'
							, 'facebook_page', 'instagram', 'product_categories', 'product_filter_by_material');
		foreach( $file_names as $file_name ){
			$file = plugin_dir_path( __FILE__ ) . '/widgets/' . $file_name . '.php';
			if( file_exists($file) ){
				require_once($file);
			}
		}
	}
	
	function remove_theme_support_custom_header(){
		remove_theme_support( 'custom-header' );
	}
	
	function template_redirect(){
		if( is_singular('product') ){
			add_filter('wp_get_attachment_image_attributes', array($this, 'unset_srcset_on_cloudzoom'), 9999);
			add_filter('wp_calculate_image_sizes', '__return_false', 9999);
			add_filter('wp_calculate_image_srcset', '__return_false', 9999);
			remove_filter('the_content', 'wp_make_content_images_responsive');
		}
	}
	
	function unset_srcset_on_cloudzoom( $attr ){
		if( isset($attr['sizes']) ){
			unset($attr['sizes']);
		}
		if( isset($attr['srcset']) ){
			unset($attr['srcset']);
		}
		return $attr;
	}
	
}
new ThemeSky_Plugin();
?>