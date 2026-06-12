<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Translations\BlocVideoTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocVideo
 *
 * @property int $id
 * @property string|null $video_type
 * @property int $use_fr
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocVideoTranslation|null $translation
 * @property-read Collection<int, BlocVideoTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $image
 * @property string|null $video_url
 * @property string|null $video_filename
 * @property string|null $description
 * @property string|null $legend
 * @method static Builder|Model active()
 * @method static Builder|BlocVideo listsTranslations(string $translationField)
 * @method static Builder|BlocVideo newModelQuery()
 * @method static Builder|BlocVideo newQuery()
 * @method static Builder|BlocVideo notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocVideo orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocVideo orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocVideo orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocVideo query()
 * @method static Builder|BlocVideo translated()
 * @method static Builder|BlocVideo translatedIn(?string $locale = null)
 * @method static Builder|BlocVideo whereCreatedAt($value)
 * @method static Builder|BlocVideo whereId($value)
 * @method static Builder|BlocVideo whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocVideo whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocVideo whereUpdatedAt($value)
 * @method static Builder|BlocVideo whereUseFr($value)
 * @method static Builder|BlocVideo whereVideoType($value)
 * @method static Builder|BlocVideo withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocVideo extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title', 'description', 'legend'];

	protected $fillable = [

		'@ Paramètres du bloc vidéo',
		'label',
		'title',
		'title_color',
		'video_type',
		'image',
		'video_url',
		'video_filename',
		'use_fr',
		'description',
		'legend',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active'
	];

	protected array $toggleFields = [
		'image'          => "form.video_type.value == 'video'",
		'video_url'      => "form.video_type.value == 'youtube' || form.video_type.value == 'vimeo'",
		'video_filename' => "form.video_type.value == 'video'",
	];

	public $translatedAttributes = [
		'title',
		'image',
		'video_url',
		'video_filename',
		'description',
		'legend'
	];

	protected array $customFields = [
		'description' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		]
	];

	protected array $niceNames = [
		'use_fr'         => 'Utiliser le contenu FR pour toutes les langues',
		'image'          => 'Image',
		'video_type'     => 'Type de média',
		'video_url'      => 'Id',
		'video_filename' => 'Vidéo',
		'bg_color'       => 'Couleur de fond',
	];

	protected array $enum = [
		'video_type' => [
			'youtube' => 'Youtube',
			'vimeo'   => 'Vimeo',
			'video'   => 'Vidéo'
		]
	];

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		$arr = parent::toArray();
		if ($this->use_fr) {
			$arr['image'] = $this->translate('fr')->image ?? null;
			$arr['video_url'] = $this->translate('fr')->video_url ?? null;
			$arr['video_filename'] = $this->translate('fr')->video_filename ?? null;
		}
		return $arr;
	}
}
