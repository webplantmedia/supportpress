<?php get_header(); ?>

<section id="content" class="full-width">
	
	<div class="inner-content">
	
		<div class="notice yellow"><span><p><strong><?php _e('At a glance...', 'woothemes'); ?></strong> <?php 
				
			$open_count = woo_supportpress_get_open_ticket_count();
			$assigned_count = woo_supportpress_get_open_user_tickets( get_current_user_id() );
			$unassigned_count = woo_supportpress_get_unassigned_tickets();
			
			echo sprintf(_n('There is <a href="%s">%s</a> ticket assigned to you', 'There are <a href="%s">%s</a> tickets assigned to you', $assigned_count, 'woothemes'), add_query_arg('assigned_to', get_current_user_id(), get_post_type_archive_link('ticket')), $assigned_count);
			
			echo sprintf(_n(', <a href="%s">%s</a> unassigned ticket and ', ', <a href="%s">%s</a> unassigned tickets and ', $unassigned_count, 'woothemes'), add_query_arg('assigned_to', '0', get_post_type_archive_link('ticket')), $unassigned_count);
			
			echo sprintf(_n('<a href="%s">%s</a> open ticket in total.', '<a href="%s">%s</a> open tickets in total.', $open_count, 'woothemes'), get_post_type_archive_link('ticket'), $open_count);
			
		?></p></span></div>
				
		
		<div class="col2-set">
		
			<div class="col-1">
			
				<h2><?php _e('Tickets', 'woothemes'); ?></h2>
				
				<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" class="knowledgebase-search">
					<div>
						<label for="Search" for="s"><span><?php _e('Search tickets&hellip;', 'woothemes'); ?></span><input type="text" value="<?php the_search_query(); ?>" class="input-text" name="s" id="s" placeholder="<?php _e('Search', 'woothemes'); ?>" /><input type="hidden" name="post_type" value="ticket" /></label>
					</div>
				</form>
				
				<div id="live_ticket_results"></div>
				<div id="ticket_results">
					<?php
					$args = array(
						'post_type'	=> 'ticket',
						'post_status' => 'publish',
						'posts_per_page' => 5,
						'orderby' => 'modified',
						'order' => 'desc',
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
					);
					query_posts( $args );
					
					if (have_posts()) : ?>
				
						<ul class="user-dashboard-tickets">
							
					        <?php while (have_posts()) : the_post(); 
						
								$ticket_details = woo_supportpress_get_ticket_details( $post->ID );
								
								$status = (isset($ticket_details['status']->name)) ? $ticket_details['status']->name : 'new';
								$type = (isset($ticket_details['type']->name)) ? $ticket_details['type']->name : 'problem';
								$priority = (isset($ticket_details['priority']->name)) ? $ticket_details['priority']->name : 'low';
								
								$last_comment = $wpdb->get_results("SELECT comment_date, comment_author, user_id FROM $wpdb->comments WHERE comment_post_ID = $post->ID ORDER BY comment_date DESC;");
								
								if (!$last_comment) $last_update = $post->post_date; else $last_update = $last_comment[0]->comment_date;
								if (!$last_comment) $last_user = $post->post_author; else $last_user = $last_comment[0]->user_id;
								
								$user_info = get_userdata($last_user);
								?>
								
								<li class="status-<?php echo sanitize_html_class($status); ?> priority-<?php echo sanitize_html_class($priority); ?>">
								
									<h5><a href="<?php the_permalink(); ?>"><span>#<?php the_ID(); ?></span> <?php the_title(); ?></a></h5>
									<ul class="meta">
										<li class="status"><mark><?php echo ucwords($status); ?></mark></li>
										<li class="type"><mark><?php echo ucwords($type); ?></mark></li>
										<li class="priority"><mark><?php echo ucwords($priority); ?></mark></li>
										<?php echo '<li class="updated">'.sprintf(__('Updated %s ago by <a href="%s">%s</a>.', 'woothemes'), '<time title="'.get_the_time('U').'">'.human_time_diff(strtotime($last_update), current_time('timestamp')).'</time>', get_author_posts_url($last_user), $user_info->display_name ).'</li>';  ?>
									</ul>
								
								</li>
										
							<?php endwhile; ?>
							
						</ul>
				
					<?php endif; wp_reset_query(); ?>
					
					<a class="button" href="<?php echo get_post_type_archive_link('ticket'); ?>"><?php _e('View more &rarr;', 'woothemes'); ?></a>
				
				</div>
				
				<script type="text/javascript">
					
					var xhr;
					
					jQuery(function(){
						
						jQuery('#s').keyup(function(){
							
							var s = encodeURI(jQuery(this).val());
							
							s = s.replace("#", "");
							
							if (xhr) xhr.abort();
							jQuery('#live_ticket_results').html('').removeClass('loading');
							jQuery('#ticket_results').hide();
							
							if (s.length > 1) {
								
								jQuery('#live_ticket_results').addClass('loading');
								
								var data = {
									action: 				'search_tickets',
									search: 				s,
									security: 				'<?php echo wp_create_nonce("ticket-search"); ?>'
								};
			
								xhr = jQuery.ajax({
									url: '<?php echo admin_url('admin-ajax.php'); ?>',
									data: data,
									type: 'POST',
									success: function(data) {
									
										jQuery('#ticket_results').hide();
										jQuery('#live_ticket_results').html(data).removeClass('loading');
										
									}
								});
							
							}
							if (s.length==0) {
								jQuery('#ticket_results').show();
							}
							
						});
						
					});
					
				</script>

			</div><!--/col-1-->
			
			<div class="col-2 my-tickets">
			
				<h2><?php _e('Tickets assigned to me', 'woothemes'); ?></h2>
				
				<?php
					
					global $wpdb;
					
					$args = array(
						'post_type'	=> 'ticket',
						'post_status' => 'publish',
						'posts_per_page' => 6,
						'meta_query' => array(
							array(
								'key' => '_responsible',
								'value' => get_current_user_id(),
								'compare' => '='
							)
						),
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
					);
					query_posts( $args );
					
					if (have_posts()) :
				?>
				<ul class="user-dashboard-tickets">
				
					<?php while (have_posts()) : the_post(); 
						
						$ticket_details = woo_supportpress_get_ticket_details( $post->ID );
						
						$status = (isset($ticket_details['status']->name)) ? $ticket_details['status']->name : 'new';
						$type = (isset($ticket_details['type']->name)) ? $ticket_details['type']->name : 'problem';
						$priority = (isset($ticket_details['priority']->name)) ? $ticket_details['priority']->name : 'low';
						
						$last_comment = $wpdb->get_results("SELECT comment_date, comment_author, user_id FROM $wpdb->comments WHERE comment_post_ID = $post->ID ORDER BY comment_date DESC;");
						
						if (!$last_comment) $last_update = $post->post_date; else $last_update = $last_comment[0]->comment_date;
						if (!$last_comment) $last_user = $post->post_author; else $last_user = $last_comment[0]->user_id;
						
						$user_info = get_userdata($last_user);
						?>
						
						<li class="status-<?php echo sanitize_html_class($status); ?> priority-<?php echo sanitize_html_class($priority); ?>">
						
							<h5><a href="<?php the_permalink(); ?>"><span>#<?php the_ID(); ?></span> <?php the_title(); ?></a></h5>
							<ul class="meta">
								<li class="status"><mark><?php echo ucwords($status); ?></mark></li>
								<li class="type"><mark><?php echo ucwords($type); ?></mark></li>
								<li class="priority"><mark><?php echo ucwords($priority); ?></mark></li>
								<?php echo '<li class="updated">'.sprintf(__('Updated %s ago by <a href="%s">%s</a>.', 'woothemes'), '<time title="'.get_the_time('U').'">'.human_time_diff(strtotime($last_update), current_time('timestamp')).'</time>', get_author_posts_url($last_user), $user_info->display_name ).'</li>';  ?>
							</ul>
						
						</li>
								
					<?php endwhile; ?>
				
				</ul>
				
				<a class="button" href="<?php echo add_query_arg('assigned_to', get_current_user_id(), get_post_type_archive_link('ticket')); ?>"><?php _e('View more &rarr;', 'woothemes'); ?></a>
				
				<?php else : ?>
				
				<p><?php _e('There are currently no open tickets assigned to you.', 'woothemes'); ?></p>
				
				<?php endif; wp_reset_query(); ?>
								
			</div><!--/col-2-->
		
		</div><!--/col2-set-->
	
	</div>

</section>

<?php get_footer('fullwidth'); ?>