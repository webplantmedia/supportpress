<?php get_header(); ?>
<?php global $woo_options, $wp_query; ?>

<section id="content" class="full-width">

	<div class="inner-content">
	
		<article class="bio">
		
			<?php echo get_avatar( $wp_query->get_queried_object()->ID, '128' ); ?>
			
			<h1><?php echo wptexturize( $wp_query->get_queried_object()->display_name ); ?> <?php if ($url = $wp_query->get_queried_object()->user_url) echo ' &ndash; <a href="'.$url.'">'.$url.'</a>'; ?></a></h1> 
			
			<?php 
			if (isset($wp_query->get_queried_object()->description) && !empty($wp_query->get_queried_object()->description)) 
				echo wpautop( wptexturize( $wp_query->get_queried_object()->description )); 
			else 
				echo wpautop( wptexturize( sprintf( __('%s hasn\'t added a biography yet.', 'woothemes'), $wp_query->get_queried_object()->display_name ) ) );
			?>
			
			<?php
				$social = array();
				if ($twitter = get_user_meta( $wp_query->get_queried_object()->ID, 'twitter', true)) :
					$social[] = '<li class="twitter"><a href="http://twitter.com/'.$twitter.'">'.sprintf( __('Follow %s on Twitter', 'woothemes'), $wp_query->get_queried_object()->display_name ).'</a></li>';
				endif;
				if ($aim = get_user_meta( $wp_query->get_queried_object()->ID, 'aim', true)) :
					$social[] = '<li class="aim"><a href="aim:goim?screename='.$aim.'">'.sprintf( __('Chat on Aim (%s)', 'woothemes'), $aim).'</a></li>';
				endif;
				if ($gtalk = get_user_meta( $wp_query->get_queried_object()->ID, 'jabber', true)) :
					$social[] = '<li class="gtalk"><a href="gtalk:chat?jid='.$gtalk.'">'.sprintf( __('Chat on Jabber / Gtalk', 'woothemes'), $gtalk).'</a></li>';
				endif;
				if ($yahoo = get_user_meta( $wp_query->get_queried_object()->ID, 'yim', true)) :
					$social[] = '<li class="yahoo"><a href="http://edit.yahoo.com/config/send_webmesg?.target='.$yahoo.'&.src=pg">'.sprintf( __('Chat on Yahoo (%s)', 'woothemes'), $yahoo).'</a></li>';
				endif;
				
				if (sizeof($social)>0) :
					echo '<ul class="social">'.implode('', $social).'</ul>';
				endif;
			?>
		
		</article><!--/.bio-->
		
	</div><!--/inner-content-->

</section><!--/content-->
		
<?php get_footer(); ?>