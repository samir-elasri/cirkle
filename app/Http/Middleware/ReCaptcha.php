<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use ReCaptcha\Response;

class ReCaptcha
{
	public function handle($request, Closure $next, $action = '', $score = 0.34, $hostname = '')
	{
		if (config('google.recaptcha.active')) {

			/** @var \ReCaptcha\ReCaptcha $recaptcha */
			$recaptcha = app('recaptcha');

			if (false) {
				$recaptcha->setExpectedHostname($hostname ?: $request->getHost());
			}

			if ($action) {
				$recaptcha->setExpectedAction($action);
			}

			if ($score) {
				$recaptcha->setScoreThreshold($score);
			}

			$response = $recaptcha->verify(
				$request->input(config('google.recaptcha.input_name')),
				$request->getClientIp()
			);

			if (!$response->isSuccess()) {
				Log::error('Recaptcha Error', $response->toArray());

				if ($request->expectsJson()) {
					return response()->json([
						'error' => __('validation.form.robot')
					], 400);
				}

				return redirect()->back()->with('error', __('validation.form.robot'))->withInput();
			}
		}

		return $next($request);
	}
}
