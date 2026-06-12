<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\GalleryTranslation
 *
 * @property int $id
 * @property int $gallery_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static Builder|GalleryTranslation newModelQuery()
 * @method static Builder|GalleryTranslation newQuery()
 * @method static Builder|GalleryTranslation query()
 * @method static Builder|GalleryTranslation whereDescription($value)
 * @method static Builder|GalleryTranslation whereGalleryId($value)
 * @method static Builder|GalleryTranslation whereId($value)
 * @method static Builder|GalleryTranslation whereLocale($value)
 * @method static Builder|GalleryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class GalleryTranslation extends TranslationModel {
	public $timestamps = false;
}