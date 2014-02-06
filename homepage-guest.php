<?php
	$errors = new WP_Error();

	if (isset($_POST['register']) && $_POST['register'] && get_option('users_can_register')==1) :

		// Process signup form
		$posted = array(
			'username' 		=> $_POST['your_username'],
			'email' 		=> $_POST['your_email'],
			'password' 		=> $_POST['your_password'],
			'password_2' 	=> $_POST['your_password_2']
		);

		$posted = array_map('stripslashes', $posted);

		$posted['username'] = sanitize_user($posted['username']);

		// Terms
		if (get_option('woo_show_terms_on_signup')=='true' && get_option('woo_supportpress_terms_page_id')>0) :
			if (!isset($_POST['terms'])) :
				$errors->add('required-terms', __('You must read and accept our terms and conditions to continue.', 'woothemes') );
			endif;
		endif;

		// Validation
		if ( empty($posted['username']) ) $errors->add('required-username', __('Please enter a username.', 'woothemes') );
		if ( empty($posted['email']) ) $errors->add('required-email', __('Please enter your email address.', 'woothemes') );
		if ( empty($posted['password']) ) $errors->add('required-password', __('Please enter a password.', 'woothemes') );
		if ( empty($posted['password_2']) ) $errors->add('required-password_2', __('Please re-enter your password.', 'woothemes') );
		if ( $posted['password']!==$posted['password_2'] ) $errors->add('required-password', __('Passwords do not match.', 'woothemes') );

		// Check the username
		if ( !validate_username( $posted['username'] ) || strtolower($posted['username'])=='admin' ) :
			$errors->add('required-username', __('Invalid username.', 'woothemes') );
		elseif ( username_exists( $posted['username'] ) ) :
			$errors->add('required-username', __('An account is already registered with that username. Please choose another.', 'woothemes') );
		endif;

		// Check the e-mail address
		if ( !is_email( $posted['email'] ) ) :
			$errors->add('required-email', __('Invalid email address.', 'woothemes') );
		elseif ( email_exists( $posted['email'] ) ) :
			$errors->add('required-email', __('An account is already registered with your email address. Please login.', 'woothemes') );
		endif;

		if ( !$errors->get_error_code() ) :

			do_action('register_post', $posted['username'], $posted['email'], $errors);
			$errors = apply_filters( 'registration_errors', $errors, $posted['username'], $posted['email'] );

            // if there are no errors, let's create the user account
			if ( !$errors->get_error_code() ) :

	            $user_id = wp_create_user( $posted['username'], $posted['password'], $posted['email'] );
	            if ( !$user_id ) :

	            	$errors->add('error', sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'woothemes'), get_option('admin_email')) );

	            else :

		            // Change role
		            wp_update_user( array ('ID' => $user_id, 'role' => 'subscriber') ) ;

		            // send the user a confirmation and their login details
		            wp_new_user_notification( $user_id, $posted['password'] );

		            // set the WP login cookie
		            $secure_cookie = is_ssl() ? true : false;
		            wp_set_auth_cookie($user_id, true, $secure_cookie);

		            // Redirect home
		            wp_safe_redirect(home_url());
		            exit;

	            endif;

			endif;

		endif;

	endif;
?>

<?php get_header(); ?>

<section id="content" class="full-width">

	<div class="inner-content">

	    <?php
	    	// Errors and notices
	    	if ( $errors->get_error_code() ) :

	    		echo '<div class="notice red user"><span><strong><p>'.__('Sign-up error', 'woothemes').'</strong>'.wptexturize(str_replace('<strong>ERROR</strong>: ', '', $errors->get_error_message())).'</p></span></div>';

	    	else :

	    		if ($notice = get_option('woo_guest_homepage_notice')) : ?><div class="notice yellow"><span><?php echo wpautop(wptexturize(stripslashes($notice))); ?></span></div><?php endif;

	    	endif;
	   	?>

	    <div class="col2-set">

	    	<div <?php if (get_option('woo_homepage_signup')=='true' && get_option('users_can_register')==1) : ?>class="col-1"<?php endif; ?>>

	    		<h2><?php _e('Knowledgebase', 'woothemes'); ?></h2>

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
						'posts_per_page' => 5,
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

										if ( data.length > 0 ) {
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

	    	</div>

	    	<?php if (get_option('woo_homepage_signup')=='true' && get_option('users_can_register')==1) : ?>
	    	<section class="col-2">

	    		<h2><?php _e('Sign up', 'woothemes'); ?></h2>

	    		<form method="post" class="signup_form">

	                <p>
	                    <label for="your_username"><?php _e('Username', 'woothemes'); ?></label>
	                    <input type="text" class="input-text" placeholder="<?php _e('Enter a username', 'woothemes'); ?>" tabindex="1" name="your_username" id="your_username" value="<?php if (isset($posted['username'])) echo esc_attr( $posted['username'] ); ?>" />
	                </p>

	                <p>
	                    <label for="your_email"><?php _e('Email', 'woothemes'); ?></label>
	                    <input type="text" class="input-text" placeholder="<?php _e('Your email address', 'woothemes'); ?>" tabindex="2" name="your_email" id="your_email" value="<?php if (isset($posted['email'])) echo esc_attr( $posted['email'] ); ?>" />
	                </p>

					<div class="col2-set">
						<p class="col-1">
		                    <label for="your_password"><?php _e('Password', 'woothemes'); ?></label>
		                    <input type="password" class="input-text" placeholder="<?php _e('Enter a password', 'woothemes'); ?>" tabindex="3" name="your_password" id="your_password" value="" />
		                </p>

		                <p class="col-2">
		                    <label for="your_password_2"><?php _e('Re-enter password', 'woothemes'); ?></label>
		                    <input type="password" class="input-text" placeholder="<?php _e('Re-enter password', 'woothemes'); ?>" tabindex="4" name="your_password_2" id="your_password_2" value="" />
		                </p>
	                </div>

	                <?php do_action('supportpress_signup_form'); ?>

	                <?php do_action('register_form'); ?>

					<?php if (get_option('woo_show_terms_on_signup')=='true' && get_option('woo_supportpress_terms_page_id')>0) : ?><p>
						<input type="checkbox" name="terms" tabindex="5" value="yes" id="terms" <?php if (isset($_POST['terms'])) echo 'checked="checked"'; ?> /> <label for="terms" class="terms"><?php echo sprintf(__('I accept the <a href="%s" target="_blank">terms &amp; conditions</a>.', 'woothemes'), get_permalink(get_option('woo_supportpress_terms_page_id'))); ?></label>
					</p><?php endif; ?>

					<p><input type="submit" class="button" tabindex="6" name="register" value="<?php _e('Create Account &rarr;', 'woothemes'); ?>" /></p>

				</form>

	    	</section>
	    	<?php endif; ?>

	    </div>

	</div><!-- /inner-content -->

</section><!--/#content-->

<?php get_footer('fullwidth'); ?>