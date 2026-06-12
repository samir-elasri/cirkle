<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\InvoiceAddress
 *
 * @property int $id
 * @property int|null $subscriber_id
 * @property int|null $state_id
 * @property int|null $country_id
 * @property string|null $number
 * @property string|null $street
 * @property string|null $app
 * @property string|null $city
 * @property string|null $zip_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Country|null $country
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read State|null $state
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static Builder|InvoiceAddress newModelQuery()
 * @method static Builder|InvoiceAddress newQuery()
 * @method static Builder|InvoiceAddress query()
 * @method static Builder|InvoiceAddress whereApp($value)
 * @method static Builder|InvoiceAddress whereCity($value)
 * @method static Builder|InvoiceAddress whereCountryId($value)
 * @method static Builder|InvoiceAddress whereCreatedAt($value)
 * @method static Builder|InvoiceAddress whereId($value)
 * @method static Builder|InvoiceAddress whereNumber($value)
 * @method static Builder|InvoiceAddress whereStateId($value)
 * @method static Builder|InvoiceAddress whereStreet($value)
 * @method static Builder|InvoiceAddress whereSubscriberId($value)
 * @method static Builder|InvoiceAddress whereUpdatedAt($value)
 * @method static Builder|InvoiceAddress whereZipCode($value)
 * @mixin Eloquent
 */
class InvoiceAddress extends Model
{

	protected $fillable = [
		'subscriber_id',
		'number',
		'street',
		'app',
		'city',
		'state_id',
		'country_id',
		'zip_code',
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'subscriber_grid',
		'number',
		'street',
		'app',
	];

	protected array $rules = [];

	protected array $niceNames = [
		'number'     => 'Numéro',
		'street'     => 'Rue',
		'app'        => 'Appartement',
		'city'       => 'Ville/municipalité',
		'state_id'   => 'Province',
		'country_id' => 'Pays',
		'zip_code'   => 'Code postal',
	];

	protected $appends = [];

	/**
	 * @return BelongsTo|Subscriber
	 */
	public function subscriber()
	{
		return $this->belongsTo(Subscriber::class);
	}

	/**
	 * @return BelongsTo|State
	 */
	public function state()
	{
		return $this->belongsTo(State::class);
	}

	/**
	 * @return BelongsTo|Country
	 */
	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	protected static function boot()
	{
		parent::boot();

		/**
		 * saving - traitement avant la sauvegarde
		 */
		static::saved(function ($model) {
			if ($shipping = $model->subscriber->shippingAddress()->first()) {
				$shipping->save();
			}
		});
	}
}
