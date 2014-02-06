<?php get_header(); ?>

<section id="content" class="full-width">

	<div class="inner-content">

		<div class="col2-set">
		
			<div class="col-1">
			
				<h2><?php _e('Knowledgebase', 'woothemes'); ?></h2>
				
				<p><?php _e('Please search the knowledgebase before adding a new ticket.', 'woothemes'); ?></p>
				
				<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" class="knowledgebase-search">
					<div>
						<label for="Search" for="s"><span><?php _e('Search knowledgebase&hellip;', 'woothemes'); ?></span><input type="text" value="<?php the_search_query(); ?>" class="input-text kb_search_input" name="s" id="s" placeholder="<?php _e('Search', 'woothemes'); ?>" /><input type="hidden" name="post_type" value="knowledgebase" /></label>
					</div>
				</form>
				
				<div id="live_knowledgebase_results"></div>
				<div id="knowledgebase_results">
					<?php
					$args = array(
						'post_type'	=> 'knowledgebase',
						'post_status' => 'publish',
						'posts_per_page' => 10,
					);
					query_posts( $args );
					
					if (have_posts()) : ?>
				
						<ul class="post-list">
							
					        <?php while (have_posts()) : the_post(); $votes_up = (int) get_post_meta($post->ID, 'votes_up', true); ?>
					                                                                    
					            <li class="kb-item">
					            
					            	<span class="likes tooltip" title="<?php echo sprintf(_n('%s person found this useful', '%s people found this useful', $votes_up, 'woothemes'), $votes_up); ?> "><?php echo $votes_up; ?></span>
					            	
					            	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					            	
					            	<small class="meta"><?php _e('Posted', 'woothemes'); ?> <?php echo woo_supportpress_human_time_diff($post->post_date); ?> <?php echo get_the_term_list( $post->ID, 'knowledgebase_category', __(' in ', 'woothemes'), ', ', '' ); ?></small>
					    
					            </li>
					                                                
					        <?php endwhile; ?>
							
						</ul>
				
					<?php endif; wp_reset_query(); ?>
					
					<a class="button" href="<?php echo get_post_type_archive_link('knowledgebase'); ?>"><?php _e('View more &rarr;', 'woothemes'); ?></a>
				
				</div>
				
				<script type="text/javascript">
					
					var xhr;
					
					jQuery(function(){
						
						jQuery('.kb_search_input').keyup(function(){
							
							var s = encodeURI(jQuery(this).val());
							
							jQuery('#knowledgebase_results').hide();
							
							s = s.replace("#", "");
							
							if (xhr) xhr.abort();
							jQuery('#live_knowledgebase_results').html('').removeClass('loading');
							
							if (s.length > 1) {
							
								jQuery('#live_knowledgebase_results').addClass('loading');
								
								var data = {
									action: 				'search_kb',
									only_show_if_found: 	0,
									search: 				s,
									security: 				'<?php echo wp_create_nonce("kb-search"); ?>'
								};
			
								xhr = jQuery.ajax({
									url: '<?php echo admin_url('admin-ajax.php'); ?>',
									data: data,
									type: 'POST',
									success: function(data) {
									
										jQuery('#live_knowledgebase_results').removeClass('loading');
										
										data = jQuery.trim( data );

										if (data.length > 0) {
											jQuery('#live_knowledgebase_results').html(data);
											jQuery('#live_knowledgebase_results .tooltip').tipsy({gravity: 's'});
										
										}
										
									}
								});
							
							} 
							if (s.length==0) {
								jQuery('#knowledgebase_results').show();
							}
							
						});
						
					});
					
				</script>

			</div><!--/col-1-->
			
			<div class="col-2 my-tickets">
			
				<h2><?php _e('My tickets', 'woothemes'); ?></h2>
				
				<?php
					
					global $wpdb;
					
					$args = array(
						'post_type'	=> 'ticket',
						'post_status' => 'publish',
						'author' => get_current_user_id(),
						'posts_per_page' => -1
					);
					query_posts( $args );
					
					if (have_posts()) :
				?>
				<ul class="user-dashboard-tickets">
				
					<?php while (have_posts()) : the_post(); 
						
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
						
							<h5><a href="<?php the_permalink(); ?>"><span>#<?php the_ID(); ?></span> <?php the_title(); ?></a></h5>
							<ul class="meta">
								<li class="status"><mark><?php echo ucwords($status); ?></mark></li>
								<li class="type"><mark><?php echo ucwords($type); ?></mark></li>
								<li class="priority"><mark><?php echo ucwords($priority); ?></mark></li>
								<?php if (isset($user_info->display_name) && $last_user) echo '<li class="updated">'.sprintf(__('Updated %s ago by <a href="%s">%s</a>.', 'woothemes'), '<time title="'.get_the_time('U').'">'.human_time_diff(strtotime($last_update), current_time('timestamp')).'</time>', get_author_posts_url($last_user), $user_info->display_name ).'</li>';  ?>
							</ul>
						
						</li>
								
					<?php endwhile; ?>
				
				</ul>
				
				<?php else : ?>
				
				<p><?php _e('You currently have no open tickets.', 'woothemes'); ?></p>
				
				<?php endif; wp_reset_query(); ?>
				
				<a href="<?php echo get_permalink(get_option('woo_supportpress_new_ticket_page_id')); ?>" class="button"><?php _e('Open a new ticket', 'woothemes'); ?></a>
								
			</div><!--/col-2-->
		
		</div><!--/col2-set-->

	</div><!--/inner-content-->

</section><!--/content-->

<?php get_footer('fullwidth'); ?>