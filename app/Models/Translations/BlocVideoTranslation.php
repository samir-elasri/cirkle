<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocVideoTranslation
 *
 * @property int $id
 * @property int $bloc_video_id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $video_url
 * @property string|null $video_filename
 * @property string|null $description
 * @property string|null $legend
 * @property string $locale
 * @method static Builder|BlocVideoTranslation newModelQuery()
 * @method static Builder|BlocVideoTranslation newQuery()
 * @method static Builder|BlocVideoTranslation query()
 * @method static Builder|BlocVideoTranslation whereBlocVideoId($value)
 * @method static Builder|BlocVideoTranslation whereDescription($value)
 * @method static Builder|BlocVideoTranslation whereId($value)
 * @method static Builder|BlocVideoTranslation whereImage($value)
 * @method static Builder|BlocVideoTranslation whereLegend($value)
 * @method static Builder|BlocVideoTranslation whereLocale($value)
 * @method static Builder|BlocVideoTranslation whereTitle($value)
 * @method static Builder|BlocVideoTranslation whereVideoFilename($value)
 * @method static Builder|BlocVideoTranslation whereVideoUrl($value)
 * @mixin Eloquent
 */
class BlocVideoTranslation extends TranslationModel
{
	public $timestamps = false;
}