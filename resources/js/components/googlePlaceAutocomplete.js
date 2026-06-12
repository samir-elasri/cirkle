export default ($component, elements, attributes, properties) => {
    const type = attributes.type;
    const displayInput = $component[0].querySelector('input[type="text"]');
    const formInput = $component[0].querySelector('input[type="hidden"]');
    const google = window.google;

    const autocomplete = new google.maps.places.Autocomplete(displayInput, {
        types: [type],
        fields: ['address_components']
    });


    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        let value;

        if (type === 'postal_code_prefix') {
            const postalCode = place
                ?.address_components
                ?.find(f => f.types?.includes('postal_code_prefix'))
                ?.short_name;
            const province = place
                ?.address_components
                ?.find(f => f.types?.includes('administrative_area_level_1'))
                ?.short_name;
            const country = place
                ?.address_components
                ?.find(f => f.types?.includes('country'))
                ?.short_name;

            value = postalCode + ', ' + province + ', ' + country;
        }
        else {
            value = place
                ?.address_components
                ?.find(f => f.types?.includes(type))
                ?.short_name;
        }

        formInput.value = value;
    });
}
