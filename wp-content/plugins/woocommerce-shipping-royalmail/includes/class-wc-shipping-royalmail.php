<?php
/**
 * Shipping method class.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * WC_Shipping_Royalmail class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Royalmail extends WC_Shipping_Method {
	/**
	 * Whether debug setting is enabled or not.
	 *
	 * @var bool
	 */
	private $debug;

	/**
	 * Logger instance.
	 *
	 * @var WC_Logger_Interface
	 */
	private $logger;

	/**
	 * Packing method that is being used.
	 *
	 * @var string.
	 */
	protected $packing_method = '';

	/**
	 * Which rate types are being used.
	 *
	 * @var string.
	 */
	protected $rate_type = '';

	/**
	 * How the rates being offered.
	 *
	 * @var string.
	 */
	protected $offer_rates = '';

	/**
	 * Whether use compensation or not.
	 *
	 * @var string.
	 */
	protected $compensation_optional = '';

	/**
	 * List of custom services.
	 *
	 * @var array.
	 */
	public $custom_services = array();

	/**
	 * List of boxes.
	 *
	 * @var array.
	 */
	protected $boxes = array();

	/**
	 * Pre-defined services.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * Tax exemption lookup table.
	 *
	 * @var array
	 */
	private $is_taxed = array(
		Services::FIRST_CLASS                  => false,
		Services::FIRST_CLASS_SIGNED           => false,
		Services::SECOND_CLASS                 => false,
		Services::SECOND_CLASS_SIGNED          => false,

		Services::SPECIAL_DELIVERY_9AM         => true,
		Services::SPECIAL_DELIVERY_1PM         => true,

		Services::TRACKED_24                   => true,
		Services::TRACKED_24_SIGNED            => true,
		Services::TRACKED_24_AGE_VERIFICATION  => true,

		Services::TRACKED_48                   => true,
		Services::TRACKED_48_SIGNED            => true,
		Services::TRACKED_48_AGE_VERIFICATION  => true,

		Services::PARCELFORCE_EXPRESS_9        => true,
		Services::PARCELFORCE_EXPRESS_10       => true,
		Services::PARCELFORCE_EXPRESS_AM       => true,
		Services::PARCELFORCE_EXPRESS_24       => true,
		Services::PARCELFORCE_EXPRESS_48       => true,
		Services::PARCELFORCE_EXPRESS_48_LARGE => true,

		Services::INTERNATIONAL_STANDARD       => false,
		Services::INTERNATIONAL_TRACKED_SIGNED => false,
		Services::INTERNATIONAL_TRACKED        => false,
		Services::INTERNATIONAL_SIGNED         => false,
		Services::INTERNATIONAL_ECONOMY        => false,

		Services::PARCELFORCE_IRELANDEXPRESS   => true,
		Services::PARCELFORCE_GLOBALECONOMY    => true,
		Services::PARCELFORCE_GLOBALEXPRESS    => true,
		Services::PARCELFORCE_GLOBALPRIORITY   => true,
		Services::PARCELFORCE_GLOBALVALUE      => true,
	);

	/**
	 * List of services that are only available at certain post offices.
	 *
	 * @var array
	 */
	private $limited_availability = array(
		Services::FIRST_CLASS                  => false,
		Services::FIRST_CLASS_SIGNED           => false,
		Services::SECOND_CLASS                 => false,
		Services::SECOND_CLASS_SIGNED          => false,

		Services::SPECIAL_DELIVERY_9AM         => false,
		Services::SPECIAL_DELIVERY_1PM         => false,

		Services::TRACKED_24                   => false,
		Services::TRACKED_24_SIGNED            => false,
		Services::TRACKED_24_AGE_VERIFICATION  => false,

		Services::TRACKED_48                   => false,
		Services::TRACKED_48_SIGNED            => false,
		Services::TRACKED_48_AGE_VERIFICATION  => false,

		Services::PARCELFORCE_EXPRESS_9        => false,
		Services::PARCELFORCE_EXPRESS_10       => false,
		Services::PARCELFORCE_EXPRESS_AM       => false,
		Services::PARCELFORCE_EXPRESS_24       => false,
		Services::PARCELFORCE_EXPRESS_48       => false,
		Services::PARCELFORCE_EXPRESS_48_LARGE => true,

		Services::INTERNATIONAL_STANDARD       => false,
		Services::INTERNATIONAL_TRACKED_SIGNED => false,
		Services::INTERNATIONAL_TRACKED        => false,
		Services::INTERNATIONAL_SIGNED         => false,
		Services::INTERNATIONAL_ECONOMY        => false,

		Services::PARCELFORCE_IRELANDEXPRESS   => false,
		Services::PARCELFORCE_GLOBALECONOMY    => false,
		Services::PARCELFORCE_GLOBALEXPRESS    => false,
		Services::PARCELFORCE_GLOBALPRIORITY   => false,
		Services::PARCELFORCE_GLOBALVALUE      => false,
	);

	/**
	 * Service tips.
	 *
	 * @var array
	 */
	public $service_tips;

	/**
	 * Sets the box packer library to use.
	 *
	 * @var string
	 */
	public $box_packer_library;

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Instance ID.
	 *
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'royal_mail';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Royal Mail', 'woocommerce-shipping-royalmail' );
		$this->method_description = sprintf(
			// translators: %1$s is the regular price guide link, %3$s is the online price guide link, %2$s and %4$s is a closing anchor tag.
			__( 'Offer Royal Mail shipping rates automatically to your customers. Prices according to the 2025 %1$sregular price guide%2$s and %3$sonline price guide%4$s.', 'woocommerce-shipping-royalmail' ),
			'<a target="_blank" href="https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf">',
			'</a>',
			'<a target="_blank" href="https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf">',
			'</a>'
		);
		$this->supports = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);

		$this->service_tips = array(
			Services::PARCELFORCE_EXPRESS_48_LARGE => esc_html__( 'This service is only available at select post offices. Please contact your local branch to inquire about its specific availability.', 'woocommerce-shipping-royalmail' ),
		);

		$this->services = WC_RoyalMail::$service_names;

		$this->init();

		if ( true === $this->debug ) {
			$this->logger = wc_get_logger();
		}
	}

	/**
	 * Checks whether this shipping method is available or not.
	 *
	 * @param array $package Package to ship.
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( empty( $package['destination']['country'] ) ) {
			return false;
		}

		/**
		 * Filter to allow third party disable/enable the shipping method availability.
		 *
		 * @param boolean Flag for the availability.
		 * @param array Cart package.
		 *
		 * @since 2.5.0
		 */
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}

	/**
	 * Initialize settings.
	 *
	 * @version 2.5.0
	 * @since 2.5.0
	 */
	private function set_settings() {
		// Define user set variables.
		$this->title                 = $this->get_option( 'title', $this->method_title );
		$this->box_packer_library    = $this->get_option( 'box_packer_library', $this->get_default_box_packer_library() );
		$this->packing_method        = $this->get_option( 'packing_method', 'per_item' );
		$this->rate_type             = $this->get_option( 'rate_type', 'regular' );
		$this->offer_rates           = $this->get_option( 'offer_rates', 'all' );
		$this->compensation_optional = $this->get_option( 'compensation_optional', 'no' );
		$this->debug                 = 'yes' === $this->get_option( 'debug_mode' );
		$this->custom_services       = $this->get_option( 'services', array() );
		$this->boxes                 = $this->get_option( 'boxes', array() );
		$this->tax_status            = $this->get_option( 'tax_status' );
	}

	/**
	 * Init form fields and set properties from saved settings.
	 *
	 * @return void
	 */
	private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->set_settings();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		if ( ! defined( 'WC_ROYALMAIL_DEBUG' ) ) {
			define( 'WC_ROYALMAIL_DEBUG', $this->debug );
		}
	}

	/**
	 * Process settings on save
	 *
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$this->set_settings();
	}

	/**
	 * Render services matrix input in instance setting page.
	 *
	 * @return string HTML of matrix input
	 */
	public function generate_services_html() {
		ob_start();
		?>
		<tr valign="top" id="wc_royal_mail_service_options">
			<th scope="row" class="titledesc">
				<?php esc_html_e( 'Services', 'woocommerce-shipping-royalmail' ); ?>
				<p style="margin-top:30px;">
					<span style="margin-right:8px; background-color:#ffffb0; width:15px; height:15px; display:block; float:left; position:relative; top:3px;">&nbsp;</span>
					<strong style="display:block; overflow:hidden;"><?php esc_html_e( 'Only available at select post offices', 'woocommerce-shipping-royalmail' ); ?></strong><br />
					<span style="display:block;clear:both;"></span>
					<span style="margin-right:8px; background-color:#ffffff; width:15px; height:15px; display:block; float:left; position:relative; top:3px;">&nbsp;</span>
					<strong style="display:block; overflow:hidden;"><?php esc_html_e( 'Available at all post offices', 'woocommerce-shipping-royalmail' ); ?></strong>
					<span style="display:block;clear:both;"></span>
				</p>
			</th>
			<td class="forminp" rowspan="2">
				<table class="royal_mail_services widefat">
					<thead>
						<th class="sort">&nbsp;</th>
						<th width="1%">&nbsp;</th>
						<th><?php esc_html_e( 'Name', 'woocommerce-shipping-royalmail' ); ?></th>
						<th><?php esc_html_e( 'Enabled', 'woocommerce-shipping-royalmail' ); ?></th>
						<th><?php esc_html_e( 'Available on', 'woocommerce-shipping-royalmail' ); ?></th>
						<th>
						<?php
						/* translators: currency symbol */
						printf( esc_html__( 'Price Adjustment (%s)', 'woocommerce-shipping-royalmail' ), esc_html( get_woocommerce_currency_symbol() ) );
						?>
						</th>
						<th><?php esc_html_e( 'Price Adjustment (%)', 'woocommerce-shipping-royalmail' ); ?></th>
					</thead>
					<tbody>
						<?php
						$sort             = 0;
						$ordered_services = array();

						foreach ( $this->services as $code => $name ) {

							if ( isset( $this->custom_services[ $code ]['order'] ) ) {
								$sort = absint( $this->custom_services[ $code ]['order'] );
							}

							while ( isset( $ordered_services[ $sort ] ) ) {
								++$sort;
							}

							$ordered_services[ $sort ] = array( $code, $name );

							++$sort;
						}

						ksort( $ordered_services );

						foreach ( $ordered_services as $value ) {
							$code = $value[0];
							$name = $value[1];

							$row_color = true === $this->limited_availability[ $code ] ? '#ffffb0' : '#ffffff';
							$tip       = ! empty( $this->service_tips[ $code ] ) ? $this->service_tips[ $code ] : $code;
							?>
							<tr style="background-color:<?php echo esc_attr( $row_color ); ?>">
								<td class="sort"><input type="hidden" class="order" name="<?php echo esc_attr( "royal_mail_service[$code][order]" ); ?>" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : '' ); ?>" /></td>

								<td width="1%"><strong><?php echo '<img class="help_tip" data-tip="' . esc_attr( $tip ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />'; ?></strong></td>

								<td><input type="text" name="<?php echo esc_attr( "royal_mail_service[$code][name]" ); ?>" placeholder="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : '' ); ?>" size="50" /></td>

								<td width="10%"><input type="checkbox" name="<?php echo esc_attr( "royal_mail_service[$code][enabled]" ); ?>" <?php checked( ! empty( $this->custom_services[ $code ]['enabled'] ), true ); ?> /></td>

								<td><?php echo esc_html( $this->get_service_availability( $code ) ); ?></td>

								<td><input type="text" name="<?php echo esc_attr( "royal_mail_service[$code][adjustment]" ); ?>" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : '' ); ?>" size="4" /></td>

								<td><input type="text" name="<?php echo esc_attr( "royal_mail_service[$code][adjustment_percent]" ); ?>" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : '' ); ?>" size="4" /></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render boxes matrix input.
	 *
	 * @return string HTML boxes matrix input
	 */
	public function generate_box_packing_html() {
		ob_start();
		?>
		<tr valign="top" id="wc_royal_mail_packing_options">
			<th scope="row" class="titledesc"><?php esc_html_e( 'International Parcel Sizes', 'woocommerce-shipping-royalmail' ); ?></th>
			<td class="forminp">
				<style type="text/css">
					.royal_mail_boxes td, .royal_mail_services td {
						vertical-align: middle;
						padding: 4px 7px;
					}
					.royal_mail_boxes th, .royal_mail_services th {
						padding: 9px 7px;
					}
					.royal_mail_boxes td input {
						margin-right: 4px;
					}
					.royal_mail_boxes .check-column {
						vertical-align: middle;
						text-align: left;
						padding: 0 7px;
					}
					.royal_mail_services th.sort {
						width: 16px;
					}
					.royal_mail_services td.sort {
						cursor: move;
						width: 16px;
						padding: 0 16px;
						cursor: move;
						background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
					}
				</style>
				<table class="royal_mail_boxes widefat">
					<thead>
						<tr>
							<th class="check-column"><input type="checkbox" /></th>
							<th><?php esc_html_e( 'Name', 'woocommerce-shipping-royalmail' ); ?></th>
							<th><?php esc_html_e( 'Length', 'woocommerce-shipping-royalmail' ); ?></th>
							<th><?php esc_html_e( 'Width', 'woocommerce-shipping-royalmail' ); ?></th>
							<th><?php esc_html_e( 'Height', 'woocommerce-shipping-royalmail' ); ?></th>
							<th><?php esc_html_e( 'Weight of Box', 'woocommerce-shipping-royalmail' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="2">
								<a href="#" class="button plus insert"><?php esc_html_e( 'Add Box', 'woocommerce-shipping-royalmail' ); ?></a>
								<a href="#" class="button minus remove"><?php esc_html_e( 'Remove selected box(es)', 'woocommerce-shipping-royalmail' ); ?></a>
							</th>
							<th colspan="3">
								<small class="description"><?php esc_html_e( 'When calculating rates for international mail, items will be packed into these boxes depending on item dimensions and volume. The boxes will then be quoted accordingly.', 'woocommerce-shipping-royalmail' ); ?></small>
								<br/><br/>
								<small class="description"><?php esc_html_e( 'The parcels length, width and depth combined must not be no greater than 900mm. The greatest single dimension must not exceed 600mm', 'woocommerce-shipping-royalmail' ); ?></small>
							</th>
						</tr>
					</tfoot>
					<tbody id="rates">
						<?php
						if ( $this->boxes ) {
							foreach ( $this->boxes as $key => $box ) {
								?>
								<tr>
									<td class="check-column"><input type="checkbox" /></td>
									<td><input type="text" size="10" name="<?php echo esc_attr( "boxes_name[$key]" ); ?>" value="<?php echo isset( $box['name'] ) ? esc_attr( $box['name'] ) : ''; ?>" /></td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_length[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_length'] ); ?>" />mm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_width[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_width'] ); ?>" />mm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_height[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_height'] ); ?>" />mm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_box_weight[$key]" ); ?>" value="<?php echo esc_attr( $box['box_weight'] ); ?>" />g</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
				<script type="text/javascript">

					jQuery().ready(function(){

						jQuery('.royal_mail_boxes .insert').click( function() {
							var $tbody = jQuery('.royal_mail_boxes').find('tbody');
							var size = $tbody.find('tr').length;
							var code = '<tr class="new">\
									<td class="check-column"><input type="checkbox" /></td>\
									<td><input type="text" size="10" name="boxes_name[' + size + ']" /></td>\
									<td><input type="text" size="5" name="boxes_inner_length[' + size + ']" />mm</td>\
									<td><input type="text" size="5" name="boxes_inner_width[' + size + ']" />mm</td>\
									<td><input type="text" size="5" name="boxes_inner_height[' + size + ']" />mm</td>\
									<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" />g</td>\
								</tr>';

							$tbody.append( code );

							return false;
						} );

						jQuery('.royal_mail_boxes .remove').click(function() {
							var $tbody = jQuery('.royal_mail_boxes').find('tbody');

							$tbody.find('.check-column input:checked').each(function() {
								jQuery(this).closest('tr').hide().find('input').val('');
							});

							return false;
						});

						// Ordering
						jQuery('.royal_mail_services tbody').sortable({
							items:'tr',
							cursor:'move',
							axis:'y',
							handle: '.sort',
							scrollSensitivity:40,
							forcePlaceholderSize: true,
							helper: 'clone',
							opacity: 0.65,
							placeholder: 'wc-metabox-sortable-placeholder',
							start:function(event,ui){
								ui.item.css('background-color','#f6f6f6');
							},
							stop:function(event,ui){
								ui.item.removeAttr('style');
								royal_mail_services_row_indexes();
							}
						});

						function royal_mail_services_row_indexes() {
							jQuery('.royal_mail_services tbody tr').each(function(index, el){
								jQuery('input.order', el).val( parseInt( jQuery(el).index('.royal_mail_services tr') ) );
							});
						};

					});

				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Validate box packing fields.
	 *
	 * @param mixed $key Key.
	 * @return array
	 */
	public function validate_box_packing_field( $key ) {
		// No need to verify. It has been verified on `WC_Settings_Shipping::instance_settings_screen()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$boxes_name         = isset( $_POST['boxes_name'] ) ? wc_clean( wp_unslash( $_POST['boxes_name'] ) ) : array();
		$boxes_inner_length = isset( $_POST['boxes_inner_length'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_length'] ) ) : array();
		$boxes_inner_width  = isset( $_POST['boxes_inner_width'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_width'] ) ) : array();
		$boxes_inner_height = isset( $_POST['boxes_inner_height'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_height'] ) ) : array();
		$boxes_box_weight   = isset( $_POST['boxes_box_weight'] ) ? wc_clean( wp_unslash( $_POST['boxes_box_weight'] ) ) : array();
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$boxes       = array();
		$box_counter = is_array( $boxes_inner_length ) ? count( $boxes_inner_length ) : 0;

		for ( $i = 0; $i < $box_counter; $i++ ) {

			if (
				! empty( $boxes_name[ $i ] ) &&
				! empty( $boxes_inner_length[ $i ] ) &&
				! empty( $boxes_inner_width[ $i ] ) &&
				! empty( $boxes_inner_height[ $i ] )
			) {
				$boxes[] = array(
					'name'         => $boxes_name[ $i ],
					'inner_length' => floatval( $boxes_inner_length[ $i ] ),
					'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
					'inner_height' => floatval( $boxes_inner_height[ $i ] ),
					'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
				);
			}
		}

		return $boxes;
	}

	/**
	 * Validate services fields.
	 *
	 * @param mixed $key Key.
	 * @return array
	 */
	public function validate_services_field( $key ) {
		// No need to verify. It has been verified on `WC_Settings_Shipping::instance_settings_screen()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$posted_services = isset( $_POST['royal_mail_service'] ) ? wc_clean( wp_unslash( $_POST['royal_mail_service'] ) ) : array();
		$services        = array();
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		foreach ( $posted_services as $code => $settings ) {
			$services[ $code ] = array(
				'name'               => isset( $settings['name'] ) ? $settings['name'] : '',
				'order'              => isset( $settings['order'] ) ? $settings['order'] : '',
				'enabled'            => isset( $settings['enabled'] ) ? true : false,
				'adjustment'         => isset( $settings['adjustment'] ) ? floatval( $settings['adjustment'] ) : '',
				'adjustment_percent' => isset( $settings['adjustment_percent'] ) ? floatval( str_replace( '%', '', $settings['adjustment_percent'] ) ) : '',
			);
		}

		return $services;
	}

	/**
	 * Get service availability.
	 *
	 * @param string $service_id service ID.
	 *
	 * @return string
	 */
	public function get_service_availability( $service_id ) {
		if ( in_array( $service_id, WC_RoyalMail::get_available_both_services(), true ) ) {
			return __( 'Regular &amp; Online', 'woocommerce-shipping-royalmail' );
		} elseif ( in_array( $service_id, WC_RoyalMail::get_regular_services_only(), true ) ) {
			return __( 'Regular', 'woocommerce-shipping-royalmail' );
		} elseif ( in_array( $service_id, WC_RoyalMail::get_online_services_only(), true ) ) {
			return __( 'Online', 'woocommerce-shipping-royalmail' );
		}

		return '';
	}

	/**
	 * Init form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'                     => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-royalmail' ),
				'type'        => 'text',
				'description' => '',
				'default'     => __( 'Royal Mail', 'woocommerce-shipping-royalmail' ),
			),
			'rates'                     => array(
				'title'       => __( 'Rates and Services', 'woocommerce-shipping-royalmail' ),
				'type'        => 'title',
				'description' => '',
			),
			'tax_status'                => array(
				'title'       => __( 'Tax Status', 'woocommerce-shipping-royalmail' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Taxable', 'woocommerce-shipping-royalmail' ),
					'none'    => __( 'None', 'woocommerce-shipping-royalmail' ),
				),
			),
			'packing_method'            => array(
				'title'   => __( 'Parcel Packing Method', 'woocommerce-shipping-royalmail' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'packing_method',
				'options' => array(
					'per_item'    => __( 'Default: Pack items individually', 'woocommerce-shipping-royalmail' ),
					'box_packing' => __( 'Recommended: Pack items into boxes together', 'woocommerce-shipping-royalmail' ),
				),
			),
			'boxes'                     => array(
				'type' => 'box_packing',
			),
			'rate_type'                 => array(
				'title'       => __( 'Rate Type', 'woocommerce-shipping-royalmail' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'regular',
				'options'     => array(
					'regular' => __( 'Regular prices', 'woocommerce-shipping-royalmail' ),
					'online'  => __( 'Online prices', 'woocommerce-shipping-royalmail' ),
				),
			),
			'offer_rates'               => array(
				'title'       => __( 'Offer Rates', 'woocommerce-shipping-royalmail' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					'all'      => __( 'Offer the customer all returned rates', 'woocommerce-shipping-royalmail' ),
					'cheapest' => __( 'Offer the customer the cheapest rate only, anonymously', 'woocommerce-shipping-royalmail' ),
				),
			),
			'compensation_optional'     => array(
				'title'       => __( 'Ignore Maximum Compensation', 'woocommerce-shipping-royalmail' ),
				'type'        => 'checkbox',
				'description' => __( 'Enabling this will return rates where order amount is greater than what will be compensated.', 'woocommerce-shipping-royalmail' ),
				'default'     => 'no',
			),
			'ignore_max_total_cover'    => array(
				'title'       => __( 'Ignore Maximum Total Cover for Parcelforce', 'woocommerce-shipping-royalmail' ),
				'type'        => 'checkbox',
				'description' => __( 'Enabling this will return Parcelforce rates even when order amount is greater than maximum total cover.', 'woocommerce-shipping-royalmail' ),
				'default'     => 'no',
			),
			'enable_addit_compensation' => array(
				'title'       => __( 'Enable Additional Compensation', 'woocommerce-shipping-royalmail' ),
				'type'        => 'checkbox',
				'description' => __( 'Enabling this will add an additional fee for increasing compensation value up to 250 Poundsterling for certain services.', 'woocommerce-shipping-royalmail' ),
				'default'     => 'no',
			),
			'services'                  => array(
				'type' => 'services',
			),
		);

		$this->form_fields = array(
			'box_packer_library' => array(
				'title'       => __( 'Box Packer Library', 'woocommerce-shipping-royalmail' ),
				'type'        => 'select',
				'default'     => '',
				'class'       => 'box_packer_library',
				'options'     => array(
					'original' => __( 'Speed Packer', 'woocommerce-shipping-royalmail' ),
					'dvdoug'   => __( 'Accurate Packer', 'woocommerce-shipping-royalmail' ),
				),
				'description' => __( 'Speed Packer packs items by volume, Accurate Packer check each dimension allowing more accurate packing but might be slow when you sell items in large quantities.', 'woocommerce-shipping-royalmail' ),
			),
			'debug_mode'         => array(
				'title'       => __( 'Debug Mode', 'woocommerce-shipping-royalmail' ),
				'label'       => __( 'Enable debug mode', 'woocommerce-shipping-royalmail' ),
				'type'        => 'checkbox',
				'default'     => '',
				'desc_tip'    => true,
				'description' => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'woocommerce-shipping-royalmail' ),
			),
		);
	}

	/**
	 * Calculate shipping cost.
	 *
	 * @param mixed $package Package to ship.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		include_once 'class-wc-shipping-royalmail-rates.php';

		$rates     = array();
		$rates_api = new WC_Shipping_Royalmail_Rates( $package, $this->box_packer_library, $this->packing_method, $this->boxes, $this->instance_id, $this->rate_type, $this->logger );
		$quotes    = $rates_api->get_quotes();

		if ( ! is_array( $quotes ) || empty( $quotes ) ) {
			return;
		}

		foreach ( $quotes as $rate_code => $cost ) {

			$rate_id       = $this->id . ':' . $rate_code;
			$rate_name     = $this->services[ $rate_code ];
			$rate_is_taxed = $this->is_taxed[ $rate_code ];
			$rate_cost     = $cost;

			// Name adjustment.
			if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
				$rate_name = $this->custom_services[ $rate_code ]['name'];
			}

			// Cost adjustment %.
			if ( ! empty( $this->custom_services[ $rate_code ]['adjustment_percent'] ) ) {
				$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $rate_code ]['adjustment_percent'] ) / 100 ) );
			}

			// Cost adjustment.
			if ( ! empty( $this->custom_services[ $rate_code ]['adjustment'] ) ) {
				$rate_cost = $rate_cost + floatval( $this->custom_services[ $rate_code ]['adjustment'] );
			}

			// Enabled check.
			if ( isset( $this->custom_services[ $rate_code ] ) && empty( $this->custom_services[ $rate_code ]['enabled'] ) ) {
				continue;
			}

			// Sort.
			if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
				$sort = $this->custom_services[ $rate_code ]['order'];
			} else {
				$sort = 999;
			}

			$rates[ $rate_id ] = array(
				'id'    => $rate_id,
				'label' => $rate_name,
				'cost'  => $rate_cost,
				'sort'  => $sort,
				'taxes' => $rate_is_taxed,
			);
		}

		if ( ! is_array( $rates ) || empty( $rates ) ) {
			return;
		}

		// Add rates.
		if ( 'all' === $this->offer_rates ) {

			uasort( $rates, array( $this, 'sort_rates' ) );

			foreach ( $rates as $key => $rate ) {
				$this->add_rate( $rate );
			}
		} else {

			$cheapest_rate = '';

			foreach ( $rates as $key => $rate ) {
				if ( ! $cheapest_rate || $cheapest_rate['cost'] > $rate['cost'] ) {
					$cheapest_rate = $rate;
				}
			}

			$cheapest_rate['label'] = $this->title;

			$this->add_rate( $cheapest_rate );
		}

		if ( $this->logger instanceof WC_Logger_Interface ) {
			$context = array(
				'DESTINATION'       => $package['destination'],
				'REQUEST'           => $rates_api,
				'INSTANCE SETTINGS' => $this->instance_settings,
				'MATCHED RATES'     => $rates,
				'PRESENTED RATES'   => array_intersect_key( $rates, $this->rates ),
			);

			$hash = md5( wp_json_encode( $package ) . wp_json_encode( $this->instance_settings ) );
			$this->logger->debug(
				sprintf(
					// translators: rates request hash.
					__( 'Rates request: %s', 'woocommerce-shipping-royalmail' ),
					$hash
				),
				$context
			);
		}
	}

	/**
	 * Sort rates.
	 *
	 * @param mixed $a A.
	 * @param mixed $b B.
	 * @return int
	 */
	public function sort_rates( $a, $b ) {
		if ( $a['sort'] === $b['sort'] ) {
			return 0;
		}
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	/**
	 * If the box packer library option is not yet set and there are existing
	 * RoyalMail shipping method instances, we can assume that this is not a
	 * new/fresh installation of the RoyalMail plugin,
	 * so we should default to 'original'
	 *
	 * If the box packer library option is not set and there are no
	 * RoyalMail shipping method instances, then this is likely a new
	 * installation of the RoyalMail plugin,
	 * so we should default to 'dvdoug'
	 *
	 * @return string
	 */
	public function get_default_box_packer_library(): string {
		if ( ( empty( $this->get_option( 'box_packer_library' ) ) && $this->instances_exist() ) ) {
			return 'original';
		} else {
			return 'dvdoug';
		}
	}

	/**
	 * Helper method to get the number of Canada Post method instances.
	 *
	 * @return int The number of Canada Post method instances
	 */
	public function instance_count(): int {
		global $wpdb;

		// phpcs:ignore --- Need to use WPDB::get_var() to count the existing Canada Post in the shipping zone
		return absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'royal_mail'" ) );
	}

	/**
	 * Helper method to check if there are existing Canada Post method instances.
	 *
	 * @return bool
	 */
	public function instances_exist(): bool {
		return $this->instance_count() > 0;
	}
}
