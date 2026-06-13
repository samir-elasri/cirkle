<?php

namespace App\Models;

use App\Models\Core\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\Model;

/**
 * App\Models\Diploma
 *
 * Option payante « Diplômes académiques » (PDIPOMECK) — calque sur License.
 *
 * @property int $id
 * @property int|null $position
 * @property int|null $subscriber_id
 * @property string|null $school
 * @property string|null $graduated_at
 * @property int $active
 * @property string|null $title
 * @property string|null $description
 * @mixin \Eloquent
 */
class Diploma extends Model
{
	use HasFactory;
	use Translatable;

	public string $order_default = 'position';
	public string $order_direction = 'asc';

	protected bool $bigData = true;

	protected $fillable = [
		'title',
		'description',
		'school',
		'graduated_at',
		'subscriber_id',
	];

	public bool $isAjaxEnabled = true;

	public array $positionParentFields = ['subscriber_id'];

	protected array $grid = [
		'id',
		'school',
		'graduated_at',
		'active',
	];

	public array $translatedAttributes = [
		'title',
		'description',
	];

	protected array $niceNames = [
		'title' => 'Nom du cours / de la formation',
		'description' => 'Description',
		'school' => 'École / université',
		'graduated_at' => 'Date d\'obtention (AN/MOIS)',
	];

	protected array $enum = [];

	protected array $customFields = [];
}
