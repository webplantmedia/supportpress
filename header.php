<!doctype html>

<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8" />

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<!--  Mobile viewport scale | Disable user zooming as the layout is optimised -->
	<meta content="initial-scale=1.0; maximum-scale=1.0; user-scalable=no" name="viewport"/>

	<title><?php woo_title(); ?></title>
	<?php global $woo_options; woo_meta(); ?>

	<!-- Favicons -->
	<?php if(get_option( 'woo_custom_favicon') == '') : ?><link rel="icon" href="<?php bloginfo('template_url'); ?>/favicon.ico" /><?php endif; ?>
	<link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/apple-touch-icon.png" />

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />

	<!-- RSS -->
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( $woo_options['woo_feed_url'] ) { echo $woo_options['woo_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" />

	<!-- Ping Back -->
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
	<?php woo_head(); ?>

</head>

<body <?php body_class(get_option('woo_site_layout')); ?>>

<?php woo_top(); ?>

<div id="wrapper">

	<header id="header">

		<?php
		if ( get_option('woo_show_top_navigation')=='true' && function_exists('has_nav_menu') && has_nav_menu('top-menu') ) :

			echo '<nav class="top">';

			wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'theme_location' => 'top-menu' ) );

			echo '<div class="clear"></div></nav>';

		endif;
		?>

		<div class="intro">

			<div id="logo">

				<?php if( is_singular() && !is_front_page() ) $logo_tag = 'span'; else $logo_tag = 'h1'; ?>

				<?php if ($woo_options['woo_texttitle'] <> "true") : $logo = $woo_options['woo_logo']; ?>

					<<?php echo $logo_tag; ?> class="site-title"><a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>">
						<img src="<?php if ($logo) echo $logo; else { bloginfo('template_directory'); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo('name'); ?>" />
					</a></<?php echo $logo_tag; ?>>

				<?php else : ?>

					<<?php echo $logo_tag; ?> class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></<?php echo $logo_tag; ?>>
					<?php if ($woo_options['woo_tagline']=="true") : ?><span class="site-description"><?php bloginfo( 'description' ); ?></span><?php endif; ?>

				<?php endif; ?>

			</div><!-- /#logo -->

			<?php if (!is_user_logged_in()) : ?>
			<form class="login-form" id="supportpress_login" action="<?php echo home_url('/'); ?>" method="post">

				<p class="inputs">
					<label for="username"><?php _e('Username', 'woothemes'); ?></label>
					<input type="text" name="log" id="username" placeholder="<?php _e('Username', 'woothemes'); ?>" class="input-text username" />

					<label for="password"><?php _e('Password', 'woothemes'); ?></label>
					<input type="password" name="pwd" id="password" placeholder="<?php _e('Password', 'woothemes'); ?>" class="input-text password" />
					<input type="hidden" name="redirect_to" value="<?php
						echo strip_tags( home_url( '/' ) );
					?>" />
					<input type="hidden" name="supportpress_login" value="1" />
					<input type="hidden" name="login_nonce" value="<?php echo wp_create_nonce("supportpress-login-action"); ?>" />
					<input type="hidden" name="ajax_url" value="<?php echo admin_url('admin-ajax.php'); ?>" />
					<input type="submit" class="button" value="<?php _e('Go', 'woothemes'); ?>" />
				</p>
				<p class="actions"><small><a href="<?php echo wp_lostpassword_url( home_url() ); ?>" class="forgot-password"><?php _e('Forgot password?', 'woothemes'); ?></a><?php
					if ( get_option('users_can_register') ) echo ' | <a href="' . site_url('wp-login.php?action=register', 'login') . '">' . __('Register', 'woothemes') . '</a>';
				?></small></p>
			</form>

			<?php else : ?>

				<section class="logged-in">

					<div class="user-details">

						<a href="<?php $user_info = get_userdata( get_current_user_id() ); echo get_author_posts_url( $user_info->ID, $user_info->user_nicename ); ?>" class="tooltip profile-img" title="<?php echo $user_info->user_login; ?>"><?php echo get_avatar( get_current_user_id() ); ?></a>

						<ul>
							<?php
							global $wpdb;
							$items = $wpdb->get_col("SELECT DISTINCT item_id FROM ".$wpdb->prefix."supportpress_watching_tickets WHERE user_id = ".get_current_user_id().";");
							$open_tickets = get_objects_in_term( array(get_term_by('slug', NEW_STATUS_SLUG, 'ticket_status')->term_id, get_term_by('slug', OPEN_STATUS_SLUG, 'ticket_status')->term_id, get_term_by('slug', PENDING_STATUS_SLUG, 'ticket_status')->term_id), 'ticket_status' );
							$count = sizeof(array_intersect($items, $open_tickets));
							?>
							<li class="watch-link"><a href="<?php echo add_query_arg('watching', 'true', get_post_type_archive_link('ticket')); ?>" class="tooltip" title="<?php echo sprintf( __('Watching %s open tickets', 'woothemes'), $count); ?>"><mark><?php echo $count; ?></mark></a></li>

							<li class="edit"><a href="<?php echo get_permalink(get_option('woo_supportpress_profile_page_id')); ?>"><span><?php _e('Profile', 'woothemes'); ?></span></a></li>

							<li class="logout"><a href="<?php echo wp_logout_url( home_url() ); ?>"><span><?php _e('Logout', 'woothemes'); ?></span></a></li>

						</ul>

					</div>

				</section><!--/.logged-in-->

			<?php endif; ?>

		</div><!--/.intro-->

		<div class="clear"></div>

		<nav id="main-nav">

			<ul>
				<li class="first dashboard <?php if (is_front_page()) echo 'active'; ?>"><a href="<?php echo home_url(); ?>"><span><?php _e('Dashboard', 'woothemes'); ?></span></a></li>

				<?php
				$ticket_status = get_terms('ticket_status', 'orderby=description&hide_empty=0');

				if (is_agent()) : ?>

					<li class="parent tickets <?php if (is_post_type_archive('ticket')) echo 'active'; ?>"><a href="<?php echo get_post_type_archive_link('ticket'); ?>"><span><?php _e('Tickets', 'woothemes'); ?></span></a>
						<?php
							$loop = 0;
							if ($ticket_status) :
								echo '<ul>';
								foreach($ticket_status as $status) :
									$class = '';
									if ($loop==sizeof($ticket_status)) $class = 'last';
									echo '<li class="'.$class.'"><a href="'.get_term_link($status->slug, 'ticket_status').'"><span>'.$status->count.'</span> '.$status->name.'</a></li>';
									$loop++;
								endforeach;
								echo '</ul>';
							endif;
						?>
					</li>

					<li class="messages <?php if (is_post_type_archive('message')) echo 'active'; ?>"><a href="<?php echo get_post_type_archive_link('message'); ?>"><span><?php _e('Messages', 'woothemes'); ?></span></a></li>

				<?php elseif (is_user_logged_in()) : ?>

					<li class="parent tickets <?php if (is_post_type_archive('ticket')) echo 'active'; ?>"><a href="<?php echo get_post_type_archive_link('ticket'); ?>"><span><?php _e('My Tickets', 'woothemes'); ?></span></a> <ul>
							<li class="resolved"><a href="<?php echo get_term_link(RESOLVED_STATUS_SLUG, 'ticket_status'); ?>"><?php _e('Resolved', 'woothemes'); ?></a></li>
						</ul>
					</li>

				<?php endif; ?>

				<li class="knowledgebase <?php if (is_post_type_archive('knowledgebase')) echo 'active'; ?>"><a href="<?php echo get_post_type_archive_link('knowledgebase'); ?>"><span><?php _e('Knowledgebase', 'woothemes'); ?></span></a></li>

				<?php if (get_option('woo_supportpress_blog_page_id')>0) : ?>
				<li class="blog <?php if (is_page(get_option('woo_supportpress_blog_page_id'))) echo 'active'; ?>"><a href="<?php echo get_permalink(get_option('woo_supportpress_blog_page_id')) ?>"><span><?php _e('Blog', 'woothemes'); ?></span></a></li>
				<?php endif; ?>

				<?php if (is_user_logged_in()) : ?>

					<li class="new-ticket"><a href="<?php echo get_permalink(get_option('woo_supportpress_new_ticket_page_id')); ?>"><span><?php _e('New Ticket', 'woothemes'); ?></span></a></li>

				<?php endif; ?>
			</ul>

			<div class="clear"></div>

		</nav><!--/main-nav-->

		<div class="clear"></div>

	</header><!-- /#header -->

	<?php if (is_front_page() && get_option('woo_supportpress_blog_page_id')>0) : ?>

		<?php
			$args = array(
				'post_type'	=> 'post',
				'post_status' => 'publish',
				'posts_per_page' => 1,
				'post__in'  => get_option( 'sticky_posts' ),
				'ignore_sticky_posts' => 1
			);
			query_posts( $args );

			if (have_posts()) :
		?>
		<article class="latest-post">

			<?php while (have_posts()) : the_post(); ?>

				<aside class="meta"><?php _e('Posted on', 'woothemes'); ?> <time class="published" title="<?php the_time('c'); ?>"><?php the_time('F j, Y'); ?></time>. <a href="<?php the_permalink(); ?>#comments"><?php comments_number(__('0 Comments', 'woothemes'),__('1 Comment', 'woothemes'),__('% Comments', 'woothemes')); ?></a></aside>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

				<?php the_excerpt(); ?>

			<?php endwhile; ?>

		</article><!--/.blog-->

		<?php endif; wp_reset_query(); ?>

    </article>
    <?php endif; ?>