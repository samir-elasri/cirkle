export default ($component, elements, attributes, properties) => {
    const {
        getList,
        moveUrl,
        deleteModalDetails,
        editLegendModalDetails,
        deleteUrl,
        editUrl,
        isPromotions = false,
        isPhotos = false,
        promotionSwitchUrl = null,
        jobOfferSwitchUrl = null,
    } = properties;

    const {
        list,
    } = elements;

    refreshList();

    window.addEventListener('option-added', async () => {
        list.style.opacity = 0;
        await refreshList();
    })


    async function refreshList() {
        let list = $component[0].querySelector('[data-ref="list"]')
        await $.ajax({
            url: getList,
            success: (data) => {
                list.innerHTML = data.view;
                list.style.opacity = 1;
            }
        });
       listeners();
    }

   async function move(dir, id) {
       list.style.opacity = 0;

        await $.ajax({
            url: `${moveUrl}/${id}/${dir}`,
            success: async ()=>{
                await refreshList();
            }
        });
    }

    function upArrow(id) {
        move('up', id);
    }

    function downArrow(id) {
        move('down', id);
    }

    function deleteModal(id)
    {
        let currentId = id
        Swal.fire({
            title: deleteModalDetails.title,
            cancelButtonText: deleteModalDetails.cancel,
            confirmButtonText: deleteModalDetails.confirm,
            text: deleteModalDetails.text,
            showCancelButton: true,
            customClass: {
                cancelButton: 'call-to-action',
                confirmButton: 'call-to-action',
                actions: 'swal-action-style',
            },
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: async () => {
                list.style.opacity = 0;
                await $.ajax({
                    url: `${deleteUrl}/${currentId}`,
                    success: () => {
                        refreshList();
                    }
                })
            },
            preDeny() {
                currentId = null;
            }
        })
    }
    function editLegendModal(id, legend)
    {
        let currentId = id
        Swal.fire({
            title: editLegendModalDetails.title,
            cancelButtonText: deleteModalDetails.cancel,
            confirmButtonText: deleteModalDetails.confirm,
            showCancelButton: true,
            customClass: {
                cancelButton: 'call-to-action',
                confirmButton: 'call-to-action',
                actions: 'swal-action-style',
            },
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            html: $('#edit-photo-modal').html() ?? '',
            willOpen(){
                $component[0].querySelector('[name="legend"]').value = legend
            },
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: async () => {
                list.style.opacity = 0;
                await $.ajax({
                    url: `${editUrl}/${currentId}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: currentId,
                        legend:  $component[0].querySelector('[name="legend"]').value
                    },
                    success: () => {
                        refreshList();
                    }
                })
            },
            preDeny() {
                currentId = null;
            }
        })
    }

    async function switchToggle(id, switchEl)
    {

        let url = promotionSwitchUrl ?? jobOfferSwitchUrl
        switchEl.disabled = true;
        await $.ajax({
            url: `${url}/${id}`,
            success: async ()=>{
                switchEl.disabled = false;
            }
        });


    }

    function listeners() {
        let elList = list.querySelectorAll('.list__item');
        elList.forEach((el) => {
            el.querySelector('.up-button')?.addEventListener('click', async (e) => {
                await upArrow(el.dataset.id);
            });
            el.querySelector('.down-button')?.addEventListener('click', async (e) => {
                await downArrow(el.dataset.id);
            });
            el.querySelector('.delete-button')?.addEventListener('click', async ()=>{
                await deleteModal(el.dataset.id);
            });

            if(isPromotions) {
                el.querySelector('[name="in_progress"]')?.addEventListener('change', async function() {
                    await switchToggle(el.dataset.id, this)
                })
            }

            if(jobOfferSwitchUrl) {
                el.querySelector('[name="in_progress"]')?.addEventListener('change', async function() {
                    await switchToggle(el.dataset.id, this)
                })
            }
            if(isPhotos) {
                el.querySelector('.edit-legend')?.addEventListener('click', async function() {

                    editLegendModal(el.dataset.id, el.dataset.legend)
                })
            }

        });
    }
}