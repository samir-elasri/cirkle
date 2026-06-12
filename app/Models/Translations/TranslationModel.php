<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\TranslationModel
 *
 * @method static Builder|TranslationModel newModelQuery()
 * @method static Builder|TranslationModel newQuery()
 * @method static Builder|TranslationModel query()
 * @mixin Eloquent
 */
class TranslationModel extends Eloquent
{

	protected static $unguarded = true;
}
