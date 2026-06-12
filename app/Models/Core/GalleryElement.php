<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocGallery;
use App\Models\Core\Blocs\BlocPortfolio;
use App\Models\Translations\GalleryElementTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Core\GalleryElement
 *
 * @property int $id
 * @property string|null $type_element
 * @property int $use_fr
 * @property string|null $publication_date
 * @property int $is_headline
 * @property int|null $position
 * @property int $active
 * @property int|null $gallery_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Gallery|null $gallery
 * @property-read mixed $collection_name
 * @property-read mixed $file
 * @property-read mixed $image
 * @property-read SearchResult $search_result
 * @property-read mixed $thumb
 * @property-read mixed $type
 * @property-read mixed $type_label
 * @property-read GalleryElementTranslation|null $translation
 * @property-read Collection<int, GalleryElementTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $filename
 * @property string|null $filename_thumb
 * @property string|null $description
 * @property string|null $legend
 * @method static Builder|Model active()
 * @method static Builder|GalleryElement listsTranslations(string $translationField)
 * @method static Builder|GalleryElement newModelQuery()
 * @method static Builder|GalleryElement newQuery()
 * @method static Builder|GalleryElement notTranslatedIn(?string $locale = null)
 * @method static Builder|GalleryElement orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GalleryElement orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GalleryElement orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|GalleryElement query()
 * @method static Builder|GalleryElement translated()
 * @method static Builder|GalleryElement translatedIn(?string $locale = null)
 * @method static Builder|GalleryElement whereActive($value)
 * @method static Builder|GalleryElement whereCreatedAt($value)
 * @method static Builder|GalleryElement whereGalleryId($value)
 * @method static Builder|GalleryElement whereId($value)
 * @method static Builder|GalleryElement whereIsHeadline($value)
 * @method static Builder|GalleryElement wherePosition($value)
 * @method static Builder|GalleryElement wherePublicationDate($value)
 * @method static Builder|GalleryElement whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|GalleryElement whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|GalleryElement whereTypeElement($value)
 * @method static Builder|GalleryElement whereUpdatedAt($value)
 * @method static Builder|GalleryElement whereUseFr($value)
 * @method static Builder|GalleryElement withTranslation()
 * @mixin Eloquent
 */
class GalleryElement extends Model implements TranslatableContract
{

	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	public bool $isAjaxEnabled = true;

	public string $singular = 'un élément';

	public $relatedGrid = 'éléments pour cette galerie';

	protected $appends = [
		'image',
		'thumb',
		'file',
		'type'
	];

	protected array $toggleFields = [
		'filename_thumb' => "form.type_element.value == 'local' || form.type_element.value == 'img'",
	];

	protected array $rules = [
		'type_element' => 'required',
	];

	protected $fillable = [
		'gallery_id',
		'title',
		'type_element',
		'filename',
		'filename_thumb',
		'use_fr',
		'legend',
		'description',
		'is_headline',
		'publication_date',
		'active'
	];

	public $translatedAttributes = [
		'title',
		'filename',
		'filename_thumb',
		'description',
		'legend'
	];

	protected array $grid = [
		'thumb',
		'title',
		'type_element_grid',
		'is_headline'
	];

	protected array $niceNames = [
		'use_fr'            => 'Utiliser le contenu FR pour toutes les langues',
		'type_label'        => 'Type',
		'type_element'      => 'Type',
		'filename'          => 'Fichier ou identifiant',
		'filename_thumb'    => 'Vignette',
		'is_headline'       => 'Principal',
		'thumb'             => 'Aperçu',
		'type_element_grid' => 'Type de fichier'
	];

	protected array $enum = [
		'type_element' => [
			'img'     => 'Fichier image',
			'local'   => 'Fichier vidéo',
			'youtube' => 'Vidéo youtube',
			'vimeo'   => 'Vidéo vimeo',
		]
	];

	protected array $customFields = [
		'description' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => '150']
		],
		'is_headline' => ['widget' => 'skip']
	];

	protected $resetCacheOnChange = [
		Gallery::class
	];

	protected $resetPages = [
		BlocGallery::class   => [
			'id'       => 'gallery_id',
			'relation' => 'gallery_id',
		],
		BlocPortfolio::class => [
			'id'       => 'gallery_id',
			'relation' => 'gallery_id',
		],
	];

	public array $positionParentFields = ['gallery_id'];

	public function __construct(array $attributes = [])
	{
		$this->attributes['publication_date'] = Carbon::now();
		parent::__construct($attributes);
	}

	protected function getFileAttribute()
	{

		if ($this->type_element === 'youtube') {
			return '//www.youtube.com/watch?v=' . $this->filename;
		}

		return $this->filename;
	}

	protected function getImageAttribute()
	{

		if ($this->type_element === 'youtube') {
			return getYoutubeThumb($this->filename);
		}

		if ($this->type_element === 'vimeo') {
			return getVimeoThumb($this->filename);
		}

		if ($this->type_element === 'local') {
			return $this->filename_thumb;
		}

		return $this->filename;
	}

	protected function getThumbAttribute()
	{

		if ($this->type_element === 'youtube') {
			return getYoutubeThumb($this->filename);
		}

		if ($this->type_element === 'vimeo') {
			return getVimeoThumb($this->filename);
		}

		if ($this->type_element === 'local') {
			return $this->filename_thumb;
		}

		return $this->filename;
	}

	protected function getTypeAttribute()
	{
		if ($this->type_element === 'youtube') { // youtube type does not exist
			return 'local';
		}

		return $this->type_element;
	}

	protected function getTypeLabelAttribute()
	{

		if ($this->type_element) {
			$e = $this->enum['type_element'][$this->type_element];
			if ($e) {
				return $e;
			}
		}
		return '';
	}

	/**
	 * @return BelongsTo|Gallery
	 */
	public function gallery()
	{
		return $this->belongsTo(Gallery::class);
	}

	public function toArray(): array
	{
		$arr = parent::toArray();
		if ($this->use_fr) {
			$arr['filename'] = $this->translate('fr')->filename ?? null;
			$arr['filename_thumb'] = $this->translate('fr')->filename_thumb ?? null;
		}
		return $arr;
	}
}
