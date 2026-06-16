<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Session;
use Illuminate\Database\Eloquent\Collection;
use Arr;

/**
 * App\Models\Core\BasicCart
 *
 * @property-read Collection $cart
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|BasicCart newModelQuery()
 * @method static Builder|BasicCart newQuery()
 * @method static Builder|BasicCart query()
 * @mixin Eloquent
 */
class BasicCart extends Model
{

	protected $currentCart;

	protected $coupon;

	public function __construct()
	{
		parent::__construct();
		self::getCart(); // why?
	}

	/**
	 * Fetch the cart.
	 *
	 * @param  Bool  $asArray
	 * @return array|Collection
	 */
	public static function getCart($asArray = false)
	{
		$cart = Session::get('cart', []);

		if ($asArray) {
			return $cart;
		}

		return new Collection($cart);
	}

	/**
	 *    Change and return the cart
	 *
	 * @return Collection
	 */
	protected function getCartAttribute()
	{
		if ($this->currentCart === null) {
			$this->currentCart = self::getCart();
		}
		return $this->currentCart;
	}

	/**
	 * Check if the BasicCart is selected in the .env file
	 *
	 * @return bool
	 */
	public static function isActive()
	{
		return config('cart.cart_type') === 'basic';
	}

	/**
	 * Add an element to the cart
	 *
	 * @param  Model  $item
	 */
	public static function add($item, $amount = 1)
	{
		// Yes, I know pushing multiple times is dumb, but I don't have time to refactor quantity
		for ($i = 0; $i < $amount; $i++) {
			Session::push('cart', $item);
		}
	}

	/**
	 *    Overwrite the cart with a new one
	 *
	 * @param  array  $cart
	 */
	public static function reassign($cart)
	{
		Session::put('cart', $cart);
	}

	/**
	 *    Add the coupon to the cart
	 *
	 * @param  PriceCut  $coupon
	 */
	public static function addCoupon($coupon)
	{
		Session::push('coupon', $coupon);
	}

	/**
	 *    Remove the coupon in the cart
	 */
	public static function removeCoupon()
	{
		Session::forget('coupon');
	}

	/**
	 *    Fetch the item int the cart.
	 *
	 * @param  Int  $id
	 * @param  String  $model
	 * @return Model
	 */
	public function getItem($id, $model)
	{
		return $model::find($id);
	}

	/**
	 *    Check if item is in cart.
	 *
	 * @param  Model|ProductTrait  $item
	 * @return Integer
	 */
	public function countInCart($item)
	{
		if (!$this->isInCart($item)) {
			return 0;
		}

		if ($item instanceof Purchase) {
			return $this->cart->filter(function ($itemInCart) use ($item) {
				return (
					$itemInCart->id === $item->id
					&&
					$itemInCart->product_type === $item->product_type
					&&
					$itemInCart->item_name === $item->item_name
				);
			})->count();
		} else {
			return $this->cart->filter(function ($itemInCart) use ($item) {
				return (
					$itemInCart->id === $item->id
					&&
					$itemInCart->product_type === $item->product_type
				);
			})->count();
		}
	}

	/**
	 *    Check if item is in cart.
	 *
	 * @param  Model|ProductTrait|null  $item
	 * @param  Bool  $onlyOneOfType
	 * @return Bool
	 */
	public function isInCart($item, $onlyOneOfType = false)
	{
		if ($item !== null) {
			$itemInCart = $this->cart->first(function ($itemInCart) use ($item, $onlyOneOfType) {
				if ($onlyOneOfType) {
					return ($itemInCart->product_type === $item->product_type);
				}

				return ($itemInCart->id === $item->id && $itemInCart->product_type === $item->product_type);
			});
		} else {
			return false;
		}
		return $itemInCart !== null;
	}

	/**
	 * Create an Entity for each item in the cart.
	 *
	 * @param  Order  $order
	 * @param  Subscriber  $subscriber
	 */
	public function buyCart($order, $subscriber)
	{
        $cartType = null;

		foreach ($this->cart as $item) {

			switch (true) {
                case $item instanceof Purchase:
                    // Handle registration fee separately - it's not a profile option
                    if ($item->purchase_type === 'Registration') {
                        // Just save the purchase record, no profile updates needed
                        $item->order_id = $order->id;
                        $item->save();
                        $cartType = 'registration';
                    } else {
                        // Handle profile options as before
                        $optionName = 'profile_' . $item->item_name . '_active';
                        $optionDatetime = 'profile_' . $item->item_name . '_activation_datetime';
                        if ($item->item_name !== 'url') {
                            $subscriber->$optionName = true;
                        }
                        else {
                            $subscriber->end_date = now()->addMonths(setting('url_month_duration'));
                        }
                        $subscriber->$optionDatetime = now();
                        $subscriber->save();

                        $item->order_id = $order->id;
                        $item->save();

                        $cartType = 'option';
                    }

                    break;
				case $item instanceof Subscription:
					$subscriber->active = true;
					$subscriber->registration_completed = true;
					$subscriber->is_public = true;
					$subscriber->save();

					PurchasedSub::create([
						'order_id'        => $order->id,
						'record_date'     => now(),
						'subscription_id' => $item->id,
						'state_id'        => $item->state_id ?? null, // zone : NULL = code postal, sinon province
						'subscriber_id'   => $subscriber->id,
						'start_date'      => $item->start_datetime,
						'end_date'        => $item->end_datetime,
						'active'          => true,
					]);
					break;

				case $item instanceof BasicEvent:
					Attendee::create([
						'order_id'       => $order->id,
						'basic_event_id' => $item->id,
						'register_date'  => now(),
						'subscriber_id'  => $subscriber->id,
						'active'         => true,
					]);
					break;
			}
		}

		// TODO would greatly benefit from implementing is_cart logic in the base

		self::empty();
        return $cartType;
	}

	/**
	 *    Empty the cart
	 */
	public static function empty()
	{
		Session::forget('cart');
		Session::forget('coupon');
	}

	/**
	 * Return totals informations
	 *
	 * @return array
	 */
	public function getTotals()
	{
		$return = [
			'sub_total'  => $this->getSubTotal(),
			'discount'   => $this->getDiscount(),
			'discounted' => $this->getDiscounted(),
			'tps'        => $this->getTps(),
			'tvq'        => $this->getTvq(),
		];
		$return += [
			'total' => $return['discounted'] + $return['tps'] + $return['tvq'],
			//			'total' => $return['sub_total'] + $return['tps'] + $return['tvq'],
		];

		return $return;
	}

	/**
	 * Return sub_total of the cart
	 *
	 * @return Double
	 */
	public function getSubTotal()
	{
		$total = 0;
		foreach ($this->cart as $item) {
			$total += $item->cost;
		}
		return $total;
	}

	/**
	 * Return discount amount of the cart
	 *
	 * @return Double
	 */
	public function getDiscount()
	{
		$pricecut = PriceCut::whereActive(true)
			->where('code', self::getCoupon())
			->where(static function ($query){
				/** @var Builder $query */
				$query->where('end_date', '>=', today()->toDateString())
					->orWhereNull('end_date');
			})
			->first();

		return $pricecut ? $pricecut->getDiscountAmount($this->getSubTotal()) : 0;
	}

	/**
	 * Return sub_total of the cart after discount
	 *
	 * @return Double
	 */
	public function getDiscounted()
	{
		return (($this->getSubTotal() - $this->getDiscount()) > 0) ? $this->getSubTotal() - $this->getDiscount() : 0;
	}

	/**
	 * Return tps of the cart
	 *
	 * @return Double
	 */
	public function getTps()
	{
		$discounted = $this->getDiscounted();
		$tps = 0;
		foreach ($this->cart as $item) {
			if ($item instanceof Purchase) {
				$tps += $discounted * (setting('platform_tps') / 100);
			} else {
				$tps += $discounted * ($item->tps_amount / 100);
			}
		}
		return $tps;
	}

	/**
	 * Return tvq of the cart
	 *
	 * @return Double
	 */
	public function getTvq()
	{
		$discounted = $this->getDiscounted();
		$tvq = 0;
		foreach ($this->cart as $item) {
			if ($item instanceof Purchase) {
				$tvq += $discounted * (setting('platform_tvq') / 100);
			} else {
				$tvq += $discounted * ($item->tvq_amount / 100);
			}
		}
		return $tvq;
	}

	/**
	 *    Fetch the coupon in the cart
	 *
	 * @return PriceCut|null
	 */
	public static function getCoupon()
	{
		$coupon = Session::get('coupon');
		return $coupon ? $coupon[0] : null;
	}

	/**
	 * Check if event fits in the subsciption in cart.
	 *
	 * @param  BasicEvent  $event
	 * @return Bool
	 */
	public function compareEventStartDatetimeWithSubscriptionPeriod($event)
	{

		$subscriptionInCart = Arr::first($this->cart, function ($itemInCart) {
			return $itemInCart instanceof Subscription;
		});

		if ($subscriptionInCart === null) {
			return false;
		}

		// TODO: Valider la logique
		// L'événeménement doit commencer APRÈS le début de la souscription
		// EG: pourquoi après
		// ET L'événeménement doit commencer AVANT la fin de la souscription
		return $event->start_datetime >= $subscriptionInCart->start_datetime && $event->start_datetime < $subscriptionInCart->end_datetime;
	}
}
