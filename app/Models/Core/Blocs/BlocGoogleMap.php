<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\GoogleMapGroup;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocGoogleMapTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use View;

/**
 * App\Models\Core\Blocs\BlocGoogleMap
 *
 * @property int $id
 * @property float|null $zoom
 * @property int|null $height
 * @property int|null $google_map_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read GoogleMapGroup|null $googleMapGroup
 * @property-read BlocGoogleMapTranslation|null $translation
 * @property-read Collection<int, BlocGoogleMapTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $content
 * @method static Builder|Model active()
 * @method static Builder|BlocGoogleMap listsTranslations(string $translationField)
 * @method static Builder|BlocGoogleMap newModelQuery()
 * @method static Builder|BlocGoogleMap newQuery()
 * @method static Builder|BlocGoogleMap notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocGoogleMap orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGoogleMap orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGoogleMap orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocGoogleMap query()
 * @method static Builder|BlocGoogleMap translated()
 * @method static Builder|BlocGoogleMap translatedIn(?string $locale = null)
 * @method static Builder|BlocGoogleMap whereCreatedAt($value)
 * @method static Builder|BlocGoogleMap whereGoogleMapGroupId($value)
 * @method static Builder|BlocGoogleMap whereHeight($value)
 * @method static Builder|BlocGoogleMap whereId($value)
 * @method static Builder|BlocGoogleMap whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocGoogleMap whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGoogleMap whereUpdatedAt($value)
 * @method static Builder|BlocGoogleMap whereZoom($value)
 * @method static Builder|BlocGoogleMap withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocGoogleMap extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title', 'content'];
	protected $fillable = [

		'@ Paramètres du bloc Google maps',
		'label',
		'title',
		'title_color',
		'content',
		'google_map_group_id',
		'zoom',
		'height',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	public $translatedAttributes = ['title', 'content'];

	protected array $customFields = [
		'content' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		]
	];

	protected array $niceNames = [
		'bg_color'            => 'Couleur de fond',
		'height'              => 'Hauteur',
		'zoom'                => 'Zoom',
		'google_map_group_id' => 'Regroupement',
	];

	/**
	 * @return Attribute
	 */
	protected function height(): Attribute
	{
		return Attribute::make(
			set: static fn ($value) => $value === '' ? null : $value
		);
	}

	/**
	 * @return Attribute
	 */
	protected function zoom(): Attribute
	{
		return Attribute::make(
			set: static fn ($value) => $value === '' ? null : $value
		);
	}

	/**
	 * @return BelongsTo|GoogleMapGroup
	 */
	public function googleMapGroup(): BelongsTo
	{
		return $this->belongsTo(GoogleMapGroup::class);
	}

	/**
	 * @param $field
	 * @return string|null
	 */
	public function getFieldPlaceholder($field): ?string
	{
		if ($field === 'height') {
			return '400px';
		}

		return null;
	}
}

View::composer('core.blocs.google-maps', function ($view) {

	/** @var BlocGoogleMap $bloc */
	$group = $view->bloc->googleMapGroup;
	$dots = $group->googleMaps ?? [];

	$mapData = [
		'zoom'      => $view->google_map_zoom,
		'center'    => count($dots) == 1 ? (object) [
			'lat' => $dots[0]->lat, 'lng' => $dots[0]->lng
		] : (object) ['lat' => 45.505172, 'lng' => -73.569329],
		'fitBounds' => count($dots) > 1,
		'dots'      => $dots,
		'disableDefaultUI' => $view->bg_bleed,
	];

	$view->with(compact('mapData'));
});
