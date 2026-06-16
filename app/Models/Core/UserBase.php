<?php


namespace App\Models\Core;

use App;
use App\Mail\GenericMail;
use App\Models\Translations\SettingTranslation;
use Carbon\Carbon;
use Eloquent;
use Error;
use Exception;
use Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Log;
use Mail;
use RuntimeException;
use StringUtility;

/**
 * App\Models\Core\UserBase
 *
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-write mixed $password
 * @method static Builder|Model active()
 * @method static Builder|UserBase newModelQuery()
 * @method static Builder|UserBase newQuery()
 * @method static Builder|UserBase query()
 * @mixin Eloquent
 */
class UserBase extends Model implements
	AuthenticatableContract,
	AuthorizableContract,
	CanResetPasswordContract
{
	use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;

	public function __construct(array $attributes = [])
	{
		$this->attributes['created_at'] = Carbon::now();

		if (in_array('login_datetime', $this->fillable)) {
			$this->attributes['login_datetime'] = Carbon::now();
		}

		parent::__construct($attributes);
	}

	/**
	 * Send a mail to the registred subscriber email
	 *
	 * @param  String  $type
	 * @param  array   $data
	 * @return bool
	 */
	public function sendMail($type, $data, $replyTo = null): bool
	{
		$preferredLang = in_array(
			$this->preference_language,
			[
				'en',
				'fr'
			]
		) ? $this->preference_language : App::getLocale();

		$setting = setting();
		/** @var SettingTranslation $settingTrans */
		$settingTrans = $setting->translate($preferredLang);

		try {
			$bcc = $subject = '';

			switch ($type) {
				case 'register':
				case 'email_changed':
					$subject = $settingTrans->validation_email_title;
					$data['text'] = str_replace(
						[
							'{{first_name}}',
							'{{last_name}}',
							'{{url_activation}}'
						],
						[
							$this->first_name,
							$this->last_name,
							'<a href="'.$data['url'].'">'.$data['url'].'</a>'
						],
						$settingTrans->validation_email_content
					);
					break;
				case 'recover':
					$subject = $settingTrans->reset_email_title;
					$data['text'] = str_replace(
						[
							'{{first_name}}',
							'{{last_name}}',
							'{{url_activation}}',
						],
						[
							$this->first_name,
							$this->last_name,
							'<a href="'.$data['url'].'">'.$data['url'].'</a>'
						],
						$settingTrans->reset_email_content
					);
					break;

				case 'confirm-purchase':
					$subject = $settingTrans->purchase_confirmation_email_title;
					$data['text'] = str_replace(
						[
							'{{first_name}}',
							'{{last_name}}',
							'{{url}}'
						],
						[
							$this->first_name,
							$this->last_name,
							'<a href="'.$data['url'].'">'.$data['url'].'</a>'
						],
						$settingTrans->purchase_confirmation_email_content
					);
					break;

                case 'customer_evaluation':
					$subject = $settingTrans->customer_evaluation_title;
					$data['text'] = str_replace(
                        [
                            '{{url}}'
                        ],
                        [
                            $data['url']
                        ],
                        $settingTrans->customer_evaluation_text
                    );
                    break;

                case 'search_notification':
                    $subject = $settingTrans->search_notification_title;
                    $data['text'] = str_replace(
                        [
                            '{{list}}'
                        ],
                        [
                            $data['list']
                        ],
                        $settingTrans->search_notification_text
                    );
                    break;

                case 'new_contact_request':
                    $subject = $settingTrans->new_contact_request_title;
                    $data['text'] = str_replace(
                        [
                            '{{text}}'
                        ],
                        [
                            $data['text']
                        ],
                        $settingTrans->new_contact_request_text
                    );
                    break;

				default:
			}

			$message = Mail::to($this);

			if (!empty($bcc)) {
				$message->bcc($bcc);
			}

			$message->send(new GenericMail($this, $data['text'], $subject, $replyTo));

			return true;
		} catch (Exception|Error $e) {
			Log::error($e->getMessage());
		}

		return false;
	}

	/**
	 * @return string|null
	 * @throws Exception
	 */
	public function recoveringToken(): ?string
	{
		$this->recover_token = StringUtility::generateRandomString(20);

		if ($this->save()) {
			return $this->recover_token;
		}

		throw new RuntimeException('An error occured while trying to reset password.');
	}

	/**
	 * @param $value
	 */
	public function setPasswordAttribute($value): void
	{
		if (!empty($value) && $this->password !== $value) {
			$this->attributes['password'] = Hash::make($value);
		}
	}

	public static function getByToken($token)
	{
		return self::active()->whereNotNull('recover_token')->where('recover_token', $token)->first();
	}

	/**
	 * Recherche par jeton SANS exiger « active » : à la validation du courriel, un
	 * fournisseur fraîchement inscrit est encore inactif (il ne devient actif qu'au
	 * paiement), donc getByToken() ne le trouverait pas. Le jeton aléatoire à usage
	 * unique reste la garantie de sécurité.
	 */
	public static function getByValidationToken($token)
	{
		return self::whereNotNull('recover_token')->where('recover_token', $token)->first();
	}
}
