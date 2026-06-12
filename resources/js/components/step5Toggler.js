export default ($component, elements, attributes, properties) => {
    const target = document.querySelector($component[0].dataset.target);
    const parent = $component[0].closest('sl-dropdown');

    $component[0].addEventListener('sl-change', () => {
        parent.hide();
        if ($component[0].checked) {
            target.classList.remove('hide');
        }
        else {
            target.classList.add('hide');
        }
    });

    customElements.whenDefined('sl-checkbox')
        .then(() => {
            if ($component[0].checked) {
                target.classList.remove('hide');
            }
            else {
                target.classList.add('hide');
            }
        });
};
