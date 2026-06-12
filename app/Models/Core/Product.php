<?php

namespace App\Models\Core;

use App\Models\Translations\ProductTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use SplTempFileObject;

/**
 * App\Models\Core\Product
 *
 * @property int $id
 * @property string|null $label
 * @property int|null $category
 * @property int $manage_inventory
 * @property int|null $quantity_left
 * @property string|null $base_price
 * @property int|null $current_discount
 * @property string|null $discount_end_date
 * @property int $promotion
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read ProductTranslation|null $translation
 * @property-read Collection<int, ProductTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static Builder|Product listsTranslations(string $translationField)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product notTranslatedIn(?string $locale = null)
 * @method static Builder|Product orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Product orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Product orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Product query()
 * @method static Builder|Product translated()
 * @method static Builder|Product translatedIn(?string $locale = null)
 * @method static Builder|Product whereActive($value)
 * @method static Builder|Product whereBasePrice($value)
 * @method static Builder|Product whereCategory($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereCurrentDiscount($value)
 * @method static Builder|Product whereDiscountEndDate($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereLabel($value)
 * @method static Builder|Product whereManageInventory($value)
 * @method static Builder|Product wherePromotion($value)
 * @method static Builder|Product whereQuantityLeft($value)
 * @method static Builder|Product whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Product whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product withTranslation()
 * @mixin Eloquent
 */
class Product extends Model implements TranslatableContract
{

	use Translatable;

	protected $fillable = [
		'label',
		'title',
		'category',
		'description',
		'manage_inventory',
		'quantity_left',
		'base_price',
		'current_discount',
		'discount_end_date',
		'promotion',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'description',
	];


	protected array $toggleFields = [];

	protected array $customFields = [
		'category'    => [
			'widget'  => 'associate_entity',
			'options' => [
				'associate_class' => ProductCat::class,
			]
		],
		'description' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		]
	];

	protected array $grid = [
		'label',
		'quantity_left',
		'base_price',
		'active',
	];

	protected array $rules = [];

	protected array $niceNames = [
		'category'          => 'Catégorie ',
		'manage_inventory'  => 'Gérer l’inventaire ',
		'quantity_left'     => 'Quantité restante',
		'base_price'        => 'Prix de base (non-membre)',
		'current_discount'  => 'Rabais courant %',
		'discount_end_date' => 'Date/heure limite du rabais',
		'promotion'         => 'En promotion ',
		'active'            => 'Actif',
		'label'             => 'Titre interne',
		'title'             => 'Titre',
	];

	protected $appends = ['subscriber_grid'];

	protected $exports = ['generic' => ['label' => 'Exporter la liste', 'method' => 'exportFile']];

	public function blocs()
	{
		return $this->morphMany(Bloc::class, 'pageable');
	}

	public function exportFile()
	{

		set_time_limit(0);

		$entities = static::all();
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		if ($entities->count()) {

			$csv->setDelimiter(';');
			$csv->setOutputBOM(Writer::BOM_UTF8);

			$header = [
				'label',
				'title',
				'category',
				'manage_inventory',
				'quantity_left',
				'base_price',
				'current_discount',
				'discount_end_date',
				'promotion',
				'active'
			];
			foreach ($header as $index => $value) {
				$header[$index] = $this->niceNames[$value];
			}

			$csv->insertOne($header);
			foreach ($entities as $entity) {
				$csv->insertOne([
					$entity->label,
					$entity->title,
					$entity->category ? Category::getTitle($entity->category) : 'N/D',
					$entity->manage_inventory,
					$entity->quantity_left,
					$entity->base_price,
					$entity->current_discount,
					prettyDate($entity->discount_end_date),
					$entity->promotion ? 'Oui' : 'Non',
					$entity->active ? 'Oui' : 'Non',
				]);
			}
		}
		$csv->output('Produits.csv');
		exit;
	}
}
