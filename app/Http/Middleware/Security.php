<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Security
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param  Closure  $next
	 * @return mixed
	 * @noinspection PhpReturnDocTypeMismatchInspection
	 */
	public function handle(Request $request, Closure $next)
	{
		/** @var Response $response */
		$response = $next($request);

		foreach(config('security.headers') as $key => $value) {

			if ($key === 'Content-Security-Policy') {
				$value = "default-src 'self' 'unsafe-inline' 'unsafe-eval' script-src-elem: blob: data: ";

				$value .= implode(' ', config('security.csp-sites'));
			}

			if($value && method_exists($response, 'header')) {
				$response->header($key, $value);
			}
		}

		return $response;
	}
}
