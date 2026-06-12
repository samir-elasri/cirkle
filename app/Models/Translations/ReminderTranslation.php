<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ReminderTranslation
 *
 * @property int $id
 * @property int $reminder_id
 * @property string|null $email_title
 * @property string|null $email_content
 * @property string $locale
 * @method static Builder|ReminderTranslation newModelQuery()
 * @method static Builder|ReminderTranslation newQuery()
 * @method static Builder|ReminderTranslation query()
 * @method static Builder|ReminderTranslation whereEmailContent($value)
 * @method static Builder|ReminderTranslation whereEmailTitle($value)
 * @method static Builder|ReminderTranslation whereId($value)
 * @method static Builder|ReminderTranslation whereLocale($value)
 * @method static Builder|ReminderTranslation whereReminderId($value)
 * @mixin Eloquent
 */
class ReminderTranslation extends TranslationModel {
	public $timestamps = false;
}