<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\SlideTranslation
 *
 * @property int $id
 * @property int $slide_id
 * @property string|null $title
 * @property string|null $mobile_title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_label_mobile
 * @property string|null $call_to_action_url
 * @property string|null $action_label
 * @property string|null $action_url
 * @property string|null $sub_text
 * @property string $locale
 * @method static Builder|SlideTranslation newModelQuery()
 * @method static Builder|SlideTranslation newQuery()
 * @method static Builder|SlideTranslation query()
 * @method static Builder|SlideTranslation whereActionLabel($value)
 * @method static Builder|SlideTranslation whereActionUrl($value)
 * @method static Builder|SlideTranslation whereCallToActionLabel($value)
 * @method static Builder|SlideTranslation whereCallToActionLabelMobile($value)
 * @method static Builder|SlideTranslation whereCallToActionUrl($value)
 * @method static Builder|SlideTranslation whereContent($value)
 * @method static Builder|SlideTranslation whereId($value)
 * @method static Builder|SlideTranslation whereLocale($value)
 * @method static Builder|SlideTranslation whereMobileTitle($value)
 * @method static Builder|SlideTranslation whereSlideId($value)
 * @method static Builder|SlideTranslation whereSubText($value)
 * @method static Builder|SlideTranslation whereTitle($value)
 * @mixin Eloquent
 */
class SlideTranslation extends TranslationModel {
	public $timestamps = false;
}