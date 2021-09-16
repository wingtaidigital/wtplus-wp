<?php
defined('ABSPATH') || exit;

if ($post->post_name != 'showcase')
{
	global $post;
	
	$showcase = get_page_by_path('showcase');
	
	setup_postdata( $GLOBALS['post'] = &$showcase );
}

if (have_posts())
{
	$title = get_post_meta(get_the_ID(), 'wt_join_title', true);
	$content = get_post_meta(get_the_ID(), 'wt_join_content', true);
	?>
	
	<section id="join" class="wt-relative">
		<div class="wt-center wt-gutter-half wt-clear-last-child-margin">
			<?php if ($title)
			{ ?>
				<h1 class="text-center wt-h2 wt-margin-bottom"><?php echo sanitize_text_field($title); ?></h1>
			<?php } ?>
			
			<?php if ($content)
			{ ?>
				<div class="text-left wt-content wt-margin-bottom">
					<?php echo wpautop(wp_kses_post($content)); ?>
				</div>
			<?php } ?>
			
			<?php
			if (!is_user_logged_in())
			{
//			$cta = get_post_meta($post->ID, 'wt_join_cta', true);
				?>
				
				<p><a href="<?php echo wp_registration_url(); ?>" class="wt-text-hover">[ SIGN UP NOW ]</a></p>
				
				<p>Already have an account?<br>
					<a data-open="showcase-login-modal" class="wt-text-hover">Click here to log in.</a></p>
				
				<?php
			}
			else
			{
				?>
				
				<p><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>" class="wt-text-hover">[ ACCOUNT DASHBOARD ]</a></p>
				
				<p><a href="<?php echo wp_logout_url(); ?>" class="wt-text-hover">Log out</a></p>
				
				<?php
			}
			?>
		</div>
	</section>
	
	<?php
}

wp_reset_postdata();
