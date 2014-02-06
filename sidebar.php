<aside id="sidebar">

	<?php if (is_post_type_archive('knowledgebase') || ('knowledgebase' == get_post_type()) ) : ?>
	
		<?php if (woo_active_sidebar('knowledgebase')) : ?>
	    	<?php woo_sidebar('knowledgebase'); ?>		           
		<?php else : ?>
			<?php 
			the_widget('Woo_Supportpress_kb_cats'); 
			the_widget('Woo_Supportpress_kb_tags');
			?> 
		<?php endif; ?> 
		
	<?php else : ?>
	
		<?php if (woo_active_sidebar('primary')) : ?>
	    	<?php woo_sidebar('primary'); ?>		           
		<?php else : ?>
			<?php 
			the_widget('WP_Widget_Categories'); 
			?> 
		<?php endif; ?>  
	
	<?php endif; ?>
		
</aside><!--/#sidebar-->
