<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Gallery;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocGalleryTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocGallery
 *
 * @property int $id
 * @property int|null $gallery_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read Gallery|Collection|array $elements
 * @property-read Gallery|null $gallery
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocGalleryTranslation|null $translation
 * @property-read Collection<int, BlocGalleryTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|BlocGallery listsTranslations(string $translationField)
 * @method static Builder|BlocGallery newModelQuery()
 * @method static Builder|BlocGallery newQuery()
 * @method static Builder|BlocGallery notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocGallery orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGallery orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGallery orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocGallery query()
 * @method static Builder|BlocGallery translated()
 * @method static Builder|BlocGallery translatedIn(?string $locale = null)
 * @method static Builder|BlocGallery whereCreatedAt($value)
 * @method static Builder|BlocGallery whereGalleryId($value)
 * @method static Builder|BlocGallery whereId($value)
 * @method static Builder|BlocGallery whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocGallery whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocGallery whereUpdatedAt($value)
 * @method static Builder|BlocGallery withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocGallery extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title'];

	protected $fillable = [

		'@ Paramètres du bloc galerie',
		'label',
		'title',
		'title_color',
		'gallery_id',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	public $translatedAttributes = [
		'title',
	];

	protected array $niceNames = [
		'label'           => 'Titre interne',
		'title'           => 'Titre',
		'gallery_id'      => 'Galerie',
		'half_width_mode' => 'Mode demi-largeur',
	];

	protected $appends = ['elements'];

	/**
	 * @return Attribute
	 */
	protected function elements(): Attribute
	{
		return Attribute::make(
			get: function (): Gallery|Collection|array {
				if ($this->relationLoaded('gallery')) {
					$gallery = $this->gallery;
				} else {
					$gallery = $this->gallery()
						->active()
						->first();
				}

				if ($gallery) {
					if ($gallery->relationLoaded('elements')) {
						return $gallery->elements;
					}

					return $gallery->elements()
						->active()
						->orderBy('position')
						->get();
				}

				return [];
			}
		);
	}

	/**
	 * @return BelongsTo|Gallery
	 */
	public function gallery(): BelongsTo
	{
		return $this->belongsTo(Gallery::class);
	}
}
