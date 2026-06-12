<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\SentReminder
 *
 * @property int $id
 * @property int|null $purchased_sub_id
 * @property int|null $reminder_id
 * @property string|null $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read PurchasedSub|null $purchasedSub
 * @property-read Reminder|null $reminder
 * @method static Builder|Model active()
 * @method static Builder|SentReminder newModelQuery()
 * @method static Builder|SentReminder newQuery()
 * @method static Builder|SentReminder query()
 * @method static Builder|SentReminder whereCreatedAt($value)
 * @method static Builder|SentReminder whereDate($value)
 * @method static Builder|SentReminder whereId($value)
 * @method static Builder|SentReminder wherePurchasedSubId($value)
 * @method static Builder|SentReminder whereReminderId($value)
 * @method static Builder|SentReminder whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SentReminder extends Model
{
	protected bool $bigData = true;

	protected $fillable = [
		'purchased_sub_id',
		'reminder_id',
		'date',
	];

	protected array $toggleFields = [];

	protected array $grid = [
		'date',
		'reminder.identifier'
	];

	protected array $rules = [];

	protected array $niceNames = [
		'date'                => 'Date',
		'reminder_id'         => 'Courriel de rappel',
		'reminder.identifier' => 'Courriel de rappel',
	];

	/**
	 * @return BelongsTo|PurchasedSub
	 */
	public function purchasedSub()
	{
		return $this->belongsTo(PurchasedSub::class);
	}

	/**
	 * @return BelongsTo|Reminder
	 */
	public function reminder()
	{
		return $this->belongsTo(Reminder::class);
	}
}
