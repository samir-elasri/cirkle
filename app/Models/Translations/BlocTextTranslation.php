<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocTextTranslation
 *
 * @property int $id
 * @property int $bloc_text_id
 * @property string|null $label
 * @property string|null $title
 * @property string|null $content
 * @property string|null $summary
 * @property string|null $image
 * @property string|null $back_image
 * @property string|null $video_url
 * @property string|null $video_filename
 * @property string|null $alt
 * @property string|null $legend
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @property string $locale
 * @method static Builder|BlocTextTranslation newModelQuery()
 * @method static Builder|BlocTextTranslation newQuery()
 * @method static Builder|BlocTextTranslation query()
 * @method static Builder|BlocTextTranslation whereAlt($value)
 * @method static Builder|BlocTextTranslation whereBackImage($value)
 * @method static Builder|BlocTextTranslation whereBlocTextId($value)
 * @method static Builder|BlocTextTranslation whereCallToActionLabel($value)
 * @method static Builder|BlocTextTranslation whereCallToActionUrl($value)
 * @method static Builder|BlocTextTranslation whereContent($value)
 * @method static Builder|BlocTextTranslation whereId($value)
 * @method static Builder|BlocTextTranslation whereImage($value)
 * @method static Builder|BlocTextTranslation whereLabel($value)
 * @method static Builder|BlocTextTranslation whereLegend($value)
 * @method static Builder|BlocTextTranslation whereLocale($value)
 * @method static Builder|BlocTextTranslation whereSummary($value)
 * @method static Builder|BlocTextTranslation whereTitle($value)
 * @method static Builder|BlocTextTranslation whereVideoFilename($value)
 * @method static Builder|BlocTextTranslation whereVideoUrl($value)
 * @mixin Eloquent
 */
class BlocTextTranslation extends TranslationModel
{
	public $timestamps = false;

	protected $fillable = [
		'title',
	];
}
