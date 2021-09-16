<?php
/*
Plugin Name: wt+
*/

defined('ABSPATH') || exit;



require_once 'inc/actions.php';
require_once 'inc/filters.php';
require_once 'inc/pluggable.php';
require_once 'inc/functions.php';
require_once 'inc/acf.php';
//require_once 'inc/crm.php';
require_once 'inc/ascentis.php';



if (is_admin())
{
	require_once 'inc/brand.php';
	require_once 'inc/lookbook.php';
	require_once 'inc/store.php';
	
	
	
	add_action('admin_menu', function()
	{
//		remove_menu_page( 'index.php' );                  //Dashboard
//		remove_menu_page( 'jetpack' );                    //Jetpack*
//		remove_menu_page( 'edit.php' );                   //Posts
//		remove_menu_page( 'upload.php' );                 //Media
//		remove_menu_page( 'edit.php?post_type=page' );    //Pages
		remove_menu_page( 'edit-comments.php' );          //Comments
//		remove_menu_page( 'themes.php' );                 //Appearance
//		remove_menu_page( 'plugins.php' );                //Plugins
//		remove_menu_page( 'users.php' );                  //Users
//		remove_menu_page( 'tools.php' );                  //Tools
//		remove_menu_page( 'options-general.php' );        //Settings


//		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
//		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
//		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
//		remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
//		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
//		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
//		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
//		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
	});
	
	
	
	add_action( 'admin_init', function()
	{
		if ( ! current_user_can( 'edit_pages' ) ) // && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF']
		{
			wp_redirect( home_url() );
			exit;
		}
		
		
		
		global $wp_roles;
		
		if ( ! isset( $wp_roles ) )
			return;
		
		$wp_roles->roles['subscriber']['name'] = 'Partner';
		$wp_roles->role_names['subscriber'] = 'Partner';
	});
	
	
	
	add_action( 'restrict_manage_users', function($which)
	{
		?>

		<a href="<?php echo admin_url('admin-post.php?action=export_partners'); ?>" class="button" style="display: inline-block; margin: 1px 0 0 1rem">Export Partners</a>
		
		<?php
	});
	
	add_action('admin_post_export_partners', function()
	{
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=partners.csv');
		header('Pragma: no-cache');
		
		$output = fopen('php://output', 'w');
		
		fputcsv( $output, [
			'First Name',
			'Last Name',
			'NRIC',
			'Email',
			'Mobile',
			'Company / Organization',
			'Nature of business',
			'Subscribed'
		] );
		
		$query = new WP_User_Query([
			'role' => 'Subscriber'
		]);
		$users = $query->get_results();
//		wt_log($users);
		foreach ($users as $user)
		{
//			$data = get_userdata($user->ID);
//			wt_log($data);
			fputcsv( $output, [
				sanitize_text_field($user->first_name),
				sanitize_text_field($user->last_name),
				sanitize_text_field(get_user_meta($user->ID, 'wt_nric', true)),
				sanitize_email($user->user_email),
				sanitize_text_field(get_user_meta($user->ID, 'wt_mobile', true)),
				sanitize_text_field(get_user_meta($user->ID, 'wt_organization', true)),
				sanitize_text_field(get_user_meta($user->ID, 'wt_nature', true)),
				get_user_meta($user->ID, 'wt_subscribed', true)
			] );
		}
		
		
		return $output;
	});
	
	
	
//	if (function_exists('wp_cache_post_change'))
//	{
//		add_action("save_post_wt_brand", function($post_ID, $post, $update)
//		{
//			$page = get_page_by_path('brands');
//
//			if ($page)
//				wp_cache_post_change($page->ID);
//		});
//	}
}
