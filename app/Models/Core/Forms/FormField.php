<?php

namespace App\Models\Core\Forms;

use App\Models\Core\Blocs\BlocForm;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\FormFieldTranslation;
use Arr;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use RuntimeException;
use StringUtility;

/**
 * App\Models\Core\Forms\FormField
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $label
 * @property string|null $field_type
 * @property int|null $max_chars
 * @property int|null $choice_group_id
 * @property string|null $allowed_files
 * @property int|null $max_file_size
 * @property int $is_essential
 * @property int|null $position
 * @property int $active
 * @property int|null $form_generator_id
 * @property-read ChoiceGroup|null $choiceGroup
 * @property-read string|null $field_type_custom
 * @property-read FormGenerator|null $form
 * @property-read FormGenerator|null $formGenerator
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read FormFieldTranslation|null $translation
 * @property-read Collection<int, FormFieldTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $explanations
 * @method static Builder|Model active()
 * @method static Builder|FormField listsTranslations(string $translationField)
 * @method static Builder|FormField newModelQuery()
 * @method static Builder|FormField newQuery()
 * @method static Builder|FormField notTranslatedIn(?string $locale = null)
 * @method static Builder|FormField orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|FormField orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|FormField orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|FormField query()
 * @method static Builder|FormField translated()
 * @method static Builder|FormField translatedIn(?string $locale = null)
 * @method static Builder|FormField whereActive($value)
 * @method static Builder|FormField whereAllowedFiles($value)
 * @method static Builder|FormField whereChoiceGroupId($value)
 * @method static Builder|FormField whereCreatedAt($value)
 * @method static Builder|FormField whereFieldType($value)
 * @method static Builder|FormField whereFormGeneratorId($value)
 * @method static Builder|FormField whereId($value)
 * @method static Builder|FormField whereIsEssential($value)
 * @method static Builder|FormField whereLabel($value)
 * @method static Builder|FormField whereMaxChars($value)
 * @method static Builder|FormField whereMaxFileSize($value)
 * @method static Builder|FormField wherePosition($value)
 * @method static Builder|FormField whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|FormField whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|FormField whereUpdatedAt($value)
 * @method static Builder|FormField withTranslation()
 * @mixin Eloquent
 */
class FormField extends Model implements TranslatableContract
{
	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	protected $fillable = [
		'form_generator_id',
		'label',
		'title',
		'explanations',
		'field_type',
		'max_chars',
		'choice_group_id',
		'allowed_files',
		'max_file_size',
		'is_essential',
		'active',
	];

	public $translatedAttributes = [
		'title',
		'explanations',
	];

	protected array $enum = [
		'field_type' => [
			'label' => 'Libellé',
			'integer' => 'Entier',
			'money' => '$',
			'email' => 'Courriel',
			'date' => 'Date',
			'text' => 'Paragraphe',
			'tel' => 'Téléphone',
			'choice_unique' => 'Choix - réponse unique',
			'choice_multiple' => 'Choix - réponse multiples',
			'file' => 'Téléchargement de fichier',
		]
	];

	protected array $niceNames = [
		'form_generator_id' => 'Formulaire',
		'label' => 'Titre interne',
		'title' => 'Titre',
		'explanations' => 'Explications',
		'field_type' => 'Type',
		'max_chars' => 'Nombre de caractères max',
		'choice_group_id' => 'Regroupement de choix associé',
		'allowed_files' => 'Liste des extensions de fichiers autorisées (séparés par des « ; »)',
		'max_file_size' => 'Poids maximal (en MB)',
		'is_essential' => 'Essentiel',
		'field_type_custom' => 'Type',
	];

	protected array $customFields = [
		'choice_group_id' => [
			'widget' => 'associate_entity',
			'options' => [
				'associate_class' => ChoiceGroup::class
			]
		]
	];

	protected array $rules = [
		'label' => 'required|alpha_dash',
		'field_type' => 'required',
	];

	protected array $grid = ['label', 'title', 'field_type_custom', 'is_essential'];

	protected $appends = ['field_type_custom'];

	protected $resetPages = [
		BlocForm::class => [
			'relation' => 'form_generator_id',
			'id' => 'form_generator_id',
		],
	];

	/**
	 * @return Attribute
	 */
	protected function fieldTypeCustom(): Attribute
	{
		return Attribute::make(
			get: function (): ?string {
				$fieldTypeEnum = $this->enum['field_type'];

				return Arr::get($fieldTypeEnum, $this->field_type);
			}
		);
	}


	/**
	 * @return Attribute
	 */
	protected function name(): Attribute
	{
		return Attribute::make(
			set: static fn(?string $value) => StringUtility::sluggify($value)
		);
	}

	/**
	 * @return BelongsTo|FormGenerator
	 */
	public function form(): BelongsTo
	{
		return $this->belongsTo(FormGenerator::class, 'form_generator_id');
	}

	/**
	 * @return BelongsTo|FormGenerator
	 */
	public function formGenerator(): BelongsTo
	{
		return $this->belongsTo(FormGenerator::class);
	}

	/**
	 * @return BelongsTo|ChoiceGroup
	 */
	public function choiceGroup(): BelongsTo
	{
		return $this->belongsTo(ChoiceGroup::class);
	}

	/**
	 * @param $fieldValue
	 * @param $errorMsg
	 * @return string
	 * @throws InputFieldTypeNotFound
	 */
	public function html($fieldValue, $errorMsg = ''): string
	{
		$field_type = $this->field_type;
		if ($field_type === 'other') {
			$field_type = 'label';
		}

		$className = 'App\\Models\\Core\\Forms\\Fields\\' . ucfirst(str_replace('_', '', $field_type)) . 'InputField';

		if (!class_exists($className)) {
			throw new InputFieldTypeNotFound("There is no class to generate html input for the provided type : {$field_type}.");
		}

		if ($className === 'App\\Models\\Core\\Forms\\Fields\\InputField') {
			throw new RuntimeException("Your field of id: {$this->id} has no type.");
		}

		return $className::generate($this->title, $this->label, $fieldValue, [
			'explanations' => !empty($this->explanations) ? $this->explanations : null,
			'required' => $this->is_essential,
			'max_chars' => $this->max_chars,
			'is_other' => $this->field_type === 'other',
			'allowed_files' => $this->allowed_files,
			'max_file_size' => $this->max_file_size,
			'options' => ($this->choiceGroup) ? $this->choiceGroup->mapChoices($fieldValue) : [],
			'error_msg' => $errorMsg,
		]);
	}
}
