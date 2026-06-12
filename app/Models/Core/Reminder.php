<?php

namespace App\Models\Core;

use App\Models\Translations\ReminderTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Reminder
 *
 * @property int $id
 * @property int|null $subscription_id
 * @property string|null $identifier
 * @property int|null $days
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Subscription|null $subscription
 * @property-read ReminderTranslation|null $translation
 * @property-read Collection<int, ReminderTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $email_title
 * @property string|null $email_content
 * @method static Builder|Model active()
 * @method static Builder|Reminder listsTranslations(string $translationField)
 * @method static Builder|Reminder newModelQuery()
 * @method static Builder|Reminder newQuery()
 * @method static Builder|Reminder notTranslatedIn(?string $locale = null)
 * @method static Builder|Reminder orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Reminder orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Reminder orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Reminder query()
 * @method static Builder|Reminder translated()
 * @method static Builder|Reminder translatedIn(?string $locale = null)
 * @method static Builder|Reminder whereActive($value)
 * @method static Builder|Reminder whereCreatedAt($value)
 * @method static Builder|Reminder whereDays($value)
 * @method static Builder|Reminder whereId($value)
 * @method static Builder|Reminder whereIdentifier($value)
 * @method static Builder|Reminder whereSubscriptionId($value)
 * @method static Builder|Reminder whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Reminder whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Reminder whereUpdatedAt($value)
 * @method static Builder|Reminder withTranslation()
 * @mixin Eloquent
 */
class Reminder extends Model implements TranslatableContract
{

	use Translatable;

	protected bool $bigData = true;

	protected $fillable = [
		'identifier',

		'subscription_id',
		'days',
		'email_title',
		'email_content',

		'active'
	];

	public $translatedAttributes = [
		'email_content',
		'email_title',
	];

	protected array $grid = [
		'identifier',
		'active'
	];

	protected array $niceNames = [
		'identifier'    => 'Titre interne',
		'days'          => 'Entier (jours) relatif à la date de fin ',
		'email_content' => 'Contenu courriel ',
		'email_title'   => 'Titre courriel ',
	];

	protected array $enum = [
		'date_type' => [
			'today' => 'Date du jour',
			'fix'   => 'Date fixe'
		]
	];

	/**
	 * @return BelongsTo|Subscription
	 */
	public function subscription()
	{
		return $this->belongsTo(Subscription::class);
	}
}
