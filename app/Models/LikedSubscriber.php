<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\LikedSubscriber
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $subscriber_id
 * @property int|null $liked_subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read Subscriber|null $likedSubscriber
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber query()
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereLikedSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikedSubscriber whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LikedSubscriber extends Model
{
    use HasFactory;

    public string $order_default = 'id';
    public string $order_direction = 'desc';

    protected bool $bigData = true;

    protected $fillable = [
        'active',
	    'created_at',
	    'subscriber_id',
	    'liked_subscriber_id'
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
        'active'
    ];

    protected array $niceNames = [
		'liked_subscriber_id' => 'Fournisseur aimé'
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

	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class);
	}

	public function likedSubscriber(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class);
	}
}
