<?php
/*
Template Name: Blog
*/
?>
<?php get_header(); ?>
<?php global $woo_options; ?>

<section id="content">

	<div class="inner-content">
	
	    <?php woo_content_before(); ?>
	    
	    <h1 class="title"><?php the_title(); ?> <span class="fr"><?php echo '<a href="'.get_bloginfo('rss2_url').'" class="subscribe fr">'.__('Subscribe', 'woothemes').'</a>'; ?></span></h1>
              
		<?php if ( get_query_var('paged') ) $paged = get_query_var('paged'); elseif ( get_query_var('page') ) $paged = get_query_var('page'); else $paged = 1; ?>
        <?php query_posts("post_type=post&paged=$paged"); ?>
        <?php get_template_part('loop');  ?>            

	</div><!--/inner-content-->

</section><!--/content-->  
	
<?php get_sidebar(); ?>
	
<?php get_footer(); ?>