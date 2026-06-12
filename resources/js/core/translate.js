const translations = {};
let currentLocale;

window.__ = __;
window.getLocale = getLocale;

export function __(key, replace = {}, locale = null) {

	const translation = getTranslation(locale ?? getLocale());

	return key in translation
		? makeReplacements(translation[key], replace)
		: key ?? '';
}

export function getTranslation(locale) {

	return translations[locale] ?? loadFromJson(locale);
}

export function loadFromJson(locale) {

	$.ajax({
		url: '/dist/compiled/lang/' + locale + '.json',
		success: function (response) {
			translations[locale] = (typeof response === 'object') ? response : [];
		},
		async: false
	});

	return translations[locale] ?? {};
}

export function makeReplacements(text, replace) {
	if (typeof text === 'string' && typeof replace === 'object') {
		for (const match of text.matchAll(/(:([_\w\d]+))/g)) {
			text = text.replaceAll(match[1], replace[match[2]] ?? '')
		}
	}

	return text;
}

export function getLocale()
{
	return currentLocale
		?? (currentLocale = document.querySelector('html').getAttribute('lang'));
}
