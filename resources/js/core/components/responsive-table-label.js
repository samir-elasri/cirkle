import $ from 'jquery';
import components from "../components";

export default ($component, elements, attributes, properties) => {

	const $table = $component;
	const $thead = $table.find('thead');
	const $tbody = $table.find('tbody');

	if ($thead.length > 0 && $tbody.length > 0) {

		const labels = [];

		$thead.find('td, th').each((k, cell) => {
			labels.push($(cell).text());
		});

		$tbody.find('tr').each((k, row) => {

			const $cells = $(row).children();

			if ($cells.length == labels.length) {

				$cells.each((key, cell) => {

					const $cell = $(cell);

					$cell.data('label', labels[key]);
					$cell.html('<span class="responsive-table-label">' + labels[key] + '</span>' + $cell.html());
				});
			}
		});
	}

	components($component);
};
