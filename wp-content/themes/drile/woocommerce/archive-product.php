<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$theme_options = drile_get_theme_options();

$filter_style = $theme_options['ts_filter_widget_area_style'];
$show_filter_widget_area = drile_is_active_filter_area() && !in_array($filter_style, array('sidebar', 'bottom'));

if( $theme_options['ts_filter_widget_area'] && $filter_style == 'sidebar' ){
	$theme_options['ts_prod_cat_layout'] = '0-1-0';
}

$extra_class = '';
$page_column_class = drile_page_layout_columns_class($theme_options['ts_prod_cat_layout']);

$show_breadcrumb = get_post_meta(wc_get_page_id( 'shop' ), 'ts_show_breadcrumb', true);
$show_page_title = apply_filters( 'woocommerce_show_page_title', true ) && get_post_meta(wc_get_page_id( 'shop' ), 'ts_show_page_title', true);
if( $show_breadcrumb || $show_page_title ){
	$extra_class = 'show_breadcrumb_'.$theme_options['ts_breadcrumb_layout'];
}
drile_shop_top_product_categories();

drile_breadcrumbs_title($show_breadcrumb, $show_page_title, woocommerce_page_title(false));
?>
<div class="page-container <?php echo esc_attr($extra_class) ?>">

	<!-- Left Sidebar -->
	<?php if( $page_column_class['left_sidebar'] ): ?>
		<aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>">
		<?php 
		if( is_active_sidebar($theme_options['ts_prod_cat_left_sidebar']) ){
			dynamic_sidebar( $theme_options['ts_prod_cat_left_sidebar'] );
		}
		?>
		</aside>
	<?php endif; ?>	
	
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>
	<div id="main-content" class="<?php echo esc_attr($page_column_class['main_class']); ?>">	
		<div id="primary" class="site-content">
		<?php do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( woocommerce_product_loop() ) : ?>
		
			<?php 
			if( class_exists('WC_Widget_Layered_Nav_Filters') ){
				echo '<div class="ts-active-filters">';
				the_widget('WC_Widget_Layered_Nav_Filters', array('title' => esc_html__('Active filters:', 'drile')));
				echo '</div>';
			}
			?>
		
			<div class="before-loop-wrapper"><?php do_action( 'woocommerce_before_shop_loop' ); ?></div>
			
			<!-- Filter Widget Area -->
			<?php if( $show_filter_widget_area ){ ?>
				<aside class="ts-sidebar filter-widget-area style-<?php echo esc_attr($filter_style); ?>" style="display: none">
					<?php dynamic_sidebar( 'filter-widget-area' ); ?>
				</aside>
				<?php 
			}

			global $woocommerce_loop;
			if( absint($theme_options['ts_prod_cat_columns']) > 0 ){
				$woocommerce_loop['columns'] = absint($theme_options['ts_prod_cat_columns']);
			}
			?>
			<div class="woocommerce main-products columns-<?php echo esc_attr($woocommerce_loop['columns']); ?>">
			<?php
			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ){
					the_post();

					do_action( 'woocommerce_shop_loop' );
				
					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();
			?>
			</div>
			
			<div class="after-loop-wrapper">
			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>
			</div>
			
		<?php else: ?>

			<?php do_action( 'woocommerce_no_products_found' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
		</div>
	</div>
	<!-- Right Sidebar -->
	<?php if( $page_column_class['right_sidebar'] ): ?>
		<aside id="right-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['right_sidebar_class']); ?>">
		<?php 
		if( is_active_sidebar($theme_options['ts_prod_cat_right_sidebar']) ){
			dynamic_sidebar( $theme_options['ts_prod_cat_right_sidebar'] );
		}
		?>
		</aside>
	<?php endif; ?>	
	
</div>
<?php get_footer(); ?>