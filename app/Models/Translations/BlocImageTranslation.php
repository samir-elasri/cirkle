<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocImageTranslation
 *
 * @property int $id
 * @property int $bloc_image_id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $alt
 * @property string|null $legend
 * @property string $locale
 * @method static Builder|BlocImageTranslation newModelQuery()
 * @method static Builder|BlocImageTranslation newQuery()
 * @method static Builder|BlocImageTranslation query()
 * @method static Builder|BlocImageTranslation whereAlt($value)
 * @method static Builder|BlocImageTranslation whereBlocImageId($value)
 * @method static Builder|BlocImageTranslation whereId($value)
 * @method static Builder|BlocImageTranslation whereImage($value)
 * @method static Builder|BlocImageTranslation whereLegend($value)
 * @method static Builder|BlocImageTranslation whereLocale($value)
 * @method static Builder|BlocImageTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocImageTranslation extends TranslationModel {
	public $timestamps = false;
}