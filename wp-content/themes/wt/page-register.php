<?php
wt_crm_logged_out_only();

get_header();
?>

<div class="wt-background-light-gray wt-padding-vertical">
	<div class="row align-center">
		<div class="column small-12 medium-8">
			<?php
			while (have_posts())
			{
				the_post();
				?>
				
				<h1 class="text-center wt-margin-bottom"><?php the_title(); ?></h1>
				
				<div class="text-center wt-background-dark-gray wt-rounded-top wt-padding-vertical wt-clear-last-child-margin">
					<?php wt_fb_button(); ?>
					
					<p>Already have an account?<br>
						<a data-open="login-modal">Click here to log in.</a></p>
				</div>
				
				<form data-route="wt/v1/crm/customers" method="post" class="wt-background-white wt-rounded-bottom wt-padding-vertical wt-gutter-x2">
					<div class="callout hide" data-success="Your profile has been created successfully. <a href='<?php echo home_url('login'); ?>'>Click to log in.</a>"></div>
					
					<?php
					$fb_user = null;
					
					if (!empty($_SESSION['wt_fb']['access_token']))
					{
						try
						{
							$fb = wt_get_fb();
							// Returns a `Facebook\FacebookResponse` object
							$response = $fb->get('/me?fields=id,first_name,last_name,email,gender', $_SESSION['wt_fb']['access_token']);//,birthday
							$fb_user = $response->getGraphUser();
			//				wt_dump($fb_user);
			//				if ($fb_user['birthday'])
			//				{
			//					$fb_user['birthday'] = $fb_user->getBirthday();
			////					wt_dump($fb_user['birthday']);
			//					$fb_user['birthday'] = date('Y-m-d', strtotime($fb_user['birthday']->date));
			//				}
						}
						catch(Facebook\Exceptions\FacebookResponseException $e)
						{
							wt_log('Graph returned an error: ' . $e->getMessage(), 'fb');
						}
						catch(Facebook\Exceptions\FacebookSDKException $e)
						{
							wt_log('Facebook SDK returned an error: ' . $e->getMessage(), 'fb');
						}
					}
					
//					$customer_number = wt_crm_search($fb_user['email']);
//
//					if ($customer_number)
//					{
//						wt_crm_set_social_profile($fb_user, $customer_number);
//					}
					
			//		wt_dump($fb_user);
					$fields = array(
						'FirstName' => array(
							'label' => 'First Name',
							'fb' => 'first_name',
							'attr' => [
								'type' => 'text',
								'autocomplete' => 'given-name',
							]
						),
						'LastName' => array(
							'label' => 'Last Name',
							'attr' => [
								'type' => 'text',
								'autocomplete' => 'family-name',
							],
							'fb' => 'last_name',
						),
						'IC' => array(
							'label' => 'NRIC',
							'attr' => [
								'type' => 'text',
								'class' => 'wt-crm-exists',
							],
							'error' => 'NRIC exists. <a href="' . home_url('login') . '">Log in here.</a>'
						),
						'Email' => array(
							'label' => 'Email',
							'attr' => [
								'type' => 'email',
								'autocomplete' => 'email',
								'class' => 'wt-crm-exists',
							],
							'fb' => 'email',
							'error' => 'Email exists. <a href="' . home_url('login') . '">Log in here.</a>'
						),
						'Mobile' => array(
							'label' => 'Mobile number',
							'attr' => [
								'type' => 'number',
								'autocomplete' => 'tel',
							]
						),
						'DOB' => array(
							'label' => 'Date of Birth',
							'attr' => [
								'placeholder' => 'Please select your date of birth',
								'type' => 'date',
								'class' => 'wt-date',
							],
			//				'fb' => 'birthday',
						),
					);
					
					foreach ($fields as $name => $field)
					{
						$value = empty($field['fb']) || empty($fb_user[$field['fb']]) ? '' : sanitize_text_field($fb_user[$field['fb']]);
						?>
						
						<label>
							<?php echo $field['label']; ?>
							<input name="<?php echo $name; ?>" value="<?php echo $value; ?>" required
								<?php foreach ($field['attr'] as $key => $value) { ?>
									<?php echo $key; ?>="<?php echo sanitize_text_field($value); ?>"
								<?php } ?>
							>
							<span class="form-error"><?php echo empty($field['error']) ? '' : $field['error']; ?></span>
						</label>
						
						<?php
					}
					
					wt_gender_field(empty($fb_user['gender']) ? '' : strtoupper($fb_user['gender'][0]));
					?>
					
					<?php get_template_part('template-parts/form/field-password'); ?>
					
					<label class="wt-form-check-label">
						<input type="checkbox" name="agreed" class="wt-form-check-input wt-checkbox" required>
						I agree to the terms and conditions of the wt+ programme.
						<span class="form-error">Kindly accept the terms and conditions to join us as a member.</span>
					</label>
					
					<label class="wt-form-check-label wt-margin-bottom">
						<input type="checkbox" name="consent" class="wt-form-check-input wt-checkbox">
						I agree to receive marketing, advertising and promotional information as per Wing Tai Retail’s personal data notice and consent.
					</label>
					
					<div class="text-center">
						<button type="submit" class="button alert" data-submit="Create Account" data-submitting="Creating Account..." data-submitted="Created Account">Create Account</button>
					</div>
				</form>
				
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php get_footer();
