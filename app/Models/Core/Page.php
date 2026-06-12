<?php

namespace App\Models\Core;

use App\Models\Core\Forms\FormGenerator;
use App\Models\Translations\PageTranslation;
use Arr;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Cache;
use Carbon\Carbon;
use DB;
use Eloquent;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Route;
use Schema;
use StringUtility;

/**
 * App\Models\Core\Page
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $banner_image
 * @property int|null $banner_height
 * @property int|null $page_top_spacing
 * @property int|null $footer_top_spacing
 * @property int $has_right_column
 * @property int $is_form_before_pubs
 * @property string|null $custom_code
 * @property string|null $publication_date
 * @property int $restricted
 * @property int|null $integrated
 * @property int $active
 * @property int|null $slideshow_id
 * @property int|null $form_generator_id
 * @property int|null $pub_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @property-read FormGenerator|null $formGenerator
 * @property-read mixed $collection_name
 * @property-read mixed $elements
 * @property-read mixed $label_grid
 * @property-read mixed $nb_blocs_grid
 * @property-read mixed $nb_elements
 * @property-read SearchResult $search_result
 * @property-read string $slug
 * @property-read string $url
 * @property-read mixed $url_grid
 * @property-read PubGroup|null $pubGroup
 * @property-read Sharing|null $sharing
 * @property-read Slideshow|null $slideshow
 * @property-read PageTranslation|null $translation
 * @property-read Collection<int, PageTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $custom_url
 * @method static Builder|Model active()
 * @method static Builder|Page adminMode()
 * @method static Builder|Page listsTranslations(string $translationField)
 * @method static Builder|Page newModelQuery()
 * @method static Builder|Page newQuery()
 * @method static Builder|Page notTranslatedIn(?string $locale = null)
 * @method static Builder|Page orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Page orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Page orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Page query()
 * @method static Builder|Page translated()
 * @method static Builder|Page translatedIn(?string $locale = null)
 * @method static Builder|Page whereActive($value)
 * @method static Builder|Page whereBannerHeight($value)
 * @method static Builder|Page whereBannerImage($value)
 * @method static Builder|Page whereCreatedAt($value)
 * @method static Builder|Page whereCustomCode($value)
 * @method static Builder|Page whereFooterTopSpacing($value)
 * @method static Builder|Page whereFormGeneratorId($value)
 * @method static Builder|Page whereHasRightColumn($value)
 * @method static Builder|Page whereId($value)
 * @method static Builder|Page whereIntegrated($value)
 * @method static Builder|Page whereIsFormBeforePubs($value)
 * @method static Builder|Page whereLabel($value)
 * @method static Builder|Page wherePageTopSpacing($value)
 * @method static Builder|Page wherePubGroupId($value)
 * @method static Builder|Page wherePublicationDate($value)
 * @method static Builder|Page whereRestricted($value)
 * @method static Builder|Page whereSlideshowId($value)
 * @method static Builder|Page whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Page whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Page whereUpdatedAt($value)
 * @method static Builder|Page withTranslation()
 * @mixin Eloquent
 */
class Page extends Model implements TranslatableContract
{
	use Translatable;

	public static $customCodePages;

	public bool $isAjaxEnabled = true;

	public $searchFields = ['title', 'blocs'];

	public bool $isCache = true;

	public $page_type;

	protected $fillable = [

		'@ Paramètres de page',
		'label',
		'title',
		'meta_title',
		'meta_description',

		'@ Personnalisation',
		'slideshow_id',
		'banner_image',
		'banner_height',
		'page_top_spacing',
		'footer_top_spacing',

		'@ Colonne de droite',
		'has_right_column',
		'pub_group_id',
		'form_generator_id',
		'is_form_before_pubs',

		'@ Paramètres avancés',
		'custom_code',
		'custom_url',
		'publication_date',
		'restricted',
		'active',

		'integrated',
	];

	protected array $toggleFields = [
		'banner_image' => "form.slideshow_id.value == ''",
		'banner_height' => "form.slideshow_id.value == ''",
		'pub_group_id' => 'form.has_right_column[1].checked',
		'form_generator_id' => 'form.has_right_column[1].checked',
		'is_form_before_pubs' => 'form.has_right_column[1].checked',
	];

	protected array $grid = ['labelGrid', 'title', 'urlGrid', 'nbBlocsGrid', 'active'];

	protected array $niceNames = [
		'banner_image' => 'Image bandeau',
		'has_right_column' => 'Active',
		'bg_color' => 'Couleur de fond de la page',
		'title_zone_bg_color' => 'Couleur de fond du titre',
		'right_column_bg_color' => 'Couleur de fond pour la colonne de droite',
		'pub_group_id' => 'Regroupement de publicités internes',
		'form_generator_id' => 'Formulaire désiré en colone de droite',
		'is_form_before_pubs' => 'Formulaire avant regroupement de publités internes dans la colonne de droite',
		'url' => 'Url',
		'nbElements' => 'Nb. de blocs',
		'custom_code' => 'Code personalisé',
		'custom_url' => 'Url personnalisé',
		'urlGrid' => 'Url',
		'page_top_spacing' => 'Espacement au-dessus de la page',
		'footer_top_spacing' => 'Espacement au-dessus du pied de page',
		'titleGrid' => 'Titre',
		'nbBlocsGrid' => 'Nb. de blocs',
		'slideshow_id' => 'Id diaporama',
		'banner_height' => 'Hauteur du bandeau',
		'integrated' => 'Page intégrée',
		'labelGrid' => 'Titre interne',
		'restricted' => 'Page à accès restreint',
	];

	public $translatedAttributes = ['title', 'meta_title', 'meta_description', 'custom_url'];

	protected array $enum = [
		'custom_code' => [
			'home' => 'Page d\'accueil',
			'reportsList' => 'Communiqués',
			'mediasList' => 'Médias',
			'documentsList' => 'Documents',
			'basic-event-list' => 'Événements',
			'search-results' => 'Page de résultats de recherche',
			'sitemap' => 'Carte du site',
			// 'conventionsList' => 'Conventions collectives'
			// 'galleryList' => 'Liste de galleries',
			// 'contact' => 'Contactez-nous',
		],
	];

	protected array $rules = [
		'label' => 'required',
		'banner_image' => 'image|mimes:jpeg,jpg,png,gif',
		'fr.custom_url' => 'nullable|unique:page_translations,custom_url,{id},page_id,locale,fr',
		'en.custom_url' => 'nullable|unique:page_translations,custom_url,{id},page_id,locale,en',
	];

	protected array $customFields = [
		'integrated' => ['widget' => 'hidden'],
	];

	protected array $readOnlyFields = [];

	public function __construct($attributes = [])
	{
		// Définition des valeurs par défaut
		$this->attributes['publication_date'] = Carbon::now();

		// Constructeur
		parent::__construct($attributes);
	}

	protected function getElementsAttribute()
	{
		return $this->blocs;
	}

	protected function getLabelGridAttribute()
	{
		return $this->label . ($this->integrated ? '<br/><small>Page intégrée</small>' : '');
	}

	protected function getNbBlocsGridAttribute()
	{
		$url = adminRouteName('admin.pages.edit', [$this->id ?? $this->label, 'blocs']);
		return "<a href='$url'>{$this->nbElements}</a>";
	}

	protected function getNbElementsAttribute()
	{
		return $this->blocs->count();
	}

	public function getSearchResultAttribute(): SearchResult
	{
		$result = new SearchResult();
		$result->label = $this->title;
		$result->url = $this->url;

		return $result;
	}

	/**
	 * @return string
	 */
	protected function getSlugAttribute(): string
	{
		return StringUtility::sluggify($this->title);
	}

	protected function getUrlAttribute(): string
	{
		return $this->getUrl();
	}

	protected function getUrlGridAttribute()
	{
		$str = '';

		foreach (getLocales() as $locale) {
			$url = $this->getUrl($locale);
			$str .= '<a href="' . $url . '" target="_blank">' . $url . '</a><br>';
		}

		return $str;
	}

	protected function setBannerHeightAttribute($value)
	{
		$this->attributes['banner_height'] = null_or_empty_string($value) ? 200 : $value;
	}

	protected function setFooterTopSpacingAttribute($value)
	{
		$this->attributes['footer_top_spacing'] = null_or_empty_string($value) ? null : $value;
	}

	protected function setPageTopSpacingAttribute($value)
	{
		$this->attributes['page_top_spacing'] = null_or_empty_string($value) ? null : $value;
	}

	public static function clearCache()
	{
		Cache::forget('page-routes');
		self::getRoutes();
	}

	public function getCacheKey(): string
	{
		return trim($this->url, '/');
	}

	public static function getRoutes($locale = null)
	{

		$routes = Cache::get('page-routes');
		if (empty($routes)) {
			$routes = [];

			try {
				if (Schema::hasTable('pages')) {
					$locales = getLocales();

					foreach (self::get() as $page) {
						foreach ($locales as $l) {
							$custom_url = $page->translate($l)->custom_url ?? '';
							if (!empty($custom_url)) {
								$routes[$l][$custom_url] = $page->id;
							}
						}
					}

					Cache::forever('page-routes', $routes);
				}

			} catch (Exception|Error) {
				echo 'DB connection not working.';
			}
		}

		if (is_null($locale)) {
			$locale = app()->getLocale();
		}

		return Arr::get($routes, $locale, []);
	}

	public static function getListForSearch()
	{
		return static::where('active', true)->get();
	}

	public static function getByLabel($label)
	{
		return static::where('label', $label)->first();
	}

	public static function getPageCustomCode($customCode)
	{
		if (!isset(static::$customCodePages)) { // store them for future use
			static::$customCodePages = static::where('custom_code', '!=', '')->get();
		}
		foreach (static::$customCodePages as $item) {
			if ($item->custom_code == $customCode) {
				return $item;
			}
		}
	}

	public static function getUrlByCustomCode($customCode)
	{
		$page = static::getPageCustomCode($customCode);
		if ($page) {
			return $page->url;
		}
		return ''; // default, return nothing
	}

	public static function getIdByCustomCode($customCode)
	{
		$page = static::getPageCustomCode($customCode);
		if ($page) {
			return $page->id;
		}
		return null; // default, return null
	}

	public function getFieldPlaceholder($field)
	{
		switch ($field) {
			case 'banner_height':
				return 'Automatique';
			case 'page_top_spacing':
				return setting()->default_page_top_spacing . ' (Valeur par défaut dans les paramètres)';
			case 'footer_top_spacing':
				return setting()->default_footer_top_spacing . ' (Valeur par défaut dans les paramètres)';
			case 'bg_color':
			case 'title_zone_bg_color':
			case 'right_column_bg_color':
				return 'Aucune';
		}

		return null;
	}

	/**
	 * @param Builder $query
	 * @return Builder mixed
	 */
	public function scopeAdminMode($query): Builder
	{
		return is_admin()
			? $query
			: $query->where('active', true)->where('publication_date', '<=', DB::raw('NOW()'));
	}

	/**
	 * @return MorphMany|Bloc[]|Bloc
	 */
	public function blocs()
	{
		return $this->morphMany(Bloc::class, 'pageable');
	}

	/**
	 * @return MorphOne|Sharing
	 */
	public function sharing()
	{
		return $this->morphOne(Sharing::class, 'shareable');
	}

	public function getUrl($locale = null): string
	{
		if (!$locale) {
			$locale = app()->getLocale();
		}

		if ($this->integrated) {
			$config = config('routes.front-end');
			$route = Arr::get($config, $this->label, []);
			$uri = Arr::get($route, 'uri', []);

			$params = ($r = Route::getCurrentRoute())
				? $r->parameters()
				: [];

			$keys = array_keys($params);
			$values = array_values($params);

			foreach ($keys as $i => $iValue) {

				$keys[$i] = '/\{' . $iValue . '\??\}/';
			}

			$keys[] = '/\/\{.*\??\}/';
			$values[] = '';

			$uri = preg_replace($keys, $values, $uri);

			$custom_url = Arr::get($uri, $locale, '');
		} else {
			$custom_url = $this->translate($locale)->custom_url ?? '';
		}

		if (empty($custom_url)) {
			$title = $this->translate($locale)->title ?? '';

			if (!$this->id) {
				return '';
			}

			return standardRoute(['id' => $this->id, 'slug' => StringUtility::sluggify($title ?? '')], $locale);
		}

		return urlPath($custom_url, $locale);
	}

	public function deleteCache()
	{
		parent::deleteCache();
		self::clearCache();
		MenuTree::clearCache();
	}

	public static function get($columns = ['*'])
	{
		// Récupère toutes les pages
		$pages = parent::with('blocs')->get($columns);

		// Filtre les pages intégrées déjà sauvegardées
		$integrated = $pages->filter(static function ($page) {
			return $page->integrated;
		})->map(static function ($page) {
			return $page->label;
		})->toArray();

		// Récupère toutes les pages intégrées qui ne sont pas déjà sauvegardées en base de données
		foreach (Arr::except(config('routes.front-end'), $integrated) as $name => $params) {

			// Détermine si la page peut être administrée
			$admin = Arr::get($params, 'admin', false);
			if ($admin && !is_string($admin)) {

				$page = Arr::get($params, 'page', []);

				foreach (getLocales() as $locale) {
					$page[$locale]['custom_url'] = Arr::get($params, 'uri.' . $locale);
				}

				$page['label'] = $name;
				$page['active'] = true;
				$page['integrated'] = true;

				$pages->push(new Page($page));
			}
		}

		return $pages;
	}

	/**
	 * @return BelongsTo|Slideshow
	 */
	public function slideshow()
	{
		return $this->belongsTo(Slideshow::class);
	}

	/**
	 * @return BelongsTo|PubGroup
	 */
	public function pubGroup()
	{
		return $this->belongsTo(PubGroup::class);
	}

	/**
	 * @return BelongsTo|FormGenerator
	 */
	public function formGenerator()
	{
		return $this->belongsTo(FormGenerator::class);
	}
}
