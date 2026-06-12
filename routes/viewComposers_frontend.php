<?php

use App\Models\Service;
use App\Models\ServiceCategory;

View::composer('core.partials.page.header', static function ($view) {
    // Compteur de membres : le plus récent numéro de la séquence partagée C/F (sans la lettre).
    // DB::table contourne les global scopes pour couvrir tous les membres, même inactifs.
    $latestMemberNumber = DB::table('subscribers')->max('member_number');

    return $view->with('latestMemberNumber', $latestMemberNumber);
});

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
