<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">

	<div class="inner-content">
	
		<?php do_action('before_content'); ?>

		<h1 class="title"><?php echo __('Knowledgebase', 'woothemes'); ?> <a href="<?php echo add_query_arg('post_type', 'knowledgebase', get_bloginfo('rss2_url')); ?>" class="subscribe"><?php _e('Subscribe', 'woothemes'); ?></a></h1>
		
		<form role="search" method="get" id="searchform" action="<?php echo home_url(); ?>" class="knowledgebase-search">
			<div>
				<label for="Search" for="s"><span><?php _e('Search', 'woothemes'); ?></span><input type="text" value="<?php the_search_query(); ?>" name="s" id="s" class="input-text" placeholder="<?php _e('Search knowledgebase', 'woothemes'); ?>" /><input type="hidden" name="post_type" value="knowledgebase" /><input type="submit" id="searchsubmit" value="Search"></label>
			</div>
		</form>
		
		<?php get_template_part('loop', 'knowledgebase'); ?>   
	
	</div><!--/inner-content-->

</section><!--/content-->
		
<?php get_sidebar('knowledgebase'); ?>
		
<?php get_footer(); ?>