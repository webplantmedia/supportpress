<ul class="post-list">

<?php if (have_posts()) : while (have_posts()) : the_post(); $votes_up = (int) get_post_meta($post->ID, 'votes_up', true); ?>
                                                            
    <li class="kb-item">
    
    	<span class="likes tooltip" title="<?php echo sprintf(_n('%s person found this useful', '%s people found this useful', $votes_up, 'woothemes'), $votes_up); ?> "><?php echo $votes_up; ?></span>
    	
    	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
    	
    	<small class="meta"><?php _e('Posted', 'woothemes'); ?> <time title="<?php echo get_the_time('U'); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')).__(' ago', 'woothemes'); ?></time> <?php echo get_the_term_list( $post->ID, 'knowledgebase_category', __(' in ', 'woothemes'), ', ', '' ); ?></small>

    </li>
                                        
<?php endwhile; else : echo '<li>'._e('No knowledgebase articles were found.', 'woothemes').'</li>'; endif; ?>  

</ul>

<?php woo_pagination(); ?>
<?php wp_reset_query(); ?> 