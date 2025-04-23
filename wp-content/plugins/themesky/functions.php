<?php 
/*** Header Social Icons ***/
if( !function_exists('ts_header_social_icons') ){
	function ts_header_social_icons(){
		if( function_exists('drile_get_theme_options') && drile_get_theme_options('ts_enable_header_social_icons') ){
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'templates/header-social-icons.php';
			$icons_html = ob_get_clean();
			echo apply_filters('ts_header_social_icons_html', $icons_html);
		}
	}
}

/*** Product - Blog Social Sharing ***/
if( !function_exists('ts_use_sharethis') ){
	function ts_use_sharethis(){
		if( !function_exists('drile_get_theme_options') ){
			return false;
		}
		$theme_options = drile_get_theme_options();
		$sharethis_key = '';
		if( is_singular('post') || is_singular('ts_portfolio') ){
			if( $theme_options['ts_blog_details_sharing_sharethis'] && $theme_options['ts_blog_details_sharing_sharethis_key'] ){
				$sharethis_key = $theme_options['ts_blog_details_sharing_sharethis_key'];
			}
		}
		if( is_singular('product') ){
			if( $theme_options['ts_prod_sharing_sharethis'] && $theme_options['ts_prod_sharing_sharethis_key'] ){
				$sharethis_key = $theme_options['ts_prod_sharing_sharethis_key'];
			}
		}
		return $sharethis_key;
	}
}

if( !function_exists('ts_template_social_sharing') ){
	function ts_template_social_sharing(){
		if( ts_use_sharethis() ){
			echo '<div class="sharethis-inline-share-buttons"></div>';
		}
		else{
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'templates/social-sharing.php';
			$icons_html = ob_get_clean();
			echo apply_filters('ts_social_sharing_html', $icons_html);
		}
	}
}

add_action('wp_head', 'ts_add_sharethis_script');
if( !function_exists('ts_add_sharethis_script') ){
	function ts_add_sharethis_script(){
		$sharethis_key = ts_use_sharethis();
		if( $sharethis_key ){
		?>
		<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=<?php echo esc_attr($sharethis_key) ?>&product=inline-share-buttons' async='async'></script>
		<?php
		}
	}
}