<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SubscriberImage
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $legend
 * @property string|null $image
 * @property int|null $position
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereLegend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberImage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubscriberImage extends Model
{
    use HasFactory;

    public string $order_default = 'position';
    public string $order_direction = 'asc';

    protected bool $bigData = true;

    protected $fillable = [
        'subscriber_id',
		'image',
		'legend',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
        'image',
        'legend',
    ];

    protected array $niceNames = [
		'image' => 'Photo',
		'legend' => 'Légende',
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
