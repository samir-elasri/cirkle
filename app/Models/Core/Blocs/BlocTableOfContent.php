<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocTableOfContentTranslation;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocTableOfContent
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocTableOfContentTranslation|null $translation
 * @property-read Collection<int, BlocTableOfContentTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|BlocTableOfContent listsTranslations(string $translationField)
 * @method static Builder|BlocTableOfContent newModelQuery()
 * @method static Builder|BlocTableOfContent newQuery()
 * @method static Builder|BlocTableOfContent notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocTableOfContent orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocTableOfContent orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocTableOfContent orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocTableOfContent query()
 * @method static Builder|BlocTableOfContent translated()
 * @method static Builder|BlocTableOfContent translatedIn(?string $locale = null)
 * @method static Builder|BlocTableOfContent whereCreatedAt($value)
 * @method static Builder|BlocTableOfContent whereId($value)
 * @method static Builder|BlocTableOfContent whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocTableOfContent whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocTableOfContent whereUpdatedAt($value)
 * @method static Builder|BlocTableOfContent withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocTableOfContent extends BlocModel
{
	use Translatable;
	use HasFactory;

	public string $order_default = 'id';
	public string $order_direction = 'asc';
	/**
	 * @var null|Collection
	 */
	public $blocs = null;

	protected bool $bigData = true;

	protected $fillable = [
		'@ Paramètres du bloc table des matières',
		'label',
		'title',
		'title_color',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active'
	];

	public $translatedAttributes = [
		'title',
	];

	public array $positionParentFields = [];

	protected array $grid = [
		'id'
	];

	protected array $niceNames = [];

	protected array $enum = [];

	protected array $customFields = [];

	protected $appends = ['data'];

	/**
	 * @return Attribute
	 */
	protected function data(): Attribute
	{
		return Attribute::make(
			get: function (){
				$bloc = $this->bloc;
				$position = $bloc->position;
				$pageableType = $bloc->pageable_type;
				$pageableId = $bloc->pageable_id;

				$data = collect();

				if ($this->blocs) {
					$blocs = $this->blocs->filter(function ($value, $key) use ($position) {
						/** @var Bloc $value */
						return $value->position > $position;
					});
				} else {
					$blocs = Bloc::active()
						->with('blocable')
						->where('pageable_id', $pageableId)
						->where('pageable_type', $pageableType)
						->where('position', '>', $position)
						->orderBy('position')
						->get();
				}

				foreach ($blocs as $bloc) {
					/** @var Bloc $bloc */
					if (optional($bloc->blocable)->title) {
						$data->push([
							'title' => $bloc->blocable->title,
							'target' => $bloc->target,
						]);
					}
				}

				return $data->split(2);
			}
		);
	}
}
