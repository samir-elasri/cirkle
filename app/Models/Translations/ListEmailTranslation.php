<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;


/**
 * App\Models\Translations\ListEmailTranslation
 *
 * @property int $id
 * @property int $list_email_id
 * @property string|null $title
 * @property string|null $object
 * @property string|null $content
 * @property string $locale
 * @method static Builder|ListEmailTranslation newModelQuery()
 * @method static Builder|ListEmailTranslation newQuery()
 * @method static Builder|ListEmailTranslation query()
 * @method static Builder|ListEmailTranslation whereContent($value)
 * @method static Builder|ListEmailTranslation whereId($value)
 * @method static Builder|ListEmailTranslation whereListEmailId($value)
 * @method static Builder|ListEmailTranslation whereLocale($value)
 * @method static Builder|ListEmailTranslation whereObject($value)
 * @method static Builder|ListEmailTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ListEmailTranslation extends TranslationModel {
	public $timestamps = false;

	protected $fillable = [
		'title',
		'object',
		'content',
	];
}