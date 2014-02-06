<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Hook in on activation
- Install
- Install pages
- Install default taxonomies
- Install tables

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Hook in on activation */
/*-----------------------------------------------------------------------------------*/

global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action('init', 'woo_supportpress_install', 1);

/*-----------------------------------------------------------------------------------*/
/* Install */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_install() {
	
	global $wp_rewrite;
		
	woo_supportpress_install_pages();
	woo_supportpress_install_default_taxonomies();
	woo_supportpress_install_tables();
	
	/* Reg option */
	update_option('users_can_register', 1);
	
	/* Default notification settings */
	$admin_user = get_user_by_email( get_option('admin_email') );
	if ($admin_user->ID>0) :
		add_user_meta($admin_id, 'new_ticket_notification', 'yes');
		add_user_meta($admin_id, 'new_message_notification', 'yes');
		add_user_meta($admin_id, 'watched_item_notification', 'yes');
	endif;
	
	$wp_rewrite->flush_rules();
}

/*-----------------------------------------------------------------------------------*/
/* Install Pages */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_install_pages() {

	global $wpdb;
    
    $page_id = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'new-ticket';");

    if (!$page_id) {
    
        $my_page = array(
	        'post_status' => 'publish',
	        'post_type' => 'page',
	        'post_author' => 1,
	        'post_name' => 'new-ticket',
	        'post_title' => __('Add a new ticket', 'woothemes'),
	        'post_content' => __('Please be descriptive and provide demonstrative links where possible. You will be notified when a member of our support staff updates your ticket.', 'woothemes')
        );
		$page_id = wp_insert_post($my_page);

        update_post_meta($page_id, '_wp_page_template', 'template-new-ticket.php');
        update_option('woo_supportpress_new_ticket_page_id', $page_id);

    } else {
   		update_post_meta($page_id, '_wp_page_template', 'template-new-ticket.php');
    	update_option('woo_supportpress_new_ticket_page_id', $page_id);
    }
    
    $page_id = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'new-message';");

    if (!$page_id) {
    
        $my_page = array(
	        'post_status' => 'publish',
	        'post_type' => 'page',
	        'post_author' => 1,
	        'post_name' => 'new-message',
	        'post_title' => __('Add a new message', 'woothemes'),
	        'post_content' => __('Start a new discussion by posting your message below.', 'woothemes')
        );
		$page_id = wp_insert_post($my_page);

        update_post_meta($page_id, '_wp_page_template', 'template-new-message.php');
        update_option('woo_supportpress_new_message_page_id', $page_id);

    } else {
   		update_post_meta($page_id, '_wp_page_template', 'template-new-message.php');
    	update_option('woo_supportpress_new_message_page_id', $page_id);
    }
	   
   	$page_id = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'support-staff';");

    if (!$page_id) {
    
        $my_page = array(
	        'post_status' => 'publish',
	        'post_type' => 'page',
	        'post_author' => 1,
	        'post_name' => 'support-staff',
	        'post_title' => __('Support Staff', 'woothemes'),
	        'post_content' => __('Our friendly support staff are listed below.', 'woothemes')
        );
		$page_id = wp_insert_post($my_page);

        update_post_meta($page_id, '_wp_page_template', 'template-staff.php');
        update_option('woo_supportpress_staff_page_id', $page_id);

    } else {
   		update_post_meta($page_id, '_wp_page_template', 'template-staff.php');
    	update_option('woo_supportpress_staff_page_id', $page_id);
    }
    
    $page_id = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'profile';");

    if (!$page_id) {
    
        $my_page = array(
	        'post_status' => 'publish',
	        'post_type' => 'page',
	        'post_author' => 1,
	        'post_name' => 'profile',
	        'post_title' => __('Edit Profile', 'woothemes'),
	        'post_content' => __('Edit your information below; this controls your notification settings as well as the information shown on your profile.', 'woothemes')
        );
		$page_id = wp_insert_post($my_page);

        update_post_meta($page_id, '_wp_page_template', 'template-edit-profile.php');
        update_option('woo_supportpress_profile_page_id', $page_id);

    } else {
   		update_post_meta($page_id, '_wp_page_template', 'template-edit-profile.php');
    	update_option('woo_supportpress_profile_page_id', $page_id);
    }
    
    $page_id = $wpdb->get_var("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = 'blog';");

    if (!$page_id) {
    
        $my_page = array(
	        'post_status' => 'publish',
	        'post_type' => 'page',
	        'post_author' => 1,
	        'post_name' => 'blog',
	        'post_title' => __('Blog', 'woothemes')
        );
		$page_id = wp_insert_post($my_page);

        update_post_meta($page_id, '_wp_page_template', 'template-blog.php');
        update_option('woo_supportpress_blog_page_id', $page_id);

    } else {
   		update_post_meta($page_id, '_wp_page_template', 'template-blog.php');
    	update_option('woo_supportpress_blog_page_id', $page_id);
    }

}

/*-----------------------------------------------------------------------------------*/
/* Install default taxonomies */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_install_default_taxonomies() {

	$terms = array(
		'low', 'medium', 'high', 'urgent'
	);
	$loop = 1;
	if ($terms) foreach($terms as $term) {
		if (!get_term_by( 'slug', sanitize_title($term), 'ticket_priority')) {
			wp_insert_term($term, 'ticket_priority', array( 'description' => $loop ));
			$loop++;
		}
	}
	
	$terms = array(
		'new', 'open', 'pending', 'resolved'
	);
	$loop = 1;
	if ($terms) foreach($terms as $term) {
		if (!get_term_by( 'slug', sanitize_title($term), 'ticket_status')) {
			$ins_id = wp_insert_term($term, 'ticket_status', array( 'description' => $loop ));
			$loop++;
		}
	}

	$terms = array(
		'question', 'incident', 'problem', 'task'
	);
	if ($terms) foreach($terms as $term) {
		if (!get_term_by( 'slug', sanitize_title($term), 'ticket_type')) {
			$ins_id = wp_insert_term($term, 'ticket_type');
		}
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Install Tables */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_install_tables() {
	
	global $wpdb;
	
	$collate = '';
    if($wpdb->supports_collation()) {
	    if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
	    if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
    }
	
	$wpdb->query("CREATE TABLE IF NOT EXISTS ". $wpdb->prefix . "supportpress_watching_tickets (
		`id` int(9) NOT NULL AUTO_INCREMENT,
		`user_id` int(9) NOT NULL,
		`item_id` int(9) NOT NULL,
		PRIMARY KEY id (`id`)) $collate;");
}