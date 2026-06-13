<?php

namespace App\Models\Translations;

use App\Models\Translations\TranslationModel;

/**
 * App\Models\Translations\DiplomaTranslation
 *
 * @property int $id
 * @property int $diploma_id
 * @property string|null $title
 * @property string|null $description
 * @property string $locale
 * @mixin \Eloquent
 */
class DiplomaTranslation extends TranslationModel
{
	public $timestamps = false;
}
