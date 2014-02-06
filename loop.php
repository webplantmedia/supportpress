<?php if (have_posts()) : $alt = 1; while (have_posts()) : the_post(); $alt = $alt * -1; ?>

	<article class="blog-post <?php if ($alt==1) echo 'odd'; else echo 'even'; ?>">
				
		<header class="header">
		
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php 
				echo '<a class="comments" href="' . get_comments_link() . '">';
				comments_number('No comments', '1 comment', '% comments');
				echo '</a>';
			 ?>
			<?php woo_post_meta(); ?>
		
		</header>
		
		<section class="content">
		
			<?php global $more, $woo_options; $more = 0; ?>	                                        
            <?php if ( $woo_options[ 'woo_post_content' ] == "content" ) the_content(__( 'Read More...', 'woothemes' )); else the_excerpt(); ?>
		
		</section>
	
	</article>

<?php endwhile; else: ?>
    <article>
    	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
    </article>
<?php endif; ?>  

<?php woo_pagination(); ?>
<?php wp_reset_query(); ?> 