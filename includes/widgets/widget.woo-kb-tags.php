<?php
/*---------------------------------------------------------------------------------*/
/* Knowledgebase tags list */
/*---------------------------------------------------------------------------------*/
class Woo_Supportpress_kb_tags extends WP_Widget {

	function Woo_Supportpress_kb_tags() {
		$widget_ops = array('description' => 'Knowledgebase tag cloud.' );
		parent::WP_Widget(false, __('SupportPress - Knowledgebase Tag Cloud', 'woothemes'), $widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		if (isset($instance['title'])) $title = $instance['title'];
		if (!isset($title) || !$title) $title = __('Tags', 'woothemes');
		
		echo $before_widget;
		echo $before_title . $title . $after_title;

		$args = array(
		    'taxonomy'                  => 'knowledgebase_tags', 
		    'echo'                      => true,
		    'format'                    => 'list'
		); 
			
		wp_tag_cloud( $args );
        
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

register_widget('Woo_Supportpress_kb_tags');