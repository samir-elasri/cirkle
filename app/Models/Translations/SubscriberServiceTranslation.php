<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\SubscriberServiceTranslation
 *
 * @property int $id
 * @property int $subscriber_service_id
 * @property string|null $description
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubscriberServiceTranslation whereSubscriberServiceId($value)
 * @mixin \Eloquent
 */
class SubscriberServiceTranslation extends TranslationModel
{
	public $timestamps = false;
}
