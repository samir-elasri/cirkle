
/*
Exemple:

<select data-component="dropdown" data-dropdown-search="true" multiple>
    <option value="">Veuillez sélectionner</option>
    <option value="1">One</option>
    <option value="2">Two</option>
    <option value="3">Three</option>
</select>
*/

/**
 * Dropdown (semantic-ui - https://semantic-ui.com)
 *
 * data-dropdown-search:
 *     Specifying to "true" will use a fuzzy full text search, setting to "exact" will force the exact search to be matched somewhere in the string, setting to "false" will only match start of string.
 *
 * data-dropdown-sort-select:
 *     Whether to sort values when creating a dropdown automatically from a select element.
 *
 * data-dropdown-clearable:
 *     Whether the dropdown value can be cleared by the user after being selected.
 */

export default ($component, elements, attributes) => {

	const fullTextSearch = attributes['dropdownSearch'] === true;
	const sortSelect = attributes['dropdownSortSelect'] === true;
	const clearable = attributes['dropdownClearable'] === true;
    let useLabels = true;
    let message;

	$component.addClass('ui ' + (fullTextSearch ? 'search' : '') + ' dropdown');

    if ($component.data('labels') === false) {
        useLabels = false;
        message = {count: `{count} ${$component.data('count')}`}
    }

	$component.dropdown({
		fullTextSearch,
		sortSelect,
		clearable,
        useLabels,
        message
	});
};
