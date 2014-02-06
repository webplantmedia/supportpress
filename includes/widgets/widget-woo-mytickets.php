<?php
/*---------------------------------------------------------------------------------*/
/* Current MyTickets widget */
/*---------------------------------------------------------------------------------*/
class Woo_SupportPress_MyTickets extends WP_Widget {

	function Woo_SupportPress_MyTickets() {
		$widget_ops = array('description' => 'Displays tickets assigned to the logged in user.' );
		parent::WP_Widget(false, __('SupportPress - My Tickets', 'woothemes'),$widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		$title = $instance['title'];
		if (!$title) $title = __('My Tickets &rarr;', 'woothemes');
		
		if (!user_is_member_of_site()) return;
		
		echo $before_widget;
		echo $before_title . '<a href="' . add_query_arg('assigned_to', get_current_user_id(), get_post_type_archive_link('ticket')) . '">' . $title . '</a>' . $after_title;
        
        $args = array(
			'post_type'	=> 'ticket',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => '_responsible',
			'meta_value' => get_current_user_id()
		);
		$my_query = new WP_Query($args);
		
		if ($my_query->have_posts()) :
			$counts = array();
			while ($my_query->have_posts()) : $my_query->the_post();
				
				$status = current(wp_get_object_terms( $my_query->post->ID, 'ticket_status' ))->slug;
				if (!isset($counts[$status])) $counts[$status] = 0;
				$counts[$status]++;
				
			endwhile;
		endif;
		
		$ticket_status = get_terms('ticket_status', 'orderby=description&hide_empty=0');
		echo '<ul class="numberlist">';
		if ($ticket_status) : foreach($ticket_status as $status) :
			
			if ($status->slug == RESOLVED_STATUS_SLUG) continue;
			
			if (!isset($counts[$status->slug])) :
				$counts[$status->slug] = '0';
			endif;
			echo '<li><a href="'.add_query_arg('assigned_to', get_current_user_id(), get_term_link($status->slug, 'ticket_status')).'">'.$status->name.' <span>'.$counts[$status->slug].'</span></a></li>';

		endforeach; endif;
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

register_widget('Woo_SupportPress_MyTickets');