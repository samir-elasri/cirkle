<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Translations\BlocDocumentTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use App\Models\Core\Translatable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use View;
use App\Models\Core\Document;

/**
 * App\Models\Core\Blocs\BlocDocument
 *
 * @property int $id
 * @property string|null $associate_documents
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read array|null $documents
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocDocumentTranslation|null $translation
 * @property-read Collection<int, BlocDocumentTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @method static Builder|Model active()
 * @method static Builder|BlocDocument listsTranslations(string $translationField)
 * @method static Builder|BlocDocument newModelQuery()
 * @method static Builder|BlocDocument newQuery()
 * @method static Builder|BlocDocument notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocDocument orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocDocument orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocDocument orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocDocument query()
 * @method static Builder|BlocDocument translated()
 * @method static Builder|BlocDocument translatedIn(?string $locale = null)
 * @method static Builder|BlocDocument whereAssociateDocuments($value)
 * @method static Builder|BlocDocument whereCreatedAt($value)
 * @method static Builder|BlocDocument whereId($value)
 * @method static Builder|BlocDocument whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocDocument whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocDocument whereUpdatedAt($value)
 * @method static Builder|BlocDocument withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocDocument extends BlocModel implements TranslatableContract
{
	use Translatable;

	public $searchFields = ['title'];

	protected $fillable = [

		'@ Paramètres du bloc documents',
		'label',
		'title',
		'title_color',
		'associate_documents',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	public $translatedAttributes = [
		'title',
	];

	protected array $niceNames = [
		'title' => 'Titre',
		'associate_documents' => 'Documents'
	];

	protected array $customFields = [
		'associate_documents' => [
			'widget' => 'associate_grid',
			'options' => ['associate_class' => Document::class]
		],
	];

	protected array $enum = [];

	/**
	 * @return Attribute
	 */
	protected function documents(): Attribute
	{
		return Attribute::make(
			get: function (): ?array {
				if ($this->associate_documents) {

					return explode(',', $this->associate_documents);
				}

				return null;
			}
		);
	}
}

View::composer('core.blocs.document', function ($view) {

	if (!empty($view->associate_documents)) {

		$ids = explode(',', $view->associate_documents);
	} else {
		$ids = [];
	}

	$documents = Document::where('active', true)->whereIn('id', $ids)->get();

	return $view->with(compact('documents'));
});
