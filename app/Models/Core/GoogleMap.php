<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocGoogleMap;
use App\Models\Translations\GoogleMapTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\GoogleMap
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $image
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $position
 * @property int $active
 * @property int|null $google_map_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $content
 * @property-read mixed $lat
 * @property-read mixed $lng
 * @property-read SearchResult $search_result
 * @property-read GoogleMapGroup|null $googleMapGroup
 * @property-read GoogleMapGroup|null $google_map_group
 * @property-read GoogleMapTranslation|null $translation
 * @property-read Collection<int, GoogleMapTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $text
 * @property string|null $url_label
 * @property string|null $url
 * @method static Builder|Model active()
 * @method static Builder|GoogleMap listsTranslations(string $translationField)
 * @method static Builder|GoogleMap newModelQuery()
 * @method static Builder|GoogleMap newQuery()
 * @method static Builder|GoogleMap notTranslatedIn(?string $locale = null)
 * @method static Builder|GoogleMap orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GoogleMap orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GoogleMap orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|GoogleMap query()
 * @method static Builder|GoogleMap translated()
 * @method static Builder|GoogleMap translatedIn(?string $locale = null)
 * @method static Builder|GoogleMap whereActive($value)
 * @method static Builder|GoogleMap whereCreatedAt($value)
 * @method static Builder|GoogleMap whereGoogleMapGroupId($value)
 * @method static Builder|GoogleMap whereId($value)
 * @method static Builder|GoogleMap whereImage($value)
 * @method static Builder|GoogleMap whereLabel($value)
 * @method static Builder|GoogleMap whereLatitude($value)
 * @method static Builder|GoogleMap whereLongitude($value)
 * @method static Builder|GoogleMap wherePosition($value)
 * @method static Builder|GoogleMap whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|GoogleMap whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GoogleMap whereUpdatedAt($value)
 * @method static Builder|GoogleMap withTranslation()
 * @mixin Eloquent
 */
class GoogleMap extends Model implements TranslatableContract
{
	use Translatable;

	protected $fillable = [
		'google_map_group_id',
		'label',
		'title',
		'image',
		'latitude',
		'longitude',
		'text',
		'url_label',
		'url',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'text',
		'url_label',
		'url',
	];

	protected array $niceNames = [
		'label'     => 'Titre interne',
		'title'     => 'Titre',
		'image'     => 'Image',
		'latitude'  => 'Latitude',
		'longitude' => 'Longitude',
		'text'      => 'Texte',
		'url_label' => 'Libellé lien',
		'url'       => 'Lien',
	];

	protected array $grid = ['label', 'title'];

	protected $appends = ['lat', 'lng', 'content'];

	protected $resetCacheOnChange = [
		GoogleMapGroup::class
	];

	protected $resetPages = [
		BlocGoogleMap::class => [
			'id' => 'google_map_group_id',
			'relation' => 'google_map_group_id',
		],
	];

	public array $positionParentFields = ['google_map_group_id'];

	protected function getContentAttribute()
	{
		return '<div class="bloc-google-maps__box-title">' . $this->title . '</div><div class="bloc-google-maps__box-text">' . $this->texte . '</div>' . (empty($this->url) ? '' : '<a class="bloc-google-maps__box-link" href="' . $this->url . '">' . $this->url_label . '</a>');
	}

	protected function getLatAttribute()
	{
		return $this->latitude;
	}

	protected function getLngAttribute()
	{
		return $this->longitude;
	}

	/**
	 * @return BelongsTo|GoogleMapGroup
	 */
	public function google_map_group()
	{
		return $this->belongsTo(GoogleMapGroup::class);
	}

	/**
	 * @return BelongsTo|GoogleMapGroup
	 */
	public function googleMapGroup()
	{
		return $this->belongsTo(GoogleMapGroup::class);
	}
}
