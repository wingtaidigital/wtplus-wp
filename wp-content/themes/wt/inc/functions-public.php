<?php
defined('ABSPATH') || exit;



function wt_cta($cta)
{
	?>

	<div class="wt-cta <?php echo strpos($cta, 'color:') === false ? '' : 'wt-text-transparent'; ?>"><?php echo $cta; ?></div>

	<?php
}



function wt_form_fields($fields)
{
	foreach ($fields as $name => $field)
	{
		$parent_tag = 'label';
		$required = empty($field['attr']['required']) ? '' : 'required';
		$field['label'] = $field['label'] . ($required ? '*' : '');

		if (!empty($field['attr']['type']) && $field['attr']['type'] == 'radio')
		{
			$parent_tag = 'fieldset';
			$field['label'] = '<legend>' . $field['label'] . '</legend>';
		}

		if (empty($field['error']))
			$field['error'] = [];
		?>

		<<?php echo $parent_tag; ?>
		<?php
		if (isset($field['parent_attr']))
		{
			foreach ($field['parent_attr'] as $key => $value)
			{
				?>

				<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"

				<?php
			}
		}
		?>
		>
		<?php echo $field['label']; ?>

		<?php if (!empty($field['attr']['title'])) { ?>
			<i class="icon icon-exclamation-circle" title="<?php echo sanitize_text_field($field['attr']['title']); ?>" data-tooltip></i>
		<?php } ?>

		<?php
		if (empty($field['attr']['type']))
		{
			$selected = isset($field['attr']['value']) ? $field['attr']['value'] : '';
			?>

			<select name="<?php echo $name; ?>" class="wt-select <?php echo empty($field['attr']['class']) ? '' : $field['attr']['class']; ?>" <?php echo $required; ?>>
				<?php if (!empty($field['attr']['placeholder'])) { ?>
					<option value=""><?php echo $field['attr']['placeholder']; ?></option>
				<?php } ?>

				<?php foreach ($field['options'] as $value => $label) { ?>
					<option value="<?php echo esc_attr($value); ?>" <?php selected($selected, $value); ?>><?php echo sanitize_text_field($label); ?></option>
				<?php } ?>
			</select>

			<?php
		}
		else
		{
			if (empty($field['options']))
			{
				?>

				<input name="<?php echo $name; ?>"
				<?php foreach ($field['attr'] as $key => $value) { ?>
					<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
				<?php } ?>
				>

				<?php
			}
			else
			{
				$checked = empty($field['attr']['value']) ? '' : $field['attr']['value'];

				foreach ($field['options'] as $value => $label)
				{
					$id = "$name-$value";
					?>

					<input name="<?php echo $name; ?>" value="<?php echo $value; ?>" id="<?php echo $id; ?>" <?php checked($checked, $value); ?>
					<?php foreach ($field['attr'] as $key => $value) { ?>
					<?php echo esc_attr($key); ?>="<?php echo esc_attr($value); ?>"
				<?php } ?>
					>
					<label for="<?php echo $id; ?>"><?php echo $label; ?></label>

					<?php
				}
			}

		}
		?>

		<span class="form-error">
				<?php foreach ($field['error'] as $key => $error) { ?>
					<span class="<?php echo $key; ?>"><?php echo $error; ?></span>
				<?php } ?>
			</span>
		</<?php echo $parent_tag; ?>>

		<?php
	}
}



function wt_format_date($date = null, $context = 'display')
{
	if (empty($date))
		$date = time();

	if ($context == 'input')
	{
		$format = 'Y-m-d';
	}
	else if ($context == 'crm')
	{
		$format = 'c';
	}
	else
	{
		$format = get_option('date_format');
	}

	if (is_string($date))
		$date = strtotime($date);
//	wt_dump($date);
//	wt_dump(time());
	return date($format, $date);
}



function wt_get_content_fields()
{
	$fields = [];
	$fields['image'] = esc_url(get_sub_field('image'));
	$fields['portrait_image'] = esc_url(get_sub_field('portrait_image'));
	$fields['title'] = wp_kses_post(get_sub_field('title'));
	$fields['url'] = esc_url(get_sub_field('url'));
	$fields['cta'] = wp_kses_post(get_sub_field('cta', false));
	$fields['background_color'] = sanitize_hex_color(get_sub_field('background_color'));
	$fields['align'] = esc_attr(get_sub_field('align'));
	$fields['post'] = get_sub_field('post');

	$allowed = wp_kses_allowed_html( 'post' );
	$allowed['iframe'] = [
		'src'             => [],
		'height'          => [],
		'width'           => [],
		'frameborder'     => [],
		'allowfullscreen' => [],
	];
	$fields['content'] = wp_kses(get_sub_field('content'), $allowed);

	if ($fields['post'])
	{
		if (empty($fields['image'] ))
			$fields['image'] = esc_url(get_the_post_thumbnail_url($fields['post'], 'large'));

		if (empty($fields['portrait_image']))
		{
			$fields['portrait_image'] = get_post_meta($fields['post']->ID, 'wt_portrait_image', true);

			if ($fields['portrait_image'])
				$fields['portrait_image'] = esc_url(wp_get_attachment_image_url($fields['portrait_image']));
		}

		if (empty(wp_strip_all_tags($fields['title'])))
			$fields['title'] = '<h1>' . get_the_title($fields['post']) . '</h1>';

		if (empty($fields['content']))
			$fields['content'] = get_the_excerpt($fields['post']);

		if (empty($fields['url']))
			$fields['url'] = esc_url(get_permalink($fields['post']));
	}

	return $fields;
}



function wt_get_excerpt($excerpt, $post)
{
	if (empty($excerpt))
		return $excerpt;

	$search = get_search_query();

	if (!$search)
		return $excerpt;

	if (strpos($excerpt, $search) !== false)
		return $excerpt;

	$matched = preg_match("/[^(.?!)]*" . $search . "[^(.?!)]*[(.?!)]/i", sanitize_text_field($post->post_content), $matches);

	if (!$matched)
		return $excerpt;

	return sanitize_text_field($matches[0]);
}



function wt_mark_search($text)
{
	if (empty($text))
		return $text;
	global $post;
//	wt_dump($post);
	if (!is_main_query())
		return $text;

	$replaced = preg_replace('/' . get_search_query() . '/i', '<mark>\0</mark>', $text);

	if ($replaced)
		return $replaced;

	return $text;
}



function wt_product($post, $cta = 'FIND OUT MORE', $link_to_brand = false)
{
	?>

	<article itemscope itemtype="http://schema.org/Product" <?php post_class(); ?>>
		<div class="wt-gutter">
			<?php echo get_the_post_thumbnail($post, 'medium', ['itemprop' => "image"]); ?>

			<?php
			$brand = get_post_meta($post->ID, 'wt_brand', true);

			if ($brand)
				$brand = get_post($brand);

			if ($brand)
			{
				$brand_title = sanitize_text_field($brand->post_title);
				?>

				<h1 itemprop="brand" class="wt-margin-top"><?php echo $brand_title; ?></h1>

				<?php
			}
			?>

			<div class="wt-content" itemprop="name"><?php echo sanitize_text_field($post->post_title); ?></div>

			<?php
                $regular_price = number_format((float)get_post_meta($post->ID, 'wt_regular_price', true), 2);
                $sale_price = get_post_meta($post->ID, 'wt_sale_price', true);
                $sale_price = $sale_price ? number_format((float) $sale_price, 2) : "";
            ?>

            <?php if($sale_price) { ?>
                <div class="wt-content" itemprop="price"><s>SGD<?php echo $regular_price; ?></s></div>
                <div class="wt-content" itemprop="price" style="color:#ff4178;">SGD<?php echo $sale_price; ?></div>
            <?php } else { ?>
                <div class="wt-content" itemprop="price">SGD<?php echo $regular_price; ?></div>
            <?php } ?>

			<div class="wt-content"><a href="<?php echo get_permalink($post); ?>" class="wt-text-hover" itemprop="url"><?php echo (empty($cta) ? 'FIND OUT MORE' : $cta); ?></a></div>

			<?php
			if ($link_to_brand && $brand)
			{
				?>

				<a href="<?php echo get_permalink($brand); ?>#catalog" class="wt-text-hover wt-upper">View <?php echo $brand_title; ?> Catalog</a>

				<?php
			}
			?>
		</div>
	</article>

	<?php
}



//function wt_get_voucher_image($member_type, $voucher_code)
//{
//	$vouchers = get_transient('wt_' . $member_type . '_vouchers');
//
//	if (!$vouchers)
//		return;
//
//	$vouchers = json_decode($vouchers, true);
//
//	foreach ($vouchers as $voucher)
//	{
//		if ($voucher['VoucherCode'] == $voucher_code)
//		{
//			if (!empty($voucher['Image']))
//				return '<img src="data:image/gif;base64, ' . esc_attr($voucher['Image']) . '">';
//
//			break;
//		}
//	}
//}

//function wt_get_membership_colors()
//{
//	return [
//		'Silver' => '#A5A09A',
//		'Gold' => '#A4947C',
//		'Platinum' => '#413F3B',
//	];
//}

/*function wt_gender_field($selected = '')
{
	$genders = wt_get_genders()
	?>
	
	<label>
		Gender*
		<select name="GenderCode" class="wt-select">
			<option value="">Please select your gender</option>
			
			<?php foreach ($genders as $value => $label) { ?>
				<option value="<?php echo $value; ?>" <?php selected($selected, $value); ?>><?php echo $label; ?></option>
			<?php } ?>
		</select>
	</label>
	
	<?php
}*/
