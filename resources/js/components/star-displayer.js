import $ from 'jquery';

export default ($component, elements, attributes, properties) => {

    const {
        value,
        type
    } = properties;

    function roundToNearestFiveCents(number) {
        return parseFloat((Math.round(number / 0.5) * 0.5).toFixed(1));
    }

    let newnumber = roundToNearestFiveCents(value)

    let starcounter = 0;

    let stars = '<div>';
    const star = '<i class="fa fa-star"></i>'
    const halfstar = '<i class="fas fa-star-half-alt"></i>'
    const emptystar = '<i class="far fa-star"></i>'

    while (newnumber > 0) {
        stars += star
        starcounter++;
        newnumber--;
        if (newnumber < 1 && newnumber > 0) {
            stars += halfstar
            starcounter++;
            newnumber--;
        }
    }

    while (starcounter < 5) {
        stars += emptystar
        starcounter++;
    }
    stars += '</div>'
    $component.append(stars);


}
