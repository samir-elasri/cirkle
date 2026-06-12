<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SubscriberServiceCategory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $subscriber_id
 * @property int|null $service_category_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\ServiceCategory|null $serviceCategory
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereServiceCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubscriberServiceCategory extends Model
{
    use HasFactory;

    public string $order_default = 'id';
    public string $order_direction = 'desc';

    protected bool $bigData = true;

    protected $fillable = [
        'subscriber_id',
        'service_category_id',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
	    'serviceCategory.title',
    ];

    protected array $niceNames = [
		'service_category_id' => 'Sous catégorie'
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
     * @return BelongsTo|ServiceCategory
     */
    public function serviceCategory()
    {
    	return $this->belongsTo(ServiceCategory::class);
    }

    /**
     * @return HasMany|ChildClass[]|ChildClass
     */
    /*public function children()
    {
    	return $this->hasMany('#TODO');
    }*/
}
