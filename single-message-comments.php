<?php
global $ticket_details;

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'single-ticket-comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) die ('Please do not load this page directly. Thanks!');

if ( post_password_required() ) : ?><p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'woothemes') ?></p><?php return; endif ?>

<?php $comments_by_type = &separate_comments($comments); ?>    

<!-- You can start editing here. -->

<div id="comments">

<?php if ( have_comments() ) : ?>
	
	<h2><?php _e('Replies', 'woothemes'); ?></h2>
	
	<ol class="commentlist">
		<?php wp_list_comments('avatar_size=48&callback=custom_comment&type=comment'); ?>
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
	
		<div class="clear"></div>

		<div id="respond">
		
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="ticket-update" enctype="multipart/form-data">
			
				<div class="cancel-comment-reply">
					<?php cancel_comment_reply_link(); ?>
				</div><!-- /.cancel-comment-reply -->
				
				<h3 class="ticket-update-heading"><?php _e('Reply to this message', 'woothemes'); ?></h3>
				
				<?php if ( !$user_ID ) : //If user is  not logged in ?>
			
					<p class="first">
						<label for="author"><?php _e('Name', 'woothemes') ?></label>
						<input type="text" name="author" class="input-text" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
					</p>
			
					<p class="last">
						<label for="email"><?php _e('Email', 'woothemes') ?></label>
						<input type="text" name="email" class="input-text" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
					</p>
					
					<div class="clear"></div>
					
				<?php endif; // End if logged in ?>
				
				<p><label for="comment"><?php _e('Add a comment', 'woothemes') ?></label><textarea name="comment" id="comment" class="input-text" rows="10" cols="50" tabindex="4"></textarea></p>
				
				<p>
					<label for="attachment"><?php _e('Attach a file', 'woothemes'); ?></label>
					<input type="file" name="attachment" id="attachment" />
				</p>
				
				<?php comment_id_fields(); ?>
				<?php do_action('update_form', $post->ID); ?>
				
				<p>
					<input type="submit" class="button cta" value="<?php _e('Add reply', 'woothemes') ?>" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
				</p>
		
			</form>
			
		</div>

	<?php endif; // If registration required ?>

	<div class="clear"></div>

<?php endif; // if you delete this the sky will fall on your head ?>