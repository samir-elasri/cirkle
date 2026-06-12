<?php

namespace App\Models\Core\Forms;

use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Forms\Answer
 *
 * @property int $id
 * @property string|null $field_name
 * @property string|null $field_value
 * @property int|null $form_answer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read FormAnswer|null $formAnswer
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @method static Builder|Model active()
 * @method static Builder|Answer newModelQuery()
 * @method static Builder|Answer newQuery()
 * @method static Builder|Answer query()
 * @method static Builder|Answer whereCreatedAt($value)
 * @method static Builder|Answer whereFieldName($value)
 * @method static Builder|Answer whereFieldValue($value)
 * @method static Builder|Answer whereFormAnswerId($value)
 * @method static Builder|Answer whereId($value)
 * @method static Builder|Answer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Answer extends Model
{
	protected $fillable = [
		'form_answer_id',
		'field_name',
		'field_value',
	];

	/**
	 * @return BelongsTo|FormAnswer
	 */
	public function formAnswer()
	{
		return $this->belongsTo(FormAnswer::class);
	}
}
