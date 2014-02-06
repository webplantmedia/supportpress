<?php supportpress_agents_only(); ?>
<?php get_header(); ?>
<?php global $woo_options; ?>
       
<section id="content" class="full-width">	

	<div class="inner-content">

        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                
        <!-- Post Starts -->
            <article <?php post_class('blog-post'); ?>>
                  
            	<header class="header">
					
					<?php if (is_user_logged_in()) : ?>
					<p class="actions">
						
						<?php if (current_user_can('edit_posts')) : ?>
							<?php if ($link = get_edit_post_link()) : ?><a href="<?php echo $link; ?>" class="button edit" title="<?php _e('Edit this message', 'woothemes'); ?>"><?php _e('Edit', 'woothemes'); ?></a><?php endif; ?>
						<?php endif; ?>
						
						<?php if (!is_watched()) : ?>
							<a href="<?php echo add_query_arg('watch', $post->ID, get_permalink($post->ID)); ?>" class="button watch" title="<?php _e('Watch this message', 'woothemes'); ?>"><?php _e('Watch', 'woothemes'); ?></a>
						<?php else : ?>
							<a href="<?php echo add_query_arg('unwatch', $post->ID, get_permalink($post->ID)); ?>" class="button watch" title="<?php _e('Unwatch this message', 'woothemes'); ?>"><?php _e('Unwatch', 'woothemes'); ?></a>
						<?php endif; ?>
					</p>
					<?php endif; ?>
					
					<h1 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
					<?php 
						echo '<a class="comments" href="' . get_comments_link() . '">';
						comments_number('No comments', '1 comment', '% comments');
						echo '</a>';
											
							
					 ?>			
					<p class="post-meta">Posted <time title="<?php echo get_the_time('U'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')).__(' ago', 'woothemes'); ?></time> by <cite><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author_meta('display_name'); ?></a></cite>.
					
					<?php
						global $wpdb;
						
						
									
						$last_comment_date = $wpdb->get_var("SELECT comment_date FROM $wpdb->comments WHERE comment_post_ID = $post->ID ORDER BY comment_date DESC;");
						if ($last_comment_date)
							echo _e('Updated ', 'woothemes').'<time title="'.get_the_time('U').'">'.human_time_diff(strtotime($last_comment_date), current_time('timestamp')).' ago</time>.'; 
		
					?>
					
					</p>
				
				</header>

				<?php
				$args = array(
					'order'          => 'ASC',
					'post_type'      => 'attachment',
					'post_parent'    => $post->ID,
					'post_status'    => null,
					'numberposts'    => -1,
				);
				$attachments = get_posts($args);
				if ($attachments) :
					?>
					<section class="attachments">
						<h3><?php _e('Attachments', 'woothemes'); ?></h3>
						<ul>
						<?php
						foreach ($attachments as $attachment) :
							echo '<li><a href="'.wp_get_attachment_url($attachment->ID).'">'.apply_filters('the_title', $attachment->post_title).'</a>';
							
							if ($size = woo_supportpress_get_filesize( get_attached_file( $attachment->ID ) )) echo ' ('.$size.')';
							
							if ($attachment->post_author) :
								echo ' &ndash; '.__('added by', 'woothemes').' <a href="'.home_url('/author/').get_the_author_meta('user_nicename', $attachment->post_author ).'">'.get_the_author_meta('display_name', $attachment->post_author ).'</a> '.woo_supportpress_human_time_diff($attachment->post_date).'</li>';
							else :
								echo ' &ndash; '.__('added ', 'woothemes').woo_supportpress_human_time_diff($attachment->post_date).'</li>';
							endif;
						endforeach;
						?>
						</ul>
					</section>
					<h3><?php _e('Message', 'woothemes'); ?></h3>
					<?php
				endif;
				?>
				
				<?php the_content(); ?>
				
				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>

    
            </article><!-- /.post -->
        
			<?php // woo_subscribe_connect(); ?>
        
            <?php comments_template('/single-message-comments.php', true); ?>
                                                
		<?php endwhile; else: ?>
			<article class="page">
            	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </article><!-- .page -->              
       	<?php endif; ?>  

        </div><!--/inner-content-->
	
	</section><!--/content-->  

<?php get_footer('fullwidth'); ?>