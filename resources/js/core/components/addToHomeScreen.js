export default ($component, elements, attributes, properties) => {
    const LOCAL_STORAGE_KEY = 'add-to-home-screen-prompt';

    const {
        options,
        androidContent,
        iosContent,
        title
    } = properties;

    const is_iOs = !!navigator.platform && /iPad|iPhone|iPod/.test(navigator.platform);

    if ('serviceWorker' in navigator && is_iOs && !localStorage.getItem(LOCAL_STORAGE_KEY)) {
        Swal.fire({
            ...options,
            title,
            html: iosContent
        });

        localStorage.setItem(LOCAL_STORAGE_KEY, ` ${ Date.now() }`);
    }

    // Code to handle install prompt on desktop
    let deferredPrompt;

    if (is_iOs) {
        $component.siblings('.download-procedure').css('display', 'block');
    }
    $component.css('display', 'none');

    if (!localStorage.getItem(LOCAL_STORAGE_KEY)) {
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();

            // Stash the event so it can be triggered later.
            deferredPrompt = e;

            if (!localStorage.getItem(LOCAL_STORAGE_KEY)) {
                Swal.fire({
                    ...options,
                    title,
                    html: androidContent
                }).then(result => {
                    if (result.isConfirmed) {
                        deferredPrompt.prompt();

                        deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the A2HS prompt');
                            } else {
                                console.log('User dismissed the A2HS prompt');
                            }
                            deferredPrompt = null;
                        });
                    }
                });

                localStorage.setItem(LOCAL_STORAGE_KEY, ` ${ Date.now() }`);
            }
        });
    }
};
