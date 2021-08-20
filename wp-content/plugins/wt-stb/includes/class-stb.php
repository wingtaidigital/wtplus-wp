<?php
namespace WT;

class STB
{
	const API_URL = 'https://tih-api.stb.gov.sg/';

	const TAGS = ['Clothes', 'Fashion', 'International Brands', 'Lifestyles', 'Shopping', 'Shops'];

	private static $days = [];

	public function __construct()
	{
		add_action('init', [$this, 'register_tag']);

		if (is_admin())
		{
			add_action('admin_menu', [$this, 'add_menu_page']);

			add_action('admin_notices', [$this, 'show_admin_notices']);

			add_action('publish_wt_store', [$this, 'update_shop']);

			add_action('trash_wt_store', [$this, 'delete_shop']);

			add_action('acf/save_post', [$this, 'update_post'], 99);
		}
	}

	public static function add_menu_page()
	{
		if (get_current_user_id() !== 1)
			return;

		$title = 'STB';

		add_submenu_page(
			'tools.php',
			$title,
			$title,
			'administrator',
			'stb',
			function() use ($title)
			{
				?>

				<div class="wrap">
					<form method="post" enctype="multipart/form-data">
						<h1><?php echo $title; ?></h1>

						<p>
							<input type="submit" name="action" value="Format Address">
							<input type="submit" name="action" value="Format Business Hour">
						</p>

<!--						<p>-->
<!--							<input type="submit" name="action" value="Insert Tags">-->
<!--						</p>-->

						<p>
							<input type="submit" name="action" value="Create Shops">
						</p>

<!--						<input type="submit" name="action" value="Import Tags">-->
<!--						<input type="submit" name="action" value="Import Nearest MRT">-->
					</form>
				</div>

				<?php
				if (!empty($_POST['action']))
				{
					switch ($_POST['action'])
					{
						case 'Create Shops':
							global $wpdb;

							$stores = $wpdb->get_results("
								SELECT ID, post_title, post_content
								FROM $wpdb->posts
								WHERE post_type = 'wt_store' AND post_status = 'publish' AND post_title <> 'Auto Draft'
									AND NOT EXISTS(
									    SELECT 1
									    FROM $wpdb->postmeta
									    WHERE post_id = ID AND meta_key = 'wt_stb_uuid' AND meta_value <> ''
									)
							");

							self::update_shops($stores);

							break;

						case 'Format Address':
							global $wpdb;

							$stores = $wpdb->get_results("
								SELECT ID, TRIM(d.meta_value) display, m.meta_value map
								FROM $wpdb->posts
								    LEFT JOIN $wpdb->postmeta d ON ID = d.post_id AND d.meta_key = 'wt_display_location'
								    LEFT JOIN $wpdb->postmeta m ON ID = m.post_id AND m.meta_key = 'wt_location'
								WHERE post_type = 'wt_store' AND post_status = 'publish'
							");//AND ID = 19424

							foreach ($stores as $store)
							{
								wt_dump($store);

								$address = $store->display;
								$map = maybe_unserialize($store->map);

								if (empty($address) && !empty($map['address']))
								{
									$address = $map['address'];
								}

								if (empty($address))
								{
									continue;
								}

								/*preg_match('/^(\d++)/x', $address, $matches);

								if (empty($matches[1]))
								{
									wt_dump($matches);
									return;
								}
								else
								{
									$block = $matches[1];
								}*/

								$postal_code = self::get_postal_code($address);
								$mall = self::get_building_name($store->ID);

								if (empty($postal_code) && empty($mall))
								{
									wt_dump('No postal code & mall');
									return;
								}

								$return_geom = empty($map['lat']) || empty($map['lng']) ? 'Y' : 'N';
								$data = self::get_address(empty($postal_code) ? $mall : $postal_code, $return_geom);

								if (is_wp_error($data))
								{
									wt_dump($data);
									return $data;
								}

								$address_values = $data['address'];

								if (!empty($mall))
								{
									$address_values['buildingName'] = $mall;
								}

								preg_match('/(\#|[lL]evel |L)(\w++)/', $address, $matches);

								if (empty($matches[2]))
								{
									wt_dump($matches);
								}
								else
								{
									$address_values['floorNumber'] = $matches[2];
								}

								preg_match('/-([\d|\/]++)/', $address, $matches);

								if (empty($matches[1]))
								{
									wt_dump($matches);
								}
								else
								{
									$address_values['unitNumber'] = $matches[1];
								}

								wt_dump($address_values);

								foreach ($address_values as $key => $value)
								{
									$key = 'wt_stb_address_' . $key;
//									$current = get_post_meta($store->ID, $key, true);

//									if (!$current)
										update_post_meta($store->ID, $key, $value);
								}

								if (!empty($data['location']))
								{
									if (!is_array($map))
										$map = [];

									$location = self::map_address($data['location'], 'stb', 'acf');
									$map = array_merge($map, $location['location']);

									update_post_meta($store->ID, 'wt_location', $map);
								}

								echo '<hr>';
							}

							break;

						case 'Format Business Hour':
							global $wpdb;

							$stores = $wpdb->get_results("
								SELECT ID, TRIM(m.meta_value) hours
								FROM $wpdb->posts
								    JOIN $wpdb->postmeta m ON ID = m.post_id AND m.meta_key = 'wt_opening_hours' AND m.meta_value <> ''
								WHERE post_type = 'wt_store' AND post_status = 'publish'
									AND NOT EXISTS(
									    SELECT 1
									    FROM $wpdb->postmeta s
									    WHERE ID = s.post_id AND s.meta_key = 'wt_stb_businessHour' AND s.meta_value <> ''
								    )
							");

							foreach ($stores as $i => $store)
							{
								wt_dump($store);
								$data = [];

								if (ctype_digit($store->hours[0]))
								{
									$times = self::get_time_range($store->hours);

									if (is_wp_error($times))
									{
										wt_dump($store->hours);
										return;
									}

									$data[] = [
										'days' => ['daily'],
										'openTime' => $times[0],
										'closeTime' => $times[1],
									];
								}
								else
								{
									$lines = array_filter(explode(PHP_EOL, $store->hours));

									if (count($lines) === 1)
									{
										$lines = array_filter(explode('   ', $store->hours));
									}

									foreach ($lines as $line)
									{
										$line = trim($line);

										if (empty($line))
											continue;

//										echo $line . '<br>';
										preg_match('/\d/', $line, $matches, PREG_OFFSET_CAPTURE);

										if (empty($matches[0][1]))
										{
											wt_dump($line);
											wt_dump($matches);
											return;
										}

										$t = substr($line, $matches[0][1]);
										$times = self::get_time_range($t);

										if (is_wp_error($times))
										{
											wt_dump($store->hours);
											return;
										}

										$d = substr($line, 0, $matches[0][1]);
										$d = trim($d, ": \t\n\r\0\x0B");
										$d = explode(',', $d);
										$days = [];

										foreach ($d as $da)
										{
											$da = preg_split('/[-|–|&]/', $da, 0, PREG_SPLIT_NO_EMPTY);

											if (!$da)
												continue;

											foreach ($da as $j => $day)
											{
												$da[$j] = trim($day);

												if ($da[$j] !== 'Weekends/PH')
													$da[$j] = substr($da[$j], 0, 3);
											}

											if (count($da) === 2)
											{
												if (in_array('Weekends/PH', $da))
												{
													$days[] = 'saturday';
													$days[] = 'sunday';
													$days[] = 'public_holiday';
													$days[] = $da[1] === 'Weekends/PH' ? self::get_weekday_key($da[0]) : self::get_weekday_key($da[1]);
												}
												else
												{
													try
													{
														$start = new \DateTime('next ' . $da[0]);
														$end   = new \DateTime($start->format('Y-m-d') . ' next ' . $da[1]);
													}
													catch (\Exception $e)
													{
														wt_dump($da);
														wt_dump($e);

														return;
													}

													for ($j = $start; $j <= $end; $j->modify('+1 day'))
													{
														$days[] = self::get_weekday_key($j->format("D"));
													}
												}
											}
											else
											{
												if ($da[0] === 'Weekends/PH')
												{
													$days[] = 'saturday';
													$days[] = 'sunday';
													$days[] = 'public_holiday';
												}
												else
												{
													$days[] = self::get_weekday_key($da[0]);
												}
											}
										}

										$data[] = [
											'days' => $days,
											'openTime' => $times[0],
											'closeTime' => $times[1],
										];
									}
								}

								update_field('wt_stb_businessHour', $data, $store->ID);

								wt_dump($data);
								echo '<hr>';
							}

							break;

						/*case 'Import Tags':
							self::import_tags();
							wt_dump(implode(', ', $GLOBALS['tags']));
							break;*/

						/*case 'Insert Tags':
							global $wpdb;

							$tags = array_merge(self::TAGS, ['Boutiques', 'Luxury', 'Premium', 'Kids']);

							$terms = get_terms([
								'taxonomy' => 'wt_region'
							]);
							$regions = array_column($terms, 'name');
							$tags = array_merge($tags, $regions);
//							$streets = $wpdb->get_col("
//								SELECT DISTINCT meta_value
//								FROM $wpdb->postmeta
//								WHERE meta_key = 'wt_stb_address_streetName' and meta_value <> ''
//							");
//							$tags = array_merge($tags, $streets);

							foreach ($tags as $tag)
							{
								wp_insert_term($tag, 'wt_stb_tag');
							}

							break;*/

						/*case 'Import Nearest MRT':
							global $wpdb;

							$locations = $wpdb->get_results("
								SELECT ID, m.meta_value map
								FROM $wpdb->posts
								    LEFT JOIN $wpdb->postmeta m ON ID = m.post_id AND m.meta_key = 'wt_location'
								WHERE post_type = 'wt_store' AND post_status = 'publish'
							");

							foreach ($locations as $location)
							{
								$response = wp_remote_get('https://www.onemap.sg/nearby-api/getNearestMrtStops?lat=1.3332933447345081&lon=103.74327874234149', [
									'body' => [
										'lat' => 'client_credentials'
									]
								]);
							}
							break;*/
					}
				}
			}
		);
	}

	public static function delete_shop($post_id, $post = null)
	{
		$uuid = get_post_meta($post_id, 'wt_stb_uuid', true);

		if ($uuid)
			self::request('admin/shops' . '/' . $uuid, 'DELETE', ['language' => 'EN'], true);
	}

	public static function update_shop($store_id, $store = null, $brand = null)
	{
		if (!$store)
			$store = get_post($store_id);

		if (!$store || $store->post_title === 'Auto Draft')
			return;

		$tags = [];
		$terms = get_the_terms($store, 'wt_stb_tag');

		if ($terms && is_array($terms))
		{
			$tags = array_map(function($term)
			{
				return sanitize_text_field($term->name);
			}, $terms);
		}
		else
		{
			$tags = self::TAGS;
			$street = get_post_meta($store->ID, 'wt_stb_address_streetName', true);

			if ($street)
				$tags[] = $street;

			$regions = get_the_terms($store, 'wt_region');

			if ($regions && is_array($regions))
			{
				foreach ($regions as $region)
					$tags[] = $region->name;
			}

			wp_set_post_terms($store->ID, $tags, 'wt_stb_tag');
		}

		$url = esc_url(get_post_meta($store->ID, 'wt_url', true));
		$data = [
			'uuid' => sanitize_text_field(get_post_meta($store->ID, 'wt_stb_uuid', true)),
			'name' => sanitize_text_field($store->post_title),
			'type' => 'Boutiques',
			'tags' => $tags,
			'description' => '-',
			'body' => wp_kses_post($store->post_content),
			'notes' => sanitize_text_field(get_post_meta($store->ID, 'wt_stb_notes', true)),
			'nearestMrtStation' => sanitize_text_field(get_post_meta($store->ID, 'wt_stb_nearestMrtStation', true)),
			'officialWebsite' => $url ? $url : 'https://www.wtplus.com.sg/',
			'officialEmail' => 'help@wtplus.com.sg'
		];

		if (!$brand)
			$brand = get_field('wt_brand', $store->ID);

		if ($brand)
		{
			$description = get_post_meta($brand->ID, 'wt_stb_description', true);

			if ($description)
				$data['description'] = sanitize_text_field($description);

			if (empty($data['body']))
			{
				$data['body'] = empty($brand->post_content) ? '-' : wp_kses_post($brand->post_content);
			}

			$terms = get_the_terms($brand, 'wt_stb_tag');

			if ($terms && is_array($terms))
			{
				$names = array_map(function($term)
				{
					return sanitize_text_field($term->name);
				}, $terms);

				$data['tags'] = array_merge($data['tags'], $names);
			}

			$images = get_field('wt_header_image', $brand->ID);

			if (is_array($images))
			{
				foreach ($images as $image)
				{
					$data['images'][] = $data['thumbnails'][] = ['url' => $image['url']];
				}
			}
		}

		$location = get_post_meta($store->ID, 'wt_location', true);
		$address = get_post_meta($store->ID, 'wt_display_location', true);

		if (!$address && !empty($location['address']))
			$address = $location['address'];

		$postal_code = $address ? self::get_postal_code($address) : '';
		$mall = self::get_building_name($store->ID);
		$search = empty($postal_code) ? $mall : $postal_code;

		if ((empty($location['lat']) || empty($location['lng'])) && $search)
		{
			$om = self::get_address($search, 'Y');

			if (!is_wp_error($om))
			{
				$acf = self::map_address($om['location'], 'stb', 'acf');

				if (!is_array($location))
					$location = [];

				$location = array_merge($location, $acf['location']);

				update_post_meta($store->ID, 'wt_location', $location);
			}
		}

		if (!empty($location['lat']) && !empty($location['lng']))
		{
			$data['location'] = [
				'latitude' => substr($location['lat'], 0, 9),
				'longitude' => substr($location['lng'], 0 ,9)
			];
		}

		$address_keys = [
			'block',
			'streetName',
			'floorNumber',
			'unitNumber',
			'buildingName',
			'postalCode'
		];
		$has_address = false;

		foreach ($address_keys as $key)
		{
			$data['address'][$key] = sanitize_text_field(get_post_meta($store->ID, 'wt_stb_address_' . $key, true));

			if ($data['address'][$key])
				$has_address = true;
		}

		if (!$has_address && $search)
		{
			if (!isset($om))
				$om = self::get_address($search);

			if (!is_wp_error($om))
			{
				if ($mall)
					$om['address']['buildingName'] = $mall;

				foreach ($address_keys as $key)
				{
					$value = $om['address'][$key];

					update_post_meta($store->ID, 'wt_stb_address_' . $key, $value);

					$data['address'][$key] = update_post_meta($store->ID, 'wt_stb_address_' . $key, $value);
				}
			}
		}

		$contact = sanitize_text_field(get_post_meta($store->ID, 'wt_telephone', true));

		if ($contact)
		{
			if ($contact[0] !== '+')
				$contact = '+65 ' . $contact;

			$data['contact'] = ['primaryContactNo' => $contact];
		}

		$hours = get_field('wt_stb_businessHour', $store->ID);

		if ($hours)
		{
			$data['businessHour'] = [];

			foreach ($hours as $hour)
			{
				foreach ($hour['days'] as $day)
				{
					$data['businessHour'][] = [
						'day'      => $day,
						'openTime' => $hour['openTime'],
						'closeTime' => $hour['closeTime'],
						'description' => $hour['description'],
                        'daily' => $day === 'daily' ? true : false
					];
				}
			}
		}

		if ($data['uuid'])
			$response = self::request('admin/shops' . '/' . $data['uuid'], 'PUT', $data, true);
		else
			$response = self::request('admin/shops', 'POST', $data);

		if (is_wp_error($response))
		{
			set_transient('wt_admin_notices', [[
				'post_id' => $store->ID,
				'class' => 'error',
				'message' => 'STB Error: ' . $response->get_error_message()
			]], MINUTE_IN_SECONDS);

			if ($response->get_error_code() === 'stb-409')
			{
				$search = self::request('admin/shops/search?keyword=' . $data['name'], 'GET');

				if (is_wp_error($search) || empty($search->data) || !is_array($search->data))
				{
					delete_post_meta($store->ID, 'wt_stb_uuid');
				}
				else
				{
					foreach ($search->data as $shop)
					{
						update_post_meta($store->ID, 'wt_stb_uuid', sanitize_text_field($shop->uuid));
						break;
					}
				}
			}

			return;
		}

		if (!empty($response->data->uuid))
			update_post_meta($store->ID, 'wt_stb_uuid', sanitize_text_field($response->data->uuid));
	}

	public static function update_post($post_id)
	{
		$post = get_post($post_id);

		if (!$post || !in_array($post->post_type, ['wt_brand', 'wt_store']) || $post->post_title === 'Auto Draft')
			return;

		if ($post->post_status !== 'publish')
		{
			if ($post->post_type === 'wt_store')
			{
				self::delete_shop($post_id, $post);
			}

			return;
		}

		if ($post->post_type === 'wt_brand')
		{
			global $wpdb;

			$brand = $post;
			$stores = $wpdb->get_results("
				SELECT ID, post_title, post_content
				FROM $wpdb->posts
					JOIN $wpdb->postmeta ON ID = post_id AND meta_key = 'wt_brand' AND meta_value = $post->ID
				WHERE post_type = 'wt_store' AND post_status = 'publish'
			");
		}
		else
		{
			$stores = [$post];
			$brand = get_field('wt_brand', $post_id);
		}

		self::update_shops($stores, $brand);
	}

	public static function register_tag()
	{
		register_taxonomy('wt_stb_tag', ['wt_brand', 'wt_store'], [
			'label' => 'STB Tags',
		]);
	}

	public static function show_admin_notices()
	{
		global $pagenow;

		if ($pagenow !== 'post.php')
			return;

		global $post;

		if (empty($post->ID))
			return;

		$notices = get_transient('wt_admin_notices');

		if ($notices)
		{
			foreach ($notices as $notice)
			{
				if ($notice['post_id'] != $post->ID)
					continue;
				?>

				<div class="notice notice-<?php esc_attr_e($notice['class']); ?>">
					<p><?php echo sanitize_text_field($notice['message']); ?></p>
				</div>

				<?php
			}

			delete_transient('wt_admin_notices');
		}
	}

	private static function get_address_by_lat_lng($lat, $lng, $map = 'stb')
	{
		require_once 'class-om.php';

		$data = OM::request('privateapi/commonsvc/revgeocode', 'GET', [
			'location'      => "$lat,$lng",
//			'buffer'        => 500,
//			'otherFeatures' => 'Y'
		]);

		if (is_wp_error($data))
		{
			return $data;
		}

		if ($map)
			return self::map_address($data['GeocodeInfo'][0], 'om_private', $map);

		return $data['GeocodeInfo'][0];
	}

	private static function get_address($search, $return_geom = 'N', $map = 'stb')
	{
		require_once 'class-om.php';

		$args = [
			'searchVal'      => $search,
			'getAddrDetails' => 'Y',
			'returnGeom' => $return_geom
		];
		$data = OM::request('commonapi/search', 'GET', $args);

		if (is_wp_error($data))
		{
			return $data;
		}

		if ($map)
			return self::map_address($data['results'][0], 'om_common', $map);

		return $data['results'][0];
	}

	private static function get_building_name($post_id)
	{
		$malls = get_the_terms($post_id, 'wt_mall');

		foreach ($malls as $mall)
		{
			return $mall->name;
		}

		return;
	}

	private static function get_key_map($type, $key, $map)
	{
		$keys['address']['stb'] = [
			'block',
			'streetName',
			'buildingName',
			'postalCode'
		];
		$keys['address']['om_common'] = [
			'BLK_NO',
			'ROAD_NAME',
			'BUILDING',
			'POSTAL'
		];
		$keys['address']['om_private'] = [
			'BLOCK',
			'ROAD',
			'BUILDINGNAME',
			'POSTALCODE'
		];

		$keys['location']['stb'] = [
			'latitude',
			'longitude',
		];
		$keys['location']['om'] = $keys['location']['om_common'] = $keys['location']['om_private'] = [
			'LATITUDE',
			'LONGITUDE',
		];
		$keys['location']['acf'] = [
			'lat',
			'lng',
		];

		$data = [];

		foreach ($keys[$type][$key] as $i => $k)
		{
			if (isset($keys[$type][$map][$i]))
				$data[$k] = $keys[$type][$map][$i];
		}

		return $data;
	}

	private static function get_postal_code($address)
	{
		preg_match('/Singapore\s++(\d++)/', $address, $matches);

		if (empty($matches[1]))
		{
			wt_log($address, 'stb');
			wt_log($matches, 'stb');
			return '';
		}
		else
		{
			return $matches[1];
		}

		return '';
	}

	private static function get_time_range($str)
	{
		$times = preg_split('/[-|–|to]/', $str, 0, PREG_SPLIT_NO_EMPTY);

		if (count($times) !== 2)
		{
			return new \WP_Error('wt-400', 'Invalid string');
		}

		foreach ($times as $j => $time)
		{
			$times[$j] = date("H:i", strtotime($time));
		}

		return $times;
	}

	private static function get_weekday_key($key, $format = 'l')
	{
		$key = trim($key);

		if (!self::$days)
		{
			$date = new \DateTime('next Monday');

			for ( $i = 0; $i < 7; $i++ )
			{
				self::$days[$date->format( 'D' )] = $date->format( $format );

				if ($format === 'l')
					self::$days[$date->format( 'D' )] = lcfirst(self::$days[$date->format( 'D' )]);

				$date->modify( '+1 day' );
			}

//			self::$days['Thur'] = self::$days['Thu'];
//			self::$days['Thurs'] = self::$days['Thu'];
		}

		if (empty(self::$days[$key]))
			return '';

		return self::$days[$key];

		$date = new \DateTime('next Monday');
		$mapping = [];

		for ( $days = 0; $days < 7; $days++ )
		{
			$mapping[$date->format( 'D' )] = lcfirst($date->format( 'l' ));
			$date->modify( '+1 day' );
		}

		$mapping['Thurs'] = 'thursday';

		wt_dump($mapping);

		return;
		$str = trim($str, ": \t\n\r\0\x0B");
		$days = [];

		switch ($str)
		{
			case 'Sun - Thur':
				for ($i = 0; $i < 5; $i++)
					$days[] = $i;
				break;

			case 'Mon - Fri':
				for ($i = 1; $i < 6; $i++)
					$days[] = $i;
				break;

			case 'Fri & Sat':
				$days = [5, 6];
				break;

			case 'Sat & Sun':
				$days = [6, 7];
				break;
		}
	}

	private static function map_address($address, $key, $map)
	{
		$types = ['address', 'location'];
		$data  = [];

		foreach ($types as $type)
		{
			$keys = self::get_key_map($type, $key, $map);

			foreach ($keys as $k => $m)
			{
				if (!empty($address[$k]))
				{
					$data[$type][$m] = sanitize_text_field($address[$k]);
				}
			}

		}

		return $data;
	}

	private static function request($endpoint, $method = 'POST', $args = [], $attach_api_key = false, $refresh_token = 0)
	{
		if ($refresh_token > 2)
			return new \WP_Error('stb-500', 'Unable to get valid token');

		$token = get_transient('wt_stb_token');

		if (!$token || $refresh_token)
		{
			$response = wp_remote_post(self::API_URL . 'oauth/accesstoken', [
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode('kVblpedqZsV9c7DBhMAbrr3HEHfNyCQg:Fhf1KRnonYaDOeQK'),
					'ContentType' => 'application/x-www-form-urlencoded'
				],
				'body' => [
					'grant_type' => 'client_credentials'
				]
			]);

			if (is_wp_error($response))
			{
				wt_log($response, 'stb');
				return $response;
			}

			if ($response['response']['code'] != 200)
			{
				wt_log($response['response'], 'stb');
				wt_log($response['body'], 'stb');
				return new \WP_Error('stb-'. $response['response']['code'], $response['response']['message'], ['status' => $response['response']['code']]);
			}

			$data = json_decode($response['body']);
			$token = $data->access_token;

			set_transient('wt_stb_token', $token, $data->expires_in);
		}

		$url = self::API_URL . 'content/v1/' . $endpoint;

		if ($attach_api_key)
			$url .= '?apikey=kVblpedqZsV9c7DBhMAbrr3HEHfNyCQg';

		$a = [
			'method' => $method,
			'headers' => [
				'Authorization' => "BearerToken " . $token,
				'Content-Type' => 'application/json',
				'Cache-Control' => 'no-store'
			]
		];

		if (in_array($method, ['DELETE', 'GET']))
			$url = add_query_arg($args, $url);
		else
			$a['body'] = json_encode($args);

		wt_log($url, 'stb');
		wt_log($a, 'stb');
		$response = wp_remote_request($url, $a);

		if (is_wp_error($response))
		{
			wt_log($response, 'stb');
			return $response;
		}

		$body = json_decode($response['body']);

		if ($response['response']['code'] < 200 || $response['response']['code'] > 299)
		{
			wt_log($response['response'], 'stb');
			wt_log($response['body'], 'stb');

			if (!empty($body->fault->detail->errorcode) && in_array($body->fault->detail->errorcode, ['oauth.v2.InvalidAccessToken', 'keymanagement.service.access_token_expired']))
			{
				$response = self::request($endpoint, $method, $args, $attach_api_key, ++$refresh_token);
				return $response;
			}

			return new \WP_Error('stb-'. $response['response']['code'], $response['response']['message'], ['status' => $response['response']['code']]);
		}

		if ($body->status->code < 200 || $body->status->code > 299)
		{
			wt_log($response['body'], 'stb');
			return new \WP_Error('stb-' . $body->status->code, $body->status->errorDetail);
		}
		wt_log($body, 'stb');
		return $body;
	}

	private static function update_shops($stores, $brand = null)
	{
		foreach ($stores as $store)
		{
			self::update_shop($store->ID, $store, $brand);
		}
	}

	/*private static function import_tags($arg = [])
	{
		$response = self::api('tag', 'GET', $arg);

		if (!empty($response->data) && is_array($response->data))
		{
			if (!isset($GLOBALS['tags']) || !is_array($GLOBALS['tags']))
				$GLOBALS['tags'] = [];

			$GLOBALS['tags'] = array_merge($GLOBALS['tags'], array_column($response->data, 'name'));
		}

		if (!empty($response->nextToken))
		{
			self::import_tags(['nextToken' => $response->nextToken]);
		}
	}*/

	/*static function update_location($post_id, $postal_code, $address = '')
	{
		$data = OM::request('commonapi/search', 'GET', [
			'searchVal'      => $postal_code,
			'returnGeom'     => 'Y',
			'getAddrDetails' => 'N'
		]);

		if (is_wp_error($data))
		{
			return $data;
		}

		$acf_keys = [
			'lat' => 'LATITUDE',
			'lng' => 'LONGITUDE'
		];

		foreach ($data['results'] as $result)
		{
			$map = [];

			if ($address)
				$map['address'] = $address;

			foreach ($acf_keys as $acf => $om)
			{
				$map[$acf] = $result[$om];
			}

			update_post_meta($post_id, 'wt_location', $map);

			return $map;
		}
	}*/
}
