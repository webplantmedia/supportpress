<?php
/*---------------------------------------------------------------------------------*/
/* Knowledgebase category list */
/*---------------------------------------------------------------------------------*/
class Woo_Supportpress_kb_cats extends WP_Widget {

	function Woo_Supportpress_kb_cats() {
		$widget_ops = array('description' => 'Lists knowledgebase categories.' );
		parent::WP_Widget(false, __('SupportPress - Knowledgebase Categories', 'woothemes'), $widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		if (isset($instance['title'])) $title = $instance['title'];
		if (!isset($title) || !$title) $title = __('Categories', 'woothemes');
		
		echo $before_widget;
		echo $before_title . $title . $after_title;

		echo '<ul class="numberlist">';

			$args = array(
			    'orderby'            => 'name',
			    'order'              => 'ASC',
			    'show_last_update'   => 0,
			    'style'              => 'list',
			    'show_count'         => 1,
			    'hide_empty'         => 1,
			    'use_desc_for_title' => 0,
			    'child_of'           => 0,
			    'hierarchical'       => true,
			    'title_li'           => '',
			    'show_option_none'   => __('No categories'),
			    'number'             => NULL,
			    'echo'               => 0,
			    'pad_counts'         => 1,
			    'taxonomy'           => 'knowledgebase_category'
			    );
				
			$cats = wp_list_categories( $args );
			$cats = str_replace('(', '<span class="count">', $cats);
			$cats = str_replace(')', '</span>', $cats);
			echo $cats;
			
		echo '</ul>';
		
		wp_reset_query();
        
		echo $after_widget; 
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
       $title = esc_attr($instance['title']);

       ?>
       <p>
	   	   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
       </p>
      <?php
   }
} 

register_widget('Woo_Supportpress_kb_cats');