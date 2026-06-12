<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocDocumentTranslation
 *
 * @property int $id
 * @property int $bloc_document_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|BlocDocumentTranslation newModelQuery()
 * @method static Builder|BlocDocumentTranslation newQuery()
 * @method static Builder|BlocDocumentTranslation query()
 * @method static Builder|BlocDocumentTranslation whereBlocDocumentId($value)
 * @method static Builder|BlocDocumentTranslation whereId($value)
 * @method static Builder|BlocDocumentTranslation whereLocale($value)
 * @method static Builder|BlocDocumentTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocDocumentTranslation extends TranslationModel {
	public $timestamps = false;
}