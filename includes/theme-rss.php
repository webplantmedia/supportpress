<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Disable Feeds

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Disable Feeds
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_disable_feed() {
	
	if (get_query_var('post_type')=='message' || get_query_var('post_type')=='ticket') :
	
		wp_die( __('No feed available.', 'woothemes') );
	
	endif;

}
add_action('do_feed', 'woo_supportpress_disable_feed', 1);
add_action('do_feed_rdf', 'woo_supportpress_disable_feed', 1);
add_action('do_feed_rss', 'woo_supportpress_disable_feed', 1);
add_action('do_feed_rss2', 'woo_supportpress_disable_feed', 1);
add_action('do_feed_atom', 'woo_supportpress_disable_feed', 1);