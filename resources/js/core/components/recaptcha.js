/**
 *
 * @param {jQuery} $component
 * @param {Object.<string, jQuery>} elements
 * @param {object} attributes
 * @param {object} properties
 * @param {object} additionalData
 */
export default ($component, elements, attributes, properties, additionalData= {}) => {
	const form = $component[0];
	const { then } = additionalData;
	let customAction;

	// Si activé, execute le reCaptcha avant de soumettre tout formulaire
	const metaSiteKey = document.head.querySelector('meta[property="recaptcha:site_key"]');

	form.addEventListener('submit', submitHandler);

	/**
	 *
	 * @param {SubmitEvent} submitEvent
	 */
	function submitHandler(submitEvent) {
		submitEvent.preventDefault();

		const submitter = submitEvent.submitter;

		if (submitter?.hasAttribute('formAction')) {
			customAction = submitter?.formAction;
		} else {
			customAction = null;
		}

		if (metaSiteKey) {
			const siteKey = metaSiteKey.content;
			const inputName = document.head.querySelector('meta[property="recaptcha:input_name"]').content;

			grecaptcha.execute(siteKey, { action: 'submit' }).then(function (token) {
				const input = form.elements[inputName];

				if (input) {
					input.value = token;
				}

				complete();
			});

		} else {
			complete();
		}
	}

	function complete() {
		if (then) {
			then();

		} else {
			if (customAction) {
				form.action = customAction
			}

			form.submit();
		}
	}
}
