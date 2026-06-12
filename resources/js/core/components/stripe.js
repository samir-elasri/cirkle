import Swal from 'sweetalert2';

export default ($component, elements, attributes, properties) => {

	const { publishableKey, sessionId } = properties;

	const stripe = Stripe(publishableKey);

	$component.on('click', () => {
		stripe.redirectToCheckout({
			sessionId: sessionId
		}).then((result) => {
			Swal.fire('', result.error.message, 'error');
		});
	});
};
