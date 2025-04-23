<?php $theme_options = drile_get_theme_options(); ?>
<div class="clear"></div>
</div><!-- #main .wrapper -->
<div class="clear"></div>
	<?php if( !is_page_template('page-templates/blank-page-template.php') ): ?>
	<footer id="colophon">
		<div class="footer-container">
			<?php if( $theme_options['ts_first_footer_area'] ): ?>
			<div class="first-footer-area footer-area">
				<div class="container">
					<?php drile_get_footer_content( $theme_options['ts_first_footer_area'] ); ?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if( $theme_options['ts_second_footer_area'] ): ?>
			<div class="end-footer footer-area">
				<div class="container">
					<?php drile_get_footer_content( $theme_options['ts_second_footer_area'] ); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</footer>
	<?php endif; ?>
</div><!-- #page -->

<?php 
if( ( !wp_is_mobile() && $theme_options['ts_back_to_top_button'] ) || ( wp_is_mobile() && $theme_options['ts_back_to_top_button_on_mobile'] ) ): 
?>
<div id="to-top" class="scroll-button">
	<a class="scroll-button" href="javascript:void(0)" title="<?php esc_attr_e('Back to Top', 'drile'); ?>"><?php esc_html_e('Back to Top', 'drile'); ?></a>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>