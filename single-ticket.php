<?php 
	global $post;
	supportpress_members_only(); 
	if (!is_agent() && $post->post_author != get_current_user_id()) :
		wp_die(__('You do not have permission to view this ticket.', 'woothemes'));
	endif;
?>
<?php get_header(); ?>
<?php global $woo_options, $ticket_details; ?>

<section id="content" class="full-width">

	<div class="inner-content">
		           
		<?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; 
        
        	do_action('before_ticket', $post);
        	
        	$ticket_details = woo_supportpress_get_ticket_details( $post->ID );      	
        	?>
        
			<article <?php post_class(); ?>>
				
				<header>
					
					<?php if (is_user_logged_in()) : ?>
					<p class="actions">
						
						<?php if (current_user_can('edit_posts')) : ?>
							<a href="<?php echo add_query_arg('ticket_id', $post->ID, admin_url('post-new.php?post_type=knowledgebase')); ?>" class="button edit knowledgebase" title="<?php _e('Post to knowledge base', 'woothemes'); ?>"><?php _e('Add to Knowledgebase', 'woothemes'); ?></a>
							<?php if ($link = get_edit_post_link()) : ?><a href="<?php echo $link; ?>" class="button edit" title="<?php _e('Edit this ticket', 'woothemes'); ?>"><?php _e('Edit', 'woothemes'); ?></a><?php endif; ?>
						<?php endif; ?>
						
						<?php if (!is_watched()) : ?>
							<a href="<?php echo add_query_arg('watch', $post->ID, get_permalink($post->ID)); ?>" class="button watch" title="<?php _e('Watch this ticket', 'woothemes'); ?>"><?php _e('Watch', 'woothemes'); ?></a>
						<?php else : ?>
							<a href="<?php echo add_query_arg('unwatch', $post->ID, get_permalink($post->ID)); ?>" class="button watch" title="<?php _e('Unwatch this ticket', 'woothemes'); ?>"><?php _e('Unwatch', 'woothemes'); ?></a>
						<?php endif; ?>
					</p>
					<?php endif; ?>
					
					<h1 class="ticket-title"><?php the_title(); ?></h1>
				
				</header>
				
				<dl class="details">
					
					<dt class="odd"><?php _e('Opened:', 'woothemes'); ?></dt>
					<dd><?php echo woo_supportpress_human_time_diff($post->post_date); ?></dd>
					
					<dt class="even"><?php _e('Last updated:', 'woothemes'); ?></dt>
					<dd><?php echo woo_supportpress_human_time_diff($post->post_modified); ?></dd>
					
					<dt class="odd"><?php _e('Reported by:', 'woothemes'); ?></dt>
					<dd><?php if ($ticket_details['reported_by']) echo $ticket_details['reported_by']; elseif ($post->post_author>0) the_author_posts_link(); ?></dd>
					
					<dt class="even"><?php _e('Assigned to:', 'woothemes'); ?></dt>
					<dd><?php
						if (isset($ticket_details['assigned_to']->user_login)) echo '<a href="' . get_author_posts_url( $ticket_details['assigned_to']->ID, $ticket_details['assigned_to']->user_login ) . '">' . $ticket_details['assigned_to']->display_name . '</a>';
						else echo __('Anybody', 'woothemes');
					?> </dd>
					
					<dt class="odd"><?php _e('Priority:', 'woothemes'); ?></dt>
					<dd class="priority-<?php echo sanitize_title($ticket_details['priority']->name); ?>"><?php 
						if ($ticket_details['priority']) echo '<a href="' . get_term_link($ticket_details['priority']->slug, 'ticket_priority') . '">' . $ticket_details['priority']->name . '</a>'; 
					?> </dd>
					
					<dt class="even"><?php _e('Type:', 'woothemes'); ?></dt>
					<dd><?php 
						if ($ticket_details['type']) echo '<a href="' . get_term_link($ticket_details['type']->slug, 'ticket_type') . '">' . $ticket_details['type']->name . '</a>'; else _e('N/A', 'woothemes');
					?></dd>
					
					<dt class="odd"><?php _e('Tags:', 'woothemes'); ?></dt>
					<dd><?php
						if ($terms = wp_get_object_terms( $post->ID, 'ticket_tags' )) :
							$terms_array = array();
							foreach ($terms as $term) :
								$terms_array[] = '<a href="'.get_term_link($term->slug, 'ticket_tags').'">'.$term->name.'</a>';
							endforeach;
							echo implode(', ', $terms_array);
						else :
							echo __('No tags set.', 'woothemes');
						endif;
					?></dd>
					
					<dt class="even"><?php _e('Status:', 'woothemes'); ?></dt>
					<dd class="status-<?php echo sanitize_title($ticket_details['status']->name); ?>">
						<?php if ($ticket_details['status']) echo '<a href="' . get_term_link($ticket_details['status']->slug, 'ticket_status') . '">' . $ticket_details['status']->name . '</a>'; ?>
					</dd>
				
				</dl>
				
				<?php
				$args = array(
					'order'          => 'ASC',
					'post_type'      => 'attachment',
					'post_parent'    => $post->ID,
					'post_status'    => null,
					'numberposts'    => -1,
				);
				$attachments = get_posts($args);
				if ($attachments) :
					?>
					<section class="attachments">
						<h3><?php _e('Attachments', 'woothemes'); ?></h3>
						<ul>
						<?php
						foreach ($attachments as $attachment) :
							echo '<li><a href="'.wp_get_attachment_url($attachment->ID).'">'.apply_filters('the_title', $attachment->post_title).'</a>';
							
							if ($size = woo_supportpress_get_filesize( get_attached_file( $attachment->ID ) )) echo ' ('.$size.')';
							
							if ($attachment->post_author && !$ticket_details['reported_by']) :
								echo ' &ndash; '.__('added by', 'woothemes').' <a href="'.home_url('/author/').get_the_author_meta('user_nicename', $attachment->post_author ).'">'.get_the_author_meta('display_name', $attachment->post_author ).'</a> '.woo_supportpress_human_time_diff($attachment->post_date).'</li>';
							else :
								echo ' &ndash; '.__('added ', 'woothemes').woo_supportpress_human_time_diff($attachment->post_date).'</li>';
							endif;
						endforeach;
						?>
						</ul>
					</section>
					<?php
				endif;
				?>
				
				<?php if ($post->post_content) : ?>
					<h3><?php _e('Description', 'woothemes'); ?></h3>
                	<?php the_content(); ?>
                <?php endif; ?>
                                
            </article><!-- .post -->

            <?php comments_template('/single-ticket-comments.php', true); ?>
                                                
		<?php endwhile; else: ?>
			<article class="ticket">
            	<p><?php _e('Ticket does not exist!', 'woothemes') ?></p>
			</article><!-- .post -->             
       	<?php endif; ?>  
        
	</div><!--/inner-content-->

</section><!--/content-->  
		
<?php get_footer('fullwidth'); ?>