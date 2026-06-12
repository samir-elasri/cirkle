<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use SplTempFileObject;

/**
 * App\Models\Core\PurchasedSub
 *
 * @property int $id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int $active
 * @property int $validated
 * @property string|null $record_date
 * @property string|null $domain
 * @property int|null $subscriber_id
 * @property int|null $subscription_id
 * @property int|null $order_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Order|null $order
 * @property-read Collection<int, SentReminder> $sentReminders
 * @property-read int|null $sent_reminders_count
 * @property-read Subscriber|null $subscriber
 * @property-read Subscription|null $subscription
 * @method static Builder|Model active()
 * @method static Builder|PurchasedSub newModelQuery()
 * @method static Builder|PurchasedSub newQuery()
 * @method static Builder|PurchasedSub query()
 * @method static Builder|PurchasedSub whereActive($value)
 * @method static Builder|PurchasedSub whereCreatedAt($value)
 * @method static Builder|PurchasedSub whereDomain($value)
 * @method static Builder|PurchasedSub whereEndDate($value)
 * @method static Builder|PurchasedSub whereId($value)
 * @method static Builder|PurchasedSub whereOrderId($value)
 * @method static Builder|PurchasedSub whereRecordDate($value)
 * @method static Builder|PurchasedSub whereStartDate($value)
 * @method static Builder|PurchasedSub whereSubscriberId($value)
 * @method static Builder|PurchasedSub whereSubscriptionId($value)
 * @method static Builder|PurchasedSub whereUpdatedAt($value)
 * @method static Builder|PurchasedSub whereValidated($value)
 * @property string|null $pause_start_date
 * @property string|null $pause_end_date
 * @property int $on_pause
 * @method static Builder|PurchasedSub whereOnPause($value)
 * @method static Builder|PurchasedSub wherePauseEndDate($value)
 * @method static Builder|PurchasedSub wherePauseStartDate($value)
 * @mixin Eloquent
 */
class PurchasedSub extends Model
{
	protected bool $bigData = true;

	protected $fillable = [
		'record_date',
		'subscription_id',
		'subscriber_id',
		'order_id',
		'start_date',
		'end_date',
		'pause_start_date',
		'pause_end_date',
		'on_pause',
//		'domain',
//		'validated',
		'active'
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'subscription.title',
		'active'
	];

	protected $appends = [];

	protected array $rules = [];

	protected array $niceNames = [
		'domain'            => 'Domaine associé',
		'record_date'       => 'Date de création de l’enregistrement',
		'subscription.name' => 'Abonnement',
		'subscription_id'   => 'Id de l’abonnement',
		'subscriber_id'     => 'Id de l’inscrit',
		'order_id'          => 'Id de la commande associée',
		'start_date'        => 'Date de début',
		'end_date'          => 'Date de fin',
		'validated'         => 'Validé ',
		'active'            => 'Actif',
		'pause_start_date'  => 'Date de début de pause',
		'pause_end_date'    => 'Date de fin de pause',
		'on_pause'          => 'En pause',
	];

	protected $exports = [
		'generic' => [
			'label'  => 'Exporter la liste',
			'method' => 'exportFile'
		]
	];

	/**
	 * @return HasMany|SentReminder[]|SentReminder
	 */
	public function sentReminders()
	{
		return $this->hasMany(SentReminder::class);
	}

	/**
	 * @return BelongsTo|Subscriber
	 */
	public function subscriber()
	{
		return $this->belongsTo(Subscriber::class);
	}

	/**
	 * @return BelongsTo|Subscription
	 */
	public function subscription()
	{
		return $this->belongsTo(Subscription::class);
	}

	/**
	 * @return BelongsTo|Order
	 */
	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function exportFile($relation = null, $id = null)
	{
		set_time_limit(0);

		if ($relation && $id) {
			$entities = Subscriber::find($id)->purchasedSubs;
		} else {
			$entities = static::all();
		}

		$csv = Writer::createFromFileObject(new SplTempFileObject());
		if ($entities->count()) {

			$csv->setDelimiter(';');
			$csv->setOutputBOM(Writer::BOM_UTF8);

			$header = [
				'record_date',
				'subscription_id',
				'subscriber_id',
				'order_id',
				'start_date',
				'end_date',
				'validated',
				'active'
			];
			foreach ($header as $index => $value) {
				$header[$index] = $this->niceNames[$value];
			}

			$csv->insertOne($header);
			foreach ($entities as $entity) {
				$subscription = Subscription::find($entity->subscription_id);
				$subscriber = Subscriber::find($entity->subscriber_id);
				$title = optional($subscription)->title;
				$name = optional($subscriber)->name;
				$csv->insertOne([
					prettyDate($entity->record_date),
					$title ?? '',
					$name ?? '',
					$entity->order_id,
					prettyDate($entity->start_date),
					prettyDate($entity->end_date),
					$entity->validated ? 'Oui' : 'Non',
					$entity->active ? 'Oui' : 'Non',
				]);
			}
		}
		$csv->output('AbonnementsAchetes.csv');
		exit;
	}

    /**
     * @return bool
     */
    public function isSubgridExportable(): bool
    {
        return false;
    }
}
