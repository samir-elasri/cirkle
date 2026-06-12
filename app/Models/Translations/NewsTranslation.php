<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\NewsTranslation
 *
 * @property int $id
 * @property int $news_id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $legend
 * @property string|null $description
 * @property string $locale
 * @method static Builder|NewsTranslation newModelQuery()
 * @method static Builder|NewsTranslation newQuery()
 * @method static Builder|NewsTranslation query()
 * @method static Builder|NewsTranslation whereDescription($value)
 * @method static Builder|NewsTranslation whereId($value)
 * @method static Builder|NewsTranslation whereImage($value)
 * @method static Builder|NewsTranslation whereLegend($value)
 * @method static Builder|NewsTranslation whereLocale($value)
 * @method static Builder|NewsTranslation whereNewsId($value)
 * @method static Builder|NewsTranslation whereTitle($value)
 * @mixin Eloquent
 */
class NewsTranslation extends TranslationModel {
	public $timestamps = false;
}