<?php

namespace App\Models\Core\Blocs;

use App\Models\Core\Bloc;
use App\Models\Core\Forms\FormGenerator;
use App\Models\Core\SearchResult;
use App\Models\Core\Translatable;
use App\Models\Translations\BlocFormTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Core\Blocs\BlocForm
 *
 * @property int $id
 * @property int|null $form_generator_id
 * @property int $use_activation
 * @property string|null $start_datetime
 * @property string|null $end_datetime
 * @property int $email_confirm_send
 * @property string|null $email_confirm_from
 * @property string|null $email_confirm_bcc
 * @property int $email_alert_send
 * @property string|null $email_alert_from
 * @property string|null $email_alert_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Bloc|null $bloc
 * @property-read FormGenerator|null $formGenerator
 * @property-read mixed $collection_name
 * @property-read SearchResult $search_result
 * @property-read BlocFormTranslation|null $translation
 * @property-read Collection<int, BlocFormTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $title
 * @property string|null $content
 * @property string|null $call_to_action_label
 * @property string|null $note
 * @property string|null $message
 * @property string|null $email_confirm_title
 * @property string|null $email_confirm_content
 * @property string|null $email_confirm_name
 * @property string|null $email_alert_title
 * @property string|null $email_alert_content
 * @property string|null $email_alert_name
 * @method static Builder|Model active()
 * @method static Builder|BlocForm listsTranslations(string $translationField)
 * @method static Builder|BlocForm newModelQuery()
 * @method static Builder|BlocForm newQuery()
 * @method static Builder|BlocForm notTranslatedIn(?string $locale = null)
 * @method static Builder|BlocForm orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocForm orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocForm orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|BlocForm query()
 * @method static Builder|BlocForm translated()
 * @method static Builder|BlocForm translatedIn(?string $locale = null)
 * @method static Builder|BlocForm whereCreatedAt($value)
 * @method static Builder|BlocForm whereEmailAlertFrom($value)
 * @method static Builder|BlocForm whereEmailAlertSend($value)
 * @method static Builder|BlocForm whereEmailAlertTo($value)
 * @method static Builder|BlocForm whereEmailConfirmBcc($value)
 * @method static Builder|BlocForm whereEmailConfirmFrom($value)
 * @method static Builder|BlocForm whereEmailConfirmSend($value)
 * @method static Builder|BlocForm whereEndDatetime($value)
 * @method static Builder|BlocForm whereFormGeneratorId($value)
 * @method static Builder|BlocForm whereId($value)
 * @method static Builder|BlocForm whereStartDatetime($value)
 * @method static Builder|BlocForm whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|BlocForm whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|BlocForm whereUpdatedAt($value)
 * @method static Builder|BlocForm whereUseActivation($value)
 * @method static Builder|BlocForm withTranslation()
 * @property-write mixed $active
 * @property-write mixed $bg_bleed
 * @property-write mixed $bg_color
 * @property-write mixed $half_width_mode
 * @property-write mixed $label
 * @property-write mixed $title_color
 * @property-write mixed $top_spacing
 * @mixin Eloquent
 */
class BlocForm extends BlocModel implements TranslatableContract
{

	public function __construct($attributes = [])
	{
		$this->attributes['start_datetime'] = Carbon::now();
		$this->attributes['end_datetime'] = Carbon::now()->addMonth(1);
		parent::__construct($attributes);
	}

	public $searchFields = ['title', 'content'];

	use Translatable;

	protected $fillable = [

		'@ Paramètres du bloc formulaire',
		'label',
		'title',
		'title_color',
		'content',

		'@ Formulaire',
		'form_generator_id',
		'use_activation',
		'start_datetime',
		'end_datetime',
		'call_to_action_label',
		'note',
		'message',

		'@ Envoi d\'un courriel de confirmation',
		'email_confirm_send',
		'email_confirm_name',
		'email_confirm_from',
		'email_confirm_bcc',
		'email_confirm_title',
		'email_confirm_content',

		'@ Envoi d\'un courriel d\'alerte',
		'email_alert_send',
		'email_alert_name',
		'email_alert_from',
		'email_alert_to',
		'email_alert_title',
		'email_alert_content',

		'@ Paramètres généraux',
		'top_spacing',
		'bg_color',
		'bg_bleed',
		'half_width_mode',
		'active',
	];

	protected array $toggleFields = [
		'start_datetime'        => 'form.use_activation[1].checked',
		'end_datetime'          => 'form.use_activation[1].checked',
		'email_confirm_title'   => 'form.email_confirm_send[1].checked',
		'email_confirm_content' => 'form.email_confirm_send[1].checked',
		'email_confirm_name'    => 'form.email_confirm_send[1].checked',
		'email_confirm_from'    => 'form.email_confirm_send[1].checked',
		'email_confirm_bcc'     => 'form.email_confirm_send[1].checked',

		'email_alert_title'   => 'form.email_alert_send[1].checked',
		'email_alert_content' => 'form.email_alert_send[1].checked',
		'email_alert_name'    => 'form.email_alert_send[1].checked',
		'email_alert_from'    => 'form.email_alert_send[1].checked',
		'email_alert_to'      => 'form.email_alert_send[1].checked',
	];

	public $translatedAttributes = [
		'title',
		'content',
		'call_to_action_label',
		'note',
		'message',
		'email_confirm_title',
		'email_confirm_content',
		'email_confirm_name',
		'email_alert_title',
		'email_alert_content',
		'email_alert_name',
	];

	protected array $niceNames = [

		'title'                => 'Titre',
		'content'              => 'Contenu',
		'note'                 => 'Note',
		'message'              => 'Rétroaction positive',
		'bg_color'             => 'Couleur de fond',
		'form_generator_id'    => 'Formulaire désiré',
		'call_to_action_label' => 'Libellé bouton',
		'half_width_mode'      => 'Mode demi-largeur',
		'use_activation'       => 'Utiliser l\'activation',
		'start_datetime'       => 'Date/heure activation',
		'end_datetime'         => 'Date/heure désactivation',

		'email_confirm_send'    => 'Actif',
		'email_confirm_title'   => 'Titre',
		'email_confirm_content' => 'Contenu',
		'email_confirm_name'    => 'Nom envoyeur',
		'email_confirm_from'    => 'Courriel envoyeur',
		'email_confirm_bcc'     => 'Liste d\'adresses BCC (séparé par des \';\')',

		'email_alert_send'    => 'Actif',
		'email_alert_title'   => 'Titre',
		'email_alert_content' => 'Contenu',
		'email_alert_name'    => 'Nom envoyeur',
		'email_alert_from'    => 'Courriel envoyeur',
		'email_alert_to'      => 'Courriel du destinataire',

	];

	protected array $customFields = [
		'content'               => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		],
		'note'                  => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		],
		'email_confirm_content' => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		],
		'email_alert_content'   => [
			'widget'  => 'wysiwyg',
			'options' => ['height' => 150]
		],
	];

	protected array $grid = ['title', 'content'];

	/**
	 * @return BelongsTo|FormGenerator
	 */
	public function formGenerator()
	{
		return $this->belongsTo(FormGenerator::class);
	}
}
