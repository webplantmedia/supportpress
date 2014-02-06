<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* Start WooThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// WooFramework init
require_once ( get_template_directory() . '/functions/admin-init.php' );	

// Define the theme-specific key to be sent to PressTrends.
define( 'WOO_PRESSTRENDS_THEMEKEY', 'jigdzvbf7uopc5sculgxoqv43i9lbzat2' );

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/

$includes = array(
	'includes/theme-custom-post-types.php',		// Custom Post Types
	'includes/theme-options.php', 				// Options panel settings and custom settings
	'includes/theme-ticket-options.php', 		// Code for the custom write panel for tickets
	'includes/theme-functions.php', 			// Custom theme functions
	'includes/theme-actions.php',				// Theme actions & user defined hooks
	'includes/theme-comments.php',	 			// Custom comments/pingback loop
	'includes/theme-js.php',					// Load javascript in wp_head
	'includes/sidebar-init.php',				// Initialize widgetized areas
	'includes/theme-widgets.php',				// Theme widgets
	'includes/theme-users.php',					// Sets up roles + caps + user functions
	'includes/theme-install.php',				// Install default pages and tags etc
	'includes/theme-form-handlers.php',			// Handles forms
	'includes/theme-login.php',					// Handles login/reg
	'includes/theme-rss.php',					// Handles rss feeds
	'includes/theme-emails.php'					// Handles system emails
);				

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'woo_includes', $includes );
				
foreach ( $includes as $i ) {
	locate_template( $i, true );
}

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/










/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>