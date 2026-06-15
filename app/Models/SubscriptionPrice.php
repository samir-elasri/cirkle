<?php

namespace App\Models;

use App\Models\Core\Subscription;
use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SubscriptionPrice
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $subscription_id
 * @property int|null $position
 * @property string|null $cost
 * @property int|null $month_duration
 * @property int|null $year_duration
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read Subscription $subscription
 * @property-read \App\Models\Translations\SubscriptionPriceTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\SubscriptionPriceTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $text
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice translated()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereMonthDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice whereYearDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPrice withTranslation()
 * @mixin \Eloquent
 */
class SubscriptionPrice extends Model
{
    use HasFactory;
    use Translatable;

    // public string $order_default = 'position';
    // public string $order_direction = 'asc';

    protected bool $bigData = true;

    protected $fillable = [
        'service_category_id',
		'state_id',
		'cost',
		'subscription_id',
		// 'month_duration',
		// 'year_duration',
		// 'text',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
        'service_category_id',
        'serviceCategory.label',
	    'cost',
	    // 'month_duration',
	    // 'year_duration',
    ];

	public array $translatedAttributes = [
		'text',
	];

    protected array $niceNames = [
        'service_category_id' => 'Catégorie de service',
	    'cost' => 'Prix',
	    'month_duration' => 'Nombre de mois',
	    'year_duration' => 'Nombre d\'années',
	    'text' => 'Texte',
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

	public function subscription(): BelongsTo
	{
		return $this->belongsTo(Subscription::class);
	}

	public function serviceCategory(): BelongsTo
	{
		return $this->belongsTo(ServiceCategory::class);
	}

	/**
	 * Province visée par ce forfait (NULL = forfait par code postal, le défaut).
	 */
	public function state(): BelongsTo
	{
		return $this->belongsTo(\App\Models\Core\State::class);
	}
}
