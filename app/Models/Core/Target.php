<?php

namespace App\Models\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Target
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property int $sent
 * @property string|null $sent_date
 * @property int $active
 * @property int|null $list_email_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Collection<int, ListEmail> $listEmail
 * @property-read int|null $list_email_count
 * @method static Builder|Model active()
 * @method static Builder|Target newModelQuery()
 * @method static Builder|Target newQuery()
 * @method static Builder|Target query()
 * @method static Builder|Target whereActive($value)
 * @method static Builder|Target whereCreatedAt($value)
 * @method static Builder|Target whereEmail($value)
 * @method static Builder|Target whereFirstName($value)
 * @method static Builder|Target whereId($value)
 * @method static Builder|Target whereLastName($value)
 * @method static Builder|Target whereListEmailId($value)
 * @method static Builder|Target whereSent($value)
 * @method static Builder|Target whereSentDate($value)
 * @method static Builder|Target whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Target extends Model
{

	public string $order_default = 'title';

	public string $order_direction = 'ASC';

	protected $fillable = [
		'list_email_id',
		'first_name',
		'last_name',
		'email',
		'sent',
		'sent_date',
		'active',
	];

	protected array $niceNames = [
		'first_name' => 'Prénom',
		'last_name'  => 'Nom',
		'email'      => 'Courriel',
		'sent'       => 'Envoyé',
		'sent_date'  => 'Date d\'envois',
	];

	protected array $enum = [
	];

	protected array $customFields = [
	];

	protected array $grid = [
		'first_name',
		'last_name',
		'email',
	];

	/**
	 * @return HasMany|ListEmail
	 */
	public function listEmail()
	{
		return $this->hasMany(ListEmail::class);
	}
}
