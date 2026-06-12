<?php

/** @noinspection OneTimeUseVariablesInspection */

namespace App\Models\Core;

use App\Models\ContactedProvider;
use App\Models\Core\UserBase as Authenticatable;
use App\Models\Evaluation;
use App\Models\JobOffer;
use App\Models\License;
use App\Models\LikedSubscriber;
use App\Models\PostalCode;
use App\Models\Promotion;
use App\Models\PurchasedSubRecord;
use App\Models\SavedSearch;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubscriberImage;
use App\Models\SubscriberService;
use App\Models\SubscriberServiceCategory;
use Arr;
use Carbon\Carbon;
use Database\Factories\Core\SubscriberFactory;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Writer;
use SplTempFileObject;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

/**
 * App\Models\Core\Subscriber
 *
 * @property int $id
 * @property int|null $member_number
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $login_datetime
 * @property string|null $email
 * @property string|null $password
 * @property string|null $preference_language
 * @property string|null $remember_token
 * @property string|null $recover_token
 * @property int $accept_condition
 * @property int $email_validated
 * @property int $active
 * @property string|null $api_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read string|null $formatted_member_number
 * @property-read string $name
 * @property-read mixed $preference_language_name
 * @property-read SearchResult $search_result
 * @property-read InvoiceAddress|null $invoiceAddress
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @property-read Collection<int, PurchasedSub> $purchasedSubs
 * @property-read int|null $purchased_subs_count
 * @property-read ShippingAddress|null $shippingAddress
 * @method static Builder|Model active()
 * @method static SubscriberFactory factory($count = null, $state = [])
 * @method static Builder|Subscriber newModelQuery()
 * @method static Builder|Subscriber newQuery()
 * @method static Builder|Subscriber query()
 * @method static Builder|Subscriber whereAcceptCondition($value)
 * @method static Builder|Subscriber whereActive($value)
 * @method static Builder|Subscriber whereApiToken($value)
 * @method static Builder|Subscriber whereCreatedAt($value)
 * @method static Builder|Subscriber whereEmail($value)
 * @method static Builder|Subscriber whereEmailValidated($value)
 * @method static Builder|Subscriber whereFirstName($value)
 * @method static Builder|Subscriber whereId($value)
 * @method static Builder|Subscriber whereLastName($value)
 * @method static Builder|Subscriber whereLoginDatetime($value)
 * @method static Builder|Subscriber wherePassword($value)
 * @method static Builder|Subscriber wherePreferenceLanguage($value)
 * @method static Builder|Subscriber whereRecoverToken($value)
 * @method static Builder|Subscriber whereRememberToken($value)
 * @method static Builder|Subscriber whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Subscriber extends Authenticatable implements TranslatableContract
{
	use Notifiable, HasFactory, Translatable;

	/**
	 * Premier numéro de la séquence de membres partagée clients/fournisseurs
	 * (C02350, F02351, C02352, ...). La lettre est dérivée de is_provider à l'affichage.
	 */
	public const MEMBER_NUMBER_START = 2350;

    public $searchFields = ['serviceCategory', 'serviceCategories', 'services', 'main_description'];

	protected $fillable = [
		'login_datetime',
		'email',
		'last_name',
		'first_name',
		'preference_language',

		'@Adresse',
		'number',
		'street',
		'app',
		'city',
		'state_id',
		'country_id',
		'postal_code',

		'phone',
		'toll_free_phone',
		'password',
		// 'website_subscriber',
		'accept_condition',
		'email_validated',
		'is_provider',

		'@En tant que fournisseur',
		'provider_type',
		'service_category_id',
		'profile_image',
		'company_name',
		'legal_form_id',
		'federal_tax_number',
		'fax',
		'start_date',
		'insurance_coverage',
		'business_hours',
		'main_description',
		'other_service_descriptions',
        'served_country',
        'served_state',
		'is_public',

		'@Option de profil Website URL (connection)',
		'url',
		'profile_url_activation_datetime',
		'end_date',

		'profile_license_active',
		'profile_license_activation_datetime',

		'profile_promotion_active',
		'profile_promotion_activation_datetime',

		'profile_image_active',
		'profile_image_activation_datetime',

		'profile_estimation_active',
		'profile_estimation_activation_datetime',

		'profile_job_offer_active',
		'profile_job_offer_activation_datetime',

		'@Section estimation',
		'estimation_cost',
		'accepts_cash',
		'accepts_check',
		'accepts_debit',
		'accepts_credit',

		'registration_completed',
		'active',
	];

	protected $translatedAttributes = [
		'other_service_descriptions',
		'url'
	];

	protected bool $bigData = true;

	protected array $enum = [
		'provider_type' => [
			'residential' => 'Résidentiel',
			'business'    => 'Business',
			'both'        => 'Les 2'
		]
	];

	protected array $grid = [
		'id',
		'member_number',
		'name',
		'company_name',
		'email',
		'is_provider',
		'active'
	];

	protected array $gridFields = [
		'name' => 'CONCAT(subscribers.first_name, " ", subscribers.last_name)',
	];

	public static $auth_rules = [
		'email'    => 'required|email',
		'password' => 'required'
	];

	protected array $rules = [
		'email' => 'required|email|unique:subscribers,email,{id},id',
	];

	protected array $niceNames = [
		'member_number'                             => 'Numéro de membre',
		'email'                                     => 'Courriel',
		'first_name'                                => 'Prénom',
		'last_name'                                 => 'Nom',
		'created_at'                                => 'Date/heure de création',
		'login_datetime'                            => 'Date/heure du plus récent login',
		'preference_language'                       => 'Langue de préférence',
		'accept_condition'                          => 'Accepte les conditions ',
		'email_validated'                           => 'Courriel validé ',
		'active'                                    => 'Actif',
		'number'                                    => 'Numéro',
		'street'                                    => 'Rue',
		'app'                                       => 'Bureau',
		'city'                                      => 'Ville',
		'state'                                     => 'Province',
		'state_id'                                  => 'Province',
		'postal_code'                               => 'Code postal',
		'country'                                   => 'Pays',
		'country_id'                                => 'Pays',
		'phone'                                     => 'Tél',
		'website_subscriber'                        => 'Inscrit au site',
		'is_provider'                               => 'Fournisseur',
		'other_service_descriptions'                => 'Description autres services',
		'url'                                       => 'URL',
		'profile_url_activation_datetime'           => 'Date heure d\'activation',
		'end_date'                                  => 'Date de fin d\'affichage',
		'profile_license_active'                    => 'Option de profil Permis activé',
		'profile_promotion_active'              => 'Option de profil promotions activé',
		'profile_image_active'                      => 'Option de profil photos activé',
		'profile_estimation_active'                 => 'Option de profil Estimation activé',
		'profile_job_offer_active'                  => 'Option de profil offres d’emploi activé',
		'profile_license_activation_datetime'       => 'Date heure d\'activation',
		'profile_promotion_activation_datetime' => 'Date heure d\'activation',
		'profile_image_activation_datetime'         => 'Date heure d\'activation',
		'profile_estimation_activation_datetime'    => 'Date heure d\'activation',
		'profile_job_offer_activation_datetime'     => 'Date heure d\'activation',
		'accepts_cash'                              => 'Accepte cash',
		'accepts_check'                             => 'Accepte Cheque',
		'accepts_debit'                             => 'Accepte Debit',
		'accepts_credit'                            => 'Accept credit card',
		'estimation_cost'                           => 'Coût de l’estimé',
		'is_public'                                 => 'Fiche publique visible',
		'service_category_id'                       => 'Catégorie de service associé',
		'company_name'                              => 'Nom de la compagnie',
		'profile_image'                             => 'Photo de profil',
		'main_description'                          => 'Description principale',
		'provider_type'                             => 'Type de fournisseur',
        'served_country' => 'Pays desservi',
        'served_state' => 'Province desservie',
		'registration_completed' => 'Inscription complétée et payée'
	];

	protected $appends = ['name'];

	protected array $customFields = [];

	protected array $toggleFields = [
		'profile_license_activation_datetime'       => 'form.profile_license_active[1].checked',
		'profile_promotion_activation_datetime' => 'form.profile_promotion_active[1].checked',
		'profile_image_activation_datetime'         => 'form.profile_image_active[1].checked',
		'profile_estimation_activation_datetime'    => 'form.profile_estimation_active[1].checked',
		'profile_job_offer_activation_datetime'     => 'form.profile_job_offer_active[1].checked',
	];

	protected $exports = [
		'generic' => array(
			'label'  => 'Exporter la liste',
			'method' => 'exportFile'
		)
	];

	public static function boot()
	{
		parent::boot();

		// Assigne le prochain numéro de la séquence de membres partagée (clients et fournisseurs).
		// DB::table contourne les global scopes : le max doit couvrir tous les membres, même inactifs.
		// L'index unique sur member_number sert de garde-fou en cas de création simultanée.
		static::creating(static function ($subscriber) {
			if (empty($subscriber->member_number)) {
				$max = DB::table($subscriber->getTable())->max('member_number');
				$subscriber->member_number = max((int)$max + 1, self::MEMBER_NUMBER_START);
			}
		});
	}

	/**
	 * Numéro de membre affichable : C02350 pour un client, F02351 pour un fournisseur.
	 *
	 * @return string|null
	 */
	public function getFormattedMemberNumberAttribute(): ?string
	{
		if (!$this->member_number) {
			return null;
		}

		return sprintf('%s%05d', $this->is_provider ? 'F' : 'C', $this->member_number);
	}

	/**
	 * @return string
	 */
	public function getNameAttribute(): string
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public static function getCurrentSubName($id)
	{
		$title = null;

		/** @var PurchasedSub|null $purchased */
		$purchased = PurchasedSub::where('subscriber_id', $id)->where(
			'start_date',
			'<',
			Carbon::now()
		)->where('end_date', '>', Carbon::now())->first();

		if ($purchased) {
			/** @var Subscription $sub */
			$sub = Subscription::find($purchased->subscription_id);
			$title = $sub->title ?? null;
		}

		return $title ?? 'Aucun';
	}

	public function getPreferenceLanguageNameAttribute()
	{
		return $this->preference_language ? Arr::get($this->enum, "preference_language.{$this->preference_language}") : null;
	}

	/**
	 * @return HasOne|InvoiceAddress
	 */
	public function invoiceAddress()
	{
		return $this->hasOne(InvoiceAddress::class);
	}

	/**
	 * @return HasOne|ShippingAddress
	 */
	public function shippingAddress()
	{
		return $this->hasOne(ShippingAddress::class);
	}

	/**
	 * @return HasMany|PurchasedSub[]|PurchasedSub
	 */
	public function purchasedSubs()
	{
		return $this->hasMany(PurchasedSub::class);
	}

	/**
	 * @return HasMany| Order[] | Order
	 */
	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	/**
	 * @return bool
	 */
	public function hasActiveSubscription(): bool
	{
		return ($this->purchasedSubs()
				->where('active', true)
				->where('on_pause', false)
				->where('end_date', '>=', now())
				->where('start_date', '<=', now())
				->first() !== null); // return bool
	}

    public function activeSubscription(): HasOne {
        return $this->purchasedSubs()
            ->where('active', true)
            ->where('on_pause', false)
            ->where('end_date', '>=', now())
            ->where('start_date', '<=', now())
            ->one();
    }

	/**
	 * @return bool
	 */
	public function hasPausedSubscription(): bool
	{
		return ($this->purchasedSubs()
				->where('active', true)
				->where('on_pause', true)
				->where('end_date', '>=', now())
				->where('start_date', '<=', now())
				->first() !== null); // return bool
	}

	/**
	 * @return bool
	 */
	public function hasPreviousSubscription(): bool
	{
		return ($this->purchasedSubs()
				->where('active', true)
				->where('on_pause', false)
				->where('end_date', '<=', now())
				->first() !== null); // return bool
	}


	public function getSubscriptionTagAttribute()
	{
		$result = '';
		if ($this->activeSubscription) {
			$result = "<span>" . trans('subscription.subscription-details', ['date' => prettyDate($this->activeSubscription->end_date)]) . "</span>";
		} else if ($this->purchasedSubs()
			->where('active', true)
			->where('end_date', '<=', now())->first()) {
			$url = urlRouteName('subscriptions');
			$result = "<h4>" . trans('subscription.no-active-subscription') . "</h4>" . "<br><a href='$url' class='call-to-action'>" . trans('subscription.no-active-subscription-renew') . "</a>";
		} else {
			$url = urlRouteName('subscriptions');
			$result = "<h4>" . trans('subscription.no-active-subscription') . "</h4>" . "<br><a href='$url' class='call-to-action'>" . trans('subscription.no-active-subscription-subscribe') . "</a>";
		}
		return $result;
	}

	/**
	 * Return an currently valid and active subscription with the latest end date
	 * return null otherwise
	 *
	 * @return PurchasedSub|\Illuminate\Database\Eloquent\Model|HasMany|object|null
	 */
	public function getActiveSubscription()
	{
		return $this->purchasedSubs()
			->where('active', true)
			->where('on_pause', false)
			->where('end_date', '>=', now())
			->where('start_date', '<=', now())
			->orderBy('end_date', 'DESC')
			->first(); // return object
	}

	public function getLatestSubscription()
	{
		return $this->purchasedSubs()->where('active', true)
			->where('end_date', '>=', now())
			->where('start_date', '<=', now())
			->orderBy('end_date', 'DESC')
			->first(); // return object
	}



	public function getPausedSubscription()
	{
		return $this->purchasedSubs()
				->where('active', true)
				->where('on_pause', true)
				->first(); // return bool
	}

	/**
	 * @throws CannotInsertRecord
	 * @throws Exception
	 */
	public function exportFile(): void
	{

		set_time_limit(0);

		$entities = static::all();
		$csv = Writer::createFromFileObject(new SplTempFileObject());
		if ($entities->count()) {

			$csv->setDelimiter(';');
			$csv->setOutputBOM(Writer::BOM_UTF8);

			$header = [
				'created_at',
				'login_datetime',
				'email',
				'first_name',
				'last_name',
				'preference_language',
				'accept_condition',
				'email_validated',
				'active'
			];
			foreach ($header as $index => $value) {
				$header[$index] = $this->niceNames[$value];
			}

			$header[] = 'Statut d’abonnement actuel';

			$csv->insertOne($header);
			foreach ($entities as $entity) {
				$csv->insertOne([
					prettyDate($entity->created_at),
					prettyDate($entity->login_datetime),
					$entity->email,
					$entity->first_name,
					$entity->last_name,
					$entity->preference_language,
					$entity->accept_condition ? 'Oui' : 'Non',
					$entity->email_validated ? 'Oui' : 'Non',
					$entity->active ? 'Oui' : 'Non',
					static::getCurrentSubName($entity->id),
				]);
			}
		}
		$csv->output('Inscrits.csv');
		exit;
	}

	/**
	 * @return BelongsTo|ServiceCategory[]|ServiceCategory
	 */
	public function serviceCategory(): BelongsTo
	{
		return $this->belongsTo(ServiceCategory::class);
	}

	/**
	 * @return HasMany|PurchasedSubRecord[]|PurchasedSubRecord
	 */
	public function purchasedSubRecords(): HasMany
	{
		return $this->hasMany(PurchasedSubRecord::class);
	}

	/**
	 * @return HasMany|JobOffer[]|JobOffer
	 */
	public function jobOffers(): HasMany
	{
		return $this->hasMany(JobOffer::class);
	}

	/**
	 * @return HasMany|SubscriberImage[]|SubscriberImage
	 */
	public function subscriberImages(): HasMany
	{
		return $this->hasMany(SubscriberImage::class);
	}

	/**
	 * @return HasMany|Promotion[]|Promotion
	 */
	public function promotions(): HasMany
	{
		return $this->hasMany(Promotion::class);
	}

	/**
	 * @return HasMany|License[]|License
	 */
	public function licenses(): HasMany
	{
		return $this->hasMany(License::class);
	}

	public function likedSubscribers(): HasMany
	{
		return $this->hasMany(LikedSubscriber::class);
	}

    public function likedBySubscribers(): HasMany {
        return $this->hasMany(LikedSubscriber::class, 'liked_subscriber_id');
    }

    public function likedByLoggedInUser(): HasOne {
        return $this->likedBySubscribers()
            ->where('subscriber_id', '=', auth('subscribers')->user()?->id)
            ->one();
    }

    public function likedSubscriberList()
	{
		return $this->belongsToMany(Subscriber::class, LikedSubscriber::class);
	}

	public function postalCodes()
	{
		return $this->hasMany(PostalCode::class);
	}

	public function services()
	{
		return $this->belongsToMany(Service::class, SubscriberService::class);
	}

	public function subscriberServices()
	{
		return $this->hasMany(SubscriberService::class);
	}

	public function serviceCategories()
	{
		return $this->belongsToMany(ServiceCategory::class, SubscriberServiceCategory::class);
	}

	public function subscriberServiceCategories()
	{
		return $this->hasMany(SubscriberServiceCategory::class);
	}

	public function contactedProviders()
	{
		return $this->hasMany(ContactedProvider::class, 'client_id', 'id');
	}

	public function savedSearches()
	{
		return $this->hasMany(SavedSearch::class);
	}

	public function state()
	{
		return $this->belongsTo(State::class);
	}

	public function country()
	{
		return $this->belongsTo(Country::class);
	}

	public function givenEvaluations()
	{
		return $this->hasMany(Evaluation::class, 'client_id', 'id');
	}

	public function receivedEvaluations()
	{
		return $this->hasMany(Evaluation::class, 'provider_id', 'id');
	}

	public function getEvaluationsAverageAttribute()
	{
		return $this->receivedEvaluations()->avg('global_grade');
	}

    public static function ProviderSearch($providerType, $mainCategory, $subcategories,  $serviceIds, $formattedPostalCode)
    {
        $subscribersQuery = DB::table('subscribers')
            ->leftJoin('purchased_subs', 'subscribers.id', '=', 'purchased_subs.subscriber_id')
            ->leftJoin('subscriptions', 'purchased_subs.subscription_id', '=', 'subscriptions.id')
            ->leftJoin('postal_codes', 'postal_codes.subscriber_id', '=', 'subscribers.id')
            ->where('subscribers.active', '=', true)
            ->where('subscribers.is_provider', '=', true)
            ->where('purchased_subs.active', '=', true)
            ->where('purchased_subs.on_pause', '=', false)
            ->where('purchased_subs.end_date', '>=', now())
            ->where('purchased_subs.start_date', '<=', now())
            ->select([
                'subscribers.*', 'subscriptions.type as subscription_type'
            ])
            ->groupBy('subscribers.id');

        if (!empty($providerType)) {
            $subscribersQuery = $subscribersQuery->where('subscribers.provider_type', '=', $providerType);
        }

        if ($mainCategory) {
            $subscribersQuery = $subscribersQuery->whereIn('subscribers.service_category_id', $mainCategory);
        }

        if ($subcategories && !$subcategories->isEmpty()) {
            $subscribersQuery = $subscribersQuery->leftJoin('subscriber_service_categories', 'subscribers.id', '=', 'subscriber_service_categories.subscriber_id')
                ->whereIn('subscriber_service_categories.service_category_id', $subcategories);
        }

        if ($serviceIds && !$serviceIds->isEmpty()) {
            $subscribersQuery = $subscribersQuery->leftJoin('subscriber_services', 'subscribers.id', '=', 'subscriber_services.subscriber_id')
                ->whereIn('subscriber_services.service_id', $serviceIds);
        }

        if (empty($formattedPostalCode)) {
            return $subscribersQuery;
        }

        $explodedPostalCode = explode(', ', $formattedPostalCode);
        $postalCode = $explodedPostalCode[0];
        $state = $explodedPostalCode[1];
        $country = $explodedPostalCode[2];

        return DB::query()->from($subscribersQuery, 'subscribers')
            ->select('subscribers.id')
            ->leftJoin('postal_codes', 'postal_codes.subscriber_id', '=', 'subscribers.id')
            ->groupBy('subscribers.id')
            ->where(function($subquery) use ($country) {
                $subquery->where('subscription_type', '=', 'country')
                    ->where('served_country', '=', $country);
            })
            ->orWhere(function($subquery) use ($state) {
                $subquery->where('subscription_type', '=', 'state')
                    ->where('served_state', '=', $state);
            })
            ->orWhere(function($subquery) use ($postalCode) {
                $subquery->where('subscription_type', '=', 'cities')
                    ->where('postal_codes.postal_code', 'like', $postalCode.'%');
            })
        ;
    }

    public function getSearchResultAttribute(): SearchResult
    {
        $result = new SearchResult();
        $result->label = $this->company_name;
        $result->url = urlRouteName('provider', ['id' => $this->id]);

        return $result;
    }

    public static function getListForSearch()
    {
        return static::where('active', '=', true)
            ->where('is_provider', '=', true)
            ->has('activeSubscription')
            ->get();
    }

	public function legalForm() {
		return $this->belongsTo(Category::class, 'legal_form_id');
	}
}
