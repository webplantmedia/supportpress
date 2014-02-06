<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Show Messages
- ticket_updated - delete transient cache
- ticket_updated - update to update last modified time 
- Remove unused parts from HEAD
- Add IE Specific Scripts to HEAD
- Add custom styling to HEAD
- Add custom typograhpy to HEAD
- Add layout to body_class output

-----------------------------------------------------------------------------------*/

add_action('woo_head','woo_custom_styling');			// Add custom styling to HEAD
add_action('woo_head','woo_custom_typography');			// Add custom typography to HEAD
add_filter('body_class','woo_layout_body_class');		// Add layout to body_class output


/*-----------------------------------------------------------------------------------*/
/* Show Messages */
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_show_messages() {
	
	if (isset($_SESSION['note'])) :
		
		echo '<p class="note">'.wptexturize( $_SESSION['note'] ).'</p>';
		unset($_SESSION['note']);
		
	endif;
	
	if (isset($_SESSION['success'])) :
		
		echo '<p class="success">'.wptexturize( $_SESSION['success'] ).'</p>';
		unset($_SESSION['success']);
		
	endif;
	
	if (isset($_SESSION['error'])) :
		
		echo '<div class="notice red delete"><span><p>'.wptexturize( $_SESSION['error'] ).'</p></span></div>';
		unset($_SESSION['error']);
		
	endif;
	
}
add_action('before_content', 'woo_supportpress_show_messages');


/*-----------------------------------------------------------------------------------*/
/* ticket_updated - delete transient cache */
/*-----------------------------------------------------------------------------------*/

add_action('ticket_updated', 'woo_supportpress_delete_transients');
add_action('post_updated', 'woo_supportpress_delete_transients');
add_action('deleted_post', 'woo_supportpress_delete_transients');

function woo_supportpress_delete_transients() {
	global $wpdb;
	
	delete_transient( 'open_count' );
	delete_transient( 'unassigned_count' );
	
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_user_%_open_count')");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_user_%_author_tickets_count')");
	$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_user_%_author_resolved_tickets_count')");
}


/*-----------------------------------------------------------------------------------*/
/* ticket_updated - update to update last modified time */
/*-----------------------------------------------------------------------------------*/

add_action('new_ticket_comment', 'woo_supportpress_update_last_modified');

function woo_supportpress_update_last_modified( $ticket_id ) {
	if ( $ticket_id && $ticket_id>0 ) :
		$ticket = array();
		$ticket['ID'] = $ticket_id;
		wp_update_post( $ticket );
	endif;
}

/*-----------------------------------------------------------------------------------*/
/* Remove unused parts from HEAD */
/*-----------------------------------------------------------------------------------*/

// Remove links to the extra feeds (e.g. category feeds)
remove_action( 'wp_head', 'feed_links_extra', 3 );
// Remove links to the general feeds (e.g. posts and comments)
remove_action( 'wp_head', 'feed_links', 2 );
// Remove prev link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
// Remove start link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
// Display relational links for adjacent posts
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );


/*-----------------------------------------------------------------------------------*/
/* Add IE Specific Scripts to HEAD */
/*-----------------------------------------------------------------------------------*/

// Add specific IE styling/hacks to HEAD
add_action('wp_head','woo_IE_head');
function woo_IE_head() {
?>

<!--[if IE 6]>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/includes/js/pngfix.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/includes/js/menu.js"></script>
<![endif]-->	

<?php
}



/*-----------------------------------------------------------------------------------*/
/* Add Custom Styling to HEAD */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_custom_styling')) {
	function woo_custom_styling() {
	
		global $woo_options;
		
		$output = '';
		// Get options
		$body_color = (!isset($woo_options['woo_body_color'])) ? '' : $woo_options['woo_body_color'];
		$body_img = (!isset($woo_options['woo_body_img'])) ? '' : $woo_options['woo_body_img'];
		$body_repeat = (!isset($woo_options['woo_body_repeat'])) ? '' : $woo_options['woo_body_repeat'];
		$body_position = (!isset($woo_options['woo_body_pos'])) ? '' : $woo_options['woo_body_pos'];
		$link = (!isset($woo_options['woo_link_color'])) ? '' : $woo_options['woo_link_color'];
		$hover = (!isset($woo_options['woo_link_hover_color'])) ? '' : $woo_options['woo_link_hover_color'];
		$button = (!isset($woo_options['woo_button_color'])) ? '' : $woo_options['woo_button_color'];
			
		// Add CSS to output
		if ($body_color)
			$output .= 'body {background:'.$body_color.'}' . "\n";
			
		if ($body_img)
			$output .= 'body {background-image:url('.$body_img.')}' . "\n";

		if ($body_img && $body_repeat && $body_position)
			$output .= 'body {background-repeat:'.$body_repeat.'}' . "\n";

		if ($body_img && $body_position)
			$output .= 'body {background-position:'.$body_position.'}' . "\n";

		if ($link)
			$output .= 'a {color:'.$link.'}' . "\n";

		if ($hover)
			$output .= 'a:hover, .post-more a:hover, .post-meta a:hover, .post p.tags a:hover {color:'.$hover.'}' . "\n";

		if ($button) {
			$output .= 'a.button, a.comment-reply-link, #commentform #submit, #contact-page .submit {background:'.$button.';border-color:'.$button.'}' . "\n";
			$output .= 'a.button:hover, a.button.hover, a.button.active, a.comment-reply-link:hover, #commentform #submit:hover, #contact-page .submit:hover {background:'.$button.';opacity:0.9;}' . "\n";
		}
		
		// Output styles
		if (isset($output) && $output != '') {
			$output = strip_tags($output);
			$output = "<!-- Woo Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
		}
			
	}
} 

/*-----------------------------------------------------------------------------------*/
/* Add custom typograhpy to HEAD */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_custom_typography')) {
	function woo_custom_typography() {
	
		// Get options
		global $woo_options;
				
		// Reset	
		$output = '';
		
		// Add Text title and tagline if text title option is enabled
		if ( isset($woo_options['woo_texttitle']) && $woo_options['woo_texttitle'] == "true" ) {		
			
			if ( $woo_options['woo_font_site_title'] )
				$output .= '#header #logo .site-title a {'.woo_generate_font_css($woo_options['woo_font_site_title']).'}' . "\n";	
			if ( $woo_options['woo_font_tagline'] )
				$output .= '#header #logo .site-description {'.woo_generate_font_css($woo_options['woo_font_tagline']).'}' . "\n";	
		}

		/*if ( isset($woo_options['woo_typography']) && $woo_options['woo_typography'] == "true") {
			
			if ( $woo_options['woo_font_body'] )
				$output .= 'body { '.woo_generate_font_css($woo_options['woo_font_body'], '1.5').' }' . "\n";	

			if ( $woo_options['woo_font_nav'] )
				$output .= '#navigation, #navigation .nav a { '.woo_generate_font_css($woo_options['woo_font_nav'], '1.4').' }' . "\n";	

			if ( $woo_options['woo_font_post_title'] )
				$output .= '.post .title { '.woo_generate_font_css($woo_options['woo_font_post_title']).' }' . "\n";	
		
			if ( $woo_options['woo_font_post_meta'] )
				$output .= '.post-meta { '.woo_generate_font_css($woo_options['woo_font_post_meta']).' }' . "\n";	

			if ( $woo_options['woo_font_post_entry'] )
				$output .= '.entry, .entry p { '.woo_generate_font_css($woo_options['woo_font_post_entry'], '1.5').' } h1, h2, h3, h4, h5, h6 { font-family:'.stripslashes($woo_options['woo_font_post_entry']['face']).'}'  . "\n";	

			if ( $woo_options['woo_font_widget_titles'] )
				$output .= '.widget h3 { '.woo_generate_font_css($woo_options['woo_font_widget_titles']).' }'  . "\n";	
		}*/
		
		// Output styles
		if (isset($output) && $output != '') {
		
			// Enable Google Fonts stylesheet in HEAD
			if (function_exists('woo_google_webfonts')) woo_google_webfonts();
			
			$output = "\n<!-- Woo Custom Typography -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
			
		}
			
	}
} 

if (!function_exists('woo_generate_font_css')) {
	// Returns proper font css output
	function woo_generate_font_css($option, $em = '1') {
		return 'font:'.$option["style"].' '.$option["size"].$option["unit"].'/'.$em.'em '.stripslashes($option["face"]).';color:'.$option["color"].';';
	}
}

// Output stylesheet and custom.css after custom styling
remove_action('wp_head', 'woothemes_wp_head');
add_action('woo_head', 'woothemes_wp_head');


/*-----------------------------------------------------------------------------------*/
/* Add layout to body_class output */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_layout_body_class')) {
	function woo_layout_body_class($classes) {
		
		global $woo_options;
		$layout = (!isset($woo_options['woo_site_layout'])) ? '' : $woo_options['woo_site_layout'];

		// Set main layout on post or page
		if ( is_singular() ) {
			global $post;
			$single = get_post_meta($post->ID, '_layout', true);
			if ( $single != "" ) 
				$layout = $single;
		}
		
		// Add layout to $woo_options array for use in theme
		$woo_options['woo_layout'] = $layout;
		
		// Add classes to body_class() output 
		$classes[] = $layout;
		return $classes;						
					
	}
}

/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>