<?php

namespace App\Models\Core\Forms;

use App\Models\Core\SearchResult;
use Eloquent;
use App\Models\Core\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Forms\ChoiceGroup
 *
 * @property int $id
 * @property string|null $label
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Choice> $choices
 * @property-read int|null $choices_count
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read string|null $name
 * @method static Builder|Model active()
 * @method static Builder|ChoiceGroup newModelQuery()
 * @method static Builder|ChoiceGroup newQuery()
 * @method static Builder|ChoiceGroup query()
 * @method static Builder|ChoiceGroup whereActive($value)
 * @method static Builder|ChoiceGroup whereCreatedAt($value)
 * @method static Builder|ChoiceGroup whereId($value)
 * @method static Builder|ChoiceGroup whereLabel($value)
 * @method static Builder|ChoiceGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ChoiceGroup extends Model
{
	public string $singular = 'un regroupement de choix';

	protected $fillable = [
		'label',
	];

	protected array $niceNames = [
		'label' => 'Titre interne',
	];

	protected array $grid = ['label'];

	protected $appends = ['name'];

	/**
	 * @return Attribute
	 */
	protected function name(): Attribute
	{
		return Attribute::make(
			get: fn(): ?string => $this->label
		);
	}

	/**
	 * @param $selectedValues
	 * @return array
	 */
	public function mapChoices($selectedValues): array
	{
		if ($this->relationLoaded('choices')) {
			$choices = $this->choices;
		} else {
			$choices = $this->choices()->whereActive(true)->get();
		}

		return $choices->map(function ($option) use ($selectedValues) {
			$option->selected = $this->detectOptionSelection($selectedValues, $option->code_value);
			return $option;
		})->toArray();
	}

	/**
	 * @return HasMany|Choice[]|Choice
	 */
	public function choices(): HasMany
	{
		return $this->hasMany(Choice::class);
	}

	/**
	 * @param $values
	 * @param $optionValue
	 * @return string
	 */
	private function detectOptionSelection($values, $optionValue): string
	{
		if (is_array($values)) {
			return in_array($optionValue, $values) ? 'selected' : '';
		}

		return $values == $optionValue ? 'selected' : '';
	}
}
