<?php

namespace App\Models\Core;

use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use View;

/**
 * App\Models\Core\Slideshow
 *
 * @property int $id
 * @property string|null $label
 * @property int|null $slideshow_height
 * @property int|null $auto_play_speed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read mixed $nb_elements
 * @property-read SearchResult $search_result
 * @property-read Collection<int, Slide> $slides
 * @property-read int|null $slides_count
 * @method static Builder|Model active()
 * @method static Builder|Slideshow newModelQuery()
 * @method static Builder|Slideshow newQuery()
 * @method static Builder|Slideshow query()
 * @method static Builder|Slideshow whereAutoPlaySpeed($value)
 * @method static Builder|Slideshow whereCreatedAt($value)
 * @method static Builder|Slideshow whereId($value)
 * @method static Builder|Slideshow whereLabel($value)
 * @method static Builder|Slideshow whereSlideshowHeight($value)
 * @method static Builder|Slideshow whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Slideshow extends Model
{

	protected $fillable = [
		'label',
		'slideshow_height',
		'auto_play_speed',
	];

	protected $appends = ['nbElements'];

	protected array $niceNames = [
		'nbElements' => 'Nb. d\'éléments',
		'slideshow_height' => 'Hauteur diaporama en pixels',
		'auto_play_speed' => 'Nombre de secondes par diapositive'
	];

	protected array $grid = ['label', 'nbElements'];

	protected array $rules = ['label' => 'required'];

	protected function getNbElementsAttribute()
	{
		return $this->slides->count();
	}

	public function slides()
	{
		return $this->hasMany(Slide::class);
	}

	protected function setAutoPlaySpeedAttribute($value)
	{
		$this->attributes['auto_play_speed'] = null_or_empty_string($value) ? null : $value;
	}

	public function getFieldPlaceholder($field)
	{
		switch ($field) {
			case 'auto_play_speed':
				return '5';
			default:
				return parent::getFieldPlaceholder($field);
		}
	}

	protected static function boot()
	{
		parent::boot();

		static::saved(function ($model) {
			foreach (Page::where('slideshow_id', $model->id)->get() as $page) {
				if (Cache::has($page->getCacheKey())) {
					Cache::pull($page->getCacheKey());
				}
			}
		});
	}
}

View::composer('core.partials.slideshow', function ($view) {
	$slideshow = Slideshow::find($view->id);
	$height = $slideshow->slideshow_height;
	$auto_play_speed = is_null($slideshow->auto_play_speed) ? 5 : $slideshow->auto_play_speed;
	$slides =  $slideshow ? $slideshow->slides()->where('active', true)->orderBy('position')->get() : [];
	$view->with(compact('slides', 'height', 'auto_play_speed'));
});
