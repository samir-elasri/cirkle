<?php

namespace App\Models\Translations;

use App\Models\Core\MenuTree;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\MenuTreeTranslation
 *
 * @property int $id
 * @property int $menu_tree_id
 * @property string|null $title
 * @property string|null $url
 * @property int|null $target_blank
 * @property string $locale
 * @method static Builder|MenuTreeTranslation newModelQuery()
 * @method static Builder|MenuTreeTranslation newQuery()
 * @method static Builder|MenuTreeTranslation query()
 * @method static Builder|MenuTreeTranslation whereId($value)
 * @method static Builder|MenuTreeTranslation whereLocale($value)
 * @method static Builder|MenuTreeTranslation whereMenuTreeId($value)
 * @method static Builder|MenuTreeTranslation whereTargetBlank($value)
 * @method static Builder|MenuTreeTranslation whereTitle($value)
 * @method static Builder|MenuTreeTranslation whereUrl($value)
 * @mixin Eloquent
 */
class MenuTreeTranslation extends TranslationModel {
	public $timestamps = false;

	public static function boot() {
		parent::boot();
		static::saved(function ($model) {
			MenuTree::clearCache();
		});
	}
}