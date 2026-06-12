<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\SharingTranslation
 *
 * @property int $id
 * @property int $sharing_id
 * @property string|null $fb_title
 * @property string|null $fb_description
 * @property string|null $fb_image
 * @property string|null $tw_title
 * @property string|null $tw_description
 * @property string|null $tw_image
 * @property string $locale
 * @method static Builder|SharingTranslation newModelQuery()
 * @method static Builder|SharingTranslation newQuery()
 * @method static Builder|SharingTranslation query()
 * @method static Builder|SharingTranslation whereFbDescription($value)
 * @method static Builder|SharingTranslation whereFbImage($value)
 * @method static Builder|SharingTranslation whereFbTitle($value)
 * @method static Builder|SharingTranslation whereId($value)
 * @method static Builder|SharingTranslation whereLocale($value)
 * @method static Builder|SharingTranslation whereSharingId($value)
 * @method static Builder|SharingTranslation whereTwDescription($value)
 * @method static Builder|SharingTranslation whereTwImage($value)
 * @method static Builder|SharingTranslation whereTwTitle($value)
 * @mixin Eloquent
 */
class SharingTranslation extends TranslationModel {
	public $timestamps = false;

	protected $fillable = [
		'fb_title',
		'fb_description',
		'fb_image',
		'tw_title',
		'tw_description',
		'tw_image',
	];
}