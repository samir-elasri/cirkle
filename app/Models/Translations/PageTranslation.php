<?php

namespace App\Models\Translations;

use App;
use App\Models\Core\MenuTree;
use App\Models\Core\Page;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Mbiance\AdminUtility\StringUtility;

/**
 * App\Models\Translations\PageTranslation
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $custom_url
 * @property string $locale
 * @method static Builder|PageTranslation newModelQuery()
 * @method static Builder|PageTranslation newQuery()
 * @method static Builder|PageTranslation query()
 * @method static Builder|PageTranslation whereCustomUrl($value)
 * @method static Builder|PageTranslation whereId($value)
 * @method static Builder|PageTranslation whereLocale($value)
 * @method static Builder|PageTranslation whereMetaDescription($value)
 * @method static Builder|PageTranslation whereMetaTitle($value)
 * @method static Builder|PageTranslation wherePageId($value)
 * @method static Builder|PageTranslation whereTitle($value)
 * @mixin Eloquent
 */
class PageTranslation extends TranslationModel
{
	public $timestamps = false;

	public function setCustomUrlAttribute($value)
	{
		/** @var StringUtility $stringUtility */
		$stringUtility = App::make(StringUtility::class);
		$this->attributes['custom_url'] = $stringUtility->sluggify($value, true);
	}

	public static function boot()
	{
		parent::boot();
		static::saved(function () {
			Page::clearCache();
			MenuTree::clearCache();
		});
	}
}