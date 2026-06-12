<?php

namespace App\Models\Core;

use Eloquent;
use Hash;
use App\Models\Core\UserBase as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\User
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $gender
 * @property string|null $birth_date
 * @property string|null $avatar
 * @property int $receive_notification_in_advance
 * @property int $receive_reminder
 * @property string $email
 * @property string $password
 * @property string|null $previous_login
 * @property int $admin
 * @property string|null $email_verified_at
 * @property string|null $remember_token
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $is_admin
 * @property-read mixed $is_mbiance
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|Model active()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereAdmin($value)
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereBirthDate($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereGender($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePreviousLogin($value)
 * @method static Builder|User whereReceiveNotificationInAdvance($value)
 * @method static Builder|User whereReceiveReminder($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
	use Notifiable;

	public static function filterGetRange($query)
	{
		if( ! auth('users')->user()->isMbiance) {
			$query->where('email', '!=', 'mbiance@mbiance.com');
		}
	}

	public bool $bigData = true;

	public static $auth_rules = [
		'email'    => 'required|email',
		'password' => 'required'
	];
	public bool $isAjaxEnabled = true;
	protected $table = 'users';
	protected array $rules = [
		'first_name'            => 'required',
		'last_name'             => 'required',
		'email'                 => 'required|email|unique:users,email,{id},id',
		'password'              => 'nullable|min:6|confirmed'
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'admin',
		'active'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	protected array $grid = ['first_name', 'last_name', 'admin', 'active'];

	protected $appends = ['is_admin', 'is_mbiance'];

	public function getIsAdminAttribute()
	{
		return $this->admin;
	}

	public function getIsMbianceAttribute()
	{
		return $this->email == 'mbiance@mbiance.com';
	}

	public function getNameAttribute()
	{
		return $this->is_mbiance ? 'mbiance' : $this->first_name.' '.$this->last_name;
	}
}
