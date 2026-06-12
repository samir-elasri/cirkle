<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Attendee
 *
 * @property int $id
 * @property string|null $register_date
 * @property int|null $order_id
 * @property int $active
 * @property int|null $basic_event_id
 * @property int|null $subscriber_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BasicEvent|null $basicEvent
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Order|null $order
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static Builder|Attendee newModelQuery()
 * @method static Builder|Attendee newQuery()
 * @method static Builder|Attendee query()
 * @method static Builder|Attendee whereActive($value)
 * @method static Builder|Attendee whereBasicEventId($value)
 * @method static Builder|Attendee whereCreatedAt($value)
 * @method static Builder|Attendee whereId($value)
 * @method static Builder|Attendee whereOrderId($value)
 * @method static Builder|Attendee whereRegisterDate($value)
 * @method static Builder|Attendee whereSubscriberId($value)
 * @method static Builder|Attendee whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Attendee extends Model
{
	protected bool $bigData = true;

	protected $fillable = [
		'basic_event_id',
		'subscriber_id',
		'register_date',
		'order_id',
		'active',
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'register_date',
		'subscriber.name',
	];

	protected array $rules = [];

	protected array $niceNames = [
		'register_date'   => 'Date inscription',
		'subscriber_grid' => 'Id Inscrit',
		'subscriber.name' => 'Id Inscrit',
		'subscriber_id'   => 'Id Inscrit',
		'order_id'        => 'Id commande',
		'active'          => 'Actif',
	];

	/**
	 * @return BelongsTo|Subscriber
	 */
	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class);
	}

	/**
	 * @return BelongsTo|BasicEvent
	 */
	public function basicEvent(): BelongsTo
	{
		return $this->belongsTo(BasicEvent::class);
	}

	/**
	 * @return BelongsTo|Order
	 */
	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class);
	}
}
