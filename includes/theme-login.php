<?php

/*-----------------------------------------------------------------------------------*/
/* Hook to detect login
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_login_process() {
	
	global $supportpress_login_errors;

	if (isset($_POST['supportpress_login']) && $_POST['supportpress_login']) :
		$supportpress_login_errors = woo_supportpress_handle_login();
	endif;
	
}
add_action('init', 'woo_supportpress_login_process');

/*-----------------------------------------------------------------------------------*/
/* Process AJAX Login
/*-----------------------------------------------------------------------------------*/
add_action('wp_ajax_nopriv_woo_supportpress_ajax_login_process', 'woo_supportpress_ajax_login_process');

function woo_supportpress_ajax_login_process() {

	check_ajax_referer( 'supportpress-login-action', 'security' );
	
	woo_supportpress_handle_login();

	die();
}

/*-----------------------------------------------------------------------------------*/
/* Process Login
/*-----------------------------------------------------------------------------------*/

function woo_supportpress_handle_login() {
	
	if ( isset( $_REQUEST['redirect_to'] ) ) $redirect_to = $_REQUEST['redirect_to']; else $redirect_to = admin_url();
		
	if ( is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )$secure_cookie = false; else $secure_cookie = '';

	$user = wp_signon('', $secure_cookie);
	
	// Check the username
	if ( !$_POST['log'] ) :
		$user = new WP_Error();
		$user->add('empty_username', __('<strong>ERROR</strong>: Please enter a username.', 'woothemes'));
	elseif ( !$_POST['pwd'] ) :
		$user = new WP_Error();
		$user->add('empty_username', __('<strong>ERROR</strong>: Please enter your password.', 'woothemes'));
	endif;

	if (woo_supportpress_is_ajax()) :
		
		// Result
		$result = array();
		
		if ( !is_wp_error($user) ) :
			$result['success'] = 1;
			$result['redirect'] = $redirect_to;
		else :
			$result['success'] = 0;
			foreach ($user->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
		endif;
		
		echo json_encode($result);
		die();
		
	else :
		if ( !is_wp_error($user) ) :
			wp_redirect($redirect_to);
			exit;
		endif;
	endif;
	return $user;
}

/*-----------------------------------------------------------------------------------*/
/* Style the login page
/*-----------------------------------------------------------------------------------*/

function custom_loginpage_logo_link($url)
{
     // Return a url; in this case the homepage url of wordpress
     return get_bloginfo('wpurl');
}
function custom_loginpage_logo_title($message)
{
     // Return title text for the logo to replace 'wordpress'; in this case, the blog name.
     return get_bloginfo('name');
}
function custom_loginpage_head()
{
	global $woo_options;
	
	$stylesheet_uri = get_bloginfo('template_url')."/css/login.css";
	echo '<link rel="stylesheet" href="'.$stylesheet_uri.'" type="text/css" media="screen" />';
	
	if ($woo_options['woo_texttitle'] <> "true" && $woo_options['woo_logo']) : $logo = $woo_options['woo_logo'];
		?>
		<style type="text/css">
			#login h1 a {
			background:url(<?php echo $logo; ?>) no-repeat top;
			background-size: 100%;
			}
		</style>
		<?php
	endif;
}
// Hook in
add_filter("login_headerurl","custom_loginpage_logo_link");
add_filter("login_headertitle","custom_loginpage_logo_title");
add_action("login_head","custom_loginpage_head");
