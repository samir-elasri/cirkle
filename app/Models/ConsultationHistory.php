<?php

namespace App\Models;

use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Historique des consultations : une fiche fournisseur consultée par un client (feature #11).
 * Remplace l'auto-suivi « fournisseur contacté » (ContactedProvider) abandonné.
 *
 * @property int $id
 * @property int|null $subscriber_id
 * @property int|null $viewed_subscriber_id
 * @mixin \Eloquent
 */
class ConsultationHistory extends Model
{
	use HasFactory;

	protected bool $bigData = true;

	protected $fillable = [
		'subscriber_id',
		'viewed_subscriber_id',
	];

	public array $positionParentFields = [];
	protected array $grid = ['id'];
	protected array $niceNames = [];
	protected array $enum = [];
	protected array $customFields = [];

	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(\App\Models\Core\Subscriber::class, 'subscriber_id');
	}

	public function viewedSubscriber(): BelongsTo
	{
		return $this->belongsTo(\App\Models\Core\Subscriber::class, 'viewed_subscriber_id');
	}
}
