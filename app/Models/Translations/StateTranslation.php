<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\StateTranslation
 *
 * @property int $id
 * @property int $state_id
 * @property string|null $title
 * @property string $locale
 * @method static Builder|StateTranslation newModelQuery()
 * @method static Builder|StateTranslation newQuery()
 * @method static Builder|StateTranslation query()
 * @method static Builder|StateTranslation whereId($value)
 * @method static Builder|StateTranslation whereLocale($value)
 * @method static Builder|StateTranslation whereStateId($value)
 * @method static Builder|StateTranslation whereTitle($value)
 * @mixin Eloquent
 */
class StateTranslation extends TranslationModel {
	public $timestamps = false;
}