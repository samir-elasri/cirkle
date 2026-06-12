<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\ProfileOptionTranslation
 *
 * @property int $id
 * @property int $profile_option_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation whereProfileOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfileOptionTranslation whereTitle($value)
 * @mixin \Eloquent
 */
class ProfileOptionTranslation extends TranslationModel
{
	public $timestamps = false;
}
