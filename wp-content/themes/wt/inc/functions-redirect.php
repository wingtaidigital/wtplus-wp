<?php
function wt_me_only()
{
	global $author_name, $author;
	
	$curauth = isset($author_name) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	
	if (!is_user_logged_in() || get_current_user_id() != $curauth->ID)
	{
		wt_redirect(home_url());
//		wt_redirect(get_author_posts_url($curauth->ID));
		exit;
	}
}



function wt_logged_out_only()
{
	if ( is_user_logged_in() )
	{
		global $current_user;
		
		wp_redirect(get_author_posts_url($current_user->ID));
		
		exit;
	}
}



function wt_crm_logged_in_only()
{
	if ( !wt_is_user_logged_in() )
	{
		wp_redirect(home_url('login'));
		
		exit;
	}
}



function wt_crm_logged_out_only()
{
	if ( wt_is_user_logged_in() )
	{
		wp_redirect(home_url('my-account'));
		
		exit;
	}
}
