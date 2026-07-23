<?php

namespace App\Http\Controllers;

use App;
use App\Mail\AdminMail;
use App\Models\Core\BasicEvent;
use App\Models\Core\Bloc;
use App\Models\Core\Blocs\BlocForm;
use App\Models\Core\Category;
use App\Models\Core\Country;
use App\Models\Core\Forms\FormAnswer;
use App\Models\Core\Forms\FormGenerator;
use App\Models\Core\Gallery;
use App\Models\Core\News;
use App\Models\Core\Page;
use App\Models\Core\PubGroup;
use App\Models\Core\Sharing;
use App\Models\Core\State;
use App\Models\Core\Subscription;
use App\Models\Translations\BlocFormTranslation;
use App\Models\Translations\CountryTranslation;
use App\Models\Translations\StateTranslation;
use Arr;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request as RouteRequest;
use Illuminate\Support\Collection;
use Mail;
use Redirect;
use Request;
use Route;
use RuntimeException;
use Session;
use Throwable;
use Validator;
use View;

class PageController extends Controller
{
	use StructuredDataTrait;

	/**
	 * Page intégrée
	 *
	 * @param  RouteRequest  $request
	 * @param  mixed  ...$args
	 * @return array|\Illuminate\Contracts\View\View|string
	 * @throws Throwable
	 */
	public function integrated(RouteRequest $request, ...$args)
	{
		// Nom de la route
		$name = Route::currentRouteName();
		$isCollection = false;

		// Récupère les paramètres de la route
		$config = config('routes.front-end');
		$route = Arr::get($config, $name, []);

		// Détermine si la route est gérée par une autre
		$admin = Arr::get($route, 'admin');
		$label = is_string($admin) ? $admin : $name;

		// Récupère la page standard associée ou l'entité associée s'il y a lieu
		$collectionName = Arr::get($route, 'collection', '');
		$className = Arr::get(config('classmap'), $collectionName);

		if ($className && class_exists($className)) {
			/** @var App\Models\Core\Model $className */
			$page = $className::find(Route::input('id'));
			$isCollection = true;

		} else {
			$page = Page::whereIntegrated(true)
				->whereLabel($label)
				->first();
		}

		// Récupère les paramètres de la page intégrée
		$routeAttributes = Arr::get($route, 'page', []);
		$attributes = $page ? [] : $routeAttributes;

		// Défini les urls personnalisés, afin de permettre le changement de langue
		$uri = Arr::get($route, 'uri', []);
		foreach (getLocales() as $locale) {
			$attributes[$locale]['custom_url'] = $this->generateRouteUri(
				$locale,
				Arr::get($uri, $locale),
				Arr::get($route, 'translate_uri')
			);
		}

		// Applique les paramètres
		if ($page) {
			$page->fill($attributes);

			if (Arr::has($routeAttributes, 'restricted')) {
				$page->restricted = $routeAttributes['restricted'];
			}

		} else {
			$page = new Page($attributes);
		}

		// Configure les paramètres de rendu de la page
		$params = $this->setupParams($page, [], 'integrated', $isCollection);

		if ($isCollection) {
			$params['restricted'] = Arr::get($route, 'page.restricted', false);
		}

		// Rend les blocs de la page
		$params['blocs'] = Bloc::renderBlocs($page);

		// Défini le blade à rendre pour le contenu de la page
		$params['view_name'] = Arr::get($route, 'view', '');

		// Détermine si la page à un controlleur
		$uses = Arr::get($route, 'uses', false);
		if ($uses) {
			$restricted = Arr::get($params, 'restricted', false);

			// Check not restricted first to lessen Auth guard load.
			if (!$restricted || logged_in()) {
				$method = strtoupper(Request::method());

				if (is_array($uses) && array_key_exists($method, $uses)) {
					$uses = $uses[$method];
				}

				if (!is_array($uses)) {
					// Divise la chaîne de charactères pour récupérer le nom du controlleur et le nom de la méthode à appeler
					[$controller, $method] = explode('@', $uses);

					// Appel la méthode du controlleur avec les paramètres de rendu de la page
					$params = app($controller)->{$method}($params, $request, ...$args);

					// Si le paramètre n'est pas un array, permet de retourner un json ou de faire une redirection au lieu d'un rendu de page
					if (!is_array($params)) {
						return $params;
					}
				}
			}
		}

		return $this->render($params);
	}

	/**
	 * Génère l'uri de la route d'une page intégrée
	 *
	 * @param $locale
	 * @param $uri
	 * @param $action
	 * @return string|string[]
	 */
	public function generateRouteUri($locale, $uri, $action)
	{
		if (empty($action)) {

			$params = [];
		} else {

			// Divise la chaîne de charactères pour récupérer le nom de la classe et le nom de la méthode à appeler
			[$class, $method] = explode('@', $action);

			// Appel la méthode de la classe avec la locale désirée et récupère les paramètres traduits
			$params = $class::{$method}($locale);
		}

		// Détermine et remplace les paramètres de la route
		if (preg_match_all('/{(\w+)\??}/', $uri, $matches, PREG_SET_ORDER)) {
			foreach ($matches as [$pattern, $name]) {
				$uri = str_replace($pattern, Arr::get($params, $name, Route::input($name)), $uri);
			}
		}

		return $uri;
	}

	/**
	 * @param BasicEvent|News|Page $page
	 * @param $params
	 * @param $type
	 * @param bool $isCollection
	 * @return array
	 */
	public function setupParams(Page|News|BasicEvent $page, $params, $type, bool $isCollection = false): array
	{

		// Récupère les paramètres du site
		$settings = setting();

		// Configure le changement de langue
		$this->setupLocaleLinks($type, $page, $isCollection);

		// Initialise les paramètres de rendu de la page
		$params = array_merge($page->toArray(), $params);

		// Défini le type de page
		$params['page_type'] = $page->page_type = $type;

		// Si en mode admin, défini le url pour éditer le bloc
		if (is_admin()) {
			if (!isset($params['edit_url'])) {
				$page_id = !empty($page->id) ? $page->id : Route::currentRouteName();

				$params['edit_url'] = adminRouteName("admin.{$page->collection_name}.edit", [$page_id]);
			}
		} else {

			$params['edit_url'] = false;
		}

		// Configure les métadonnées
		$params['metadata'] = $this->createMetaFromPage($page);

		// Aligne les pubs avec le premier bloc de page
		/** @var Bloc $bloc */
		$bloc = $page->blocs()->where('active', true)->orderBy('position')->first();
		$params['first_bloc_top_spacing'] = $bloc && $bloc->top_spacing !== null ? $bloc->top_spacing : $settings->default_bloc_spacing;

		// Configure les publicités
		$pubGroup = PubGroup::find($page->getAttribute('pub_group_id') ?? $settings->pub_group_id);
		$params['pubs'] = $pubGroup ? $pubGroup->pubs()->whereActive(true)->orderBy('position')->get() : new Collection;

		// Retourne les paramètres de rendu
		return $params;
	}

	/**
	 * Configuration des paramètres de rendu
	 *
	 * @param $params
	 * @return \Illuminate\Contracts\View\View|string
	 * @throws Throwable
	 */
	public function render($params)
	{
		$settings = setting();
		if ($settings->maintenance && !is_admin()) {
			return view('core.pages.maintenance');
		}

		// Crée la vue pour la page
		$view = view('core.layouts.page', $params);
		$restricted = Arr::get($params, 'restricted', false);
		// Si la page est restricted, on rend seulement un contenu de connexion
		if (!$restricted || logged_in()) {
			// Défini le blade à rendre pour le contenu de la page
			$view_name = Arr::get($params, 'view_name');

			if (!View::exists($view_name)) {
				Arr::pull($params, 'view_name');
				$params['content'] = Arr::get($params, 'blocs', '');
			}

			$view->with($params);

		} else {
			$view->with(['view_name' => 'core.partials.restricted']);
		}

		// Rend la page
		return $view->render();
	}

	/**
	 * @param $type
	 * @param  null|Page|string  $page
	 * @param  bool  $isCollection
	 */
	private function setupLocaleLinks($type, $page = null, $isCollection = false): void
	{
		// Récupère la langue courante
		$current = app()->getLocale();

		// Récupère la liste des langues disponibles
		$locales = getLocales();

		// Liste des liens pour les changements de langues
		$links = [];

		if (isMultilingual()) {
			foreach ($locales as $locale) {

				if ($locale === $current) {
					continue;
				}

				// Génère l'url
				if ($type === 'error') {
					$url = "";
				} elseif ($page instanceof Page) {
					$url = $page->getUrl($locale);
				} elseif ($isCollection) {
					$name = Route::currentRouteName();
					$config = config('routes.front-end');
					$route = Arr::get($config, $name, []);

					$tempPage = Page::whereIntegrated(true)
						->whereLabel($name)
						->first();

					if (!$tempPage) {
						$tempPage = new Page(Arr::get($route, 'page', []));
						$tempPage->integrated = true;
						$tempPage->label = $name;
					}
					$url = $tempPage->getUrl($locale);

				} elseif (is_string($page)) {
					$url = $page;
				} else {
					$url = '/' . $locale . substr(Request::path(), strlen($locale));
				}

				if (!empty(Request::getQueryString())) {
					$url .= '?' . Request::getQueryString();
				}

				$links[$locale] = $url;
			}
		}

		View::share('localeLinks', $links);
	}

	/**
	 * Page standard
	 *
	 * @return \Illuminate\Contracts\View\View|string
	 * @throws Throwable
	 */
	public function standard()
	{

		// Récupère le id de la page
		$id = Route::input('id');

		$page = Page::adminMode()->where('id', $id)->first();

		// La page n'existe pas
		if ($page === null) {
			App::abort(404);
		}

		// Configure les paramètres de rendu de la page
		$params = $this->setupParams($page, [], 'standard');

		// Rend les blocs de la page
		$params['blocs'] = Bloc::renderBlocs($page);

		$params['structured_datas'] = $this->getStructuredDatas($page);

		// Pages standard qui possèdent une mise en page dédiée (blade) — p. ex. la page
		// Contact de Denis (id 29). On garde l'URL /{id}/{slug} existante mais on rend
		// pages.<label> s'il existe (même convention que custom()). Sinon rendu par blocs.
		$customStandardViews = [
			'contact' => 'pages.contact',
		];
		$customView = $customStandardViews[$page->label] ?? null;
		if ($customView && View::exists($customView)) {
			$params['view_name'] = $customView;
		}

		// Retourne le rendu de la page
		return $this->render($params);
	}

	/**
	 * Formulaire « Contact rapide » de la page Contact (Denis 22.07).
	 * Anti-bot : honeypot (ck_website) + middleware recaptcha (si activé plus tard).
	 * Le courriel est envoyé à settings.reception_email.
	 *
	 * @param  RouteRequest  $request
	 * @return RedirectResponse
	 */
	public function contactUs(RouteRequest $request): RedirectResponse
	{
		$loc = app()->getLocale();

		$thanks = $loc === 'en'
			? 'Thank you for contacting us! Please allow 24 to 48h for a reply by email or phone. Subject to holidays or acts of nature.'
			: 'Merci de prendre contact avec nous ! 24h à 48h pour une réponse par courriel ou téléphone. Selon les jours fériés ou en raison de mère nature.';

		// Honeypot : un robot remplit ce champ caché → on ignore silencieusement
		// (on renvoie quand même le remerciement pour ne pas révéler le piège).
		if (!empty($request->input('ck_website'))) {
			return Redirect::back()->with('success', $thanks);
		}

		$data = $request->validate([
			'name'    => ['required', 'string', 'max:150'],
			'email'   => ['required', 'email', 'max:190'],
			'phone'   => ['nullable', 'string', 'max:50'],
			'message' => ['required', 'string', 'max:6000'],
		]);

		$to = setting()->reception_email ?: config('mail.from.address');

		$body = ($loc === 'en' ? "New message from the Contact form:\n\n" : "Nouveau message du formulaire Contact :\n\n")
			. "Nom : {$data['name']}\n"
			. "Courriel : {$data['email']}\n"
			. 'Téléphone : ' . ($data['phone'] ?: '—') . "\n\n"
			. "Message :\n" . $data['message'] . "\n";

		// Envoi en texte simple (Mail::raw) : fiable et sans dépendance au gabarit
		// « mail::raw » d'AdminMail qui n'existe pas dans ce projet. Reply-To = le
		// visiteur, pour que Cirkle réponde directement au courriel du client.
		try {
			Mail::raw($body, static function ($m) use ($to, $data) {
				$m->to($to)
					->subject('Contact rapide — cirkleservices.com')
					->replyTo($data['email'], $data['name']);
			});
		} catch (Throwable $e) {
			\Log::error('Contact form mail failed: ' . $e->getMessage());
			return Redirect::back()
				->with('error', $loc === 'en'
					? 'Sorry, your message could not be sent. Please try again later.'
					: "Désolé, votre message n'a pas pu être envoyé. Veuillez réessayer plus tard.")
				->withInput();
		}

		return Redirect::back()->with('success', $thanks);
	}

	/**
	 * @return \Illuminate\Contracts\View\View|string
	 * @throws Throwable
	 */
	public function custom()
	{
		// Nettoie les barres obliques en trop du chemin de la requête
		$path = trim(Request::path(), '/');

		// Retrait de la locale du chemin de la requête
		if (isMultilingual()) {
			$cleanedPath = '/' . substr($path, min(strlen($path), strlen(App::getLocale()) + 1));
		} else {
			$cleanedPath = '/' . $path;
		}

		// Récupère le id de la page associée
		$id = Arr::get(Page::getRoutes(), $cleanedPath);

		$page = Page::adminMode()->where('id', $id)->first();

		// La page n'existe pas
		if ($page === null) {
			App::abort(404);
		}

		// Configure les paramètres de rendu de la page
		$params = $this->setupParams($page, [], 'custom');

		$custom = $page->custom_code;

		// Défini le blade à rendre pour le contenu de la page
		if (!empty($custom)) {

			// Vérifie si la page existe pour le site
			$view_name = 'pages.' . $page->custom_code;
			if (View::exists($view_name)) {

				$params['view_name'] = $view_name;
			} else {

				// Autrement vérifie si la page existe dans le noyau
				$view_name = 'core.pages.' . $page->custom_code;
				if (View::exists($view_name)) {
					$params['view_name'] = $view_name;
				}
			}
		}

		// Rend les blocs de la page
		$params['blocs'] = Bloc::renderBlocs($page);

		$params['structured_datas'] = $this->getStructuredDatas($page);

		// Retourne le rendu de la page
		return $this->render($params);
	}

	/**
	 * Error Pages (404, 500)
	 *
	 * @param  Throwable  $exception
	 * @param  int  $code
	 * @return array|string
	 * @throws Throwable
	 */
	public function error(Throwable $exception, $code = 404)
	{
		$page = Page::getByLabel('not-found');

		// Doit prendre en considération que c'est possible que le premier segment ne soit pas une langue
		// i.e.: Quand c'est un site unilingue
		// La locale est défini à partir du middleware Localization bien avant d'arriver à cette endroit
		// Ce fixe peut causer une erreur qui surpasse l'erreur original et induit en erreur l'analyse
		// Ou vestige du passé et donc on peu s'en passer?
		// À revoir si nécessaire, bogue récurrant
		//$locale = Request::segment(1);
		//app()->setLocale($locale);

		$requestLocale = Request::segment(1);
		$locale = in_array($requestLocale, getLocales(), true)
			? $requestLocale
			: config('app.fallback_locale', 'fr');

		app()->setLocale($locale);

		if (!$page) {
			$page = new Page([
				'title' => __('main.page-not-found.title'),
			]);
		}

		$params = $this->setupParams($page, [], 'error');

		$params['metadata'] = $this->createMeta('Nous ne trouvons pas la page recherchée', 'Erreur : ' . $code);

		$params['exception'] = $exception;

		$params['blocs'] = Bloc::renderBlocs($page);

		$params['view_name'] = 'core.pages.page-not-found';

		$view = view('core.layouts.page', $params);

		$view_name = Arr::get($params, 'view_name');

		if (!View::exists($view_name)) {
			Arr::pull($params, 'view_name');
			$params['content'] = Arr::get($params, 'blocs', '');
		}

		$view->with($params);

		return $view->render();
	}

	/**
	 * TODO: Form
	 *
	 * @return RedirectResponse
	 * @throws Exception
	 */
	public function handleGeneratedForm(): RedirectResponse
	{
		$bloc_id = Request::get('bloc_id');
		$id = Route::input('id');
		$form = FormGenerator::find($id);

		/** @var BlocForm|BlocFormTranslation $bloc */
		$bloc = BlocForm::find($bloc_id);

		$answers = Request::except(['_token', 'current_url', config('google.recaptcha.input_name')]);

		$validator = Validator::make($answers, $form->compileRulesArray($answers));

		if ($validator->fails()) {

			return Redirect::back()->withErrors($validator)->withInput();
		}

		if ($bloc->email_confirm_send && (isset($answers['email']) || isset($answers['courriel']))) {
			$mail = $answers['email'] ?? $answers['courriel'];

			$m = Mail::to($mail);

			if (!empty($bloc->email_confirm_bcc)) {
				$bccArr = explode(';', $bloc->email_confirm_bcc);
				$m->bcc($bccArr);
			}

			$m->send(new AdminMail($bloc->email_confirm_content, $bloc->email_confirm_title, $bloc->email_confirm_from, $bloc->email_confirm_name));
		}

		if ($bloc->email_alert_send && $bloc->email_alert_to) {
			$m = Mail::to($bloc->email_alert_to);
			$m->send(new AdminMail($bloc->email_alert_content, $bloc->email_alert_title, $bloc->email_alert_from, $bloc->email_alert_name));
		}

		$this->uploadFiles($this->fileInputs($answers), $id);
		$answers = $this->excludeNull($answers);
		FormAnswer::saveAnswers($id, $answers);
		Session::flash('success', $bloc->message);
		return Redirect::to(Request::get('current_url'));
	}

	/**
	 * @param $files
	 * @param  string  $prefix
	 * @return array|bool
	 * @throws Exception
	 */
	private function uploadFiles($files, string $prefix)
	{
		if (empty($files)) {
			return false;
		}

		$filenames = [];

		foreach ($files as $inputName => $file) {
			if ($file === null) {
				throw new RuntimeException('The file input is empty');
			}
			$file = Request::file($inputName);
			$filename = $prefix . '_' . $file->getClientOriginalName();
			$file->move(public_path() . '/medias/forms', $filename);
			$filenames[] = '/medias/forms/' . $filename;
		}

		return $filenames;
	}

	/**
	 * @param  array  $inputs
	 * @return array
	 */
	private function fileInputs(array $inputs): array
	{
		return array_filter($inputs, static function ($value, $name) {
			return str_contains($name, '_file') && $value !== null;
		}, ARRAY_FILTER_USE_BOTH);
	}

	/**
	 * @param  array  $values
	 * @return array
	 */
	private function excludeNull(array $values): array
	{
		return array_filter($values, static function ($value) {
			return $value !== null;
		});
	}

	/**
	 * Controleur pour la liste des événements
	 *
	 * @param $params
	 * @return mixed
	 */
	public function basicEvents($params)
	{
		$events = BasicEvent::where('active', true)->where('start_datetime', '>',
			Carbon::now())->orderBy('start_datetime', 'ASC')->paginate(12);
		$passedEvents = BasicEvent::where('active', true)->where('end_datetime', '<',
			Carbon::now())->orderBy('start_datetime', 'DESC')->paginate(12);
		$params['events'] = $events;
		$params['passedEvents'] = $passedEvents;
		$params['structured_datas'] = $this->createDataFromBasicEvents($events);

		return $params;
	}

	/**
	 * Controleur pour une page évènement
	 *
	 * @param $params
	 * @return array
	 */
	public function basicEvent($params): array
	{

		$id = Route::input('id');
		$event = BasicEvent::whereActive(true)->where('id', $id)->first();
		if ($event === null) {
			App::abort(404);
		}
		/** @var Country|CountryTranslation $country */
		$country = Country::find($event->country_id);
		/** @var State|StateTranslation $state */
		$state = State::find($event->state_id);

		/** @var Builder $req */
		$req = Sharing::where('shareable_id', $id);
		$sharing_count = count($req->get());

		if ($sharing_count > 0) {
			$sharings = $req
				->join('sharing_translations as t', static function ($join) {
					/** @var JoinClause $join */
					$join->on('sharings.id', '=', 't.sharing_id')
						->where('t.locale', '=', App::getLocale())
						->where('sharings.shareable_type', '=', BasicEvent::class);
				})
				->with('translations')
				->get();
		} else {
			$sharings = 0;
		}

		if ((bool) $event->online_register === true && Carbon::parse($event->end_datetime) > Carbon::now()) {
			$placesLeft = $event->available_places;
			foreach ($event->attendees()->whereActive(true)->get() as $attendee) {
				$placesLeft -= $attendee->order()->get()->count();
			}
			if ($placesLeft < 0) {
				$placesLeft = 0;
			}
		} else {
			$placesLeft = null;
		}

		$params = array_merge($params, $event->toArray());
		$params['metadata'] = $this->createMetaFromPage($event);
		$params['blocs'] = Bloc::renderBlocs($event);
		$params['class'] = $event->class;
		$params['country'] = $country->title;
		$params['state'] = $state->title;
		$params['placesLeft'] = $placesLeft;
		$params['query'] = '?year=' . $event->start_datetime->year . '&month=' . $event->start_datetime->month . '#' . $event->start_datetime->format('Y-m-d');
		$params['sharings'] = $sharings;
		return $params;
	}

	/**
	 * Controleur pour la liste des nouvelle
	 *
	 * @param                $params
	 * @param  RouteRequest  $request
	 * @return mixed
	 */
	public function newsList($params, RouteRequest $request)
	{
		$categoryParameter = $request->query('category', null);
		$categories = News::categories();
		$category = null;

		if ($categoryParameter !== null) {
			$category = Category::find($categoryParameter);
		}

		$query = News::active()
			->select('news.*')
			->where('publication_date', '<=', DB::raw('CURDATE()'));

		if ($category) {
			$query->join('associate_news_categories', static function ($join) use ($categoryParameter) {
				/** @var JoinClause $join */
				$join->on('associate_news_categories.mid', '=', 'news.id')
					->where('associate_news_categories.cid', '=', $categoryParameter);
			});
		}

		$news = $query->orderBy('official_date', 'DESC')->paginate(12);

		$structured_datas = $this->createDataFromNewsList($news);

		return array_merge($params, compact('news', 'categories', 'structured_datas'));
	}

	/**
	 * Controleur pour une page nouvelle
	 *
	 * @param $params
	 * @return array
	 */
	public function news($params): array
	{

		$id = Route::input('id');
		$news = News::find($id);

		if (empty($news) || !$news->active) {
			return inaccessible($params);
		}

		$params = array_merge($params, $news->toArray());
		$params['metadata'] = $this->createMetaFromPage($news);
		$params['structured_datas'] = $this->createDataFromNews($news);
		$params['next'] = $news->next();
		$params['previous'] = $news->previous();
		$params['blocs'] = Bloc::renderBlocs($news);
		$params['categories'] = $news->getCategoryNamesAttribute();

		return $params;
	}

	/**
	 * Controleur pour une page gallerie
	 *
	 * @param $params
	 * @return array
	 */
	public function gallery($params): array
	{

		$id = Route::input('id');
		$gallery = Gallery::find($id);
		if (!$gallery->active) {
			App::abort(404);
		}
		$params = array_merge($params, $gallery->toArray());
		$params['metadata'] = $this->createMetaFromPage($gallery);

		return $params;
	}


	/**
	 * @return RedirectResponse
	 */
	public function fontScaled(): RedirectResponse
	{
		if (!Session::has('font-scaled')) {
			Session::put('font-scaled', 1);
		} else {
			Session::forget('font-scaled');
		}
		return Redirect::back();
	}

	/**
	 * @return RedirectResponse
	 */
	public function showInactiveBlocs(): RedirectResponse
	{
		if (!Session::has('show-inactive')) {
			Session::put('show-inactive', 1);
		} else {
			Session::forget('show-inactive');
		}
		return Redirect::back();
	}

	/**
	 * @return string
	 */
	public function sitemap(): string
	{
		return View::make('core.templates.sitemap')->render();
	}

	public function subscriptions($params)
	{
		$page = Page::firstOrNew(['label' => 'subscriptions']);
		if ($page->exists) {
			$params['blocs'] = Bloc::renderBlocs($page);
		} else {
			$page->saveElement([
				'integrated' => true
			], true);
		}
		$subscriber = Auth::guard('subscribers')->user();

		if (!$subscriber) {
			return restricted($params);
		}

		$subs = Subscription::active()->get();

		$ids = $subs->pluck('id');
		$types = $subs->pluck('type');
		$recommended = $subs->pluck('is_recommended');

		$userSubs = $subscriber
			->purchasedSubs()
			->where('active', true)
			->where('on_pause', false)
			->where('end_date', '>=', now())
			->where('start_date', '<=', now())
			->pluck('subscription_id');
		$activeSubs = [];

		foreach ($ids as $id) {
			$activeSubs[] = $userSubs->contains($id);
		}

		$attr = [
			'title',
			'description',
			'subscriptionPrices'
			];

		$rows = [];

		for($i = 0; $i < count($attr); $i++) {
			$rows[$attr[$i]] = $subs->pluck($attr[$i]);
		}

		return array_merge($params, compact('subs', 'rows', 'ids', 'subscriber', 'types', 'recommended', 'activeSubs'));

	}

	public function publicSubscriptions($params)
	{
		$page = Page::firstOrNew(['label' => 'subscriptions']);
		if ($page->exists) {
			$params['blocs'] = Bloc::renderBlocs($page);
		} else {
			$page->saveElement([
				'integrated' => true
			], true);
		}

		$subs = Subscription::active()->get();

		$ids = $subs->pluck('id');
		$types = $subs->pluck('type');
		$recommended = $subs->pluck('is_recommended');

		$attr = [
			'title',
			'description',
			'subscriptionPrices'
		];

		$rows = [];

		for($i = 0; $i < count($attr); $i++) {
			$rows[$attr[$i]] = $subs->pluck($attr[$i]);
		}

		return array_merge($params, compact('subs', 'rows', 'ids', 'types', 'recommended'));
	}

}
