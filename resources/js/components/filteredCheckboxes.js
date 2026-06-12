
export default ($component, elements, attributes, properties) => {

    const {
        parentInputId
    } = attributes;

    const parentInput = document.getElementById(parentInputId);
    const items = $component[0].querySelectorAll('.item');

    const updateList = () => {
        const values = parentInput.value.split(',');
        items.forEach(item => {
            if (values.includes(item.dataset.parent)) {
                item.classList.remove('hide');
                item.querySelector('input[type="checkbox"]')
                    .removeAttribute('disabled');
            }
            else {
                item.classList.add('hide');
                item.querySelector('input[type="checkbox"]')
                    .setAttribute('disabled', true);
            }
        });
    };

    parentInput.addEventListener('change', updateList);

    updateList();
};