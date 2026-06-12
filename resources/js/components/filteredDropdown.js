
export default ($component, elements, attributes, properties) => {

    const {
        noCache,
        parentInputId,
        hideIfParentEmpty
    } = attributes;


    const $dropdown = $('.dropdown', $component);
    const dropdownValueInput = $component[0].querySelector('input[type="hidden"]');
    const parentInput = document.getElementById(parentInputId);
    const items = $component[0].querySelectorAll('.item');

    const valueCache = {};
    const setValueCache = () => {
        if (!noCache) {
            valueCache[parentInput.value] = dropdownValueInput.value.split(',');
        }
    }

    const updateList = () => {
        if (!noCache) {
            $dropdown.dropdown('set exactly', valueCache[parentInput.value]);
        }

        items.forEach(item => {
            if (!parentInput.value) {
                if (hideIfParentEmpty) {
                    item.classList.add('hide');
                }
                else {
                    item.classList.remove('hide');
                }
            }
            else {
                const values = parentInput.value.split(',');
                if (values.includes(item.dataset.parent)) {
                    item.classList.remove('hide');
                }
                else {
                    item.classList.add('hide');
                }
            }
        });
    };

    parentInput.addEventListener('change', updateList);
    dropdownValueInput.addEventListener('change', setValueCache);

    setValueCache();
    updateList();
};