<?php
/*---------------------------------------------------------------------------------*/
/* Current MyTickets widget */
/*---------------------------------------------------------------------------------*/
class Woo_SupportPress_Attachments extends WP_Widget {

	function Woo_SupportPress_Attachments() {
		$widget_ops = array('description' => 'Displays a list of the latest attachments uploaded to your site.' );
		parent::WP_Widget(false, __('SupportPress - Attachments', 'woothemes'),$widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		$title = $instance['title'];
		if (!$title) $title = __('Recent Attachments', 'woothemes');
		
        $args = array(
			'post_type'	=> 'attachment',
			'post_status' => 'inherit',
			'posts_per_page' => 5
		);
		$my_query = new WP_Query($args);
		
		if ($my_query->have_posts()) :
			echo $before_widget;
			echo $before_title . $title . $after_title;
			echo '<ul class="attachments">';
			$counts = array();
			while ($my_query->have_posts()) : $my_query->the_post();
				
				$parent = '';
				if ($my_query->post->post_parent > 0) :
					$parent = get_post($my_query->post->post_parent);
					if ($parent->post_type!='ticket' && $parent->post_type!='message') continue;
				endif;
					
				echo '<li>';
				
				echo ' <a href="'.wp_get_attachment_url($my_query->post->ID).'">'.get_the_title().'</a>';
				
				if ($parent) :
					echo ' <span>was attached to</span> <a href="'.get_permalink($my_query->post->post_parent).'">'.$parent->post_title.'</a></li>';
				endif;
				
			endwhile;
			echo '</ul>';
			echo $after_widget; 
		endif;

		wp_reset_query();

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

register_widget('Woo_SupportPress_Attachments');