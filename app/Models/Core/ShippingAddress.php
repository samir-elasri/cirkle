<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\ShippingAddress
 *
 * @property int $id
 * @property int $same_invoice
 * @property string|null $number
 * @property string|null $street
 * @property string|null $app
 * @property string|null $city
 * @property string|null $zip_code
 * @property int|null $subscriber_id
 * @property int|null $state_id
 * @property int|null $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Country|null $country
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read State|null $state
 * @property-read Subscriber|null $subscriber
 * @method static Builder|Model active()
 * @method static Builder|ShippingAddress newModelQuery()
 * @method static Builder|ShippingAddress newQuery()
 * @method static Builder|ShippingAddress query()
 * @method static Builder|ShippingAddress whereApp($value)
 * @method static Builder|ShippingAddress whereCity($value)
 * @method static Builder|ShippingAddress whereCountryId($value)
 * @method static Builder|ShippingAddress whereCreatedAt($value)
 * @method static Builder|ShippingAddress whereId($value)
 * @method static Builder|ShippingAddress whereNumber($value)
 * @method static Builder|ShippingAddress whereSameInvoice($value)
 * @method static Builder|ShippingAddress whereStateId($value)
 * @method static Builder|ShippingAddress whereStreet($value)
 * @method static Builder|ShippingAddress whereSubscriberId($value)
 * @method static Builder|ShippingAddress whereUpdatedAt($value)
 * @method static Builder|ShippingAddress whereZipCode($value)
 * @mixin Eloquent
 */
class ShippingAddress extends Model
{

	protected $fillable = [
		'subscriber_id',
		'same_invoice',
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
		'same_invoice' => 'Même qu’adresse de facturation?',
		'number' => 'Numéro',
		'street' => 'Rue',
		'app' => 'Appartement',
		'city' => 'Ville/municipalité',
		'state_id' => 'Province',
		'country_id' => 'Pays',
		'zip_code' => 'Code postal',
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
		static::saving(function ($model) {
			if ($model->same_invoice && $invoice = $model->subscriber->invoiceAddress()->first()) {
				$model->number = $invoice->number;
				$model->street = $invoice->street;
				$model->app = $invoice->app;
				$model->city = $invoice->city;
				$model->state_id = $invoice->state_id;
				$model->country_id = $invoice->country_id;
				$model->zip_code = $invoice->zip_code;
			}
		});
	}
}
