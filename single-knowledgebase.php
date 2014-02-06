<?php get_header(); ?>
<?php global $woo_options; ?>
       
		
<section id="content">	

	<div class="inner-content">
		           
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                
        <!-- Post Starts -->
            <article <?php post_class('blog-post'); ?>>
                        
            	<header class="header">
                
					<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
					<p class="post-meta"><?php _e('Posted', 'woothemes'); ?> <time title="<?php echo get_the_time('U'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')).__(' ago', 'woothemes'); ?></time> <?php echo get_the_term_list( $post->ID, 'knowledgebase_category', __(' in ', 'woothemes'), ', ', '' ); ?><?php echo get_the_term_list( $post->ID, 'knowledgebase_tags', __(' and tagged as ', 'woothemes'), ', ', '' ); ?>.</p>
				
				</header>
				
				
					<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
				
				
				<footer class="footer">
		
					<?php supportpress_show_votes(); ?>
				
				</footer> 
    
            </article><!-- /.post -->
                                                
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </article><!-- .page -->              
       	<?php endif; ?>  
        
	</div>
</section>

<?php get_sidebar('knowledgebase'); ?>
		
<?php get_footer(); ?>