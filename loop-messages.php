<?php if (have_posts()) : $alt = 1; while (have_posts()) : the_post(); $alt = $alt * -1; ?>

	<article class="message <?php if ($alt==1) echo 'odd'; else echo 'even'; ?>">
				
		<header class="header">
		
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php 
				echo '<a class="comments" href="' . get_comments_link() . '">';
				comments_number('No comments', '1 comment', '% comments');
				echo '</a>';	
			 ?>			
			<p class="post-meta"><?php _e('Posted', 'woothemes'); ?> <time title="<?php echo get_the_time('U'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')).__(' ago', 'woothemes'); ?></time> <?php _e('by', 'woothemes'); ?> <cite><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author_meta('display_name'); ?></a></cite>.
			
			<?php
				global $wpdb;
				
				
							
				$last_comment_date = $wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_post_ID = $post->ID ORDER BY comment_date DESC;");
				if ($last_comment_date)
					echo _e('Last updated ', 'woothemes').'<time title="'.get_the_time('U').'">'.human_time_diff(strtotime($last_comment_date), current_time('timestamp')).' ago</time>.'; 

			?>
			
			</p>
		
		</header>
		
		<section class="content">
		
			<?php the_excerpt(); ?>
		
		</section>
		
	</article>

<?php endwhile; endif; ?>

<?php woo_pagination(); ?>
<?php wp_reset_query(); ?> 