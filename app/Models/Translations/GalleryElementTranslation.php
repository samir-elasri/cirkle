<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\GalleryElementTranslation
 *
 * @property int $id
 * @property int $gallery_element_id
 * @property string|null $title
 * @property string|null $filename
 * @property string|null $filename_thumb
 * @property string|null $description
 * @property string|null $legend
 * @property string $locale
 * @method static Builder|GalleryElementTranslation newModelQuery()
 * @method static Builder|GalleryElementTranslation newQuery()
 * @method static Builder|GalleryElementTranslation query()
 * @method static Builder|GalleryElementTranslation whereDescription($value)
 * @method static Builder|GalleryElementTranslation whereFilename($value)
 * @method static Builder|GalleryElementTranslation whereFilenameThumb($value)
 * @method static Builder|GalleryElementTranslation whereGalleryElementId($value)
 * @method static Builder|GalleryElementTranslation whereId($value)
 * @method static Builder|GalleryElementTranslation whereLegend($value)
 * @method static Builder|GalleryElementTranslation whereLocale($value)
 * @method static Builder|GalleryElementTranslation whereTitle($value)
 * @mixin Eloquent
 */
class GalleryElementTranslation extends TranslationModel {
	public $timestamps = false;
}