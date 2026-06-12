<?php

namespace App\Models\Core\Forms;

use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Arr;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\NoReturn;
use League\Csv\ByteSequence;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use SplTempFileObject;

/**
 * App\Models\Core\Forms\FormGenerator
 *
 * @property int $id
 * @property string|null $label
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FormAnswer> $formAnswers
 * @property-read int|null $form_answers_count
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|FormGenerator newModelQuery()
 * @method static Builder|FormGenerator newQuery()
 * @method static Builder|FormGenerator query()
 * @method static Builder|FormGenerator whereCreatedAt($value)
 * @method static Builder|FormGenerator whereId($value)
 * @method static Builder|FormGenerator whereLabel($value)
 * @method static Builder|FormGenerator whereUpdatedAt($value)
 * @property-read Collection<int, \App\Models\Core\Forms\FormField> $formFields
 * @property-read int|null $form_fields_count
 * @mixin Eloquent
 */
class FormGenerator extends Model
{
	protected $fillable = [
		'label',
	];

	protected bool $bigData = true;

	public array $niceNames = [
		'label'                         => 'Titre interne',
		'answers'                       => 'Réponses',
		'submitted_at'                  => 'Date de soumission',
		'formFields.count'              => 'Nombre de champs',
		'formAnswers.count'             => 'Nombre de réponses',
		'formAnswers.latest.created_at' => 'Date de la dernière réponse',
	];

	protected array $grid = [
		'label',
		'formFields.count',
		'formAnswers.count',
		'formAnswers.latest.created_at',
	];

	protected $exports = [
		'individual' => [
			'label'  => 'Export',
			'method' => 'export'
		]
	];

	/**
	 * @return HasMany|FormField[]|FormField
	 */
	public function formFields(): HasMany
	{
		return $this->hasMany(FormField::class);
	}

	public function fieldsHtml(array $oldInputs, $errors = []): string
	{
		return htmlspecialchars_decode(
			implode('', $this->formFields
				->map(function ($field) use ($oldInputs, $errors) {
					$fieldLabel = $field->label;

					$fieldValue = $oldInputs[$fieldLabel] ?? '';
					if ($field->field_type === 'file') {
						$fieldLabel .= '_file';
					}

					$errorMsg = !is_array($errors) ? $errors->get($fieldLabel) : '';

					$html = htmlspecialchars($field->html($fieldValue, $errorMsg));

					// Ajoute le champ Préciser pour
					if (in_array($field->field_type, [
						'choice_unique',
						'choice_multiple'
					])) {
						$other = false;

						foreach ($field->choiceGroup->choices as $choice) { // On verifie si une des options permet le champ Préciser
							if ($choice->other) {
								$other = true;
								break;
							}
						}

						if ($other) {
							$otherFieldLabel = 'other-choice-' . $fieldLabel;
							$otherFieldValue = $oldInputs[$otherFieldLabel] ?? '';
							$otherField = new FormField([
								'label'        => $otherFieldLabel,
								'field_type'   => 'other',
								'title'        => 'Préciser',
								'is_essential' => $field->is_essential,
								'explanations' => 'Veuillez préciser votre réponse'
							]);

							$otherErrorMsg = !is_array($errors) ? $errors->get($otherFieldLabel) : '';

							$html .= htmlspecialchars($otherField->html($otherFieldValue, $otherErrorMsg));
						}
					}

					return $html;
				})->toArray())
		);
	}

	public function compileRulesArray($answers): array
	{
		$requiredFields = $this->formFields()->where('active', true)->get()
			->filter(function ($field) {
				return $field->is_essential || $field->field_type === 'file';
			});

		$otherRequiredFields = new Collection;
		foreach ($requiredFields as $field) {

			if ($field->field_type === 'choice_unique') {
				$name = $field->label;
				$choice = $field->choiceGroup->choices()->whereCodeValue(Arr::get($answers, $name))->first();
				if ($choice && $choice->other) {
					$otherRequiredFields->push(new FormField([
						'label'      => 'other-choice-' . $name,
						'field_type' => 'label',
						'title'      => $field->title
					]));
				}
			}
		}

		$requiredFields = $requiredFields->merge($otherRequiredFields);

		return array_combine(
			$this->compileFieldsKeys($requiredFields),
			$this->compileFieldsRules($requiredFields)
		);
	}

	private function compileFieldsKeys($fields): array
	{
		return $fields->map(function ($field) {
			return $field->field_type === 'file' ? $field->label . '_file' : $field->label;
		})->toArray();
	}

	/**
	 * @param $fields
	 * @return array
	 */
	private function compileFieldsRules($fields): array
	{
		return $fields->map(function ($field) {
			if ($field->field_type === 'file') {
				return $field->is_essential
					? 'required|' . $this->compileFileRule($field->allowed_files, $field->max_file_size)
					: $this->compileFileRule($field->allowed_files, $field->max_file_size);
			}
			if ($field->field_type === 'email') {
				return $field->is_essential
					? 'required|email' : 'email';
			}
			return 'required';
		})->toArray();
	}

	/**
	 * @param $mimes
	 * @param $maxSize
	 * @return string
	 */
	private function compileFileRule($mimes, $maxSize): string
	{
		if ($mimes) {
			return 'mimes:' . parseMimes($mimes) . '|max:' . $this->sizeInKB($maxSize);
		}

		return 'max:' . $this->sizeInKB($maxSize);
	}

	/**
	 * @param $size
	 * @return float|int
	 */
	private function sizeInKB($size): float|int
	{
		return $size * 1000;
	}

	/**
	 * @return HasMany|FormAnswer[]|FormAnswer
	 */
	public function formAnswers(): HasMany
	{
		return $this->hasMany(FormAnswer::class);
	}

	/**
	 * @return void
	 * @throws CannotInsertRecord
	 * @throws Exception
	 * @throws InvalidArgument
	 */
	#[NoReturn] public function export(): void
	{
		set_time_limit(0);

		$formAnswers = $this->formAnswers;
		$fields = $this->getFormFieldsLabel();

		$csv = Writer::createFromFileObject(new SplTempFileObject());

		if ($formAnswers->count() && count($fields)) {
			$csv->setDelimiter(';');
			$csv->setOutputBOM(ByteSequence::BOM_UTF8);

			$header = [
				'label',
				'submitted_at'
			];

			foreach ($header as $index => $value) {
				$header[$index] = $this->niceNames[$value];
			}

			$header = array_merge($header, $fields);

			$csv->insertOne($header);

			foreach ($formAnswers as $formAnswer) {
				$lineData = [
					$this->label,
					$formAnswer->created_at
				];
				foreach ($formAnswer->toArray() as $key => $answer) {
					if ($key === 'content') {
						foreach ($answer as $fields) {
							if (isset($fields['value'])) {
								$lineData[] = $fields['value'];
							}
						}
					}
				}

				$csv->insertOne($lineData);
			}
		}

		$csv->output('answers.csv');
		exit;
	}

	/**
	 * @return array
	 */
	public function getFormFieldsLabel(): array
	{
		return $this->formFields()
			->active()
			->orderBy('position')
			->pluck('label')
			->toArray();
	}
}
