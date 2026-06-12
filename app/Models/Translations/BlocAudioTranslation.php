<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocAudioTranslation
 *
 * @property int $id
 * @property int $bloc_audio_id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $audio_filename
 * @property string $locale
 * @method static Builder|BlocAudioTranslation newModelQuery()
 * @method static Builder|BlocAudioTranslation newQuery()
 * @method static Builder|BlocAudioTranslation query()
 * @method static Builder|BlocAudioTranslation whereAudioFilename($value)
 * @method static Builder|BlocAudioTranslation whereBlocAudioId($value)
 * @method static Builder|BlocAudioTranslation whereId($value)
 * @method static Builder|BlocAudioTranslation whereImage($value)
 * @method static Builder|BlocAudioTranslation whereLocale($value)
 * @method static Builder|BlocAudioTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocAudioTranslation extends TranslationModel {
	public $timestamps = false;
}