<?php

namespace App\Models\Core\Forms;

use App\Models\Core\Bloc;
use App\Models\Core\Blocs\BlocForm;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\ChoiceTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Forms\Choice
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $label
 * @property string|null $code_value
 * @property int|null $other
 * @property int|null $position
 * @property int $active
 * @property int|null $choice_group_id
 * @property-read ChoiceGroup|null $choiceGroup
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read ChoiceGroup|null $group
 * @property-read ChoiceTranslation|null $translation
 * @property-read Collection<int, ChoiceTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|Choice listsTranslations(string $translationField)
 * @method static Builder|Choice newModelQuery()
 * @method static Builder|Choice newQuery()
 * @method static Builder|Choice notTranslatedIn(?string $locale = null)
 * @method static Builder|Choice orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Choice orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Choice orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Choice query()
 * @method static Builder|Choice translated()
 * @method static Builder|Choice translatedIn(?string $locale = null)
 * @method static Builder|Choice whereActive($value)
 * @method static Builder|Choice whereChoiceGroupId($value)
 * @method static Builder|Choice whereCodeValue($value)
 * @method static Builder|Choice whereCreatedAt($value)
 * @method static Builder|Choice whereId($value)
 * @method static Builder|Choice whereLabel($value)
 * @method static Builder|Choice whereOther($value)
 * @method static Builder|Choice wherePosition($value)
 * @method static Builder|Choice whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Choice whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Choice whereUpdatedAt($value)
 * @method static Builder|Choice withTranslation()
 * @mixin Eloquent
 */
class Choice extends Model implements TranslatableContract
{
	use Translatable;

	public string $singular = 'un choix';

	public $relatedGrid = 'choix pour ce regroupement de choix';

	public string $order_default = 'position';
	public string $order_direction = 'ASC';

	protected $fillable = [
		'choice_group_id',
		'label',
		'title',
		'code_value',
		'other',
		'active',
	];

	public $translatedAttributes = [
		'title',
	];

	protected array $rules = [
		'title'      => 'required',
		'code_value' => 'required',
	];

	protected array $niceNames = [
		'choice_group_id' => 'Formulaire',
		'label'           => 'Titre interne',
		'title'           => 'Titre',
		'code_value'      => 'Code/valeur',
		'other'           => 'Type autre',
	];

	protected array $grid = ['label', 'title', 'active'];

	/**
	 * @return BelongsTo|ChoiceGroup
	 */
	public function group()
	{
		return $this->belongsTo(ChoiceGroup::class, 'choice_group_id');
	}

	/**
	 * @return BelongsTo|ChoiceGroup
	 */
	public function choiceGroup()
	{
		return $this->belongsTo(ChoiceGroup::class);
	}

	protected static function boot()
	{
		parent::boot();

		static::saved(function ($model) {

			$fields = FormField::where('choice_group_id', $model->choice_group_id)->pluck('form_generator_id')->toArray();

			$bloc_ids = BlocForm::whereIn('form_generator_id', $fields)->pluck('id')->toArray();
			$blocs = Bloc::where('blocable_type', BlocForm::class)->whereIn('blocable_id', $bloc_ids)->get();
			foreach ($blocs as $bloc) {
				$page = $bloc->pageable_type::find($bloc->pageable_id);

				if (Cache::has($page->getCacheKey())) {
					Cache::pull($page->getCacheKey());
				}
			}
		});
	}
}
