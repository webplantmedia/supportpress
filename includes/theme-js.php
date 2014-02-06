<?php
if (!is_admin()) add_action( 'wp_print_scripts', 'woothemes_add_javascript' );
if (!function_exists('woothemes_add_javascript')) {
	function woothemes_add_javascript( ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'modernizr', get_bloginfo('template_directory').'/includes/js/modernizr.min.js' );
		wp_enqueue_script( 'plugins', get_bloginfo('template_directory').'/includes/js/plugins.js', array( 'jquery' ) );
		
		// Placeholder support
		wp_enqueue_script( 'jquery.defaultvalue', get_bloginfo('template_directory').'/includes/js/jquery.defaultvalue.js', array( 'jquery' ) );
		
		// Load the following in footer for performance reasons
		wp_enqueue_script( 'superfish', get_bloginfo('template_directory').'/includes/js/superfish.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'general', get_bloginfo('template_directory').'/includes/js/general.js', array( 'jquery' ), '1.0', true );
	}
}