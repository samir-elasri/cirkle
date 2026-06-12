import $ from 'jquery';

export default ($component, elements, attributes) => {

	const { display } = attributes;

	const $fiche = $(`.${display}`, $component);
	const $switch = $(`.${display}--btn`, $component);

	$switch.each((i, btn) => {
		$(btn).on('click', () => {
			$fiche.toggle();
		});
	});
};
