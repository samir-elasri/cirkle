
export default ($component) => {
    const checkbox = $component[0].querySelector('sl-checkbox');
    const innerInput = $component[0].querySelector('input');
    const originalInputName = innerInput.getAttribute('name') || '';
        
    const updateInputName = () => {
        if (checkbox.checked) {
            innerInput.setAttribute('name', originalInputName);
        } else {
            innerInput.removeAttribute('name');
        }
    }

    checkbox.addEventListener('sl-change', updateInputName);
    customElements.whenDefined('sl-checkbox').then(updateInputName);
};
