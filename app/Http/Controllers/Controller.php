<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbiance\MediaUtility\MediaTrait;
use Route;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, MetaTrait, MediaTrait;

    /*
    * Constructor
    */
    public function __construct() {

        if (!config('app.isCache', false)) Cache::flush();

        View::share('route_name', Route::currentRouteName());
        View::share('custom_code', null);

    }
}
