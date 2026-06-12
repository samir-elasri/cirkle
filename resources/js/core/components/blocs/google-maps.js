import $ from 'jquery';
const google = window.google;

export default ($component, elements, attributes, properties) => {

	const pinImage = '/dist/img/pin.png';
	const styles = [];

	// Set map options
	const centerMap = new google.maps.LatLng(properties.center.lat, properties.center.lng);
	const mapOptions = {
		zoom: 8,
		mapTypeControl: false,
		center: centerMap,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scrollwheel: false
	};

	if (properties.zoom) mapOptions.zoom = properties.zoom;
	if (properties.disableDefaultUI) mapOptions.disableDefaultUI = properties.disableDefaultUI;

	const map = new google.maps.Map($component[0], mapOptions);

	google.maps.event.addDomListener(window, 'load', () => {
		$component.closest('.wait-ready').removeClass('wait-ready').addClass('ready');
	});

	/* Stylish */
	if (styles) {
		map.setOptions({
			styles
		});
	}

	/* Single Info window */
	const infowindow = new google.maps.InfoWindow({
		content: ""
	});

	// create empty LatLngBounds object
	const bounds = new google.maps.LatLngBounds();

	/* Add markers */
	for (let i = properties.dots.length - 1; i >= 0; i--) {
		const dot = properties.dots[i];
		const marker = new google.maps.Marker({
			position: new google.maps.LatLng(dot.lat, dot.lng),
			map,
			icon: (dot.pinImage) ? dot.pinImage : pinImage,
			title: dot.title
		});
		bounds.extend(marker.position);
		attachWindowToMarker(marker, dot.content);
		marker.content = dot.content;
	}

	if (properties.fitBounds === true) {
		map.fitBounds(bounds);
	}

	function attachWindowToMarker(marker, content) {
		marker.addListener('click', () => {
			infowindow.setContent(content);
			infowindow.open(map, marker);
		});
	}

	return map;
}
