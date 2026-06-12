<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocMiniCardTranslation
 *
 * @property int $id
 * @property int $bloc_mini_card_id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @property string $locale
 * @method static Builder|BlocMiniCardTranslation newModelQuery()
 * @method static Builder|BlocMiniCardTranslation newQuery()
 * @method static Builder|BlocMiniCardTranslation query()
 * @method static Builder|BlocMiniCardTranslation whereBlocMiniCardId($value)
 * @method static Builder|BlocMiniCardTranslation whereCallToActionLabel($value)
 * @method static Builder|BlocMiniCardTranslation whereCallToActionUrl($value)
 * @method static Builder|BlocMiniCardTranslation whereContent($value)
 * @method static Builder|BlocMiniCardTranslation whereId($value)
 * @method static Builder|BlocMiniCardTranslation whereLocale($value)
 * @method static Builder|BlocMiniCardTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocMiniCardTranslation extends TranslationModel {
	public $timestamps = false;
}