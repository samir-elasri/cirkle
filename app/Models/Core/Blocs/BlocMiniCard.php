<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\MiniCardGroup;
use App\Models\Core\Model;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocMiniCardTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Blocs\BlocMiniCard
 *
 * @property int $id
 * @property int $items_per_row
 * @property int $call_to_action_present
 * @property int|null $mini_card_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read MiniCardGroup|null $miniCardGroup
 * @property-read BlocMiniCardTranslation|null $translation
 * @property-read Collection<int, BlocMiniCardTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $call_to_action_url
 * @method static Builder|Model active()
 * @method static Builder|BlocMiniCard listsTranslations(string $translationField)
 * @method static Builder|BlocMiniCard newModelQuery()
 * @method static Builder|BlocMiniCard newQuery()
 * @method static Builder|BlocMiniCard notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocMiniCard orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocMiniCard orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocMiniCard orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocMiniCard query()
 * @method static Builder|BlocMiniCard translated()
 * @method static Builder|BlocMiniCard translatedIn(?string $locale = null)
 * @method static Builder|BlocMiniCard whereCallToActionPresent($value)
 * @method static Builder|BlocMiniCard whereCreatedAt($value)
 * @method static Builder|BlocMiniCard whereId($value)
 * @method static Builder|BlocMiniCard whereItemsPerRow($value)
 * @method static Builder|BlocMiniCard whereMiniCardGroupId($value)
 * @method static Builder|BlocMiniCard whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocMiniCard whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocMiniCard whereUpdatedAt($value)
 * @method static Builder|BlocMiniCard withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocMiniCard extends BlocModel implements TranslatableContract
{

	use Translatable;

	public $searchFields = ['title', 'content'];
	protected $fillable = [

		'@ Paramètres du bloc mini-fiches',
		'label',
		'title',
		'title_color',
		'content',
		'mini_card_group_id',
		// 'items_per_row',

		'@ Appel à l\'action',
		'call_to_action_present',
		'call_to_action_label',
		'call_to_action_url',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active'
	];

	protected array $toggleFields = [
		'call_to_action_label' => 'form.call_to_action_present[1].checked',
		'call_to_action_url'   => 'form.call_to_action_present[1].checked'
	];

	public $translatedAttributes = [
		'title',
		'content',
		'call_to_action_label',
		'call_to_action_url',
	];

	protected array $niceNames = [
		'align'                  => 'Position de l\'élèment multimédia',
		'mini_card_group_id'     => 'Regroupement de mini-fiches',
		'items_per_row'          => 'Nombre par rangée',
		'call_to_action_present' => 'Présent',
		'call_to_action_label'   => 'Libellé',
		'call_to_action_url'     => 'URL',
	];

	protected array $customFields = [
		'content' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		]
	];

	protected array $enum = [
		'media_position' => [
			'top'    => 'Au-dessus du texte',
			'bottom' => 'En-dessous du texte',
			'behind' => 'Derriere le texte',
		]
	];

	protected array $grid = ['label', 'title'];

	public function getFieldPlaceholder($field)
	{
		if ($field === 'items_per_row') {
			return 'Automatique';
		}
		return parent::getFieldPlaceholder($field);
	}

	public function toArray(): array
	{
		$arr = parent::toArray();
		$arr['group'] = $this->miniCardGroup;
		return $arr;
	}

	/**
	 * @return BelongsTo|MiniCardGroup
	 */
	public function miniCardGroup()
	{
		return $this->belongsTo(MiniCardGroup::class);
	}
}
