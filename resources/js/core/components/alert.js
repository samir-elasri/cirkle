import $ from 'jquery';

export default ($component) => $component.find('.alert__times').on('click', () => $component.hide());
