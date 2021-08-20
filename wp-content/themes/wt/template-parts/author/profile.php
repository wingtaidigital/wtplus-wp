<?php defined('ABSPATH') || exit; ?>

<?php
//$user_id = $wp_query->queried_object->ID;
$user_id = get_current_user_id();
?>

<form data-route="wt/v1/users/<?php echo $user_id; ?>" method="post" autocomplete="off">
	<div class="callout hide" data-success="Your profile has been updated successfully."></div>
	
	<?php
	//		wt_dump($fb_user);
	$fields = array(
		'first_name' => [
			'label' => 'First Name',
			'attr' => [
				'type' => 'text',
//				'autocomplete' => 'given-name',
				'value' => sanitize_text_field(get_user_meta($user_id, 'first_name', true)),
				'required' => 'required'
			]
		],
		'last_name' => [
			'label' => 'Last Name',
			'attr' => [
				'type' => 'text',
//				'autocomplete' => 'family-name',
				'value' => sanitize_text_field(get_user_meta($user_id, 'last_name', true)),
				'required' => 'required'
			]
		],
		'meta[wt_nric]' => array(
			'label' => 'NRIC/FIN/Passport No',
			'attr' => [
				'type' => 'text',
				'value' => sanitize_text_field(get_user_meta($user_id, 'wt_nric', true)),
				'required' => 'required',
				'class' => 'wt-exists',
				'data-route' => 'wt/v1/user-meta-exists?key=wt_nric&value=',
			],
			'error' => [
				'customError' => "This NRIC/FIN/Passport No has already been registered by another user.",
			]
		),
		'user_email' => [
			'label' => 'Email',
			'attr' => [
				'type' => 'email',
//				'autocomplete' => 'email',
				'value' => sanitize_email($wp_query->queried_object->user_email),
				'required' => 'required',
				'class' => 'wt-exists',
				'data-route' => 'wt/v1/email-exists?email=',
//				'data-exclude' => $user_id
			],
			'error' => [
				'customError' => "This email address has already been registered by another user.",
				'typeMismatch' => 'Please enter a valid email',
				'valueMissing' => 'Please enter email'
			]
		],
		'meta[wt_mobile]' => [
			'label' => 'Mobile',
			'attr' => [
				'type' => 'tel',
//				'autocomplete' => 'tel',
				'value' => sanitize_text_field(get_user_meta($user_id, 'wt_mobile', true)),
				'required' => 'required',
				'class' => 'wt-exists',
				'data-route' => 'wt/v1/user-meta-exists?key=wt_mobile&value=',
			],
			'error' => [
				'customError' => "This mobile number has already been registered by another user.",
				'patternMismatch' => "Please enter a valid number",
			]
		],
		'meta[wt_organization]' => [
			'label' => 'Company/Organization',
			'attr' => [
				'type' => 'text',
				'value' => sanitize_text_field(get_user_meta($user_id, 'wt_organization', true)),
				'required' => 'required'
//				'class' => 'wt-exists',
//				'data-route' => 'wt/v1/username-exists?username=',
//				'data-exclude' => $user_id
			]
		],
		'meta[wt_nature]' => array(
			'label' => 'Nature of business',
			'attr' => [
				'type' => 'text',
				'value' => sanitize_text_field(get_user_meta($user_id, 'wt_nature', true)),
				'required' => 'required'
//				'placeholder' => 'What does your company/organization do?'
			],
		),
	);
	
	wt_form_fields($fields);
	
	/*foreach ($fields as $name => $field)
	{
		?>
		
		<label>
			<?php echo $field['label']; ?>*
			<input name="<?php echo $name; ?>" required
				<?php foreach ($field['attr'] as $key => $value) { ?>
			       <?php echo $key; ?>="<?php echo $value; ?>"
				<?php } ?>
			>
			<span class="form-error"><?php echo empty($field['error']) ? '' : $field['error']; ?></span>
		</label>
		
		<?php
	}*/
	?>
	
	<?php get_template_part('template-parts/form/field-password'); ?>
	
	<div class="text-center">
		<button type="submit" class="button secondary" data-submit="Save" data-submitting="Saving..." data-submitted="Save" data-enable="1">Save</button>
	</div>
</form>
