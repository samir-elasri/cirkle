<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * App\Models\Core\Blocs\BlocModel
 *
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|BlocModel newModelQuery()
 * @method static Builder|BlocModel newQuery()
 * @method static Builder|BlocModel query()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocModel extends Model
{

	protected $hidden = [
		'bloc'
	];

	function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this['appends'] = array_merge([
			'label',
			'top_spacing',
			'title_color',
			'bg_type',
			'bg_color',
			'bg_bleed',
			'half_width_mode',
			'active'
		], $this['appends']);

		$this['fieldTypes'] = array_merge([
			'label' => 'varchar',
			'top_spacing' => 'integer',
			'title_color' => 'varchar',
			'bg_color' => 'varchar',
			'bg_bleed' => 'boolean',
			'half_width_mode' => 'boolean',
			'active' => 'boolean',
		], $this['fieldTypes']);
	}

	/**
	 * @return Attribute
	 */
	protected function label(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->label
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setLabelAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->label = $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function topSpacing(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->top_spacing
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setTopSpacingAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->top_spacing = $value === '' ? null : $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function titleColor(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->title_color
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setTitleColorAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->title_color = $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function bgColor(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->bg_color
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setBgColorAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->bg_color = $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function bgBleed(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->bg_bleed
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setBgBleedAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->bg_bleed = $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function halfWidthMode(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc?->half_width_mode
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setHalfWidthModeAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->half_width_mode = $value;
		}
	}

	/**
	 * @return Attribute
	 */
	protected function active(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => $this->bloc->active ?? false
		);
	}

	/**
	 * Laravel 10's Attribute assumes you're modifying the field itself.
	 * @param $value
	 * @return void
	 */
	public function setActiveAttribute($value): void
	{
		if ($this->bloc) {
			$this->bloc->active = $value;
		}
	}

	/**
	 * Retourne le type d'arrière-plan
	 * @return Attribute
	 */
	protected function bgType(): Attribute
	{
		return Attribute::make(
			get: fn ($value) => empty($this->bg_color) ? null : "color-{$this->bg_color}",
		);
	}

	/**
	 * @return MorphOne|Bloc
	 */
	public function bloc(): MorphOne
	{
		return $this->morphOne(Bloc::class, 'blocable');
	}



	/**
	 * Retourne le type d'arrière-plan
	 * @return Attribute
	 */
	protected function isBloc(): Attribute
	{
		return Attribute::make(
			get: static fn ($value) => true,
		);
	}

	/**
	 * @param $field
	 * @return string|void
	 */
	public function getFieldPlaceholder($field)
	{
		switch ($field) {
			case 'bg_color':
				return '(Aucune)';
			case 'top_spacing':
				return setting()->default_bloc_spacing . ' (Valeur par défaut dans les paramètres)';
		}
	}
}
