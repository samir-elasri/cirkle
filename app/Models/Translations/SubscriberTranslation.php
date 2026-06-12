<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\SubscriberTranslation
 *
 * @property int $id
 * @property int $subscriber_id
 * @property string|null $other_service_descriptions
 * @property string|null $url
 * @property string $locale
 * @method static Builder|SubscriberTranslation newModelQuery()
 * @method static Builder|SubscriberTranslation newQuery()
 * @method static Builder|SubscriberTranslation query()
 * @method static Builder|SubscriberTranslation whereId($value)
 * @method static Builder|SubscriberTranslation whereLocale($value)
 * @method static Builder|SubscriberTranslation whereOtherServiceDescriptions($value)
 * @method static Builder|SubscriberTranslation whereSubscriberId($value)
 * @method static Builder|SubscriberTranslation whereUrl($value)
 * @mixin Eloquent
 */
class SubscriberTranslation extends TranslationModel {
	public $timestamps = false;
}