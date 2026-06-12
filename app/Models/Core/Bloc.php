<?php

namespace App\Models\Core;

use App\Models\Core\Blocs\BlocModel;
use App\Models\Core\Blocs\BlocTableOfContent;
use Arr;
use Error;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Str;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use ModelUtility;
use RoutingUtility;
use Session;
use View;

use App\Models\Core\Blocs\BlocText;
use App\Models\Core\Blocs\BlocImage;
use App\Models\Core\Blocs\BlocDocument;
use App\Models\Core\Blocs\BlocMiniCard;
use App\Models\Core\Blocs\BlocForm;
use App\Models\Core\Blocs\BlocVideo;
use App\Models\Core\Blocs\BlocGallery;
use App\Models\Core\Blocs\BlocPortfolio;
use App\Models\Core\Blocs\BlocGoogleMap;
use App\Models\Core\Blocs\BlocAudio;

/**
 * App\Models\Core\Bloc
 *
 * @property int                                               $id
 * @property string|null                                       $label
 * @property int|null                                          $pageable_id
 * @property string                                            $pageable_type
 * @property int|null                                          $blocable_id
 * @property string                                            $blocable_type
 * @property int|null                                          $top_spacing
 * @property string|null                                       $title_color
 * @property string|null                                       $bg_color
 * @property int                                               $half_width_mode
 * @property int                                               $bg_bleed
 * @property int|null                                          $position
 * @property int                                               $active
 * @property-read string|null                                  $bg_type
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $blocable
 * @property-read mixed                                        $bloc_collection
 * @property-read mixed                                        $collection_name
 * @property-read mixed                                        $model
 * @property-read SearchResult                                 $search_result
 * @property-read mixed                                        $target
 * @property-read mixed                                        $title_grid
 * @property-read mixed                                        $url
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $pageable
 * @method static Builder|Model active()
 * @method static Builder|Bloc newModelQuery()
 * @method static Builder|Bloc newQuery()
 * @method static Builder|Bloc query()
 * @method static Builder|Bloc whereActive($value)
 * @method static Builder|Bloc whereBgBleed($value)
 * @method static Builder|Bloc whereBgColor($value)
 * @method static Builder|Bloc whereBlocableId($value)
 * @method static Builder|Bloc whereBlocableType($value)
 * @method static Builder|Bloc whereHalfWidthMode($value)
 * @method static Builder|Bloc whereId($value)
 * @method static Builder|Bloc whereLabel($value)
 * @method static Builder|Bloc wherePageableId($value)
 * @method static Builder|Bloc wherePageableType($value)
 * @method static Builder|Bloc wherePosition($value)
 * @method static Builder|Bloc whereTitleColor($value)
 * @method static Builder|Bloc whereTopSpacing($value)
 * @property-read mixed $blocable_type_grid
 * @mixin Eloquent
 */
class Bloc extends Model
{
	public const VIEW_NAMES = [
		BlocText::class           => 'text',
		BlocImage::class          => 'image',
		BlocDocument::class       => 'document',
		BlocMiniCard::class       => 'mini-cards',
		BlocForm::class           => 'form',
		BlocVideo::class          => 'video',
		BlocGallery::class        => 'gallery',
		BlocPortfolio::class      => 'portfolio',
		BlocGoogleMap::class      => 'google-maps',
		BlocAudio::class          => 'audio',
		BlocTableOfContent::class => 'table-of-content'
	];

	public $timestamps = false;

	public string $order_default = 'position';

	public string $order_direction = 'ASC';

	protected array $morphClasses = [
		'pageable' => [
			Page::class       => 'Page',
			News::class       => 'Nouvelle',
			BasicEvent::class => 'Événement',
			Product::class    => 'Produit',
		],
		'blocable' => [
			BlocText::class           => 'Bloc de texte',
			BlocImage::class          => 'Bloc image',
			BlocDocument::class       => 'Bloc document',
			BlocMiniCard::class       => 'Bloc de mini-fiches',
			BlocForm::class           => 'Bloc formulaires',
			BlocVideo::class          => 'Bloc vidéo',
			BlocGallery::class        => 'Bloc Gallerie',
			BlocPortfolio::class      => 'Bloc Portfolio',
			BlocGoogleMap::class      => 'Bloc google maps',
			BlocAudio::class          => 'Bloc audio',
			BlocTableOfContent::class => 'Bloc table des matieres'
		]
	];

	/**
	 * RECHERCHE
	 */
	public $searchFields = [
		'blocable'
	];

	protected array $rules = [
		'bloc_type' => 'required'
	];

	protected $fillable = [
		'label',
		'pageable_id',
		'pageable_type',
		'blocable_id',
		'blocable_type',
		'top_spacing',
		'title_color',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'position',
		'active'
	];

	protected array $grid = [
		'label',
		'title_grid',
		'blocable_type_grid',
		'active'
	];

	protected array $niceNames = [
		'title_grid'         => 'Titre',
		'blocable_type_grid' => 'Type',
		'blocable_type'      => 'Type de bloc associé',
		'blocable_id'        => 'Bloc associé',
		'pageable_type'      => 'Type de page associée',
		'pageable_id'        => 'Page associée',
	];

	protected array $enum = [
		'blocable_type' => [
			'bloc_texts'       => 'Texte',
			'bloc_images'      => 'Image',
			'bloc_videos'      => 'Vidéo',
			'bloc_google_maps' => 'Google Maps',
			'bloc_mini_cards'  => 'Mini-fiches',
			'bloc_forms'       => 'Formulaire',
			'bloc_documents'   => 'Document',
			'bloc_galleries'   => 'Galerie',
			'bloc_portfolios'  => 'Portfolio',
			'bloc_audios'      => 'Audio',
		]
	];

	protected array $customFields = [
		'blocable_id' => [
			'widget' => 'skip'
		]
	];

	protected $appends = [
		'title_grid',
		'bloc_collection',
		'target',
		'blocable_type_grid',
		'url'
	];

	public array $positionParentFields = [
		'pageable_id',
		'pageable_type'
	];

	protected function getBlocCollectionAttribute()
	{
		return $this->blocable->collection_name;
	}

	/**
	 * @return Attribute
	 */
	protected function bgType(): Attribute
	{
		return Attribute::make(
			get: function (): ?string {
				if (!empty($this->back_image)) {
					// La valeur retourné doit être différente, afin d'avoir le bon espacement entre les blocs demi-largeur
					return $this->back_image . '-' . random_int(0, PHP_INT_MAX);
				}

				return empty($this->bg_color) ? null : "color-{$this->bg_color}";
			}
		);
	}

	protected function getModelAttribute()
	{
		/** @var Model $model */
		$model = $this->blocable_type;
		return $model ? $model::find($this->blocable_id) : null;
	}

	protected function getTitleGridAttribute()
	{
		return RoutingUtility::isAdmin()
			? optional($this->blocable)->title
			: '';
	}

	protected function getBlocableTypeGridAttribute()
	{
		return Arr::get($this->morphClasses, "blocable.{$this->blocable_type}");
	}

	protected function getUrlAttribute()
	{
		return $this->pageable->url . '#' . $this->target;
	}

	protected function getTargetAttribute()
	{
		return Str::slug($this->blocable->title) . '-' . $this->id;
	}

	/**
	 * @param Page|News|BasicEvent $pageable
	 * @return string
	 */
	public static function renderBlocs($pageable): string
	{
		// Récupère les blocs
		$query = $pageable->blocs()
			->with([
				'blocable' => function (MorphTo $morphTo) {
					$morphTo->morphWith([
						BlocGallery::class   => [
							'gallery'          => static function ($query) {
								$query->active();
							},
							'gallery.elements' => static function ($query) {
								$query->active()->orderBy('position');
							}
						],
						BlocPortfolio::class => [
							'gallery'          => static function ($query) {
								$query->active();
							},
							'gallery.elements' => static function ($query) {
								$query->active()->orderBy('position');
							}
						],
						BlocMiniCard::class  => [
							'miniCardGroup' => static function ($query) {
								$query->active();
							},
							'miniCardGroup.cards'
						],
						BlocForm::class      => [
							'formGenerator',
							'formGenerator.formFields'                     => static function ($query) {
								$query->active()->orderBy('position');
							},
							'formGenerator.formFields.choiceGroup.choices' => static function ($query) {
								$query->active()->orderBy('position');
							},
						],
						BlocGoogleMap::class => [
							'googleMapGroup.googleMaps',
						],
					]);
				},
				'pageable'
			]);

		// Si un admin est connecté, on affiche tous les blocs, autrement on n'affiche que les blocs actifs
		$query = is_admin() && Session::has('show-inactive') ? $query : $query->where('active', true);

		// Ordonne les blocs
		$blocs = $query->orderBy('position')->get();

		$pageable->blocs = $blocs;

		// Espacement par défaut
		$default_top_spacing = setting()->default_bloc_spacing;

		// Données des blocs à rendre
		$data = [];

		// Détermine si en mode demi-largeur
		$half_width_mode = false;

		// Bloc précédent
		$prev = null;

		foreach ($blocs as $bloc) {
			// Prévient le bloc de redemander la relation avec son conteneur
			$bloc->blocable->bloc = $bloc;

			if ($bloc->blocable instanceof BlocTableOfContent) {
				$bloc->blocable->blocs = $blocs;
			}

			// Fusionne les données du conteneur et du bloc
			$curr = array_merge($bloc->toArray(), $bloc->blocable->toArray());

			$curr['bloc'] = $bloc;

			// Défini le nom de la vue à rendre
			$curr['view_name'] = Arr::get(self::VIEW_NAMES, $bloc->blocable_type, $bloc->blocable_type);

			// Défini si la colonne de droite est présente
			$curr['has_right_column'] = Arr::get($pageable, 'has_right_column', false);

			// Est en mode demi-largeur
			$curr['half_width_mode'] = $half_width_mode = $half_width_mode !== 'left'
			&&
			$curr['half_width_mode'] == 1
				? 'left'
				: ($half_width_mode === 'left' ? 'right' : false);

			// Défini l'espacement au-dessus du bloc
			$curr['top_spacing'] = $curr['top_spacing'] === null ? $default_top_spacing : (int)$curr['top_spacing'];

			// Détermine si le bloc a besoin d'espacement à l'intérieur, lorsqu'une couleur de fond est définie
			$curr['need_inner_spacing'] = $curr['bg_type'];

			// Détermine si l'on doit laisser plus d'espacement entre deux blocs demi-largeurs, lorsque les deux couleurs de fonds ne sont pas semblables
			if ($half_width_mode === 'right' && $prev && $prev['bg_type'] != $curr['bg_type']) {
				$curr['need_inner_spacing'] = $curr['bg_type'] !== null;
				$prev['need_inner_spacing'] = $prev['bg_type'] !== null;
			}

			// Si en mode admin, défini le url pour éditer le bloc
			if (is_admin()) {

				$pageable_name = ModelUtility::getCollectionNameFromClassName($bloc->pageable_type);
				$blocable = ModelUtility::getCollectionNameFromClassName($bloc->blocable_type);

				$curr['edit_url'] = adminRouteName("admin.$pageable_name.edit",
					[
						$bloc->pageable_id,
						'blocs',
						$bloc->blocable->id,
						'bloc_type' => $blocable
					]);

			} else {
				$curr['edit_url'] = false;
			}

			$ogPrev = $prev;

			// Défini le bloc suivant et précédent
			if ($prev) {
				end($data);
				$k = key($data);
				$prev['next_bloc'] = $curr;
				$data[$k] = $prev;

				$curr['prev_bloc'] = $prev;
			}

			// Accumule les données
			$data[] = $curr;

			// Conserve les paramètres du bloc courant en tant que paramètres du bloc précédent pour la prochaine itération
			$prev = $curr;
		}

		// Rendu des blocs
		$render = '';

		foreach ($data as $params) {

			$blade_name = false;
			$view_name = $params['view_name'];

			// Détermine si le blade existe
			if (View::exists('blocs.' . $view_name)) {
				$blade_name = 'blocs.' . $view_name;
			} elseif (View::exists('core.blocs.' . $view_name)) {
				$blade_name = 'core.blocs.' . $view_name;
			}

			if ($blade_name) {

				try {
					// Rend le bloc
					$render .= View::make($blade_name)->with($params)->render();
				} catch (Exception|Error $e) {

					View::flushSections();
					$params['error'] = $e;
					$render .= View::make('core.blocs.error')->with($params)->render();
				}
			} else {

				$params['error'] = "Blade not found '" . $view_name . "'";
				$render .= View::make('core.blocs.error')->with($params)->render();
			}
		}

		return $render;
	}

	/**
	 * @return MorphTo
	 */
	public function pageable()
	{
		return $this->morphTo();
	}

	/**
	 * @return MorphTo|BlocModel
	 */
	public function blocable(): MorphTo
	{
		return $this->morphTo();
	}

	public function filterGetRange($query)
	{
		$query->with([
			'blocable',
			'pageable'
		]);
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		$this->blocable->bloc = $this;

		return parent::toArray();
	}
}

View::composer('core.layouts.bloc', function ($view) {
	$view->with(['wait_ready' => $view->wait_ready]);
});
