<?php
global $ticket_details;

if (!is_user_logged_in()) return;

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'single-ticket-comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) : ?><p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'woothemes') ?></p><?php return; endif ?>

<?php $comments_by_type = separate_comments($comments); ?>

<!-- You can start editing here. -->

<div id="comments">

<?php if ( have_comments() ) : ?>

	<h3><?php _e('Comments and updates', 'woothemes'); ?></h3>

	<ol class="commentlist">
		<?php wp_list_comments('avatar_size=48&callback=ticket_update_comment&type=comment'); ?>
	</ol>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :  ?>
		<div class="navigation">
			<div class="fl"><?php previous_comments_link() ?></div>
			<div class="fr"><?php next_comments_link() ?></div>
			<div class="clear"></div>
		</div><!-- /.navigation -->
	<?php endif; ?>

<?php endif; ?>

</div> <!-- /#comments_wrap -->

<?php if ('open' == $post->comment_status) : ?>

	<?php if ( get_option('comment_registration') && !$user_ID ) : //If registration required & not logged in. ?>

		<p><?php _e('You must be', 'woothemes') ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>" rel="nofollow"><?php _e('logged in', 'woothemes') ?></a> <?php _e('to post a comment.', 'woothemes') ?></p>

	<?php else : //No registration required ?>

		<div id="respond">

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="ticket-update" enctype="multipart/form-data">

				<div class="cancel-comment-reply">
					<?php cancel_comment_reply_link(); ?>
				</div><!-- /.cancel-comment-reply -->

				<h3 class="ticket-update-heading"><?php _e('Update this ticket', 'woothemes'); ?></h3>

				<?php if ( is_agent() ) : ?>

					<ul class="updates">
						<li class="first">
							<label for="responsible"><?php _e('Who\'s responsible?', 'woothemes'); ?></label>
							<select name="responsible" id="responsible" autocomplete="off">
								<option value=""><?php _e('Anybody', 'woothemes'); ?></option>
								<?php
									$users = woo_supportpress_get_support_staff();
									if ($users) foreach ($users as $user) :
										if (isset($user->secret_agent) && $user->secret_agent==1) continue;
										echo '<option ';
										if (isset($ticket_details['assigned_to']) && $ticket_details['assigned_to']->ID==$user->ID) echo 'selected="selected" ';
										echo 'value="'.$user->ID.'">'.$user->display_name.'</option>';
									endforeach;
								?>
							</select>
						</li>
						<li class="last">
							<label for="priority"><?php _e('Priority', 'woothemes'); ?></label>
							<select id="priority" name="priority" autocomplete="off">
								<?php
								$terms = get_terms( 'ticket_priority', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['priority']) && $ticket_details['priority']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
						<li class="first">
							<label for="type"><?php _e('Type', 'woothemes'); ?></label>
							<select name="type" id="type" autocomplete="off">
								<option value=""><?php _e('N/A', 'woothemes'); ?></option>
								<?php
								$terms = get_terms( 'ticket_type', array( 'hide_empty' => '0', 'orderby' => 'name' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['type']) && $ticket_details['type'] && $ticket_details['type']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
						<li class="last">
							<label for="status"><?php _e('Status', 'woothemes'); ?></label>
							<select class="status" name="status" autocomplete="off">
								<?php
								$terms = get_terms( 'ticket_status', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['status']) && $ticket_details['status']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
					</ul>

					<p>
						<label for="tags"><?php _e('Add tags (comma separated)', 'woothemes'); ?></label>
						<input type="text" class="input-text" name="tags" id="tag-input" autocomplete="off" value="<?php if (isset($posted['tags'])) echo $posted['tags']; ?>" data-seperator="," />
					</p>

				<?php else : // Normal user ?>

					<ul class="updates col3-set">

						<li class="first col-1">
							<label for="type"><?php _e('Type', 'woothemes'); ?></label>
							<select name="type" id="type">
								<option value=""><?php _e('N/A', 'woothemes'); ?></option>
								<?php
								$terms = get_terms( 'ticket_type', array( 'hide_empty' => '0', 'orderby' => 'name' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['type']) && $ticket_details['type'] && $ticket_details['type']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
						<li class="col-2">
							<label for="priority"><?php _e('Priority', 'woothemes'); ?></label>
							<select id="priority" name="priority">
								<?php
								$terms = get_terms( 'ticket_priority', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['priority']) && $ticket_details['priority']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
						<li class="last col-3">
							<label for="status"><?php _e('Status', 'woothemes'); ?></label>
							<select class="status" name="status">
								<?php
								$terms = get_terms( 'ticket_status', array( 'hide_empty' => '0', 'orderby' => 'description' ) );
								if ($terms && sizeof($terms) > 0) {
									foreach ($terms as $term) {
										echo '<option ';
										if (isset($ticket_details['status']) && $ticket_details['status']->name==$term->name) echo 'selected="selected" ';
										echo 'value="'.$term->term_id.'">'.$term->name.'</option>';
									}
								}
								?>
							</select>
						</li>
					</ul>

				<?php endif; ?>

				<p><label for="comment"><?php _e('Add a comment', 'woothemes') ?></label><textarea name="comment" id="comment" class="input-text" rows="10" cols="50" tabindex="4"></textarea></p>

				<p>
					<label for="attachment"><?php _e('Attach a file', 'woothemes'); ?></label>
					<input type="file" name="attachment" id="attachment" />
				</p>

				<?php comment_id_fields(); ?>
				<?php do_action('update_form', $post->ID); ?>

				<p>
					<input type="submit" class="button cta" value="<?php _e('Update Ticket', 'woothemes') ?>" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
				</p>

				<?php do_action('comment_form');?>

			</form>

		</div>

	<?php endif; // If registration required ?>

	<div class="clear"></div>

<?php endif; // if you delete this the sky will fall on your head ?>