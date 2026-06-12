import $ from 'jquery';

// Dictionnaire de toutes les composantes disponibles.
let list = {};

/**
 * Analyse la cible et initialise les composantes trouvées.
 *
 * @param target
 * @param components
 */
export default (target, components = null) => {

	if (components !== null) Object.assign(list, components);

	$('[data-component]', target).each((i, el) => {

		const $component = $(el);

		// Nom de la composante.
		const name = $component.data('component');

		// Fonction initiatrice de la composante.
		const fn = list[name];

		if (fn && fn.default) {

			// Initisalisation de la composante.
			try {
				fn.default(
					$component,
					mapReferences($component),
					mapAttributes($component),
					mapProperties($component)
				);
			} catch (error) {
				console.error(error);
				// expected output: ReferenceError: nonExistentFunction is not defined
				// Note - error messages will vary depending on browser
			}

		} else {

			console.error(`data-component : Invalid component name '${name}'`, el);
		}
	});
};

/**
 * Retourne un dictionnaire de tous les éléments à l'intérieur de la composante ayant l'attribut [data-ref].
 * La valeur de l'attribut sert de clé à l'intérieur de l'objet.
 *
 * @param $component
 */
function mapReferences($component) {

	const references = {};

	$component.find('[data-ref]').each((i, element) => {

		const name = element.dataset.ref;
		const $name = '$' + name;
		const current = references[name];

		if(Array.isArray(current)) {
			references[name].push(element);
			references[$name] = references[$name].add(element);
		} else if(current) {
			references[name] = [current, element];
			references[$name] = references[$name].add(element);
		} else {
			references[name] = element;
			references[$name] = $(element);
		}
	});

	return references;
}

/**
 * Retourne un dictionnaire de tous les data attributs, à l'exception de [data-component] et [data-ref].
 * La clé est le nom du data attribut sans le préfix [data-].
 *
 * @param $component
 */
function mapAttributes($component) {

	const data = $component.data();

	delete data['component'];
	delete data['ref'];

	return data;
}

/**
 * Retourne un objet créer à partir de la première balise script contenant du json.
 *
 * @param $component
 */
function mapProperties($component) {

	const $script = $component.find('script[type="application/json"]').first();

	return $script.length ? JSON.parse($script.text()) : {};
}
