<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocMiniCard;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\MiniCardGroup
 *
 * @property int $id
 * @property string|null $label
 * @property int|null $width
 * @property string|null $bg_color
 * @property int $active
 * @property int|null $image_height
 * @property string|null $image_mode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MiniCard> $cards
 * @property-read int|null $cards_count
 * @property-read mixed $collection_name
 * @property-read mixed $name
 * @property-read SearchResult $search_result
 * @property-read Collection<int, MiniCard> $miniCards
 * @property-read int|null $mini_cards_count
 * @method static Builder|Model active()
 * @method static Builder|MiniCardGroup newModelQuery()
 * @method static Builder|MiniCardGroup newQuery()
 * @method static Builder|MiniCardGroup query()
 * @method static Builder|MiniCardGroup whereActive($value)
 * @method static Builder|MiniCardGroup whereBgColor($value)
 * @method static Builder|MiniCardGroup whereCreatedAt($value)
 * @method static Builder|MiniCardGroup whereId($value)
 * @method static Builder|MiniCardGroup whereImageHeight($value)
 * @method static Builder|MiniCardGroup whereImageMode($value)
 * @method static Builder|MiniCardGroup whereLabel($value)
 * @method static Builder|MiniCardGroup whereUpdatedAt($value)
 * @method static Builder|MiniCardGroup whereWidth($value)
 * @mixin Eloquent
 */
class MiniCardGroup extends Model
{
	public function __construct($attributes = [])
	{
		// Définition des valeurs par défaut
		$this->attributes['image_mode'] = 'cover';

		// Constructeur
		parent::__construct($attributes);
	}

	protected $fillable = [
		'label',
		'width',
		'image_height',
		'bg_color',
		'image_mode',
	];

	protected array $niceNames = [
		'label'        => 'Titre interne',
		'width'        => 'Largeur des mini-fiches',
		'bg_color'     => 'Couleur de fond',
		'image_height' => 'Hauteur d\'image',
		'image_mode'   => 'Mode de présentation des images',
	];

	protected array $grid = ['label'];

	protected $appends = ['name'];

	protected array $enum = [
		'image_mode' => [
			'cover'   => 'Centre  l\'image pour couvrir tout l\'espace',
			'contain' => 'Montre l\'image au complet'
		]
	];

	protected function getNameAttribute()
	{
		return $this->label;
	}

	protected function setWidthAttribute($value)
	{
		$this->attributes['width'] = null_or_empty_string($value) ? null : $value;
	}

	protected function setImageModeAttribute($value)
	{
		$this->attributes['image_mode'] = $value ?? 'cover';
	}

	protected function setImageHeightAttribute($value)
	{
		$this->attributes['image_height'] = null_or_empty_string($value) ? null : $value;
	}

	public function getImageHeightAttribute($value)
	{
		return $value ?? setting('default_single_image_height');
	}

	public function miniCards()
	{
		return $this->hasMany(MiniCard::class);
	}

	public function cards()
	{
		return $this->hasMany(MiniCard::class)->where('active', true)->orderBy('position');
	}

	protected $resetPages = [
		BlocMiniCard::class => [
			'relation' => 'mini_card_group_id',
			'id'       => 'id',
		],
	];
}
