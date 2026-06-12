<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\PubTranslation
 *
 * @property int $id
 * @property int $pub_id
 * @property string|null $title
 * @property string|null $pub_image
 * @property string|null $content
 * @property string|null $url
 * @property int $isTargetBlank
 * @property string $locale
 * @method static Builder|PubTranslation newModelQuery()
 * @method static Builder|PubTranslation newQuery()
 * @method static Builder|PubTranslation query()
 * @method static Builder|PubTranslation whereContent($value)
 * @method static Builder|PubTranslation whereId($value)
 * @method static Builder|PubTranslation whereIsTargetBlank($value)
 * @method static Builder|PubTranslation whereLocale($value)
 * @method static Builder|PubTranslation wherePubId($value)
 * @method static Builder|PubTranslation wherePubImage($value)
 * @method static Builder|PubTranslation whereTitle($value)
 * @method static Builder|PubTranslation whereUrl($value)
 * @mixin Eloquent
 */
class PubTranslation extends TranslationModel {
	public $timestamps = false;
	protected $fillable = ['title', 'pub_image', 'content', 'url', 'isTargetBlank'];
}