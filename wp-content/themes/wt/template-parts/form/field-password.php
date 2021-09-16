<?php
//$policy = ['password_min_length', 'password_complexity'];
//$settings = [];
//foreach ($policy as $setting)
//{
//	$settings[$setting] = get_option('options_wt_' . $setting);
//}

global $password_field_name;

if (function_exists('wt_get_password_min_length'))
{
	$settings['password_min_length'] = wt_get_password_min_length();
	$settings['password_complexity'] = wt_get_password_complexity();
	
	wp_localize_script('wt', 'wtPasswordSettings', $settings);
}

$is_author = is_author();

if ($password_field_name)
	$field_name = $password_field_name;
else
	$field_name = $is_author || is_page(['showcase/signup', 'showcase/reset-password']) ? 'user_pass' : 'NewPassword';

$fields = array(
	$field_name => array(
		'label' => (is_author() ? 'New ' : '') . 'Password' . ($is_author ? '' : '*'),
		'attr' => [
			'placeholder' => 'Please enter a password',
		]
	),
	'pass2' => array(
		'label' => 'Re-enter password' . ($is_author ? '' : '*'),
		'attr' => [
			'placeholder' => 'Please re-enter your password'
		],
		'error' => [
			'customError' => 'Passwords must match.'
		]
	),
);

if (!empty($settings['password_min_length']))
{
	$fields[$field_name] = array_merge_recursive($fields[$field_name], [
		'attr' => [
			'minlength' => $settings['password_min_length']['len']
		],
		'error' => [
			'tooShort' => $settings['password_min_length']['error']
		]
	]);
}


$complexity = '';

if (!empty($settings['password_complexity']))
{
	$fields[$field_name] = array_merge_recursive($fields[$field_name], [
		'attr' => [
			'pattern' => $settings['password_complexity']['regex'] . '.*'
		],
		'error' => [
			'patternMismatch' => 'Please enter all required characters'//$settings['password_complexity']['error']
		]
	]);
	
	$error = explode(':', $settings['password_complexity']['error']);
	
	if (!empty($error[1]))
		$complexity = 'including a <span class="wt-text-secondary">' . $error[1] . '</span>';
}


if (is_page('signup') && !is_page('showcase/signup'))
{
	$fields[$field_name] = array_merge_recursive($fields[$field_name], [
		'attr' => [
			'title' => 'Create a password to enjoy 24/7 access to your online wt+ account.'
		],
	]);
}

$first = true;

foreach ($fields as $name => $field)
{
	?>
	
	<label>
		<?php echo $field['label']; ?>
		
		<?php if (!empty($field['attr']['title'])) { ?>
			<i class="icon icon-exclamation-circle" title="<?php echo sanitize_text_field($field['attr']['title']); ?>" data-tooltip></i>
		<?php } ?>
		
		<input type="password" name="<?php echo $name; ?>" autocomplete="off" <?php echo $is_author ? '' : 'required'; ?>
			<?php foreach ($field['attr'] as $key => $value) { ?>
				<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
			<?php } ?>
		>
		
		<?php
		if ($first)
		{
			?>
			
			<small class="wt-text-weight-normal wt-text-black">
				Minimum <?php echo $settings['password_min_length']['len']; ?> characters <?php echo $complexity; ?>
			</small>
			
			<?php
			$first = false;
		}
		?>
		
		<?php if (!empty($field['error'])) { ?>
			<span class="form-error">
			<?php foreach ($field['error'] as $key => $error) { ?>
				<span class="<?php echo $key; ?>"><?php echo $error; ?></span>
			<?php } ?>
		</span>
		<?php } ?>
	</label>
	
	<?php
}
