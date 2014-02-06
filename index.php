<?php if (is_agent()) : // Agent Homepage ?>

	<?php get_template_part('homepage-agent'); ?>

<?php elseif (is_user_logged_in()) : // Customer Homepage ?>

	<?php get_template_part('homepage-client'); ?>

<?php else : ?>

	<?php get_template_part('homepage-guest'); ?>

<?php endif; ?>
	
