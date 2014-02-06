<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Remove Meta Boxes
- Add meta boxes
- Ticket Meta Box
- Process Ticket Meta Box

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Remove Meta Boxes */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_remove_meta_boxes() {
	remove_meta_box( 'ticket_statusdiv', 'ticket', 'side' );
	remove_meta_box( 'ticket_prioritydiv', 'ticket', 'side' );
	remove_meta_box( 'ticket_typediv', 'ticket', 'side' );
}

add_action( 'admin_menu' , 'woo_supportpress_remove_meta_boxes' );

/*-----------------------------------------------------------------------------------*/
/* Add Meta Boxes */
/*-----------------------------------------------------------------------------------*/

add_action( 'add_meta_boxes', 'woo_supportpress_meta_boxes' );

function woo_supportpress_meta_boxes() {
	add_meta_box( 'supportpress-ticket-info', __('Ticket Information', 'woothemes'), 'supportpress_ticket_meta', 'ticket', 'normal', 'high' );
}


/*-----------------------------------------------------------------------------------*/
/* Ticket Meta Box */
/*-----------------------------------------------------------------------------------*/

function supportpress_ticket_meta() {
	global $post;
	
	wp_nonce_field( 'woo_supportpress_save_ticket_meta', 'woo_supportpress_save_ticket_meta_nonce' );
	
	$ticket_id = $post->ID;
	
	$ticket_details = woo_supportpress_get_ticket_details( $ticket_id );
	
	?>
	<table class="woo_metaboxes_table">
		<tbody>
			
			<tr>		
				<th class="woo_metabox_names">
					<label for="woothemes__responsible"><?php _e('Ticket Owner', 'woothemes'); ?></label>
				</th>
				<td>
					<?php
					$agents_array = $users_array = array();
					
					$query_args = array();
					$query_args['fields'] = array( 'ID', 'display_name' );
					$query_args['role'] = 'subscriber';
					$users = get_users( $query_args );
					foreach ($users as $user) $users_array[$user->ID] = $user->display_name;
					
					$query_args = array();
					$query_args['fields'] = array( 'ID', 'display_name' );
					$query_args['who'] = 'authors';
					$users = get_users( $query_args );
					foreach ($users as $user) $agents_array[$user->ID] = $user->display_name;
					?>
					<select class="woo_input_select" id="post_author_override" name="post_author_override">
						<?php if (sizeof($agents_array) > 0) : ?>
						<optgroup label="<?php _e('Agents', 'woothemes'); ?>">
							<?php foreach ($agents_array as $id => $name) : ?>
							
								<option value="<?php echo $id; ?>" <?php if ($post->post_author==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
								
							<?php endforeach; ?>
						</optgroup>
						<?php endif; ?>
						<?php if (sizeof($users_array) > 0) : ?>
						<optgroup label="<?php _e('Clients', 'woothemes'); ?>">
							<?php foreach ($users_array as $id => $name) : ?>
							
								<option value="<?php echo $id; ?>" <?php if ($post->post_author==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
								
							<?php endforeach; ?>
						</optgroup>
						<?php endif; ?>
					</select>
					<span class="woo_metabox_desc"><?php _e('Define who owns the ticket.', 'woothemes'); ?></span>
				</td>
			</tr>
			
			<tr>		
				<th class="woo_metabox_names">
					<label for="woothemes__responsible"><?php _e('Assigned to...', 'woothemes'); ?></label>
				</th>
				<td>
					<?php
					$query_args['fields'] = array( 'ID', 'display_name' );
					$query_args['who'] = array( 'authors' );
					$users = get_users( $query_args );
					$users_array = array();
					foreach ($users as $user) :
						$users_array[$user->ID] = $user->display_name;
					endforeach;
					?>
					<select class="woo_input_select" id="woothemes__responsible" name="_responsible">
						<option value=""><?php _e('Anyone', 'woothemes'); ?></option>
						<?php foreach ($users_array as $id => $name) : ?>
						
							<option value="<?php echo $id; ?>" <?php if (isset($ticket_details['assigned_to']) && $ticket_details['assigned_to']->ID==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
							
						<?php endforeach; ?>
					</select>
					<span class="woo_metabox_desc"><?php _e('Define who the ticket is assigned to (optional).', 'woothemes'); ?></span>
				</td>
			</tr>
			
			<tr>		
				<th class="woo_metabox_names">
					<label for="woothemes_ticket_priority"><?php _e('Ticket Priority', 'woothemes'); ?></label>
				</th>
				<td>
					<?php
					$term_array = array();
					$terms = get_terms( 'ticket_priority', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
					if ($terms && sizeof($terms) > 0) :
						foreach ($terms as $term) :
							$term_array[$term->term_id] = $term->name;
						endforeach;
					endif;
					?>
					<select class="woo_input_select" id="woothemes_ticket_priority" name="ticket_priority">
						<?php foreach ($term_array as $id => $name) : ?>
						
							<option value="<?php echo $id; ?>" <?php if (isset($ticket_details['priority']->term_id) && $ticket_details['priority']->term_id==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
							
						<?php endforeach; ?>
					</select>
					<span class="woo_metabox_desc"><?php _e('Define the current ticket priority.', 'woothemes'); ?></span>
				</td>
			</tr>
			
			<tr>		
				<th class="woo_metabox_names">
					<label for="woothemes_ticket_type"><?php _e('Ticket Type', 'woothemes'); ?></label>
				</th>
				<td>
					<?php
					$term_array = array();
					$terms = get_terms( 'ticket_type', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
					if ($terms && sizeof($terms) > 0) :
						foreach ($terms as $term) :
							$term_array[$term->term_id] = $term->name;
						endforeach;
					endif;
					?>
					<select class="woo_input_select" id="woothemes_ticket_type" name="ticket_type">
						<?php foreach ($term_array as $id => $name) : ?>
						
							<option value="<?php echo $id; ?>" <?php if (isset($ticket_details['type']->term_id) && $ticket_details['type']->term_id==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
							
						<?php endforeach; ?>
					</select>
					<span class="woo_metabox_desc"><?php _e('Define the current ticket type.', 'woothemes'); ?></span>
				</td>
			</tr>
			
			<tr>		
				<th class="woo_metabox_names">
					<label for="woothemes_ticket_status"><?php _e('Ticket Status', 'woothemes'); ?></label>
				</th>
				<td>
					<?php
					$term_array = array();
					$terms = get_terms( 'ticket_status', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
					if ($terms && sizeof($terms) > 0) :
						foreach ($terms as $term) :
							$term_array[$term->term_id] = $term->name;
						endforeach;
					endif;
					?>
					<select class="woo_input_select" id="woothemes_ticket_status" name="ticket_status">
						<?php foreach ($term_array as $id => $name) : ?>
						
							<option value="<?php echo $id; ?>" <?php if (isset($ticket_details['status']->term_id) && $ticket_details['status']->term_id==$id) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
							
						<?php endforeach; ?>
					</select>
					<span class="woo_metabox_desc"><?php _e('Define the current ticket status.', 'woothemes'); ?></span>
				</td>
			</tr>
			
		</tbody>
	</table>
	<?php
}	


/*-----------------------------------------------------------------------------------*/
/* Process Ticket Meta Box */
/*-----------------------------------------------------------------------------------*/

add_action( 'save_post', 'supportpress_ticket_meta_save', 1, 2 );

function supportpress_ticket_meta_save( $post_id, $post ) {
	global $wpdb;
	
	if ( !$_POST ) return $post_id;
	if ( $post->post_type != 'ticket' ) return $post_id;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
	if ( !isset($_POST['woo_supportpress_save_ticket_meta_nonce']) || !wp_verify_nonce( $_POST['woo_supportpress_save_ticket_meta_nonce'], 'woo_supportpress_save_ticket_meta' )) return $post_id;
	if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
	
	$data = array();
	
	// Get Post Data
	$data['ticket_status']			= stripslashes( $_POST['ticket_status'] );
	$data['ticket_priority']		= stripslashes( $_POST['ticket_priority'] );
	$data['ticket_type']			= stripslashes( $_POST['ticket_type'] );
	$data['_responsible']			= stripslashes( $_POST['_responsible'] );
	
	// Priority
	$new_term_slug = get_term_by( 'id', $data['ticket_priority'], 'ticket_priority')->slug;
	wp_set_object_terms( $post_id, $new_term_slug, 'ticket_priority' );
	
	// Status
	$new_term_slug = get_term_by( 'id', $data['ticket_status'], 'ticket_status')->slug;
	wp_set_object_terms( $post_id, $new_term_slug, 'ticket_status' );
	
	// Type
	$new_term_slug = get_term_by( 'id', $data['ticket_type'], 'ticket_type')->slug;
	wp_set_object_terms( $post_id, $new_term_slug, 'ticket_type' );
		
	// Custom fields
	update_post_meta( $post_id, '_responsible', $data['_responsible'] );

}
