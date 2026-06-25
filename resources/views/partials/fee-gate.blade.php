{{--
    Porte d'acceptation des frais de la fiche de compétence (feature #6).
    S'affiche en tête du flux de compétence — juste après le choix de la profession,
    AVANT que le formulaire de services ne soit rendu (spec : refus → le reste reste caché).

    Le bouton « J'accepte » appelle window.cirkleAcceptFee(id) défini dans pages/register/step-2 :
    enregistre l'acceptation en session puis recharge ce conteneur avec le vrai formulaire.

    $serviceCategory : la profession choisie (porte fiche_fee + fiche_fee_text du fichier MASTER)
--}}
{{-- Styles inline temporaires : la rebuild SCSS est gelée (voir docs/gap-map.md) --}}
<style>
    .fee-gate { border: 2px solid #e6b800; background: #fff9e6; border-radius: 6px; padding: 1.25em 1.5em; margin-top: 1em; }
    .fee-gate__title { font-weight: 700; text-transform: uppercase; margin-bottom: .5em; }
    .fee-gate__amount { font-size: 1.6em; font-weight: 700; margin: .35em 0 .75em; }
    .fee-gate__text { white-space: pre-wrap; line-height: 1.6; margin-bottom: 1em; }
    .fee-gate__error { color: #b00020; margin-top: .6em; display: none; }
</style>

<div class="fee-gate" data-fee-gate>
    <div class="fee-gate__title">{{ __('auth.register.fee_title') }}</div>

    {{-- Frais UNIQUE selon la plateforme (résidentiel / B2B). Le montant affiché suit le
         bouton « choix de la plateforme » de l'étape 2 (mis à jour par le script de step-2).
         Les deux montants sont fournis en data-* pour basculer sans recharger. --}}
    @php
        $ckFeeRes = \App\Support\FicheFee::residential();
        $ckFeeBus = \App\Support\FicheFee::business();
        $ckFeeDefault = \App\Support\FicheFee::for($serviceCategory->provider_type);
    @endphp
    <div class="fee-gate__amount"
         data-fee-residential="{{ prettyPrice($ckFeeRes) }}"
         data-fee-business="{{ prettyPrice($ckFeeBus) }}">{{ prettyPrice($ckFeeDefault) }}</div>

    @if (!empty($serviceCategory->fiche_fee_text))
        <div class="fee-gate__text">{!! nl2br(e($serviceCategory->fiche_fee_text)) !!}</div>
    @endif

    {{-- Bouton NON-JS : soumet le formulaire principal avec un drapeau; storeStep2 enregistre
         l'acceptation et recharge l'étape 2 avec le 2350. (Fiable même si Unpoly/AJAX empêche
         l'exécution des <script> inline — voir [[cirkle-blade-no-compile-in-script]].) --}}
    <button type="submit" name="ck_fee_action" value="accept" class="call-to-action">
        {{ __('auth.register.fee_accept') }}
    </button>
</div>
