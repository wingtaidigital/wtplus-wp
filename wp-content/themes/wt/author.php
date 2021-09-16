<?php
defined('ABSPATH') || exit;

wt_me_only();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

get_header();

$url = get_author_posts_url(get_current_user_id());
$is_profile = isset($_GET['profile']);

if ($is_profile)
{
	$title = 'My Profile';
}
else
{
	$title = 'My Events';
}
?>

<div class="wt-account wt-gutter-bottom-x2">
	<h1 class="text-center wt-gutter-vertical-x2 wt-h3"><?php echo $title; ?></h1>
	
	<div class="row collapse">
		<nav class="column small-12 large-4 wt-background-light-gray">
			<ul class="no-bullet wt-gutter-vertical-x2 wt-gutter-x2">
				<li class="wt-upper wt-h6 <?php echo $is_profile ? '' : 'is-active'; ?>"><a href="<?php echo add_query_arg('events', '', $url); ?>">My Events</a></li>
				<li class="wt-upper wt-h6 <?php echo $is_profile ? 'is-active' : ''; ?>"><a href="<?php echo add_query_arg('profile', '', $url); ?>">My Profile</a></li>
				<li class="wt-upper wt-h6"><a href="<?php echo wp_logout_url(); ?>">Log out</a></li>
			</ul>
		</nav>
		
		<div class="column small-12 large-8 wt-border">
			<div class="wt-gutter-vertical-x2 wt-gutter-x2">
				<?php
				if ($is_profile)
					get_template_part('template-parts/author/profile');
				else
					get_template_part('template-parts/author/events');
				?>
			</div>
		</div>
	</div>
</div>

<?php get_footer();
