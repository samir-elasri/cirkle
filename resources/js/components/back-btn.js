export default ($component,) => {
    const containerElement = $component[0];
    const textLabel = containerElement.dataset.text; 

    const hasPreviousPage = history.length > 1;
    const isSameOrigin = document.referrer && (new URL(document.referrer)).origin === window.location.origin;
    
    if (hasPreviousPage && isSameOrigin) {
        const button = document.createElement('button');
        button.textContent = textLabel;
        button.classList.add('call-to-action');
        button.type = 'button';

        button.addEventListener('click', function(e) {
            e.preventDefault();
            history.back();
        });
        
        containerElement.appendChild(button);
    }
}
