<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\GoogleMapTranslation
 *
 * @property int $id
 * @property int $google_map_id
 * @property string|null $title
 * @property string|null $text
 * @property string|null $url_label
 * @property string|null $url
 * @property string $locale
 * @method static Builder|GoogleMapTranslation newModelQuery()
 * @method static Builder|GoogleMapTranslation newQuery()
 * @method static Builder|GoogleMapTranslation query()
 * @method static Builder|GoogleMapTranslation whereGoogleMapId($value)
 * @method static Builder|GoogleMapTranslation whereId($value)
 * @method static Builder|GoogleMapTranslation whereLocale($value)
 * @method static Builder|GoogleMapTranslation whereText($value)
 * @method static Builder|GoogleMapTranslation whereTitle($value)
 * @method static Builder|GoogleMapTranslation whereUrl($value)
 * @method static Builder|GoogleMapTranslation whereUrlLabel($value)
 * @mixin Eloquent
 */
class GoogleMapTranslation extends TranslationModel {
	public $timestamps = false;
}