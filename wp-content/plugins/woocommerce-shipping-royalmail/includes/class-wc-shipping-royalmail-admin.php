<?php
/**
 * Admin handler class.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable Generic.Classes.DuplicateClassName.Found --- The other one only for mock

use Automattic\WooCommerce\Admin\BlockTemplates\BlockInterface;

/**
 * Admin handler.
 */
class WC_Shipping_Royalmail_Admin {
// phpcs:enable Generic.Classes.DuplicateClassName.Found

	const META_KEY_PRINTED_PAPERS = '_shipping-royalmail-printed-papers';

	const META_KEY_BOOK = '_shipping-royalmail-book';

	const META_KEY_TUBE = '_shipping-royalmail-tube';

	/**
	 * Label text for printed papers.
	 *
	 * @var string
	 */
	public $label_printed_papers;

	/**
	 * Label text for book.
	 *
	 * @var string
	 */
	public $label_book;

	/**
	 * Label text for tube.
	 *
	 * @var string
	 */
	public $label_tube;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->label_printed_papers = __( 'Printed Papers', 'woocommerce-shipping-royalmail' );
		$this->label_book           = __( 'Book', 'woocommerce-shipping-royalmail' );
		$this->label_tube           = __( 'Tube/Rolls', 'woocommerce-shipping-royalmail' );

		if ( is_admin() ) {
			add_action( 'woocommerce_product_options_dimensions', array( $this, 'product_options' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta' ) );
			add_action( 'woocommerce_variation_options_dimensions', array( $this, 'variation_options' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'process_product_variation_meta' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		add_action( 'woocommerce_block_template_area_product-form_after_add_block_product-fee-and-dimensions-section', array( $this, 'add_block' ) );
		add_action( 'woocommerce_block_template_area_product-form_after_add_block_product-variation-fee-and-dimensions-section', array( $this, 'add_block' ) );
		add_filter( 'woocommerce_rest_pre_insert_product_object', array( $this, 'process_rest_product_meta' ) );
		add_filter( 'woocommerce_rest_pre_insert_product_variation_object', array( $this, 'process_rest_product_meta' ) );
	}

	/**
	 * Enqueue script for edit product page.
	 */
	public function admin_enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		wp_enqueue_script( 'wc-royalmail-edit-product', plugins_url( 'assets/js/edit-product.js', WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ), array( 'jquery' ), WOOCOMMERCE_SHIPPING_ROYALMAIL_VERSION, true );
	}

	/**
	 * Add more option for simple product type.
	 */
	public function product_options() {

		// Printed Papers option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_PRINTED_PAPERS,
				'label'       => $this->label_printed_papers,
				// translators: %s is a label printed papers.
				'description' => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				'desc_tip'    => true,
			)
		);

		// Book option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_BOOK,
				'label'       => $this->label_book,
				// translators: %1$s is a label book.
				'description' => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				'desc_tip'    => true,
			)
		);

		// Tube option.
		woocommerce_wp_checkbox(
			array(
				'id'          => self::META_KEY_TUBE,
				'label'       => $this->label_tube,
				// translators: %1$s is a label tube.
				'description' => sprintf( __( 'Use %s rates to ship package. For rolled and cylinder-shaped parcels, the length of the item plus twice the diameter ( width &amp; height must be equal ) must not exceed 104cm, with the greatest dimension being no more than 90cm.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				'desc_tip'    => true,
			)
		);
	}

	/**
	 * Add more option for variation product type.
	 *
	 * @param array   $loop Loop of the variation.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation Product variation object.
	 */
	public function variation_options( $loop, array $variation_data, WP_Post $variation ) {

		// Printed Papers option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_PRINTED_PAPERS . $loop,
				'name'          => 'variable_' . self::META_KEY_PRINTED_PAPERS . '[' . $loop . ']',
				'label'         => $this->label_printed_papers,
				// translators: %s is a label printed papers.
				'description'   => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_PRINTED_PAPERS, true ),
			)
		);

		// Book option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_BOOK . $loop,
				'name'          => 'variable_' . self::META_KEY_BOOK . '[' . $loop . ']',
				'label'         => $this->label_book,
				// translators: %1$s is a label book.
				'description'   => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_BOOK, true ),
			)
		);

		// Tube option.
		woocommerce_wp_checkbox(
			array(
				'id'            => 'variable_' . self::META_KEY_TUBE . $loop,
				'name'          => 'variable_' . self::META_KEY_TUBE . '[' . $loop . ']',
				'label'         => $this->label_tube,
				// translators: %1$s is a label tube.
				'description'   => sprintf( __( 'Use %s rates to ship package. The length of the item plus twice the diameter must not exceed 104cm, with the greatest dimension being no more than 90cm. And the width and height of this item should be equal.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				'desc_tip'      => true,
				'wrapper_class' => 'form-row form-row-last hide_if_variation_virtual',
				'value'         => get_post_meta( $variation->ID, self::META_KEY_TUBE, true ),
			)
		);
	}

	/**
	 * Add a new block to the template after the product name field.
	 *
	 * @param BlockInterface $product_section The product section block.
	 */
	public function add_block( BlockInterface $product_section ) {
		$parent = $product_section->get_parent();

		if ( ! method_exists( $parent, 'add_section' ) ) {
			return;
		}

		// Basic Details Section.
		$royal_mail = $parent->add_section(
			array(
				'id'         => 'royal-mail-section',
				'order'      => 30,
				'attributes' => array(
					'title'       => __( 'Royal Mail Fields', 'woocommerce' ),
					'description' => __( 'Set up Royal Mail fields.', 'woocommerce' ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_PRINTED_PAPERS,
				'order'      => 5,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_PRINTED_PAPERS,
					'label'          => $this->label_printed_papers,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %s is a label printed papers.
					'description'    => sprintf( __( 'Use %s rates to ship package', 'woocommerce-shipping-royalmail' ), $this->label_printed_papers ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_BOOK,
				'order'      => 10,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_BOOK,
					'label'          => $this->label_book,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %1$s is a label book.
					'description'    => sprintf( __( 'This product is a %1$s. Only %1$ss can be sent to the Republic of Ireland with Printed Papers rates.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_book ) ),
				),
			)
		);

		$royal_mail->add_block(
			array(
				'id'         => self::META_KEY_TUBE,
				'order'      => 15,
				'blockName'  => 'woocommerce/product-checkbox-field',
				'attributes' => array(
					'property'       => 'meta_data.' . self::META_KEY_TUBE,
					'label'          => $this->label_tube,
					'checkedValue'   => 'yes',
					'uncheckedValue' => '',
					// translators: %1$s is a label tube.
					'description'    => sprintf( __( 'Use %s rates to ship package. For rolled and cylinder-shaped parcels, the length of the item plus twice the diameter ( width &amp; height must be equal ) must not exceed 104cm, with the greatest dimension being no more than 90cm.', 'woocommerce-shipping-royalmail' ), strtolower( $this->label_tube ) ),
				),
			)
		);
	}

	/**
	 * Save custom fields
	 *
	 * @param int $post_id Product ID.
	 */
	public function process_product_meta( $post_id ) {
		// No need to verify. It has been verified on `WC_Meta_Box_Product_Data::save()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$checkbox_meta_fields = array(
			self::META_KEY_PRINTED_PAPERS, // Printed papers.
			self::META_KEY_BOOK, // Book.
			self::META_KEY_TUBE, // Tube.
		);

		// Save or delete checkbox meta fields value.
		foreach ( $checkbox_meta_fields as $checkbox_field ) {
			if ( ! empty( $_POST[ $checkbox_field ] ) ) {
				update_post_meta( $post_id, $checkbox_field, 'yes' );
			} else {
				delete_post_meta( $post_id, $checkbox_field );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Save variation custom fields
	 *
	 * @param int $post_id Post ID.
	 * @param int $loop Variation loop.
	 */
	public function process_product_variation_meta( $post_id, $loop ) {
		// No need to verify. It has been verified on `WC_AJAX::save_variations()`.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$checkbox_meta_fields = array(
			self::META_KEY_PRINTED_PAPERS, // Printed papers.
			self::META_KEY_BOOK, // Book.
			self::META_KEY_TUBE, // Tube.
		);

		// Save or delete checkbox meta fields value.
		foreach ( $checkbox_meta_fields as $checkbox_field ) {
			if ( ! empty( $_POST[ 'variable_' . $checkbox_field ][ $loop ] ) ) {
				update_post_meta( $post_id, $checkbox_field, 'yes' );
			} else {
				delete_post_meta( $post_id, $checkbox_field );
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Process product meta through REST API.
	 *
	 * @param WC_Product $product Product object.
	 */
	public function process_rest_product_meta( $product ) {
		if ( 'yes' === $product->get_meta( self::META_KEY_TUBE ) && $product->get_width() !== $product->get_height() ) {
			return new WP_Error(
				'woocommerce_shipping_royalmail_rest_tube_error',
				__( 'Width and height must have the same value when tube is checked.', 'woocommerce-shipping-royalmail' ),
				array(
					'status' => 404,
				)
			);
		}

		return $product;
	}
}
