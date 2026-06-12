import Inputmask from 'inputmask';

/**
	Récupère tous les champs et applique le masque s'il y a lieu.

	Alias de base: email, currency, decimal, integer, date, datetime, dd/mm/yyyy

	i.e.:
		<input type="tel" data-inputmask-alias="phone"/>
		<input type="text" data-inputmask-alias="email"/>
		<input type="text" data-inputmask-alias="currency"/>

	@see https://github.com/RobinHerbots/Inputmask
*/
export default function() {

	Inputmask.extendDefaults({
		removeMaskOnSubmit: true
	});

	Inputmask.extendAliases({
		// Dépend du pays...
		// postal: { mask: 'A9A 9A9' }, 
		phone: { mask: '[9 ]{0,1}(999) 999-9999', numericInput: true },
		// Cause une erreur, car le type email ne supporte pas la sélection...
		//email: { supportsInputType: ['email'] } 
	})

	Inputmask().mask(
		document.querySelectorAll('input')
	);
}