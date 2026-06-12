import $ from 'jquery';

export default ($component, elements) => {

	const $window = $(window);
	const $html = $('html');
	const $html_body = $('html, body');
	const $body = $html.find('body');
	const {
		$button
	} = elements;
	const $footer = $('footer');

	const adjust = () => {
		const scrollTop = $window.scrollTop();
		const threshold = Math.floor($html.outerHeight() - $window.outerHeight() - $footer.outerHeight() - parseInt($body.css('padding-bottom'), 10) + $button.height() * 0.5);
		const target = Math.max($window.width() <= 1000 ? 68 : 20, scrollTop - threshold);

		$component.css('bottom', target + 'px');
		$button[scrollTop >= 200 ? 'fadeIn' : 'fadeOut'](200);
	};

	$window.scroll(adjust);
	$window.resize(adjust);

	$button.click(() => $html_body.animate({
		scrollTop: 0
	}, 500));

	$component.css('bottom', $window.outerHeight() + 'px');
};
