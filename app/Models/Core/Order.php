<?php

namespace App\Models\Core;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Core\Order
 *
 * @property int $id
 * @property string|null $order_datetime
 * @property string|null $sub_total_price
 * @property string|null $discount_amount
 * @property string|null $shipping_price
 * @property string|null $tvq_price
 * @property string|null $tps_price
 * @property string|null $total_price
 * @property string|null $token
 * @property int $is_cart
 * @property int|null $subscriber_id
 * @property int|null $price_cut_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read PriceCut|null $priceCut
 * @property-read Collection<int, Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read Subscriber|null $subscriber
 * @property-read string $url
 * @method static Builder|Model active()
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereDiscountAmount($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereIsCart($value)
 * @method static Builder|Order whereOrderDatetime($value)
 * @method static Builder|Order wherePriceCutId($value)
 * @method static Builder|Order whereShippingPrice($value)
 * @method static Builder|Order whereSubTotalPrice($value)
 * @method static Builder|Order whereSubscriberId($value)
 * @method static Builder|Order whereToken($value)
 * @method static Builder|Order whereTotalPrice($value)
 * @method static Builder|Order whereTpsPrice($value)
 * @method static Builder|Order whereTvqPrice($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Order extends Model
{
	protected bool $bigData = true;
	public string $order_default = 'created_at';
	public string $order_direction = 'desc';

	public static function filterGetRange($query)
	{
		$query->where('is_cart', false);
	}

	public function __construct(array $attributes = [])
	{
		$this->attributes['created_at'] = Carbon::now();
		parent::__construct($attributes);
	}

	protected $fillable = [
		'order_datetime',
		'subscriber_id',
		'price_cut_id',
		'sub_total_price',
		'discount_amount',
		// 'shipping_price',
		'tps_price',
		'tvq_price',
		'total_price',
		'token',
		'is_cart',
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'id',
		'created_at',
		'subscriber.name',
		'total_price',
	];

	protected array $rules = [];

	protected array $enum = [];

	protected array $customFields = [
//		'token'   => [
//			'widget' => 'readonly'
//		],
		'is_cart' => [
			'widget' => 'empty',
		],
	];

	protected array $niceNames = [
		'created_at'  => 'Date/heure',
		'subscriber_id'   => 'Id inscrit',
		'price_cut_id'    => 'Id Rabais',
		'sub_total_price' => 'Sous total',
		'discount_amount' => 'Montant du rabais consenti',
		'shipping_price'  => 'Coûts de transport',
		'tvq_price'       => 'TVQ',
		'tps_price'       => 'TPS',
		'total_price'     => 'Montant total',
		'token'           => 'Token Stripe',
	];

	protected $appends = [
		'name'
	];

	public function getNameAttribute()
	{
		return $this->id;
	}

	public function getTpsPriceAttribute($value)
	{
		return $value ?? 0;
	}

	public function getTvqPriceAttribute($value)
	{
		return $value ?? 0;
	}

	public function setTpsPriceAttribute($value): void
	{
		$this->attributes['tps_price'] = $value ?? 0;
	}

	public function setTvqPriceAttribute($value): void
	{
		$this->attributes['tvq_price'] = $value ?? 0;
	}

    /**
     * @return HasMany|Purchase[]|Purchase
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * @return HasMany|PurchasedSub[]|PurchasedSub
     */
    public function purchasedSubs(): HasMany
    {
        return $this->hasMany(PurchasedSub::class);
    }

	/**
	 * @return BelongsTo|Subscriber
	 */
	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class);
	}

	/**
	 * @return BelongsTo|PriceCut
	 */
	public function priceCut(): BelongsTo
	{
		return $this->belongsTo(PriceCut::class);
	}

	/**
	 * @return Attribute
	 */
	protected function url(): Attribute
	{
		return Attribute::make(
			get: fn($value): string => urlRouteName('order', ['token' => $this->token], true)
		);
	}
}
