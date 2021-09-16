<?php
defined('ABSPATH') || exit;

wt_logged_out_only();

get_header();
?>

<div class="wt-gutter-vertical-x2 wt-gutter-half">
	<?php
	while (have_posts())
	{
		the_post();
		?>
		
		<?php get_template_part('template-parts/form/form-showcase-login'); ?>
		
		<?php
	}
	?>
</div>

<?php
if ( isset($_GET['loggedout']) )
{
	?>
	<script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
	<?php
}
?>

<?php get_footer();
