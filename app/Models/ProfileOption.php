<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ProfileOption
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $label
 * @property int|null $duration
 * @property string|null $price
 * @property string|null $type
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Translations\ProfileOptionTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\ProfileOptionTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption translated()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOption withTranslation()
 * @mixin \Eloquent
 */
class ProfileOption extends Model
{
	use HasFactory;
	use Translatable;

	public string $order_default = 'id';
	public string $order_direction = 'desc';

	protected bool $bigData = true;

	protected $fillable = [
		'label',
		'duration',
		'price',
		'type',
		'title',
		'description',

	];

	public array $positionParentFields = [];

	protected array $grid = [
		'id',
		'active'
	];

	public array $translatedAttributes = [
		'title',
		'description',
	];

	protected array $niceNames = [
		'duration'    => 'Durée en mois',
		'price'       => 'Prix',
		'type'        => 'Type',
		'title'       => 'Libellé',
		'description' => 'Description',
	];

	protected array $enum = [
		'type' => [
			'license' => 'Permis',
			'promotion' => 'Promotion',
			'job_offer' => 'Offre d’emploi',
			'image' => 'Photo',
			'estimation' => 'Estimation',
			'url' => 'Website URL',
		]
	];

	protected array $customFields = [];

	/**
	 * @return BelongsTo|TODOClass
	 */
	/*public function parent()
	{
		return $this->belongsTo('#TODO');
	}*/

	/**
	 * @return HasMany|ChildClass[]|ChildClass
	 */
	/*public function children()
	{
		return $this->hasMany('#TODO');
	}*/
}
