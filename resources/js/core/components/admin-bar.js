import $ from 'jquery';

const $body = $('body');

function setupToggleButton($component, name) {

	const selector = '.admin-bar__button--' + name;
	const className = 'show-' + name;
	const keyName = 'admin-bar.show-' + name;

	$component.find(selector).click(() => {
		const value = localStorage.getItem(keyName) !== 'true';
		localStorage.setItem(keyName, value);
		if (value) $body.addClass(className);
		else $body.removeClass(className);
	});

	if (localStorage.getItem(keyName) === 'true') $body.addClass(className);
}

export default ($component, elements) => {

	const $window = $(window);
	const {
		$resolution
	} = elements;

		$window.resize(() => {
			if (typeof($resolution) !== 'undefined'){
				$resolution.text($window.innerWidth() + 'x' + $window.innerHeight());
			}
			$body.css('paddingTop', $component.outerHeight());
		}).resize();

	setupToggleButton($component, 'guides');
};
