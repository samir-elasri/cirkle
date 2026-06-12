import $ from 'jquery';


export default () => {
	$('.bloc-text__accordion-checkbox').change(function() {
		if (this.checked) {
			$(this).attr('aria-expanded', 'true');
		} else {
			$(this).attr('aria-expanded', 'false');
		}
	});
}