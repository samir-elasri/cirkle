<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Purchase
 *
 * @property int $id
 * @property string|null $purchase_type
 * @property string|null $item_name
 * @property int|null $quantity
 * @property string $unit_price
 * @property string $total_price
 * @property int|null $order_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Order|null $order
 * @method static Builder|Model active()
 * @method static Builder|Purchase newModelQuery()
 * @method static Builder|Purchase newQuery()
 * @method static Builder|Purchase query()
 * @method static Builder|Purchase whereCreatedAt($value)
 * @method static Builder|Purchase whereId($value)
 * @method static Builder|Purchase whereItemName($value)
 * @method static Builder|Purchase whereOrderId($value)
 * @method static Builder|Purchase wherePurchaseType($value)
 * @method static Builder|Purchase whereQuantity($value)
 * @method static Builder|Purchase whereTotalPrice($value)
 * @method static Builder|Purchase whereUnitPrice($value)
 * @method static Builder|Purchase whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Purchase extends Model
{
	use ProductTrait;

	protected $fillable = [
		'order_id',
		'purchase_type',
		'item_name',
		'quantity',
		'unit_price',
		'total_price',
	];

	protected array $toggleFields = [
	];

	protected array $grid = [
		'purchase_type',
		'item_name',
        'title',
		'unit_price',
		'total_price',
	];

	protected array $rules = [
	];

	protected array $enum = [
	];

	protected array $niceNames = [
		'purchase_type' => 'Type d’achat',
		'item_name'     => 'Nom de l’item',
		'quantity'      => 'Quantité',
		'unit_price'    => 'Coût unitaire',
		'total_price'   => 'Coût total',
	];


	/**
	 * @return BelongsTo|Order
	 */
	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function getCostAttribute(){
		return $this->unit_price;
	}

	public function getTitleAttribute(){
		// Option photos du bloc PROMOTION (Denis 07.07) : pas de reglage dedie.
		if ($this->item_name === 'promotion_photos_A') {
			return 'PROMOTION - OPTION A (3 PHOTOS)';
		}
		if ($this->item_name === 'promotion_photos_B') {
			return 'PROMOTION - OPTION B (6 PHOTOS)';
		}
        $key = "{$this->item_name}_title";
		return setting($key) ?? $key;
	}

    public function getProductDescriptionAttribute() {
        return setting("{$this->item_name}_description", "{$this->item_name}_description");
    }
}
