<?php


/**
 * MENU PRINCIPAL
 */

use App\Models\Core\BasicEvent;
use App\Models\Core\CategoryGroup;
use App\Models\Core\Forms\ChoiceGroup;
use App\Models\Core\Model;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use App\Models\Core\PurchasedSub;
use App\Models\Core\Order;
use App\Models\Core\PriceCut;
use App\Models\Core\Forms\FormAnswer;
use App\Models\Core\Forms\FormGenerator;
use App\Models\Core\Gallery;
use App\Models\Core\GoogleMapGroup;
use App\Models\Core\MenuTree;
use App\Models\Core\MiniCardGroup;
use App\Models\Core\News;
use App\Models\Core\Onglet;
use App\Models\Core\Page;
use App\Models\Core\PubGroup;
use App\Models\Core\SideMenu;
use App\Models\Core\Slideshow;
use App\Models\Core\User;
use Symfony\Component\Finder\Finder;

View::composer('partials.sidemenu', function ($view) {
	$view->with('menus', SideMenu::getAll());
	$view->with('user', Auth::guard('users')->user());
});

/**
 * ONGLET - NAVIGATION HORIZONTALE D'UNE ENTITÉ
 */
View::composer('partials.onglet', function ($view) {
	$view->with('onglets', Onglet::findOnglets(Request::segment(2)));
});


/**
 * MESSAGE FLASH DE SUCCES OU ERREUR
 */
View::composer('partials.message', function ($view) {
	$view->with('flash', [
		'success' => Session::get('success'),
		'error' => Session::get('error')
	]);
});

View::composer('grid.menu_trees', function ($view) {
	$locales = getLocales();
	$collections = MenuTree::where('parent_id', null)
		->get()
		->toJson(JSON_HEX_APOS);
	$enumGroup = MenuTree::getEnum('group')
		->toJson(JSON_HEX_APOS);
	$pages = Page::all()
		->toJson(JSON_HEX_APOS);
	$view->with(compact('locales', 'collections', 'enumGroup', 'pages'));
});

View::composer('generic.dashboard', function ($view) {

	$extract_elements = function($elements, &$names) use (&$extract_elements) {
		foreach ($elements as $element) {
			$names[(string)$element->Identifiant] = (string)$element->Nom;
			if (count($element->SideMenuItems)) {
				$extract_elements($element->SideMenuItems->SideMenuItem, $names);
			}
		}
	};

	$names = [];
	$extract_elements(SideMenu::getAll(), $names);

	$class_names = [
		User::class,
		Gallery::class,
		Slideshow::class,
		PubGroup::class,
		CategoryGroup::class,
		GoogleMapGroup::class,
		MiniCardGroup::class,
		Page::class,
		ChoiceGroup::class,
		FormGenerator::class,
		FormAnswer::class,
		//		News::class,
		// Document::class,
		//		BasicEvent::class,
		Subscriber::class,
		Subscription::class,
		PurchasedSub::class,
		Order::class,
		PriceCut::class
	];

	$models = array_map(static function ($class_name) use ($names) {

		if (class_exists($class_name)) {

			/** @var Model $model */
			$model = new $class_name;

			$name = Arr::get($names, $model->getTable(), '(' . $class_name . ')');

			if (in_array('active', $model['fillable'], true)) {
				$actives = $model::where('active', 1)->count();
				$inactives = $model::where('active', 0)->count();
				$total = $actives + $inactives;
			} else {
				$actives = '';
				$inactives = '';
				$total = $model::count();
			}
		} else {

			$name = 'Élément introuvable (' . $class_name . ')';
			$actives = 'N/D';
			$inactives = 'N/D';
			$total = 'N/D';
		}

		return (object)[
			'name' => $name,
			'actives' => $actives,
			'inactives' => $inactives,
			'total' => $total
		];
	}, $class_names);

	$view->with(compact('models'));
});

View::composer('generic.smtp', function ($view) {
	$smtp = [
		'MAIL_MAILER' => null,
		'MAIL_HOST' => null,
		'MAIL_PORT' => null,
		'MAIL_ENCRYPTION' => null,
		'MAIL_USERNAME' => null,
		'MAIL_PASSWORD' => null,
		'MAIL_LOG_CHANNEL' => null,
		'MAIL_FROM_ADDRESS' => null,
		'MAIL_FROM_NAME' => null,
		'VERIFY_PEER' => null,
		'VERIFY_PEER_NAME' => null,
		'ALLOW_SELF_SIGNED' => null,
	];

	foreach ($smtp as $key => $item) {
		$value = env($key);

		$smtp[$key] = $value;
	}

	$view->with(compact('smtp'));
});
