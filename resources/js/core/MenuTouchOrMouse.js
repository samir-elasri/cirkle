import $ from 'jquery';

/*
 * This class make menu work correctly on all platforms and devices. (android, iOs, windows, windows with touch...)
 * You need to remove ":hover" from the css or you will get bug on iOS.
 * If the device has touch enabled, hover effects will be disabled and you will have to click once to open a menu and twice to activate a link that is also a menu.
 * If the device has only the mouse, hover effects will work and click will call links.
 * https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/HandlingEvents/HandlingEvents.html
 * http://stackoverflow.com/questions/2741816/is-it-possible-to-force-ignore-the-hover-pseudoclass-for-iphone-ipad-users
 */
function MenuTouchOrMouse(_root, _options) {

	this.root = _root;
	_options = (typeof _options === 'undefined') ? {} : _options;
	const defaultOptions = {
		containerSelector: "ul",
		targetSelector: "li",
		forceTouch: false,
		closeOthers: true,
		openClass: 'open'
	};
	this.options = $.extend(defaultOptions, _options);
	this.CONTAINER = this.options.containerSelector;
	this.TARGET = this.options.targetSelector;

	this.hasTouch = (
		this.options.forceTouch ||
		'ontouchstart' in document ||
		'ontouchstart' in window ||
		(typeof navigator.maxTouchPoints !== 'undefined' && navigator.maxTouchPoints > 0) ||
		(typeof navigator.msMaxTouchPoints !== 'undefined' && navigator.msMaxTouchPoints > 0)
	);
	this.parentNodes = this.root.find(this.TARGET).has(this.CONTAINER);

	if (this.hasTouch) {
		// Events for touch screen only
		this.parentNodes.on('click', $.proxy(this.onActivate, this));
		if (this.options.closeOthers) $(document).on('click', $.proxy(this.onBlur, this));
	} else {
		// Mouse events for non-touch (mouse) screen only
		this.parentNodes.on('mouseover', $.proxy(this.onMouseOver, this));
		this.parentNodes.on('mouseout', $.proxy(this.onMouseOut, this));
	}

	// Initialize
	this.init();
}

MenuTouchOrMouse.prototype = {

	// Initialize : Show the menu and hide the containers
	init() {
		this.root.find(this.CONTAINER).hide(); // hide everything
		this.root.show(); // but show menu if it has been hidden
	},

	// Mouse over show the child container
	onMouseOver(e) {
		const target = $(e.currentTarget);
		target.find('>' + this.CONTAINER).show();
		target.addClass(this.options.openClass);
		target.find('> a').addClass(this.options.openClass);
	},

	// Mouse out hide the child container
	onMouseOut(e) {
		const target = $(e.currentTarget);
		target.find('>' + this.CONTAINER).hide();
		target.removeClass(this.options.openClass);
		target.find('> a').removeClass(this.options.openClass);
	},

	// Touch event to open a container if not opened or follow a link
	onActivate(e) {
		const target = $(e.currentTarget); // $(e.target).parent();
		const childContainer = target.find('>' + this.CONTAINER);

		if (this.options.closeOthers) {
			if (target.parents(this.CONTAINER).length == 1) { // first level elements
				this.parentNodes.not(target).removeClass(this.options.openClass).find('> a').removeClass(this.options.openClass);
				this.root.find(this.CONTAINER).not(childContainer).hide(); // hide everything but not the current one
			}
		}
		if (childContainer.length) { // the target is a parent
			if (childContainer.is(':visible')) { // it is already open
				const link = target.find('> a');
				if (link.length === 0 || link.attr('href').indexOf('#') === 0) {
					childContainer.hide(); // not a link, close it back on second touch/click
					target.removeClass(this.options.openClass);
					target.find('> a').removeClass(this.options.openClass);
				} else {
					// follow link, it has already been opened
				}
			} else {
				// Open it but don't follow any link
				target.addClass(this.options.openClass);
				target.find('> a').addClass(this.options.openClass);
				childContainer.show();
				e.preventDefault();
				e.stopPropagation();
			}
		} else {
			// follow link, it doesn't have any child container
		}
	},

	// Touch events to close container when clicking elsewhere
	onBlur(e) {
		if (this.root.has(e.target).length) return;
		this.root.find(this.CONTAINER).hide();
	}
};

export default MenuTouchOrMouse;