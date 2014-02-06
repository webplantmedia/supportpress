<?php
/*---------------------------------------------------------------------------------*/
/* Staff widget */
/*---------------------------------------------------------------------------------*/
class Woo_Supportpress_Agents extends WP_Widget {

	function Woo_Supportpress_Agents() {
		$widget_ops = array('description' => 'Displays support agents.' );
		parent::WP_Widget(false, __('SupportPress - Agents', 'woothemes'), $widget_ops);      
	}
	
	function widget($args, $instance) {  
		extract( $args );
		if (isset($instance['title'])) $title = $instance['title'];
		if (!isset($title) || !$title) $title = __('Support Agents', 'woothemes');
		
        $users = woo_supportpress_get_support_staff();
		if ($users) :
			echo $before_widget;
			echo $before_title . $title . $after_title;
			echo '<ul>';
			foreach ($users as $user) :
				echo '<li>';
				echo '<a href="' . get_author_posts_url( $user->ID, $user->user_nicename ) . '">';
				
				echo get_avatar( $user->ID, '28' );
				
				echo $user->display_name;
				
				if ($last_update = get_user_meta($user->ID, 'last_update', true)) echo '<span>'.__(' Last update:', 'woothemes').' '.human_time_diff($last_update, current_time('timestamp')).__(' ago', 'woothemes').'</span>';
				
				echo '</a></li>';
			endforeach;
			echo '</ul>';
			echo $after_widget; 
		endif;
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
		$title = esc_attr($instance['title']);
		if (!$title) $title = __('Support Agents', 'woothemes');

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<?php
   }
} 

register_widget('Woo_Supportpress_Agents');