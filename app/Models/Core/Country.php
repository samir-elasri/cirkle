<?php

namespace App\Models\Core;

use App\Models\Translations\CountryTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Country
 *
 * @property int $id
 * @property int $shipping
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read CountryTranslation|null $translation
 * @property-read Collection<int, CountryTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|Country listsTranslations(string $translationField)
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country notTranslatedIn(?string $locale = null)
 * @method static Builder|Country orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Country orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Country orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Country query()
 * @method static Builder|Country translated()
 * @method static Builder|Country translatedIn(?string $locale = null)
 * @method static Builder|Country whereActive($value)
 * @method static Builder|Country whereCreatedAt($value)
 * @method static Builder|Country whereId($value)
 * @method static Builder|Country whereShipping($value)
 * @method static Builder|Country whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Country whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Country whereUpdatedAt($value)
 * @method static Builder|Country withTranslation()
 * @mixin Eloquent
 */
class Country extends Model implements TranslatableContract
{

	use Translatable;

	public $appends = [
		'name'
	];
	protected $fillable = [
		'title',
		// 'shipping',
		'active',
	];
	protected array $niceNames = [
		'shipping' => 'Livraison',
		'title'    => 'Pays',
	];
	protected array $grid = ['title', 'active'];
	public $translatedAttributes = [
		'title',
	];

	public function getNameAttribute()
	{
		return $this->title;
	}
}
