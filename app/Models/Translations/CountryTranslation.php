<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\CountryTranslation
 *
 * @property int $id
 * @property int $country_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|CountryTranslation newModelQuery()
 * @method static Builder|CountryTranslation newQuery()
 * @method static Builder|CountryTranslation query()
 * @method static Builder|CountryTranslation whereCountryId($value)
 * @method static Builder|CountryTranslation whereId($value)
 * @method static Builder|CountryTranslation whereLocale($value)
 * @method static Builder|CountryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class CountryTranslation extends TranslationModel
{
	public $timestamps = false;
}