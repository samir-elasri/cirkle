<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\FormFieldTranslation
 *
 * @property int $id
 * @property int $form_field_id
 * @property string|null $title
 * @property string|null $explanations
 * @property string|null $main_logo_image
 * @property string $locale
 * @method static Builder|FormFieldTranslation newModelQuery()
 * @method static Builder|FormFieldTranslation newQuery()
 * @method static Builder|FormFieldTranslation query()
 * @method static Builder|FormFieldTranslation whereExplanations($value)
 * @method static Builder|FormFieldTranslation whereFormFieldId($value)
 * @method static Builder|FormFieldTranslation whereId($value)
 * @method static Builder|FormFieldTranslation whereLocale($value)
 * @method static Builder|FormFieldTranslation whereMainLogoImage($value)
 * @method static Builder|FormFieldTranslation whereTitle($value)
 * @mixin Eloquent
 */
class FormFieldTranslation extends TranslationModel {
	public $timestamps = false;

	protected $fillable = [
		'title',
		'explanations',
	];
}