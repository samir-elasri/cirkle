import $ from 'jquery';
import tileGrid from '../tile-grid';

export default ($component) => {
	tileGrid($component);
	$component.find('.single--flip').each((i, tile) => {
		$(tile).click(() => $(tile).toggleClass('flipped'));
	});
};
