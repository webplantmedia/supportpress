<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- HTML Type
- Send email to multiple recipients
- Retrieve password email fix
- Get watchers
- Get recipients
- Get Email Template
- New Ticket Notices
- Updated Ticket Notices
- New Message Notice
- Watched item comment notice

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* HTML type
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_content_type($content_type){
	return 'text/html';
}

add_filter('wp_mail_content_type', 'woo_supportpress_email_content_type');


/*-----------------------------------------------------------------------------------*/
/* Send email to multiple recipients
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_send_mail( $recipients = array(), $subject = '', $message = '' ) {

	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	// Generate Headers
	$header['From'] 		= get_bloginfo('name') . " <noreply@".$sitename.">";
	$header['X-Mailer'] 	= "PHP" . phpversion() . "";
	$header['Content-Type'] = get_option('html_type') . "; charset=\"". get_option('blog_charset') . "\"";

	foreach ( $header as $key => $value ) {
		$headers[$key] = $key . ": " . $value;
	}
	$headers = implode("\n", $headers);
	$headers .= "\n";

	// Main Recipient
	$email = array_pop($recipients);

	// BCC Recipients
	if (sizeof($recipients)>0) :
		$bcc = 'Bcc: ' . implode(', ',$recipients);
		$headers .= "$bcc\n";
	endif;

	// Filter
	$headers = apply_filters('woo_supportpress_send_mail_headers', $headers);

	// Send email
	wp_mail($email, $subject, $message, $headers);

}

/*-----------------------------------------------------------------------------------*/
/* Retrieve password email fix
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_retrieve_password_message($content){
	return htmlspecialchars($content);
}

add_filter('retrieve_password_message', 'woo_supportpress_retrieve_password_message');


/*-----------------------------------------------------------------------------------*/
/* Get watchers
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_get_item_watchers( $item_id ){

	global $wpdb;

	$user = new WP_User(get_current_user_id());

	// Get users watching an item (except the user doing the action whos logged in or those with the feature disabled)
	$watchers = $wpdb->get_col("SELECT DISTINCT user_email
	FROM ".$wpdb->prefix."supportpress_watching_tickets as watch
	LEFT JOIN $wpdb->users ON watch.user_id = $wpdb->users.ID
	WHERE watch.item_id = $item_id;");

	$exclude = $wpdb->get_col("SELECT DISTINCT user_email
	FROM $wpdb->usermeta
	LEFT JOIN $wpdb->users ON $wpdb->usermeta.user_id = $wpdb->users.ID
	WHERE meta_key = 'watched_item_notification'
	AND meta_value = 'no';");

	if (!is_array($exclude)) $exclude = array();
	$exclude[] = $user->user_email;

	if (is_array($watchers)) $watchers = array_diff($watchers, $exclude);

	return $watchers;

}


/*-----------------------------------------------------------------------------------*/
/* Get recipients
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_send_to( $type = 'ticket' ){

	global $wpdb;

	if (get_current_user_id()>0) :
		$user = new WP_User(get_current_user_id());
		$exclude = array( $user->user_email );
	else :
		$exclude = array();
	endif;

	switch ($type) :
		case "ticket" :
			$send_to = $wpdb->get_col("SELECT DISTINCT user_email
			FROM $wpdb->usermeta
			LEFT JOIN $wpdb->users ON $wpdb->usermeta.user_id = $wpdb->users.ID
			WHERE meta_key = 'new_ticket_notification'
			AND meta_value ='yes';");
		break;
		case "message" :
			$send_to = $wpdb->get_col("SELECT DISTINCT user_email
			FROM $wpdb->usermeta
			LEFT JOIN $wpdb->users ON $wpdb->usermeta.user_id = $wpdb->users.ID
			WHERE meta_key = 'new_message_notification'
			AND meta_value ='yes';");
		break;
	endswitch;

	if (!is_array($send_to)) $send_to = array();
	//$send_to[] = get_option('admin_email');

	$send_to = array_diff($send_to, $exclude);

	$send_to = array_unique($send_to);

	return $send_to;
}


/*-----------------------------------------------------------------------------------*/
/* Get Email Template
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_template( $phpmailer ) {

	$template = file_get_contents( TEMPLATEPATH . '/includes/email/index.html');

	$subject = $phpmailer->Subject;
	$subject = str_replace('['.get_bloginfo('name').'] ', '', $subject);

	$template = str_replace('{heading}', $subject, $template);

	if( $phpmailer->Body != strip_tags($phpmailer->Body) ) {
		$template = str_replace('{content}', $phpmailer->Body, $template);
	} else {
		$template = str_replace('{content}', nl2br(wptexturize($phpmailer->Body)), $template);
	}

	$template = str_replace( '{email_url}', get_bloginfo('template_url') . '/includes/email/', $template );

	$phpmailer->Body = $template;

	return $phpmailer;
}

add_action('phpmailer_init', 'woo_supportpress_email_template');

/*-----------------------------------------------------------------------------------*/
/* New Ticket Notices
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_new_ticket( $ticket_id ) {

	$ticket = get_post($ticket_id);
	if (is_user_logged_in()) :
		$user = new WP_User(get_current_user_id());
		$display_name = $user->display_name;
	else :
		$display_name = __('Guest', 'woothemes');
	endif;

	// Send new ticket notification to admin
	$subject = '[' . get_bloginfo('name'). '] ' . __('New Ticket', 'woothemes');
	$content = __("Hi there,\n\nA new ticket has been submitted by &ldquo;%s&rdquo;. To view this ticket click the link below:\n\n%s\n\nRegards,\n%s", 'woothemes');

	$email_content = sprintf(
		nl2br( wptexturize( $content ) )
		, $display_name
		, '<a href="'.get_permalink($ticket->ID).'">'.$ticket->post_title.'</a>'
		, get_bloginfo('name')
	);

	woo_supportpress_send_mail( woo_supportpress_send_to( 'ticket' ), $subject, $email_content );
}

function woo_supportpress_email_owner_of_new_ticket( $ticket_id ) {

	$ticket = get_post($ticket_id);

	// Only send if ticket created on users behalf
	if ($ticket->post_author == get_current_user_id()) return;

	$user = get_user_by('id', $ticket->post_author);

	// Send new ticket notification to admin
	$subject = '[' . get_bloginfo('name'). '] ' . __('New Ticket', 'woothemes');
	$content = __("Hi there,\n\nA new ticket has been created on your behalf. To view this ticket click the link below:\n\n%s\n\nRegards,\n%s", 'woothemes');

	$email_content = sprintf(
		nl2br( wptexturize( $content ) )
		, '<a href="'.get_permalink($ticket->ID).'">'.$ticket->post_title.'</a>'
		, get_bloginfo('name')
	);

	// Generate Headers
	$header['From'] 		= get_bloginfo('name') . " <noreply@".$sitename.">";
	$header['X-Mailer'] 	= "PHP" . phpversion() . "";
	$header['Content-Type'] = get_option('html_type') . "; charset=\"". get_option('blog_charset') . "\"";

	foreach ( $header as $key => $value ) {
		$headers[$key] = $key . ": " . $value;
	}
	$headers = implode("\n", $headers);
	$headers .= "\n";

	// Filter
	$headers = apply_filters('woo_supportpress_send_mail_headers', $headers);

	wp_mail( $user->user_email, $subject, $email_content, $headers );
}

function woo_supportpress_email_assigned_to_new_ticket( $ticket_id ) {

	$ticket = get_post($ticket_id);
	if (is_user_logged_in()) :
		$user = new WP_User(get_current_user_id());
		$display_name = $user->display_name;
	else :
		$display_name = __('Guest', 'woothemes');
	endif;

	// Send ticket notification to assigned user
	$assigned_user = get_user_by('id', get_post_meta( $ticket->ID, '_responsible', true));
	if ($assigned_user && !is_wp_error($assigned_user) && $assigned_user->ID!=get_current_user_id()) :

		$subject = '[' . get_bloginfo('name'). '] ' . __('Assigned to ticket', 'woothemes');
		$content = __("Hi there %s,\n\nIt's your lucky day! A new ticket has been submitted by &ldquo;%s&rdquo; and assigned to you. To view this ticket click the link below:\n\n%s\n\nRegards,\n%s", 'woothemes');

		$email_content = sprintf(
			nl2br( wptexturize( $content ) )
			, $assigned_user->display_name
			, $display_name
			, '<a href="'.get_permalink($ticket->ID).'">'.$ticket->post_title.'</a>'
			, get_bloginfo('name')
		);

		// Generate Headers
		$header['From'] 		= get_bloginfo('name') . " <noreply@".$sitename.">";
		$header['X-Mailer'] 	= "PHP" . phpversion() . "";
		$header['Content-Type'] = get_option('html_type') . "; charset=\"". get_option('blog_charset') . "\"";

		foreach ( $header as $key => $value ) {
			$headers[$key] = $key . ": " . $value;
		}
		$headers = implode("\n", $headers);
		$headers .= "\n";

		// Filter
		$headers = apply_filters('woo_supportpress_send_mail_headers', $headers);

		wp_mail( $assigned_user->user_email, $subject, $email_content, $headers );

	endif;
}

add_action('new_ticket', 'woo_supportpress_email_new_ticket', 1, 1);
add_action('new_ticket', 'woo_supportpress_email_owner_of_new_ticket', 1, 1);
add_action('new_ticket', 'woo_supportpress_email_assigned_to_new_ticket', 1, 1);

/*-----------------------------------------------------------------------------------*/
/* Updated ticket Notice
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_updated_ticket( $ticket_id, $updates = array(), $comment_id = '' ) {

	global $wpdb, $post;

	$ticket = get_post($ticket_id);

	if (is_user_logged_in()) :
		$user = new WP_User(get_current_user_id());
		$display_name = $user->display_name;
	else :
		$display_name = __('Guest', 'woothemes');
	endif;

	$comment = get_comment( $comment_id );
	if ($comment->comment_content && $comment->comment_content!=='[UPDATE]') $comment_content = "\n\n".__('Comment', 'woothemes').":\n\n<quote>" . wptexturize(strip_tags($comment->comment_content)) . "</quote>"; else $comment_content = '';

	$subject = '[' . get_bloginfo('name'). '] ' . __('Ticket Updated', 'woothemes');
	$content = __("Hi there,\n\nTicket #%s has been updated by &ldquo;%s&rdquo;. To view this ticket click the link below:\n\n%s\n\nTicket updates: %s%s\n\nRegards,\n%s", 'woothemes');

	$email_content = sprintf(
		nl2br( wptexturize( $content ) )
		, $ticket->ID
		, $display_name
		, '<a href="'.get_permalink($ticket->ID).'">'.$ticket->post_title.'</a>'
		, implode(', ', $updates)
		, $comment_content
		, get_bloginfo('name')
	);

	// Send notification to users watching the ticket
	$watchers = woo_supportpress_get_item_watchers( $ticket->ID );

	// Email admin if no-one is assigned
	if (!get_post_meta( $ticket->ID, '_responsible', true)) $watchers[] = get_option('admin_email');

	if (sizeof($watchers)>0) woo_supportpress_send_mail( $watchers, $subject, $email_content );
}

add_action('ticket_updated', 'woo_supportpress_email_updated_ticket', 1, 3);

/*-----------------------------------------------------------------------------------*/
/* New Message Notice
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_new_message( $item_id ) {

	$item = get_post($item_id);
	if (is_user_logged_in()) :
		$user = new WP_User(get_current_user_id());
		$display_name = $user->display_name;
	else :
		$display_name = __('Guest', 'woothemes');
	endif;

	// Send new item notification to admin
	$subject = '[' . get_bloginfo('name'). '] ' . __('New Message', 'woothemes');
	$content = __("Hi there,\n\nA new message has been submitted by &ldquo;%s&rdquo;. To view this message click the link below:\n\n%s\n\nRegards,\n%s", 'woothemes');

	$email_content = sprintf(
		nl2br( wptexturize( $content ) )
		, $display_name
		, '<a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a>'
		, get_bloginfo('name')
	);

	woo_supportpress_send_mail( woo_supportpress_send_to( 'message' ), $subject, $email_content );
}

add_action('new_message', 'woo_supportpress_email_new_message', 1, 1);

/*-----------------------------------------------------------------------------------*/
/* Watched item comment notice
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_email_commented_item( $comment_id ) {

	global $wpdb, $post, $ticket_updated;

	if ($ticket_updated) return;

	$comment = get_comment( $comment_id );
	$item = get_post($comment->comment_post_ID);

	if ($item->post_type!=='ticket' && $item->post_type!=='message') return;

	$comment_content = "\n\n".__('Comment', 'woothemes').":\n\n<quote>" . wptexturize(strip_tags($comment->comment_content)) . "</quote>";

	if ($comment->user_id > 0) :
		$user = new WP_User(get_current_user_id());
		$display_name = $user->display_name;
	else :
		$display_name = $comment->comment_author;
	endif;

	if ($item->post_type=='ticket') :
		$content = __("Hi there,\n\nTicket #%s has been commented on by &ldquo;%s&rdquo;. To view this ticket click the link below:\n\n%s%s\n\nRegards,\n%s", 'woothemes');

		$email_content = sprintf(
			nl2br( wptexturize( $content ) )
			, $item->ID
			, $display_name
			, '<a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a>'
			, $comment_content
			, get_bloginfo('name')
		);
	else :
		$content = __("Hi there,\n\nA message you are watching has been commented on by &ldquo;%s&rdquo;. To view this message click the link below:\n\n%s%s\n\nRegards,\n%s", 'woothemes');

		$email_content = sprintf(
			nl2br( wptexturize( $content ) )
			, $display_name
			, '<a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a>'
			, $comment_content
			, get_bloginfo('name')
		);
	endif;

	$subject = '[' . get_bloginfo('name'). '] ' . __('Comment on watched item', 'woothemes');

	// Send notification to users watching the ticket
	$watchers = woo_supportpress_get_item_watchers( $item->ID );

	if (sizeof($watchers)>0) woo_supportpress_send_mail( $watchers, $subject, $email_content );
}

add_action ('comment_post', 'woo_supportpress_email_commented_item', 2);