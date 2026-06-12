<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class Role
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  Request  $request
	 * @param  Closure  $next
	 * @param $role
	 * @return mixed
	 */
    public function handle($request, Closure $next, $role)
    {
		if (! Auth::guard('users')->user()->$role ){
			return redirect(route('admin.dashboard'));
		}

        return $next($request);
    }
}
