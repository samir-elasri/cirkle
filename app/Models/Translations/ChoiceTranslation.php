<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\ChoiceTranslation
 *
 * @property int $id
 * @property int $choice_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|ChoiceTranslation newModelQuery()
 * @method static Builder|ChoiceTranslation newQuery()
 * @method static Builder|ChoiceTranslation query()
 * @method static Builder|ChoiceTranslation whereChoiceId($value)
 * @method static Builder|ChoiceTranslation whereId($value)
 * @method static Builder|ChoiceTranslation whereLocale($value)
 * @method static Builder|ChoiceTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ChoiceTranslation extends TranslationModel {
	public $timestamps = false;

	protected $fillable = [
		'title',
	];
}