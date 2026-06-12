import $ from 'jquery';

import MenuTouchOrMouse from './MenuTouchOrMouse';

import SlidingPanel from './SlidingPanel';

// import debounce from 'lodash/debounce';

export default () => {

	// Commons vars

	const header = $('#header');

	const menuMobile = $("#menu-mobile");

	const mainMenu = header.find('ul.menu-main');

	const corpoMenu = header.find('ul.menu-corpo');

	/* eslint-disable no-new */

	new MenuTouchOrMouse(menuMobile.find('nav > ul'), {
		forceTouch: true,
		closeOthers: true
	});

	/* eslint-enable no-new */

	// Menu mobile : Sliding Panel

	const bg = $('#menu-mobile + .menu-mobile__shadow');

	const mobileMenuIcon = header.find('.mobile-menu-icon');

	const slidingMobileMenu = new SlidingPanel(menuMobile, {
		bg,
		togglesOpen: [mobileMenuIcon],
		togglesClose: [bg, menuMobile.find('.menu-mobile__close-icon')]
	});

	function openMobileMenuOnHash() {

		const hash = document.location.hash;

		if (hash === '#menu') slidingMobileMenu.open(0);

	}

	// Open with hash

	$(window).on('hashchange ', openMobileMenuOnHash); // check when hash is changing

	openMobileMenuOnHash(); // check now

	/*

	$("#menuMobile ul.main").on('mousedown', '> li.parent:not(.active)', function() {

	    $("#menuMobile ul.main > li, #menuMobile ul.main > li > a").removeClass('open');

	    $(this).addClass('open').find(' > a').addClass('open');

	});

	*/

	// Desktop : MenuTouchOrMouse

	/* eslint-disable no-new */

	// new MenuTouchOrMouse(mainMenu, { forceTouch: true, closeOthers: true });

	// new MenuTouchOrMouse(corpoMenu);

	/* eslint-enable no-new */

	// Desktop - Replace sub element in 2/3 columns

	let i, i2, i3, l, l2, l3, currentLevelList, qty, listItems;

	const firstLevelList = mainMenu.find('> li');

	for (i = 0, l = firstLevelList.length; i < l; i++) {

		currentLevelList = firstLevelList.eq(i);

		const subUL = currentLevelList.find('> ul');

		if (subUL.length === 0) continue; // no child

		const subULparent = subUL.parent();

		const megaContainer = $('<div class="megamenu"></div>');

		subULparent.append(megaContainer);

		subUL.detach();

		/* if(subUL.find('ul').length === 0) {

		    listItems = subUL.find('> li').detach();

		    const links = $('<div class="links"></div>');

		    // megaContainer.append(header.find('.visitUsMenu').clone().show());

		    megaContainer.append(links);

		    const linksContainer = $('<div></div>');

		    linksContainer.append(listItems.find('a'));

		    links.append(linksContainer);

		} else {*/

		listItems = subUL.find('> li').detach();

		megaContainer.empty();

		const listItemsCount = listItems.length;

		let remainingItems = listItemsCount;

		let colUL;

		let index = 0;

		let listItem;

		const colCount = Math.min(3, listItemsCount);

		let colToCreate = colCount;

		megaContainer.addClass('need-' + colCount);

		for (i2 = 0, l2 = colCount; i2 < l2; i2++) {

			qty = Math.ceil(remainingItems / colToCreate);

			colToCreate--;

			remainingItems -= qty;

			colUL = subUL.clone();

			megaContainer.append(colUL);


			// colUL.css('width', (100/colCount) + '%'); // please let me take care of this. js ain't meant for styling.

			for (i3 = 0, l3 = qty; i3 < l3; i3++) {



				listItem = listItems.eq(index);

				colUL.append(listItem.clone());

				index++;

			}

		}

		// }

	}

	// First level -------

	const megaContainerOpeners = mainMenu.find('> li.parent > a');

	const megaContainers = mainMenu.find('> li.parent > .megamenu');

	const megaContainerLI = megaContainers.parents('li');

	megaContainers.hide();

	megaContainerOpeners.on('click', (e) => {

		e.preventDefault();

		e.stopPropagation();

		const opener = $(e.currentTarget);

		const li = opener.parent();

		mainMenu.find('.open').removeClass('open'); // remove open class

		// let ul = li.parent();

		megaContainerLI.each((index, element) => {

			const el = $(element);

			const megaCont = el.find('.megamenu');

			if (el.get(0) === li.get(0) && !megaCont.is(':visible')) {

				header.find('.mobileSearchIcon').removeClass('open'); // remove open class

				header.find('> form').slideUp(200);

				el.addClass('open');

				el.find('> a').addClass('open');

				megaCont.find('> ul > li > ul').hide(); // close child

				megaCont.slideDown(500);

			} else {

				megaCont.slideUp(200);

				el.find('> a').removeClass('open');

				el.removeClass('open');

			}

		});

	});



	// Second level -------

	megaContainers.each((index, element) => {

		const megaCont = $(element);

		const secondLevelOpeners = megaCont.find('> ul > li > a.parent');

		const secondLevelLI = megaCont.find('> ul > li');



		secondLevelOpeners.on('click', (e) => {

			e.preventDefault();

			e.stopPropagation();

			const opener = $(e.currentTarget);

			const li = opener.parent();

			secondLevelLI.each((index, element) => {

				const el = $(element);

				const container = el.find('> ul');

				if (el.get(0) === li.get(0) && !container.is(':visible')) {

					el.addClass('open');

					el.find('> a').addClass('open');

					container.slideDown(500);

				} else if (container.is(':visible')) {

					container.slideUp(500);

					el.removeClass('open');

					el.find('> a').removeClass('open');

				}

			});

		});

	});



	$(document).on('click', (e) => {

		if (mainMenu.has(e.target).length) return; // clicking on or in the menu

		mainMenu.find('.open').removeClass('open'); // remove open class

		megaContainers.slideUp(200);

	});



	// Search toggle

	(function() {

		const searchBtn = mainMenu.find('.search').add(header.find('.mobileSearchIcon'));

		const searchForm = header.find('> form');

		const searchCloseBtn = searchForm.find('.close-btn');

		const searchInput = searchForm.find('[name=q]');

		searchBtn.add(searchCloseBtn).on('click', (e) => {

			mainMenu.find('.open').removeClass('open'); // remove open class

			megaContainers.slideUp(200);

			// toggle search

			if (searchBtn.hasClass('open')) {

				searchBtn.removeClass('open');

				searchForm.slideUp(200);

			} else {

				searchBtn.addClass('open');

				searchForm.slideDown(500);

				searchInput.trigger('focus');

			}

			e.preventDefault();

			e.stopPropagation();

		});

	}());



	// Sticky Header

	/*

	    let headerHeight = header.outerHeight(); // header height

	    const phantomHeader = $('<div id="phantomHeader"></div>');

	    header.before(phantomHeader);

	    $(window).on('scroll resize', debounce(() => {

	        const top = $(window).scrollTop();

	        header.toggleClass("sticky", (top > 90));

	        headerHeight = header.outerHeight();

	        phantomHeader.css('height', headerHeight + 'px');

	    }, 40)).scroll();

	*/

	// Add class on header when going down

	/*

	    // Hide header when going down

	    let lastScrollPos = 0;

	    let isHidden = false;

	    setInterval(

	        () => {

	            const scrollPos = $(window).scrollTop();

	            if(scrollPos == lastScrollPos) return;



	            if(scrollPos < 50) {

	                header.removeClass('sticky');

	                $("#alert").removeClass('sticky');

	            } else {

	                header.addClass('sticky');

	                $("#alert").addClass('sticky');

	            }



	            if(!isHidden && scrollPos > lastScrollPos) {

	                header.stop().slideUp(100);

	                isHidden = true;

	            } else if(isHidden && scrollPos < lastScrollPos) {

	                header.stop().slideDown(100);

	                isHidden = false;

	            }

	            lastScrollPos = scrollPos;

	        }

	        , 500);

	*/



	// Sticky footer

	/* (function() {

	 const footer = document.querySelector('#footer');

	 $(window).on('resize', debounce(() => {

	 footer.classList.remove('sticky');

	 if(footer.getBoundingClientRect().bottom < document.documentElement.clientHeight){ // if the bottom of the footer is small than the viewport height...

	 footer.classList.add('sticky');

	 }

	 }, 250)).resize();

	 }());*/

}