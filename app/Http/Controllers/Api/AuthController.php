<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Core\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	private $frontendGuards = [
		'subscribers' => Subscriber::class,
	];

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 *
	 * @throws Exception
	 */
	public function login(Request $request): JsonResponse
	{
		$request->validate(Subscriber::$auth_rules);

		foreach ($this->frontendGuards as $guard => $class) {
			if (Auth::guard($guard)->attempt(array(
				'email'    => $request->get('email'),
				'password' => $request->get('password'),
			))) {
				/** @var Subscriber $subscriber */
				$subscriber = Auth::guard($guard)->user();

				if (!$subscriber->active) {
					return response()->json([
						'message' => __('auth.register.login-failure')
					], 403);
				}

				$subscriber->login_datetime = Carbon::now()->toDateTimeString();
				$subscriber->api_token = \Str::random(60);
				$subscriber->save(); // to get last connection into updated

				return response()->json([
					'message'   => __('auth.subscriber.login-success'),
					'api_token' => $subscriber->api_token
				]);
			}
		}

		return response()->json([
			'message' => __('auth.register.login-failure'),
		], 404);
	}

	/**
	 * @param  Request  $request
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	public function register(Request $request): JsonResponse
	{
		$data = $request->validate([
			'email'                 => 'required|email|unique:subscribers,email',
			'check_email'           => 'required_with:email|same:email',
			'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
			'password_confirmation' => 'required_with:password|same:password',
			'last_name'             => 'required',
			'first_name'            => 'required',
			'accept_condition'      => 'accepted',
		]);

		$data['active'] = true;

		$subscriber = new Subscriber();

		if ($subscriber->saveElement($data)) {
			$token = $subscriber->recoveringToken();

			$data['url'] = urlRouteName('subscriber.validate', ['token' => $token], true);

			if ($subscriber->sendMail('register', $data)) {
				return response()->json([
					'message' => __('form.register_success_message')
				]);
			}

			return response()->json([
				'message' => __('main.errorOccurred')
			], 500);
		}

		return response()->json($subscriber->errors, 422);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function updatePassword(Request $request): JsonResponse
	{
		$subscriber = Auth::user();

		if ($subscriber === null) {
			return response()->json([
				'message' => __('main.subscriber.not-found')
			], 404);
		}

		$data = $request->validate([
			'password'              => 'required|regex:/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/',
			'password_confirmation' => 'required_with:password|same:password',
		]);

		if ($subscriber->saveElement($data, true)) {
			return response()->json([
				'message' => __('auth.register.password-form-success')
			]);
		}

		return response()->json($subscriber->errors, 422);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function logout(Request $request): JsonResponse
	{
		$subscriber = Auth::user();

		if ($subscriber === null) {
			return response()->json([
				'message' => __('main.subscriber.not-found')
			], 404);
		}

		$subscriber->api_token = null;
		$subscriber->save();

		return response()->json([
			'message' => __('auth.subscriber.login-disconnected')
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function lostPassword(Request $request)
	{
		$data = $request->validate([ 'email-or-cellphone' => 'required|email' ]);

		$email = $data['email-or-cellphone'];

		$subscriber = Subscriber::where('email', $email)->whereActive(true)->first();

		if ($subscriber) {
			try {
				$token = $subscriber->recoveringToken();
			} catch(Exception $e) {
				return response()->json([
					'message' => $e->getMessage()
				], $e->getCode());
			}

			$url = urlRouteName('reset-password', [], true) . '?token=' . $token;
			$subscriber->sendMail('recover', [
				'subscriber' => $subscriber,
				'url'        => $url
			], app()->getLocale());
		}

		return response()->json([
			'message' => __('auth.register.lost-success')
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return JsonResponse
	 */
	public function profile(Request $request): JsonResponse {
		return response()->json([
			'message' => '',
			'data' => $request->user()
		]);
	}
}
