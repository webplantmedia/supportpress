<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Change author archive slug
- Count users
- Count agents
- Set up Roles & Capabilities
- Watch lists
- Watch Items when posted
- Backend ability to enable/disable agent status

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Change author archive slug */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_author_base() {
	global $wp_rewrite;
	$wp_rewrite->author_base = __('users', 'woothemes');
}
add_action('init', 'woo_supportpress_author_base', 0);


/*-----------------------------------------------------------------------------------*/
/* Count users */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_user_count() {
	$counts = count_users();
	$count = $counts['total_users'] - woo_supportpress_staff_count();
	return $count;
}

/*-----------------------------------------------------------------------------------*/
/* Count agents */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_staff_count() {
	$counts = count_users();
	$user_count = 0;
	$count_roles = array('administrator', 'author', 'editor', 'contributor');
	foreach($counts['avail_roles'] as $role => $count) :
    	if (in_array($role, $count_roles)) $user_count = $user_count + $count;
	endforeach;
	return $user_count;
}

/*-----------------------------------------------------------------------------------*/
/* Set up Roles & Capabilities */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_init_roles() {
	global $wp_roles;

	if (class_exists('WP_Roles')) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();	
	
	if (is_object($wp_roles)) :
		
		// Staff roles
		$wp_roles->add_cap( 'administrator', 'manage_tickets' );
		$wp_roles->add_cap( 'editor', 'manage_tickets' );
		$wp_roles->add_cap( 'author', 'manage_tickets' );
		$wp_roles->add_cap( 'contributor', 'manage_tickets' );
		
		// Client/user role
		$wp_roles->add_cap( 'subscriber', 'submit_tickets' );
		
	endif;
}

add_action('init', 'woo_supportpress_init_roles');

/*-----------------------------------------------------------------------------------*/
/* Watch lists */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_watch_items() {
	if (isset($_GET['watch'])) :
		
		$item = (int) $_GET['watch'];
		if ($item>0) woo_supportpress_watch_item($item, get_current_user_id());
		
		wp_redirect(get_permalink( $item ));
		exit;
		
	elseif (isset($_GET['unwatch'])) :
	
		$item = (int) $_GET['unwatch'];
		if ($item>0) woo_supportpress_unwatch_item($item, get_current_user_id());
		
		wp_redirect(get_permalink( $item ));
		exit;
	
	endif;
}

function woo_supportpress_watch_item($item, $user) {
	global $wpdb;
	
	$result = $wpdb->get_var( "SELECT item_id FROM ".$wpdb->prefix."supportpress_watching_tickets WHERE item_id=".$wpdb->prepare('%d', $item)." AND user_id=".$wpdb->prepare('%d', $user).";" );
	if (!$result) $wpdb->insert( $wpdb->prefix.'supportpress_watching_tickets', array( 'user_id' => $user, 'item_id' => $item ), array( '%d', '%d' ) );	
}

function woo_supportpress_unwatch_item($item, $user) {
	global $wpdb;
	
	$wpdb->query( "DELETE FROM ".$wpdb->prefix."supportpress_watching_tickets WHERE item_id=".$wpdb->prepare('%d', $item)." AND user_id=".$wpdb->prepare('%d', $user).";" );
}

function is_watched() {
	global $post, $wpdb;
	
	$result = $wpdb->get_var( "SELECT item_id FROM ".$wpdb->prefix."supportpress_watching_tickets WHERE item_id=".$wpdb->prepare('%d', $post->ID)." AND user_id=".$wpdb->prepare('%d', get_current_user_id()).";" );
	
	if ($result) return true;
}

if (( isset($_GET['watch']) || isset($_GET['unwatch']) ) && is_user_logged_in()) add_action('init', 'woo_supportpress_watch_items');


/*-----------------------------------------------------------------------------------*/
/* Watch Items when posted */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_watch_new_item($item_id) {
	
	$item = get_post($item_id);
	
	// Poster
	woo_supportpress_watch_item( $item_id, get_current_user_id() );
	
	// Author
	woo_supportpress_watch_item( $item_id, $item->post_author );

	// Person Assigned to ticket
	$assigned_user = get_user_by('id', get_post_meta( $item_id, '_responsible', true));
	if ($assigned_user && !is_wp_error($assigned_user)) :
		woo_supportpress_watch_item( $item_id, $assigned_user->ID );
	endif;
	
}

add_action('new_ticket', 'woo_supportpress_watch_new_item');
add_action('new_message', 'woo_supportpress_watch_new_item');

/*-----------------------------------------------------------------------------------*/
/* Backend ability to enable/disable agent status */
/*-----------------------------------------------------------------------------------*/

add_action( 'edit_user_profile', 'woo_supportpress_show_extra_profile_fields', 10 );
 
function woo_supportpress_show_extra_profile_fields( $user ) { ?>
 
	<h3><?php _e('Agent setup', 'woothemes'); ?></h3>
 
	<table class="form-table">
 
		<tr>
			<th><label for="secret_agent"><?php _e('Secret agent?'); ?></label></th>
 
			<td>
				<input type="checkbox" name="secret_agent" id="secret_agent" <?php checked(get_user_meta( $user->ID, 'secret_agent', true ), 1); ?> class="checkbox" /> 
				<span class="description"><?php _e('Enable this option to hide this agent from the front-end forms.', 'woothemes'); ?></span>
			</td>
		</tr>
 
	</table>
<?php }
 
add_action( 'edit_user_profile_update', 'woo_supportpress_save_extra_profile_fields' );
 
function woo_supportpress_save_extra_profile_fields( $user_id ) {
 
	if ( !current_user_can( 'edit_user', $user_id ) ) return false;
 
	if ($_POST['secret_agent']) update_user_meta( $user_id, 'secret_agent', 1 ); else update_user_meta( $user_id, 'secret_agent', 0 );

}