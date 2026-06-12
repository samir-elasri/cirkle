<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\BasicEventTranslation
 *
 * @property int $id
 * @property int $basic_event_id
 * @property string|null $title
 * @property string|null $image
 * @property string|null $legend
 * @property string|null $description
 * @property string|null $email_title
 * @property string|null $email_text
 * @property string $locale
 * @method static Builder|BasicEventTranslation newModelQuery()
 * @method static Builder|BasicEventTranslation newQuery()
 * @method static Builder|BasicEventTranslation query()
 * @method static Builder|BasicEventTranslation whereBasicEventId($value)
 * @method static Builder|BasicEventTranslation whereDescription($value)
 * @method static Builder|BasicEventTranslation whereEmailText($value)
 * @method static Builder|BasicEventTranslation whereEmailTitle($value)
 * @method static Builder|BasicEventTranslation whereId($value)
 * @method static Builder|BasicEventTranslation whereImage($value)
 * @method static Builder|BasicEventTranslation whereLegend($value)
 * @method static Builder|BasicEventTranslation whereLocale($value)
 * @method static Builder|BasicEventTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BasicEventTranslation extends TranslationModel
{
	public $timestamps = false;

	protected $fillable = [
		'title',
		'image',
		'legend',
	];
}
