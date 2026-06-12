<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\CategoryGroup
 *
 * @property int $id
 * @property string|null $identifier
 * @property string|null $title
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Category> $categories
 * @property-read int|null $categories_count
 * @property-read mixed $collection_name
 * @property-read string $nb_elements
 * @property-read SearchResult $search_result
 * @property-read string $title_grid
 * @method static Builder|Model active()
 * @method static Builder|CategoryGroup newModelQuery()
 * @method static Builder|CategoryGroup newQuery()
 * @method static Builder|CategoryGroup query()
 * @method static Builder|CategoryGroup whereActive($value)
 * @method static Builder|CategoryGroup whereCreatedAt($value)
 * @method static Builder|CategoryGroup whereId($value)
 * @method static Builder|CategoryGroup whereIdentifier($value)
 * @method static Builder|CategoryGroup whereTitle($value)
 * @method static Builder|CategoryGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CategoryGroup extends Model
{

	public string $order_default = 'id';

	public string $order_direction = 'ASC';

	public $visible = ['id', 'identifier', 'nbElements', 'categories'];

	protected $fillable = [
		'title',
		'identifier'
	];

	protected array $grid = ['titleGrid', 'nbElements'];

	protected array $rules = ['title' => 'required'];

	protected array $niceNames = [
		'id'         => 'Ordre',
		'titleGrid'  => 'Titre',
		'identifier' => 'Identifiant',
		'nbElements' => 'Nb. d\'éléments'
	];

	protected $appends = ['nbElements'];

	/**
	 * @return string
	 */
	protected function getNbElementsAttribute(): string
	{
		$url = adminRouteName('admin.category_groups.edit', [$this->id, 'categories']);
		return "<a href='$url'>{$this->categories->count()}</a>";
	}

	/**
	 * @return string
	 */
	protected function getTitleGridAttribute(): string
	{
		return $this->title . (empty($this->identifier) ? '' : '<br/><small>' . $this->identifier . '</small>');
	}

	/**
	 * @param $identifier
	 * @return CategoryGroup
	 */
	public static function finentifier($identifier): CategoryGroup
	{
		return CategoryGroup::firstOrCreate(['identifier' => $identifier]);
	}

	/**
	 * @return HasMany|Category[]|Category
	 */
	public function categories()
	{
		return $this->hasMany(Category::class);
	}

}
