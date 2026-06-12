<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ProductTranslation
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @method static Builder|ProductTranslation newModelQuery()
 * @method static Builder|ProductTranslation newQuery()
 * @method static Builder|ProductTranslation query()
 * @method static Builder|ProductTranslation whereDescription($value)
 * @method static Builder|ProductTranslation whereId($value)
 * @method static Builder|ProductTranslation whereLocale($value)
 * @method static Builder|ProductTranslation whereProductId($value)
 * @method static Builder|ProductTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ProductTranslation extends TranslationModel {
	public $timestamps = false;
}