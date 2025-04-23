<?php 
function drile_child_register_scripts(){
    $parent_style = 'drile-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array('font-awesome-5', 'drile-reset'), drile_get_theme_version() );
    wp_enqueue_style( 'drile-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style )
    );
}
add_action( 'wp_enqueue_scripts', 'drile_child_register_scripts' );