<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Core\Country;
use App\Models\Core\State;
use App\Models\Core\Subscriber;
use App\Models\Core\Subscription;
use Auth;
use Error;
use Illuminate\Http\Request;
use Validator;
use Redirect;
use Session;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Exception;
use Route;
use Arr;

class AuthController extends Controller
{
	private $frontendGuards = [
		'subscribers' => Subscriber::class,
	];

	public function profile($params)
	{
		/** @var Subscriber */
		if ($subscriber = Auth::guard('subscribers')->user()) {
			$params['subscriber'] = $subscriber;
			$params['orders'] = $subscriber->orders()->orderBy('order_datetime', 'DESC')->get();
		}

		$profileOptions = [
			'license' ,
			'promotion' ,
			'image' ,
			'estimation' ,
			'job_offer' ,
			'url'
		];

        $searches = $subscriber->savedSearches()->active()
            ->with('services')
            ->get();

		$countries = Country::active()->get();
		$states = State::active()->get();

        $orders = $subscriber->orders()
            ->where('is_cart', '=', false)
            ->orderByDesc('created_at')
            ->get();

		return array_merge($params, compact(
            'profileOptions',
            'searches',
            'countries',
            'states',
            'orders',
        ));
	}

	/**
	 * Controleur pour la liste des abonnements
	 *
	 * @param $params
	 * @return mixed
	 */
	public function subscriptionsList($params)
	{
		$params['subscriptions'] = Subscription::whereActive(true)->get();

		return $params;
	}

	public function updatePassword(Request $request)
	{
		$data = $request->all();
		$subscriber = null;

		foreach ($this->frontendGuards as $guard => $class) {
			if (Auth::guard($guard)->check()) {
				$subscriber = Auth::guard($guard)->user();
				break;
			}
		}

		if (!$subscriber) {
			foreach ($this->frontendGuards as $guard => $class) {
				/** @var Subscriber $class */
				$subscriber = $class::getByToken($request->token);
				if ($subscriber) {
					$data['email_validated'] = true;
					$data['recover_token'] = '';
					break;
				}
			}
		}


		if ($subscriber === null) {
			return Redirect::to('/')->with(
				'error',
				__('main.subscriber.not-found')
			);
		}

		$validator = Validator::make($data, [
			'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
			'password_confirmation' => 'required_with:password|same:password',
		]);

		if ($validator->fails()) {
			return Redirect::back()->with('errors', $validator->errors())->withInput();
		}

		if ($subscriber->saveElement($data, true)) {
			return Redirect::to(urlRouteName('profile'))->with(
				'success',
				__('auth.register.password-form.success')
			);
		}

		return Redirect::back()->with('error', $subscriber->errors);
	}

	/**
	 *
	 * Update SubScriber
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws Exception
	 */
	public function update(Request $request)
	{
		$data = $request->all();
		$sendMail = false;
		$subscriber = null;

		foreach ($this->frontendGuards as $guard => $class) {
			if (Auth::guard($guard)->check()) {
				$subscriber = Auth::guard($guard)->user();

				if (Arr::get($data, 'email') !== $subscriber->email) {
					$validator = Validator::make(
						$data,
						[
							'email'       => 'required|email',
							'check_email' => 'required_with:email|same:email',
						]
					);
					if ($validator->fails()) {
						return Redirect::back()->withInput()->with('error', __('form.same-email'));
					}
				}

				$validator = Validator::make($data, [
					'first_name' => 'required',
					'last_name'  => 'required',
				]);

				if ($validator->fails()) {
					return Redirect::back()->with(
						'error',
						$validator->errors()
					)->withInput();
				}

				/** @var Subscriber $subscriber */
				if ($subscriber->email !== $data['email']) {
					/** @var Subscriber $class */
					if ($class::whereEmail($data['email'])->first()) {
						return Redirect::back()->withInput()->with('error', __('validation.unique'));
					}

					$sendMail = true;
				}

				if ($subscriber->saveElement($data, true)) {
					if ($sendMail) {
						$subscriber->email_validated = false;
						$token = $subscriber->recoveringToken();

						$data['url'] = urlRouteName('subscriber.validate', [
							'token' => $token,
							'ec'    => 1
						], true);

						if ($subscriber->sendMail('email_changed', $data)) {
							return Redirect::back()->with('success', __('auth.register.email_changed.success'));
						}

						Session::flash('error', __('main.errorOccurred'));
						return Redirect::back()->withInput();
					}

					return Redirect::back()->with('success', __('auth.register.updated.success'));
				}

				return Redirect::back()->with('error', $subscriber->errors)->withInput();
			}
		}

		return Redirect::to('/')->with(
			'error',
			__('main.subscriber.not-found')
		);
	}

	/**
	 * @param $params
	 * @return RedirectResponse
	 */
	public function login($params)
	{
		if (logged_in()) {
			$url = urlRouteName('home');
			return Redirect::to($url);
		}
		return $params;
	}


	/**
	 *
	 * Create SubScriber token
	 *
	 */
	public function token()
	{
		return csrf_token();
	}

	public function postLogin(Request $request)
	{

		$data = $request->all();

		$validator = Validator::make($data, Subscriber::$auth_rules);

		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}

		foreach ($this->frontendGuards as $guard => $class) {
			if (Auth::guard($guard)->attempt(array(
				'email'           => $request->get('email'),
				'password'        => $request->get('password'),
			))) {
				/** @var Subscriber $subscriber */
				$subscriber = Auth::guard($guard)->user();

				if (!$subscriber->active) {
					return Redirect::to(urlRouteName('cart'))
					->with('error', __('auth.register.login.inactive'));
				}

				$subscriber->login_datetime = Carbon::now()->toDateTimeString();
				$subscriber->save(); // to get last connection into updated

				// Since this is a post function, back should always be the right url.
				return Redirect::back();
			}
		}

		return Redirect::back()->withInput()->with('error', __('auth.register.login.failure'));
	}

	/**
	 *
	 * Logout SubScriber
	 *
	 */
	public function logout()
	{
		if (logged_in()) {
			foreach ($this->frontendGuards as $guard => $class) {
				// We don't actually put anything in the guard session anymore...
				Session::forget($guard);
				Auth::guard($guard)->logout();
			}
		}
		// Vide le panier à la déconnexion : sinon le raccourci panier reste affiché
		// pour un visiteur déconnecté (et la page /panier renvoie 404 sans session valide).
		Session::forget('cart');
		Session::forget('coupon');
		return Redirect::to(urlRouteName('profile'))->with('success', __('auth.register.login.disconnected'));
	}

	/**
	 *
	 * SubScriber form : I lost my password
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws Exception
	 */
	public function lost(Request $request)
	{
		$data = $request->all();
		$subscriber = null;

		$validator = Validator::make($data, array('email' => 'required|email'));

		if (!$validator->fails()) {
			$email = $data['email'];

			foreach ($this->frontendGuards as $guard => $class) {
				/** @var Subscriber $class */
				$subscriber = $class::where('email', '=', $email)->whereActive(true)->first();
				if ($subscriber) {
					break;
				}
			}

			if ($subscriber) {
				$token = $subscriber->recoveringToken();

				$url = urlRouteName('reset-password', [], true) . '?token=' . $token;
				$subscriber->sendMail('recover', [
					'subscriber' => $subscriber,
					'url'        => $url
				]);
			}

			return Redirect::back()->with(
				'success',
				__('auth.register.lost.success')
			); // On indique tjrs succes pour ne pas dévoiler existence o/n des adresses des membres
		}

		return Redirect::back()->withErrors($validator)->withInput();
	}


	/**
	 *
	 * SubScriber form : I reset the password
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws Exception
	 */
	public function reset(Request $request)
	{
		$data = $request->all();
		$subscriber = null;

		try {
			foreach ($this->frontendGuards as $guard => $class) {
				/** @var Subscriber $class */
				$subscriber = $class::getByToken($data['token']);
				if ($subscriber) {
					break;
				}
			}

			if ($subscriber) {
				$validator = Validator::make($data, [
					'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
					'password_confirmation' => 'required_with:password|same:password',
				]);

				if (!$validator->fails()) {
					/** @var Subscriber $subscriber */
					if ($subscriber->saveElement($data, true)) {
						$subscriber->recover_token = '';
						$subscriber->email_validated = true;
						$subscriber->save();
						return Redirect::to(urlRouteName('profile'))->with(
							'success',
							__('auth.register.password-form.success')
						);
					}

					return Redirect::back()->withErrors($validator)->withInput();
				}

				return Redirect::back()->withErrors($validator)->withInput();
			}

			return Redirect::back()->with('error', __('reminders.token'));
		} catch (Exception|Error $e) {
			throw $e; // !TODO ???
		}
	}

	/**
	 * Validate a subscriber either his registration or the mail change
	 */
	public function validateSubscriber()
	{
		$subscriber = null;

		foreach ($this->frontendGuards as $guard => $class) {
			/** @var Subscriber $class */
			$subscriber = $class::getByValidationToken(Route::input('token'));

			if ($subscriber) {
				/** @var Subscriber $subscriber */
				$subscriber->email_validated = true;
				$subscriber->recover_token = '';
				$subscriber->save();
				Auth::guard($guard)->login($subscriber);

				return Redirect::to(urlRouteName('email-validated'))->with(
					'success',
					__('auth.register.validated.success')
				);
			}
		}

		// Jeton introuvable : lien déjà utilisé (usage unique), compte déjà confirmé,
		// ou lien expiré. On évite la page blanche brute : on renvoie vers la page de
		// validation avec un message clair.
		return Redirect::to(urlRouteName('email-validated'))->with('info', __('auth.register.validated.already'));
	}
}
