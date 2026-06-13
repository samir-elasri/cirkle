<?php

namespace App\Models;

use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Evaluation
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $provider_id
 * @property int|null $client_id
 * @property string|null $global_grade
 * @property string|null $service_quality_grade
 * @property string|null $reliability_grade
 * @property string|null $communication_grade
 * @property string|null $hourly_rate_grade
 * @property string|null $comment
 * @property int|null $insulting
 * @property int|null $validated
 * @property int|null $treated
 * @property int $active
 * @property-read Subscriber|null $client
 * @property-read mixed $collection_name
 * @property-read mixed $has_less_than_two
 * @property-read \App\Models\Core\SearchResult $search_result
 * @property-read Subscriber|null $provider
 * @method static Builder|Model active()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereCommunicationGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereGlobalGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereHourlyRateGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereInsulting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereReliabilityGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereServiceQualityGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereTreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Evaluation whereValidated($value)
 * @mixin \Eloquent
 */
class Evaluation extends Model
{
	use HasFactory;

	public string $order_default = 'treated';
	public string $order_direction = 'asc';

	protected bool $bigData = true;

	protected $fillable = [
		'created_at',
		'provider_id',
		'client_id',
		'global_grade',
		'service_quality_grade',
		'reliability_grade',
		'communication_grade',
		'hourly_rate_grade',
		'comment',
		'reply',
		'reply_approved',
		'reply_created_at',
		'insulting',
		'validated',
		'treated',
		'active',
	];

	public array $positionParentFields = [];

	protected array $grid = [
		'id',
		'provider.name',
		'client.name',
		'global_grade',
		'reply_approved',
		'insulting',
		'validated',
		'treated',
		'active',
	];

	protected array $niceNames = [
		'created_at' => 'Date-heure',
		'provider_id' => 'Fournisseur',
		'client_id' => 'Client',
		'global_grade' => 'Note donnée global',
		'service_quality_grade' => 'Note donnée Qualité service',
		'reliability_grade' => 'Note donnée Ponctualité et fiabilité',
		'communication_grade' => 'Note donnée Communication et réactivité',
		'hourly_rate_grade' => 'Note donnée Taux horaire',
		'comment' => 'Commentaire',
		'reply' => 'Réponse du fournisseur',
		'reply_approved' => 'Réponse approuvée',
		'reply_created_at' => 'Date de la réponse',
		'insulting' => 'Injurieux',
		'validated' => 'Validé',
		'treated' => 'Traité',
		'active' => 'Actifs',
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

	/**
	 * Note façon Google : une seule note (global_grade). Les avis de 1–2 sont
	 * acheminés à Cirkle (spec). Tolère les anciens avis multi-critères.
	 */
	public function getHasLessThanTwoAttribute() {
		$grades = array_filter([
			$this->global_grade,
			$this->service_quality_grade,
			$this->reliability_grade,
			$this->communication_grade,
			$this->hourly_rate_grade,
		], static fn ($g) => $g !== null);

		return !empty($grades) && min($grades) <= 2;
	}
}
