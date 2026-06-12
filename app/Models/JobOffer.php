<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\JobOffer
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $currently_recruiting
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read \App\Models\Translations\JobOfferTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Translations\JobOfferTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $description
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer translated()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereCurrentlyRecruiting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOffer withTranslation()
 * @mixin \Eloquent
 */
class JobOffer extends Model
{
    use HasFactory;
    use Translatable;

    public string $order_default = 'id';
    public string $order_direction = 'desc';
	public bool $isAjaxEnabled = true;

    protected bool $bigData = true;

    protected $fillable = [
        'currently_recruiting',
        'subscriber_id',
        'title',
        'description',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
        'active'
    ];

	public array $translatedAttributes = [
		'title',
		'description',
	];

    protected array $niceNames = [
		'currently_recruiting' => 'Recrutement en cours',
		'title' => 'Titre',
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
