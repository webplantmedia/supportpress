<?php
/*
Template Name: New Ticket
*/
?>
<?php supportpress_members_only(); ?>
<?php get_header(); ?>
<?php global $woo_options, $posted; ?>
       
	<section id="content" class="full-width">
	
		<div class="inner-content">
	
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        
	        	<article <?php post_class(); ?>>
	        	
					<h1><?php the_title(); ?></h1>
					
					<?php the_content(); ?>
					
					<?php do_action('new_ticket_form'); ?>
				
					<form class="new-ticket-form" method="post" enctype="multipart/form-data">
						
						<p>
							<label for="title"><?php _e('Ticket Title', 'woothemes'); ?></label>
							<input name="title" id="title" value="<?php if (isset($posted['title'])) echo $posted['title']; ?>" placeholder="<?php _e('Enter a one line summary for the ticket', 'woothemes'); ?>" class="input-text kb_search_input" />
						</p>
						
							<div id="live_kb_results"></div>
							<script type="text/javascript">
								
								var xhr;
								
								jQuery(function(){
									
									jQuery('.kb_search_input').keyup(function(){
										
										var s = encodeURI(jQuery(this).val());
										
										s = s.replace("#", "");
										
										if (xhr) xhr.abort();
										jQuery('#live_kb_results').hide().html('').removeClass('loading');
										
										if (s.length > 1) {
											
											jQuery('.kb_search_input').addClass('loading');
											
											var data = {
												action: 				'search_kb',
												only_show_if_found: 	1,
												search: 				s,
												security: 				'<?php echo wp_create_nonce("kb-search"); ?>'
											};
						
											xhr = jQuery.ajax({
												url: '<?php echo admin_url('admin-ajax.php'); ?>',
												data: data,
												type: 'POST',
												success: function(data) {
												
													jQuery('.kb_search_input').removeClass('loading');
													
													data = jQuery.trim( data );
													
													if (data.length > 0) {
														jQuery('#live_kb_results').show().html('<h2><?php _e('Knowledgebase articles that may be useful&hellip;', 'woothemes'); ?></h2>' + data);
													} else {
														jQuery('#live_kb_results').hide();
													}
													
												}
											});
										
										} else {
											jQuery('#live_kb_results').hide();
										}
										
									});
									
								});
								
							</script>

						<ul class="updates">
							
							<?php if (is_agent()) : ?>
							
								<li class="first">
									<label for="responsible"><?php _e('Who\'s responsible?', 'woothemes'); ?></label>
									<select name="responsible" id="responsible">
										<option value=""><?php _e('Anybody', 'woothemes'); ?></option>
										<?php
											$users = woo_supportpress_get_support_staff();
											if ($users) foreach ($users as $user) :
												if (isset($user->secret_agent) && $user->secret_agent==1) continue;
												echo '<option ';
												if (isset($posted['assigned_to'])) : selected($posted['assigned_to'], $user->ID); endif;
												echo 'value="'.$user->ID.'">'.$user->display_name.'</option>';
											endforeach;
										?>
									</select>
								</li>
								<li class="last">
									<label for="priority"><?php _e('Priority', 'woothemes'); ?></label>
									<select id="priority" name="priority">
										<?php
										$terms = get_terms( 'ticket_priority', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
										if ($terms && sizeof($terms) > 0) {
											foreach ($terms as $term) {
												?>
												<option value="<?php echo $term->term_id; ?>" <?php if (isset($posted['priority']) && $posted['priority']==$term->name) echo 'selected="selected"'; ?>><?php echo $term->name; ?></option>
												<?php
											}
										}
										?>
									</select>
								</li>
								<li class="first">
									<label for="ticket_type"><?php _e('Type', 'woothemes'); ?></label>
									<select id="ticket_type" name="ticket_type">
										<?php
										$terms = get_terms( 'ticket_type', array( 'hide_empty' => '0', 'orderby' => 'title' ) );
										if ($terms && sizeof($terms) > 0) {
											foreach ($terms as $term) {
												echo '<option ';
												if (isset($posted['ticket_type']) && $posted['ticket_type']==$term->term_id) echo 'selected="selected" '; 
												echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
											}
										}
										?>
									</select>
								</li>
								<li class="last">
									<label for="status"><?php _e('Status', 'woothemes'); ?></label>
									<select class="status" name="status">
										<?php
										$terms = get_terms( 'ticket_status', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
										if ($terms && sizeof($terms) > 0) {
											foreach ($terms as $term) {
												echo '<option ';
												if (isset($posted['status']) && $posted['status']==$term->name) echo 'selected="selected" '; 
												echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
											}
										}
										?>
									</select>
								</li>
								<li class="first">
									<label for="ticket_owner"><?php _e('Ticket Owner', 'woothemes'); ?></label>
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
									<select id="ticket_owner" name="ticket_owner">
										<?php if (sizeof($agents_array) > 0) : ?>
										<optgroup label="<?php _e('Agents', 'woothemes'); ?>">
											<?php foreach ($agents_array as $id => $name) : ?>
											
												<option value="<?php echo $id; ?>" <?php if ((isset($posted['ticket_owner']->ID) && $posted['ticket_owner']->ID==$id) || $id==get_current_user_id()) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
												
											<?php endforeach; ?>
										</optgroup>
										<?php endif; ?>
										<?php if (sizeof($users_array) > 0) : ?>
										<optgroup label="<?php _e('Clients', 'woothemes'); ?>">
											<?php foreach ($users_array as $id => $name) : ?>
											
												<option value="<?php echo $id; ?>" <?php if ((isset($posted['ticket_owner']->ID) && $posted['ticket_owner']->ID==$id) || $id==get_current_user_id()) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
												
											<?php endforeach; ?>
										</optgroup>
										<?php endif; ?>
									</select>
								</li>
							
							<?php else : ?>
							
								<li class="first">
									<label for="ticket_type"><?php _e('Type', 'woothemes'); ?></label>
									<select id="ticket_type" name="ticket_type">
										<?php
										$terms = get_terms( 'ticket_type', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
										if ($terms && sizeof($terms) > 0) {
											foreach ($terms as $term) {
												echo '<option ';
												if (isset($posted['ticket_type']) && $posted['ticket_type']==$term->term_id) echo 'selected="selected" '; 
												echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
											}
										}
										?>
									</select>
								</li>
								<li class="last">
									<label for="priority"><?php _e('Priority', 'woothemes'); ?></label>
									<select id="priority" name="priority">
										<?php
										$terms = get_terms( 'ticket_priority', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
										if ($terms && sizeof($terms) > 0) {
											foreach ($terms as $term) {
												?>
												<option value="<?php echo $term->term_id; ?>" <?php if (isset($posted['priority']) && $posted['priority']==$term->name) echo 'selected="selected"'; ?>><?php echo $term->name; ?></option>
												<?php
											}
										}
										?>
									</select>
								</li>
							
							<?php endif; ?>
							
						</ul>
						
						<?php if (is_agent()) : ?>
						<p>
							<label for="tags"><?php _e('Add tags (comma separated)', 'woothemes'); ?></label>
							<input type="text" class="input-text" name="tags" id="tag-input" value="<?php if (isset($posted['tags'])) echo $posted['tags']; ?>" data-seperator="," /> 
						</p>
						<?php endif; ?>
					
						<p>
							<label for="comment"><?php _e('Describe the problem', 'woothemes'); ?></label>
							<textarea class="input-text" name="comment" id="comment" placeholder="<?php _e('Describe the problem, including details such as how to reproduce it and what version you are using.', 'woothemes'); ?>" cols="20" rows="5"><?php if (isset($posted['comment'])) echo $posted['comment']; ?></textarea>
						</p>
	
						<p>
							<label for="attachment"><?php _e('Attach a file', 'woothemes'); ?></label>
							<input type="file" name="attachment" id="attachment" />
						</p>
						
						<p><input type="submit" class="button alt" value="<?php _e('Add ticket', 'woothemes'); ?>" /></p>
					
					</form>				
					
				<?php endwhile; else: ?>
				<article class="page">
	            	<p><?php _e('Page does not exist!', 'woothemes') ?></p>
				</article><!-- .page -->             
	       	<?php endif; ?> 
	
		</div><!--/inner-content-->
	
	</section><!--/content-->  

<?php get_footer('fullwidth'); ?>