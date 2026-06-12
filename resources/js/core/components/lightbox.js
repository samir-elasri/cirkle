import $ from 'jquery';
import Swal from 'sweetalert2';
import 'slick-carousel';

export default ($component) => {

	const $slideshow = $('#lightbox-slick');
	const $modal = $('#' + $component.data('id'));
	let mouse_is_inside = false;
	let slick;
	$component.children().click((e) => {
		const $target = $(e.currentTarget);
		const index = $target.data('index');
		if (typeof slick == 'undefined') {
			slick = $slideshow.slick({
				arrows: true,
				prevArrow: '<div class="slick-prev"></div>',
				nextArrow: '<div class="slick-next"></div>',
				autoplay: false,
				autoplaySpeed: 0,
				dots: false,
				adaptiveHeight: false,
				useCSS: false,
				rows: 0,
			});
		}
		$slideshow.slick('slickGoTo', index, true);

		$modal.removeClass('hide');
	});

	$slideshow.hover(() => {
		mouse_is_inside = true;
	}, () => {
		mouse_is_inside = false;
	});

	$("body").on('mousedown', () => {
		if (!mouse_is_inside && !$modal.hasClass('hide')) {
			$modal.addClass('hide');
		}
	});

	$(".close").on('click', () => {
		$modal.addClass('hide');
	});
};
