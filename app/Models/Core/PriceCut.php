<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\PriceCut
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $code
 * @property string|null $discount_type
 * @property float|null $value
 * @property int $use_once
 * @property string|null $end_date
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $price_cut_count
 * @property-read SearchResult $search_result
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @method static Builder|Model active()
 * @method static Builder|PriceCut newModelQuery()
 * @method static Builder|PriceCut newQuery()
 * @method static Builder|PriceCut query()
 * @method static Builder|PriceCut whereActive($value)
 * @method static Builder|PriceCut whereCode($value)
 * @method static Builder|PriceCut whereCreatedAt($value)
 * @method static Builder|PriceCut whereDiscountType($value)
 * @method static Builder|PriceCut whereEndDate($value)
 * @method static Builder|PriceCut whereId($value)
 * @method static Builder|PriceCut whereLabel($value)
 * @method static Builder|PriceCut whereUpdatedAt($value)
 * @method static Builder|PriceCut whereUseOnce($value)
 * @method static Builder|PriceCut whereValue($value)
 * @mixin Eloquent
 */
class PriceCut extends Model
{

	protected $fillable = [
		'label',
		'code',
		'discount_type',
		'value',
		'use_once',
		'end_date',
		'active',
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'label',
		'code',
		'price_cut_count',
		'active',
	];

	protected array $rules = [];

	protected array $enum = [
		'discount_type' => [
			'$' => '$',
			'%' => '%'
		]
	];

	protected $appends = ['price_cut_count'];

	protected array $niceNames = [
		'price_cut_count' => 'Nombre de fois utilisé',
		'code'            => 'Code unique ',
		'discount_type'   => 'Type ',
		'value'           => 'Valeur ',
		'use_once'        => 'Utilisation unique ',
		'end_date'        => 'Date de fin',
	];

	public function getPriceCutCountAttribute()
	{
		return Order::where('price_cut_id', $this->id)->count();
	}

	/**
	 * @return HasMany|Order[]|Order
	 */
	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function getDiscountAmount($amount)
	{
		if ($this->discount_type === '%') {
			return $amount * $this->value / 100;
		}

		return ($amount > $this->value) ? $this->value : $amount;
	}
}
