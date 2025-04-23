<?php 
class TS_Product_Filter_By_Material{
	public $attr_slug = 'pa_material';
	public function __construct(){
		$this->init_handle();
		add_action( 'init', array($this, 'add_image_size') );
	}
	
	function init_handle(){
		add_action( $this->attr_slug . '_edit_form_fields', array($this, 'edit_material_attribute'), 1000, 2 );
		add_action( $this->attr_slug . '_add_form_fields', array($this, 'add_material_attribute'), 1000 );
		
		add_action( 'created_term', array($this, 'save_material_fields'), 10,3 );
		add_action( 'edit_term', array($this, 'save_material_fields'), 10,3 );
		add_action( 'delete_term', array($this, 'remove_material_fields'), 10,3 );
	}
	
	function add_image_size(){
		if( !class_exists('WooCommerce') ){
			return;
		}
		$attribute_name_array = wc_get_attribute_taxonomy_names();
		$taxonomy_exists = in_array($this->attr_slug, $attribute_name_array);
		if( $taxonomy_exists ){
			add_image_size('ts_prod_material_thumb', 46, 46, true);
		}
	}
	
	function add_material_attribute(){
		$placeholder = wc_placeholder_img_src();
		?>
		<div class="form-field">
			<label><?php esc_html_e( 'Thumbnail Image', 'themesky' ); ?></label>
			<input name="ts_material_thumb_id" type="hidden" class="ts_material_thumb_id" value="" />
			<img src="<?php echo esc_url($placeholder) ?>" class="ts_material_preview_image" data-placeholder="<?php echo esc_url($placeholder) ?>" /><br />
			<input class="ts_material_upload_image_button button" type="button" value="<?php esc_attr_e('Choose Image', 'themesky') ?>" />
			<input class="ts_material_remove_image_button button" type="button" value="<?php esc_attr_e('Remove Image', 'themesky') ?>" />
		</div>
		
		<script type="text/javascript">
			jQuery(function($){
				"use strict";
				
				$('.ts_material_remove_image_button').hide();

				var file_frame;

				$(document).on( 'click', '.ts_material_upload_image_button', function( event ){

					event.preventDefault();

					if ( file_frame ) {
						file_frame.open();
						return;
					}

					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'themesky' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'themesky' ); ?>',
						},
						multiple: false
					});

					file_frame.on( 'select', function() {
						var attachment = file_frame.state().get('selection').first().toJSON();

						$('input.ts_material_thumb_id').val( attachment.id );
						$('.ts_material_preview_image').attr('src', attachment.url );
						$('.ts_material_remove_image_button').show();
					});

					file_frame.open();
				});

				$(document).on( 'click', '.ts_material_remove_image_button', function( event ){
					$('.ts_material_preview_image').attr('src', $('.ts_material_preview_image').data('placeholder'));
					$('input.ts_material_thumb_id').val('');
					$('.ts_material_remove_image_button').hide();
					return false;
				});
			});
		</script>
		<?php
	}
	
	function edit_material_attribute( $term, $taxonomy ){
		$placeholder = wc_placeholder_img_src();
		$thumb_id = get_term_meta($term->term_id, 'ts_material_thumb_id', true);
		
		if( $thumb_id ){
			$image = wp_get_attachment_thumb_url( $thumb_id );
		}
		else{
			$image = $placeholder;
		}
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php esc_html_e( 'Thumbnail Image', 'themesky' ); ?></label></th>
			<td>
				<input name="ts_material_thumb_id" type="hidden" class="ts_material_thumb_id" value="<?php echo absint($thumb_id);?>" />
				<img src="<?php echo esc_url( $image ); ?>" class="ts_material_preview_image" data-placeholder="<?php echo esc_url($placeholder) ?>" /><br />
				<input class="ts_material_upload_image_button button" type="button" value="<?php esc_attr_e('Choose Image', 'themesky') ?>" />
				<input class="ts_material_remove_image_button button" type="button" value="<?php esc_attr_e('Remove Image', 'themesky') ?>" />
			</td>
		</tr>
		<script type="text/javascript">
			jQuery(function($){
				"use strict";
				
				if( $('input.ts_material_thumb_id').val() == '0' ){
					$('.ts_material_remove_image_button').hide();
				}

				var file_frame;

				$(document).on( 'click', '.ts_material_upload_image_button', function( event ){

					event.preventDefault();

					if ( file_frame ) {
						file_frame.open();
						return;
					}

					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php esc_html_e( 'Choose an image', 'themesky' ); ?>',
						button: {
							text: '<?php esc_html_e( 'Use image', 'themesky' ); ?>',
						},
						multiple: false
					});

					file_frame.on( 'select', function() {
						var attachment = file_frame.state().get('selection').first().toJSON();

						$('input.ts_material_thumb_id').val( attachment.id );
						$('.ts_material_preview_image').attr('src', attachment.url );
						$('.ts_material_remove_image_button').show();
					});

					file_frame.open();
				});

				$(document).on( 'click', '.ts_material_remove_image_button', function( event ){
					$('.ts_material_preview_image').attr('src', $('.ts_material_preview_image').data('placeholder'));
					$('input.ts_material_thumb_id').val('');
					$('.ts_material_remove_image_button').hide();
					return false;
				});
			});
		</script>
		<?php
	}
	
	function save_material_fields( $term_id, $tt_id, $taxonomy ){
		if( isset($_POST['ts_material_thumb_id']) ){
			update_term_meta( $term_id, 'ts_material_thumb_id', $_POST['ts_material_thumb_id'] );
		}
	}
	
	function remove_material_fields( $term_id, $tt_id, $taxonomy ){
		delete_term_meta( $term_id, 'ts_material_thumb_id' );
	}
}
new TS_Product_Filter_By_Material();
?>