<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SubscriberService
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $service_id
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Service|null $service
 * @property-read Subscriber|null $subscriber
 * @property-read \App\Models\Translations\SubscriberServiceTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\SubscriberServiceTranslation> $translations
 * @property-read int|null $translations_count
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService translated()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberService withTranslation()
 * @mixin \Eloquent
 */
class SubscriberService extends Model
{
    use HasFactory;
    use Translatable;

    public string $order_default = 'id';
    public string $order_direction = 'asc';

    protected bool $bigData = true;

    protected $fillable = [
        'subscriber_id',
        'service_id',
        'custom_value',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
        'service.title',
    ];

	public array $translatedAttributes = [];

    protected array $niceNames = [
		'service_id' => 'Service',
        'custom_value' => 'Valeur custom',
    ];

    protected array $enum = [];

    protected array $customFields = [];

    /**
     * @return BelongsTo|Subscriber
     */
    public function subscriber()
    {
    	return $this->belongsTo(Subscriber::class);
    }

    /**
     * @return BelongsTo|Service
     */
    public function service()
    {
    	return $this->belongsTo(Service::class);
    }

    /**
     * @return HasMany|ChildClass[]|ChildClass
     */
    /*public function children()
    {
    	return $this->hasMany('#TODO');
    }*/
}
