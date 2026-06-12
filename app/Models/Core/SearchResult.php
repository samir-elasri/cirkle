<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Models\Core\SearchResult
 *
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|SearchResult newModelQuery()
 * @method static Builder|SearchResult newQuery()
 * @method static Builder|SearchResult query()
 * @mixin Eloquent
 */
class SearchResult extends Model
{

	public $url;
	public $label;
	public $id;
	public $type_element;

	public static function getSearchResults($string)
	{

		$search =  new Collection;
		$search['pages'] = Page::Search($string);
        $search['providers'] = Subscriber::Search($string);

		return $search;
	}
}
