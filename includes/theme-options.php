<?php

// Define ticket slugs used throughout the theme
if (!defined('OPEN_STATUS_SLUG')) define('OPEN_STATUS_SLUG', __('open', 'woothemes'));
if (!defined('NEW_STATUS_SLUG')) define('NEW_STATUS_SLUG', __('new', 'woothemes'));
if (!defined('PENDING_STATUS_SLUG')) define('PENDING_STATUS_SLUG', __('pending', 'woothemes'));
if (!defined('RESOLVED_STATUS_SLUG')) define('RESOLVED_STATUS_SLUG', __('resolved', 'woothemes'));

//Enable WooSEO on these custom Post types
$seo_post_types = array('post', 'page');
define("SEOPOSTTYPES", serialize($seo_post_types));

//Global options setup
add_action('init','woo_global_options');
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option('woo_options');
}

add_action( 'admin_head','woo_options' );
if (!function_exists('woo_options')) {
function woo_options() {

// Make sure this function only runs on the "Theme Options" admin screen. Performance optimization.
global $pagenow;
if ( ( $pagenow == 'admin.php' ) && isset( $_GET['page'] ) && ( $_GET['page'] == 'woothemes' ) ) {} else { return; }

// VARIABLES
$themename = "SupportPress";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/supportpress/';
$shortname = "woo";

//Access the WordPress Categories via an Array
$woo_categories = array();
$woo_categories_obj = get_categories('hide_empty=0');
foreach ($woo_categories_obj as $woo_cat) {
    $woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;}
$categories_tmp = array_unshift($woo_categories, "Select a category:");

//Access the WordPress Pages via an Array
$woo_pages = array();
$woo_pages_obj = get_pages('sort_column=post_parent,menu_order');
foreach ($woo_pages_obj as $woo_page) {
    $woo_pages[$woo_page->ID] = $woo_page->post_name; }
$woo_pages_tmp = array_unshift($woo_pages, "Select a page:");

//Stylesheets Reader
$alt_stylesheet_path = TEMPLATEPATH . '/styles/';
$alt_stylesheets = array();
if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) {
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }
    }
}

//More Options
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");

// THIS IS THE DIFFERENT FIELDS
$options = array();

$page_dropdown = array();
$page_dropdown_pages = get_posts('numberposts=-1&post_type=page&post_status=publish&orderby=name&order=asc');
if ($page_dropdown_pages) foreach ($page_dropdown_pages as $page) $page_dropdown[$page->ID] = $page->post_title;

$page_dropdown_none = $page_dropdown;
$page_dropdown_none['0'] = __('None', 'woothemes');

// General

$options[] = array( "name" => __( 'General Settings', 'woothemes' ),
					"type" => "heading",
					"icon" => "general" );

$options[] = array( "name" => __( 'Members Only', 'woothemes' ),
					"desc" => __( 'Activate to only allow members to view the site, anyone else will be redirected to the login page.', 'woothemes' ),
					"id" => $shortname."_members_only",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Prevent Admin Access', 'woothemes' ),
					"desc" => __( 'Activate to prevent client users from accessing admin (redirects them to the main site)', 'woothemes' ),
					"id" => $shortname."_prevent_admin_access",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Custom Logo', 'woothemes' ),
					"desc" => __( 'Upload a logo for your theme or specify an image URL directly.', 'woothemes' ),
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload" );

$options[] = array( "name" => __( 'Text Title', 'woothemes' ),
    				'desc' => sprintf( __( 'Enable text-based Site Title and Tagline. Setup title & tagline in %1$s.', 'woothemes' ), '<a href="' . esc_url( home_url() ) . '/wp-admin/options-general.php">' . __( 'General Settings', 'woothemes' ) . '</a>' ),
					"id" => $shortname."_texttitle",
					"std" => "false",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Site Title Typography', 'woothemes' ),
					"desc" => __( 'Change the site title typography', 'woothemes' ),
					"id" => $shortname."_font_site_title",
					"std" => array( 'size' => '1.8','unit' => 'em','face' => 'Helvetica','style' => '','color' => '#A4C346'),
					"class" => "hidden",
					"type" => "typography" );

$options[] = array( "name" => __( 'Site Description', 'woothemes' ),
					"desc" => __( 'Enable the site description / tagline under the site title', 'woothemes' ),
					"id" => $shortname."_tagline",
					"class" => "hidden",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Site Description Typography', 'woothemes' ),
					"desc" => __( 'Change the site description typography', 'woothemes' ),
					"id" => $shortname."_font_tagline",
					"std" => array( 'size' => '0.8','unit' => 'em','face' => 'Helvetica','style' => '','color' => '#485160'),
					"class" => "hidden last",
					"type" => "typography" );

$options[] = array( "name" => __( 'Custom Favicon', 'woothemes' ),
    				'desc' => sprintf( __( 'Upload a 16px x 16px %1$s that will represent your website\'s favicon.', 'woothemes' ), '<a href="http://www.faviconr.com/">'.__( 'ico image', 'woothemes' ).'</a>' ),
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload" );

$options[] = array( "name" => __( 'Tracking Code', 'woothemes' ),
					"desc" => __( 'Paste your Google analytics (or other) tracking code here. This will be added into the footer template of your theme', 'woothemes' ),
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea" );

$options[] = array( "name" => __( 'RSS URL', 'woothemes' ),
					"desc" => __( 'Enter your preferred RSS URL. (Feedburner or other)', 'woothemes' ),
					"id" => $shortname."_feed_url",
					"std" => "",
					"type" => "text" );

$options[] = array( "name" => __( 'Email Subscription URL', 'woothemes' ),
					"desc" => __( 'Enter your preferred email subscription URL', 'woothemes' ),
					"id" => $shortname."_subscribe_email",
					"std" => "",
					"type" => "text" );

$options[] = array( "name" => __( 'Contact Form Email', 'woothemes' ),
					"desc" => __( 'Enter your email address to use the Contact Form Page Template. Add the contact form by adding a new page and selecting "Contact Form" as page template.', 'woothemes' ),
					"id" => $shortname."_contactform_email",
					"std" => "",
					"type" => "text" );

$options[] = array( "name" => __( 'Custom CSS', 'woothemes' ),
					"desc" => __( 'Quickly add some CSS to your theme by adding it to this block.', 'woothemes' ),
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea" );

$options[] = array( "name" => __( 'Post / Page Comments', 'woothemes' ),
					"desc" => __( 'Select if you want to enable / disable comments on posts and / or pages.', 'woothemes' ),
					"id" => $shortname."_comments",
					"type" => "select2",
					"options" => array( "post" => "Posts Only", "page" => "Pages Only", "both" => "Pages / Posts", "none" => "None") );

$options[] = array( "name" => __( 'Post Content', 'woothemes' ),
					"desc" => __( 'Select if you want to show the full content for the excerpt on posts.', 'woothemes' ),
					"id" => $shortname."_post_content",
					"type" => "select2",
					"options" => array( "excerpt" => "The Excerpt", "content" => "Full Content" ) );


/* Guest Homepage */

$options[] = array( "name" => __( 'Guest Homepage', 'woothemes' ),
					"type" => "heading",
					"icon" => "homepage");

$options[] = array( "name" => __( 'Homepage Notice', 'woothemes' ),
					"desc" => __( 'This text will appear on the guest homepage in a yellow notice box', 'woothemes' ),
					"id" => $shortname."_guest_homepage_notice",
					"std" => "Welcome to our support desk. Please search our knowledgebase for any known issues or signup to report a new issue.",
					"type" => "textarea" );

$options[] = array( "name" => __( 'Enable sign up form on guest homepage', 'woothemes' ),
					"desc" => __( 'Activate to show the signup form on the guest homepage.', 'woothemes' ),
					"id" => $shortname."_homepage_signup",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Show terms checkbox on signup form', 'woothemes' ),
					"desc" => __( 'Activate to show a terms checkbox on the guest homepage signup form. Set up the terms page below.', 'woothemes' ),
					"id" => $shortname."_show_terms_on_signup",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Terms Page', 'woothemes' ),
					"desc" => __( 'Select the terms page', 'woothemes' ),
					"id" => "woo_supportpress_terms_page_id",
					"std" => "0",
					"type" => "select2",
					"options" => $page_dropdown_none);


// Pages

$options[] = array( "name" => __( 'Page Setup', 'woothemes' ),
					"type" => "heading",
					"icon" => "post");

$options[] = array( "name" => __( 'New Ticket Page', 'woothemes' ),
					"desc" => __( 'Select the new ticket page.', 'woothemes' ),
					"id" => "woo_supportpress_new_ticket_page_id",
					"std" => "",
					"type" => "select2",
					"options" => $page_dropdown);

$options[] = array( "name" => __( 'New Message Page', 'woothemes' ),
					"desc" => __( 'Select the new message page.', 'woothemes' ),
					"id" => "woo_supportpress_new_message_page_id",
					"std" => "",
					"type" => "select2",
					"options" => $page_dropdown);

$options[] = array( "name" => __( 'Staff Page', 'woothemes' ),
					"desc" => __( 'Select the staff page.', 'woothemes' ),
					"id" => "woo_supportpress_staff_page_id",
					"std" => "",
					"type" => "select2",
					"options" => $page_dropdown);

$options[] = array( "name" => __( 'Profile Page', 'woothemes' ),
					"desc" => __( 'Select the profile page.', 'woothemes' ),
					"id" => "woo_supportpress_profile_page_id",
					"std" => "",
					"type" => "select2",
					"options" => $page_dropdown);

$options[] = array( "name" => __( 'Blog Page', 'woothemes' ),
					"desc" => __( 'Select the blog page, or choose "none" to disable the link on the nav bar / the blog notice', 'woothemes' ),
					"id" => "woo_supportpress_blog_page_id",
					"std" => "",
					"type" => "select2",
					"options" => $page_dropdown_none);

/* Layout */

$options[] = array( "name" => __( 'Layout Options', 'woothemes' ),
					"type" => "heading",
					"icon" => "layout" );

$url =  get_template_directory_uri() . '/functions/images/';
$options[] = array( "name" => __( 'Main Layout', 'woothemes' ),
					"desc" => __( 'Select which layout you want for your site.', 'woothemes' ),
					"id" => $shortname."_site_layout",
					"std" => "layout-left-content",
					"type" => "images",
					"options" => array(
						'layout-left-content' => $url . '2cl.png',
						'layout-right-content' => $url . '2cr.png')
					);

/* Header */
$options[] = array( "name" => __( 'Header Customization', 'woothemes' ),
					"type" => "heading",
					"icon" => "header" );

$options[] = array( "name" => __( 'Enable Top Navigation?', 'woothemes' ),
					"desc" => __( 'Activate to show a navigation bar on the top of the header.', 'woothemes' ),
					"id" => $shortname."_show_top_navigation",
					"std" => "true",
					"type" => "checkbox" );

/* Footer */
$options[] = array( "name" => __( 'Footer Customization', 'woothemes' ),
					"type" => "heading",
					"icon" => "footer" );


$options[] = array( "name" => __( 'Custom Affiliate Link', 'woothemes' ),
					"desc" => __( 'Add an affiliate link to the WooThemes logo in the footer of the theme.', 'woothemes' ),
					"id" => $shortname."_footer_aff_link",
					"std" => "",
					"type" => "text" );

$options[] = array( "name" => __( 'Enable Custom Footer (Left)', 'woothemes' ),
					"dec" => __( 'Activate to add the custom text below to the theme footer.', 'woothemes' ),
					"id" => $shortname."_footer_left",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Custom Text (Left)', 'woothemes' ),
					"desc" => __( 'Custom HTML and Text that will appear in the footer of your theme', 'woothemes' ),
					"id" => $shortname."_footer_left_text",
					"std" => "",
					"type" => "textarea" );

$options[] = array( "name" => __( 'Enable Custom Footer (Right)', 'woothemes' ),
					"dec" => __( 'Activate to add the custom text below to the theme footer.', 'woothemes' ),
					"id" => $shortname."_footer_right",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => __( 'Custom Text (Right)', 'woothemes' ),
					"desc" => __( 'Custom HTML and Text that will appear in the footer of your theme', 'woothemes' ),
					"id" => $shortname."_footer_right_text",
					"std" => "",
					"type" => "textarea" );


// Add extra options through function
if ( function_exists( "woo_options_add") )
	$options = woo_options_add($options);

if ( get_option( 'woo_template') != $options) update_option( 'woo_template',$options);
if ( get_option( 'woo_themename') != $themename) update_option( 'woo_themename',$themename);
if ( get_option( 'woo_shortname') != $shortname) update_option( 'woo_shortname',$shortname);
if ( get_option( 'woo_manual') != $manualurl) update_option( 'woo_manual',$manualurl);

	}
}

// We don't need settings metaboxes
remove_action( 'admin_menu', 'woothemes_metabox_add' );