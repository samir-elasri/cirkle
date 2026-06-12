<?php

namespace App\Models\Translations;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Translations\SettingTranslation
 *
 * @property int $id
 * @property int $setting_id
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
 * @property string $locale
 * @method static Builder|SettingTranslation newModelQuery()
 * @method static Builder|SettingTranslation newQuery()
 * @method static Builder|SettingTranslation query()
 * @method static Builder|SettingTranslation whereAndroidInstallText($value)
 * @method static Builder|SettingTranslation whereAppleInstallText($value)
 * @method static Builder|SettingTranslation whereCompanyAddress($value)
 * @method static Builder|SettingTranslation whereCompanyName($value)
 * @method static Builder|SettingTranslation whereConfirmButtonText($value)
 * @method static Builder|SettingTranslation whereCopyrightNotice($value)
 * @method static Builder|SettingTranslation whereCorpoStatement($value)
 * @method static Builder|SettingTranslation whereEmailFooterText($value)
 * @method static Builder|SettingTranslation whereEmailHeaderImage($value)
 * @method static Builder|SettingTranslation whereFooterAbout($value)
 * @method static Builder|SettingTranslation whereFooterContactDetails($value)
 * @method static Builder|SettingTranslation whereFooterPartnersTitle($value)
 * @method static Builder|SettingTranslation whereId($value)
 * @method static Builder|SettingTranslation whereLocale($value)
 * @method static Builder|SettingTranslation whereMainLogoImage($value)
 * @method static Builder|SettingTranslation whereMaintenanceText($value)
 * @method static Builder|SettingTranslation whereMobileLogoImage($value)
 * @method static Builder|SettingTranslation whereNegativeRetroaction($value)
 * @method static Builder|SettingTranslation wherePositiveRetroaction($value)
 * @method static Builder|SettingTranslation wherePurchaseConfirmationEmailContent($value)
 * @method static Builder|SettingTranslation wherePurchaseConfirmationEmailTitle($value)
 * @method static Builder|SettingTranslation whereResetEmailContent($value)
 * @method static Builder|SettingTranslation whereResetEmailTitle($value)
 * @method static Builder|SettingTranslation whereSettingId($value)
 * @method static Builder|SettingTranslation whereValidationEmailContent($value)
 * @method static Builder|SettingTranslation whereValidationEmailTitle($value)
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
 * @property string|null $sender_email_name
 * @property string|null $sender_email
 * @property string|null $new_service_proposition_title
 * @property string|null $new_service_proposition_text
 * @property string|null $new_contact_request_title
 * @property string|null $new_contact_request_text
 * @property string|null $low_evaluation_title
 * @property string|null $low_evaluation_text
 * @property string|null $customer_evaluation_title
 * @property string|null $customer_evaluation_text
 * @method static Builder|SettingTranslation whereCustomerEvaluationText($value)
 * @method static Builder|SettingTranslation whereCustomerEvaluationTitle($value)
 * @method static Builder|SettingTranslation whereEstimationDescription($value)
 * @method static Builder|SettingTranslation whereEstimationOrder($value)
 * @method static Builder|SettingTranslation whereEstimationPrice($value)
 * @method static Builder|SettingTranslation whereEstimationTitle($value)
 * @method static Builder|SettingTranslation whereImageDescription($value)
 * @method static Builder|SettingTranslation whereImageOrder($value)
 * @method static Builder|SettingTranslation whereImagePrice($value)
 * @method static Builder|SettingTranslation whereImageTitle($value)
 * @method static Builder|SettingTranslation whereJobOfferDescription($value)
 * @method static Builder|SettingTranslation whereJobOfferOrder($value)
 * @method static Builder|SettingTranslation whereJobOfferPrice($value)
 * @method static Builder|SettingTranslation whereJobOfferTitle($value)
 * @method static Builder|SettingTranslation whereLicenseDescription($value)
 * @method static Builder|SettingTranslation whereLicenseOrder($value)
 * @method static Builder|SettingTranslation whereLicensePrice($value)
 * @method static Builder|SettingTranslation whereLicenseTitle($value)
 * @method static Builder|SettingTranslation whereLowEvaluationText($value)
 * @method static Builder|SettingTranslation whereLowEvaluationTitle($value)
 * @method static Builder|SettingTranslation whereNewContactRequestText($value)
 * @method static Builder|SettingTranslation whereNewContactRequestTitle($value)
 * @method static Builder|SettingTranslation whereNewServicePropositionText($value)
 * @method static Builder|SettingTranslation whereNewServicePropositionTitle($value)
 * @method static Builder|SettingTranslation wherePromotionDescription($value)
 * @method static Builder|SettingTranslation wherePromotionOrder($value)
 * @method static Builder|SettingTranslation wherePromotionPrice($value)
 * @method static Builder|SettingTranslation wherePromotionTitle($value)
 * @method static Builder|SettingTranslation whereSenderEmail($value)
 * @method static Builder|SettingTranslation whereSenderEmailName($value)
 * @mixin Eloquent
 */
class SettingTranslation extends TranslationModel {
	public $timestamps = false;
}