<?php

// Register widgetized areas

if (!function_exists('the_widgets_init')) {
	function the_widgets_init() {
	    if ( !function_exists('register_sidebars') )
	        return;
	
	    register_sidebar(array('name' => 'Primary','id' => 'primary','description' => "Normal full width Sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h2 class="widgettitle">','after_title' => '</h2>'));  
	    
	    
	    register_sidebar(array('name' => 'Knowledgebase','id' => 'knowledgebase','description' => "Knowledgebase Sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h2 class="widgettitle">','after_title' => '</h2>'));  
	    
	    
	    register_sidebar(array('name' => 'Tickets','id' => 'ticket','description' => "Tickets Sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h2 class="widgettitle">','after_title' => '</h2>'));  
	     

	}
}

add_action( 'init', 'the_widgets_init' );


    
?>