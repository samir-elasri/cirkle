<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\JobOfferTranslation
 *
 * @property int $id
 * @property int $job_offer_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation whereJobOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JobOfferTranslation whereTitle($value)
 * @mixin \Eloquent
 */
class JobOfferTranslation extends TranslationModel
{
	public $timestamps = false;
}
