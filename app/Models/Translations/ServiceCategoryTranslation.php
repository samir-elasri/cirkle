<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ServiceCategoryTranslation
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $provider_description
 * @property string|null $client_description
 * @property string|null $legend
 * @property string $locale
 * @method static Builder|SettingTranslation newModelQuery()
 * @method static Builder|SettingTranslation newQuery()
 * @method static Builder|SettingTranslation query()
 * @method static Builder|SettingTranslation whereTitle($value)
 * @method static Builder|SettingTranslation whereDescription($value)
 * @method static Builder|SettingTranslation whereProviderDescription($value)
 * @method static Builder|SettingTranslation whereClientDescription($value)
 * @method static Builder|SettingTranslation whereLegend($value)
 * @property int $service_category_id
 * @method static Builder|ServiceCategoryTranslation whereId($value)
 * @method static Builder|ServiceCategoryTranslation whereLocale($value)
 * @method static Builder|ServiceCategoryTranslation whereServiceCategoryId($value)
 * @mixin Eloquent
 */
class ServiceCategoryTranslation extends TranslationModel {
	public $timestamps = false;
}