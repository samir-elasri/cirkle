const serviceInputTemplate = document.getElementById('serviceInputTemplate');
const servicesContainer = document.getElementById('services')

let serviceIndex = -1;

const makeServiceInput = () => {
  const index = ++serviceIndex;

  const result = serviceInputTemplate.content.cloneNode(true);

  const elFr = result.querySelector('input.fr')
  const elEn = result.querySelector('input.en')

  elFr.name = 'services[' + serviceIndex + '][fr][title]';
  elEn.name = 'services[' + serviceIndex + '][en][title]';

  elFr.addEventListener('focus', () => {
    if (index + 1 > serviceIndex) {
      makeServiceInput();
    }
  });
  elEn.addEventListener('focus', () => {
    if (index + 1 > serviceIndex) {
      makeServiceInput();
    }
  });

  servicesContainer.appendChild( result);
}

makeServiceInput();
makeServiceInput();
