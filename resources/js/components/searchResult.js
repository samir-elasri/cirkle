export default ($component, elements, _, properties) => {
    const { allProfessions, presentProfessions } = properties;
    

    elements.categoryFilter.addEventListener('change', () => {
        setTimeout(() => {
            const selectedText = elements.categoryFilter.parentElement.querySelector('.item.selected')?.textContent;
            let topContent = '<h3>' + selectedText + '</h3>';
            let bottomContent = '';
            for (let profession of allProfessions) {
                if (profession.service_category_id == elements.categoryFilter.value) {
                    if (presentProfessions.find(a => a == profession.id)) {
                        topContent += '<a href="'+ profession.url +'">'+ profession.title +'</a>';
                    }
                    else {
                        bottomContent += '<span>'+ profession.title +'</span>';
                    }
                }
            }
            elements.filteredProfessionsContainer.innerHTML = topContent + bottomContent;
        }, 0);
    });
};
