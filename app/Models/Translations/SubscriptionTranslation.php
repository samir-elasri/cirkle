<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\SubscriptionTranslation
 *
 * @property int $id
 * @property int $subscription_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|SubscriptionTranslation newModelQuery()
 * @method static Builder|SubscriptionTranslation newQuery()
 * @method static Builder|SubscriptionTranslation query()
 * @method static Builder|SubscriptionTranslation whereId($value)
 * @method static Builder|SubscriptionTranslation whereLocale($value)
 * @method static Builder|SubscriptionTranslation whereSubscriptionId($value)
 * @method static Builder|SubscriptionTranslation whereTitle($value)
 * @property string|null $description
 * @method static Builder|SubscriptionTranslation whereDescription($value)
 * @mixin Eloquent
 */
class SubscriptionTranslation extends TranslationModel
{
	public $timestamps = false;
}