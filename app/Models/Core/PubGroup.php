<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\PubGroup
 *
 * @property int $id
 * @property string|null $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $nb_elements
 * @property-read SearchResult $search_result
 * @property-read Collection<int, Pub> $pubs
 * @property-read int|null $pubs_count
 * @method static Builder|Model active()
 * @method static Builder|PubGroup newModelQuery()
 * @method static Builder|PubGroup newQuery()
 * @method static Builder|PubGroup query()
 * @method static Builder|PubGroup whereCreatedAt($value)
 * @method static Builder|PubGroup whereId($value)
 * @method static Builder|PubGroup whereLabel($value)
 * @method static Builder|PubGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PubGroup extends Model
{

	public string $order_default = 'label';

	public string $order_direction = 'ASC';

	public $visible = ['id', 'label', 'nbElements', 'pubs'];

	/**
	 * Propriété dynamique
	 */
	protected $appends = array('nbElements');

	/**
	 *  MAIN
	 */
	protected array $rules = ['label' => 'required'];

	protected $fillable = ['label'];

	protected array $grid = ['label', 'nbElements'];

	protected array $niceNames = ['nbElements' => 'Nb. d\'éléments'];

	protected function getNbElementsAttribute() { return $this->pubs->count(); }

	public function pubs()
	{
		return $this->hasMany(Pub::class);
	}
}
