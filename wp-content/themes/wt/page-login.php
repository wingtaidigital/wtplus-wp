<?php
if (isset($_GET['loggedout']))
{
	unset($_SESSION['wt_crm']);
	unset($_SESSION['wt_fb']);
}

/*if (isset($_GET['cid']))
{
	$customer_number = base64_decode($_GET['cid']);
	
	if ($customer_number)
	{
		$verified = wt_crm_email_verified($customer_number);
//		wt_log($verified, 'crm');
		
		if (is_wp_error($verified))
		{
			$verified = false;
		}
		else
		{
			wp_redirect(home_url('forgot-password/'));
//			$verified = true;
		}
	}
}*/

wt_crm_logged_out_only();

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-gutter-half">
	<?php
	while (have_posts())
	{
		the_post();
		?>
		
		<?php get_template_part('template-parts/form/form-login'); ?>
		
		<?php
	}
	?>
</div>

<?php get_footer();
