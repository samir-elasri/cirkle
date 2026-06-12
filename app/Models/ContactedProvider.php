<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ContactedProvider
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $client_id
 * @property int|null $provider_id
 * @property int $evaluation_mail_sent
 * @property int $deal_made
 * @property int $active
 * @property-read Subscriber|null $client
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read Subscriber|null $provider
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereDealMade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereEvaluationMailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactedProvider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContactedProvider extends Model
{
    use HasFactory;

    public string $order_default = 'id';
    public string $order_direction = 'desc';

    protected bool $bigData = true;

    protected $fillable = [
		'created_at',
        'client_id',
        'provider_id',
        'evaluation_mail_sent',
        'deal_made',
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
	    'client_id',
	    'provider_id',
	    'evaluation_mail_sent',
	    'deal_made',
    ];

    protected array $niceNames = [
	    'client_id' => 'Client',
	    'provider_id' => 'Fournisseur',
	    'evaluation_mail_sent' => 'Courriel d\'évaluation envoyé',
	    'deal_made' => 'On fait affaire',
		'created_at' => 'Date heure du contact'
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

	public function client(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class, 'client_id', 'id');
	}
	public function provider(): BelongsTo
	{
		return $this->belongsTo(Subscriber::class, 'provider_id', 'id');
	}
}
