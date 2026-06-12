/* eslint-disable complexity, no-new, prefer-reflect */

import 'slick-carousel';
import $ from 'jquery';

function Gallery($component) {

	this.gallery = $component;

	const isHalfWidthMode = this.gallery.closest('.bloc__content').hasClass('half-content-width');

	const bgSlideCount = isHalfWidthMode ? 3 : 5;
	const mdSlideCount = isHalfWidthMode ? 2 : 4;
	const smSlideCount = isHalfWidthMode ? 2 : 3;
	const tnSlideCount = isHalfWidthMode ? 1 : 2;
	const xtnSlideCount = isHalfWidthMode ? 1 : 1;

	const _options = {};
	const _slickOptions = {
		responsive: [{
				breakpoint: 9999,
				settings: {
					slidesToShow: bgSlideCount,
					slidesToScroll: bgSlideCount
				}
			},
			{
				breakpoint: 1200,
				settings: {
					slidesToShow: mdSlideCount,
					slidesToScroll: mdSlideCount
				}
			},
			{
				breakpoint: 768,
				settings: {
					slidesToShow: smSlideCount,
					slidesToScroll: smSlideCount
				}
			},
			{
				breakpoint: 520,
				settings: {
					slidesToShow: tnSlideCount,
					slidesToScroll: tnSlideCount
				}
			},
			{
				breakpoint: 420,
				settings: {
					slidesToShow: tnSlideCount,
					slidesToScroll: xtnSlideCount
				}
			},
			{
				breakpoint: 350,
				settings: {
					slidesToShow: xtnSlideCount,
					slidesToScroll: xtnSlideCount
				}
			}
		]
	}

	const defaultOptions = {
		gallerySelector: ".galler-o",
		slidesListSelector: ".slidesList",
		slideSelector: ".slide",
		infosListSelector: ".infosList",
		infosSelector: ".infos",
		thumbsListSelector: ".thumbsList",
		thumbSelector: ".thumb",
		thumbClickableSelector: ".slick-slide"
	};
	const defaultSlickOptions = {
		infinite: true,
		dots: false,
		arrows: true,
		slidesToShow: 3,
		slidesToScroll: 3,
		prevArrow: '<div class="slick-prev"></div>',
		nextArrow: '<div class="slick-next"></div>',
		useCSS: false, // without this we sometime have a flickering bug (when infinite mode and autoplay is true, and we move from the last slide to the first)
		rows: 0,
	};
	this.options = $.extend(defaultOptions, _options);
	this.clickValid = false;
	this.target = null;

	// Thumbs
	const thumbsList = this.thumbsList = this.gallery.find(this.options.thumbsListSelector);
	this.thumbs = thumbsList.find(this.options.thumbSelector);
	this.thumbsCount = this.thumbs.length;
	this.currentID = -1;

	// Slides
	this.slidesList = this.gallery.find(this.options.slidesListSelector);
	this.slides = this.slidesList.find(this.options.slideSelector);

	// Buttons .prev-btn, .next-btn
	this.prevSlideBtn = this.slidesList.find('.prev-btn');
	this.nextSlideBtn = this.slidesList.find('.next-btn');
	this.prevSlideBtn.on('click', $.proxy(this.prevSlide, this));
	this.nextSlideBtn.on('click', $.proxy(this.nextSlide, this));

	// Infos
	this.infosList = this.gallery.find(this.options.infosListSelector);
	this.infos = this.infosList.find(this.options.infosSelector);

	// Ensure no bugs
	if ((this.thumbs.length + this.slides.length + this.infos.length) / 3 !== this.infos.length) {
		console.error('Gallery.js : you must have the same number of slides(%s), thumbs(%s), and infos(%s)', this.slides.length, this.thumbs.length, this.infos.length);
	}

	// Events
	thumbsList.on("init", $.proxy(this.onSlickInit, this));
	thumbsList.on("setPosition", $.proxy(this.onSetPosition, this));
	thumbsList.find(this.options.thumbSelector).on('mousedown', $.proxy(this.onMouseDown, this));
	thumbsList.find(this.options.thumbSelector).on('mouseup', $.proxy(this.onMouseUp, this));

	thumbsList.on('mousedown', this.options.thumbClickableSelector, $.proxy(this.onMouseDown, this));
	thumbsList.on('mouseup', this.options.thumbClickableSelector, $.proxy(this.onMouseUp, this));

	// Init slick (carrousel)
	this.slick = thumbsList.slick($.extend(defaultSlickOptions, _slickOptions));


}

// Slick : Fires after position/size changes
Gallery.prototype.onSetPosition = function() {

	/*this.thumbsList.height('unset');

	const size = this.gallery.find(".slick-track").first().height();

	this.thumbsList.height(size);*/

	this.gallery.closest('.wait-ready').removeClass('wait-ready').addClass('ready');
};

// onSlickInit
Gallery.prototype.onSlickInit = function() {
	this.move(0);
};

// onMouseDown
Gallery.prototype.onMouseDown = function(event) {
	this.clickValid = true;
	this.target = $(event.currentTarget);
	setTimeout(this.onMouseDownDone.bind(this), 300);
};
Gallery.prototype.onMouseDownDone = function() {
	this.clickValid = false;
};

// onMouseUp
Gallery.prototype.onMouseUp = function() {
	setTimeout(this.checkClick.bind(this), 10);
};

// Check Click
Gallery.prototype.checkClick = function() {
	this.move($(this.target).data("slick-index"));
};

// Go to prev slide
Gallery.prototype.prevSlide = function() {
	let id = this.currentID - 1;
	if (id < 0) id = (this.thumbsCount - 1);
	this.move(id);
	this.slick.slick('slickGoTo', id);
};

// Go to next slide
Gallery.prototype.nextSlide = function() {
	let id = this.currentID + 1;
	if (id >= this.thumbsCount) id = 0;
	this.move(id);
	this.slick.slick('slickGoTo', id);
};

// Se déplace à un slide ID
Gallery.prototype.move = function(currentID) {
	if (this.currentID === currentID) return; // already there
	this.currentID = currentID;
	let thumb;
	let slide;
	let infos;
	let slideImg;
	let $video;
	let url;
	let range;
	for (let i = 0; i < this.thumbsCount; i++) {
		thumb = $(this.thumbs.eq(i));
		slide = $(this.slides.eq(i));
		infos = $(this.infos.eq(i));

		if (this.currentID == i) {
			thumb.addClass("active");
			slide.show();
			slideImg = slide.find('img');
			if (slideImg.data('lazy') !== '') {
				slideImg.attr('src', slideImg.data('lazy'));
				slideImg.data('lazy', ''); // unset
			}
			infos.show();
		} else {
			thumb.removeClass("active");
			$video = slide.find('video').get(0);
			if ($video) {
				$video.pause();
			}
			$video = slide.find('iframe');
			range = slide.find("input[type=range]");
			if ($video) {
				url = $video.attr('src');
				range.val(0);
				$video.attr('src', '');
				$video.attr('src', url);
			}
			slide.hide();
			infos.hide();
		}
	}
};

export default ($component) => new Gallery($component);
