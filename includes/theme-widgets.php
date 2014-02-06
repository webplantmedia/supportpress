<?php

/*---------------------------------------------------------------------------------*/
/* Loads all the .php files found in /includes/widgets/ directory */
/*---------------------------------------------------------------------------------*/

include( TEMPLATEPATH . '/includes/widgets/widget-woo-twitter.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-woo-mytickets.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-woo-tickettags.php' );
include( TEMPLATEPATH . '/includes/widgets/widget-woo-agents.php' );
include( TEMPLATEPATH . '/includes/widgets/widget.woo-kb-cats.php' );
include( TEMPLATEPATH . '/includes/widgets/widget.woo-kb-tags.php' );

/*---------------------------------------------------------------------------------*/
/* Deregister Default Widgets */
/*---------------------------------------------------------------------------------*/
if (!function_exists('woo_deregister_widgets')) {
	function woo_deregister_widgets(){
	    unregister_widget('WP_Widget_Search');  
	    unregister_widget( 'WP_Widget_Pages' );
		unregister_widget( 'WP_Widget_Calendar' );
		unregister_widget( 'WP_Widget_Archives' );
		unregister_widget( 'WP_Widget_Links' );
		//unregister_widget( 'WP_Widget_Categories' );
		unregister_widget( 'WP_Widget_Recent_Posts' );
		unregister_widget( 'WP_Widget_Tag_Cloud' );       
	}
}
add_action('widgets_init', 'woo_deregister_widgets');  