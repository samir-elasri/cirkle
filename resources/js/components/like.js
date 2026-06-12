export default ($component, elements, attributes, properties) => {

    const {
        likeTrue,
        likeFalse,
    } = elements;

    const {
        url,
        id
    } = properties;

    likeTrue.addEventListener('click', async () => {
        await $.ajax({
            url: url + '?id=' + id + '&like=0',
            success: async() => {
                likeTrue.classList.add('hide');
                likeFalse.classList.remove('hide');
            }
        });
    });

    likeFalse.addEventListener('click', async () => {
        await $.ajax({
            url:url + '?id=' + id + '&like=1',
            success: async() => {
                likeTrue.classList.remove('hide');
                likeFalse.classList.add('hide');
            }
        });
    });
};
