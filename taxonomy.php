<?php supportpress_members_only(); ?>
<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">

	<div class="inner-content">
	
		<?php do_action('before_content'); ?>
		
		<?php
			$title = '';
			$term = '';
			$author = '';
			$sidebar = '';
			$bonus_query = array();
			
			// If user is not an agent, only show them their own tickets
			$client_query = array();
			if (!is_agent() && is_user_logged_in()) :
				$client_query = array(
					'author' => get_current_user_id()
				);
			endif;
			
			if (is_tax('ticket_status') ) :
				
				$term_slug = get_query_var('ticket_status');
				$term = get_term_by( 'slug', $term_slug, 'ticket_status');
				$title = sprintf ( __('Ticket Status: %s', 'woothemes') , wptexturize($term->name));
				
				if (isset($_GET['assigned_to']) && get_the_author_meta('ID', $_GET['assigned_to'])) :
					$author = (int) $_GET['assigned_to'];
					$title .= __(' &mdash; assigned to ', 'woothemes') . get_the_author_meta('display_name', $author);
					
					$bonus_query = array(
						'meta_key' => '_responsible',
						'meta_value' => $author
					);
				endif;
				
				$sidebar = 'ticket';
			
			elseif (is_tax('ticket_priority') ) :
				
				$term_slug = get_query_var('ticket_priority');
				$term = get_term_by( 'slug', $term_slug, 'ticket_priority');
				$title = sprintf ( __('Ticket Priority: %s', 'woothemes') , wptexturize($term->name));
				
				$sidebar = 'ticket';
				
			elseif (is_tax('ticket_type') ) :
				
				$term_slug = get_query_var('ticket_type');
				$term = get_term_by( 'slug', $term_slug, 'ticket_type');
				$title = sprintf ( __('Ticket Type: %s', 'woothemes') , wptexturize($term->name));
				
				$sidebar = 'ticket';
				
			elseif (is_tax('ticket_tags') ) :
				
				$term_slug = get_query_var('ticket_tags');
				$term = get_term_by( 'slug', $term_slug, 'ticket_tags');
				$title = sprintf ( __('Ticket Tag: %s', 'woothemes') , wptexturize($term->name));
				
				$sidebar = 'ticket';
				
			endif;
		?>

		<h1 class="title"><?php echo $title; ?></h1>

		<?php
			global $wp_query;
			
			$order_query = woo_supportpress_ticket_ordering();
			
			/* Load posts in term - this is so we can still order by status on this pages */
			$posts_to_show = get_objects_in_term( $term->term_id, $term->taxonomy );
			$posts_to_show[] = 0;
			
			/* Remove taxonomy arg */
			unset($wp_query->query['ticket_tags']);
			
			$args = array_merge(
				$wp_query->query,
				$bonus_query,
				$order_query,
				$client_query,
				array(
					'post_type'	=> 'ticket',
					'post_status' => 'publish',
					'post__in' => $posts_to_show
				)
			);
			query_posts( $args );
			get_template_part('loop', 'tickets'); 
			
			do_action('after_ticket_query');
		?>
		
	</div><!--/inner-content-->

</section><!--/content-->  

<?php 
	get_sidebar($sidebar); 
	get_footer();
?>