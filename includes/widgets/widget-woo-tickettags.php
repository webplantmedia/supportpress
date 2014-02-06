<?php
/*---------------------------------------------------------------------------------*/
/* Current MyTickets widget */
/*---------------------------------------------------------------------------------*/
class Woo_SupportPress_TicketTags extends WP_Widget {

	function Woo_SupportPress_TicketTags() {
		$widget_ops = array('description' => 'Displays ticket tags.' );
		parent::WP_Widget(false, __('SupportPress - Ticket Tags', 'woothemes'), $widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		$title = $instance['title'];
		$number = $instance['number'];
		if (!$title) $title = __('Ticket Tags', 'woothemes');
		if (!$number) $number = 20;
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
        
        $args = array(
			'taxonomy'  => array('ticket_tags'), 
			'number' => $number,
			'separator' => ", "
		); 
   
  		wp_tag_cloud($args);
        
		echo $after_widget; 
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
		$title = esc_attr($instance['title']);
		$number = esc_attr($instance['number']);
		if (!$title) $title = __('Ticket Tags', 'woothemes');
		if (!$number) $number = 20;

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number to show:','woothemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('number'); ?>"  value="<?php echo $number; ?>" class="widefat" id="<?php echo $this->get_field_id('number'); ?>" />
		</p>
		<?php
   }
} 

register_widget('Woo_SupportPress_TicketTags');