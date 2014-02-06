<?php
/*
Template Name: Edit Profile
*/
?>
<?php supportpress_members_only();  ?>
<?php nocache_headers(); get_header(); global $woo_options, $posted; ?>
       
<section id="content" class="full-width">

	<div class="inner-content">
		
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        
        	<article <?php post_class(); ?>>
        	
				<h1><?php the_title(); ?></h1>
				
				<?php the_content(); ?>
		
				<?php do_action('edit_profile_form'); ?>
				
				<form class="edit-profile-form" method="post">
				
					
					
					<?php $user_info = get_userdata( get_current_user_id() ); ?>
					
					<?php wp_nonce_field('update_profile_' . $user_info->ID); ?>
				
					<h3><?php _e('About yourself', 'woothemes'); ?></h3>
				
					<p class="col-1">
						<label for="first_name"><?php _e('First Name', 'woothemes'); ?></label>
						<input name="first_name" id="first_name" placeholder="<?php _e('First Name', 'woothemes'); ?>" class="input-text" value="<?php echo $user_info->first_name ?>" />
					</p>
					
					<p class="col-2">
						<label for="last_name"><?php _e('Last Name', 'woothemes'); ?></label>
						<input name="last_name" id="last_name" placeholder="<?php _e('Last Name', 'woothemes'); ?>" class="input-text" value="<?php echo $user_info->last_name ?>" />
					</p>
					
					<div class="clear"></div>
					
					<p>
						<label for="nickname"><?php _e('Nickname/Display name', 'woothemes'); ?></label>
						<input name="nickname" id="nickname" placeholder="<?php _e('Nickname', 'woothemes'); ?>" class="input-text" value="<?php echo $user_info->nickname ?>" />
					</p>
					
					<div class="clear"></div>
					
					<p>
						<label for="description"><?php _e('Biographical info', 'woothemes'); ?></label>
						<textarea name="description" id="description" placeholder="Tell everyone about yourself :)" class="input-text"><?php echo $user_info->user_description; ?></textarea>
					</p>
					
					<h3><?php _e('Contact Info', 'woothemes'); ?></h3>
					
					<p class="col-1">
						<label for="email"><?php _e('Email (not shown publicly)', 'woothemes'); ?></label>
						<input name="email" id="email" placeholder="email@email.com" type="email" class="input-text" value="<?php echo $user_info->user_email ?>" />
					</p>
					
					<p class="col-2">
						<label for="url"><?php _e('Website', 'woothemes'); ?></label>
						<input name="url" id="url" placeholder="http://" type="url" class="input-text" value="<?php echo $user_info->user_url ?>" />
					</p>
					
					<p class="col-1">
						<label for="aim"><?php _e('AIM', 'woothemes'); ?></label>
						<input name="aim" id="aim" placeholder="<?php _e('AIM', 'woothemes'); ?>" class="input-text" value="<?php echo get_user_meta($user_info->ID, 'aim', true); ?>" />
					</p>
					
					<p class="col-2">
						<label for="yim"><?php _e('Yahoo IM', 'woothemes'); ?></label>
						<input name="yim" id="yim" placeholder="<?php _e('Yahoo IM', 'woothemes'); ?>" class="input-text" value="<?php echo get_user_meta($user_info->ID, 'yim', true); ?>" />
					</p>
					
					<p class="col-1">
						<label for="jabber"><?php _e('Jabber / Google Talk', 'woothemes'); ?></label>
						<input name="jabber" id="jabber" placeholder="<?php _e('Jabber / Google Talk', 'woothemes'); ?>" class="input-text" value="<?php echo get_user_meta($user_info->ID, 'jabber', true); ?>" />
					</p>
					
					<p class="col-2">
						<label for="twitter"><?php _e('Twitter'); ?></label>
						<input name="twitter" id="twitter" placeholder="<?php _e('Twitter username', 'woothemes'); ?>" class="input-text" value="<?php echo get_user_meta($user_info->ID, 'twitter', true); ?>" />
					</p>
					
					<div class="clear"></div>
					
					<h3><?php _e('Send me an email whenever&hellip;', 'woothemes'); ?></h3>
					
					<?php if (is_agent()) : ?>
					
					<p class="col-1 checkbox">
						<input name="new_ticket_notification" id="new_ticket_notification" class="checkbox" type="checkbox" <?php if (get_user_meta($user_info->ID, 'new_ticket_notification', true)=="yes") echo 'checked="checked"'; ?> />
						<label for="new_ticket_notification"><?php _e('Someone posts a new ticket', 'woothemes'); ?></label>
					</p>
					
					<p class="col-2 checkbox">
						<input name="new_message_notification" id="new_message_notification" class="checkbox" type="checkbox" <?php if (get_user_meta($user_info->ID, 'new_message_notification', true)=="yes") echo 'checked="checked"'; ?> />
						<label for="new_message_notification"><?php _e('Someone posts a new message', 'woothemes'); ?></label>
					</p>
					
					<?php endif; ?>
					
					<p class="col-1 checkbox">
						<input name="watched_item_notification" id="watched_item_notification" class="checkbox" type="checkbox" <?php if (get_user_meta($user_info->ID, 'watched_item_notification', true)!=="no") echo 'checked="checked"'; ?> />
						<label for="watched_item_notification"><?php _e('A watched item is updated', 'woothemes'); ?></label>
					</p>
					
					<div class="clear"></div>

					<h3><?php _e('Change password', 'woothemes'); ?></h3>
					
					<p class="new-password">
						<label for="newpassword"><?php _e('New Password', 'woothemes'); ?></label>
						<input name="pass1" id="newpassword" placeholder="New password" type="password" class="first input-text" autocomplete="off" />
						<input name="pass2" id="newpassword2" placeholder="Confirm" type="password" class="second input-text" autocomplete="off" />
						<small><?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).', 'woothemes'); ?></small>
		
						<small class="strength"><?php _e('Strength indicator', 'woothemes'); ?></small>
					</p>
					
					<p>
						<input type="hidden" name="action" value="update" />
						<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_info->ID; ?>" />
						<input type="hidden" name="user_login" id="user_login" value="<?php echo $user_info->user_login; ?>" />
						<input type="submit" value="<?php _e('Update Profile', 'woothemes'); ?>" class="button" />
					</p>
				
				</form><!--/.edit-profile-form-->
				
				<script type="text/javascript" src="<?php echo site_url(); ?>/wp-admin/js/password-strength-meter.js?ver=20081210"></script>
				<script type="text/javascript">
				// <![CDATA[
					jQuery(function(){
			
						function password_strength() {
				
							var pass = jQuery('#newpassword').val();
							var pass2 = jQuery('#newpassword2').val();
							var user = jQuery('#user_login').val();
				
							jQuery('.strength').removeClass('short bad good strong empty mismatch');
							if ( !pass ) {
								jQuery('#pass-strength-result').html( pwsL10n.empty );
								return;
							}
		
							var strength = passwordStrength(pass, user, pass2);
				
							if ( 2 == strength )
								jQuery('.strength').addClass('bad').html( pwsL10n.bad );
							else if ( 3 == strength )
								jQuery('.strength').addClass('good').html( pwsL10n.good );
							else if ( 4 == strength )
								jQuery('.strength').addClass('strong').html( pwsL10n.strong );
							else if ( 5 == strength )
								jQuery('.strength').addClass('short').html( pwsL10n.mismatch );
							else
								jQuery('.strength').addClass('short').html( pwsL10n.short );
				
						}
			
						jQuery('#newpassword, #newpassword2').val('').keyup( password_strength );
					});
			
					pwsL10n = {
						empty: "<?php _e('Strength indicator','woothemes') ?>",
						short: "<?php _e('Very weak','woothemes') ?>",
						bad: "<?php _e('Weak','woothemes') ?>",
						good: "<?php _e('Medium','woothemes') ?>",
						strong: "<?php _e('Strong','woothemes') ?>",
						mismatch: "<?php _e('Mismatch','woothemes') ?>"
					}
					try{convertEntities(pwsL10n);}catch(e){};
					
				// ]]>
				</script>
        
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Page does not exist!', 'woothemes') ?></p>
			</article><!-- .post -->             
       	<?php endif; ?>  
        
	</div><!--/inner-content-->

</section><!--/content-->
		
<?php get_footer('fullwidth'); ?>