<?php get_header(); ?>
<?php global $woo_options; ?>
        	
<section id="content" class="full-width">	

	<div class="inner-content">
		           
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                
        <!-- Post Starts -->
            <article <?php post_class('blog-post'); ?>>
                        
            	<header class="header">
		
					<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
					<?php 
						echo '<a class="comments" href="' . get_comments_link() . '">';
						comments_number('No comments', '1 comment', '% comments');
						echo '</a>';
					 ?>			
					 <?php woo_post_meta(); ?>
				
				</header>
				
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>

            </article><!-- /.post -->
        
			<?php $comm = $woo_options['woo_comments']; if ( ($comm == "post" || $comm == "both") ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>
                                                
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </article><!-- .page -->              
       	<?php endif; ?>  

        <?php // get_sidebar(); ?>
        
	</div>
</section>
		
<?php get_footer('fullwidth'); ?>