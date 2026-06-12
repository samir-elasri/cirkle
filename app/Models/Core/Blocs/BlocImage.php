<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Translations\BlocImageTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocImage
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocImageTranslation|null $translation
 * @property-read Collection<int, BlocImageTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $image
 * @property string|null $alt
 * @property string|null $legend
 * @method static Builder|Model active()
 * @method static Builder|BlocImage listsTranslations(string $translationField)
 * @method static Builder|BlocImage newModelQuery()
 * @method static Builder|BlocImage newQuery()
 * @method static Builder|BlocImage notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocImage orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocImage orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocImage orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocImage query()
 * @method static Builder|BlocImage translated()
 * @method static Builder|BlocImage translatedIn(?string $locale = null)
 * @method static Builder|BlocImage whereCreatedAt($value)
 * @method static Builder|BlocImage whereId($value)
 * @method static Builder|BlocImage whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocImage whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocImage whereUpdatedAt($value)
 * @method static Builder|BlocImage withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocImage extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title', 'alt', 'legend'];

	protected $fillable = [

		'@ Paramètres du bloc image',
		'label',
		'title',
		'title_color',
		'image',
		'alt',
		'legend',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active'
	];

	public $translatedAttributes = [
		'title',
		'image',
		'alt',
		'legend'
	];
}
