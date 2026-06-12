export default ($component, elements, attributes, properties) => {
    const {
        searchInput,
        listContainer,
        messageContainer,
        countContainer,
    } = elements;

    const {
        max,
        servedPostalCodes,
        translations,
    } = properties;

    const autocomplete = new google.maps.places.Autocomplete(searchInput, {
        fields: ['address_components']
    });

    const makeInput = (value) => {
        const div = document.createElement('div');
        div.classList.add('postalCodeSelector__list-item');

        const valueInput = document.createElement('input');
        valueInput.value = value;
        valueInput.setAttribute('name', 'servedPostalCodes[]');
        valueInput.setAttribute('type', 'hidden');
        div.appendChild(valueInput);

        const valueLabel = document.createElement('div')
        valueLabel.textContent = value;
        div.appendChild(valueLabel);

        const deleteIcon = document.createElement('i');
        deleteIcon.classList.add('delete', 'icon');

        const deleteButton = document.createElement('button');
        deleteButton.appendChild(deleteIcon);
        deleteButton.addEventListener('click', () => {
            div.remove();
            const index = servedPostalCodes.indexOf(valueInput.value.substring(0, 3));
            servedPostalCodes.splice(index, 1);
            setCount();
        });
        div.appendChild(deleteButton);

        return div;
    }

    const setCount = () => {
        countContainer.textContent = translations.remaining
            + ' ' + servedPostalCodes.length
            + ' / ' + max;
    }

    autocomplete.addListener('place_changed', () => {
        messageContainer.textContent = '';
        searchInput.value = '';
        const place = autocomplete.getPlace();

        const postalCode = place
            ?.address_components
            ?.find(f => f.types?.includes('postal_code'))
            ?.short_name
            .substring(0, 3);

        const city = place
            ?.address_components
            ?.find(f => f.types?.includes('locality'))
            ?.long_name;

        const province = place
            ?.address_components
            ?.find(f => f.types?.includes('administrative_area_level_1'))
            ?.long_name;

        if (!postalCode) {
            messageContainer.textContent = translations.noPostalCode;
        }
        else if (servedPostalCodes.length >= max) {
            messageContainer.textContent = translations.maxReached;
        }
        else if (servedPostalCodes.includes(postalCode)) {
            messageContainer.textContent = translations.alreadyHas;
        }
        else {
            const value = postalCode + ', ' + city + ', ' + province;
            servedPostalCodes.push(value)
            listContainer.appendChild(makeInput(value));
        }

        setCount();
    });

    servedPostalCodes.forEach(region => listContainer.appendChild(makeInput(region)));
    setCount();

}
