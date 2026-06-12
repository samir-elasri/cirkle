<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\GoogleMapGroup;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocTextTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use View;

/**
 * App\Models\Core\Blocs\BlocText
 *
 * @property int $id
 * @property string|null $align
 * @property string|null $media_type
 * @property int|null $google_map_zoom
 * @property string|null $relation
 * @property string|null $bg_color
 * @property int|null $top_spacing
 * @property int|null $width
 * @property int|null $height
 * @property int $half_width_mode
 * @property int $accordion
 * @property int $call_to_action_present
 * @property int|null $google_map_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string|null $bg_type
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read GoogleMapGroup|null $googleMapGroup
 * @property-read BlocTextTranslation|null $translation
 * @property-read Collection<int, BlocTextTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $content
 * @property string|null $summary
 * @property string|null $image
 * @property string|null $back_image
 * @property string|null $video_url
 * @property string|null $video_filename
 * @property string|null $alt
 * @property string|null $legend
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @method static Builder|Model active()
 * @method static Builder|BlocText listsTranslations(string $translationField)
 * @method static Builder|BlocText newModelQuery()
 * @method static Builder|BlocText newQuery()
 * @method static Builder|BlocText notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocText orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocText orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocText orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocText query()
 * @method static Builder|BlocText translated()
 * @method static Builder|BlocText translatedIn(?string $locale = null)
 * @method static Builder|BlocText whereAccordion($value)
 * @method static Builder|BlocText whereAlign($value)
 * @method static Builder|BlocText whereBgColor($value)
 * @method static Builder|BlocText whereCallToActionPresent($value)
 * @method static Builder|BlocText whereCreatedAt($value)
 * @method static Builder|BlocText whereGoogleMapGroupId($value)
 * @method static Builder|BlocText whereGoogleMapZoom($value)
 * @method static Builder|BlocText whereHalfWidthMode($value)
 * @method static Builder|BlocText whereHeight($value)
 * @method static Builder|BlocText whereId($value)
 * @method static Builder|BlocText whereMediaType($value)
 * @method static Builder|BlocText whereRelation($value)
 * @method static Builder|BlocText whereTopSpacing($value)
 * @method static Builder|BlocText whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocText whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocText whereUpdatedAt($value)
 * @method static Builder|BlocText whereWidth($value)
 * @method static Builder|BlocText withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @mixin Eloquent
 */
class BlocText extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title', 'content', 'summary', 'alt', 'legend'];

	protected $fillable = [

		'@ Paramètres du bloc texte',
		'label',
		'title',
		'title_color',
		'content',
		'accordion',
		'summary',

		'@ Élément d\'accompagnement multimédia',
		'media_type',
		'image',
		'google_map_group_id',
		'google_map_zoom',
		'video_url',
		'video_filename',
		'align',
		'relation',
		'width',
		'height',
		'alt',
		'legend',

		'@ Appel à l\'action',
		'call_to_action_present',
		'call_to_action_label',
		'call_to_action_url',

		'@Image en arrière-plan',
		'back_image',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	protected array $toggleFields = [
		'summary' => '!form.accordion[1].checked',
		'google_map_group_id' => "form.media_type.value == 'map'",
		'google_map_zoom' => "form.media_type.value == 'map'",
		'height' => "form.media_type.value != ''",
		'image' => "form.media_type.value == 'image' || form.media_type.value == 'video'",
		'alt' => "form.media_type.value == 'image'",
		'video_url' => "form.media_type.value == 'vimeo' || form.media_type.value == 'youtube'",
		'video_filename' => "form.media_type.value == 'video'",
		'align' => "form.media_type.value != ''",
		'legend' => "form.media_type.value != ''",
		'call_to_action_label' => 'form.call_to_action_present[1].checked',
		'call_to_action_url' => 'form.call_to_action_present[1].checked',
	];

	public $translatedAttributes = [
		'title',
		'content',
		'summary',
		'alt',
		'legend',
		'video_url',
		'video_filename',
		'call_to_action_label',
		'call_to_action_url',
		'image',
		'back_image',
	];

	protected array $niceNames = [
		'width' => 'Largeur',
		'height' => 'Hauteur',
		'accordion' => 'Accordéon',
		'video_url' => 'Url vidéo',
		'video_filename' => 'Vidéo',
		'align' => 'Position',
		'media_type' => 'Type',
		'google_map_group_id' => 'Regroupement',
		'google_map_zoom' => 'Zoom',
		'relation' => 'Relation avec le texte',
		'call_to_action_present' => 'Présent',
		'call_to_action_label' => 'Libellé',
		'call_to_action_url' => 'URL',
		'back_image_present' => 'Présent',
		'back_image' => 'Image',
	];

	protected array $customFields = [
		'content' => [
			'widget' => 'wysiwyg',
			'options' => ['height' => 150]
		]
	];

	protected array $enum = [
		'media_type' => [
			'image' => 'Image',
			'video' => 'Vidéo',
			'vimeo' => 'Vidéo Vimeo',
			'youtube' => 'Vidéo Youtube',
			'map' => 'Google Map',
		],
		'align' => [
			'top' => 'Au-dessus',
			'left' => 'Gauche',
			'right' => 'Droite',
			'bottom' => 'En-dessous',
		],
		'relation' => [
			'around' => 'Coule autour',
			'straight' => 'Droite'
		]
	];

	/**
	 * @return Attribute
	 */
	protected function bgType(): Attribute
	{
		return Attribute::make(
			get: function (): ?string {
				if (!empty($this->back_image)) {
					// La valeur retourné doit être différente, afin d'avoir le bon espacement entre les blocs demi-largeur
					return $this->back_image . '-' . random_int(0, PHP_INT_MAX);
				}

				return empty($this->bg_color) ? null : "color-{$this->bg_color}";
			}
		);
	}

	/**
	 * @return Attribute
	 */
	protected function googleMapZoom(): Attribute
	{
		return Attribute::make(
			set: static fn($value) => $value === '' ? null : $value
		);
	}

	/**
	 * @return Attribute
	 */
	protected function height(): Attribute
	{
		return Attribute::make(
			set: static fn($value) => $value === '' ? null : $value
		);
	}

	/**
	 * @return Attribute
	 */
	protected function width(): Attribute
	{
		return Attribute::make(
			set: static fn($value) => $value === '' ? null : $value
		);
	}

	public function getFieldPlaceholder($field)
	{
		return match ($field) {
			'google_map_zoom' => '16',
			'width' => '100%',
			'height' => '400px',
			default => parent::getFieldPlaceholder($field),
		};
	}

	/**
	 * @return BelongsTo|GoogleMapGroup
	 */
	public function googleMapGroup(): BelongsTo
	{
		return $this->belongsTo(GoogleMapGroup::class);
	}
}

View::composer('core.blocs.text', function ($view) {

	if ($view->google_map_group_id && $view->media_type == 'map') {

		$group = GoogleMapGroup::find($view->google_map_group_id);
		$dots = $group ? $group->googleMaps()->get() : [];

		$mapData = [
			'zoom' => $view->google_map_zoom,
			'center' => count($dots) == 1 ? (object)[
				'lat' => $dots[0]->lat, 'lng' => $dots[0]->lng
			] : (object)['lat' => 45.505172, 'lng' => -73.569329],
			'fitBounds' => count($dots) > 1,
			'disableDefaultUI' => true,
			'dots' => $dots
		];

		$view->with(compact('mapData'));
	}
});
