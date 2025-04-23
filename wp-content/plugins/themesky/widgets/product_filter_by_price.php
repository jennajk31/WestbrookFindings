<?php 
add_action('widgets_init', 'ts_product_filter_by_price_load_widget');

function ts_product_filter_by_price_load_widget()
{
	register_widget('TS_Product_Filter_By_Price_Widget');
}

class TS_Product_Filter_By_Price_Widget extends WP_Widget{

	function __construct(){
		$widgetOps = array('classname' => 'product-filter-by-price', 'description' => esc_html__('Filter products in the pre-defined prices', 'themesky'));
		parent::__construct('ts_product_filter_by_price', esc_html__('TS - Product Filter By Price', 'themesky'), $widgetOps);
	}
	
	function widget( $args, $instance ) {
		global $wp, $wp_the_query;
		extract($args);
		
		if( !class_exists('WooCommerce') ){
			return;
		}
		if( !is_post_type_archive( 'product' ) && !is_tax( get_object_taxonomies( 'product' ) ) ){
			return;
		}
		
		if( ! $wp_the_query->post_count ){
			return;
		}
		
		$title = apply_filters('widget_title', $instance['title']);
		$all_text = $instance['all_text'];
		$over_text = $instance['over_text'];
		
		$min_prices = (array) apply_filters('ts_filter_by_price_min_prices', $instance['min_prices']);
		$max_prices = (array) apply_filters('ts_filter_by_price_max_prices', $instance['max_prices']);
		
		if( !$min_prices || !$max_prices ){
			return;
		}
		
		$over_price = max( $max_prices );
		
		$selected_min_price = isset($_GET['min_price'])?absint($_GET['min_price']):'';
		$selected_max_price = isset($_GET['max_price'])?absint($_GET['max_price']):'';
		
		$selected_over_price = ($selected_min_price == $over_price && $selected_max_price == '')?true:false;
		
		if ( '' == get_option( 'permalink_structure' ) ) {
			$form_action = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
		} else {
			$form_action = preg_replace( '%\/page/[0-9]+%', '', home_url( trailingslashit( $wp->request ) ) );
		}
		
		echo $before_widget;
			
		if( $title ){
			echo $before_title . $title . $after_title;
		}
		
		?>
		<div class="product-filter-by-price-wrapper">
			<ul>
				<?php if( $all_text ){ ?>
				<li data-min="" data-max="">
					<label><?php echo esc_html($all_text); ?></label>
				</li>
				<?php } ?>
				
				<?php foreach( $min_prices as $i => $min_pice ){
					if( !isset($max_prices[$i]) ){
						continue;
					}
					$max_price = $max_prices[$i];
					if( $max_price <= $min_pice ){
						continue;
					}
					
					$class = ($selected_min_price == $min_pice && $selected_max_price == $max_price)?'chosen':'';
				?>
				<li data-min="<?php echo esc_attr($min_pice); ?>" data-max="<?php echo esc_attr($max_price); ?>" class="<?php echo esc_attr($class); ?>">
					<label><?php echo $this->format_price($min_pice); ?> - <?php echo $this->format_price($max_price); ?></label>
				</li>
				<?php } ?>
				
				<?php if( $over_text ){ ?>
				<li data-min="<?php echo esc_attr($over_price); ?>" data-max="" class="<?php echo $selected_over_price?'chosen':''; ?>">
					<label><?php echo esc_html($over_text); ?> <?php echo $this->format_price($over_price); ?></label>
				</li>
				<?php } ?>
			</ul>
			
			<form method="get" action="<?php echo esc_url($form_action) ?>">
				<input type="hidden" name="min_price" value="" />
				<input type="hidden" name="max_price" value="" />
				<?php wc_query_string_form_fields( null, array( 'submit', 'paged', 'product-page', 'min_price', 'max_price' ) ); ?>
			</form>
		</div>
		<?php
		echo $after_widget;
	}
	
	function format_price( $price ){
		$price = number_format($price, 0, '.', wc_get_price_thousand_separator());
		return sprintf(get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $price);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['all_text'] = strip_tags($new_instance['all_text']);
		$instance['over_text'] = strip_tags($new_instance['over_text']);
		
		$instance['min_prices'] = (array) $new_instance['min_prices'];
		$instance['max_prices'] = (array) $new_instance['max_prices'];
		foreach( $instance['min_prices'] as $i => $min_price ){
			if( $min_price === '' || $instance['max_prices'][$i] === '' ){
				unset($instance['min_prices'][$i], $instance['max_prices'][$i]);
			}
		}
		$instance['min_prices'] = array_map('absint', $instance['min_prices']);
		$instance['max_prices'] = array_map('absint', $instance['max_prices']);
		sort( $instance['min_prices'] );
		sort( $instance['max_prices'] );
		return $instance;
	}

	function form( $instance ) {
		
		$defaults = array(
			'title' 		=> 'Price'
			,'min_prices'	=> array()
			,'max_prices'	=> array()
			,'all_text'		=> 'All'
			,'over_text'	=> 'Over'
		);
	
		$instance = wp_parse_args( (array) $instance, $defaults );
		extract($instance);
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title', 'themesky'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('all_text'); ?>"><?php esc_html_e('All text', 'themesky'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('all_text'); ?>" name="<?php echo $this->get_field_name('all_text'); ?>" type="text" value="<?php echo esc_attr($all_text); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('over_text'); ?>"><?php esc_html_e('Over text', 'themesky'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('over_text'); ?>" name="<?php echo $this->get_field_name('over_text'); ?>" type="text" value="<?php echo esc_attr($over_text); ?>" />
		</p>
		<p class="two-columns">
			<label><?php esc_html_e('From', 'themesky'); ?></label>
			<label><?php esc_html_e('To', 'themesky'); ?></label>
		</p>
		<?php for( $i = 0; $i < 6; $i++ ){ ?>
		<p class="two-columns">
			<input type="number" name="<?php echo $this->get_field_name('min_prices'); ?>[<?php echo esc_attr($i); ?>]" value="<?php echo isset($min_prices[$i])?$min_prices[$i]:''; ?>" min="0" />
			<input type="number" name="<?php echo $this->get_field_name('max_prices'); ?>[<?php echo esc_attr($i); ?>]" value="<?php echo isset($max_prices[$i])?$max_prices[$i]:''; ?>" min="0" />
		</p>
		<?php 
		}
	}
	
}
?>