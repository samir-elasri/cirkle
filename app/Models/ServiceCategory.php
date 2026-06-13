<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ServiceCategory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $label
 * @property string|null $image
 * @property int|null $service_category_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ServiceCategory> $serviceCategory
 * @property-read int|null $service_category_count
 * @property-read \App\Models\Translations\ServiceCategoryTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\ServiceCategoryTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @property string|null $legend
 * @property string|null $provider_description
 * @property string|null $client_description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory translated()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereServiceCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceCategory withTranslation()
 * @mixin \Eloquent
 */
class ServiceCategory extends Model implements TranslatableContract
{
	use Translatable;
	use HasFactory;

	public string $order_default = 'id';
	public string $order_direction = 'desc';

	protected bool $bigData = true;

/*	protected $appends = [
		'label',
		'title'
	];*/


	protected array $gridFields = [];

    public $searchFields = [
        'title',
        'description',
        'provider_description',
        'client_description',
    ];

	protected $fillable = [
		'label',
		'provider_type',
		'title',
		'image',
		'legend',
		'description',
		'provider_description',
		'client_description',
		'service_category_id',
		'customers_text',
		'capabilities_text',
		'services_intro_text',
		'keywords_json',
		'active',
	];

	public array $translatedAttributes = [
		'title',
		'description',
		'provider_description',
		'client_description',
		'legend',
		'customers_text',
		'capabilities_text',
		'services_intro_text',
		'keywords_json',
	];

	public array $positionParentFields = [];

	protected array $grid = [
		'id',
		'created_at',
		'label',
		'description',
		'serviceCategory.label',
		'active'
	];

	protected array $niceNames = [
		'label'                => 'Titre interne',
		'provider_type'        => 'Clientèle cible',
		'title'                => 'Libellé',
		'image'                => 'Image',
		'legend'               => 'Légende',
		'description'          => 'Définition',
		'service_category_id'  => 'Catégorie de service associé',
		'provider_description' => 'Description Fournisseur',
		'client_description'   => 'Description Client',
		'active'               => 'Actif',
	];


	protected array $enum = [];

	protected array $customFields = [
		'provider_description' => [
			'widget'  => 'wysiwyg',
			'options' => [
				'height' => 150
			]
		],
		'client_description'   => [
			'widget'  => 'wysiwyg',
			'options' => [
				'height' => 150
			]
		],
	];

	public function serviceCategory()
	{
		return $this->belongsTo(ServiceCategory::class);
	}


	public function serviceCategories() {
		return $this->hasMany(ServiceCategory::class);
	}

	public function services()
	{
		// source_row : ordre littéral du fichier MASTER 2350 (null en dernier pour l'ancien contenu)
		return $this->hasMany(Service::class)->where('type', 'service')
			->orderByRaw('source_row IS NULL, source_row')->orderBy('id');
	}

	public function capabilities()
	{
		return $this->hasMany(Service::class)->where('type', 'capability')
			->orderByRaw('source_row IS NULL, source_row')->orderBy('id');
	}

	public function getUrlAttribute() {
		if ($this->service_category_id) {
			return urlRouteName('profession', ['id' => $this->id], true);
		}
		return urlRouteName('category', ['id' => $this->id], true);
	}
}
