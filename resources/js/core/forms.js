import $ from 'jquery';
import inputmask from 'inputmask';

export default () => {
	// Input mask
	inputmask('email').mask('.mask-is-email');
	inputmask({
		mask: "(999) 999-9999"
	}).mask('.mask-is-phone');
	inputmask('currency').mask('.mask-is-money');

	// Charactère maximum
	$('[data-max-chars]').keypress((event) => {

		const $field = $(event.currentTarget);

		const fieldValue = $field.val();
		const maxLength = parseInt($field.attr('data-max-chars'), 10);
		const totalLength = fieldValue.length;
		const remaining = maxLength - totalLength;

		$field.next('.Chars-counter').children('span').text(remaining);

		if (remaining <= 0 && event.which !== 0 && event.charCode !== 0) {
			$field.parents('.control').append('<p role="alert" aria-live="assertive" class="sr-only">Vous avez atteint le nombre maximum de caractères.</p>')
			event.preventDefault();
		}
	});

	// Choix autre

	function onSelectChange(select) {
		const option = select.options[select.selectedIndex];
		const show = option.getAttribute('other-choice') != null;
		select.otherField.style.display = show ? 'block' : 'none';
		//		select.otherInput.name = show ? select.name : '';
	}

	function onCheckboxChange(input) {
		input.otherField.style.display = input.checked ? 'block' : 'none';
	}

	$('option[other-choice], input[type=checkbox][other-choice]').each((index, el) => {

		switch (el.tagName) {
			case 'OPTION': {
				const select = el.parentElement;
				const name = select.name;
				const form = select.form;

				select.otherField = form.querySelector('[other-choice=other-choice-' + name + ']');
				select.otherInput = select.otherField.querySelector('input');

				select.onchange = (ev) => onSelectChange(ev.currentTarget);

				onSelectChange(select);
			}
				break;
			case 'INPUT': {
				const name = el.name.replace('[]', '');
				const form = el.form;

				el.otherField = form.querySelector('[other-choice=other-choice-' + name + ']');
				el.otherInput = el.otherField.querySelector('input');

				el.onchange = (ev) => onCheckboxChange(ev.currentTarget);
				onCheckboxChange(el);
			}
				break;
		}
	});

	// Retrieve file's name for upload fields
	$('.file-input').change((event) => {
		const files = event.currentTarget.files;
		if (files.length > 0) {
			$(event.currentTarget).siblings('.file-name').html(files[0].name);
		}
	});

	$('form .Generated-popper').closest('.field-label').addClass('with-popper');

	$('.field-label.with-popper .Generated-popper').each(function () {
		var thatCont = $(this).closest('.field-label.with-popper').find('.label');
		$(this).detach().insertAfter(thatCont);
	});

	$('.field-label.with-popper .Generated-popper .Popper-btn').each(function () {
		$(this).hover(function () {
			$(this).closest('.field-label.with-popper').toggleClass('on_display');
		});
	});
}
