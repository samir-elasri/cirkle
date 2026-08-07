<?php

namespace App\Models;

use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gagnant d'un tirage — informations figées au moment du tirage (Denis/Steve 05.08).
 */
class DrawWinner extends Model
{
	protected $table = 'draw_winners';

	protected bool $bigData = false;

	protected $fillable = [
		'draw_id',
		'subscriber_id',
		'member_number',
		'name',
		'email',
		'position',
	];

	public function draw(): BelongsTo
	{
		return $this->belongsTo(Draw::class);
	}
}
