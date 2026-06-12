export default ($component, elements, attributes, properties) => {
    const {
        subscriptionElement,
        // totalElement,
        postalCodeInputs,
        maxPostalCodesElement,
    } = elements;

    const currencyFormatter = new Intl.NumberFormat(navigator.language || navigator.userLanguage, {
        style: 'currency',
        currency: 'CAD',
        currencyDisplay: 'narrowSymbol',
    });

    const updateDisplay = () => {
        // const price = subscriptionElement.selectedOptions[0]?.dataset.price;
        const maxPostalCodes = subscriptionElement.selectedOptions[0]?.dataset.maxPostalCodes;
        
        // totalElement.textContent = price ? currencyFormatter.format(price) : '';
        maxPostalCodesElement.textContent = maxPostalCodes;
        for (let input of postalCodeInputs) {
            if (parseInt(input.dataset.i) < parseInt(maxPostalCodes)) {
                input.classList.remove('hide');
            }
            else {
                input.classList.add('hide');
            }
        }        
    };

    subscriptionElement.addEventListener('sl-change', updateDisplay);
    customElements.whenDefined('sl-select').then(() => setTimeout(updateDisplay, 0));
    updateDisplay();
};
