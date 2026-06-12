<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\CategoryTranslation
 *
 * @property int $id
 * @property int $category_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|CategoryTranslation newModelQuery()
 * @method static Builder|CategoryTranslation newQuery()
 * @method static Builder|CategoryTranslation query()
 * @method static Builder|CategoryTranslation whereCategoryId($value)
 * @method static Builder|CategoryTranslation whereId($value)
 * @method static Builder|CategoryTranslation whereLocale($value)
 * @method static Builder|CategoryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class CategoryTranslation extends TranslationModel {
	public $timestamps = false;
}