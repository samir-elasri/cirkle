<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SavedSearch
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $obsolete
 * @property int|null $subscriber_id
 * @property int $active
 * @property string|null $postal_code
 * @property int|null $service_id
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Service|null $service
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch query()
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereObsolete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SavedSearch whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SavedSearch extends Model
{
    use HasFactory;

    public string $order_default = 'id';
    public string $order_direction = 'desc';

    protected bool $bigData = true;

    protected $fillable = [
	    'subscriber_id',
        'created_at',
        'provider_type',
		'postal_code',
//		'service_id',
        'services',
        'serviceCategories',
	    'obsolete',
	    'active',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
	    'obsolete',
	    'subscriber_id',
        'active',
    ];

    protected array $niceNames = [
	    'obsolete' => 'Caduque',
		'postal_code' => 'Code postal',
		'service_id' => 'Service',
		'services' => 'Services',
		'serviceCategories' => 'Catégories de service',
        'provider_type'                             => 'Type de fournisseur',
    ];

    protected array $enum = [
        'provider_type' => [
            'residential' => 'Résidentiel',
            'business'    => 'Business',
        ]
    ];

    protected array $customFields = [
        'services' => [
            'widget' => 'associate_entities',
            'options' => [
                'associate_class' => Service::class,
                'relation' => 'services',
            ]
        ],
        'serviceCategories' => [
            'widget' => 'associate_entities',
            'options' => [
                'associate_class' => ServiceCategory::class,
                'relation' => 'serviceCategories',
            ]
        ],
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'saved_search_service');
    }

    public function serviceCategories()
    {
        return $this->belongsToMany(ServiceCategory::class, 'saved_search_service_category');
    }

	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class);
	}

    public function getUrlAttribute(): string {
        return urlRouteName('providers-search', null, true)
            . '?postal_code=' . urlencode($this->postal_code ?? '')
            . '&displayPostalCode=' . urlencode($this->postal_code ?? '')
            . '&provider_type=' . urlencode($this->provider_type ?? '')
            . '&categories=' . $this->serviceCategories()
                ->whereNull('service_categories.service_category_id')
                ->implode('service_categories.id', ',')
            . '&subcategories=' . $this->serviceCategories()
                ->whereNotNull('service_categories.service_category_id')
                ->implode('service_categories.id', ',')
            . '&services=' . $this->services->implode('id', ',');
    }

    public function doSearch() {
        return Subscriber::ProviderSearch(
            $this->provider_type,
            $this->serviceCategories()
                ->whereNull('service_categories.service_category_id')
                ->get(),
            $this->serviceCategories()
                ->whereNotNull('service_categories.service_category_id')
                ->get(),
            $this->services?->pluck('service_id'),
            $this->postal_code
        );
    }

//	public function service(): BelongsTo
//	{
//		return $this->belongsTo(Service::class);
//	}
}
