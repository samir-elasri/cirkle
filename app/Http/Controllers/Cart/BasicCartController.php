<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Core\Attendee;
use App\Models\Core\BasicCart;
use App\Models\Core\Model;
use App\Models\Core\Order;
use App\Models\Core\Purchase;
use App\Models\Core\PurchasedSub;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use Arr;
use Auth;
use Cart;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use JsonException;
use Log;
use Redirect;
use Route;
use Session;
use StringUtility;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class BasicCartController extends Controller
{
	/**
	 * Controleur pour afficher la liste des éléments du cart
	 *
	 * @param $params
	 * @return array
	 * @throws ApiErrorException
	 * @throws JsonException
	 */
	public function cart($params)
	{
		/** @var Subscriber $subscriber */
		$subscriber = Auth::guard('subscribers')->user();
		$items = Cart::getCart();
		$coupon = Cart::getCoupon();
		$totals = Cart::getTotals();

		$sessionId = $this->createStripeCheckoutSession($subscriber);

		$stripeData = json_encode([
			'publishableKey' => env('STRIPE_PUBLIC_KEY'),
			'sessionId'      => $sessionId
		], JSON_THROW_ON_ERROR);


		return array_merge($params, compact(
			'items',
			'subscriber',
			'coupon',
			'totals',
			'stripeData'
		));
	}

	/**
	 *
	 * @param Subscriber $subscriber
	 * @return string
	 * @throws ApiErrorException
	 * @throws Exception
	 */
	private function createStripeCheckoutSession($subscriber)
	{
		$stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
		$setting = setting();

		$tps = $setting->default_tps / 100;
		$tvq = $setting->default_tvq / 100;

		$cart = $this->createCartOrder($subscriber);

		// Crate line Items
		$items = BasicCart::getCart()->map(function ($item) use ($tps, $tvq) {
			$amount = $item->cost
				+ $item->cost * ($item->applicable_tps ? $tps : 0)
				+ $item->cost * ($item->applicable_tvq ? $tvq : 0);

			$result = [
				'price_data' => [
					'currency'     => 'cad',
					'unit_amount'  => (int)($amount * 100),
					'product_data' => [
						'name'        => $item->product_name,
					],
				],
				'quantity' => 1,
			];
			if ($item->product_description) {
				$result['price_data']['product_data']['description'] = $item->product_description;
			}
			return $result;
		})->toArray();

		$sessionData = [
			'payment_method_types' => ['card'],
			'mode'                 => 'payment',
			'success_url'          => urlRouteName('cart.stripe.validate-checkout-session', [
					'token' => $cart->token
				], true) . '/{CHECKOUT_SESSION_ID}',
			'cancel_url'           => urlRouteName('cart', [], true),
			'customer_email'       => $subscriber->email,
			'line_items'           => $items,
		];

		return Cart::getTotals()['total'] > 0 ? $stripe->checkout->sessions->create($sessionData)->id : null;
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws ApiErrorException
	 */
	public function validateStripeCheckoutSession(Request $request)
	{
		$stripe = new StripeClient(env('STRIPE_SECRET_KEY'));
		$sessionID = $request->session_id;
		$token = $request->token;

		$session = $stripe->checkout->sessions->retrieve($sessionID);

		/** @var Subscriber $subscriber */
		$subscriber = Auth::guard('subscribers')->user();

		switch ($session->payment_status) {
			case 'paid':
			case 'no_payment_required':

				/** @var Order $order */
				$order = Order::where('token', $token)->first();

				if ($order) {
					// Turning off cart is done inside buyCart
					$cartType = Cart::buyCart($order, $subscriber);

					$order->is_cart = false;
                    $order->created_at = now();
					$order->save();

					$subscriber->sendMail('confirm-purchase', [
						'total' => $order->total_price,
						'url'   => $order->url
					]);

                    if ($cartType === 'option') {
                        return Redirect::to(urlRouteName('profile') . '?tab=supplier&subTab=profile-options')
                            ->with('success', __('cart.bought'));
                    }

				} else {
					Log::error("Order $token could not be found for user {$subscriber->email}");
				}

				return Redirect::to(urlRouteName('purchase-confirmation'))
					->with('success', __('cart.bought'));

			default:
			case 'unpaid':
				return Redirect::to(urlRouteName('purchase-confirmation'))
					->with('error', __('cart.unpaid'));
		}
	}

	/**
	 * Controleur pour afficher le résultat de la transaction après le retour de l'API d'achat
	 *
	 * @param $params
	 * @return array
	 */
	public function purchaseConfirmation($params)
	{
		/** @var Subscriber $subscriber */
		$subscriber = Auth::guard('subscribers')->user();

		return array_merge($params, compact('subscriber'));
	}

	/**
	 * Show information about the order with the token
	 *
	 * @param array $params
	 * @return array
	 */
	public function order($params)
	{
		$token = Route::input('token');
		$order = Order::where('token', $token)->first();
		if ($order !== null) {
			$items = [];
			$attendees = Attendee::where('order_id', $order->id)->get();
			$purchasedSubs = PurchasedSub::where('order_id', $order->id)->get();
            $purchases = $order->purchases;

			foreach ($attendees as $attendee) {
				$items[] = $attendee->basic_event;
			}
			foreach ($purchasedSubs as $purchasedSub) {
				$items[] = $purchasedSub->subscription;
			}
            foreach ($purchases as $purchase) {
                $items[] = $purchase;
            }

			return array_merge($params, compact('order', 'items'));
		}

		return $params;
	}

	/**
	 * Store a newly created cart in Session.
	 *
	 * @param Request $request
	 * @return RedirectResponse|\Illuminate\Http\Response
	 */
	public function store(Request $request)
	{

		$data = $request->all();

		if ($items = Arr::get($data, 'items')) { // options
			$itemArray = explode(',',$items);

			foreach ($itemArray as $cartItem) {
				$purchase = new Purchase;
				$price = setting("{$cartItem}_price") ?? 0;

				$purchase->fill([
					'purchase_type' => 'Option profil',
					'item_name'     => $cartItem,
					'quantity'      => 1,
					'unit_price'    => $price,
					'total_price'   => $price,
				]);

				if (Cart::isInCart($purchase, true)) {
					return Redirect::to(urlRouteName('cart'))->with('error', __('cart.item.already_in'));
				}

				Cart::add($purchase);
			}
		} else {

			$item = Cart::getItem($request->product_id, $request->product_type);
			if ($item instanceof Subscription) {
				if (Cart::isInCart($item, true)) {
					return Redirect::to(urlRouteName('cart'))->with('error', __('cart.item.already_in'));
				}
			} elseif (Cart::isInCart($item)) {
				return Redirect::to(urlRouteName('cart'))->with('error', __('cart.item.already_in'));
			}


			Cart::add($item);
		}

		return Redirect::to(urlRouteName('cart'))->with('success', __('cart.item.added'));
	}

	/**
	 * Remove the specified element from Session.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @noinspection PhpUndefinedFieldInspection
	 * @noinspection TypeUnsafeComparisonInspection
	 */
	public function destroy(Request $request)
	{
		$item = Cart::getItem($request->product_id, $request->product_type);
		if (!Cart::isInCart($item)) {
			return back()->with('error', __('cart.item.not_in'));
		}

		$cartKeys = [];
		foreach (Cart::getCart() as $key => $itemInCart) {
			//Two instances of the same class with the same id
			if ($itemInCart->id == $item->id && $itemInCart->product_type == $item->product_type) {
				$cartKeys[] = $key;
			}
		}

		$cartItems = Cart::getCart(true);
		foreach ($cartKeys as $cartKey) {
			unset($cartItems[$cartKey]);
		}
		Cart::reassign($cartItems);
		return back()->with('success', __('cart.item.removed'));
	}

	/**
	 * Remove all element from cart.
	 *
	 * @return RedirectResponse
	 */
	public function empty()
	{
		BasicCart::empty();
		return back()->with('success', __('cart.emptied'));
	}


	//	/**
	//	 * Add coupon to cart.
	//	 *
	//	 * @return \Illuminate\Http\Response
	//	 */
	//	public function addCoupon(Request $request)
	//	{
	//		$cart = new BasicCart;
	//		$pricecut = PriceCut::whereActive(true)->where('code', $request->coupon)->first();
	//
	//		if ($pricecut === null) {
	//			return back()->with('error', __('cart.coupon.invalid'));
	//		}
	//		if ($cart->getCoupon() !== null) {
	//			return back()->with('error', __('cart.coupon.already_in'));
	//		}
	//
	//		$cart->addCoupon($request->coupon);
	//		return back()->with('success', __('cart.coupon.added'));
	//	}
	//
	//
	//	/**
	//	 * Remove coupon from cart.
	//	 *
	//	 * @return \Illuminate\Http\Response
	//	 */
	//	public function removeCoupon(Request $request)
	//	{
	//		BasicCart::removeCoupon();
	//
	//		$pricecut = PriceCut::whereActive(true)->where('code', $request->coupon)->first();
	//
	//		if ($pricecut === null) {
	//			return back()->with('error', __('cart.coupon.invalid'));
	//		}
	//		return back()->with('success', __('cart.coupon.removed'));
	//	}

	//	/**
	//	 * Buy all.
	//	 *
	//	 * @param  Request  $request
	//	 * @return JsonResponse|\Illuminate\Http\Response
	//	 */
	//	public function buy(Request $request)
	//	{
	//		$subscriber = Auth::guard('subscribers')->user();
	//		$totals = Cart::getTotals();
	//		$pricecut = PriceCut::whereActive(true)->where('code', Cart::getCoupon())->first();
	//		$order = Order::create([
	//			'order_datetime'  => now(),
	//			'subscriber_id'   => $subscriber->id,
	//			'price_cut_id'    => $pricecut ? $pricecut->id : null,
	//			'sub_total_price' => $totals['sub_total'],
	//			'discount_amount' => $totals['discount'],
	//			'tps_price'       => $totals['tps'],
	//			'tvq_price'       => $totals['tvq'],
	//			'total_price'     => $totals['total'],
	//			'token'           => $request->orderID,
	//		]);
	//		Cart::buyCart($order, $subscriber);
	//		Session::flash('success', __('cart.bought'));
	//		$response['success'] = true;
	//
	//		return Response::json($response);
	//	}

	/**
	 * Buy all free cart.
	 *
	 * @return RedirectResponse
	 */
	public function buyWithoutPaying()
	{
		/** @var Subscriber $subscriber */
		$subscriber = Auth::guard('subscribers')->user();

		if (!$subscriber) {
			return Redirect::to(urlRouteName('profile'))->with('error', __('auth.must-be-connected'));
		}

		$totals = Cart::getTotals();

		if ($totals['total'] !== 0) {
			return back()->with('error', __('Please try again later'));
		}

		//		$pricecut = PriceCut::whereActive(true)->where('code', Cart::getCoupon())->first();

		$order = Order::create([
			'order_datetime'  => now(),
			'subscriber_id'   => $subscriber->id,
			//			'price_cut_id'    => $pricecut ? $pricecut->id : null,
			'sub_total_price' => $totals['sub_total'],
			//			'discount_amount' => $totals['discount'],
			'tps_price'       => $totals['tps'],
			'tvq_price'       => $totals['tvq'],
			'total_price'     => $totals['total'],
			'token'           => StringUtility::generateRandomString(20),
			'is_cart'         => false,
		]);

		Cart::buyCart($order, $subscriber);
		return Redirect::to(urlRouteName('purchase-confirmation'))->with('success', __('cart.bought'));
	}

	/**
	 * @param Subscriber $subscriber
	 * @return Order|Model
	 * @throws Exception
	 */
	private function createCartOrder(Subscriber $subscriber)
	{

		$totals = Cart::getTotals();

		$subscriber->orders()->where('is_cart', true)->delete();

		return $subscriber->orders()->create([
			'order_datetime'  => now(),
			'sub_total_price' => $totals['sub_total'],
			'tps_price'       => $totals['tps'],
			'tvq_price'       => $totals['tvq'],
			'total_price'     => $totals['total'],
			'token'           => StringUtility::generateRandomString(20),
			'is_cart'         => true,
		]);
	}
}
