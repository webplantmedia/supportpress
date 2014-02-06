<?php global $woo_options, $footer_class; ?>

	<footer id="footer" class="<?php if (isset($footer_class)) echo $footer_class; ?>">	
    
    	<div class="col2-set">
    		<div class="col-1">
	    		<?php if(isset($woo_options['woo_footer_left']) && $woo_options['woo_footer_left'] == 'true'){
				
					echo '<p>' . stripslashes($woo_options['woo_footer_left_text']) . '</p>';	
		
				} else { ?>
					<p>&copy; <?php echo date('Y'); ?> <?php bloginfo(); ?>. <?php _e('All Rights Reserved.', 'woothemes') ?></p>
				<?php } ?>
	    	</div><!--/.col-1-->
	    	
	    	<div class="col-2 credit">
	    		 <?php if(isset($woo_options['woo_footer_right']) && $woo_options['woo_footer_right'] == 'true'){
				
		        	echo '<p>' . stripslashes($woo_options['woo_footer_right_text']) . '</p>';
		       	
				} else { ?>
					<p><?php _e('Designed by', 'woothemes') ?> <a href="<?php $aff = $woo_options['woo_footer_aff_link']; if(!empty($aff)) { echo $aff; } else { echo 'http://www.woothemes.com'; } ?>"><img src="<?php bloginfo('template_directory'); ?>/images/logos/woothemes.png" width="74" height="19" alt="Woo Themes" /></a></p>
				<?php } ?>
	    	</div><!--/.col-2-->
    	</div>

    </footer>
    
    <div class="clear"></div>
    
</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<?php woo_foot(); ?>
</body>
</html>