<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Promotion
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $image
 * @property string|null $legend
 * @property int|null $in_progress
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Translations\PromotionTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\PromotionTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion translated()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereInProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereLegend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion withTranslation()
 * @mixin \Eloquent
 */
class Promotion extends Model
{
    use HasFactory;
    use Translatable;

    public string $order_default = 'position';
    public string $order_direction = 'asc';

	protected bool $bigData = true;

	protected $fillable = [
		'title',
		'image',
		'legend',
		'in_progress',
		'subscriber_id',
		'description',
	];

	public array $positionParentFields = ['subscriber_id'];

    protected array $grid = [
        'id',
		'title',
        'active'
    ];

	public array $translatedAttributes = [
		'title',
		'description',
	];

    protected array $niceNames = [
		'title' => 'Titre',
		'image' => 'Photo',
		'legend' => 'Légende',
		'in_progress' => 'En cours',
		'subscriber_id' => 'Inscrit',
		'description' => 'Description',
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

    /**
     * @return HasMany|ChildClass[]|ChildClass
     */
    /*public function children()
    {
    	return $this->hasMany('#TODO');
    }*/
}
