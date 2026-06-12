<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\PromotionTranslation
 *
 * @property int $id
 * @property int $promotion_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation wherePromotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromotionTranslation whereTitle($value)
 * @mixin \Eloquent
 */
class PromotionTranslation extends TranslationModel
{
	public $timestamps = false;
}
