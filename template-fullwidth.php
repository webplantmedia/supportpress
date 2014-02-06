<?php
/*
Template Name: Full Width
*/
?>
<?php get_header(); ?>
       
<section id="content" class="full-width">

	<div class="inner-content">
            
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                                                                    
            <article class="post">

			    <h1 class="title"><?php the_title(); ?></h1>
                
                <?php the_content(); ?>

				<?php edit_post_link( __('{ Edit }', 'woothemes'), '<small">', '</small>' ); ?>

            </article><!-- /.post -->
                                                
		<?php endwhile; else: ?>
			<article class="page">
        		<p><?php _e('Page does not exist!', 'woothemes') ?></p>
			</article><!-- .page -->  
        <?php endif; ?>  
    
	</div><!--/inner-content-->

</section><!--/content-->
	
<?php get_footer('fullwidth'); ?>