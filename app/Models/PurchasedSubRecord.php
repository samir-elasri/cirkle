<?php

namespace App\Models;

use App\Models\Core\Order;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PurchasedSubRecord
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $on_pause
 * @property int|null $subscription_id
 * @property int|null $order_id
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read Order|null $order
 * @property-read Subscriber|null $subscriber
 * @property-read Subscription|null $subscription
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereOnPause($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchasedSubRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PurchasedSubRecord extends Model
{
	use HasFactory;

	public string $order_default = 'id';
	public string $order_direction = 'desc';

	protected bool $bigData = true;

	protected $fillable = [
		'start_date',
		'subscriber_id',
		'end_date',
		'on_pause',
		'subscription_id',
		'order_id',
		'active',
	];

	public array $positionParentFields = [];

	protected array $grid = [
		'id',
		'subscription_id',
		'active',
	];

	protected array $niceNames = [
		'created_at' => 'Date de création de l’enregistrement',
		'subscription_id' => 'Forfait',
		'subscriber_id' => 'Inscrit',
		'order_id' => 'Commande associé',
		'start_date' => 'Date de début',
		'end_date' => 'Date de fin',
		'on_pause' => 'En pause O/N',
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
	public function subscription(): BelongsTo
	{
		return $this->belongsTo(Subscription::class);
	}
	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class);
	}
}
