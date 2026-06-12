<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocGalleryTranslation
 *
 * @property int $id
 * @property int $bloc_gallery_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|BlocGalleryTranslation newModelQuery()
 * @method static Builder|BlocGalleryTranslation newQuery()
 * @method static Builder|BlocGalleryTranslation query()
 * @method static Builder|BlocGalleryTranslation whereBlocGalleryId($value)
 * @method static Builder|BlocGalleryTranslation whereId($value)
 * @method static Builder|BlocGalleryTranslation whereLocale($value)
 * @method static Builder|BlocGalleryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocGalleryTranslation extends TranslationModel {
	public $timestamps = false;
}