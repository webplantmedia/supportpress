<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Updates
- prevent_admin_access
- AJAX searches
- Ticket -> KB article
- KB Useful feature
- SECURITY - prevent unauthorised access
- Detect Ajax
- Buffer output and init
- Shorten Title
- Handle ticket ordering queries
- Handle ticket assigned to filter
- Handle ticket watched filter
- Get open ticket count
- Get open tickets for user
- Get open tickets started by user
- Get resolved tickets started by user
- Get unassigned ticket count
- Get file size in readable format
- Get Dates/Human Time diff
- Get committers
- Get members
- Sorting headings on tickets list
- Load ticket details
- Page navigation
- WooTabs - Popular Posts
- WooTabs - Latest Posts
- WooTabs - Latest Comments
- Post Meta
- Subscribe & Connect
- Misc
- WordPress 3.0 New Features Support
- Knowledgebase search pagniation

-----------------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------------------*/
/* Updates
/*-----------------------------------------------------------------------------------*/

add_action('new_ticket', 'supportpress_last_update');
add_action('new_message', 'supportpress_last_update');
add_action('new_ticket_comment', 'supportpress_last_update');

function supportpress_last_update() {
	if (is_user_logged_in() && get_current_user_id()) :
		// User made an update!
		update_user_meta( get_current_user_id(), 'last_update', strtotime(current_time('mysql')));
	endif;
}

/*-----------------------------------------------------------------------------------*/
/* prevent_admin_access
/*-----------------------------------------------------------------------------------*/

add_action('admin_init', 'supportpress_prevent_admin_access');

function supportpress_prevent_admin_access() {

	if (get_option('woo_prevent_admin_access')=='true' && is_admin() && !woo_supportpress_is_ajax() && !current_user_can('manage_tickets')) :
		wp_safe_redirect(home_url());
		exit;
	endif;

}

/*-----------------------------------------------------------------------------------*/
/* AJAX searches
/*-----------------------------------------------------------------------------------*/

add_action('wp_ajax_search_kb', 'supportpress_search_kb');
add_action('wp_ajax_nopriv_search_kb', 'supportpress_search_kb');

function supportpress_search_kb() {

	check_ajax_referer( 'kb-search', 'security' );

	$search = (string) urldecode(stripslashes(strip_tags($_POST['search'])));
	if (isset($_POST['only_show_if_found'])) $only_show_if_found = (int) urldecode(stripslashes(strip_tags($_POST['only_show_if_found']))); else $only_show_if_found = 0;

	$args = array(
		'post_type'	=> 'knowledgebase',
		'post_status' => 'publish',
		'posts_per_page' => 10,
		's' => $search
	);
	$results = get_posts( $args );

	if ($results) : ?>

		<ul class="post-list">

	        <?php foreach ($results as $post) : $votes_up = (int) get_post_meta($post->ID, 'votes_up', true); ?>

	            <li class="kb-item">

	            	<span class="likes tooltip" title="<?php echo sprintf(_n('%s person found this useful', '%s people found this useful', $votes_up, 'woothemes'), $votes_up); ?> "><?php echo $votes_up; ?></span>

	            	<a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title; ?></a>

	            	<small class="meta"><?php _e('Posted', 'woothemes'); ?> <?php echo woo_supportpress_human_time_diff($post->post_date); ?> <?php echo get_the_term_list( $post->ID, 'knowledgebase_category', __(' in ', 'woothemes'), ', ', '' ); ?></small>

	            </li>

	        <?php endforeach; ?>

		</ul>

	<?php elseif ( ! $only_show_if_found ) : ?>

		<p><?php _e('No results found', 'woothemes'); ?></p>

	<?php endif;

	// Quit out
	die();
}

add_action('wp_ajax_search_tickets', 'supportpress_search_tickets');
add_action('wp_ajax_nopriv_search_tickets', 'supportpress_search_tickets');

function supportpress_search_tickets() {

	check_ajax_referer( 'ticket-search', 'security' );

	if (!is_user_logged_in()) die();

	global $wpdb;

	$search = (string) urldecode(stripslashes(strip_tags($_POST['search'])));

	$search = str_replace('#', '', $search);

	if (is_numeric($search)) :

		$args = array(
			'post_type'	=> 'ticket',
			'post_status' => 'publish',
			'posts_per_page' => 10,
			'post__in' => array(0, $search)
		);

	else :

		$args = array(
			'post_type'	=> 'ticket',
			'post_status' => 'publish',
			'posts_per_page' => 10,
			's' => $search
		);

	endif;
	$results = get_posts( $args );

	if ($results) : ?>

		<ul class="user-dashboard-tickets">

	        <?php foreach ($results as $post) :

	        	$ticket_details = woo_supportpress_get_ticket_details( $post->ID );

				$status = (isset($ticket_details['status']->name)) ? $ticket_details['status']->name : __('new', 'woothemes');
				$type = (isset($ticket_details['type']->name)) ? $ticket_details['type']->name : __('problem', 'woothemes');
				$priority = (isset($ticket_details['priority']->name)) ? $ticket_details['priority']->name : __('low', 'woothemes');

				$last_comment = $wpdb->get_results("SELECT comment_date, comment_author, user_id FROM $wpdb->comments WHERE comment_post_ID = $post->ID ORDER BY comment_date DESC;");

				if (!$last_comment) $last_update = $post->post_date; else $last_update = $last_comment[0]->comment_date;
				if (!$last_comment) $last_user = $post->post_author; else $last_user = $last_comment[0]->user_id;

				$user_info = get_userdata($last_user);
	        	?>

	            <li class="status-<?php echo sanitize_html_class($status); ?> priority-<?php echo sanitize_html_class($priority); ?>">

					<h5><a href="<?php echo get_permalink($post->ID); ?>"><span>#<?php echo $post->ID; ?></span> <?php echo $post->post_title; ?></a></h5>
					<ul class="meta">
						<li class="status"><mark><?php echo ucwords($status); ?></mark></li>
						<li class="type"><mark><?php echo ucwords($type); ?></mark></li>
						<li class="priority"><mark><?php echo ucwords($priority); ?></mark></li>
						<?php echo '<li class="updated">'.sprintf(__('Updated %s ago by <a href="%s">%s</a>.', 'woothemes'), '<time title="'.date('c', strtotime($last_update)).'">'.human_time_diff(strtotime($last_update), current_time('timestamp')).'</time>', get_author_posts_url($last_user), $user_info->display_name ).'</li>';  ?>
					</ul>

				</li>

	        <?php endforeach; ?>

		</ul>

	<?php else : ?>

		<p><?php _e('No results found', 'woothemes'); ?></p>

	<?php endif;

	// Quit out
	die();
}

/*-----------------------------------------------------------------------------------*/
/* Custom term order
/*-----------------------------------------------------------------------------------*/

function get_terms_orderby_description( $orderby, $args ) {
	if (isset($args['orderby']) && $args['orderby']=='description') return 'tt.description';
	return $orderby;
}

add_filter('get_terms_orderby', 'get_terms_orderby_description', 2, 2);

/*-----------------------------------------------------------------------------------*/
/* Ticket -> KB article
/*-----------------------------------------------------------------------------------*/

function supportpress_ticket_to_kb_title( $title ) {

	if (isset($_GET['ticket_id'])) :

		$ticket_id = (int) $_GET['ticket_id'];
		$ticket = get_post($ticket_id);
		$title = $ticket->post_title;

	endif;

	return $title;

}

function supportpress_ticket_to_kb_content( $content ) {

	if (isset($_GET['ticket_id'])) :
		$ticket_id = (int) $_GET['ticket_id'];
		$ticket = get_post($ticket_id);

		$content = '<h2>'.__('Problem:', 'woothemes').'</h2><blockquote>';

		$content .= $ticket->post_content;

		$content .= '<p><cite>'.__('Original poster', 'woothemes').'</cite></p></blockquote>';

		$content .= '<h2>'.__('Solution:', 'woothemes').'</h2>';

		$comments = get_comments('post_id='.$ticket_id);
		foreach($comments as $comment) :

			if ($comment->comment_content=='[UPDATE]') continue;

			$poster = '';
			$class = '';

			// User
			if ($comment->user_id>0) :
				if (user_can($comment->user_id, 'manage_tickets')) :

					$user = get_user_by('id', $comment->user_id);
					$class = 'agent';
					$poster .= '<p><cite>'.$user->display_name.'</cite></p>';

				elseif ($ticket->post_author==$comment->user_id) :

					$poster .= '<p><cite>'.__('Original poster', 'woothemes').'</cite></p>';

				endif;
			endif;

			$content .= '<blockquote class="'.$class.'">'.$comment->comment_content.$poster;

			$content .= '</blockquote>';

		endforeach;


	endif;

	return $content;

}

if (is_admin() && basename($_SERVER["SCRIPT_NAME"])=='post-new.php') :
	add_filter('default_title', 'supportpress_ticket_to_kb_title');
	add_filter('the_editor_content', 'supportpress_ticket_to_kb_content');
endif;

/*-----------------------------------------------------------------------------------*/
/* KB Useful feature
/*-----------------------------------------------------------------------------------*/

function supportpress_show_votes() {

	global $post;

	$votes_up = (int) get_post_meta($post->ID, 'votes_up', true);
	$votes_down = (int) get_post_meta($post->ID, 'votes_down', true);
	$total = $votes_up + $votes_down;

	$useful_title 		= sprintf(_n('%s person found this useful', '%s people found this useful', $votes_up, 'woothemes'), $votes_up);
	$not_useful_title 	= sprintf(_n('%s person did not find this useful', '%s people did not find this useful', $votes_down, 'woothemes'), $votes_down);

	echo '<p class="useful">';

	if (is_user_logged_in()) :

		$past_votes = (array) get_user_meta(get_current_user_id(), 'past_votes', true);

		if (!in_array( $post->ID, $past_votes )) :

			// User has not voted yet
			echo '<a href="'.add_query_arg('vote_up', $post->ID, get_permalink($post->ID)).'" class="likes tooltip" original-title="'.$useful_title.'">'.$votes_up.'</a> ';
			echo '<a href="'.add_query_arg('vote_down', $post->ID, get_permalink($post->ID)).'" class="dislikes tooltip" original-title="'.$not_useful_title.'">'.$votes_down.'</a> ';

		else :

			// User has voted
			echo '<span class="likes tooltip" original-title="'.$useful_title.'">'.$votes_up.'</span> ';
			echo '<span class="dislikes tooltip" original-title="'.$not_useful_title.'">'.$votes_down.'</span> ';

		endif;

	else :

		// User is not logged in
		echo '<span class="likes tooltip" original-title="'.$useful_title.'">'.$votes_up.'</span> ';
		echo '<span class="dislikes tooltip" original-title="'.$not_useful_title.'">'.$votes_down.'</span> ';

	endif;

	echo '</p>';

}

function supportpress_vote() {

	if (!is_user_logged_in()) return;

	$past_votes = (array) get_user_meta(get_current_user_id(), 'past_votes', true);

	if (isset( $_GET['vote_up'] ) && $_GET['vote_up']>0) :

		$post_id = (int) $_GET['vote_up'];
		$the_post = get_post($post_id);

		if ($the_post && !in_array( $post_id, $past_votes )) :

			$past_votes[] = $post_id;
			update_user_meta(get_current_user_id(), 'past_votes', $past_votes);

			$post_votes = (int) get_post_meta($post_id, 'votes_up', true);
			$post_votes++;
			update_post_meta($post_id, 'votes_up', $post_votes);

		endif;

	elseif (isset( $_GET['vote_down'] ) && $_GET['vote_down']>0) :

		$post_id = (int) $_GET['vote_down'];
		$the_post = get_post($post_id);

		if ($the_post && !in_array( $post_id, $past_votes )) :

			$past_votes[] = $post_id;
			update_user_meta(get_current_user_id(), 'past_votes', $past_votes);

			$post_votes = (int) get_post_meta($post_id, 'votes_down', true);
			$post_votes++;
			update_post_meta($post_id, 'votes_down', $post_votes);

		endif;

	endif;

}

add_action('init', 'supportpress_vote');


/*-----------------------------------------------------------------------------------*/
/* SECURITY - prevent unauthorised access
/*-----------------------------------------------------------------------------------*/

if (get_option('woo_members_only')=='true') add_action('template_redirect', 'supportpress_check_access');

function supportpress_check_access() {

	if (!is_user_logged_in()) supportpress_redirect_login();

}

function supportpress_members_only() {

	if (!is_user_logged_in()) supportpress_redirect_login();

}

function supportpress_agents_only() {

	if (!is_agent()) supportpress_redirect_home();

}

function user_is_member_of_site() {

	if (is_user_logged_in()) :

		if (!is_multisite()) :
			return true;
		else :
			if (is_user_member_of_blog( get_current_user_id() )) :
				return true;
			endif;
		endif;

	endif;

	return false;

}

function is_agent() {

	if ((is_multisite() && current_user_can('manage_sites')) || (is_multisite() && user_is_member_of_site() && current_user_can('manage_options')) || (!is_multisite() && current_user_can('manage_options'))) return true; // Admin / Site Admin can view whatever happens

	if (current_user_can('manage_tickets') && user_is_member_of_site()) return true;

}

function supportpress_redirect_home() {
	wp_redirect( home_url() );
	exit;
}
function supportpress_redirect_login() {
	wp_redirect( wp_login_url( home_url() ) );
	exit;
}

/*-----------------------------------------------------------------------------------*/
/* Detect Ajax
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_is_ajax')) {
	function woo_supportpress_is_ajax() {
		if (defined('DOING_AJAX')) return true;
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
		return false;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Buffer output and init
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_buffer_output')) {
	function woo_supportpress_buffer_output() {
		@session_start();
		@ob_start();
	}
}
add_action('init', 'woo_supportpress_buffer_output', 0);


/*-----------------------------------------------------------------------------------*/
/* Handle ticket ordering queries
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_ticket_ordering')) {
	function woo_supportpress_ticket_ordering() {

		global $wp_query, $order;

		if (isset($_GET['orderby'])) $orderby = $_GET['orderby']; else $orderby = 'age';
		if (isset($_GET['order'])) $order = $_GET['order']; else $order = 'asc';

		$order_query = array();

		switch ($orderby) :
			case "status" :
				add_filter('posts_clauses', 'woo_supportpress_order_by_term_desc_posts_clauses');
			break;
			case "ticket" :
				$order_query = array(
					'orderby' => 'ID'
				);
			break;
			case "title" :
				$order_query = array(
					'orderby' => 'title'
				);
			break;
			default :
				$order_query = array(
					'orderby' => 'modified'
				);
			break;
		endswitch;

		return $order_query;

	}

	function woo_supportpress_order_by_term_desc_posts_clauses( $args ) {

		global $wpdb;

		if (isset($_GET['order'])) $order = $_GET['order']; else $order = 'asc';

		$args['where'] .= " AND $wpdb->term_taxonomy.taxonomy = 'ticket_status' ";

		if (strstr($args['join'], '_postmeta')) :
			$args['join'] = "
				LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
				LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
				LEFT JOIN $wpdb->terms ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
				INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
			";
		else :
			$args['join'] = "
				LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
				LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
				LEFT JOIN $wpdb->terms ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)";
		endif;

		$args['orderby'] = "$wpdb->term_taxonomy.description $order, $wpdb->posts.post_date desc";

		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}

	function woo_supportpress_ticket_ordering_remove() {
		remove_filter( 'posts_clauses', 'woo_supportpress_order_by_term_desc_posts_clauses' );
	}
	add_action('after_ticket_query', 'woo_supportpress_ticket_ordering_remove');
}


/*-----------------------------------------------------------------------------------*/
/* Handle ticket assigned to filter
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_ticket_assigned_to')) {
	function woo_supportpress_ticket_assigned_to() {
		$assigned_to = array();

		if (isset($_GET['assigned_to']) && !$_GET['assigned_to']) $assigned_to = array(
			'meta_query' => array(
				array(
					'key' => '_responsible',
					'value' => array( '0', '' ),
					'compare' => 'IN'
				)
			)
		);

		if (isset($_GET['assigned_to']) && $_GET['assigned_to'] > 0) $assigned_to = array(
			'meta_query' => array(
				array(
					'key' => '_responsible',
					'value' => $_GET['assigned_to'],
					'compare' => '='
				)
			)
		);

		return $assigned_to;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Handle ticket watched filter
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_watching_tickets')) {
	function woo_supportpress_watching_tickets() {
		global $wpdb;

		$watching = $wpdb->get_col("SELECT item_id FROM ".$wpdb->prefix."supportpress_watching_tickets WHERE user_id = ".get_current_user_id().";");
		$watching[] = 0;

		if (isset($_GET['watching'])) $watching = array( 'post__in' => $watching );

		return $watching;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get open ticket count
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_open_ticket_count')) {
	function woo_supportpress_get_open_ticket_count() {

		if (false === ( $open_count = get_transient( 'open_count' ) ) ) :

			$new_status = get_term_by('slug', NEW_STATUS_SLUG, 'ticket_status');
			$open_status = get_term_by('slug', OPEN_STATUS_SLUG, 'ticket_status');
			$pending_status = get_term_by('slug', PENDING_STATUS_SLUG, 'ticket_status');

			$open_count = $new_status->count + $open_status->count + $pending_status->count;

			// Cache for 3 days
			set_transient( 'open_count', $open_count, 60*60*72);

		endif;

		return $open_count;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get open tickets for user
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_open_user_tickets')) {
	function woo_supportpress_get_open_user_tickets( $user_id ) {

		if (false === ( $open_count = get_transient( 'user_' . $user_id . '_open_count' ) ) ) :

			$user_tickets = get_posts( array(
				'meta_key' => '_responsible',
				'meta_value' => $user_id,
				'post_type' => 'ticket',
				'numberposts' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ticket_status',
						'field' => 'slug',
						'terms' => array(NEW_STATUS_SLUG, OPEN_STATUS_SLUG, PENDING_STATUS_SLUG)
					)
				)
			) );

			$open_count = sizeof($user_tickets);

			// Cache for 3 days
			set_transient( 'user_' . $user_id . '_open_count', $open_count, 60*60*72);

		endif;

		return $open_count;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get open tickets started by user
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_authors_tickets')) {
	function woo_supportpress_get_authors_tickets( $user_id ) {

		if (false === ( $open_count = get_transient( 'user_' . $user_id . '_author_tickets_count' ) ) ) :

			$user_tickets = get_posts( array(
				'author' => $user_id,
				'post_type' => 'ticket',
				'numberposts' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ticket_status',
						'field' => 'slug',
						'terms' => array(NEW_STATUS_SLUG, OPEN_STATUS_SLUG, PENDING_STATUS_SLUG)
					)
				)
			) );

			$open_count = sizeof($user_tickets);

			// Cache for 3 days
			set_transient( 'user_' . $user_id . '_author_tickets_count', $open_count, 60*60*72);

		endif;

		return $open_count;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get resolved tickets started by user
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_authors_resolved_tickets')) {
	function woo_supportpress_get_authors_resolved_tickets( $user_id ) {

		if (false === ( $open_count = get_transient( 'user_' . $user_id . '_author_resolved_tickets_count' ) ) ) :

			$user_tickets = get_posts( array(
				'author' => $user_id,
				'post_type' => 'ticket',
				'numberposts' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ticket_status',
						'field' => 'slug',
						'terms' => array(RESOLVED_STATUS_SLUG)
					)
				)
			) );

			$open_count = sizeof($user_tickets);

			// Cache for 3 days
			set_transient( 'user_' . $user_id . '_author_resolved_tickets_count', $open_count, 60*60*72);

		endif;

		return $open_count;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get unassigned ticket count
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_unassigned_tickets')) {
	function woo_supportpress_get_unassigned_tickets() {

		if (false === ( $unassigned_count = get_transient( 'unassigned_count' ) ) ) :

			$user_tickets = get_posts( array(
				'meta_query' => array(
					array(
						'key' => '_responsible',
						'value' => array( '0', '' ),
						'compare' => 'IN'
					)
				),
				'post_type' => 'ticket',
				'numberposts' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'ticket_status',
						'field' => 'slug',
						'terms' => array(NEW_STATUS_SLUG, OPEN_STATUS_SLUG, PENDING_STATUS_SLUG)
					)
				)
			) );

			$unassigned_count = sizeof($user_tickets);

			// Cache for 3 days
			set_transient( 'unassigned_count', $unassigned_count, 60*60*72);

		endif;

		return $unassigned_count;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get file size in readable format
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_filesize')) {
	function woo_supportpress_get_filesize( $file ) {
		$bytes = filesize($file);
		$s = array('b', 'Kb', 'Mb', 'Gb');
		$e = floor(log($bytes)/log(1024));
		return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get Dates/Human Time diff */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_human_time_diff')) {
	function woo_supportpress_human_time_diff( $date ) {

		if (strtotime($date)<strtotime('NOW -7 day')) return date('jS F Y', strtotime($date));
		else return human_time_diff(strtotime($date), current_time('timestamp')) . __(' ago', 'woothemes');
	}
}

/*-----------------------------------------------------------------------------------*/
/* Get committers */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_support_staff')) {
	function woo_supportpress_get_support_staff() {

		$users = array();
		$roles = array('administrator', 'author', 'editor', 'contributor');

		foreach ($roles as $role) :
			$users_query = new WP_User_Query( array(
				'fields' => 'all_with_meta',
				'role' => $role,
				'orderby' => 'display_name'
				) );
			$results = $users_query->get_results();
			if ($results) $users = array_merge($users, $results);
		endforeach;

		return $users;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Sorting headings on tickets list */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_sort_by_link')) {
	function woo_supportpress_sort_by_link( $ordering_link = 'age', $label = 'Age' ) {

		if (!isset($_GET['orderby'])) $_GET['orderby'] = 'age';
		if (!isset($_GET['order'])) $_GET['order'] = 'desc';

		if ( $_GET['order'] == 'asc' ) $arrow = '&uarr;'; else $arrow = '&darr;';

		$dir = ( $_GET['orderby']==$ordering_link && $_GET['order'] == 'asc' ) ? 'desc' : "asc";

		echo '<a href="' . add_query_arg( array( 'orderby' => $ordering_link, 'order' => $dir ) ) . '">'.$label.' ';
		if ($_GET['orderby']==$ordering_link) echo $arrow;
		echo '</a>';

	}
}

/*-----------------------------------------------------------------------------------*/
/* Load ticket details */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_supportpress_get_ticket_details')) {
	function woo_supportpress_get_ticket_details( $post_id ) {

		$status = current(wp_get_object_terms( $post_id, 'ticket_status' ));
		$type = current(wp_get_object_terms( $post_id, 'ticket_type' ));
		$priority = current(wp_get_object_terms( $post_id, 'ticket_priority' ));

		if (!isset($status->name)) $status = '';

		if (!isset($type->name)) $type = '';

		if (!isset($priority->name)) $priority = '';

		$ticket_details = array(
			'status' => $status
			,'type' => $type
			,'priority' => $priority
			,'assigned_to' => get_user_by('id', get_post_meta( $post_id, '_responsible', true))
			,'reported_by' => get_post_meta( $post_id, '_reported_by', true)
			,'reported_by_email' => get_post_meta( $post_id, '_reported_by_email', true)
		);

		/* Support 'Anybody' user */
		if (!isset($ticket_details['assigned_to']->display_name)) $ticket_details['assigned_to'] = new StdClass;
		if (!isset($ticket_details['assigned_to']->display_name)) $ticket_details['assigned_to']->display_name = 'Anybody';
		if (!isset($ticket_details['assigned_to']->ID)) $ticket_details['assigned_to']->ID = 0;

		return $ticket_details;

	}
}

/*-----------------------------------------------------------------------------------*/
/* Page navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_pagenav')) {
	function woo_pagenav() {

		if (function_exists('wp_pagenavi') ) { ?>

	<?php wp_pagenavi(); ?>

		<?php } else { ?>

			<?php if ( get_next_posts_link() || get_previous_posts_link() ) { ?>

	            <div class="nav-entries">
	                <?php next_posts_link( '<div class="nav-next fr">'. __( 'Next &rarr;', 'woothemes' ) . '</div>' ); ?>
	                <?php previous_posts_link( '<div class="nav-prev fl">'. __( '&larr; Previous', 'woothemes' ) . '</div>' ); ?>
	                <div class="clear"></div>
	            </div>

			<?php } ?>

		<?php }
	}
}

/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_popular')) {
	function woo_tabs_popular( $posts = 5, $size = 45 ) {
		global $post;
		$popular = get_posts('ignore_sticky_posts=1&orderby=comment_count&showposts='.$posts);
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image('height='.$size.'&width='.$size.'&class=thumbnail&single=true'); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}


/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_latest')) {
	function woo_tabs_latest( $posts = 5, $size = 45 ) {
		global $post;
		$latest = get_posts('ignore_sticky_posts=1&showposts='. $posts .'&orderby=post_date&order=desc');
		foreach($latest as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image('height='.$size.'&width='.$size.'&class=thumbnail&single=true'); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_comments')) {
	function woo_tabs_comments( $posts = 5, $size = 45 ) {
		global $wpdb;
		$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
		comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
		comment_type,comment_author_url,
		SUBSTRING(comment_content,1,50) AS com_excerpt
		FROM $wpdb->comments
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
		$wpdb->posts.ID)
		WHERE comment_approved = '1' AND comment_type = '' AND
		post_password = ''
		ORDER BY comment_date_gmt DESC LIMIT ".$posts;

		$comments = $wpdb->get_results($sql);

		foreach ($comments as $comment) {
		?>
		<li>
			<?php echo get_avatar( $comment, $size ); ?>

			<a href="<?php echo get_permalink($comment->ID); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php _e('on ', 'woothemes'); ?> <?php echo $comment->post_title; ?>">
				<?php echo strip_tags($comment->comment_author); ?>: <?php echo strip_tags($comment->com_excerpt); ?>...
			</a>
			<div class="fix"></div>
		</li>
		<?php
		}
	}
}



/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_post_meta')) {
	function woo_post_meta( ) {
?>
<p class="post-meta">
    <span class="post-date"><?php _e('Posted on', 'woothemes') ?> <?php the_time( get_option( 'date_format' ) ); ?></span>
    <span class="post-author"><?php _e('by', 'woothemes') ?> <?php the_author_posts_link(); ?></span>
    <span class="post-category"><?php _e('in', 'woothemes') ?> <?php the_category(', ') ?></span>
    <?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
</p>
<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {

		global $woo_options;

		// Setup title
		if ( $widget != 'true' )
			$title = $woo_options['woo_connect_title'];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( $woo_options['woo_connect_related'] == "true" AND $widget != "true" )
			$related_posts = do_shortcode('[related_posts limit="5"]');

?>
	<?php if ( $woo_options['woo_connect'] == "true" OR $widget == 'true' ) : ?>
	<div id="connect">
		<h3 class="title"><?php if ( $title ) echo $title; else _e('Subscribe','woothemes'); ?></h3>

		<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
			<p><?php if ($woo_options['woo_connect_content'] != '') echo stripslashes($woo_options['woo_connect_content']); else _e('Subscribe to our e-mail newsletter to receive updates.', 'woothemes'); ?></p>

			<?php if ( $woo_options['woo_connect_newsletter_id'] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $woo_options['woo_connect_newsletter_id']; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
				<input class="email" type="text" name="email" value="<?php _e('E-mail','woothemes'); ?>" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}" />
				<input type="hidden" value="<?php echo $woo_options['woo_connect_newsletter_id']; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo('name'); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit" type="submit" name="submit" value="<?php _e('Submit', 'woothemes'); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $woo_options['woo_connect_mailchimp_list_url'] != "" AND $form != 'on' AND $woo_options['woo_connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $woo_options['woo_connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social<?php if ( $related_posts == '' AND $woo_options['woo_connect_newsletter_id'] != "" ) echo ' fr'; ?>">
		   		<?php if ( $woo_options['woo_connect_rss'] == "true" ) { ?>
		   		<a href="<?php if ( $woo_options['woo_feed_url'] ) { echo $woo_options['woo_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-rss.png" title="Subscribe to our RSS feed" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_twitter'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_twitter']; ?>" class="twitter"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-twitter.png" title="<?php _e('Follow us on Twitter', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_facebook'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_facebook']; ?>" class="facebook"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-facebook.png" title="<?php _e('Connect on Facebook', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_youtube'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_youtube']; ?>" class="youtube"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-youtube.png" title="<?php _e('Watch on YouTube', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_flickr'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_flickr']; ?>" class="flickr"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-flickr.png" title="<?php _e('See photos on Flickr', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_linkedin'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_linkedin']; ?>" class="linkedin"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-linkedin.png" title="<?php _e('Connect on LinkedIn', 'woothemes'); ?>" alt=""/></a>

		   		<?php } if ( $woo_options['woo_connect_delicious'] != "" ) { ?>
		   		<a href="<?php echo $woo_options['woo_connect_delicious']; ?>" class="delicious"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-social-delicious.png" title="<?php _e('Discover on Delicious', 'woothemes'); ?>" alt=""/></a>

				<?php } ?>
			</div>
			<?php endif; ?>

		</div><!-- col-left -->

		<?php if ( $woo_options['woo_connect_related'] == "true" AND $related_posts != '' ) : ?>
		<div class="related-posts col-right">
			<h4><?php _e('Related Posts:', 'woothemes'); ?></h4>
			<?php echo $related_posts; ?>
		</div><!-- col-right -->
		<?php wp_reset_query(); endif; ?>

        <div class="fix"></div>
	</div>
	<?php endif; ?>
<?php
	}
}



/*-----------------------------------------------------------------------------------*/
/* MISC */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_head_css')) {
	function woo_head_css() {}
}

/*if (!function_exists('woo_custom_typography')) {
	function woo_custom_typography() {}
}*/

/*-----------------------------------------------------------------------------------*/
/* WordPress 3.0 New Features Support */
/*-----------------------------------------------------------------------------------*/

if ( function_exists('wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'top-menu' => __( 'Top Menu', 'woothemes' ) ) );
}

/*-----------------------------------------------------------------------------------*/
/* Knowledgebase search pagination */
/*-----------------------------------------------------------------------------------*/

add_action('init','sp_knowledgebase_search_pagination');
function sp_knowledgebase_search_pagination() {
	global $woo_options, $wp_query;
	if (isset($_GET['post_type'])) $post_type = $_GET['post_type']; else $post_type = 'ticket';
	if ($post_type=='knowledgebase') {
		add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
		add_filter( 'woo_pagination_args_defaults', 'woocommerceframework_woo_pagination_defaults', 10 );
	}
}
function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '&post_type=knowledgebase';

	return $settings;
} // End woocommerceframework_add_search_fragment()
function woocommerceframework_woo_pagination_defaults ( $settings ) {
	$settings['use_search_permastruct'] = false;

	return $settings;
} // End woocommerceframework_woo_pagination_defaults()


?>