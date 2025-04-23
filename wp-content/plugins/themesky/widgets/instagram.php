<?php
add_action('widgets_init', 'ts_instagram_load_widgets');

function ts_instagram_load_widgets()
{
	register_widget('TS_Instagram_Widget');
}

if(!class_exists('TS_Instagram_Widget')){
	class TS_Instagram_Widget extends WP_Widget {
		
		public $access_token, $base_access_token, $number, $option_key = 'ts_instagram_tokens';

		function __construct(){
			$widgetOps = array('classname' => 'ts-instagram-widget', 'description' => esc_html__('Display your photos from Instagram', 'themesky'));
			parent::__construct('ts_instagram', esc_html__('TS - Instagram', 'themesky'), $widgetOps);
		}

		function widget( $args, $instance ) {
			
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			
			$username 		= $instance['username'];
			$access_token 	= isset($instance['access_token'])?$instance['access_token']:'';
			$number 		= absint($instance['number']);
			$column 		= absint($instance['column']);
			$size 			= $instance['size'];
			$target 		= $instance['target'];
			$cache_time 	= absint($instance['cache_time']);
			
			$is_slider 	= isset($instance['is_slider'])?$instance['is_slider']:0;
			$show_nav 	= isset($instance['show_nav'])?$instance['show_nav']:1;
			$auto_play 	= isset($instance['auto_play'])?$instance['auto_play']:1;
			$margin 	= isset($instance['margin'])?$instance['margin']:0;
			
			if( !$username && !$access_token ){
				return;
			}
			
			$this->base_access_token = $access_token;
			$this->number = $number;
			
			if( $cache_time == 0 ){
				$cache_time = 12;
			}
			if( $is_slider && $show_nav ){
				$before_widget = str_replace('widget-container', 'widget-container has-nav', $before_widget);
			}
			echo $before_widget;
			
			if( $title ){
				echo $before_title . $title . $after_title; 
			}
			unset($instance['title']);
			$cache_key = 'instagram_' . md5( implode('', $instance) );
			
			$cache = get_transient($cache_key);
			
			if( $cache !== false ){
				echo $cache;
			}
			else{
				$media_array = array();
				if( $username ){
					$media_array = $this->scrape_instagram( $username, $size );
				}
				
				if( $this->base_access_token ){ // always refresh if added
					$this->maybe_clean_token();
					$refresh_result = $this->maybe_refresh_token();
					if( is_wp_error($refresh_result) ){
						echo esc_html( $refresh_result->get_error_message() );
						$this->base_access_token = ''; // dont get data
					}
				}
				
				if ( is_wp_error( $media_array ) || empty( $media_array ) ) {
					if( $this->base_access_token ){
						$media_array = $this->get_data_with_token();
						if( is_wp_error( $media_array ) ){
							echo esc_html( $media_array->get_error_message() );
						}
					}
					else if( is_wp_error( $media_array ) ){ // maybe use username
						echo esc_html( $media_array->get_error_message() );
					}
				}
				
				if( !is_wp_error( $media_array ) && !empty( $media_array ) ){
					ob_start();
					$classes = array();
					$classes[] = 'ts-instagram-wrapper items';
					$classes[] = 'columns-' . $column;
					
					$data_attr = array();
					if( $is_slider ){
						$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
						$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
						$data_attr[] = 'data-margin="'.absint($margin).'"';
						$data_attr[] = 'data-columns="'.absint($column).'"';
						
						$classes[] = 'ts-slider loading';
					}
					?>
					<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr); ?>>
						<?php foreach( $media_array as $index => $item ){
							$item_class = '';
							if( $index % $column == 0 ){
								$item_class = 'first';
							}
							elseif( $index % $column == ($column - 1) ){
								$item_class = 'last';
							}
						?>
						<div class="item <?php echo esc_attr($item_class); ?>">
							<a href="<?php echo esc_url( $item['permalink'] ) ?>" target="<?php echo esc_attr( $target ) ?>">
								<img class="ts-lazy-load" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201%201'%3E%3C/svg%3E" data-src="<?php echo esc_url( $item['media_url'] ) ?>" alt="<?php echo esc_attr( $item['caption'] ) ?>" title="<?php echo esc_attr( $item['caption'] ) ?>" />
							</a>
						</div>
						<?php } ?>
					</div>
					<?php
					$output = ob_get_clean();
					echo $output;
					set_transient($cache_key, $output, $cache_time * HOUR_IN_SECONDS);
				}
			}
			echo $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;	
			$instance['title'] 				=  strip_tags($new_instance['title']);
			$instance['username'] 			=  $new_instance['username'];
			$instance['access_token'] 		=  $new_instance['access_token'];
			$instance['number'] 			=  $new_instance['number'];
			$instance['column'] 			=  $new_instance['column'];
			$instance['size'] 				=  $new_instance['size'];									
			$instance['target'] 			=  $new_instance['target'];									
			$instance['cache_time'] 		=  $new_instance['cache_time'];									
			return $instance;
		}

		function form( $instance ) {
			$array_default = array(
							'title'			=> 'Instagram'
							,'username' 	=> ''
							,'access_token' => ''
							,'number' 		=> 9
							,'column' 		=> 3
							,'size' 		=> 'large'
							,'target' 		=> '_self'
							,'cache_time'	=> 12
							);
							
			$instance = wp_parse_args( (array) $instance, $array_default );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Enter your title', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php esc_html_e('Username', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($instance['username']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php esc_html_e('Access Token', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo esc_attr($instance['access_token']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of photos', 'themesky'); ?> </label>
				<input class="widefat" type="number" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo esc_attr($instance['number']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('column'); ?>"><?php esc_html_e('Column', 'themesky'); ?> </label>
				<select class="widefat" id="<?php echo $this->get_field_id('column'); ?>" name="<?php echo $this->get_field_name('column'); ?>" >
					<?php for( $i = 2; $i <= 6; $i++ ): if( $i == 5 ){ continue; } ?>
					<option value="<?php echo $i; ?>" <?php selected($instance['column'], $i); ?> ><?php echo $i; ?></option>
					<?php endfor; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('size'); ?>"><?php esc_html_e('Size', 'themesky'); ?> </label>
				<select class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" >
					<option value="thumbnail" <?php selected($instance['size'], 'thumbnail'); ?> ><?php esc_html_e('Thumbnail', 'themesky') ?></option>
					<option value="small" <?php selected($instance['size'], 'small'); ?> ><?php esc_html_e('Small', 'themesky') ?></option>
					<option value="large" <?php selected($instance['size'], 'large'); ?> ><?php esc_html_e('Large', 'themesky') ?></option>
					<option value="original" <?php selected($instance['size'], 'original'); ?> ><?php esc_html_e('Original', 'themesky') ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('target'); ?>"><?php esc_html_e('Target', 'themesky'); ?> </label>
				<select class="widefat" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" >
					<option value="_self" <?php selected($instance['target'], '_self'); ?> ><?php esc_html_e('Self', 'themesky') ?></option>
					<option value="_blank" <?php selected($instance['target'], '_blank'); ?> ><?php esc_html_e('New window tab', 'themesky') ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php esc_html_e('Cache time (hours)', 'themesky'); ?> </label>
				<input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" value="<?php echo esc_attr($instance['cache_time']); ?>" />
			</p>
			
			<?php 
		}
		
		function connect( $url ){
			$args = array(
				'timeout' => 60
				,'sslverify' => false
			);
			$response = wp_remote_get( $url, $args );

			if( ! is_wp_error( $response ) ){
				$response = json_decode( str_replace( '%22', '&rdquo;', $response['body'] ), true );
			}

			if( isset($response['data']) ){
				return $response['data'];
			}
			else{
				return $response;
			}
		}
		
		function maybe_clean_token(){
			$split_token = explode( ' ', trim( $this->base_access_token ) );
			$this->base_access_token = preg_replace("/[^A-Za-z0-9 ]/", '', $split_token[0] );
			
			if( substr_count ( $this->base_access_token , '.' ) < 3 ){
				$this->access_token = $this->base_access_token;
				return;
			}

			$parts = explode( '.', trim( $this->base_access_token ) );
			$last_part = $parts[2] . $parts[3];
			$this->access_token = $parts[0] . '.' . base64_decode( $parts[1] ) . '.' . base64_decode( $last_part );
		}
		
		// Token need to be refreshed every 60 days
		function maybe_refresh_token(){	
			$need_refresh = true;
			$value = get_option($this->option_key, array());
			if( isset($value[$this->base_access_token]['timestamp']) ){
				$current_token = $value[$this->base_access_token]['refreshed_token'];
				$timestamp = $value[$this->base_access_token]['timestamp'];
				if( $timestamp > time() ){
					$need_refresh = false;
				}
				$this->access_token = $current_token;
			}
			else if( !is_array($value) ){
				$value = array();
			}
			
			if( $need_refresh ){
				$url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $this->access_token;
				$data = $this->connect( $url );
				
				// show error here if failed
				if( isset($data['access_token']) ){
					if( !isset($value[$this->base_access_token]) ){
						$value[$this->base_access_token] = array();
					}
					$value[$this->base_access_token]['refreshed_token'] = $data['access_token'];
					$value[$this->base_access_token]['timestamp'] = time() + MONTH_IN_SECONDS; // refresh after a month
					
					// delete unuse token before saving
					foreach( $value as $t => $v ){
						if( $t != $this->base_access_token && isset($v['timestamp']) && ( $v['timestamp'] + YEAR_IN_SECONDS ) < time() ){
							unset($value[$t]);
						}
					}
					
					update_option($this->option_key, $value); // use refreshed token for future
					$this->access_token = $data['access_token'];
				}
				else{
					$this->access_token = $this->base_access_token;
				}
			}
			return true;
		}
		
		function get_user_id(){
			$value = get_option($this->option_key, array());
			
			if( isset($value[$this->base_access_token]['user_id']) ){
				return $value[$this->base_access_token]['user_id'];
			}
			
			$url = 'https://graph.instagram.com/me?fields=user_id,username&access_token=' . $this->access_token;
			$response = $this->connect( $url );
			
			if( isset($response['user_id']) ){
				if( !isset($value[$this->base_access_token]) ){
					$value[$this->base_access_token] = array();
				}
				$value[$this->base_access_token]['user_id'] = $response['user_id'];
				update_option($this->option_key, $value);
				return $response['user_id'];
			}
			else{
				return new WP_Error( 'invalid_response', esc_html__( 'Unable to communicate with Instagram.', 'themesky' ) );
			}
		}
		
		function get_data_with_token(){
			$user_id = $this->get_user_id();
			
			if( is_wp_error($user_id) ){
				return $user_id;
			}
			
			$number = $this->number * 2; // prevent have video/album
			
			$url = 'https://graph.instagram.com/'.$user_id.'/media?fields=media_url,caption,id,media_type,permalink&limit='.$number.'&access_token=' . $this->access_token;
			
			$response = $this->connect( $url );
			
			if( !is_array($response) ){
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram has returned invalid data.', 'themesky' ) );
			}
			
			if( isset($response['error']['message']) ){
				return new WP_Error( 'error_response', $response['error']['message'] );
			}
			
			$items = array();
			foreach( $response as $node ){
				if( !isset($node['media_type']) || $node['media_type'] != 'IMAGE' ){
					continue;
				}
				$item = array();
				$item['permalink'] =  $node['permalink'];
				$item['media_url'] =  $node['media_url'];
				$item['caption'] = isset($node['caption'])?$node['caption']:__('Instagram Image', 'themesky');
				$items[] = $item;
			}
			
			return array_slice( $items, 0, $this->number );
		}
		
		function scrape_instagram( $username, $size ) {
			$username = trim( strtolower( $username ) );
			
			switch ( substr( $username, 0, 1 ) ) {
				case '#':
					$url = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
					break;

				default:
					$url = 'https://instagram.com/' . str_replace( '@', '', $username );
					break;
			}

			$remote = wp_remote_get( $url );

			if ( is_wp_error( $remote ) ) {
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'themesky' ) );
			}

			if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'themesky' ) );
			}

			$shards = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], true );

			if ( ! $insta_array ) {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'themesky' ) );
			}

			if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			} elseif ( isset( $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			} else {
				return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'themesky' ) );
			}

			if ( ! is_array( $images ) ) {
				return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'themesky' ) );
			}

			$instagram = array();

			foreach ( $images as $image ) {
				if ( true === $image['node']['is_video'] ) {
					continue; // dont show video
				} else {
					$type = 'image';
				}

				$caption = esc_html__( 'Instagram Image', 'themesky' );
				if ( ! empty( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
					$caption = wp_kses( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array() );
				}
				
				$item = array(
					'caption' 	  	=> $caption
					,'permalink'   	=> trailingslashit( '//instagram.com/p/' . $image['node']['shortcode'] )
				);
				
				switch( $size ){
					case 'thumbnail':
						$item['media_url'] = preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src'] );
					break;
					case 'small':
						$item['media_url'] = preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src'] );
					break;
					case 'large':
						$item['media_url'] = preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][4]['src'] );
					break;
					default:
						$item['media_url'] = preg_replace( '/^https?\:/i', '', $image['node']['display_url'] );
				}
				
				$instagram[] = $item;
			}

			if ( ! empty( $instagram ) ) {
				return array_slice( $instagram, 0, $this->number );
			} else {
				return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'themesky' ) );
			}
		}
	}
}

