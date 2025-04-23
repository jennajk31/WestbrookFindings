<?php 
$options = array();
		
$options[] = array(
				'id'		=> 'portfolio_url'
				,'label'	=> esc_html__('Portfolio URL', 'themesky')
				,'desc'		=> esc_html__('Enter URL to the live version of the project', 'themesky')
				,'type'		=> 'text'
			);
			
$options[] = array(
				'id'		=> 'client'
				,'label'	=> esc_html__('Client', 'themesky')
				,'desc'		=> ''
				,'type'		=> 'text'
			);
			
$options[] = array(
				'id'		=> 'year'
				,'label'	=> esc_html__('Year', 'themesky')
				,'desc'		=> ''
				,'type'		=> 'text'
			);
			
$options[] = array(
				'id'		=> 'video_url'
				,'label'	=> esc_html__('Video URL', 'themesky')
				,'desc'		=> esc_html__('Enter Youtube or Vimeo video URL. Display this video instead of the featured image on the detail page', 'themesky')
				,'type'		=> 'text'
			);
			
$options[] = array(
				'id'		=> 'portfolio_custom_field'
				,'label'	=> esc_html__('Custom Field', 'themesky')
				,'desc'		=> ''
				,'type'		=> 'select'
				,'options'	=> array(
									'0'		=> esc_html__('Default', 'themesky')
									,'1'	=> esc_html__('Override', 'themesky')
								)
			);
			
$options[] = array(
				'id'		=> 'portfolio_custom_field_title'
				,'label'	=> esc_html__('Custom Field Title', 'themesky')
				,'desc'		=> ''
				,'type'		=> 'text'
			);
			
$options[] = array(
				'id'		=> 'portfolio_custom_field_content'
				,'label'	=> esc_html__('Custom Field Content', 'themesky')
				,'desc'		=> ''
				,'type'		=> 'textarea'
			);
?>