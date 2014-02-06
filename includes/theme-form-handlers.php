<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Process new ticket form
- Hook into ticket forms
- Process new message form
- Process edit profile form

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Process new ticket form */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_process_new_ticket() {
	
	global $posted, $wpdb;
	
	set_time_limit(0); 
	
	$errors = new WP_Error();
	$posted = array();
	
	$fields = array(
		'title', 'comment', 'ticket_type', 'priority', 'responsible', 'status', 'ticket_owner', 'tags'
	);
	
	foreach ($fields as $field) :
		if (isset($_POST[$field])) $posted[$field] = trim(stripslashes(htmlspecialchars($_POST[$field]))); else $posted[$field] = '';
	endforeach;
	
	/* Validate Requried Fields */
	if (empty($posted['title'])) $errors->add('required-field', __('<strong>Error</strong> &ldquo;Title&rdquo; is a required field.', 'woothemes'));
	if (empty($posted['comment'])) $errors->add('required-field', __('<strong>Error</strong> Please describe the problem.', 'woothemes'));

	if (sizeof($errors->errors)>0) return $errors;
	
	/* Handle attachment */
	
	$attachment = '';

	require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		
	if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			
		$attachment = wp_handle_upload($_FILES['attachment'], array('test_form'=>false), current_time('mysql'));
					
		if ( isset($attachment['error']) ) :
			$errors->add('upload-error', 'Attachment Error: ' . $attachment['error'] );
			
			return $errors;
		endif;
		
	endif;
	
	/* Author */
	if ($posted['ticket_owner']>0) $post_author = $posted['ticket_owner']; else $post_author = get_current_user_id();
	
	/* Create ticket */
	
	$data = array(
		'post_content' => esc_attr($posted['comment']),
		'post_title' => esc_attr($posted['title']),
		'post_status' => 'publish',
		'post_author' => $post_author,
		'post_type' => 'ticket'
	);		
		
	$ticket_id = wp_insert_post($data);		
		
	if ($ticket_id==0 || is_wp_error($ticket_id)) wp_die( __('Error: Unable to create ticket.', 'woothemes') );

	/* Set terms */

	$terms = array();
	if ($posted['priority']) $terms[] = get_term_by( 'id', $posted['priority'], 'ticket_priority')->slug;
	if (sizeof($terms)>0) wp_set_object_terms($ticket_id, $terms, 'ticket_priority');
	
	wp_set_object_terms($ticket_id, array(NEW_STATUS_SLUG), 'ticket_status');
	
	/* Type */
	
	$terms = array();
	if ($posted['ticket_type']) $terms[] = get_term_by( 'id', $posted['ticket_type'], 'ticket_type')->slug;
	if (sizeof($terms)>0) wp_set_object_terms($ticket_id, $terms, 'ticket_type');
	
	/* Responsible */
	
	if ($posted['responsible'] && $posted['responsible']>0) :
		update_post_meta($ticket_id, '_responsible', $posted['responsible']);
	else :
		update_post_meta($ticket_id, '_responsible', '');
	endif;
	
	/* Status */
	
	$terms = array();
	if ($posted['status']) $terms[] = get_term_by( 'id', $posted['status'], 'ticket_status')->slug;
	if (sizeof($terms)>0) wp_set_object_terms($ticket_id, $terms, 'ticket_status');
	
	/* Tags */
	
	if (isset($posted['tags']) && $posted['tags']) :
				
		$tags = explode(',', trim(stripslashes($posted['tags'])));
		$tags = array_map('strtolower', $tags);
		$tags = array_map('trim', $tags);

		if (sizeof($tags)>0) :
			wp_set_object_terms($ticket_id, $tags, 'ticket_tags');
		endif;
		
	endif;
	
	/* Attach file to ticket */
	
	if ($attachment) :
	
		$attachment_data = array(
			'post_mime_type' => $attachment['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($attachment['file'])),
			'post_content' => '',
			'post_status' => 'inherit',
			'post_author' => get_current_user_id()
		);
		$attachment_id = wp_insert_attachment( $attachment_data, $attachment['file'], $ticket_id );
		$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment['file'] );
		wp_update_attachment_metadata( $attachment_id,  $attachment_metadata );
	
	endif;

	do_action('new_ticket', $ticket_id);
	
	/* Successful, return ticket */
	return $ticket_id;
}

/*-----------------------------------------------------------------------------------*/
/* Hook into ticket forms */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_new_ticket_form() {
	if ($_POST) :
		
		if (is_user_logged_in()) :
			$return = woo_supportpress_process_new_ticket();
		endif;
		
		if ( is_wp_error($return) ) :
   			echo '<div class="notice red delete"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			if (!is_user_logged_in() && get_option('woo_moderate_guest_tickets')=='true') :
   				echo '<div class="notice green check"><span><p>'.__('<strong>Thank you</strong> Your issue has been added to our system.', 'woothemes').'</p></span></div>';
   				global $posted;
   				$posted = '';
   			else :
   				wp_redirect(get_permalink($return));
				exit;
   			endif;
   		endif;

	endif;
}
add_action('new_ticket_form', 'woo_supportpress_new_ticket_form');

/*-----------------------------------------------------------------------------------*/
/* Process new message form */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_process_new_message() {
	
	global $posted, $wpdb;
	
	$errors = new WP_Error();
	$posted = array();
	
	$fields = array(
		'message_title', 'message_content'
	);
	
	foreach ($fields as $field) :
		if (isset($_POST[$field])) $posted[$field] = trim(stripslashes($_POST[$field])); else $posted[$field] = '';
	endforeach;
	
	/* Validate Requried Fields */
	if (empty($posted['message_title'])) $errors->add('required-field', __('<strong>Error</strong> &ldquo;Message title&rdquo; is a required field.', 'woothemes'));
	if (empty($posted['message_content'])) $errors->add('required-field', __('<strong>Error</strong> Please enter a message!', 'woothemes'));

	if (sizeof($errors->errors)>0) return $errors;
	
	/* Handle attachment */
	
	$attachment = '';

	require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		
	if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			
		$attachment = wp_handle_upload($_FILES['attachment'], array('test_form'=>false), current_time('mysql'));
					
		if ( isset($attachment['error']) ) :
			$errors->add('upload-error', 'Attachment Error: ' . $attachment['error'] );
			
			return $errors;
		endif;
		
	endif;
	
	/* Create message */
	
	$data = array(
		'post_content' => esc_attr($posted['message_content']),
		'post_title' => esc_attr($posted['message_title']),
		'post_status' => 'publish',
		'post_author' => get_current_user_id(),
		'post_type' => 'message'
	);		
		
	$message_id = wp_insert_post($data);		
		
	if ($message_id==0 || is_wp_error($message_id)) wp_die( __('Error: Unable to create message.', 'woothemes') );
	
	/* Attach file to message */
	
	if ($attachment) :
	
		$attachment_data = array(
			'post_mime_type' => $attachment['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($attachment['file'])),
			'post_content' => '',
			'post_status' => 'inherit',
			'post_author' => get_current_user_id()
		);
		$attachment_id = wp_insert_attachment( $attachment_data, $attachment['file'], $message_id );
		$attachment_metadata = wp_generate_attachment_metadata( $attachment_id, $attachment['file'] );
		wp_update_attachment_metadata( $attachment_id,  $attachment_metadata );
	
	endif;
	
	do_action('new_message', $message_id);
	
	/* Successful, return message */
	return $message_id;
}

function woo_supportpress_new_message_form() {
	if ($_POST) :
		$return = woo_supportpress_process_new_message();
		if ( is_wp_error($return) ) :
   			echo '<p class="error">'.$return->get_error_message().'</p>';
   		else :
   			wp_redirect(get_permalink($return));
			exit;
   		endif;
	endif;
}
add_action('new_message_form', 'woo_supportpress_new_message_form');

/*-----------------------------------------------------------------------------------*/
/* Process edit profile form */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_process_edit_profile_form() {
	
	global $posted, $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/user.php');
	
	check_admin_referer('update_profile_' . get_current_user_id());
	
	$current_user = wp_get_current_user();
	
	if (!empty($_POST['nickname'])) :
		$_POST['display_name'] = sanitize_user($_POST['nickname']);
	else :
		$_POST['display_name'] = sanitize_user($current_user->user_login);
	endif;
	
	$errors = edit_user(get_current_user_id());
	
	if ( is_wp_error( $errors ) ) return $errors;
	
	do_action('personal_options_update', get_current_user_id());
	
    // Custom profile fields
    update_user_meta(get_current_user_id(), 'twitter', $_POST['twitter']);
   
	if (isset($_POST['new_ticket_notification']) && $_POST['new_ticket_notification']) update_user_meta(get_current_user_id(), 'new_ticket_notification', 'yes'); else update_user_meta(get_current_user_id(), 'new_ticket_notification', 'no');
	
	if (isset($_POST['new_message_notification']) && $_POST['new_message_notification']) update_user_meta(get_current_user_id(), 'new_message_notification', 'yes'); else update_user_meta(get_current_user_id(), 'new_message_notification', 'no');
	
	if (isset($_POST['watched_item_notification']) && $_POST['watched_item_notification']) update_user_meta(get_current_user_id(), 'watched_item_notification', 'yes'); else update_user_meta(get_current_user_id(), 'watched_item_notification', 'no');

	/* Successful, return */
	return;
}

function woo_supportpress_edit_profile_form() {
	if ($_POST) :
		$return = woo_supportpress_process_edit_profile_form();
		if ( is_wp_error($return) ) :
   			echo '<div class="notice red delete"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			echo '<div class="notice green check"><span><p>'.__('<strong>Success!</strong> Your profile has been updated', 'woothemes').'</p></span></div>';
   		endif;
	endif;
}
add_action('edit_profile_form', 'woo_supportpress_edit_profile_form');

