<?php

namespace App\Models\Core;

use App\Models\SubscriptionPrice;
use App\Models\Translations\SubscriptionTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model implements TranslatableContract
{

	use Translatable;
	use ProductTrait;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	protected $fillable = [
		'identifier',
		'title',
		'description',
		'duration',
		//		'date_type',
		'type',
		'date',
		'prorate_costs',
		'applicable_tvq',
		'applicable_tps',
		//		'cost',
		//		'corpo',
		'buyable_online',
		'max_postal_codes',
		'active',
		'is_recommended',
	];

	public $translatedAttributes = [
		'title',
		'description'
	];

	protected array $grid = [
        'title',
        'type',
        'duration',
        'date',
        'is_recommended',
        'active'
    ];

	protected array $niceNames = [
		'corpo'            => 'Abonnement corporatif',
		'identifier'       => 'Titre interne',
		'title'            => 'Titre',
		'applicable_tvq'   => 'TVQ applicable',
		'applicable_tps'   => 'TPS applicable',
		'duration'         => 'Durée (toujours en mois)',
		'date_type'        => 'Type',
		'date'             => 'Jour/mois',
		'prorate_costs'    => 'Prorata des coûts ',
		'buyable_online'   => 'Achetable en ligne ',
		'max_postal_codes' => 'Nombre de code postaux max inclus',
		'cost'             => 'Coûts',
		'description'      => 'Text',
		'is_recommended'   => 'Recommandé',
	];

	protected array $rules = [
		'title' => 'required'
	];

	protected array $enum = [
		'date_type' => [
			'today' => 'Date du jour',
			'fix'   => 'Date fixe'
		],
		'type'      => [
			'cities'  => 'Ville',
			'state'   => 'Province',
			'country' => 'Country',
		]
	];

	protected array $toggleFields = [
		'prorate_costs' => "form.date_type.value == 'fix'",
		'date'          => "form.date_type.value == 'fix'",
	];

	protected $productDetails = [
		'duration',
	];

	/**
	 * @return HasMany|Reminder[]|Reminder
	 */
	public function reminders()
	{
		return $this->hasMany(Reminder::class);
	}

	public function getEndDatetimeAttribute()
	{
		return Carbon::now()->add($this->duration, 'month');
	}

	public function getStartDatetimeAttribute()
	{
		return Carbon::now();
	}

	public function subscriptionPrices()
	{
		return $this->hasMany(SubscriptionPrice::class);
	}

    public function getCostAttribute() {
        // Prix résolu à l'ajout au panier selon la catégorie + la zone choisie
        // (code postal ou province) — voir SubscriberController::storeStep6. Sans
        // résolution, repli sur le premier prix (compat. historique).
        if (isset($this->attributes['cost']) && $this->attributes['cost'] !== null) {
            return (float) $this->attributes['cost'];
        }
        return $this->subscriptionPrices?->first()?->cost ?? 0;
    }

    public function getProductDescriptionAttribute()
    {
        return $this->description;
    }
}
