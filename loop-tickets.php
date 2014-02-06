<table class="items" cellspacing="0">
	<thead>
		<tr>
			<th class="status"><?php woo_supportpress_sort_by_link('status', __('Status', 'woothemes')); ?></th>
			<th class="number"><?php woo_supportpress_sort_by_link('ticket', __('#', 'woothemes')); ?></th>
			<th class="title"><?php woo_supportpress_sort_by_link('title', __('Title', 'woothemes')); ?></th>
			<th class="type"><?php _e('Type', 'woothemes'); ?></th>
			<th class="assigned"><?php _e('Assigned to', 'woothemes'); ?></th>
			<th class="age"><?php woo_supportpress_sort_by_link('age', __('Age', 'woothemes')); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (have_posts()) : $alt = 1; while (have_posts()) : the_post(); $alt = $alt * -1; ?>
		
		<?php $ticket_details = woo_supportpress_get_ticket_details( $post->ID ); ?>
		
		<tr class="ticket <?php if ($alt==1) echo 'odd'; else echo 'even'; ?> status-<?php echo $ticket_details['status']->slug; ?> priority-<?php echo $ticket_details['priority']->slug; ?>">
					
			<td class="status"><span><?php if (isset($ticket_details['status']->name)) echo $ticket_details['status']->name; ?></span></td>
			<td class="number"><mark><?php echo $post->ID; ?></mark></td>
			<td class="title">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title() ?></a></h2>
			</td>
			<td class="type"><?php if (isset($ticket_details['type']->name) && $ticket_details['type']->name!='N/A') echo ucwords($ticket_details['type']->name); else _e('&ndash;', 'woothemes'); ?></td>
			<td class="assigned">
				<a class="tooltip" href="<?php if ($ticket_details['assigned_to']->ID > 0 ) echo get_author_posts_url( $ticket_details['assigned_to']->ID ); else echo add_query_arg('assigned_to', '0', get_post_type_archive_link('ticket')); ?>" title="<?php echo $ticket_details['assigned_to']->display_name; ?>"><?php echo get_avatar( $ticket_details['assigned_to']->ID, '28' ); ?></a>
			</td>
			<td class="age"><?php echo human_time_diff(get_the_modified_time('U'), current_time('timestamp')); ?></td>

		</tr>
		<?php endwhile; else : ?>
			<tr><td colspan="6"><?php _e('No tickets found.', 'woothemes'); ?></td></tr>
		<?php endif; ?>
	</tbody>
</table>

<?php woo_pagination(); ?>
<?php wp_reset_query(); ?> 