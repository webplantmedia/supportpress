<?php
/*
Template Name: New Message
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
				
				<?php if (!is_user_logged_in()) : ?>
				
					<p class="note"><?php _e('You must be logged in to create a message.', 'woothemes'); ?></p>
				
				<?php else : ?>
				
					<?php the_content(); ?>
					
					<?php do_action('new_message_form'); ?>
					
					<form class="new-ticket-form" method="post" enctype="multipart/form-data">
					
						
	
						<p>
							<label for="message_title"><?php _e('Message Title', 'woothemes'); ?></label>
							<input name="message_title" id="message_title" value="<?php if (isset($posted['message_title'])) echo $posted['message_title']; ?>" placeholder="<?php _e('Enter a title for this message', 'woothemes'); ?>" class="input-text" />
						</p>
					
						<p>
							<label for="message_content"><?php _e('Message Content', 'woothemes'); ?></label>
							<textarea class="input-text" name="message_content" id="message_content" placeholder="<?php _e('Enter your message. HTML is allowed.', 'woothemes'); ?>" cols="20" rows="5"><?php if (isset($posted['message_content'])) echo $posted['message_content']; ?></textarea>
						</p>
						
						<p>
							<label for="attachment"><?php _e('Attach a file', 'woothemes'); ?></label>
							<input type="file" name="attachment" id="attachment" />
						</p>
						
						<p><input type="submit" class="button alt" value="<?php _e('Add Message', 'woothemes'); ?>" /></p>
						
					</form>	
				
				<?php endif; ?>		
				
			<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Message does not exist!', 'woothemes') ?></p>
			</article><!-- .post -->             
       	<?php endif; ?>  
        
		</div><!--/inner-content-->
	
	</section><!--/content-->  

<?php get_footer('fullwidth'); ?>