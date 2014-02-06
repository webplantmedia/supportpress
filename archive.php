<?php get_header(); ?>
<?php global $woo_options; ?>
        
<section id="content">

	<div class="inner-content">
	
	    <?php woo_content_before(); ?>
              
		<?php if (is_category()) { ?>
        	<h1 class="archive_header"><span class="fl cat"><?php _e('Archive:', 'woothemes'); ?> <?php echo single_cat_title(); ?></span> <span class="fr catrss"><?php $cat_obj = $wp_query->get_queried_object(); $cat_id = $cat_obj->cat_ID; echo '<a href="'; get_category_feed_link(true, $cat, ''); echo '" class="subscribe fr">'.__('Subscribe', 'woothemes').'</a>'; ?></span></h1>        
        
            <?php } elseif (is_day()) { ?>
            <h1 class="archive_header"><?php _e('Archive:', 'woothemes'); ?> <?php the_time( get_option( 'date_format' ) ); ?></h1>

            <?php } elseif (is_month()) { ?>
            <h1 class="archive_header"><?php _e('Archive:', 'woothemes'); ?> <?php the_time('F, Y'); ?></h1>

            <?php } elseif (is_year()) { ?>
            <h1 class="archive_header"><?php _e('Archive:', 'woothemes'); ?> <?php the_time('Y'); ?></h1>

            <?php } elseif (is_author()) { ?>
            <h1 class="archive_header"><?php _e('Archive by Author', 'woothemes'); ?></h1>

            <?php } elseif (is_tag()) { ?>
            <h1 class="archive_header"><?php _e('Tag Archives:', 'woothemes'); ?> <?php echo single_tag_title('', true); ?></h1>
            
        <?php } ?>
        
        <div class="clear"></div>

        <?php if ( get_query_var('paged') ) $paged = get_query_var('paged'); elseif ( get_query_var('page') ) $paged = get_query_var('page'); else $paged = 1; ?>
        <?php global $wp_query; query_posts( array_merge( $wp_query->query, array( 'post_type' => 'post', 'paged' => $paged )) ); ?>
        <?php get_template_part('loop');  ?>               

	</div><!--/inner-content-->

</section><!--/content-->
	
<?php get_sidebar(); ?>
	
<?php get_footer(); ?>