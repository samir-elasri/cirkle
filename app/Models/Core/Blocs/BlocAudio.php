<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Translations\BlocAudioTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocAudio
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocAudioTranslation|null $translation
 * @property-read Collection<int, BlocAudioTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $image
 * @property string|null $audio_filename
 * @method static Builder|Model active()
 * @method static Builder|BlocAudio listsTranslations(string $translationField)
 * @method static Builder|BlocAudio newModelQuery()
 * @method static Builder|BlocAudio newQuery()
 * @method static Builder|BlocAudio notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocAudio orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocAudio orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocAudio orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocAudio query()
 * @method static Builder|BlocAudio translated()
 * @method static Builder|BlocAudio translatedIn(?string $locale = null)
 * @method static Builder|BlocAudio whereCreatedAt($value)
 * @method static Builder|BlocAudio whereId($value)
 * @method static Builder|BlocAudio whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocAudio whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocAudio whereUpdatedAt($value)
 * @method static Builder|BlocAudio withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocAudio extends BlocModel implements TranslatableContract
{

	use Translatable;

	protected $fillable = [

		'@ Paramètres du bloc audio',
		'label',
		'title',
		'title_color',
		'audio_filename',
		'image',


		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'audio_filename',
		'image',
	];

	protected array $niceNames = [
		'audio_filename'  => 'Fichier audio',
		'label'           => 'Titre interne',
		'title'           => 'Titre',
		'half_width_mode' => 'Mode demi-largeur',
	];

	protected $appends = [];

	public $searchFields = [];
}
