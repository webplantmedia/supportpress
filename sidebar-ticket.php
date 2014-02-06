<aside id="sidebar">
	
	<?php if (!is_agent()) : ?>
		<div class="notice yellow noicon"><span><p><strong><?php _e('At a glance...', 'woothemes'); ?></strong> <?php 
				
			$open_count = woo_supportpress_get_authors_tickets( get_current_user_id() );
			$resolved_count = woo_supportpress_get_authors_resolved_tickets( get_current_user_id() );
			
			echo sprintf(_n('You have <a href="%s">%s</a> open ticket', 'You have <a href="%s">%s</a> open tickets', $open_count, 'woothemes'), get_post_type_archive_link('ticket'), $open_count);
			
			echo sprintf(_n(' and <a href="%s">%s</a> resolved ticket.', ' and <a href="%s">%s</a> resolved tickets.', $resolved_count, 'woothemes'), get_term_link(RESOLVED_STATUS_SLUG, 'ticket_status'), $resolved_count);
			
		?></p></span></div>
	<?php else : ?>
		<div class="notice yellow noicon"><span><p><strong><?php _e('At a glance...', 'woothemes'); ?></strong> <?php 
				
			$assigned_count = woo_supportpress_get_open_user_tickets( get_current_user_id() );
			$unassigned_count = woo_supportpress_get_unassigned_tickets();
			
			echo sprintf(_n('There is <a href="%s">%s</a> ticket assigned to you and ', 'There are <a href="%s">%s</a> tickets assigned to you and ', $assigned_count, 'woothemes'), add_query_arg('assigned_to', get_current_user_id(), get_post_type_archive_link('ticket')), $assigned_count);
			
			echo sprintf(_n('<a href="%s">%s</a> unassigned ticket.', '<a href="%s">%s</a> unassigned tickets.', $unassigned_count, 'woothemes'), add_query_arg('assigned_to', '0', get_post_type_archive_link('ticket')), $unassigned_count);
			
		?></p></span></div>
	<?php endif; ?>
	
	<?php if (is_agent()) : ?>
	<section class="widget widget_woo_tickets">
	
		<h2 class="widgettitle"><?php _e('Browse Tickets&hellip;', 'woothemes'); ?></h2>
	
		<?php
		
			$terms = get_terms('ticket_status', 'orderby=description&hide_empty=1');
			$loop = 0;
			if ($terms) :
				echo '<h3>'.__('By Status', 'woothemes').'</h3>';
				echo '<ul>';
				foreach($terms as $term) :
					$class = '';
					if ($loop==sizeof($terms)) $class = 'last';
					echo '<li class="'.$class.'"><a href="'.get_term_link($term->slug, 'ticket_status').'"><span>'.$term->count.'</span> '.$term->name.'</a></li>';
					$loop++;
				endforeach;
				echo '</ul>';
			endif;

			$terms = get_terms('ticket_type', 'orderby=name&hide_empty=1');
			$loop = 0;
			if ($terms) :
				echo '<h3>'.__('By Type', 'woothemes').'</h3>';
				echo '<ul>';
				foreach($terms as $term) :
					$class = '';
					if ($loop==sizeof($terms)) $class = 'last';
					echo '<li class="'.$class.'"><a href="'.get_term_link($term->slug, 'ticket_type').'"><span>'.$term->count.'</span> '.$term->name.'</a></li>';
					$loop++;
				endforeach;
				echo '</ul>';
			endif;
			
			$terms = get_terms('ticket_priority', 'orderby=description&hide_empty=1');
			$loop = 0;
			if ($terms) :
				echo '<h3>'.__('By Priority', 'woothemes').'</h3>';
				echo '<ul>';
				foreach($terms as $term) :
					$class = '';
					if ($loop==sizeof($terms)) $class = 'last';
					echo '<li class="'.$class.'"><a href="'.get_term_link($term->slug, 'ticket_priority').'"><span>'.$term->count.'</span> '.$term->name.'</a></li>';
					$loop++;
				endforeach;
				echo '</ul>';
			endif;
		?>

	</section>
	<?php endif; ?>
	
	<?php if (woo_active_sidebar('ticket')) : ?>
    	<?php woo_sidebar('ticket'); ?>		           
	<?php else : ?>
		<?php if (!is_agent()) the_widget('Woo_Supportpress_Agents'); ?> 
	<?php endif; ?> 
	
</aside>