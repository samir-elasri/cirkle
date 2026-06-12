<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocGoogleMapTranslation
 *
 * @property int $id
 * @property int $bloc_google_map_id
 * @property string|null $title
 * @property string|null $content
 * @property string $locale
 * @method static Builder|BlocGoogleMapTranslation newModelQuery()
 * @method static Builder|BlocGoogleMapTranslation newQuery()
 * @method static Builder|BlocGoogleMapTranslation query()
 * @method static Builder|BlocGoogleMapTranslation whereBlocGoogleMapId($value)
 * @method static Builder|BlocGoogleMapTranslation whereContent($value)
 * @method static Builder|BlocGoogleMapTranslation whereId($value)
 * @method static Builder|BlocGoogleMapTranslation whereLocale($value)
 * @method static Builder|BlocGoogleMapTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocGoogleMapTranslation extends TranslationModel {
	public $timestamps = false;
}