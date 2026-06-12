<?php

use App\Models\Service;
use App\Models\ServiceCategory;

View::composer('partials.profileOptionsForm', function ($view) {
	$profileOptions = setting()->sorted_profile_options;

	return $view->with('profileOptions', $profileOptions);
});

View::composer('partials.providers.logged-in-search-filters', static function ($view) {
    $categories = ServiceCategory::where('active', '=', true)
        ->whereNull('service_category_id')
        ->get()
        ->sortBy('title');

    $subcategories = ServiceCategory::where('active', '=', true)
        ->whereNotNull('service_category_id')
        ->get()
        ->sortBy('title');

    $services = Service::where('active', '=', true)
        ->whereNotNull('service_category_id')
        ->with('serviceCategory')
        ->get()
        ->sortBy('title');

    return $view->with('categories', $categories)
        ->with('subcategories', $subcategories)
        ->with('services', $services);
});

View::composer('pages.home', static function ($view) {
    $categories = ServiceCategory::where('active', '=', true)
        ->whereNull('service_category_id')
        ->get()
        ->sortBy('title');

    $subcategories = ServiceCategory::where('active', '=', true)
        ->whereNotNull('service_category_id')
        ->get()
        ->sortBy('title');

    return $view->with('categories', $categories)
        ->with('subcategories', $subcategories);
});
