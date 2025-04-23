<?php
/**
 *	Template Name: Blog Template
 */	
get_header();

global $post;
setup_postdata( $post );

$page_options = drile_get_page_options();

$extra_class = '';

$page_column_class = drile_page_layout_columns_class($page_options['ts_page_layout']);

$show_breadcrumb = ( !is_home() && !is_front_page() && $page_options['ts_show_breadcrumb'] );
$show_page_title = ( !is_home() && !is_front_page() && $page_options['ts_show_page_title'] );

if( $show_breadcrumb || $show_page_title ){
	$extra_class = 'show_breadcrumb_'.drile_get_theme_options('ts_breadcrumb_layout');
}

drile_breadcrumbs_title($show_breadcrumb, $show_page_title, get_the_title());
	
?>
<div class="page-template blog-template page-container container-post <?php echo esc_attr($extra_class) ?>">
	<!-- Page slider -->
	<?php if( $page_options['ts_page_slider'] && $page_options['ts_page_slider_position'] == 'before_main_content' ): ?>
	<div class="top-slideshow">
		<div class="top-slideshow-wrapper">
			<?php drile_show_page_slider(); ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Left Sidebar -->
	<?php if( $page_column_class['left_sidebar'] ): ?>
		<aside id="left-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['left_sidebar_class']); ?>">
		<?php if( is_active_sidebar($page_options['ts_left_sidebar']) ): ?>
			<?php dynamic_sidebar( $page_options['ts_left_sidebar'] ); ?>
		<?php endif; ?>
		</aside>
	<?php endif; ?>			
	
	<div id="main-content" class="<?php echo esc_attr($page_column_class['main_class']); ?>">	
		<div id="primary" class="site-content">
			
			<article <?php post_class(); ?>>
				<?php the_content(); ?>
			</article>
			
			<?php
				$paged = 1;
				if( is_paged() ){
					$paged = get_query_var('page');
					if( !$paged ){
						$paged = get_query_var('paged');
					}
				}
				
				$posts = new WP_Query( array('post_type'=>'post', 'paged'=>$paged) );
				if( $posts->have_posts() ):
					echo '<div class="list-posts">';
					while( $posts->have_posts() ) : $posts->the_post();
						get_template_part( 'content', get_post_format() ); 
					endwhile;
					echo '</div>';
					
					wp_reset_postdata();
				else:
					echo '<div class="alert alert-error">'.esc_html__('Sorry. There are no posts to display', 'drile').'</div>';
				endif;
				
				drile_pagination($posts);
			?>

		</div>
	</div>
	
	
	<!-- Right Sidebar -->
	<?php if( $page_column_class['right_sidebar'] ): ?>
		<aside id="right-sidebar" class="ts-sidebar <?php echo esc_attr($page_column_class['right_sidebar_class']); ?>">
		<?php if( is_active_sidebar($page_options['ts_right_sidebar']) ): ?>
			<?php dynamic_sidebar( $page_options['ts_right_sidebar'] ); ?>
		<?php endif; ?>
		</aside>
	<?php endif; ?>	
		
</div><!-- #container -->
<?php get_footer(); ?>