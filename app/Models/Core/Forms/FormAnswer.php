<?php

namespace App\Models\Core\Forms;

use App\Models\Core\SearchResult;
use Eloquent;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Arr;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Forms\FormAnswer
 *
 * @property int $id
 * @property int|null $form_generator_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Answer> $answers
 * @property-read int|null $answers_count
 * @property-read Collection|array $content
 * @property-read FormGenerator|null $form
 * @property-read FormGenerator|null $formGenerator
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read string|null $label
 * @method static Builder|Model active()
 * @method static Builder|FormAnswer newModelQuery()
 * @method static Builder|FormAnswer newQuery()
 * @method static Builder|FormAnswer query()
 * @method static Builder|FormAnswer whereCreatedAt($value)
 * @method static Builder|FormAnswer whereFormGeneratorId($value)
 * @method static Builder|FormAnswer whereId($value)
 * @method static Builder|FormAnswer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class FormAnswer extends Model
{
	public string $order_default = 'created_at';

	public string $order_direction = 'DESC';

	protected bool $bigData = true;

	protected $fillable = [
		'form_generator_id',
		'content',
		'created_at'
	];

	protected array $niceNames = [
		'form_generator_id' => 'Formulaire',
		'created_at' => 'Date de soumission',
		'content' => 'Réponses'
	];

	protected array $grid = ['label', 'created_at'];

	protected $appends = ['label', 'content'];

	protected array $customFields = [
		'form_generator_id' => [
			'widget' => 'hidden'
		],
		'created_at' => [
			'widget' => 'hidden'
		],
		'content' => [
			'widget' => 'answers'
		]
	];


	/**
	 * @return Attribute
	 */
	protected function content(): Attribute
	{
		return Attribute::make(
			get: function (): Collection|array {
				$formFields = $this->formGenerator->formFields()->active()->get();

				if ($formFields->count() === 0) {
					return [];
				}

				$answers = [];
				foreach ($this->answers as $answer) {
					$answers[$answer->field_name] = $answer->field_value;
				}

				$fields = new Collection;
				/** @var FormField $field */
				foreach ($formFields as $field) {

					if ($field->field_type === 'file') {
						$fields->put($field->label . '_file', $field);
					} else {
						$fields->put($field->label, $field);
					}
				}

				foreach ($answers as $field_name => $field_value) {
					$field = $fields->get($field_name);

					if ($field) {
						$field_value = is_serialized($field_value) ? unserialize($field_value) : $field_value;

						switch ($field->field_type) {
							case 'choice_unique':
								$choice = $field->choiceGroup
									->choices()
									->whereCodeValue($field_value)
									->first();

								/** @var Choice|null $choice */
								$field->value = $choice->title ?? $field_value;
								if ($choice && $choice->other) {
									$field->value .= ' : ' . Arr::get($answers, 'other-choice-' . $field_name);
								}
								break;

							case 'choice_multiple':
								$choices = $field->choiceGroup
									->choices()
									->whereIn('code_value', $field_value)
									->get()
									->map(function ($choice) use ($answers, $field_name) {
										return $choice->title . ($choice->other ? ' : ' . Arr::get($answers,
													'other-choice-' . $field_name) : '');
									})->toArray();

								$field->value = implode(', ', $choices ?: $field_value);
								break;

							case 'file':
								$field->value = '<a href="' . $field_value . '" target="_blank">' . $field_value . '</a>';
								break;

							default:
								$field->value = $field_value;
								break;
						}
					}
				}
				return $fields;
			}
		);
	}


	/**
	 * @return Attribute
	 */
	protected function label(): Attribute
	{
		return Attribute::make(
			get: fn(): ?string => $this->formGenerator->label ?? null
		);
	}

	/**
	 * @param int $formGeneratorId
	 * @param array $answers
	 * @return void
	 */
	public static function saveAnswers(int $formGeneratorId, array $answers): void
	{
		$formAnswer = static::create([
			'form_generator_id' => $formGeneratorId
		]);

		foreach ($answers as $fieldName => $value) {
			if (is_array($value)) {
				$value = serialize($value);
			} elseif (is_object($value)) {
				$value = '/medias/forms/' . $formGeneratorId . '_' . $value->getClientOriginalName();
			}

			Answer::create([
				'form_answer_id' => $formAnswer->id,
				'field_name' => $fieldName,
				'field_value' => $value,
			]);
		}
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
	 * @return HasMany|Answer[]|Answer
	 */
	public function answers(): HasMany
	{
		return $this->hasMany(Answer::class);
	}
}
