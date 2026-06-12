<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocFormTranslation
 *
 * @property int $id
 * @property int $bloc_form_id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $note
 * @property string|null $message
 * @property string|null $email_confirm_title
 * @property string|null $email_confirm_content
 * @property string|null $email_confirm_name
 * @property string|null $email_alert_title
 * @property string|null $email_alert_content
 * @property string|null $email_alert_name
 * @property string $locale
 * @method static Builder|BlocFormTranslation newModelQuery()
 * @method static Builder|BlocFormTranslation newQuery()
 * @method static Builder|BlocFormTranslation query()
 * @method static Builder|BlocFormTranslation whereBlocFormId($value)
 * @method static Builder|BlocFormTranslation whereCallToActionLabel($value)
 * @method static Builder|BlocFormTranslation whereContent($value)
 * @method static Builder|BlocFormTranslation whereEmailAlertContent($value)
 * @method static Builder|BlocFormTranslation whereEmailAlertName($value)
 * @method static Builder|BlocFormTranslation whereEmailAlertTitle($value)
 * @method static Builder|BlocFormTranslation whereEmailConfirmContent($value)
 * @method static Builder|BlocFormTranslation whereEmailConfirmName($value)
 * @method static Builder|BlocFormTranslation whereEmailConfirmTitle($value)
 * @method static Builder|BlocFormTranslation whereId($value)
 * @method static Builder|BlocFormTranslation whereLocale($value)
 * @method static Builder|BlocFormTranslation whereMessage($value)
 * @method static Builder|BlocFormTranslation whereNote($value)
 * @method static Builder|BlocFormTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocFormTranslation extends TranslationModel {
	public $timestamps = false;
}