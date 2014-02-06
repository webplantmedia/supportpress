<?php 

get_header();

global $woo_options, $wp_query;

if (isset($_GET['post_type'])) $post_type = $_GET['post_type']; else $post_type = 'ticket';

if ($post_type=='ticket') supportpress_members_only();
if ($post_type=='message') supportpress_agents_only();

?>
		
<section id="content">

	<div class="inner-content">
	
		<?php do_action('before_content'); ?>

		<h1 class="title"><?php _e('Search results:', 'woothemes') ?> <?php printf(the_search_query());?></h1>
		
		<?php
			switch ($post_type) :
				
				case "ticket" :
					
					$order_query = woo_supportpress_ticket_ordering();
					
					// If user is not an agent, only show them their own tickets
					$client_query = array();
					if (!is_agent()) :
						$client_query = array(
							'author' => get_current_user_id()
						);
					endif;
			
					$args = array_merge( 
						$wp_query->query,
						$client_query,
						$order_query,
						array(
							'post_type' => $post_type
						)
					);
					query_posts( $args );
					get_template_part('loop', 'tickets'); 
					do_action('after_ticket_query');
					
				break;
				case "knowledgebase" :
					
					$args = array_merge( 
						$wp_query->query,
						array(
							'post_type' => $post_type
						)
					);
					query_posts( $args );
					get_template_part('loop', 'knowledgebase'); 
					
				break;
				default :
					
					$args = array_merge( 
						$wp_query->query,
						array(
							'post_type' => $post_type
						)
					);
					query_posts( $args );
					get_template_part('loop'); 
					
				break;
				
			endswitch;
		?>
	
	</div><!--/inner-content-->

</section><!--/content-->
	
<?php 
	get_sidebar($post_type); 
	get_footer();
?>
