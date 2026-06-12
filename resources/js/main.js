// Replace @babel/polyfill - Maybe not needed?
import "core-js/stable";
import "regenerator-runtime/runtime";

// Import global resources
import Alpine from 'alpinejs';
import $ from 'jquery';
import Swal from "sweetalert2";

global.jQuery = global.$ = $;
window.Alpine = Alpine;
global.Swal = Swal;
require('slick-carousel');
require("../../public_html/dist/compiled/semantic/semantic.min");


import './core/translate.js';
import menus from './core/menus';
import forms from './core/forms';
import inputmasks from './core/inputmasks';
import components from './core/components';
import accessibility from './core/accessibility';
import externalLinkAsTargetBlank from './core/external-link-as-target-blank';

import markerSDK from '@marker.io/browser';

markerSDK.loadWidget({
	project: '686fe138eccd009e926d57bf',
});

$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

menus();
forms();
inputmasks();
accessibility();

components(document, {
	adminBar: require('./core/components/admin-bar'),
	gallery: require('./core/components/blocs/gallery'),
	googleMaps: require('./core/components/blocs/google-maps'),
	gotoTop: require('./core/components/goto-top'),
	miniCards: require('./core/components/blocs/mini-cards'),
	slideshow: require('./core/components/slideshow'),
	tileGrid: require('./core/components/tile-grid'),
	video: require('./core/components/blocs/video'),
	audio: require('./core/components/blocs/audio'),
	dropdown: require('./core/components/dropdown'),
	alert: require('./core/components/alert'),
	modal: require('./core/components/modal'),
	lightbox: require('./core/components/lightbox'),
	displayer: require('./core/components/displayer'),
	scrollfire: require('./core/components/scrollfire'),
	profile: require('./core/components/profile'),
	stripe: require('./core/components/stripe'),
	responsiveTableLabel: require('./core/components/responsive-table-label'),
	recaptcha: require('./core/components/recaptcha'),
	mySpace: require('./core/components/mySpace'),
	addToHomeScreen: require('./core/components/addToHomeScreen'),
	starSelector: require('./components/star-selector'),
	starDisplayer: require('./components/star-displayer'),
	addOption: require('./components/addOption'),
	optionList: require('./components/optionList'),
	like: require('./components/like'),
	filteredDropdown: require('./components/filteredDropdown'), 
	filteredCheckboxes: require('./components/filteredCheckboxes'),
	filteredSection: require('./components/filteredSection'),
	popup: require('./components/popup'),
	googlePlaceAutocomplete: require('./components/googlePlaceAutocomplete'),
	postalCodeSelector: require('./components/postalCodeSelector'),
	profileOptionList: require('./components/profileOptionList'),
	carouselSwiper: require('./components/carouselSwiper'),
	step2ConditionnalCheckboxInput: require('./components/step2ConditionnalCheckboxInput'),
	step2ServiceSelector: require('./components/step2ServiceSelector'),
	step5Toggler: require('./components/step5Toggler'),
	step4: require('./components/step4'),
	postalCodeSearch: require('./components/postalCodeSearch'),
	searchResult: require('./components/searchResult'),
	backBtn: require('./components/back-btn'),
});

require('./components/postalCodeSelector2');
require('./components/add-item-button');

externalLinkAsTargetBlank();
Alpine.start();
