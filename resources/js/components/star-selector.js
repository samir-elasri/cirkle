export default ($component, elements, attributes, properties) => {

	const {
		starEls,
		valueEl,
	} = elements;

	const {
		classOn,
		classOff
	} = properties;

	const render = () => {
		for (let i = 0; i < starEls.length; i++) {
			if (i < (valueEl.value * 2)) {
				starEls[i].className = classOff;
			}
			else {
				starEls[i].className = classOn;
			}
		}
	}

	for (let i = 0; i < starEls.length; i++) {
		starEls[i].addEventListener('click', () => {
			valueEl.value = (i+1) / 2;
			render();
		});
	}

	render();
};
