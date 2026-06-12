<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\MiniCardTranslation
 *
 * @property int $id
 * @property int $mini_card_id
 * @property string|null $image
 * @property string|null $title
 * @property string|null $sub_title
 * @property string|null $text
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @property string $locale
 * @method static Builder|MiniCardTranslation newModelQuery()
 * @method static Builder|MiniCardTranslation newQuery()
 * @method static Builder|MiniCardTranslation query()
 * @method static Builder|MiniCardTranslation whereCallToActionLabel($value)
 * @method static Builder|MiniCardTranslation whereCallToActionUrl($value)
 * @method static Builder|MiniCardTranslation whereId($value)
 * @method static Builder|MiniCardTranslation whereImage($value)
 * @method static Builder|MiniCardTranslation whereLocale($value)
 * @method static Builder|MiniCardTranslation whereMiniCardId($value)
 * @method static Builder|MiniCardTranslation whereSubTitle($value)
 * @method static Builder|MiniCardTranslation whereText($value)
 * @method static Builder|MiniCardTranslation whereTitle($value)
 * @mixin Eloquent
 */
class MiniCardTranslation extends TranslationModel {
	public $timestamps = false;
}