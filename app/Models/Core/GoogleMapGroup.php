<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\GoogleMapGroup
 *
 * @property int $id
 * @property string|null $label
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Collection<int, GoogleMap> $googleMaps
 * @property-read int|null $google_maps_count
 * @method static Builder|Model active()
 * @method static Builder|GoogleMapGroup newModelQuery()
 * @method static Builder|GoogleMapGroup newQuery()
 * @method static Builder|GoogleMapGroup query()
 * @method static Builder|GoogleMapGroup whereActive($value)
 * @method static Builder|GoogleMapGroup whereCreatedAt($value)
 * @method static Builder|GoogleMapGroup whereId($value)
 * @method static Builder|GoogleMapGroup whereLabel($value)
 * @method static Builder|GoogleMapGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class GoogleMapGroup extends Model
{
	protected $fillable = [
		'label',
	];

	protected array $niceNames = [
		'label' => 'Titre interne',
	];

	protected array $grid = ['label'];

	/**
	 * @return HasMany|GoogleMap[]|GoogleMap
	 */
	public function googleMaps()
	{
		return $this->hasMany(GoogleMap::class);
	}
}
