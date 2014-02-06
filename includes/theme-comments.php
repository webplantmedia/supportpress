<?php
// Fist full of comments
if (!function_exists("ticket_update_comment")) {
	function ticket_update_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; 
		
		// Check if there are updates
		$updates = get_comment_meta_updates();
		
		$classes = '';
		
		if (isset($comment->user_id) && user_can($comment->user_id, 'manage_tickets')) $classes .= 'is-agent';
		?>
	                 
		<li <?php comment_class($classes); ?> id="comment-<?php comment_ID() ?>">
		
			<article id="li-comment-<?php comment_ID() ?>" class="comment-container">
						
				<header class="comment-head meta">
				
					<cite>
						<?php 
							$url = get_comment_author_url();
							if ($comment->user_id > 0) :
								$user_info = get_userdata($comment->user_id);
								$url = get_author_posts_url( $user_info->ID, $user_info->user_nicename );
							endif;
							
							if ($url) :
								echo sprintf( '<a href="%s" title="%s" class="tooltip">', $url, get_comment_author());
								the_commenter_avatar($args);
								echo '</a>';
							else :
								the_commenter_avatar($args);
							endif;
						?>
						<time><?php echo human_time_diff( strtotime($comment->comment_date), current_time('timestamp') ) . __(' ago', 'woothemes'); ?></time>
					
					</cite> 
				
				</header>
				
				<section class="comment-content comment-entry <?php 
					if (sizeof($updates)>0) echo 'updated '; else 'not-updated '; 
					if (!$comment->comment_content || $comment->comment_content=='[UPDATE]') echo 'no-comment'; 
					?>">
					
					<?php if ($comment->comment_content!='[UPDATE]') comment_text() ?>
					
					<div class="actions">
						<div class="edit"><?php edit_comment_link(__('Edit', 'woothemes'), '', ''); ?></div>
						<div class="reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></div> 
					</div>
					
					<?php if ($comment->comment_approved == '0') { ?>
		                <p class='unapproved'><?php _e('Your comment is awaiting moderation.', 'woothemes'); ?></p>
		            <?php } ?>

					<?php
					if (sizeof($updates)>0) :
						echo '<ul class="updates">';
						echo implode('', $updates);
						echo '</ul>';
					endif;
					?>
					
				</section><!-- /.content -->
			
			</article>
			
	<?php 
	}
}


// Fist full of comments
if (!function_exists("custom_comment")) {
	function custom_comment($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; 
		
		$attached = get_comment_meta(get_comment_ID(), 'added_file', true);
		
		?>
	                 
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
		
			<article id="li-comment-<?php comment_ID() ?>" class="comment-container">
						
				<header class="comment-head meta">
				
					<cite>
						<?php 
							$url = get_comment_author_url();
							if ($comment->user_id > 0) :
								$user_info = get_userdata($comment->user_id);
								$url = get_author_posts_url( $user_info->ID, $user_info->user_nicename );
							endif;
							
							if ($url) :
								echo sprintf( '<a href="%s" title="%s" class="tooltip">', $url, get_comment_author());
								the_commenter_avatar($args);
								echo '</a>';
							else :
								the_commenter_avatar($args);
							endif;
						?>
						<time><?php echo human_time_diff( strtotime($comment->comment_date), current_time('timestamp') ) . __(' ago', 'woothemes'); ?></time>
					
					</cite> 
				
				</header>
				
				<section class="comment-content comment-entry <?php if ($attached) echo 'updated '; else 'not-updated '; ?>">
					
					<?php comment_text() ?>
					
					<div class="actions">
						<div class="edit"><?php edit_comment_link(__('Edit', 'woothemes'), '', ''); ?></div>
						<div class="reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></div> 
					</div>
					
					<?php if ($comment->comment_approved == '0') { ?>
		                <p class='unapproved'><?php _e('Your comment is awaiting moderation.', 'woothemes'); ?></p>
		            <?php } ?>
		            
		            <?php
					if ($attached) :
						echo '<ul class="updates">';
						echo '<li>'.__('<mark>File</mark> was attached ', 'woothemes').'('.$attached.')</li>';
						echo '</ul>';
					endif;
					?>
					
				</section><!-- /.content -->
			
			</article>
			
	<?php 
	}
}


/* This function handles attachments */
add_action ('comment_post', 'woo_supportpress_message_replies', 1);
if (!function_exists("woo_supportpress_message_replies")) {
	function woo_supportpress_message_replies($comment_id) {
		
		global $post;
		
		if ($post->post_type=="message") :
		
			/* Handle any new attachments */
	
			if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
					
				$comment_attachment = wp_handle_upload($_FILES['attachment'], array('test_form'=>false), current_time('mysql'));
							
				if ( isset($comment_attachment['error']) ) :
					// Ohhh Bugger
					wp_delete_comment( $comment_id );
					wp_die( 'Attachment Error: ' . $comment_attachment['error'] );
					exit;
				endif;
		
				$comment_attachment_data = array(
					'post_mime_type' => $comment_attachment['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($comment_attachment['file'])),
					'post_content' => '',
					'post_status' => 'inherit',
					'post_author' => get_current_user_id()
				);
				$comment_attachment_id = wp_insert_attachment( $comment_attachment_data, $comment_attachment['file'], $post->ID );
				$comment_attachment_metadata = wp_generate_attachment_metadata( $comment_attachment_id, $comment_attachment['file'] );
				wp_update_attachment_metadata( $comment_attachment_id,  $comment_attachment_metadata );
						
			endif;
		
		endif;
	}
}


/* Gets ticket updates */
function get_comment_meta_updates() {

	$keys = array(
		'priority' => __('Priority', 'woothemes'), 
		'type' => __('Type', 'woothemes'), 
		'status' => __('Status', 'woothemes')
		);
	
	$updates = array();
	foreach ($keys as $key => $value) :
		
		$old = get_comment_meta(get_comment_ID(), 'old_'.$key, true);
		$new = get_comment_meta(get_comment_ID(), 'new_'.$key, true);
		if ($old != $new) $updates[] = '<li><mark>'.$value.'</mark> '.__('changed from','woothemes').' "'.$old.'" '.__('to','woothemes').' "'.$new.'"</li>';
		
	endforeach;
	
	/* Get responsible user */
	$old = get_comment_meta(get_comment_ID(), 'old_responsible', true);
	$new = get_comment_meta(get_comment_ID(), 'new_responsible', true);
	if (!$old) $old = __('Anybody', 'woothemes'); else $old = get_user_by('id', $old)->display_name;
	if (!$new) $new = __('Anybody', 'woothemes'); else $new = get_user_by('id', $new)->display_name;
	
	if ($old && $new && $old!=$new) $updates[] = '<li><mark>'.__('Assigned user','woothemes').'</mark> '.__('changed from','woothemes').' "'.$old.'" '.__('to','woothemes').' "'.$new.'"</li>';
	
	/* Get tags */
	if (get_comment_meta(get_comment_ID(), 'added_tags', true)=='yes') $updates[] = '<li><mark>'.__('Tags','woothemes').'</mark> '.__('were updated.','woothemes').'</li>';
	
	/* Get File */
	$file = get_comment_meta(get_comment_ID(), 'added_file', true);
	if ($file) $updates[] = '<li><mark>'.__('File','woothemes').'</mark> '.__('was attached','woothemes').' ('.$file.')</li>';
	
	return $updates;
}


/* This function checks to see if there are any ticket updates in the comment */
function woo_supportpress_was_ticket_updated() {
	
	global $post;
	
	if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) return true; /* Updated if file attached */
	
	$responsible 		= isset($_POST['responsible']) ? trim(stripslashes($_POST['responsible'])) : 0;
	$priority			= isset($_POST['priority']) ? trim(stripslashes($_POST['priority'])) : 0;
	$type				= isset($_POST['type']) ? trim(stripslashes($_POST['type'])) : 0;
	$status				= isset($_POST['status']) ? trim(stripslashes($_POST['status'])) : 0;
	$tags				= isset($_POST['tags']) ? trim(stripslashes($_POST['tags'])) : 0;
	
	if ($tags) return true; /* Return true if adding tags */
	
	$old_responsible = get_post_meta($post->ID, '_responsible', true);
	if ($responsible!=$old_responsible) return true;
	
	if (!$priority && !$status && !$type && !$tags) return false; /* Return false if no values sent */
	
	$old_priority 		= current(wp_get_object_terms( $post->ID, 'ticket_priority' ))->term_id;
	$old_status			= current(wp_get_object_terms( $post->ID, 'ticket_status' ))->term_id;
	$old_type			= current(wp_get_object_terms( $post->ID, 'ticket_type' ))->term_id;
	if ($priority!=$old_priority) return true;
	if ($status!=$old_status) return true;
	if ($type!=$old_type) return true;
	
	return false;
}


// This function hooks in and does a ticket update when no 'comment' is set
add_action('pre_comment_on_post', 'woo_supportpress_update_ticket_bypass_comment');
function woo_supportpress_update_ticket_bypass_comment( $comment_post_ID ) {
	global $post, $wpdb;
	
	$_POST['comment'] = wp_kses_data( $_POST['comment'] );
	
	$comment_content = ( isset($_POST['comment']) ) ? trim($_POST['comment']) : null;
	
	if ($post->post_type=="ticket" && is_user_logged_in() && !$comment_content && woo_supportpress_was_ticket_updated()) :
	
		$user = wp_get_current_user();

		$comment_author 		= $wpdb->escape($user->display_name);
		$comment_author_email 	= $wpdb->escape($user->user_email);
		$comment_author_url 	= $wpdb->escape($user->user_url);
		$comment_content 		= '[UPDATE]';
		$comment_type 			= '';
		$comment_parent 		= isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
		$user_id				= (int) $wpdb->escape($user->ID);
		
		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_id');
		
		$commentdata['comment_author_IP'] 	= preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
		$commentdata['comment_agent']     	= substr($_SERVER['HTTP_USER_AGENT'], 0, 254);
		$commentdata['comment_date']     	= current_time('mysql');
		$commentdata['comment_date_gmt'] 	= current_time('mysql', 1);
		$commentdata['comment_approved'] 	= 1;
		
		$comment_ID = wp_insert_comment( $commentdata );
		
		do_action('comment_post', $comment_ID, $commentdata['comment_approved']);
		
		$post = &get_post($commentdata['comment_post_ID']); // Don't notify if it's your own comment
		
		/*if ( get_option('comments_notify') && $commentdata['comment_approved'] && ( ! isset( $commentdata['user_id'] ) || $post->post_author != $commentdata['user_id'] ) )
			wp_notify_postauthor($comment_ID, isset( $commentdata['comment_type'] ) ? $commentdata['comment_type'] : '' );*/
		
		$comment = get_comment($comment_ID);
		
		$location = empty($_POST['redirect_to']) ? get_comment_link($comment_ID) : $_POST['redirect_to'] . '#comment-' . $comment_ID;
		$location = apply_filters('comment_post_redirect', $location, $comment);
		
		wp_redirect($location);
		exit;

	endif;
}


/* This function adds update meta data to comments */
add_action ('comment_post', 'woo_supportpress_update_ticket_meta', 1);

function woo_supportpress_update_ticket_meta($comment_id) {
	global $post, $comment_attachment, $ticket_updated;
	if ($post->post_type=="ticket") :
		
		$updated = false;
		$update_message = array();
			
		// Process fields
		if (isset($_POST['responsible'])) :
			
			$responsible = trim(stripslashes($_POST['responsible']));
			
			// Remember previous values
			$old_value = get_post_meta($post->ID, '_responsible', true);
			$new_value = $responsible;
			
			if ($old_value!=$new_value) :

				// Add meta values
				add_comment_meta($comment_id, 'old_responsible', $old_value, true);
				add_comment_meta($comment_id, 'new_responsible', $new_value, true);
				
				// Update value of ticket
				update_post_meta($post->ID, '_responsible', $responsible);
				
				// Make new user watch ticket
				$assigned_user = get_user_by('id', $responsible);
				if ($assigned_user && !is_wp_error($assigned_user)) :
					woo_supportpress_watch_item( $post->ID, $assigned_user->ID );
				endif;
				
				$updated = true;

				if (!$old_value) : $old_value = __('Anybody', 'woothemes');
				else : $old_value = get_the_author_meta('display_name', $old_value); endif;
				if (!$new_value) : $new_value = __('Anybody', 'woothemes');
				else : $new_value = get_the_author_meta('display_name', $new_value); endif;
				$update_message[] = sprintf( __('Owner: <strong>%s</strong> &rarr; <strong>%s</strong>', 'woothemes'), $old_value, $new_value );
			
			endif;
			
		endif;
		
		if (isset($_POST['priority'])) :
			
			$priority = trim(stripslashes($_POST['priority']));
			
			if ($priority) :

				// Remember previous values
				$current_term = current(wp_get_object_terms( $post->ID, 'ticket_priority' ));
				
				$current_term_id = $current_term->term_id;
				$current_term_name = $current_term->name;
				
				$new_term = get_term_by( 'id', $priority, 'ticket_priority');
				$new_term_id = $new_term->term_id;
				$new_term_name = $new_term->name;
				$new_term_slug = $new_term->slug;
				
				if ($current_term_id!=$new_term_id) :
				
					// Add meta values
					add_comment_meta($comment_id, 'old_priority', $current_term_name, true);
					add_comment_meta($comment_id, 'new_priority', $new_term_name, true);
					
					// Update value of ticket
					wp_set_object_terms($post->ID, $new_term_slug, 'ticket_priority');
					
					$updated = true;

					$update_message[] = sprintf( __('Priority: <strong>%s</strong> &rarr; <strong>%s</strong>', 'woothemes'), $current_term_name, $new_term_name );
				
				endif;
			endif;
			
		endif;
		
		if (isset($_POST['type'])) :
			
			$type = trim(stripslashes($_POST['type']));

			// Remember previous values
			$current_term = current(wp_get_object_terms( $post->ID, 'ticket_type' ));
			
			if ($current_term) :
				$current_term_id = $current_term->term_id;
				$current_term_name = $current_term->name;
			else :
				$current_term_id = 0;
				$current_term_name = __('N/A', 'woothemes');
			endif;
			
			if ($type) :
				$new_term = get_term_by( 'id', $type, 'ticket_type');
				$new_term_id = $new_term->term_id;
				$new_term_name = $new_term->name;
				$new_term_slug = $new_term->slug;
			else :
				$new_term_id = 0;
				$new_term_name = __('N/A', 'woothemes');
				$new_term_slug = '';
			endif;
			
			if ($current_term_id!=$new_term_id) :
			
				// Add meta values
				add_comment_meta($comment_id, 'old_type', $current_term_name, true);
				add_comment_meta($comment_id, 'new_type', $new_term_name, true);
				
				// Update value of ticket
				wp_set_object_terms($post->ID, $new_term_slug, 'ticket_type');
				
				$updated = true;

				$update_message[] = sprintf( __('Type: <strong>%s</strong> &rarr; <strong>%s</strong>', 'woothemes'), $current_term_name, $new_term_name );
			
			endif;
							
		endif;
		
		if (isset($_POST['status'])) :
			
			$status = trim(stripslashes($_POST['status']));
			
			if ($status) :

				// Remember previous values
				$current_term = current(wp_get_object_terms( $post->ID, 'ticket_status' ));
				$current_term_id = $current_term->term_id;
				$current_term_name = $current_term->name;
				
				$new_term = get_term_by( 'id', $status, 'ticket_status');
				$new_term_id = $new_term->term_id;
				$new_term_name = $new_term->name;
				$new_term_slug = $new_term->slug;
				
				if ($current_term_id!=$new_term_id) :
				
					// Add meta values
					add_comment_meta($comment_id, 'old_status', $current_term_name, true);
					add_comment_meta($comment_id, 'new_status', $new_term_name, true);
					
					// Update value of ticket
					wp_set_object_terms($post->ID, $new_term_slug, 'ticket_status');
					
					$updated = true;

					$update_message[] = sprintf( __('Status: <strong>%s</strong> &rarr; <strong>%s</strong>', 'woothemes'), $current_term_name, $new_term_name );
				
				endif;
			endif;
			
		endif;
		
		if (isset($_POST['tags']) && $_POST['tags']) :
			
			$tags = explode(',', trim(stripslashes($_POST['tags'])));
			$tags = array_map('strtolower', $tags);
			$tags = array_map('trim', $tags);

			if (sizeof($tags)>0) :
				// Add meta values
				add_comment_meta($comment_id, 'added_tags', 'yes', true);

				// True to append
				wp_set_object_terms($post->ID, $tags, 'ticket_tags', true);
				
				$updated = true;
			endif;
			
		endif;
		
		/* Handle any new attachments */

		if(isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
		
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				
			$comment_attachment = wp_handle_upload($_FILES['attachment'], array('test_form'=>false), current_time('mysql'));
						
			if ( isset($comment_attachment['error']) ) :
				// Ohhh Bugger
				wp_delete_comment( $comment_id );
				wp_die( 'Attachment Error: ' . $comment_attachment['error'] );
				exit;
			endif;
	
			$comment_attachment_data = array(
				'post_mime_type' => $comment_attachment['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($comment_attachment['file'])),
				'post_content' => '',
				'post_status' => 'inherit',
				'post_author' => get_current_user_id()
			);
			$comment_attachment_id = wp_insert_attachment( $comment_attachment_data, $comment_attachment['file'], $post->ID );
			$comment_attachment_metadata = wp_generate_attachment_metadata( $comment_attachment_id, $comment_attachment['file'] );
			wp_update_attachment_metadata( $comment_attachment_id,  $comment_attachment_metadata );
			
			add_comment_meta($comment_id, 'added_file', basename($comment_attachment['file']), true);
		
		endif;
		
		if ($updated) :
			$ticket_updated = true;
			do_action('ticket_updated', $post->ID, $update_message, $comment_id);
		endif;
		
		do_action('new_ticket_comment', $post->ID);
	
	endif;
}

		
if (!function_exists("the_commenter_link")) {
	function the_commenter_link() {
	    $commenter = get_comment_author_link();
	    if ( ereg( ']* class=[^>]+>', $commenter ) ) {$commenter = ereg_replace( '(]* class=[\'"]?)', '\\1url ' , $commenter );
	    } else { $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );}
	    echo $commenter ;
	}
}


if (!function_exists("the_commenter_avatar")) {
	function the_commenter_avatar($args) {
	    $email = get_comment_author_email();
	    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( "$email",  $args['avatar_size']) );
	    echo $avatar;
	}
}