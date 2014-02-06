<?php
/*
Template Name: Staff
*/
?>
<?php get_header(); ?>
<?php global $woo_options, $wpdb; ?>
       
<section id="content">

	<div class="inner-content">
		
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
			<h1><?php the_title(); ?></h1>
			
			<?php the_content(); ?>
		
			<table class="items members">
			
				<thead>
					
					<th colspan="2"><?php _e('User', 'woothemes'); ?></th>
					<th><?php _e('Role', 'woothemes'); ?></th>
					<th><?php _e('Joined', 'woothemes'); ?></th>
					<th><?php _e('Last update', 'woothemes'); ?></th>
					<th class="tickets-resolved"><?php _e('Tickets resolved', 'woothemes'); ?></th>
					
				</thead>
				
				<tbody>
					<?php
					$users = woo_supportpress_get_support_staff();
					if ($users) :
						$alt = 1;
						foreach ($users as $user) : $alt = $alt * -1;
							?><tr class="<?php if ($alt==1) echo 'even'; else echo 'odd'; ?>">
								<td><?php echo get_avatar( $user->ID, '28' ); ?></td>
								<td class="username"><?php echo '<a href="' . get_author_posts_url( $user->ID, $user->user_nicename ) . '">' . $user->display_name . '</a>'; ?></td>
								<td class="role"><?php echo ( isset( $user->roles ) && is_array( $user->roles ) ) ? ucwords(str_replace('_', ' ', current($user->roles))) : ''; ?></td>
								<td class="joined"><?php echo human_time_diff(strtotime($user->user_registered)).__(' ago', 'woothemes'); ?></td>
								<td><?php if ($last_update = get_user_meta($user->ID, 'last_update', true)) echo human_time_diff($last_update).__(' ago', 'woothemes'); else _e('No updates yet', 'woothemes'); ?></td>
								<td class="tickets-resolved"><?php 
								
									echo $wpdb->get_var(sprintf("SELECT COUNT(wposts.ID) 
									FROM $wpdb->posts wposts
									LEFT JOIN $wpdb->postmeta wpostmeta ON wposts.ID = wpostmeta.post_id 
									LEFT JOIN $wpdb->term_relationships ON (wposts.ID = $wpdb->term_relationships.object_id)
									LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
									LEFT JOIN $wpdb->terms ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
									WHERE wposts.post_status = 'publish' 
									AND wposts.post_type = 'ticket'
									AND wpostmeta.meta_key = '_responsible'
									AND wpostmeta.meta_value = '%s'
									AND $wpdb->term_taxonomy.taxonomy = 'ticket_status'
									AND $wpdb->terms.slug = 'resolved'
									;", $wpdb->prepare( $user->ID ) ));
								
								?></td>
							</tr><?php
							
						endforeach;
						echo '</ul>';
					endif;
					?>
				</tbody>
			
			</table>
        
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Page does not exist!', 'woothemes') ?></p>
			</article><!-- .post -->             
       	<?php endif; ?>  
        
	</div><!--/inner-content-->

</section><!--/content-->
	
<?php get_sidebar(); ?>
	
<?php get_footer(); ?>