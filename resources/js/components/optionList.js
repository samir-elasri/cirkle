export default ($component, elements, attributes, properties) => {

    const { list }= elements;
    window.addEventListener('option-added', (e) => list.innerHTML = e.detail.data)
}