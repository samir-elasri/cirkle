import $ from 'jquery';
import Plyr from 'plyr';

export default ($component) => {

	const autoPlaySpeed = Math.round($component.data('slideshow-auto-play-speed') * 1000);
	const $slides = $('.slides', $component);
	const $slides_mobile = $('.slides-mobile', $component);
	const is_iOs = !!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform);
	const is_small_iOs = !!navigator.platform && /iPhone|iPod/.test(navigator.platform);
	const videoSlides = $slides.find('.slide [data-video-id]');

	let timeoutId = 0;

	function init(ev, slick) {
		if (slick.$dots) {
			const $items = slick.$dots.find('li');

			if ($items.length > 0) {
				$items.addClass('on-desktop');
			}
		}

		if ($('.slide', $component).length > 1) {
			setTimeout(() => beforeChange(ev, slick, -1, 0), 100);
		}
	}

	function beforeChange(ev, slick, curr, next) {

		// Aucun changement de diapositive
		if (curr == next) return;

		let id;

		const $curr = slick.$prev = $(slick.$slides[curr]);
		const $next = $(slick.$slides[next]);

		clearTimeout(timeoutId);

		// Pause le lecteur actuel
		let $video = $curr.children('video').get(0);
		if ($video) {
			$video.pause();
		}

		// Démarre le prochain lecteur
		$video = $next.children('video').get(0);
		if ($video) {
			$video.play();
		} else timeoutId = setTimeout(() => $slides.slick('next'), autoPlaySpeed);
	}

	function afterChange(ev, slick) {

		// Réinitialise le vidéo précédent
		const $video = slick.$prev ? slick.$prev.children('video').get(0) : null;
		if ($video) {
			$video.currentTime = 0;
		}

	}

	if (videoSlides.length > 0) {
		videoSlides.each((index, item) => {
			const id = $(item).data('video-id');

			const $item = $(item);
			const $slide = $item.parent('.slide');

			new Plyr('#video_' + id, {

				autoplay: true,
				muted: true,
				hideControls: true,
			});

			let $video = $slide.find('video');
			$video.on('ended', function () {
				$slides.slick('next');
			});

			if (is_iOs && !is_small_iOs) {
				$('body').on('click touchstart', () => {
					if (!item.playing) {
						item.play();
					}
				});
			}
		});
	}

	if (is_iOs) {
		Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
			get: function () {
				return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
			}
		});
	}

	const slickSettings = {
		arrows: true,
		autoplay: false,
		autoplaySpeed: 0,
		dots: true,
		adaptiveHeight: false,
		prevArrow: '<div class="on-desktop slick-prev"></div>',
		nextArrow: '<div class="on-desktop slick-next"></div>',
		useCSS: false,
		rows: 0,
	};

	if ($slides_mobile.length > 0) {
		slickSettings.asNavFor = '.slides-mobile';
	}

	$slides
		.on('init', init)
		.on('beforeChange', beforeChange)
		.on('afterChange', afterChange)
		.slick(slickSettings)
		.show();

	if ($slides_mobile.length > 0) {
		$slides_mobile.slick({
			arrows: false,
			dots: true,
			autoplay: false,
			autoplaySpeed: 0,
			adaptiveHeight: false,
			useCSS: false,
			rows: 0,
			asNavFor: '.slides',
			focusOnSelect: true
		}).show();
	}

	const $navigation = $('ul.slick-dots', $component);
	$navigation.append('<li class="on-desktop"><button aria-label="play - pause button" class="play playing" type="button"></button></li>');
	const $playBtn = $navigation.find('li button.play');

	$navigation.find('li button.play').on(`click`, () => {
		if (timeoutId == -1) {
			$slides.slick('next');
			$playBtn.removeClass('pause')
			$playBtn.addClass('playing')
		} else {
			clearTimeout(timeoutId);
			timeoutId = -1;
			$playBtn.removeClass('playing')
			$playBtn.addClass('pause')
		}
	});
};
