<?php supportpress_agents_only(); ?>
<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">	

	<div class="inner-content">
		
		<?php if (is_user_logged_in() && is_agent()) : ?>
		<p class="actions">
			<a href="<?php echo get_permalink(get_option('woo_supportpress_new_message_page_id')); ?>" class="button new" title="<?php _e('New Message', 'woothemes'); ?>"><?php _e('New Message', 'woothemes'); ?></a>
		</p>
		<?php endif; ?>
					
		<h1 class="title"><?php _e('Messages', 'woothemes'); ?></h1>
		
		<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" class="knowledgebase-search">
			<div>
				<label for="Search" for="s"><span><?php _e('Search', 'woothemes'); ?></span><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" class="input-text" placeholder="<?php _e('Search messages', 'woothemes'); ?>" /><input type="hidden" name="post_type" value="message" /></label>
			</div>
		</form>
		
		<?php
			global $wp_query;

			$args = array_merge( 
				array( 
					'orderby' => 'modified'
				),
				$wp_query->query
			);
			query_posts( $args );

			get_template_part('loop', 'messages'); 
		?>
	
		</div><!--/inner-content-->
	
	</section><!--/content-->  

<?php get_sidebar(); ?>

<?php get_footer(); ?>