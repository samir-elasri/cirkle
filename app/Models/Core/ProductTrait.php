<?php


namespace App\Models\Core;

/**
 *
 * @property string $product_type
 * @property string $product_name
 * @property string $class
 * @property float $tps_amount
 * @property float $tvq_amount
 */
trait ProductTrait
{
	public function getProductDetails(): array
	{
		return $this['productDetails'] ?? [];
	}


	/**
	 * !TODO : ATTENTION UAX OVERRIDES DES ENFANTS
	 * @return false|string
	 */
	public function getProductTypeAttribute()
	{
		//TODO: use __() instead of plain string
		switch (true) {
			case $this instanceof PriceCut:
				return 'Coupon';
			case $this instanceof BasicEvent:
				return 'Event';
			default:
				$classPath = explode('\\', get_class($this));
				return end($classPath);
		}
	}

	public function getProductNameAttribute()
	{
		return $this->name ?? $this->title ?? $this->label ?? $this->id;
	}

	public function getUnitType($attribute)
	{
		switch ($attribute) {
			case 'duration':
				return __('main.months');
			default:
				return '';
		}
	}
	public function getCount()
	{
        return 1;
	}

	public function getClassAttribute()
	{
		return base64_encode(get_class($this));
	}

	public function getTpsAmountAttribute($value)
	{
		if ($this->applicable_tps) {
			if ($value == 0) {
				return setting()->default_tps;
			}
			return $value;
		}
		return 0;
	}

	public function getTvqAmountAttribute($value)
	{
		if ($this->applicable_tvq) {
			if ($value == 0) {
				return setting()->default_tvq;
			}
			return $value;
		}
		return 0;
	}

    abstract public function getProductDescriptionAttribute();
}
