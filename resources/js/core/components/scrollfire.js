import $ from 'jquery';

const $window = $(window);
const list = [];

$window.scroll(() => {

	let i = 0;

	const bottom = $window.innerHeight();

	while(i < list.length) {

		const el = list[i];
		const bounds = el.getBoundingClientRect();

		if (bounds.top + 100 < bottom) {
			
			el.classList.add('anim-fire');
			list.splice(i, 1);
		
		} else {
			
			i++;
		}
	}
});

$window.ready(() => $window.scroll());

export default ($component, elements) => {

	list.push($component[0]);
};
