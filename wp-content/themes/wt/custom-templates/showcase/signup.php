<?php
defined('ABSPATH') || exit;

wt_logged_out_only();

get_header();
?>

<div class="wt-background-light-gray wt-account">
	<div class="row align-center">
		<div class="column small-12 large-7 wt-gutter-vertical-x2">
			<?php
			while (have_posts())
			{
				the_post();
				?>
				
				<div class="text-center wt-clear-last-child-margin wt-gutter-bottom-x2">
					<h1 class="wt-h3 wt-margin-bottom"><?php the_title(); ?></h1>
					
					<?php the_content(); ?>
				</div>
				
				<form data-route="wt/v1/users" method="post" autocomplete="off">
					<div class="callout hide" data-success="Your profile has been created successfully. <a data-open='showcase-login-modal'>Log in now.</a>"></div>
					
					<?php
			//		wt_dump($fb_user);
					$fields = array(
						'first_name' => array(
							'label' => 'First Name',
							'attr' => [
								'type' => 'text',
//								'autocomplete' => 'given-name',
								'placeholder' => 'Please fill in your first name',
								'required' => 'required'
							]
			//				'fb' => 'first_name',
						),
						'last_name' => array(
							'label' => 'Last Name',
							'attr' => [
								'type' => 'text',
//								'autocomplete' => 'family-name',
								'placeholder' => 'Please fill in your last name',
								'required' => 'required'
							]
			//				'fb' => 'last_name',
						),
						'meta[wt_nric]' => array(
							'label' => 'NRIC/FIN/Passport No',
							'attr' => [
								'type' => 'text',
								'placeholder' => 'NRIC/FIN/Passport No will only be used for verification purposes',
								'required' => 'required',
//								'pattern' => '[F|G|S|T|f|g|s|t]\d{7}[A-Za-z]',
								'class' => 'wt-exists',
								'data-route' => 'wt/v1/user-meta-exists?key=wt_nric&value=',
							],
							'error' => [
								'customError' => "This NRIC/FIN/Passport No has already been registered. <a data-open='showcase-login-modal'>Log in here.</a>",
//								'patternMismatch' => 'Please enter a valid NRIC/FIN'
							]
						),
						'user_email' => array(
							'label' => 'Email',
							'attr' => [
								'type' => 'email',
//								'autocomplete' => 'email',
								'placeholder' => 'Please fill in your email address',
								'class' => 'wt-exists',
								'data-route' => 'wt/v1/email-exists?email=',
								'required' => 'required'
							],
							'error' => [
								'customError' => "This email address has already been registered. <a data-open='showcase-login-modal'>Log in here.</a>",
								'typeMismatch' => 'Please enter a valid email',
								'valueMissing' => 'Please enter email'
							]
						),
						'meta[wt_mobile]' => array(
							'label' => 'Mobile',
							'attr' => [
								'type' => 'tel',
//								'autocomplete' => 'tel',
								'placeholder' => 'Please fill in your mobile number',
								'required' => 'required',
								'class' => 'wt-exists',
								'data-route' => 'wt/v1/user-meta-exists?key=wt_mobile&value=',
							],
							'error' => [
								'customError' => "This mobile number has already been registered. <a data-open='showcase-login-modal'>Log in here.</a>",
								'patternMismatch' => "Please enter a valid number",
							]
						),
						'meta[wt_organization]' => array(
							'label' => 'Company/Organization',
							'attr' => [
								'type' => 'text',
								'placeholder' => 'Please enter the company/organization name you’re representing',
								'required' => 'required'
//								'class' => 'wt-exists',
//								'data-route' => 'wt/v1/username-exists?username='
							],
//							'error' => "Company/Organization exists. <a data-open='showcase-login-modal'>Log in here.</a>"
						),
						'meta[wt_nature]' => array(
							'label' => 'Nature of business',
							'attr' => [
								'type' => 'text',
								'placeholder' => 'What does your company/organization do?',
								'required' => 'required'
							],
						),
					);
					
					wt_form_fields($fields);
					
					/*foreach ($fields as $name => $field)
					{
			//			$value = empty($field['fb']) || empty($fb_user[$field['fb']]) ? '' : sanitize_text_field($fb_user[$field['fb']]);
						?>
						
						<label>
							<?php echo $field['label']; ?>*
							<input name="<?php echo $name; ?>" required
							<?php foreach ($field['attr'] as $key => $value) { ?>
							<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
								<?php } ?>
							>
							<span class="form-error"><?php echo empty($field['error']) ? '' : $field['error']; ?></span>
						</label>
						
						<?php
					}*/
					?>
				
					<?php
					$query = new WP_Query([
						'post_type' => 'wt_event',
						'posts_per_page' => -1,
						'meta_query' => [
							[
								'key'   => 'wt_can_register',
								'value' => '1'
							],
							[
								'key'   => 'wt_date_to',
								'value' => current_time('mysql'),
								'compare' => '>=',
								'type' => 'DATETIME'
							],
						],
						'orderby' => [
							'title' => 'ASC',
						],
					]);
					
					if ($query->have_posts())
					{
						$event = empty($_GET['event']) ? 0 : (int) $_GET['event'];
						?>
						
						<label>
							Which event are you interested in signing up for?
							<select name="event" class="wt-select">
								<option value="">Select an event</option>
						
								<?php
								while ($query->have_posts())
								{
									$query->the_post();
									?>
									
									<option value="<?php the_ID(); ?>" <?php selected($event, get_the_ID()); ?>><?php the_title(); ?></option>
									
									<?php
								}
								
								wp_reset_query();
								?>
								
							</select>
						</label>
						
						<?php
					}
					?>
					
					<?php get_template_part('template-parts/form/field-password'); ?>
					
					<p>* Compulsory field</p>
					
					<label class="wt-form-check-label">
						<input type="checkbox" name="agreed" value="1" class="wt-form-check-input -wt-checkbox" required>
						I agree to the <a href="<?php echo home_url('tnc'); ?>" target="_blank" class="wt-underline">terms and conditions</a> of the showcase programme.
<!--						<span class="form-error">Kindly accept the <a href="--><?php //echo home_url('tnc'); ?><!--" target="_blank" class="wt-underline">terms and conditions</a> to join us as a member.</span>-->
					</label>
					
					<label class="wt-form-check-label wt-margin-bottom">
						<input type="checkbox" name="meta[wt_subscribed]" value="1" class="wt-form-check-input -wt-checkbox">
						I agree to receive marketing, advertising and promotional information as per Wing Tai Retail’s <a href="#" class="wt-underline">personal data notice and consent</a>.
					</label>
					
					<?php
					if (function_exists('wt_captcha'))
						wt_captcha();
					?>
					
					<div class="text-center">
						<button type="submit" class="button secondary" data-submit="Create Account" data-submitting="Creating Account..." data-submitted="Created Account">Create Account</button>
					</div>
				</form>
				
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php get_footer();
