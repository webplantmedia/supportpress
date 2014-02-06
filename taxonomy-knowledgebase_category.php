<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">

	<div class="inner-content">
	
		<?php do_action('before_content'); ?>
		
		<?php
			$term_slug = get_query_var('knowledgebase_category');
			$term = get_term_by( 'slug', $term_slug, 'knowledgebase_category');
			$title = sprintf ( __('Category: %s', 'woothemes') , wptexturize($term->name));
		?>

		<h1 class="title"><?php echo $title; ?></h1>

		<?php
			get_template_part('loop', 'knowledgebase'); 
			
			do_action('after_knowledgebase_query');
		?>
		
	</div><!--/inner-content-->

</section><!--/content-->
		
<?php get_sidebar('knowledgebase'); ?>
		
<?php get_footer(); ?>