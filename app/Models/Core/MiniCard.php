<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocMiniCard;
use App\Models\Translations\MiniCardTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\MiniCard
 *
 * @property int $id
 * @property string|null $label
 * @property string|null $align
 * @property int $call_to_action_present
 * @property int|null $position
 * @property int $active
 * @property int|null $mini_card_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read mixed $width
 * @property-read MiniCardGroup|null $group
 * @property-read MiniCardGroup|null $miniCardGroup
 * @property-read MiniCardTranslation|null $translation
 * @property-read Collection<int, MiniCardTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $image
 * @property string|null $title
 * @property string|null $sub_title
 * @property string|null $text
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @method static Builder|Model active()
 * @method static Builder|MiniCard listsTranslations(string $translationField)
 * @method static Builder|MiniCard newModelQuery()
 * @method static Builder|MiniCard newQuery()
 * @method static Builder|MiniCard notTranslatedIn(?string $locale = null)
 * @method static Builder|MiniCard orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MiniCard orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MiniCard orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|MiniCard query()
 * @method static Builder|MiniCard translated()
 * @method static Builder|MiniCard translatedIn(?string $locale = null)
 * @method static Builder|MiniCard whereActive($value)
 * @method static Builder|MiniCard whereAlign($value)
 * @method static Builder|MiniCard whereCallToActionPresent($value)
 * @method static Builder|MiniCard whereCreatedAt($value)
 * @method static Builder|MiniCard whereId($value)
 * @method static Builder|MiniCard whereLabel($value)
 * @method static Builder|MiniCard whereMiniCardGroupId($value)
 * @method static Builder|MiniCard wherePosition($value)
 * @method static Builder|MiniCard whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|MiniCard whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|MiniCard whereUpdatedAt($value)
 * @method static Builder|MiniCard withTranslation()
 * @mixin Eloquent
 */
class MiniCard extends Model implements TranslatableContract
{
	use Translatable;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	protected $fillable = [
		'@ Paramètres de mini-fiche',
		'mini_card_group_id',
		'label',
		'title',
		'sub_title',
		'text',
		'@ Élément d\'accompagnement multimédia',
		'image',
		'align',
		'@ Appel à l\'action',
		'call_to_action_present',
		'call_to_action_label',
		'call_to_action_url',
		'@ Paramètres généraux',
		'active',
	];

	protected array $toggleFields = [
		'call_to_action_label' => 'form.call_to_action_present[1].checked',
		'call_to_action_url' => 'form.call_to_action_present[1].checked'
	];

	public $translatedAttributes = [
		'image',
		'title',
		'sub_title',
		'text',
		'call_to_action_label',
		'call_to_action_url',
	];

	protected array $niceNames = [
		'mini_card_group_id' => 'Regroupement mini-fiches',
		'label' => 'Titre interne',
		'image' => 'Image',
		'title' => 'Titre',
		'sub_title' => 'Sous-titre',
		'text' => 'Texte',
		'align' => 'Position de l\'élèment multimédia',
		'call_to_action_present' => 'Présent',
		'call_to_action_label' => 'Libellé',
		'call_to_action_url' => 'Url',
		'width' => 'Largeur en pixels'
	];

	protected array $customFields = [
		'text' => ['widget' => 'wysiwyg'],
	];

	protected array $grid = ['image', 'label', 'title', 'active'];

	protected array $enum = [
		'align' => [
			'top' => 'Haut',
			'bottom' => 'Bas',
			'back' => 'Arrière'
		],
	];

	protected $resetPages = [
		BlocMiniCard::class => [
			'relation' => 'mini_card_group_id',
			'id' => 'mini_card_group_id',
		],
	];

	protected function getWidthAttribute($value)
	{
		return $value == 0 ? 10 : $value;
	}

	/**
	 * @return BelongsTo|MiniCardGroup
	 */
	public function group()
	{
		return $this->belongsTo(MiniCardGroup::class);
	}

	/**
	 * @return BelongsTo|MiniCardGroup
	 */
	public function miniCardGroup()
	{
		return $this->belongsTo(MiniCardGroup::class);
	}
}
