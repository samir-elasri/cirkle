<?php

namespace App\Models\Core;

use App\Models\Translations\SettingTranslation;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Core\Setting
 *
 * @property int $id
 * @property string|null $news_default_image
 * @property string|null $events_default_image
 * @property int $header_show_menu_corpo
 * @property string|null $sender_email_name
 * @property string|null $sender_email
 * @property string|null $reception_email
 * @property int $optimal_content_width
 * @property int $default_bloc_inner_spacing
 * @property int $default_bloc_spacing
 * @property int $default_page_top_spacing
 * @property int $default_footer_top_spacing
 * @property int $default_single_image_height
 * @property int $footer_zone_width_1
 * @property int $footer_zone_width_2
 * @property int $footer_zone_width_3
 * @property int $footer_zone_width_4
 * @property int $maintenance
 * @property string|null $tps_number
 * @property float $default_tps
 * @property string|null $tvq_number
 * @property float $default_tvq
 * @property int|null $pub_group_id
 * @property int|null $socials_mini_card_group_id
 * @property int|null $partner_mini_card_group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $collection_name
 * @property-read string $label
 * @property-read mixed $pubs
 * @property-read SearchResult $search_result
 * @property-read MiniCardGroup|null $partnerMiniCardGroup
 * @property-read PubGroup|null $pubGroup
 * @property-read MiniCardGroup|null $socialsMiniCardGroup
 * @property-read SettingTranslation|null $translation
 * @property-read Collection<int, SettingTranslation> $translations
 * @property-read int|null $translations_count
 * @property string|null $corpo_statement
 * @property string|null $main_logo_image
 * @property string|null $mobile_logo_image
 * @property string|null $copyright_notice
 * @property string|null $footer_about
 * @property string|null $footer_contact_details
 * @property string|null $footer_partners_title
 * @property string|null $maintenance_text
 * @property string|null $confirm_button_text
 * @property string|null $positive_retroaction
 * @property string|null $negative_retroaction
 * @property string|null $validation_email_title
 * @property string|null $validation_email_content
 * @property string|null $reset_email_title
 * @property string|null $reset_email_content
 * @property string|null $purchase_confirmation_email_title
 * @property string|null $purchase_confirmation_email_content
 * @property string|null $email_footer_text
 * @property string|null $email_header_image
 * @property string|null $company_name
 * @property string|null $company_address
 * @property string|null $android_install_text
 * @property string|null $apple_install_text
 * @method static Builder|Model active()
 * @method static Builder|Setting listsTranslations(string $translationField)
 * @method static Builder|Setting newModelQuery()
 * @method static Builder|Setting newQuery()
 * @method static Builder|Setting notTranslatedIn(?string $locale = null)
 * @method static Builder|Setting orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Setting orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Setting orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|Setting query()
 * @method static Builder|Setting translated()
 * @method static Builder|Setting translatedIn(?string $locale = null)
 * @method static Builder|Setting whereCreatedAt($value)
 * @method static Builder|Setting whereDefaultBlocInnerSpacing($value)
 * @method static Builder|Setting whereDefaultBlocSpacing($value)
 * @method static Builder|Setting whereDefaultFooterTopSpacing($value)
 * @method static Builder|Setting whereDefaultPageTopSpacing($value)
 * @method static Builder|Setting whereDefaultSingleImageHeight($value)
 * @method static Builder|Setting whereDefaultTps($value)
 * @method static Builder|Setting whereDefaultTvq($value)
 * @method static Builder|Setting whereEventsDefaultImage($value)
 * @method static Builder|Setting whereFooterZoneWidth1($value)
 * @method static Builder|Setting whereFooterZoneWidth2($value)
 * @method static Builder|Setting whereFooterZoneWidth3($value)
 * @method static Builder|Setting whereFooterZoneWidth4($value)
 * @method static Builder|Setting whereHeaderShowMenuCorpo($value)
 * @method static Builder|Setting whereId($value)
 * @method static Builder|Setting whereMaintenance($value)
 * @method static Builder|Setting whereNewsDefaultImage($value)
 * @method static Builder|Setting whereOptimalContentWidth($value)
 * @method static Builder|Setting wherePartnerMiniCardGroupId($value)
 * @method static Builder|Setting wherePubGroupId($value)
 * @method static Builder|Setting whereReceptionEmail($value)
 * @method static Builder|Setting whereSenderEmail($value)
 * @method static Builder|Setting whereSenderEmailName($value)
 * @method static Builder|Setting whereSocialsMiniCardGroupId($value)
 * @method static Builder|Setting whereTpsNumber($value)
 * @method static Builder|Setting whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|Setting whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|Setting whereTvqNumber($value)
 * @method static Builder|Setting whereUpdatedAt($value)
 * @method static Builder|Setting withTranslation()
 * @property string|null $cities_serviced_label
 * @property string|null $state_serviced_label
 * @property string|null $country_serviced_label
 * @property string|null $default_profile_image
 * @property string|null $tps_text
 * @property string|null $tvq_text
 * @property string|null $license_title
 * @property string|null $license_description
 * @property string|null $license_price
 * @property string|null $license_order
 * @property string|null $promotion_title
 * @property string|null $promotion_description
 * @property string|null $promotion_price
 * @property string|null $promotion_order
 * @property string|null $image_title
 * @property string|null $image_description
 * @property string|null $image_price
 * @property string|null $image_order
 * @property string|null $estimation_title
 * @property string|null $estimation_description
 * @property string|null $estimation_price
 * @property string|null $estimation_order
 * @property string|null $job_offer_title
 * @property string|null $job_offer_description
 * @property string|null $job_offer_price
 * @property string|null $job_offer_order
 * @property string|null $new_service_proposition_title
 * @property string|null $new_service_proposition_text
 * @property string|null $new_contact_request_title
 * @property string|null $new_contact_request_text
 * @property string|null $low_evaluation_title
 * @property string|null $low_evaluation_text
 * @property string|null $customer_evaluation_title
 * @property string|null $customer_evaluation_text
 * @method static Builder|Setting whereCitiesServicedLabel($value)
 * @method static Builder|Setting whereCountryServicedLabel($value)
 * @method static Builder|Setting whereDefaultProfileImage($value)
 * @method static Builder|Setting whereStateServicedLabel($value)
 * @method static Builder|Setting whereTpsText($value)
 * @method static Builder|Setting whereTvqText($value)
 * @mixin Eloquent
 */
class Setting extends Model implements TranslatableContract
{
	use Translatable;

	public static $instance;

	public bool $isAjaxEnabled = true;

	protected $fillable = [

		'@Paramètres généraux',
		'main_logo_image',
		'mobile_logo_image',
		'company_name',
		'company_address',
		//		'news_default_image',
		//		'events_default_image',
		'header_show_menu_corpo',
		'corpo_statement',
		'default_single_image_height',

		"@ Page d'accueil",
		'home_client_advantage_title1',
		'home_client_advantage_title2',
		'home_client_advantage_content',
		'home_client_link1_url',
		'home_client_link1_label',
		'home_client_link2_url',
		'home_client_link2_label',
		'home_provider_advantage_title1',
		'home_provider_advantage_title2',
		'home_provider_advantage_content',
		'home_provider_link1_url',
		'home_provider_link1_label',
		'home_provider_link2_url',
		'home_provider_link2_label',

		'@ Courriels',
		'sender_email_name',
		'sender_email',
		'reception_email',
		'email_header_image',
		'email_footer_text',

		'@ Text installation',
		'android_install_text',
		'apple_install_text',

		'@ Courriel de validation',
		'validation_email_title',
		'validation_email_content',

		'@ Réinitialisation de mot de passe',
		'reset_email_title',
		'reset_email_content',

		'@ Courriel de confirmation d\'achat',
		'purchase_confirmation_email_title',
		'purchase_confirmation_email_content',

		'pub_group_id',
		'socials_mini_card_group_id',
		'partner_mini_card_group_id',

		'@Achats',
		'tps_text',
		'platform_tps',
		'tvq_text',
		'platform_tvq',
		'confirm_button_text',
		'positive_retroaction',
		'negative_retroaction',

		'@Options profil',
		'default_profile_image',
		'cities_serviced_label',
		'state_serviced_label',
		'country_serviced_label',
		'license_title',
		'license_description',
		'license_price',
		'license_order',
		'promotion_title',
		'promotion_description',
		'promotion_price',
		'promotion_order',
		'image_title',
		'image_description',
		'image_price',
		'image_order',
		'estimation_title',
		'estimation_description',
		'estimation_price',
		'estimation_order',
		'job_offer_title',
		'job_offer_description',
		'job_offer_price',
		'job_offer_order',
		'url_title',
		'url_description',
		'url_price',
		'url_order',
		'url_month_duration',
		'registration_fee',
		'registration_fee_title',

		'diploma_title',
		'diploma_description',
		'diploma_price',
		'diploma_order',

		'@Courriel admin nouvel proposition de service',
		'new_service_proposition_title',
		'new_service_proposition_text',

		'@Courriel fournisseur nouvelle demande de contact',
		'new_contact_request_title',
		'new_contact_request_text',

		'@Courriel admin évaluation inférieure à 2 étoiles',
		'low_evaluation_title',
		'low_evaluation_text',

		'@Courriel admin propos injurieux',
		'insulting_evaluation_title',
		'insulting_evaluation_content',

		'@Courriel évaluation pour client',
		'customer_evaluation_title',
		'customer_evaluation_text',

        '@ Courriel notification de recherches sauvegardées',
        'search_notification_title',
        'search_notification_text',

		'@ Pied de page',
		'copyright_notice',
		'footer_about',
		//'footer_contact_details',
		//'footer_partners_title',

		'@ Paramètres avancés',
		'optimal_content_width',
		'default_page_top_spacing',
		'default_footer_top_spacing',
		'default_bloc_inner_spacing',
		'default_bloc_spacing',
		// 'footer_zone_width_1',
		// 'footer_zone_width_2',
		// 'footer_zone_width_3',
		// 'footer_zone_width_4',

		'@ Maintenance',
		'maintenance',
		'maintenance_text',
	];

	public $translatedAttributes = [
		'maintenance_text',
		'email_header_image',
		'corpo_statement',
		'main_logo_image',
		'mobile_logo_image',
		'copyright_notice',
		'footer_about',
		'footer_contact_details',
		'footer_partners_title',
		'confirm_button_text',
		'positive_retroaction',
		'negative_retroaction',
		'validation_email_title',
		'validation_email_content',
		'reset_email_title',
		'reset_email_content',
		'purchase_confirmation_email_title',
		'purchase_confirmation_email_content',
		'email_footer_text',
		'android_install_text',
		'apple_install_text',
		'company_name',
		'company_address',
		//
		'license_title',
		'license_description',
		'promotion_title',
		'promotion_description',
		'image_title',
		'image_description',
		'estimation_title',
		'estimation_description',
		'job_offer_title',
		'job_offer_description',
		'diploma_title',
		'diploma_description',
		'url_title',
		'url_description',
		'sender_email_name',
		'sender_email',
		'new_service_proposition_title',
		'new_service_proposition_text',
		'new_contact_request_title',
		'new_contact_request_text',
		'low_evaluation_title',
		'low_evaluation_text',
		'insulting_evaluation_title',
		'insulting_evaluation_content',
		'customer_evaluation_title',
		'customer_evaluation_text',
        'search_notification_title',
        'search_notification_text',
        'url_description',
		'url_title',
		'registration_fee_title',
		'home_client_advantage_title1',
		'home_client_advantage_title2',
		'home_client_advantage_content',
		'home_client_link1_url',
		'home_client_link1_label',
		'home_client_link2_url',
		'home_client_link2_label',
		'home_provider_advantage_title1',
		'home_provider_advantage_title2',
		'home_provider_advantage_content',
		'home_provider_link1_url',
		'home_provider_link1_label',
		'home_provider_link2_url',
		'home_provider_link2_label',
	];

	protected array $niceNames = [
		'maintenance'                => 'Mettre le site en maintenance ?',
		'maintenance_text'           => 'Texte pour la maintenance',
		'corpo_statement'            => 'Énoncé pour menu corpo',
		'main_logo_image'            => 'Logo principal',
		'mobile_logo_image'          => 'Logo mobile',
		'default_page_top_spacing'   => 'Espacement par défaut au-dessus d\'une page',
		'default_footer_top_spacing' => 'Espacement par défaut du pied de page',
		'default_bloc_spacing'       => 'Espacement par défaut entre les blocs',
		'default_bloc_inner_spacing' => 'Espacement par défaut dans les blocs',
		'menu_is_sticky'             => 'Menu sticky',
		'socials_mini_card_group_id' => 'Regroupement de mini fiches pour les réseaux sociaux',
		'pub_group_id'               => 'Regroupement publicités internes par défaut',
		'news_default_image'         => 'Image par défaut pour les nouvelles',
		'events_default_image'       => 'Image par défaut pour les évènements',
		'search_enabled'             => 'Recherche',
		'header_show_menu_corpo'     => 'Afficher le menu corpo dans l\'entête',
		'footer_about'               => 'Zone à coordonnées / propos',
		'footer_partners_title'      => 'Titre pour partenaires',
		'footer_contact_details'     => 'Zone coordonnées',
		'partner_mini_card_group_id' => 'Regroupement mini-fiches pour partenaires',
		'copyright_notice'           => 'Notice de droits d\'auteur',
		'sender_email_name'          => 'Nom de l’envoyeur de courriels',
		'sender_email'               => 'Adresse de l’envoyeur de courriels',
		'optimal_content_width'      => 'Largeur optimal du contenu',
		'footer_zone_width_1'        => 'Largeur de la zone 1 (pied de page)',
		'footer_zone_width_2'        => 'Largeur de la zone 2 (pied de page)',
		'footer_zone_width_3'        => 'Largeur de la zone 3 (pied de page)',
		'footer_zone_width_4'        => 'Largeur de la zone 4 (pied de page)',
		'default_tps'                => 'TPS (%)',
		'tps_number'                 => 'Numéro de la TPS',
		'default_tvq'                => 'TVQ (%)',
		'tvq_number'                 => 'Numéro de la TVQ',
		'confirm_button_text'        => 'Texte pour accompagner bouton confimer dans panier',
		'positive_retroaction'       => 'Rétroaction positive après achat',
		'negative_retroaction'       => 'Rétroaction négative après achat',
		'reception_email'            => 'Adresse admin du receveur de courriel',

		'validation_email_title'   => 'Titre',
		'validation_email_content' => 'Contenu',

		'reset_email_title'   => 'Titre',
		'reset_email_content' => 'Contenu',

		'purchase_confirmation_email_title'   => 'Titre',
		'purchase_confirmation_email_content' => 'Contenu',

		'email_footer_text' => 'Footer  de courriels',

		'email_header_image'          => 'Image d\'en-tête (570px)',
		'default_single_image_height' => 'Hauteur d\'image par défaut pour les mini fiches',

		'android_install_text'   => 'Installer cette application – android',
		'apple_install_text'     => 'Installer cette application – apple',
		'company_name'           => 'Nom de l\'entreprise',
		'company_address'        => 'Adresse de l\'entreprise',

		//		new fields
		'cities_serviced_label'  => 'Libellé zone servie type ville',
		'state_serviced_label'   => 'Libellé zone desservie type province',
		'country_serviced_label' => 'Libellé zone desservie type pays',
		'default_profile_image'  => 'Photo de profil fournisseur par défaut',
		'tps_text'               => 'Texte TPS',
		'tvq_text'               => 'Texte TVQ',
		'platform_tps'           => 'Tps de la platforme',
		'platform_tvq'           => 'Tvq de la platforme',


		// 		new translatables

		'license_title'                 => 'Titre option profil permis',
		'license_description'           => 'Description option profil permis',
		'license_price'                 => 'Prix option profil $ permis',
		'license_order'                 => 'Ordre option de profil permis',
		'diploma_title'                 => 'Titre option profil diplômes',
		'diploma_description'           => 'Description option profil diplômes',
		'diploma_price'                 => 'Prix option profil $ diplômes',
		'diploma_order'                 => 'Ordre option de profil diplômes',
		'promotion_title'               => 'Titre option profil promotion',
		'promotion_description'         => 'Description option profil promotion',
		'promotion_price'               => 'Prix option profil $ promotion',
		'promotion_order'               => 'Ordre option de profil promotion',
		'image_title'                   => 'Titre option profil photo',
		'image_description'             => 'Description option profil photos',
		'image_price'                   => 'Prix option profil $ photos',
		'image_order'                   => 'Ordre option de profil photos',
		'estimation_title'              => 'Titre option profil Estimation',
		'estimation_description'        => 'Description option profil Estimation',
		'estimation_price'              => 'Prix option profil $ Estimation',
		'estimation_order'              => 'Ordre option de profil Estimation',
		'job_offer_title'               => 'Titre option profil Offre d’emploi',
		'job_offer_description'         => 'Description option profil offre d’emploi',
		'job_offer_price'               => 'Prix option profil $ offre d’emploi',
		'job_offer_order'               => 'Ordre option de profil offre d’emploi',
		'url_price'                     => 'Prix option de profil site web',
		'url_order'                     => 'Ordre option de profil site web',
		'url_description'               => 'Description option de profil site web',
		'url_title'                     => 'Titre option de profil site web',
		'new_service_proposition_title' => 'Titre',
		'new_service_proposition_text'  => 'Description',
		'new_contact_request_title'     => 'Titre',
		'new_contact_request_text'      => 'Description',
		'low_evaluation_title'          => 'Titre',
		'low_evaluation_text'           => 'Description',
		'insulting_evaluation_title'    => 'Titre',
		'insulting_evaluation_content'  => 'Description',
		'customer_evaluation_title'     => 'Titre',
		'customer_evaluation_text'      => 'Description',
        'search_notification_title'     => 'Titre',
        'search_notification_text'      => 'Description',
        'url_month_duration'            => 'Durée en mois pour site web',
		'registration_fee'              => 'Frais d\'inscription ($)',
		'registration_fee_title'        => 'Libellé des frais d\'inscription',
		'home_client_advantage_title1' => 'Zone client - titre 1',
		'home_client_advantage_title2' => 'Zone client - titre 2',
		'home_client_advantage_content' => 'Zone client - contenu',
		'home_client_link1_url'  => 'Zone client - URL lien 1',
		'home_client_link1_label'  => 'Zone client - Libellé lien 1',
		'home_client_link2_url'  => 'Zone client - URL lien 2',
		'home_client_link2_label'   => 'Zone client - Libellé lien 2',
		'home_provider_advantage_title1'  => 'Zone Fournisseurs - titre 1',
		'home_provider_advantage_title2'  => 'Zone Fournisseurs - titre 2',
		'home_provider_advantage_content'  => 'Zone Fournisseurs - contenu',
		'home_provider_link1_url'  => 'Zone Fournisseurs - URL lien 1',
		'home_provider_link1_label'  => 'Zone Fournisseurs - Libellé lien 1',
		'home_provider_link2_url'  => 'Zone Fournisseurs - URL lien 2',
		'home_provider_link2_label'  => 'Zone Fournisseurs - Libellé lien 2',

	];

	protected array $rules = [
		'main_logo_image'      => 'image|mimes:jpeg,jpg,png,gif',
		'mobile_logo_image'    => 'image|mimes:jpeg,jpg,png,gif',
		'news_default_image'   => 'image|mimes:jpeg,jpg,png,gif',
		'events_default_image' => 'image|mimes:jpeg,jpg,png,gif',
		'email_header_image'   => 'image|mimes:jpeg,jpg,png',
	];

	protected array $customFields = [
		'maintenance_text'                    => ['widget' => 'wysiwyg'],
		'footer_about'                        => ['widget' => 'wysiwyg'],
		'footer_contact_details'              => ['widget' => 'wysiwyg'],
		'purchase_confirmation_email_content' => ['widget' => 'wysiwyg'],
		'email_footer_text'                   => ['widget' => 'wysiwyg'],
		'android_install_text'                => ['widget' => 'wysiwyg'],
		'apple_install_text'                  => ['widget' => 'wysiwyg'],
		'company_address'                     => ['widget' => 'wysiwyg'],
		'low_evaluation_text'                 => ['widget' => 'wysiwyg'],
		'insulting_evaluation_content'        => ['widget' => 'wysiwyg'],
		'validation_email_content'            => [
			'widget'  => 'wysiwyg-email',
			'options' => [
				'helper' => 'Balises disponibles :&#013;&#010;{{first_name}}&#013;&#010;{{last_name}}&#013;&#010;{{url_activation}}&#013;&#010;'
			]
		],
		'reset_email_content'                 => [
			'widget'  => 'wysiwyg-email',
			'options' => [
				'helper' => 'Balises disponibles :&#013;&#010;{{first_name}}&#013;&#010;{{last_name}}&#013;&#010;{{url_activation}}&#013;&#010;'
			]
		],
        'customer_evaluation_text' => ['widget' => 'wysiwyg'],
        'search_notification_text' => ['widget' => 'wysiwyg'],
		'socials_mini_card_group_id'          => [
			'widget'  => 'associate_entity',
			'options' => [
				'associate_class' => MiniCardGroup::class
			]
		],
		'partner_mini_card_group_id'          => [
			'widget'  => 'associate_entity',
			'options' => [
				'associate_class' => MiniCardGroup::class
			]
		],
		'home_client_advantage_content' => ['widget' => 'wysiwyg'],
		'home_provider_advantage_content' => ['widget' => 'wysiwyg'],
	];

	protected $appends = [
		'label',
		'email_to'
	];

	protected function getLabelAttribute(): string
	{
		return 'Configurations générales';
	}

	protected function getPubsAttribute()
	{
		if ($this->pub_group_id) {
			$pubgroup = $this->pubGroup;

			if ($pubgroup) {
				return $pubgroup->pubs;
			}
		}

		return new Collection;
	}

	/**
	 * @return BelongsTo|PubGroup
	 */
	public function pubGroup()
	{
		return $this->belongsTo(PubGroup::class);
	}

	/**
	 * @return BelongsTo|MiniCardGroup
	 */
	public function socialsMiniCardGroup()
	{
		return $this->belongsTo(MiniCardGroup::class);
	}

	/**
	 * @return BelongsTo|MiniCardGroup
	 */
	public function partnerMiniCardGroup()
	{
		return $this->belongsTo(MiniCardGroup::class);
	}

	/**
	 * @return Setting
	 */
	public static function getInstance(): Setting
	{
		if (!static::$instance) {
			static::$instance = static::find(1);
		}
		return static::$instance;
	}

	public function getSortedProfileOptionsAttribute(): array
	{
		$profileOptions = [
			'license',
			'diploma',
			'promotion',
			'image',
			'estimation',
			'job_offer',
			'url'
		];

		$result = [];
		$i = 10;
		$sub = Auth::guard('subscribers')->user();


		foreach ($profileOptions as $option) {
			$position = "{$option}_order";
			$optionActive = "profile_{$option}_active";
			if (($sub && !$sub->$optionActive) || !$sub) {
				$result[$this->$position ?? $i] = $option;
			}
			$i++;
		}

		ksort($result);

		return $result;
	}
}
