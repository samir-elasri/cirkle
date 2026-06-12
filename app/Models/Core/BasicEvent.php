<?php

namespace App\Models\Core;

use App\Models\Translations\BasicEventTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Auth;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\BasicEvent
 *
 * @property int $id
 * @property string|null $label
 * @property Carbon|null $start_datetime
 * @property Carbon|null $end_datetime
 * @property string|null $categories
 * @property int $online_register
 * @property string|null $non_sub_price
 * @property string|null $sub_price
 * @property int $applicable_tvq
 * @property int $applicable_tps
 * @property int|null $available_places
 * @property int $reserved_member
 * @property string|null $number
 * @property string|null $street
 * @property string|null $app
 * @property string|null $city
 * @property string|null $zip_code
 * @property string|null $email_text
 * @property int $active
 * @property int|null $state_id
 * @property int|null $country_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Attendee> $attendees
 * @property-read int|null $attendees_count
 * @property-read Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @property-read Country|null $country
 * @property-read mixed $class
 * @property-read mixed $collection_name
 * @property-read mixed $coordinates
 * @property-read mixed $cost
 * @property-read mixed $meta_description
 * @property-read mixed $meta_image
 * @property-read mixed $product_name
 * @property-read false|string $product_type
 * @property-read mixed $schedule
 * @property-read SearchResult $search_result
 * @property-read mixed $tps_amount
 * @property-read mixed $tvq_amount
 * @property-read mixed $url
 * @property-read Sharing|null $sharing
 * @property-read State|null $state
 * @property-read BasicEventTranslation|null $translation
 * @property-read Collection<int, BasicEventTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $image
 * @property string|null $legend
 * @property string|null $description
 * @property string|null $email_title
 * @method static Builder|Model active()
 * @method static Builder|BasicEvent getForMonth($year, $month)
 * @method static Builder|BasicEvent listsTranslations(string $translationField)
 * @method static Builder|BasicEvent newModelQuery()
 * @method static Builder|BasicEvent newQuery()
 * @method static Builder|BasicEvent notTranslatedIn(?string $locale = null)
 * @method static Builder|BasicEvent orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BasicEvent orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BasicEvent orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BasicEvent query()
 * @method static Builder|BasicEvent translated()
 * @method static Builder|BasicEvent translatedIn(?string $locale = null)
 * @method static Builder|BasicEvent whereActive($value)
 * @method static Builder|BasicEvent whereApp($value)
 * @method static Builder|BasicEvent whereApplicableTps($value)
 * @method static Builder|BasicEvent whereApplicableTvq($value)
 * @method static Builder|BasicEvent whereAvailablePlaces($value)
 * @method static Builder|BasicEvent whereCategories($value)
 * @method static Builder|BasicEvent whereCity($value)
 * @method static Builder|BasicEvent whereCountryId($value)
 * @method static Builder|BasicEvent whereCreatedAt($value)
 * @method static Builder|BasicEvent whereEmailText($value)
 * @method static Builder|BasicEvent whereEndDatetime($value)
 * @method static Builder|BasicEvent whereId($value)
 * @method static Builder|BasicEvent whereLabel($value)
 * @method static Builder|BasicEvent whereNonSubPrice($value)
 * @method static Builder|BasicEvent whereNumber($value)
 * @method static Builder|BasicEvent whereOnlineRegister($value)
 * @method static Builder|BasicEvent whereReservedMember($value)
 * @method static Builder|BasicEvent whereStartDatetime($value)
 * @method static Builder|BasicEvent whereStateId($value)
 * @method static Builder|BasicEvent whereStreet($value)
 * @method static Builder|BasicEvent whereSubPrice($value)
 * @method static Builder|BasicEvent whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BasicEvent whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BasicEvent whereUpdatedAt($value)
 * @method static Builder|BasicEvent whereZipCode($value)
 * @method static Builder|BasicEvent withTranslation()
 * @mixin Eloquent
 */
class BasicEvent extends Model implements TranslatableContract
{
	use Translatable;
	use ProductTrait;

	public string $singular = 'une date pour cet évènement';
	public $relatedGrid = 'dates pour cet évènement';
	protected array $rules = [
		'start_datetime' => 'required',
		'end_datetime'   => 'required',
	];

	protected $fillable = [
		'label',
		'title',
		'categories',
		'description',
		'online_register',
		'start_datetime',
		'end_datetime',
		'non_sub_price',
		'sub_price',
		'applicable_tvq',
		'applicable_tps',
		'available_places',
		'image',
		'legend',

		'@ Adresse',
		'number',
		'street',
		'app',
		'city',
		'state_id',
		'country_id',
		'zip_code',

		'@ Post achat',
		'email_title',
		'email_text',

		'@ Réservé aux membres',
		'reserved_member',
		'active'
	];
	public $translatedAttributes = [
		'title',
		'image',
		'legend',
		'description',
		'email_title',
		'email_text',
	];
	protected array $grid = [
		'label',
		'start_datetime',
		'end_datetime',
		'active',
	];
	protected array $niceNames = [
		'email_title'      => 'Titre de l\'email',
		'email_text'       => 'Contenu de l\'email',
		'number'           => 'Numéro',
		'street'           => 'Rue',
		'app'              => 'Appartement',
		'city'             => 'Ville',
		'state_id'         => 'Province',
		'country_id'       => 'Pays',
		'zip_code'         => 'Code postal',
		'reserved_member'  => 'Réservé aux membres',
		'label'            => 'Titre interne',
		'title'            => 'Titre',
		'start_datetime'   => 'Date/heure de début',
		'end_datetime'     => 'Date/heure de fin',
		'description'      => 'Résumé',
		'image'            => 'Image',
		'legend'           => 'Légende',
		'categories'       => 'Catégories d\'événements',
		'online_register'  => 'Inscriptions en ligne ',
		'non_sub_price'    => 'Coût non-membre ',
		'sub_price'        => 'Coût membre',
		'applicable_tvq'   => 'TVQ applicable',
		'applicable_tps'   => 'TPS applicable',
		'available_places' => 'Nombre de places disponibles',
	];
	protected array $customFields = [
		'email_text'  => ['widget' => 'wysiwyg'],
		'description' => ['widget' => 'wysiwyg'],
		'categories'  => [
			'widget'  => 'associate_categories',
			'options' => [
				'identifier' => 'events',
				'table'      => 'associate_events_categories',
			],
		],
	];
	protected $productDetails = [
		'coordinates',
		'schedule',
	];


	protected function getCoordinatesAttribute()
	{
		$address = '<div>';
		$address .= $this->number ? $this->number . ' ' : '';
		$address .= $this->street ? $this->street . ', ' : '';
		$address .= $this->app ?? '';
		if ($this->number || $this->street || $this->app) {
			$address .= '<br>';
		}
		$address .= $this->city ? $this->city . ', ' : '';
		$address .= $this->state ? $this->state->title . ', ' : '';
		$address .= $this->country->title ?? '';
		if ($this->state || $this->city || $this->country) {
			$address .= '<br>';
		}
		$address .= $this->zip_code ?? '';
		$address .= '</div>';
		return $address;
	}

	protected function getScheduleAttribute()
	{
		if ($this->isSameDayEvent()) {
			return '<div>' . prettyDate($this->getDay('start')) . '<br>' . __('main.events.from-to.time',
					['start' => $this->getTime('start'), 'end' => $this->getTime('end')]) . '</div>';
		}

		return '<div>' . __('main.events.from-to.day', [
				'start' => prettyDate($this->getDay('start')), 'end' => prettyDate($this->getDay('end'))
			]) . '<br>' . __('main.events.from-to.time',
				['start' => $this->getTime('start'), 'end' => $this->getTime('end')]) . '</div>';
	}

	protected function getMetaDescriptionAttribute()
	{
		return $this->resume;
	}

	protected function getMetaImageAttribute()
	{
		return $this->image;
	}

	public function getUrlAttribute()
	{
		return urlRouteName('basic-event', ['id' => $this->id, 'slug' => slug($this->title)]);
	}

	public static function getLatest($number = 3)
	{
		return static::where('event_date', '>=', date('Y-m-d'))->get()->take($number);
	}

	public function getDates()
	{
		return ['created_at', 'updated_at', 'start_datetime', 'end_datetime'];
	}

	/**
	 * @return MorphMany|Bloc[]|Bloc
	 */
	public function blocs()
	{
		return $this->morphMany(Bloc::class, 'pageable');
	}

	/**
	 * @return MorphOne|Sharing
	 */
	public function sharing()
	{
		return $this->morphOne(Sharing::class, 'shareable');
	}

	/**
	 * @return HasMany|Attendee[]|Attendee
	 */
	public function attendees()
	{
		return $this->hasMany(Attendee::class);
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

	public function scopeGetForMonth($query, $year, $month)
	{
		/** @var Builder $query */
		return $query->where(DB::raw('YEAR(start_datetime)'), '=', $year)
			->where(DB::raw('MONTH(start_datetime)'), '=', $month);
	}

	private function isSameDayEvent()
	{
		$start_date = date('Y-m-d', strtotime($this->start_datetime));
		$end_date = date('Y-m-d', strtotime($this->end_datetime));
		return $start_date === $end_date;
	}

	private function getTime($startOrEnd = 'start')
	{
		$startOrEnd .= '_datetime';
		return date('H:i', strtotime($this->$startOrEnd));
	}

	public function getDay($startOrEnd = 'start')
	{
		$startOrEnd .= '_datetime';
		return date('Y-m-d', strtotime($this->$startOrEnd));
	}

	protected function getCostAttribute()
	{
		$subscriber = null;
		if (logged_in()) {
			/** @var Subscriber $subscriber */
			$subscriber = Auth::guard('subscribers')->user();
		}
		if (($subscriber !== null && $subscriber->hasActiveSubscription()) || $this->hasSubscriptionInCart()) {
			return $this->sub_price;
		}
		return $this->non_sub_price;
	}

	public function hasSubscriptionInCart()
	{
		return (new BasicCart)->compareEventStartDatetimeWithSubscriptionPeriod($this);
	}

    public function getProductDescriptionAttribute() {
        return 'todo';
    }
}
