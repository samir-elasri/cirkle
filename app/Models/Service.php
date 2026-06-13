<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use App\Models\Translations\ServiceTranslation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Service
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $label
 * @property string|null $image
 * @property string|null $legend
 * @property int|null $service_category_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceCategory> $serviceCategory
 * @property-read int|null $service_category_count
 * @property-read \App\Models\Translations\ServiceTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\ServiceTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|Service listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Service orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Service orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Service orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service translated()
 * @method static \Illuminate\Database\Eloquent\Builder|Service translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereLegend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereServiceCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service withTranslation()
 * @mixin \Eloquent
 */
class Service extends Model implements TranslatableContract
{
	use HasFactory, Translatable;

	public string $order_default = 'id';
	public string $order_direction = 'desc';

	protected bool $bigData = true;

    public $searchFields = [
        'title'
    ];

	protected $fillable = [
		'label',
		'type',
		'has_input',
		'source_row',
		'gap_before',
		'title',
		'formatted_title',
		'input_label',
		'description',
		'image',
		'legend',
		'service_category_id',
		'active',
	];

	public array $positionParentFields = [];
	public array $translatedAttributes = [
		'title',
		'formatted_title',
		'description',
		'input_label',
	];

//	protected $appends = [
//		'label'
//	];

	protected array $grid = [
		'id',
		'type',
		'created_at',
		'label',
		'title',
		'serviceCategory.label',
		'serviceCategory.title',
		'input_label',
		'active'
	];

	protected array $niceNames = [
		'label'                => 'Titre interne',
		'title'                => 'Libellé',
		'formatted_title'      => 'Libellé formaté (colonne C littérale)',
		'has_input'            => 'Saisie fournisseur (X colonne D)',
		'source_row'           => 'Ligne Excel d\'origine',
		'gap_before'           => 'Saut de bloc avant (espacement littéral)',
		'image'                => 'Image',
		'legend'               => 'Légende',
		'description'          => 'Définition',
		'service_category_id' => 'Catégorie de service associé',
		'active'               => 'Actif',
		'input_label' => 'Champ texte',
		'type' => 'Type',
	];

	protected array $enum = [];

	protected array $customFields = [];

	/**
	 * @return BelongsTo|TODOClass
	 */
	/*public function parent()
	{
		return $this->belongsTo('#TODO');
	}*/

	public function serviceCategory()
	{
		return $this->belongsTo(ServiceCategory::class);
	}
}
