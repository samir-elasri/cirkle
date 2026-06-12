<?php

namespace Cart;

use App\Models\Core\BasicCart;
use \Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
	public function register()
	{
		$this->app->singleton('cart', static function() {

			return new BasicCart();
		});
	}
}