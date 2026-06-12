<?php

namespace App\Models\Core;

use App\Models\Translations\GalleryTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Carbon\Carbon;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use StringUtility;

/**
 * App\Models\Core\Gallery
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $publication_date
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, GalleryElement> $elements
 * @property-read int|null $elements_count
 * @property-read mixed $collection_name
 * @property-read mixed $date
 * @property-read mixed $image_principal
 * @property-read mixed $nb_elements
 * @property-read SearchResult $search_result
 * @property-read GalleryTranslation|null $translation
 * @property-read Collection<int, GalleryTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static Builder|Gallery listsTranslations(string $translationField)
 * @method static Builder|Gallery newModelQuery()
 * @method static Builder|Gallery newQuery()
 * @method static Builder|Gallery notTranslatedIn(?string $locale = null)
 * @method static Builder|Gallery orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Gallery orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Gallery orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Gallery query()
 * @method static Builder|Gallery translated()
 * @method static Builder|Gallery translatedIn(?string $locale = null)
 * @method static Builder|Gallery whereActive($value)
 * @method static Builder|Gallery whereCreatedAt($value)
 * @method static Builder|Gallery whereId($value)
 * @method static Builder|Gallery whereLabel($value)
 * @method static Builder|Gallery wherePublicationDate($value)
 * @method static Builder|Gallery whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Gallery whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Gallery whereUpdatedAt($value)
 * @method static Builder|Gallery withTranslation()
 * @mixin Eloquent
 */
class Gallery extends Model implements TranslatableContract
{

	use Translatable;

	public function __construct(array $attributes = [])
	{
		$this->attributes['publication_date'] = Carbon::now();
		parent::__construct($attributes);
	}

	public string $order_default = 'label';
	public string $order_direction = 'ASC';

	/**
	 * RECHERCHE
	 */

	public $searchFields = [
		'title',
		'description',
		'elements'
	];

	public static function getListForSearch()
	{
		return static::where('active', true)->orderBy('publication_date', 'desc')->get();
	}

	/**
	 * Propriétés dynamiques
	 */

	protected $appends = array(
		'search_result',
		'imagePrincipal',
		'nbElements',
		'date'
	);

	protected function getDateAttribute()
	{
		return prettyDate($this->publication_date);
	}

	public function getSearchResultAttribute(): SearchResult
	{

		$result = new SearchResult();
		$result->label = $this->date . ' - ' . $this->title;
		$result->url = $this->url;
		return $result;
	}

	protected function getImagePrincipalAttribute()
	{

		$firstImage = null;

		if ($this->nb_elements > 0) {
			$foundedHeadline = false;

			foreach ($this->elements as $item) {
				if ($item->is_headline) {
					$foundedHeadline = true;
					if ($item->type_element === 'img') {
						$firstImage = $item->image;
					}
				}
				if ($firstImage === null && $item->type_element === 'img') {
					$firstImage = $item->image;
				}

				if ($firstImage && $foundedHeadline) {
					break;
				} // founded and is an image
			}
		}

		return $firstImage;
	}

	protected function getNbElementsAttribute()
	{
		return $this->elements->count();
	}

	/**
	 *  MAIN
	 */
	protected array $rules = [
		'label'            => 'required',
		'publication_date' => 'date'
	];

	protected $fillable = [
		'label',
		'title',
		'description',
		'publication_date',
		'active'
	];

	public $translatedAttributes = [
		'title',
		'description'
	];

	protected array $grid = [
		'label',
		'title',
		'nbElements',
		'active'
	];

	protected array $niceNames = [
		'nbElements' => 'Nb. d\'éléments',
		'title_fr'   => 'Titre (fr)',
		'title_en'   => 'Titre (en)',
	];

	protected array $enum = [];

	protected array $customFields = [
		'description' => array(
			'widget'  => 'wysiwyg',
			'options' => array('height' => '150')
		)
	];

	/**
	 * @return HasMany|GalleryElement[]|GalleryElement
	 */
	public function elements(): HasMany
	{
		return $this->hasMany(GalleryElement::class);
	}
}
