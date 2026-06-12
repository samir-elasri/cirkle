<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocDocument;
use App\Models\Translations\DocumentTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Mbiance\MediaUtility\MediaTrait;
use StringUtility;
use URL;

/**
 * App\Models\Core\Document
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $date
 * @property int|null $doc_type
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read mixed $url
 * @property-read DocumentTranslation|null $translation
 * @property-read Collection<int, DocumentTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $filename
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $vignette_image
 * @property string|null $content
 * @method static Builder|Model active()
 * @method static Builder|Document listsTranslations(string $translationField)
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document notTranslatedIn(?string $locale = null)
 * @method static Builder|Document orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Document orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Document orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Document query()
 * @method static Builder|Document translated()
 * @method static Builder|Document translatedIn(?string $locale = null)
 * @method static Builder|Document whereActive($value)
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereDate($value)
 * @method static Builder|Document whereDocType($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereLabel($value)
 * @method static Builder|Document whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Document whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document withTranslation()
 * @mixin Eloquent
 */
class Document extends Model implements TranslatableContract
{
	use Translatable;

	public bool $isAjaxEnabled = true;

	public $searchFields = ['title', 'content'];

	protected bool $bigData = true;

	protected $fillable = [
		'label',
		'title',
		'doc_type',
		'categories',
		'filename',
		'content',
		'vignette_image',
		'description',
		'keywords',
		'date',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'filename',
		'description',
		'keywords',
		'vignette_image',
		'content'
	];

	protected array $niceNames = [
		'date'           => 'Date officielle de publication',
		'filename'       => 'Fichier associé',
		'doc_type'       => 'Type',
		'description'    => 'Description',
		'keywords'       => 'Mots-clés',
		'vignette_image' => 'Vignette',
		'categories'     => 'Catégories de document',
		'active'         => 'Actif',
	];

	protected array $rules = [
		//"description"
	];

	protected array $customFields = [
		'description' => ['widget' => 'wysiwyg'],
		'doc_type'    => [
			'widget'  => 'associate_category',
			'options' => [
				'identifier' => 'document_types',
			]
		],
		'categories'  => [
			'widget'  => 'associate_categories',
			'options' => [
				'identifier' => 'documents',
				'table'      => 'documents_categories',
			]
		],
		'content'     => ['widget' => 'skip']
	];

	protected array $enum = [];

	protected array $grid = ['date', 'label'];

	protected $appends = [];

	public function getSearchResultAttribute(): SearchResult
	{
		$result = new SearchResult();
		$result->label = $this->title;
		$result->url = '/' . $this->filename;
		return $result;
	}

	public function getUrlAttribute()
	{
		return URL::route('document', [$this->id, StringUtility::sluggify($this->label)], false);
	}

	public static function getListForSearch()
	{
		return static::where('active', true)->get();
	}

	public function saveElement($data = null, $isUnguard = false)
	{
		foreach (getLocales() as $locale) {
			if (isset($data[$locale])) {
				$filename = $data[$locale]['filename'];
				if (empty($filename)) {
					$data[$locale]['content'] = '';
				} else {
					$targetFile = ltrim($filename, '/');
					if (file_exists($targetFile) && mime_content_type($targetFile) == 'application/pdf' && ($this->translate($locale)->filename ?? null) != $filename) {
						$data[$locale]['content'] = shell_exec('pdftotext "' . $targetFile . '" -');
					}
				}
			}
		}

		return parent::saveElement($data, $isUnguard);
	}

	protected static function boot()
	{
		parent::boot();

		static::saved(function ($model) {
			$bloc_ids = BlocDocument::where('associate_documents', 'REGEXP', '[[:<:]]' . $model->id . '[[:>:]]')->pluck('id')->toArray();

			$blocs = Bloc::where('blocable_type', BlocDocument::class)->whereIn('blocable_id', $bloc_ids)->get();
			foreach ($blocs as $bloc) {
				$page = $bloc->pageable_type::find($bloc->pageable_id);

				if (Cache::has($page->getCacheKey())) {
					Cache::pull($page->getCacheKey());
				}
			}
		});
	}

	/*
	protected function getNumPagesPdf($targetFile)
	{
		$fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $targetFile), "r");
		$max = 0;
		if (!$fp) {
			return false;
		} else {
			while (!@feof($fp)) {
				$line = @fgets($fp, 255);
				if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
					preg_match('/[0-9]+/', $matches[0], $matches2);
					if ($max < $matches2[0]) {
						$max = trim($matches2[0]);
						break;
					}
				}
			}
			@fclose($fp);
		}

		return $max;
	}
	*/
}
