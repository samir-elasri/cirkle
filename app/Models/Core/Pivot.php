<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

/**
 * App\Models\Core\Pivot
 *
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pivot query()
 * @mixin \Eloquent
 */
class Pivot extends Model
{
	use AsPivot;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];
}
