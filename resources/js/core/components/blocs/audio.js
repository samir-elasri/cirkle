import $ from 'jquery';
import Plyr from 'plyr';

export default ($component, elements, attributes, properties) => {

	const uid = Math.random().toString(36).substr(2);

	function ready() {
		$component.closest('.wait-ready').removeClass('wait-ready').addClass('ready');
		delete window['bloc_video_ready_' + uid];
	}

	const {
		id
	} = attributes;

	const player = new Plyr('#' + id, {});

	ready();
};
