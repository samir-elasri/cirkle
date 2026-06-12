
export default ($component, elements, attributes, properties) => {

    const {
        parentInputId
    } = attributes;

    const parentInput = document.getElementById(parentInputId);
    const items = $component[0].querySelectorAll('.hide');

    const updateList = () => {
        const values = parentInput.value.split(',');
        items.forEach(item => {
            if (values.includes(item.dataset.parent)) {
                item.classList.remove('hide');
            }
            else {
                item.classList.add('hide');
            }
        });
    };

    parentInput.addEventListener('change', updateList);

    updateList();
};
