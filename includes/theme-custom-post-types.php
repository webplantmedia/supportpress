<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Custom post type icons
- Custom Post Types Init
- Admin columns for post types

-----------------------------------------------------------------------------------*/

function woo_supportpress_post_types_icons() {
    ?><style type="text/css" media="screen">
        #menu-posts-ticket .wp-menu-image,
        #menu-posts-knowledgebase .wp-menu-image,
        #menu-posts-message .wp-menu-image {
            background-image: url(<?php echo get_template_directory_uri(); ?>/images/menu_icons.png) !important;
            background-repeat: no-repeat !important;
        }

        #menu-posts-ticket .wp-menu-image,
        #menu-posts-knowledgebase .wp-menu-image,
        #menu-posts-message .wp-menu-image { background-size: auto !important; }

        #menu-posts-ticket .wp-menu-image { background-position: -2px -24px !important; }
		#menu-posts-knowledgebase .wp-menu-image { background-position: -33px -24px !important; }
		#menu-posts-message .wp-menu-image { background-position: -64px -24px !important; }

		#menu-posts-ticket:hover .wp-menu-image, #menu-posts-ticket.wp-has-current-submenu .wp-menu-image { background-position: -2px 0 !important; }
		#menu-posts-knowledgebase:hover .wp-menu-image, #menu-posts-knowledgebase.wp-has-current-submenu .wp-menu-image { background-position: -33px 0 !important; }
		#menu-posts-message:hover .wp-menu-image, #menu-posts-message.wp-has-current-submenu .wp-menu-image { background-position: -64px 0 !important; }
    </style><?php
}
add_action( 'admin_head', 'woo_supportpress_post_types_icons' );

/*-----------------------------------------------------------------------------------*/
/* WooThemes supportpress Custom Post Types Init */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_post_types_init')) {
	function woo_supportpress_post_types_init() {

		add_option('tickets_slug', 'tickets');

	    register_post_type( 'ticket',
	        array(
	        	'label' => __( 'Ticket', 'woothemes' ),
	            'labels' => array(
					'name' => __( 'Tickets', 'woothemes' ),
					'singular_name' 		=> __( 'Ticket', 'woothemes' ),
					'menu_name'				=> _x( 'Tickets', 'Admin menu name', 'woothemes' ),
					'add_new' 				=> __( 'Add New', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Ticket', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Ticket', 'woothemes' ),
					'new_item' 				=> __( 'New Ticket', 'woothemes' ),
					'view' 					=> __( 'View Tickets', 'woothemes' ),
					'view_item' 			=> __( 'View Ticket', 'woothemes' ),
					'search_items' 			=> __( 'Search Tickets', 'woothemes' ),
					'not_found' 			=> __( 'No tickets found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No tickets found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Tickets', 'woothemes' ),
	            ),
	            'description' => __( 'This is where you can create new tickets for your site. Tickets can also be created from the front-end.', 'woothemes' ),
	            'public' => true,
	            'show_ui' => true,
	            'capability_type' => 'post',
	            'publicly_queryable' => true,
	            'exclude_from_search' => false,
	            'hierarchical' => false,
	            'rewrite' => array( 'slug' => get_option('tickets_slug'), 'with_front' => false ),
	            'query_var' => true,
	            'has_archive' => 'tickets',
	            'supports' => array( 'title', 'editor', /*'custom-fields', 'author',*/ 'comments' ),
	        )
	    );

	    register_post_type( 'knowledgebase',
	        array(
	        	'label' => __( 'KB article', 'woothemes' ),
	            'labels' => array(
					'name' 					=> __( 'KB articles', 'woothemes' ),
					'singular_name' 		=> __( 'Knowledgebase article', 'woothemes' ),
					'menu_name'				=> _x( 'KB articles', 'Admin menu name', 'woothemes' ),
					'add_new' 				=> __( 'Add New', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Knowledgebase article', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Knowledgebase article', 'woothemes' ),
					'new_item' 				=> __( 'New Knowledgebase article', 'woothemes' ),
					'view' 					=> __( 'View Knowledgebase articles', 'woothemes' ),
					'view_item' 			=> __( 'View Knowledgebase article', 'woothemes' ),
					'search_items' 			=> __( 'Search Knowledgebase articles', 'woothemes' ),
					'not_found' 			=> __( 'No Knowledgebase articles found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Knowledgebase articles found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Knowledgebase article', 'woothemes' ),
	            ),
	            'description' => __( 'This is where you can create new knowledgebase articles for your site.', 'woothemes' ),
	            'public' => true,
	            'show_ui' => true,
	            'capability_type' => 'post',
	            'publicly_queryable' => true,
	            'exclude_from_search' => false,
	            'hierarchical' => false,
	            'rewrite' => array( 'slug' => 'knowledgebase', 'with_front' => false ),
	            'query_var' => true,
	            'has_archive' => 'knowledgebase',
	            'supports' => array( 'title', 'editor', 'author'/*, 'comments'*/ ),
	        )
	    );

	    register_post_type( 'message',
	        array(
	            'labels' => array(
					'name' 					=> __( 'Messages', 'woothemes' ),
					'singular_name' 		=> __( 'Message', 'woothemes' ),
					'menu_name'				=> _x( 'Messages', 'Admin menu name', 'woothemes' ),
					'add_new' 				=> __( 'Add New', 'woothemes' ),
					'add_new_item' 			=> __( 'Add New Message', 'woothemes' ),
					'edit' 					=> __( 'Edit', 'woothemes' ),
					'edit_item' 			=> __( 'Edit Message', 'woothemes' ),
					'new_item' 				=> __( 'New Message', 'woothemes' ),
					'view' 					=> __( 'View Messages', 'woothemes' ),
					'view_item' 			=> __( 'View Message', 'woothemes' ),
					'search_items' 			=> __( 'Search Messages', 'woothemes' ),
					'not_found' 			=> __( 'No Messages found', 'woothemes' ),
					'not_found_in_trash' 	=> __( 'No Messages found in trash', 'woothemes' ),
					'parent' 				=> __( 'Parent Message', 'woothemes' ),
	            ),
	            'description' => __( 'Messages allow discussions about the project.', 'woothemes' ),
	            'public' => true,
	            'show_ui' => true,
	            'capability_type' => 'post',
	            'publicly_queryable' => true,
	            'exclude_from_search' => false,
	            'hierarchical' => false,
	            'rewrite' => array( 'slug' => __('messages', 'woothemes'), 'with_front' => false ),
	            'query_var' => true,
	            'has_archive' => 'messages',
	            'supports' => array( 'title', 'editor', 'author', 'comments' ),
	        )
	    );

	    register_taxonomy( 'ticket_status',
	        array('ticket'),
	        array(
	            'hierarchical' => true,
	            'labels' => array(
					'name' => __( 'Ticket Statuses', 'woothemes'),
					'singular_name' => __( 'Ticket Status', 'woothemes'),
					'search_items' =>  __( 'Search Ticket Statuses', 'woothemes'),
					'all_items' => __( 'All Ticket Statuses', 'woothemes'),
					'parent_item' => __( 'Parent Ticket Status', 'woothemes'),
					'parent_item_colon' => __( 'Parent Ticket Status:', 'woothemes'),
					'edit_item' => __( 'Edit Ticket Status', 'woothemes'),
					'update_item' => __( 'Update Ticket Status', 'woothemes'),
					'add_new_item' => __( 'Add New Ticket Status', 'woothemes'),
					'new_item_name' => __( 'New Ticket Status', 'woothemes')
	            ),
	            'public' => true,
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'ticket-status', 'with_front' => false ),
	        )
	    );

	    register_taxonomy( 'ticket_priority',
	        array('ticket'),
	        array(
	            'hierarchical' => true,
	            'labels' => array(
	                    'name' => __( 'Ticket Priorities', 'woothemes'),
	                    'singular_name' => __( 'Ticket Priority', 'woothemes'),
	                    'search_items' =>  __( 'Search Ticket Priorities', 'woothemes'),
	                    'all_items' => __( 'All Ticket Priorities', 'woothemes'),
	                    'parent_item' => __( 'Parent Ticket Priority', 'woothemes'),
	                    'parent_item_colon' => __( 'Parent Ticket Priority:', 'woothemes'),
	                    'edit_item' => __( 'Edit Ticket Priority', 'woothemes'),
	                    'update_item' => __( 'Update Ticket Priority', 'woothemes'),
	                    'add_new_item' => __( 'Add New Ticket Priority', 'woothemes'),
	                    'new_item_name' => __( 'New Ticket Priority', 'woothemes')
	            ),
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'ticket-priority', 'with_front' => false ),
	        )
	    );

	    register_taxonomy( 'ticket_type',
	        array('ticket'),
	        array(
	            'hierarchical' => true,
	            'labels' => array(
	                    'name' => __( 'Ticket Types', 'woothemes'),
	                    'singular_name' => __( 'Ticket Type', 'woothemes'),
	                    'search_items' =>  __( 'Search Ticket Types', 'woothemes'),
	                    'all_items' => __( 'All Ticket Types', 'woothemes'),
	                    'parent_item' => __( 'Parent Ticket Type', 'woothemes'),
	                    'parent_item_colon' => __( 'Parent Ticket Type:', 'woothemes'),
	                    'edit_item' => __( 'Edit Ticket Type', 'woothemes'),
	                    'update_item' => __( 'Update Ticket Type', 'woothemes'),
	                    'add_new_item' => __( 'Add New Ticket Type', 'woothemes'),
	                    'new_item_name' => __( 'New Ticket Type', 'woothemes')
	            ),
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'ticket-type', 'with_front' => false ),
	        )
	    );

	    register_taxonomy( 'ticket_tags',
	        array('ticket'),
	        array(
	            'hierarchical' => false,
	            'labels' => array(
	                    'name' => __( 'Ticket Tags', 'woothemes'),
	                    'singular_name' => __( 'Ticket Tags', 'woothemes'),
	                    'search_items' =>  __( 'Search Ticket Tags', 'woothemes'),
	                    'all_items' => __( 'All Ticket Tags', 'woothemes'),
	                    'parent_item' => __( 'Parent Ticket Tag', 'woothemes'),
	                    'parent_item_colon' => __( 'Parent Ticket Tag:', 'woothemes'),
	                    'edit_item' => __( 'Edit Ticket Tag', 'woothemes'),
	                    'update_item' => __( 'Update Ticket Tag', 'woothemes'),
	                    'add_new_item' => __( 'Add New Ticket Tag', 'woothemes'),
	                    'new_item_name' => __( 'New Ticket Tag', 'woothemes')
	            ),
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'ticket-tag' ),
	        )
	    );

	    register_taxonomy( 'knowledgebase_category',
	        array('knowledgebase'),
	        array(
	            'hierarchical' => true,
	            'labels' => array(
	                    'name' => __( 'KB Category', 'woothemes'),
	                    'singular_name' => __( 'KB Categories', 'woothemes'),
	                    'search_items' =>  __( 'Search KB Categories', 'woothemes'),
	                    'all_items' => __( 'All KB Categories', 'woothemes'),
	                    'parent_item' => __( 'Parent KB Category', 'woothemes'),
	                    'parent_item_colon' => __( 'Parent KB Category:', 'woothemes'),
	                    'edit_item' => __( 'Edit KB Category', 'woothemes'),
	                    'update_item' => __( 'Update KB Category', 'woothemes'),
	                    'add_new_item' => __( 'Add New KB Category', 'woothemes'),
	                    'new_item_name' => __( 'New KB Category', 'woothemes')
	            ),
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'knowledgebase-category' ),
	        )
	    );

	    register_taxonomy( 'knowledgebase_tags',
	        array('knowledgebase'),
	        array(
	            'hierarchical' => false,
	            'labels' => array(
	                    'name' => __( 'KB Tag', 'woothemes'),
	                    'singular_name' => __( 'KB Tags', 'woothemes'),
	                    'search_items' =>  __( 'Search KB Tags', 'woothemes'),
	                    'all_items' => __( 'All KB Tags', 'woothemes'),
	                    'parent_item' => __( 'Parent KB Tag', 'woothemes'),
	                    'parent_item_colon' => __( 'Parent KB Tag:', 'woothemes'),
	                    'edit_item' => __( 'Edit KB Tag', 'woothemes'),
	                    'update_item' => __( 'Update KB Tag', 'woothemes'),
	                    'add_new_item' => __( 'Add New KB Tag', 'woothemes'),
	                    'new_item_name' => __( 'New KB Tag', 'woothemes')
	            ),
	            'show_ui' => true,
	            'query_var' => true,
	            'rewrite' => array( 'slug' => 'knowledgebase-tags' ),
	        )
	    );
	}
}

add_action( 'init', 'woo_supportpress_post_types_init', 0 );


/*-----------------------------------------------------------------------------------*/
/* Admin columns for post types */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_ticket_columns($old_columns){

	$columns = array();

	$columns["cb"] = "<input type=\"checkbox\" />";
	$columns["title"] = __("Ticket Title", 'woothemes');
	$columns["id"] = __("Ticket ID", 'woothemes');
	$columns["status"] = __("Status", 'woothemes');
	$columns["priority"] = __("Priority", 'woothemes');
	$columns["type"] = __("Type", 'woothemes');
	$columns["author"] = __("Submitted by", 'woothemes');
	$columns["assigned"] = __("Assigned to", 'woothemes');
	$columns["comments"] = $old_columns["comments"];
	$columns["date"] = __("Date", 'woothemes');

	return $columns;
}
add_filter('manage_edit-ticket_columns', 'woo_supportpress_ticket_columns');

function woo_supportpress_ticket_custom_columns($column) {
	global $post;

	$ticket_details = woo_supportpress_get_ticket_details( $post->ID );

	switch ($column) {
		case "status" :
			echo get_the_term_list($post->ID, 'ticket_status', '', ', ','');
		break;
		case "priority" :
			echo get_the_term_list($post->ID, 'ticket_priority', '', ', ','');
		break;
		case "type" :
			echo get_the_term_list($post->ID, 'ticket_type', '', ', ','');
		break;
		case "assigned" :

			if ($ticket_details['assigned_to']->ID>0) :
				$link = get_author_posts_url( $ticket_details['assigned_to']->ID );
			else :
				$link = add_query_arg('assigned_to', '0', get_post_type_archive_link('ticket'));
			endif;

			echo '<a href="'.$link.'">'.$ticket_details['assigned_to']->display_name.'</a>';

		break;
		case "id" :
			echo '#'.$post->ID;
		break;
	}
}
add_action('manage_ticket_posts_custom_column', 'woo_supportpress_ticket_custom_columns', 2);