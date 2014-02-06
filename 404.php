<?php get_header(); ?>
  
<section id="content" class="full-width <?php if ($wp_query->query_vars['post_type']!=='message' && $wp_query->query_vars['name']!=='tickets' && $wp_query->query_vars['name']!=='knowledgebase') echo 'error404content'; ?>">

	<div class="inner-content">
                                                                                
        <article class="post">
        
        	<?php
        		if ($wp_query->query_vars['post_type']=='message') :
        		
        			?>
        			<h1 class="title"><?php _e('Messages', 'woothemes') ?></h1>
            		<p><?php echo sprintf(__('There are currently no messages. To create your first message, <a href="%s">click here</a>.', 'woothemes'), get_permalink(get_option('woo_supportpress_new_message_page_id'))); ?></p>
            		<?php
            	
            	elseif ($wp_query->query_vars['name']=='tickets') :
        		
        			?>
        			<h1 class="title"><?php _e('Tickets', 'woothemes') ?></h1>
            		<p><?php echo sprintf(__('There are currently no tickets. To create your first ticket, <a href="%s">click here</a>.', 'woothemes'), get_permalink(get_option('woo_supportpress_new_ticket_page_id'))); ?></p>
            		<?php

        		elseif ($wp_query->query_vars['name']=='knowledgebase') :
        		
        			?>
        			<h1 class="title"><?php _e('Knowledgebase', 'woothemes') ?></h1>
            		<p><?php echo sprintf(__('There are currently no knowledgebase articles. When one is added it will appear in this section.', 'woothemes')); ?></p>
            		<?php

        		else :
        			
        			?>
        			<h1 class="title"><?php _e('Uh oh - you broke it!', 'woothemes') ?></h1>
            		<p><?php _e('The page you are looking for cannot be found. Please notify the site admin if you have found a broken link', 'woothemes') ?></p>
            		<?php
            		
        		endif;
        	?>
        	
        </article><!-- /.post -->
                                                
	</div><!--/inner-content-->

</section><!--/content-->  
	
<?php get_footer(); ?>