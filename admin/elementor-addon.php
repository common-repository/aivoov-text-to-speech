<?php

function register_hello_world_widget( $widgets_manager ) {
	require_once( __DIR__ . '/widgets/aivoov-widget.php' ); 
	$widgets_manager->register( new \Elementor_Aivoov_Widget() ); 

}
add_action( 'elementor/widgets/register', 'register_hello_world_widget' );

add_action( 'elementor/editor/before_enqueue_styles', 'editor_styles' );
function editor_styles() {
	wp_enqueue_style( 'aivoov-elementor-admin',  plugin_dir_url(__FILE__).'css/elementor-admin.css', [], '1.0' );
}