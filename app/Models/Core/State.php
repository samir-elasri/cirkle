<?php

namespace App\Models\Core;

use App\Models\Core\Translatable;
use App\Models\Translations\StateTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\State
 *
 * @property int $id
 * @property string|null $label
 * @property float|null $gst
 * @property float|null $pst
 * @property int $active
 * @property int|null $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Country|null $country
 * @property-read mixed $collection_name
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read StateTranslation|null $translation
 * @property-read Collection<int, StateTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|State listsTranslations(string $translationField)
 * @method static Builder|State newModelQuery()
 * @method static Builder|State newQuery()
 * @method static Builder|State notTranslatedIn(?string $locale = null)
 * @method static Builder|State orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|State orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|State orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|State query()
 * @method static Builder|State translated()
 * @method static Builder|State translatedIn(?string $locale = null)
 * @method static Builder|State whereActive($value)
 * @method static Builder|State whereCountryId($value)
 * @method static Builder|State whereCreatedAt($value)
 * @method static Builder|State whereGst($value)
 * @method static Builder|State whereId($value)
 * @method static Builder|State whereLabel($value)
 * @method static Builder|State wherePst($value)
 * @method static Builder|State whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|State whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|State whereUpdatedAt($value)
 * @method static Builder|State withTranslation()
 * @property float|null $tvp
 * @property float|null $tvh
 * @method static Builder|State whereTvh($value)
 * @method static Builder|State whereTvp($value)
 * @mixin Eloquent
 */
class State extends Model implements TranslatableContract
{
	use Translatable;

	protected $fillable = [
		'label',
		'title',
		'country_id',
		'gst',
		'tvp',
		'tvh',
		'pst',
		'active',
	];

	public $translatedAttributes = [
		'title',
	];

	protected array $niceNames = [
		'label'      => 'Titre interne',
		'title'      => 'Titre',
		'country_id' => 'Pays',
		'gst'        => 'TPS',
		'tvp'        => 'TVP',
		'tvh'        => 'TVH',
		'pst'        => 'TVQ',
	];


	protected array $grid = [
		'label',
		'title',
		'gst',
		'tvp',
		'tvh',
		'pst',
	];

	public function getNameAttribute()
	{
		return $this->title;
	}

	/**
	 * @return BelongsTo|Country
	 */
	public function country()
	{
		return $this->belongsTo(Country::class);
	}
}
