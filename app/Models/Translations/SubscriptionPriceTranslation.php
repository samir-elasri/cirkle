<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\SubscriptionPriceTranslation
 *
 * @property int $id
 * @property int $subscription_price_id
 * @property string|null $text
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation whereSubscriptionPriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriptionPriceTranslation whereText($value)
 * @mixin \Eloquent
 */
class SubscriptionPriceTranslation extends TranslationModel
{
	public $timestamps = false;
}
