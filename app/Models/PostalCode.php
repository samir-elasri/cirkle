<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PostalCode
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $postal_code
 * @property int|null $subscriber_id
 * @property int $active
 * @property-read mixed $collection_name
 * @property-read \App\Models\Core\SearchResult $search_result
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode whereSubscriberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PostalCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PostalCode extends Model
{
    use HasFactory;

    public string $order_default = 'id';
    public string $order_direction = 'desc';

    protected bool $bigData = true;

    protected $fillable = [
	    'postal_code',
	    'subscriber_id'
    ];

    public array $positionParentFields = [];

    protected array $grid = [
        'id',
	    'postal_code',

    ];

    protected array $niceNames = [
		'postal_code' => 'Code postal'
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
