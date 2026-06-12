export default ($component, elements, attributes, properties) => {
	const {
		$editPassword,
		$editPasswordButton,
		$cancelPasswordButton,
	} = elements;

	$editPasswordButton.click(() => {
		$editPassword.show();
		$editPasswordButton.hide();
	});

	$cancelPasswordButton.click(() => {
		$editPassword.hide();
		$editPasswordButton.show();
	});
};
