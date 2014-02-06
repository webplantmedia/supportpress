<?php supportpress_members_only(); ?>
<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">

	<div class="inner-content">
	
		<?php do_action('before_content'); ?>
	
		<?php
			global $wp_query;
			
			$title = __('Tickets', 'woothemes');
			
			$order_query = woo_supportpress_ticket_ordering();
			
			$assigned_to = woo_supportpress_ticket_assigned_to();
			
			$watching = woo_supportpress_watching_tickets();
			
			if (isset($_GET['assigned_to']) && get_the_author_meta('ID', $_GET['assigned_to'])) :
				$title .= __(' &mdash; assigned to ', 'woothemes') . get_the_author_meta('display_name', $_GET['assigned_to']);
			elseif (isset($_GET['assigned_to'])) :
				$title .= __(' &mdash; assigned to anyone', 'woothemes');
			endif;
			
			if (isset($_GET['watching'])) :
				$title .= __(' &mdash; open tickets i&rsquo;m watching', 'woothemes');
			endif;
			
			// If user is not an agent, only show them their own tickets
			$client_query = array();
			if (!is_agent()) :
				$client_query = array(
					'author' => get_current_user_id()
				);
			endif;
		?>

		<h1 class="title"><?php echo $title; ?></h1>
		
		<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" class="knowledgebase-search">
			<div>
				<label for="Search" for="s"><span><?php _e('Search', 'woothemes'); ?></span><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" class="input-text" placeholder="<?php _e('Search tickets', 'woothemes'); ?>" /><input type="hidden" name="post_type" value="ticket" /></label>
			</div>
		</form>
		
		<?php
			$args = array_merge( 
				$wp_query->query,
				$order_query,
				$assigned_to,
				$watching,
				$client_query,
				array (
					'tax_query' => array(
						array(
							'taxonomy' => 'ticket_status',
							'field' => 'slug',
							'terms' => array(
								OPEN_STATUS_SLUG,
								NEW_STATUS_SLUG,
								PENDING_STATUS_SLUG
							)
						)
					)
				)
			);
			query_posts( $args );

			get_template_part('loop', 'tickets'); 
			
			do_action('after_ticket_query');
		?>
	
	</div><!--/inner-content-->

</section><!--/content-->

<?php 
	get_sidebar('ticket'); 
	get_footer();
?>