<?php

use App\Models\Core\MenuTree;
use App\Models\Core\MiniCardGroup;
use App\Models\Core\SearchResult;
use App\Models\Core\Subscriber;
use Carbon\Carbon;

View::composer('core.pages.search-results', function ($view) {
	$query = Request::get('q');
	$searchResults = SearchResult::getSearchResults($query);
	$totalResultCount = 0;
	foreach ($searchResults as $item) {
		$totalResultCount += count($item);
	}
	$view->with(['results'          => $searchResults,
				 'totalResultCount' => $totalResultCount,
				 'query'            => $query
	]);
});

View::composer('core.pages.event-list', function ($view) {

	$now = Carbon::now();

	$year = Request::get('year', $now->year);
	$month = Request::get('month', $now->month);

	$events = Event::getForMonth($year, $month)->where('active', true)->orderBy('start_datetime')->get();

	return $view->with([
		'events' => $events
	]);
});

View::composer('core.partials.admin-bar', function ($view) {

	$sids = Subscriber::selectRaw('CONCAT_WS(first_name, last_name) as name, id')
		->active()
		->pluck('name', 'id');

	return $view->with(compact(
		'sids'
	));
});

View::composer('core.partials.page.header', static function ($view) {
	MenuTree::setupMenus();
});

View::composer('core.layouts.page', static function ($view) {
	$view->optimal_content_width = $view->optimal_content_width ?? setting()->optimal_content_width;
	$view->default_bloc_spacing = $view->default_bloc_spacing ?? setting()->default_bloc_spacing;
	$view->default_bloc_inner_spacing = $view->default_bloc_inner_spacing ?? setting()->default_bloc_inner_spacing;
	$view->right_column_spacing = $view->right_column_spacing ?? 40;
	$view->right_column_width = $view->right_column_width ?? 300;
	$view->default_single_image_height = $view->default_single_image_height ?? setting()->default_single_image_height;
	$view->page_top_spacing = $view->page_top_spacing ?? setting()->default_page_top_spacing;
	$view->footer_top_spacing = $view->footer_top_spacing ?? setting()->default_footer_top_spacing;
	$view->has_right_column = $view->has_right_column ?? false;
});


