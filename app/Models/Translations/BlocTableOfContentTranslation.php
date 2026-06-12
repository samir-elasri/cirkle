<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BlocTableOfContentTranslation
 *
 * @property int $id
 * @property int $bloc_table_of_content_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|BlocTableOfContentTranslation newModelQuery()
 * @method static Builder|BlocTableOfContentTranslation newQuery()
 * @method static Builder|BlocTableOfContentTranslation query()
 * @method static Builder|BlocTableOfContentTranslation whereBlocTableOfContentId($value)
 * @method static Builder|BlocTableOfContentTranslation whereId($value)
 * @method static Builder|BlocTableOfContentTranslation whereLocale($value)
 * @method static Builder|BlocTableOfContentTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlocTableOfContentTranslation extends TranslationModel
{
	public $timestamps = false;
}
