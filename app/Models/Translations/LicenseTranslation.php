<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\LicenseTranslation
 *
 * @property int $id
 * @property int $license_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation whereLicenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LicenseTranslation whereTitle($value)
 * @mixin \Eloquent
 */
class LicenseTranslation extends TranslationModel
{
	public $timestamps = false;
}
