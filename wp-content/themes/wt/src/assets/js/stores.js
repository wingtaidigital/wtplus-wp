(function($)
{
	let map;
	
	window.wtInitMap = function()
	{
		map = new WtMap();
		
		map.addMarkers();
	};
	
	
	
	$(document.body).on('mouseenter', '[data-location]', function()
	{
		// console.log(map.markers);
		
		marker: for (let i = 0; i < map.markers.length; i++)
		{
			// console.log(map.markers[i].stores);
			for (let j = 0; j < map.markers[i].stores.length; j++)
			{
				if (map.markers[i].stores[j] == '#' + this.id)
				{
					map.infoWindow.setContent(map.markers[i].title);
					map.infoWindow.open(map, map.markers[i]);
					
					$('[data-location]').removeClass('is-active');
					jQuery(map.markers[i].stores.join(',')).addClass('is-active');
					
					break marker;
				}
			}
		}
	})
	// .mouseleave(function() {
	// 	// $( this ).find( "span" ).text( "mouse leave" );
	// });
	
	
	
	if (typeof wpApiSettings === 'undefined')
		return;
	
	$('select[data-route]').change(function()
	{
		let route = $(this).data('route');
		let settings = {
			url: wpApiSettings.root + $(this).data('route'),
			type: 'GET',
			data: {},
			dataType: 'html'
		};
		let $fields = $('select[data-route="' + route + '"]');
		
		$fields.each(function()
		{
			settings.data[this.name] = $(this).val();
		});
		
		if (typeof wpApiSettings.nonce !== 'undefined')
		{
			settings.beforeSend = function(xhr)
			{
				xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
			};
		}
		
		$.ajax(settings)
		.always(function()
		{
			map.clearMarkers();
		})
		.done(function(response)
		{
			$('#stores').html(response);
			
			// map.clearMarkers();
			map.addMarkers();
		})
		.fail(function()
		{
			$('#stores').html('<p class="wt-gutter-half">No stores found.</p>');
		})
	});
	// $('select[name="brand"]').change(function()
	// {
	// 	let settings = {
	// 		url: wpApiSettings.root + 'wt/v1/stores',
	// 		type: 'GET',
	// 		data: 'brand=' + $(this).val(),
	// 		dataType: 'html'
	// 	};
	//
	// 	if (typeof wpApiSettings.nonce !== 'undefined')
	// 	{
	// 		settings.beforeSend = function(xhr)
	// 		{
	// 			xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
	// 		};
	// 	}
	//
	// 	$.ajax(settings)
	// 	.done(function(response)
	// 	{
	// 		$('#stores').html(response);
	//
	// 		map.clearMarkers();
	// 		map.addMarkers();
	// 	})
	// });
	
})(jQuery);



function WtMap()
{
	this.map = new google.maps.Map(document.getElementById('map'), {
		maxZoom: 18,
		styles: [
			{
				"featureType": "all",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"weight": "2.00"
					}
				]
			},
			{
				"featureType": "all",
				"elementType": "geometry.stroke",
				"stylers": [
					{
						"color": "#9c9c9c"
					}
				]
			},
			{
				"featureType": "all",
				"elementType": "labels.text",
				"stylers": [
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "landscape",
				"elementType": "all",
				"stylers": [
					{
						"color": "#f2f2f2"
					}
				]
			},
			{
				"featureType": "landscape",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#ffffff"
					}
				]
			},
			{
				"featureType": "landscape.man_made",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#ffffff"
					}
				]
			},
			{
				"featureType": "poi",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "all",
				"stylers": [
					{
						"saturation": -100
					},
					{
						"lightness": 45
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#eeeeee"
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#7b7b7b"
					}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.stroke",
				"stylers": [
					{
						"color": "#ffffff"
					}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "simplified"
					}
				]
			},
			{
				"featureType": "road.arterial",
				"elementType": "labels.icon",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "transit",
				"elementType": "all",
				"stylers": [
					{
						"visibility": "off"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "all",
				"stylers": [
					{
						"color": "#46bcec"
					},
					{
						"visibility": "on"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry.fill",
				"stylers": [
					{
						"color": "#c8d7d4"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
					{
						"color": "#070707"
					}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.stroke",
				"stylers": [
					{
						"color": "#ffffff"
					}
				]
			}
		]
	});
	this.markers = [];
	this.infoWindow = new google.maps.InfoWindow();
	this.markerOptions = {
		map: this.map,
		optimized: false,
		icon: {
			url: 'data:image/svg+xml;base64,' + btoa('<svg xmlns="http://www.w3.org/2000/svg" fill="#FF4178" width="44" height="44" viewBox="0 0 24 24"><path stroke="#fff" d="M18 8c0-3.31-2.69-6-6-6S6 4.69 6 8c0 4.5 6 11 6 11s6-6.5 6-11zm-8 0c0-1.1.9-2 2-2s2 .9 2 2-.89 2-2 2c-1.1 0-2-.9-2-2zM5 20v2h14v-2H5z"/><path d="M0 0h24v24H0z" fill="none" /></svg>'),
			scaledSize: new google.maps.Size(44, 44)
		}
	};
	
	// if (typeof wtThemeSettings === 'undefined')
	// 	return;
	//
	// this.markerOptions.icon = wtThemeSettings.root + '/assets/img/ic_pin_drop_black_24px.svg'
}

WtMap.prototype.addMarkers = function()
{
	let bounds = new google.maps.LatLngBounds();
	let t = this;
	let $stores = jQuery('[data-location]');
	
	function openInfoWindow()
	{
		t.infoWindow.setContent(this.title);
		t.infoWindow.open(t.map, this);
		
		$stores.removeClass('is-active');
		jQuery(this.stores.join(',')).addClass('is-active');
	}
	
	$stores.each(function()
	{
		let $store = jQuery(this);
		let location = $store.data('location');
		
		if (typeof location !== 'object')
			return true;
		
		let ll = {
			lat: parseFloat(location.lat),
			lng: parseFloat(location.lng)
		};
		// let ll = new google.maps.LatLng(location.lat, location.lng);
		
		let storeId = this.id;
		let title = $store.find('h1').text();
		let bail = false;
		
		for (let i = 0; i < t.markers.length; i++)
		{
			if (t.markers[i].position.lat() === ll.lat && t.markers[i].position.lng() === ll.lng)
			{
				let titles = [title];
				
				t.markers[i].stores.push('#' + storeId);
				
				titles.push(t.markers[i].title);
				titles.sort();
				t.markers[i].title = titles.join('<br>');
				
				bail = true;
				
				break;
			}
		}
		
		if (bail)
			return true;
		
		let options = t.markerOptions;
		
		options.position = ll;
		
		let marker = new google.maps.Marker(options);
		
		marker.stores = ['#' + storeId];
		marker.title = title;
		
		t.markers.push(marker);
		
		bounds.extend(ll);
		
		marker.addListener('mouseover', openInfoWindow);
		marker.addListener('click', openInfoWindow);
		
		// marker.addListener('mouseout', function()
		// {
		// 	t.infoWindow.close();
		// });
	});
	// console.log(t.markers);
	google.maps.event.trigger(this.map, 'resize');
	this.map.fitBounds(bounds);
};

WtMap.prototype.clearMarkers = function()
{
	for (let i = 0; i < this.markers.length; i++)
	{
		this.markers[i].setMap(null);
		this.markers[i] = null;
	}
	
	this.markers = [];
};
