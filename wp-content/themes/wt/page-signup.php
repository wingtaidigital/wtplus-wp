<?php
defined('ABSPATH') || exit;

wt_crm_logged_out_only();

get_header();
?>

<div class="wt-background-light-gray wt-account">
	<div class="row align-center">
		<div class="column small-12 large-9 wt-gutter-bottom-x2">
			<?php
			while (have_posts())
			{
				the_post();
				?>

				<h1 class="text-center wt-h3 wt-gutter-vertical-x2"><?php the_title(); ?></h1>

				<?php //<p class="wt-gutter-bottom-x2" style="color: #f00; font-weight: bold">Our member portal will be temporarily down for maintenance today from 6am to 10am. We apologize for the inconvenience caused.</p> ?>

				<?php /* <div class="text-center wt-background-gray-2 wt-rounded-top wt-gutter-vertical-x2 wt-clear-last-child-margin">
					<p class="lead wt-text-white wt-h6">Sign up via Facebook for faster access</p> */ ?>

				<div class="text-center">
					<?php wt_fb_button(false, 'Sign up with Facebook'); ?>
					
					<div class="wt-horizontal-lines wt-h3 wt-gutter-vertical-x2">OR</div>
				</div>
				
				<?php /* <p class="wt-text-black">Already have an account?<br>
						<a class="wt-text-hover" data-open="login-modal">Click here to log in.</a></p>
				</div> */ ?>

				<form data-route="wt/v1/crm/customers" method="post" class="wt-background-white wt-rounded-bottom wt-gutter-x2 wt-gutter-vertical-x2" autocomplete="off">
					<h1 class="text-center wt-h3 wt-margin-bottom">Create an account</h1>

					<div class="callout hide" data-success="Your profile has been created successfully. <a href='<?php echo home_url('login'); ?>'>Click to log in.</a>"></div>

					<?php
					$fb_user = wt_get_fb_user('first_name,last_name,email');

					$fields = array(
						'Email' => array(
							'label' => 'Email',
							'attr' => [
								'type' => 'email',
//								'autocomplete' => 'email',
								'placeholder' => 'A welcome email will be sent to you',
								'title' => 'Your email address will be used as your login ID to access your online wt+ account',
								'class' => 'wt-exists',
								'data-route' => 'wt/v1/crm/exists?field=email&value=',
								'required' => 'required'
							],
							'error' => [
								'customError' => "This email address has already been registered. <a data-open='login-modal'>Log in here.</a>",
								'typeMismatch' => 'Please enter a valid email',
								'valueMissing' => 'Please enter email'
							]
						),
						'confirm_email' => array(
							'label' => 'Re-enter Email',
							'attr' => [
								'type' => 'email',
//								'autocomplete' => 'email',
								'placeholder' => 'Please re-enter your email address',
//								'title' => 'Your email address will be used as your login ID to access your online wt+ account',
//								'class' => 'wt-exists',
//								'data-route' => 'wt/v1/crm/exists?field=email&value=',
								'required' => 'required'
							],
							'error' => [
								'customError' => "Emails must match",
								'typeMismatch' => 'Please enter a valid email',
								'valueMissing' => 'Please enter email'
							]
						),
						'DynamicFieldLists[FirstName]' => array(
							'label' => 'First Name',
//							'fb' => 'first_name',
							'attr' => [
								'value' => empty($fb_user['first_name']) ? '' : sanitize_text_field($fb_user['first_name']),
								'type' => 'text',
//								'autocomplete' => 'given-name',
								'placeholder' => 'Please fill in your first name',
								'required' => 'required',
								'maxlength' => 40
							]
						),
						'DynamicFieldLists[LastName]' => [
							'label' => 'Last Name',
							'attr' => [
								'value' => empty($fb_user['last_name']) ? '' : sanitize_text_field($fb_user['last_name']),
								'type' => 'text',
//								'autocomplete' => 'family-name',
								'placeholder' => 'Please fill in your last name',
								'required' => 'required',
								'maxlength' => 59
							],
						],
						'DynamicColumnLists[Gender]' => [
							'label' => 'Gender',
							'attr' => [
								'type'        => 'radio',
								'value'       => empty($fb_user['gender']) ? '' : strtoupper($fb_user['gender'][0]),
								'placeholder' => 'Please select your gender',
								'required'    => 'required',
								//'autocomplete' => 'sex'
							],
							'options' => wt_get_genders(),
						],
						'DynamicColumnLists[Country]' => array(
							'label' => 'Country of Residence',
							'attr'  => [
								'value'       => 'CCSG',
								'placeholder' => 'Please select your country',
								'required'    => 'required',
								'class'       => 'wt-country'
								//								'autocomplete' => 'country'
							],
							'options' => wt_crm_get_system_codes('Country')
						),
					);

					if (!empty($fb_user['email']))
					{
						$fields['Email']['attr']['value'] = sanitize_email($fb_user['email']);
						$fields['Email']['attr']['readonly'] = 'readonly';

						unset($fields['confirm_email']);
					}

					wt_form_fields($fields);
					?>

					<?php wt_mobile_fieldset(); ?>

					<?php
					$today = current_time('Y-m-d');
					$fields = array(
						'DOB' => array(
							'label' => 'Date of Birth',
							'attr' => [
								'placeholder' => 'yyyy-mm-dd',
								'type'        => 'date',
								'max'         => $today,
								'required'    => 'required',
								//								'autocomplete' => 'bday',
								'class' => 'wt-date',
							],
						),
					);

					wt_form_fields($fields);

					wt_brand_fieldset();

					$fields = array(
						'DynamicFieldLists[HaveChild]' => array(
							'label'   => 'Do you have children age 12 years and under?*',
							'attr'    => [
								'type'        => 'radio',
								'placeholder' => 'Please select',
								'required'    => 'required',
							],
							'options' => [
								//								'' => '',
								'Yes' => 'Yes',
								'No' => 'No',
							]
						),
					);

					wt_form_fields($fields);
					?>

					<?php
					$fields = array(
						'ReferrerCode' => array(
							'label'   => 'Referrer Code',
							'attr'    => [
								'type' => 'text',
								'value' => empty($_GET['ReferrerCode']) ? '' : sanitize_text_field($_GET['ReferrerCode']),
								'placeholder' => 'Referrer Code is case sensitive',
							],
						),
					);

					wt_form_fields($fields);
					?>

					<div class="wt-margin-bottom">
<!--						<a class="wt-margin-bottom wt-bold" data-toggle="optional optional-toggler" data-toggler=".is-active" id="optional-toggler">Optional fields</a>-->

						<div id="optional" -class="hide" data-toggler=".hide">
							<div class="wt-sg">
								<label>
                  Postal Code*  <small>(If you are a tourist, please input 000000)</small>
									<input type="text" name="DynamicColumnLists[PostalCode]" placeholder="Please enter postal code" minlength="6" maxlength="6" required pattern=".{6}" title="6 digits">
								</label>

								<fieldset>
									<legend>Mailing Address</legend>

									<div class="row">
										<?php
										$fields = [
											'Block'      => 'Block number',
											'Level'      => 'Level',
											'Unit'       => 'Unit number',
											'Street'     => 'Street name',
											'Building'   => 'Building name',
										];

										foreach ($fields as $name => $label)
										{
											?>

											<div class="column small-12 <?php echo in_array($name, ['Block', 'Level', 'Unit']) ? 'medium-4' : 'medium-6'; ?>">
												<input type="text" name="DynamicColumnLists[<?php echo $name; ?>]" placeholder="<?php echo $label; ?>" maxlength="255">
											</div>

											<?php
										}
										?>
									</div>
								</fieldset>
							</div>

							<div class="hide wt-not-sg">
								<label>
									ZIP / Postal Code
									<input type="text" name="DynamicColumnLists[PostalCode]" placeholder="Please enter zip / postal code" disabled maxlength="256">
								</label>

								<fieldset>
									<legend>Mailing Address</legend>

									<?php
									for ($i = 1; $i < 4; $i++)
									{
										?>

										<input type="text" name="DynamicColumnLists[Address<?php echo $i; ?>]" placeholder="Address <?php echo $i; ?>" maxlength="256">

										<?php
									}
									?>
								</fieldset>
							</div>
						</div>
					</div>

					<label class="wt-form-check-label">
						<input type="checkbox" name="DynamicFieldLists[ConsentMarketingPromo]" value="Yes" class="wt-form-check-input -wt-checkbox wt-toggle-display"  data-toggle="contact-preferences">
						Agree to receive marketing, advertising, and promotional information from Wing Tai Clothing
					</label>

					<!--<fieldset class="">
						<legend class="wt-text-weight-normal">Agree to receive marketing, advertising, and promotional information from Wing Tai Retail*</legend>
						<input type="radio" name="DynamicFieldLists[ConsentMarketingPromo]" value="Yes" required class="wt-toggle-display" data-toggle="contact-preferences" id="subscribe"><label for="subscribe">Yes</label>
						<input type="radio" name="DynamicFieldLists[ConsentMarketingPromo]" value="No" required class="wt-toggle-hide"  data-toggle="contact-preferences" id="unsubscribe"><label for="unsubscribe">No</label>
						-->
						<fieldset id="contact-preferences" class="hide wt-required wt-margin-top -wt-margin-bottom-0">
							<?php wt_notify_checkboxes(); ?>

							<span class="form-error">
								<span class="valueMissing">Please select at least one contact preference</span>
							</span>
						</fieldset>
					<!--</fieldset>-->

					<label class="wt-form-check-label">
						<input type="checkbox" name="DynamicFieldLists[TnC]" value="Yes" class="wt-form-check-input -wt-checkbox" required>
						Agree to  <a href="<?php echo home_url('tnc'); ?>" target="_blank" class="wt-text-hover">terms and conditions</a>
						and <a href="<?php echo home_url('pdpa'); ?>" target="_blank" class="wt-text-hover">privacy policy</a> of the wt+ programme*
					</label>

					<?php /*if (!$has_referral) { ?>
						<label class="wt-form-check-label wt-receipt-toggler">
							<input type="checkbox" name="no_receipt" value="1" class="wt-form-check-input wt-checkbox" required>
							I am not uploading any receipt. I acknowledge that I will not be able to upload any other receipt once my membership account is created.
						</label>
					<?php }*/ ?>

					<p class="wt-gutter-vertical">* Compulsory field</p>

					<?php
					if (function_exists('wt_captcha'))
						wt_captcha();
					?>

					<div class="text-center">
						<button type="submit" class="button secondary" data-submit="Create Account" data-submitting="Creating Account..." data-submitted="Created Account. Redirecting...">Create Account</button>
					</div>

					<?php
					if (!empty($_GET['outlet']))
					{
						$_GET['outlet'] = sanitize_text_field($_GET['outlet']);
						?>

						<input type="hidden" name="OutletCode" value="<?php echo $_GET['outlet']; ?>">
						<input type="hidden" name="signuplocation" value="<?php echo $_GET['outlet']; ?>">

						<?php
					}
					?>

					<?php if (!empty($_GET['partner'])) { ?>
						<input type="hidden" name="DynamicFieldLists[SignUpByPartner]" value="<?php echo sanitize_text_field($_GET['partner']); ?>">
					<?php } ?>

					<?php if (!empty($_GET['campaign'])) { ?>
						<input type="hidden" name="RegistrationCampaignCode" value="<?php echo sanitize_text_field($_GET['campaign']); ?>">
					<?php } ?>
				</form>

				<?php
			}
			?>
		</div>
	</div>
</div>

<?php get_footer();
