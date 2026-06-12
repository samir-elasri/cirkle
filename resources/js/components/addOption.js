
export default ($component, elements, attributes, properties) => {
    const {
        postUrl,
        csrf
    } = properties;

    const {
        addForm
    } = elements;


    addForm.addEventListener('submit', async (e) => {
        await formSubmit(e);
    });

    async function formSubmit(el) {
        el.preventDefault();
        let data = new FormData(addForm)
        for (let value of data.entries()) {
            console.log(value[0])
            console.log(value[1])
        }


        await $.ajax({
            url: postUrl,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            processData: false,
            contentType: false,
            success: () => {
                Swal.close()
                window.dispatchEvent(new CustomEvent('option-added'))
            }
        });
    }
}