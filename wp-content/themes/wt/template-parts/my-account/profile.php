<?php defined('ABSPATH') || exit; ?>

<?php
global $profile, $card;

//wt_dump($profile);

if (is_wp_error($profile))
{
	?>

	Server error occurred. Please try refreshing the browser.

	<?php
}
//elseif ($card['MembershipStatusCode'] !== 'ACTIVE')
//{
	?>

	<form data-route="wt/v1/crm/customers/<?php echo $_SESSION['wt_crm']['MemberID']; ?>" method="post" class="wt-gutter-bottom-x2" autocomplete="off">
		<h1 class="wt-h6 wt-upper wt-margin-bottom-x2">Update Profile</h1>

		<div class="callout hide" data-success="Your personal information has been updated."></div>

		<?php
		$fields = array(
			'Email' => array(
				'label' => 'Email',
				'attr' => [
					'value' => $profile['Email'],
					'type' => 'email',
					'placeholder' => 'Fill me in to receive exclusive wt+ promotions!',
//					'required' => 'required',
//					'disabled' => 'disabled',
//					'autocomplete' => 'email',
//					'class' => 'wt-exists',
//					'data-route' => 'wt/v1/crm/exists?exclude=' . $profile['CustomerNumber'] . '&nric_or_email=',
				]
			),
			'DynamicFieldLists[FirstName]' => array(
				'label' => 'First Name',
				'attr' => [
					'value' => wt_crm_get_list_field_value($profile['DynamicFieldLists'], 'FirstName'),
					'type' => 'text',
//					'autocomplete' => 'given-name',
					'required' => 'required',
					'maxlength' => 40
				]
			),
			'DynamicFieldLists[LastName]' => array(
				'label' => 'Last Name',
				'attr' => [
					'value' => wt_crm_get_list_field_value($profile['DynamicFieldLists'], 'LastName'),
					'type' => 'text',
//					'autocomplete' => 'family-name',
					'required' => 'required',
					'maxlength' => 59
				]
			),
			'DynamicColumnLists[Gender]' => [
				'label'   => 'Gender',
				'attr'    => [
					'type'        => 'radio',
					'value'       => $profile['Gender'],
					'placeholder' => 'Please select your gender',
					'required'    => 'required',
//					'autocomplete' => 'sex'
				],
				'options' => wt_get_genders()
			],
			'DynamicColumnLists[Country]' => array(
				'label' => 'Country of Residence',
				'attr' => [
					'value' => empty($profile['Country']) ? 'CCSG' : $profile['Country'],
					'placeholder' => 'Please select your country',
					'required' => 'required',
					'class' => 'wt-country'
//					'autocomplete' => 'country'
				],
				'options' => wt_crm_get_system_codes('Country') //TODO remove CHINA
			),
		);

		if (!empty($profile['Email']) && function_exists('wt_is_dummy_email') && wt_is_dummy_email($profile['Email']))
		{
			unset($fields['Email']['attr']['disabled']);

			$fields['Email']['attr']['value'] = '';
		}

		wt_form_fields($fields);

		wt_mobile_fieldset($profile);

		$fields = [
			'DOB' => array(
				'label' => 'Date of Birth',
				'attr' => [
					'value' => empty($profile['DOB']) ? '' : wt_crm_format_date($profile['DOB'], 'input'),
					'type' => 'date',
					'max' => current_time('Y-m-d'),
					'required' => 'required',
//					'autocomplete' => 'bday'
				]
			),

		];

		if (!empty($profile['DOB']))
			$fields['DOB']['attr']['disabled'] = 'disabled';

		wt_form_fields($fields);
		?>

		<?php
		if (empty($profile['fields']['BrandPreference']))
			$profile['fields']['BrandPreference'] = '';

		wt_brand_fieldset($profile['fields']['BrandPreference']);
		?>

		<?php
		$have_children = wt_crm_get_list_field_value($profile['DynamicFieldLists'], 'HaveChild');
//		wt_dump($have_children);
		$fields = [
			'DynamicFieldLists[HaveChild]' => array(
				'label'   => 'Do you have children age 12 years and under?',
				'attr'    => [
					'value'    => empty($have_children) || $have_children == 'No' ? 'No' : 'Yes',
					'required' => 'required',
//					'class'    => 'wt-children'
				],
				'options' => [
					'Yes' => 'Yes',
					'No'  => 'No',
				]
			),
		];

		wt_form_fields($fields);
		?>
		<?php /*
		<div id="children" class="table-scroll <?php echo $have_children == 1 ? '' : 'hide'; ?>">
			<table>
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Date of Birth</th>
					</tr>
				</thead>
				<tbody id="child-rows">
					<?php
					$children = wt_crm_get_children($profile['DynamicFieldLists']);
					$i = 0;

					if ($children)
					{
						foreach ($children as $child)
						{
							wt_child_row($i, $child, $have_children);
							$i++;
						}
					}
					else
					{
						wt_child_row(0, [], $have_children);
					}
					?>
				</tbody>
			</table>

			<script type="text/template" id="child-template" data-append-to="child-rows">
				<?php wt_child_row(); ?>
			</script>

			<div class="text-right">
				<button type="button" class="button small wt-clone" id="add-child" data-template="child-template" data-max="6" <?php echo ($i > 5 ? 'disabled' : ''); ?>>Add</button>
			</div>
		</div>
		*/ ?>
		<?php $disabled = $profile['Country'] === 'CCSG' ? '' : 'disabled'; ?>
		<div class="wt-sg <?php echo $profile['Country'] === 'CCSG' ? '' : 'hide'; ?>">
			<label>
				Postal Code
				<input type="text" name="DynamicColumnLists[PostalCode]" value="<?php echo empty($profile['PostalCode']) ? '' : sanitize_text_field($profile['PostalCode']); ?>" placeholder="Please enter postal code" <?php echo $disabled; ?>>
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
							<input type="text" name="DynamicColumnLists[<?php echo $name; ?>]" value="<?php echo empty($profile[$name]) ? '' : sanitize_text_field($profile[$name]); ?>" placeholder="<?php echo $label; ?>" <?php echo $disabled; ?>>
						</div>

						<?php
					}
					?>
				</div>
			</fieldset>
		</div>

		<?php $disabled = $profile['Country'] === 'CCSG' ? 'disabled' : ''; ?>
		<div class="wt-not-sg <?php echo $profile['Country'] === 'CCSG' ? 'hide' : ''; ?>">
			<label>
				ZIP / Postal Code
				<input type="text" name="DynamicColumnLists[PostalCode]" value="<?php echo empty($profile['PostalCode']) ? '' : sanitize_text_field($profile['PostalCode']); ?>" placeholder="Please enter zip / postal code" <?php echo $disabled; ?>>
			</label>

			<fieldset>
				<legend>Mailing Address</legend>

				<?php
				for ($i = 1; $i < 4; $i++)
				{
					?>

					<input type="text" name="DynamicColumnLists[Address<?php echo $i; ?>]" value="<?php echo empty($profile["Address$i"]) ? '' : sanitize_text_field($profile["Address$i"]); ?>" placeholder="Address <?php echo $i; ?>" <?php echo $disabled; ?>>

					<?php
				}
				?>
			</fieldset>
		</div>

		<fieldset>
			<legend>Communication Preferences</legend>
			<?php wt_notify_checkboxes($profile); ?>
		</fieldset>

		<?php /*<fieldset>
			<legend>Communication Types</legend>

			<?php
			$lists = wt_crm_get_mailing_lists();

			if (is_array($profile['MailingLists']))
				$subscribed = array_column($profile['MailingLists'], 'Name');
			else
				$subscribed = [];

			foreach ($lists as $list)
			{
				?>

				<input type="checkbox" name="MailingLists[]" value="<?php echo $list; ?>" id="<?php echo $list; ?>" <?php echo in_array($list, $subscribed) ? 'checked' : ''; ?>><label for="<?php echo $list; ?>"><?php echo $list; ?></label>

				<?php
			}
			?>
		</fieldset> */ ?>

		<?php if ($card['MembershipStatusCode'] === 'PENDING PROFILE UPDATE') { ?>
			<?php
			$GLOBALS['password_field_name'] = 'Password';
			get_template_part('template-parts/form/field-password');
			?>
		<?php } ?>

		<div class="text-center">
			<button type="submit" class="button secondary" data-submit="Save" data-submitting="Saving..." data-submitted="Save" data-enable="1">Save</button>
		</div>
	</form>

	<?php
//}
?>

<?php if ($card['MembershipStatusCode'] !== 'PENDING PROFILE UPDATE') { ?>
	<form data-route="wt/v1/crm/customers/<?php echo $_SESSION['wt_crm']['MemberID']; ?>/password" method="post" id="password" autocomplete="off">
		<h1 class="wt-h6 wt-upper wt-margin-bottom-x2">Change Password</h1>

		<div class="callout hide" data-success="Your password has been updated."></div>

		<label>
			Current Password
			<input type="password" name="OldPassword" autocomplete="off" required>
		</label>

		<?php get_template_part('template-parts/form/field-password'); ?>

		<div class="text-center">
			<button type="submit" class="button secondary" data-submit="Save" data-submitting="Saving..." data-submitted="Save" data-enable="1">Save</button>
		</div>
	</form>
<?php } ?>
