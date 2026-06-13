<?php

namespace App\Models;

use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Favori « profession » d'un client (feature #11).
 *
 * @property int $id
 * @property int|null $subscriber_id
 * @property int|null $service_category_id
 * @mixin \Eloquent
 */
class LikedProfession extends Model
{
	use HasFactory;

	protected bool $bigData = true;

	protected $fillable = [
		'subscriber_id',
		'service_category_id',
		'active',
	];

	public array $positionParentFields = [];
	protected array $grid = ['id', 'active'];
	protected array $niceNames = [];
	protected array $enum = [];
	protected array $customFields = [];

	public function subscriber(): BelongsTo
	{
		return $this->belongsTo(\App\Models\Core\Subscriber::class, 'subscriber_id');
	}

	public function serviceCategory(): BelongsTo
	{
		return $this->belongsTo(ServiceCategory::class, 'service_category_id');
	}
}
