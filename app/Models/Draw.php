<?php

namespace App\Models;

use App\Models\Core\Model;
use App\Models\Core\Subscriber;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tirage parmi les membres (Denis/Steve 05.08) : clients, fournisseurs, ou les deux.
 * Les gagnants sont figés dans draw_winners au moment du tirage (numéro de membre,
 * nom, courriel) — l'historique reste exact même si un membre change ensuite.
 */
class Draw extends Model
{
	public const POOL_CLIENTS = 'clients';
	public const POOL_PROVIDERS = 'providers';
	public const POOL_BOTH = 'both';

	protected $table = 'draws';

	protected bool $bigData = false;

	protected $fillable = [
		'title',
		'pool',
		'winners_count',
		'note',
		'drawn_at',
		'eligible_count',
	];

	/** Libellés des bassins de participants. */
	public static function pools(): array
	{
		return [
			self::POOL_CLIENTS   => 'Membres CLIENTS',
			self::POOL_PROVIDERS => 'Membres FOURNISSEURS',
			self::POOL_BOTH      => 'Clients + Fournisseurs',
		];
	}

	public function winners(): HasMany
	{
		return $this->hasMany(DrawWinner::class)->orderBy('position');
	}

	public function getPoolLabelAttribute(): string
	{
		return self::pools()[$this->pool] ?? $this->pool;
	}

	/**
	 * Membres admissibles : comptes actifs dont le courriel est validé, selon le bassin.
	 * Un fournisseur possède aussi un compte client jumeau — on ne prend donc que les
	 * comptes correspondant au bassin demandé pour éviter les doublons.
	 */
	public static function eligibleQuery(string $pool)
	{
		$query = Subscriber::where('active', '=', true);

		if ($pool === self::POOL_CLIENTS) {
			$query->where('is_provider', '=', false);
		} elseif ($pool === self::POOL_PROVIDERS) {
			$query->where('is_provider', '=', true);
		}

		return $query;
	}
}
