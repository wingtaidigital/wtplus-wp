<?php
$location = get_post_meta($post->ID, 'wt_location', true);
$display_location = get_post_meta($post->ID, 'wt_display_location', true);

if (!$display_location)
{
	$keys = ['block', 'streetName', 'floorNumber', 'unitNumber', 'buildingName', 'postalCode'];
	$values = [];
	$has_value = false;
	
	foreach ($keys as $key)
	{
		$values[$key] = get_post_meta($post->ID, 'wt_stb_address_' . $key, true);
		
		if ($values[$key])
			$has_value = true;
	}
	
	if ($has_value)
	{
		$display_location = $values['block'] . ' ' . $values['streetName'] . ' ' . $values['buildingName'] . ' ';
		
		if ($values['floorNumber'] && $values['unitNumber'])
			$display_location .= "#{$values['floorNumber']}-{$values['unitNumber']}";
		elseif ($values['floorNumber'])
			$display_location .= 'Level ' . $values['floorNumber'];
	}
	
	$display_location .= ' Singapore ' .  $values['postalCode'];
}

if (!$display_location && !empty($location['address']))
	$display_location = $location['address'];
?>

<article id="store-<?php echo $post->ID; ?>" class="wt-gutter-half wt-gutter-vertical-half wt-clear-last-child-margin" itemscope itemtype="http://schema.org/ClothingStore" data-location='<?php echo json_encode($location); ?>'>
	<?php
	$meta = get_post_meta($post->ID, 'wt_brand', true);
	
	if ($meta)
	{
		$brand = get_post($meta);
		
		if ($brand)
		{
			$mall = '';
			$malls = get_the_terms($post, 'wt_mall');
			
			if (is_array($malls))
				$mall = sanitize_text_field($malls[0]->name);
			?>
			
			<h1 itemprop="name"><?php echo sanitize_text_field($brand->post_title); ?> <?php echo $mall; ?></h1>
			
			<?php
		}
	}
	?>
	
	<?php if ($display_location) { ?>
		<p itemprop="address"><?php echo sanitize_text_field($display_location); ?></p>
	<?php } ?>
	
	<dl>
	
	<?php
	$meta = get_post_meta($post->ID, 'wt_opening_hours', true);
	
	if (!$meta)
	{
		$days = get_field('wt_stb_businessHour', $post->ID);
		
		if ($days && is_array($days))
		{
			$hours = [];
			$meta  = '';
			
			foreach ($days as $day)
			{
				$key = date('g:ia', strtotime($day['openTime'])) . ' - ' . date('g:ia', strtotime($day['closeTime']));
				
				if (!isset($hours[$key]))
				{
					$hours[$key] = [];
				}
				
				$hours[$key] = array_merge($hours[$key], $day['days']);
			}
			
			foreach ($hours as $hour => $days)
			{
				if (in_array('daily', $days))
				{
					$meta .= $hour . '<br>';
				}
				else
				{
					$days = array_map(function($str)
					{
						return ucwords(str_replace('_', ' ', $str));
					}, $days);
					
					wt_get_weekdays_range($days);
					
					if (count($days) > 1)
					{
						$range = wt_get_weekdays_range($days);
						$d = [];
						
						if ($range && is_array($range))
						{
							foreach ($range as $r)
							{
								if ($r[0] === $r[1])
									$d[] = $r[0];
								else
									$d[] = "$r[0] - $r[1]";
							}
						}
						
						if (in_array('Public Holiday', $days) !== false)
							$d[] = 'Public Holiday';
						
						$days = $d;
					}
					
					$meta .= implode(', ', $days) . ': ' . $hour . '<br>';
				}
			}
		}
	}
	
	if ($meta)
	{
		?>
		<dt>Opening hours</dt>
		<dd itemprop="openingHours"><?php echo nl2br(wp_kses_post($meta)); ?></dd>
		
		<?php
	}
	?>
	
	<?php
	$meta = get_post_meta($post->ID, 'wt_telephone', true);
	
	if ($meta)
	{
		$meta = antispambot(sanitize_text_field($meta));
		?>
		
		<dt>Contact</dt>
		<dd itemprop="telephone"><a href="tel:<?php echo $meta; ?>" class="wt-text-black"><?php echo $meta; ?></a></dd>
		
		<?php
	}
	?>
	
	<?php
	$meta = get_post_meta($post->ID, 'wt_url', true);
	
	if ($meta)
	{
		$meta = esc_url($meta);
		?>
		
		<dt>URL</dt>
		<dd itemprop="url"><a href="<?php echo $meta; ?>" target="_blank" rel="noreferrer" class="wt-text-black"><?php echo $meta; ?></a></dd>
		
		<?php
	}
	?>
	
	</dl>
</article>
