<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ServiceTranslation
 *
 * @property int $id
 * @property int $service_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static Builder|ServiceTranslation newModelQuery()
 * @method static Builder|ServiceTranslation newQuery()
 * @method static Builder|ServiceTranslation query()
 * @method static Builder|ServiceTranslation whereDescription($value)
 * @method static Builder|ServiceTranslation whereId($value)
 * @method static Builder|ServiceTranslation whereLocale($value)
 * @method static Builder|ServiceTranslation whereServiceId($value)
 * @method static Builder|ServiceTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ServiceTranslation extends TranslationModel {
	public $timestamps = false;
}