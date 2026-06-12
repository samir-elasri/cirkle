import components from '../core/components';
import axios from 'axios';

export default ($component) => {
    const form = $component[0].querySelector('form');
    const resultContainer = $component[0].querySelector('.postalCodeSearch__result');
    const postalCodeElement = $component[0].querySelector('#postal_code');
    const providerTypeResidential = $component[0].querySelector('#provider-type-residential');
    const providerTypeBusiness = $component[0].querySelector('#provider-type-business');
    
    form.addEventListener('submit', event => {
        event.preventDefault();
        let providerType = '';

        if (providerTypeResidential.checked) {
            providerType = 'residential';
        }
        if (providerTypeBusiness.checked) {
            providerType = 'business';
        }

        const url = $component[0].dataset.url
            + '?postal_code=' + postalCodeElement.value
            + '&provider_type=' + providerType;

        axios.get(url).then(result => {
            resultContainer.innerHTML = result.data;
            components(resultContainer);
        });
    });
};
