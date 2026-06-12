import $ from 'jquery';

/*
	|-- Tile width --|
	 ________________
	| |            | |
	| |    Tile    | |
	|_|____________|_|

	|-| Tile spacing
*/

class TileGrid {

	constructor($component) {

		this.uid = 'tile-grid-' + Math.random().toString(36).substr(2);

		this.$el = $component;

		this.$container = $('<div></div>');
		this.$container.addClass('tile-grid');
		this.$container.addClass(this.uid);

		this.$container.append(this.$el.children());
		this.$el.append(this.$container);

		this.tileMinSpacing = parseInt(this.getData('tile-grid-spacing', 20), 10) * 0.5;
		this.tileMaxWidth = parseInt(this.getData('tile-grid-width', 300), 10) + this.tileMinSpacing * 2;


		this.tileSpacing2 = this.tileMinSpacing * 2;

		this.$style = $('<style></style>');
		$(document.head).append(`<!-- ${this.uid} -->`);
		$(document.head).append(this.$style);

		$(window).resize(this.layout.bind(this));

		this.$el.closest('.wait-ready').removeClass('wait-ready').addClass('ready');
		this.layout();

	}

	layout() {

		this.$style.text('');


		const containerWidth = this.$container.parent().innerWidth() + this.tileSpacing2;
		const count = this.$container.children().length;
		const tilePerRow = Math.max(1, Math.min(count, Math.floor(containerWidth / this.tileMaxWidth)));
        const tileWidth = Math.min(this.tileMaxWidth, Math.floor(containerWidth / tilePerRow));
		const spaceLeft = containerWidth - tileWidth * tilePerRow;
		const division = 2 * (tilePerRow + (tilePerRow > 2 ? -1 : 0));
		const tileMargin = (tilePerRow === 1 ? 0 : this.tileMinSpacing) + Math.floor(spaceLeft / division);
		const containerMargin = tilePerRow > 2 ? -tileMargin : (tilePerRow === 1 ? 0 : -this.tileMinSpacing);

		let style = '';

		if (tilePerRow == 2) {

			const margin = tileMargin - this.tileMinSpacing;
			let marginOut = margin * 1.5 + this.tileMinSpacing;
			let marginIn = margin * 0.5 + this.tileMinSpacing;

			if (marginIn < this.tileMinSpacing) {
				marginOut += marginIn - this.tileMinSpacing;
				marginIn = this.tileMinSpacing;
			}

			style +=
				`.${this.uid} { display: flex; flex-wrap: wrap; margin: ${-this.tileMinSpacing}px ${containerMargin}px !important; }\n` +
				`.${this.uid}>* { width: ${tileWidth - this.tileSpacing2}px; }\n` +
				`.${this.uid}>*:nth-child(2n+1) { margin: ${this.tileMinSpacing}px ${marginIn}px ${this.tileMinSpacing}px ${marginOut}px !important; }\n` +
				`.${this.uid}>*:nth-child(2n+2) { margin: ${this.tileMinSpacing}px ${marginOut}px ${this.tileMinSpacing}px ${marginIn}px !important; }\n`;

		} else {

			style +=
				`.${this.uid} { display: flex; flex-wrap: wrap; margin: ${-this.tileMinSpacing}px ${containerMargin}px !important; }\n` +
				`.${this.uid}>* { margin: ${this.tileMinSpacing}px ${tileMargin}px !important; width: ${tileWidth - this.tileSpacing2}px; }\n`;
		}


		this.$style.text(style);
	}

	getData(key, defaultValue) {
		const value = this.$el.data(key);
		return value == null ? defaultValue : value;
	}
}

export default ($component) => new TileGrid($component);
