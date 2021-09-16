<?php
defined('ABSPATH') || exit;

wt_crm_logged_in_only();

$member =  wt_crm_api([
	'Command'  => 'MEMBER ENQUIRY',
	'MemberID' => $_SESSION['wt_crm']['MemberID'],
	'RequestDynamicColumnLists' => [
		['Name' => 'CountryCode'],
		['Name' => 'NotifySMS'],
		['Name' => 'NotifyPost'],
	],
	'RequestDynamicFieldLists' => wp_parse_args([
		['Name' => 'FirstName'],
		['Name' => 'LastName'],
		['Name' => 'HaveChild'],
		['Name' => 'BrandPreference'],
		['Name' => 'NotifyCall'],
		['Name' => 'WhatsApp'],
		['Name' => 'Badges']
	], $fields)
]);
wt_log(json_encode($member));
get_header();
?>

<?php
while (have_posts())
{
	the_post();
	?>
	
	<div class="wt-account wt-gutter-bottom-x2">
		<h1 class="text-center wt-gutter-vertical-x2 wt-h3"><?php the_title(); ?></h1>
		
		<div class="row collapse">
			<nav class="column small-12 large-4 wt-background-light-gray">
				<ul class="no-bullet wt-gutter-vertical-x2 wt-gutter-x2 wt-margin-bottom-0">
					<?php
					$parent = get_page_by_path('my-account');
					
					if ($parent)
					{
						$pages = get_posts([
							'post_type' => 'page',
							'post_parent' => $parent->ID,
							'posts_per_page' => -1,
							'orderby' => 'menu_order',
							'order' => 'ASC'
						]);
						
						array_unshift($pages, $parent);
						
						foreach ($pages as $p)
						{
							if ($p->post_name === 'badges')
								continue;
							?>
							
							<li class="wt-h6 <?php echo is_page($p->post_name) ? 'is-active' : ''; ?>">
								<a href="<?php echo get_permalink($p); ?>"><?php echo str_replace([' ', 'WT+'], ['&nbsp;', 'wt+'], strtoupper(get_the_title($p))); ?></a>
							</li>
							
							<?php
						}
					}
					?>
				</ul>
			</nav>
			
			<div class="column small-12 large-8 wt-border wt-gutter-vertical-x2">
				<?php
				$fields = [];
				$child_fields = wt_crm_get_child_fields();
				$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
				
				for ($i = 1; $i < 7; $i++)
				{
					$suffix = ucfirst($f->format($i));
					
					foreach ($child_fields as $field)
						$fields[] = ['Name' => 'Child' . $field .$suffix];
				}
				
				if (is_wp_error($member))
				{
					?>
					
					<div class="wt-gutter-x2">
						Server error occurred. Please try refreshing the browser.
					</div>
					
					<?php
				}
				else
				{
					$profile = wt_crm_get_member($member);
					$card    = wt_crm_get_card($member);
					
					if (empty($profile) || empty($card))
					{
						?>
						
						Please try refreshing the browser or contact Customer Service at
						<a href="mailto:help@wtplus.com.sg">help@wtplus.com.sg</a>
						
						<?php
					}
					else
					{
						$lists             = ['DynamicColumnLists', 'DynamicFieldLists'];
						$profile['fields'] = [];
						
						foreach ($lists as $list)
						{
							$profile['fields'] = array_merge($fields, array_combine(array_column($profile[$list], 'Name'), array_column($profile[$list], 'ColValue')));
						}
						?>
						
						<?php if (!is_page('badges')) { ?>
							<header class="wt-gutter-x2">
								<p class="lead wt-upper wt-h6">
									<?php echo sanitize_text_field($profile['Name']); ?>
								</p>
								<p>
									<i class="icon icon-user-circle" aria-hidden="true"></i>
									Member since <?php echo wt_crm_format_date($profile['JoinDate'], 'display'); ?>
								</p>
								
								<hr>
							</header>
						<?php } ?>
						
						<div class="wt-gutter-vertical-x1 wt-gutter-x2">
							<?php if ($post->post_name !== 'profile' && $card['MembershipStatusCode'] === 'PENDING PROFILE UPDATE') { ?>
								Please
								<a href="<?php echo home_url('my-account/profile/'); ?>">update your profile</a> first.
							<?php } else { ?>
								<?php get_template_part('template-parts/my-account/' . $post->post_name); ?>
							<?php } ?>
						</div>
						
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
	
	<?php
}
?>

<?php get_footer();
