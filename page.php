<?php get_header(); ?>
<?php global $woo_options; ?>
       
<section id="content">

	<div class="inner-content">

        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                                                                    
            <article class="page">

			    <h1 class="title"><?php the_title(); ?></h1>

                <div class="entry">
                	<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	</div><!-- /.entry -->

				<?php edit_post_link( __('{ Edit }', 'woothemes'), '<small>', '</small>' ); ?>
                
            </article><!-- /.page -->
            
            <?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>
                                                
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Page does not exist!', 'woothemes') ?></p>
			</article><!-- .page -->  
        <?php endif; ?>  
        
	</div><!--/inner-content-->

</section><!--/content-->
	
<?php get_sidebar(); ?>
	
<?php get_footer(); ?>