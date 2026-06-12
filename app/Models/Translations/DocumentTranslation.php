<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\DocumentTranslation
 *
 * @property int $id
 * @property int $document_id
 * @property string|null $title
 * @property string|null $filename
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $vignette_image
 * @property string|null $content
 * @property string $locale
 * @method static Builder|DocumentTranslation newModelQuery()
 * @method static Builder|DocumentTranslation newQuery()
 * @method static Builder|DocumentTranslation query()
 * @method static Builder|DocumentTranslation whereContent($value)
 * @method static Builder|DocumentTranslation whereDescription($value)
 * @method static Builder|DocumentTranslation whereDocumentId($value)
 * @method static Builder|DocumentTranslation whereFilename($value)
 * @method static Builder|DocumentTranslation whereId($value)
 * @method static Builder|DocumentTranslation whereKeywords($value)
 * @method static Builder|DocumentTranslation whereLocale($value)
 * @method static Builder|DocumentTranslation whereTitle($value)
 * @method static Builder|DocumentTranslation whereVignetteImage($value)
 * @mixin Eloquent
 */
class DocumentTranslation extends TranslationModel {
	public $timestamps = false;
}