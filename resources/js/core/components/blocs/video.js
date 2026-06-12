import Plyr from 'plyr';

export default ($component, elements, attributes) => {

	const uid = Math.random().toString(36).substr(2);

	function ready() {
		$component.closest('.wait-ready').removeClass('wait-ready').addClass('ready');
		delete window['bloc_video_ready_' + uid];
	}
	window['bloc_video_ready_' + uid] = ready;

	const {
		title,
		image,
		video,
		'plr-provider': provider
	} = attributes;

	const player = new Plyr('#' + $component.attr('id'), {
		youtube: {
			rel: 0,
			showinfo: 0,
			iv_load_policy: 3,
		},
	});

	if (provider == 'video') {

		player.source = {
			type: 'video',
			title,
			poster: image,
			sources: [{
				src: video,
			}]
		};
	}
	ready();
};
