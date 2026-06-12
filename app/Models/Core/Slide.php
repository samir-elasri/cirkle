<?php

namespace App\Models\Core;

use App\Models\Translations\SlideTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Slide
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $image
 * @property int|null $position
 * @property string|null $filename_video
 * @property int $call_to_action_present
 * @property int $active
 * @property int|null $slideshow_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read Slideshow|null $slideshow
 * @property-read SlideTranslation|null $translation
 * @property-read Collection<int, SlideTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $mobile_title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_label_mobile
 * @property string|null $call_to_action_url
 * @property string|null $sub_text
 * @method static Builder|Model active()
 * @method static Builder|Slide listsTranslations(string $translationField)
 * @method static Builder|Slide newModelQuery()
 * @method static Builder|Slide newQuery()
 * @method static Builder|Slide notTranslatedIn(?string $locale = null)
 * @method static Builder|Slide orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Slide orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Slide orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Slide query()
 * @method static Builder|Slide translated()
 * @method static Builder|Slide translatedIn(?string $locale = null)
 * @method static Builder|Slide whereActive($value)
 * @method static Builder|Slide whereCallToActionPresent($value)
 * @method static Builder|Slide whereCreatedAt($value)
 * @method static Builder|Slide whereFilenameVideo($value)
 * @method static Builder|Slide whereId($value)
 * @method static Builder|Slide whereImage($value)
 * @method static Builder|Slide whereLabel($value)
 * @method static Builder|Slide wherePosition($value)
 * @method static Builder|Slide whereSlideshowId($value)
 * @method static Builder|Slide whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Slide whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Slide whereUpdatedAt($value)
 * @method static Builder|Slide withTranslation()
 * @mixin Eloquent
 */
class Slide extends Model implements TranslatableContract
{

	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	public string $singular = 'une diapositive';

	public $relatedGrid = 'diapositives pour ce diaporama';

	protected $fillable = [
		'@ Paramètres de diapositive',
		'slideshow_id',
		'label',
		'title',
		'mobile_title',
		'content',
		'sub_text',
		'image',
		'filename_video',
		'@ Appel à l\'action',
		'call_to_action_present',
		'call_to_action_label',
		'call_to_action_label_mobile',
		'call_to_action_url',
		'@ Paramètres généraux',
		'active'
	];

	protected array $toggleFields = [
		'call_to_action_label'        => 'form.call_to_action_present[1].checked',
		'call_to_action_label_mobile' => 'form.call_to_action_present[1].checked',
		'call_to_action_url'          => 'form.call_to_action_present[1].checked'
	];

	public $translatedAttributes = [
		'title',
		'mobile_title',
		'content',
		'call_to_action_label',
		'call_to_action_label_mobile',
		'call_to_action_url',
		'sub_text',
	];

	protected array $grid = ['image', 'title', 'active'];

	protected array $rules = [
		'image' => 'image|mimes:jpeg,jpg,bmp,png,gif'
	];

	protected array $niceNames = [
		'slideshow_id'                => 'Diaporama',
		'label'                       => 'Titre interne',
		'title'                       => 'Titre',
		'mobile_title'                => 'Titre mobile',
		'content'                     => 'Contenu',
		'call_to_action_present'      => 'Présent',
		'call_to_action_label'        => 'Libellé',
		'call_to_action_label_mobile' => 'Libellé mobile',
		'call_to_action_url'          => 'Url',
		'sub_text'                    => 'Sous-texte',
		'image'                       => 'Image',
		'filename_video'              => 'Vidéo ',
	];

	protected $resetCacheOnChange = [
		Slideshow::class
	];

	public array $positionParentFields = ['slideshow_id'];

	/**
	 * @return BelongsTo|Slideshow
	 */
	public function slideshow()
	{
		return $this->belongsTo(Slideshow::class);
	}

	protected static function boot()
	{
		parent::boot();

		static::saved(function ($model) {
			$pages = Page::where('slideshow_id', $model->slideshow_id)->get();
			foreach ($pages as $page) {
				if (Cache::has($page->getCacheKey())) {
					Cache::pull($page->getCacheKey());
				}
			}
		});
	}
}
