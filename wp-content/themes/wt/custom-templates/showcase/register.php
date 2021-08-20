<?php
defined('ABSPATH') || exit;

wt_logged_out_only();

get_header();
?>

<div class="row collapsed wt-gutter-vertical-large">
	<div class="column">
		<?php
		while (have_posts())
		{
			the_post();
			?>
			
			<h1><?php the_title(); ?></h1>
			
			<form data-route="wt/v1/users" method="post">
				<div class="callout hide" data-success="Your profile has been created successfully. <a href='<?php echo home_url('showcase/login'); ?>'>Log in now.</a>"></div>
				
				<?php
		//		wt_dump($fb_user);
				$fields = array(
					'first_name' => array(
						'label' => 'First Name',
						'type' => 'text',
						'autocomplete' => 'given-name',
		//				'fb' => 'first_name',
					),
					'last_name' => array(
						'label' => 'Last Name',
						'type' => 'text',
						'autocomplete' => 'family-name',
		//				'fb' => 'last_name',
					),
					'user_email' => array(
						'label' => 'Email',
						'type' => 'email',
						'autocomplete' => 'email',
		//				'fb' => 'email',
					),
					'meta[wt_mobile]' => array(
						'label' => 'Mobile',
						'type' => 'tel',
						'autocomplete' => 'tel',
		//				'fb' => 'email',
					),
					'meta[wt_organization]' => array(
						'label' => 'Organisation/Company',
						'type' => 'text',
		//				'fb' => 'email',
					),
				);
				
				foreach ($fields as $name => $field)
				{
		//			$value = empty($field['fb']) || empty($fb_user[$field['fb']]) ? '' : sanitize_text_field($fb_user[$field['fb']]);
					?>
					
					<label>
						<?php echo $field['label']; ?>
						<input type="<?php echo $field['type']; ?>" name="<?php echo $name; ?>" value="<?php //echo $value; ?>" <?php echo empty($field['autocomplete']) ? '' : 'autocomplete="' . $field['autocomplete'] . '"'; ?> required>
					</label>
					
					<?php
				}
				?>
				
				<?php get_template_part('template-parts/form/field-password'); ?>
				
				<button type="submit" class="button" data-submit="Create Account" data-submitting="Creating Account..." data-submitted="Created Account">Create Account</button>
			</form>
			
			<?php
		}
		?>
	</div>
</div>

<?php get_footer();
