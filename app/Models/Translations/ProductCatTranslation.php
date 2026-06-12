<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ProductCatTranslation
 *
 * @property int $id
 * @property int $product_cat_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|ProductCatTranslation newModelQuery()
 * @method static Builder|ProductCatTranslation newQuery()
 * @method static Builder|ProductCatTranslation query()
 * @method static Builder|ProductCatTranslation whereId($value)
 * @method static Builder|ProductCatTranslation whereLocale($value)
 * @method static Builder|ProductCatTranslation whereProductCatId($value)
 * @method static Builder|ProductCatTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ProductCatTranslation extends TranslationModel {
	public $timestamps = false;
}