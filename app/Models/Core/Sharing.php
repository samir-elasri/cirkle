<?php

namespace App\Models\Core;

use App\Models\Translations\SharingTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Sharing
 *
 * @property int $id
 * @property int|null $shareable_id
 * @property string $shareable_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $shareable
 * @property-read SharingTranslation|null $translation
 * @property-read Collection<int, SharingTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $fb_title
 * @property string|null $fb_description
 * @property string|null $fb_image
 * @property string|null $tw_title
 * @property string|null $tw_description
 * @property string|null $tw_image
 * @method static Builder|Model active()
 * @method static Builder|Sharing listsTranslations(string $translationField)
 * @method static Builder|Sharing newModelQuery()
 * @method static Builder|Sharing newQuery()
 * @method static Builder|Sharing notTranslatedIn(?string $locale = null)
 * @method static Builder|Sharing orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Sharing orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Sharing orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Sharing query()
 * @method static Builder|Sharing translated()
 * @method static Builder|Sharing translatedIn(?string $locale = null)
 * @method static Builder|Sharing whereCreatedAt($value)
 * @method static Builder|Sharing whereId($value)
 * @method static Builder|Sharing whereShareableId($value)
 * @method static Builder|Sharing whereShareableType($value)
 * @method static Builder|Sharing whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Sharing whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Sharing whereUpdatedAt($value)
 * @method static Builder|Sharing withTranslation()
 * @mixin Eloquent
 */
class Sharing extends Model implements TranslatableContract
{
	use Translatable;

	public string $singular = 'un partage';
	public $relatedGrid = 'partages pour un élément';

	protected $fillable = [
		'shareable_id',
		'shareable_type',
		'@ Facebook',
		'fb_title',
		'fb_description',
		'fb_image',
		'@ Twitter',
		'tw_title',
		'tw_description',
		'tw_image',
	];

	public $translatedAttributes = [
		'fb_title',
		'fb_description',
		'fb_image',
		'tw_title',
		'tw_description',
		'tw_image',
	];

	protected array $niceNames = [
		'fb_title' => 'Titre',
		'fb_description' => 'Description',
		'fb_image' => 'Image',
		'tw_title' => 'Titre',
		'tw_description' => 'Description',
		'tw_image' => 'Image',
	];

	protected array $customFields = [
		'shareable_id' => ['widget' => 'hidden_morph_one'],
		'shareable_type' => ['widget' => 'empty']
	];

	protected array $grid = ['fb_title', 'tw_title'];

	/**
	 * @return MorphTo
	 */
	public function shareable()
	{
		return $this->morphTo();
	}
}
