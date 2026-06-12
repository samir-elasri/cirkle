import axios from "axios";

export default ($component, elements, attributes, properties) => {
    const cache = {};
    const container = document.getElementById('service-container');

    const display = () => {
        container.innerHTML = '';
        if ($component[0].value) {
            if (cache[$component[0].value]) {
                container.innerHTML = cache[$component[0].value];
            }
            else {
                axios.get($component[0].dataset.url + '?service_category_id=' + $component[0].value)
                    .then(result => {
                        cache[$component[0].value] = result.data;
                        container.innerHTML = cache[$component[0].value];
                    });
            }
        }
    };

    $component[0].addEventListener('sl-change', display);
    // customElements.whenDefined('sl-select').then(display);
};
