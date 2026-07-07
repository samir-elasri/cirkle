<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\License
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $position
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Translations\LicenseTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\LicenseTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|License listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|License orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|License orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|License orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License translated()
 * @method static \Illuminate\Database\Eloquent\Builder|License translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|License whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License withTranslation()
 * @mixin \Eloquent
 */
class License extends Model
{
	use HasFactory;
	use Translatable;

	public string $order_default = 'position';
	public string $order_direction = 'asc';

	protected bool $bigData = true;

	protected $fillable = [
		'title',
		'description',
		'subscriber_id',
		// Tablo de Denis (07.07.26) : emetteur, no d'inscription, debut, fin (AAAA/MM)
		'issuer',
		'registration_number',
		'start_date',
		'expiry_date',
	];

	public bool $isAjaxEnabled = true;

	public array $positionParentFields = ['subscriber_id'];

	protected array $grid = [
		'id',
		'active'
	];

	public array $translatedAttributes = [
		'title',
		'description',
	];

	protected array $niceNames = [
		'title' => 'Nom du permis',
		'description' => 'Description',
	];

	protected array $enum = [];

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
