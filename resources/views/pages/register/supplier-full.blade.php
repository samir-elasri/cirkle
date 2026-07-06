{!! $blocs !!}

{{-- INSCRIPTION FOURNISSEUR — UNE SEULE PAGE (Denis 28.06 : « pas plusieurs fenêtres,
     tous les form en un seul endroit, avec un bouton retour, minimiser les espaces »).
     Structure Denis 01.07 : 1. Coordonnées → 2. Votre 2350 (plateforme + profession +
     frais) → 3. FICHE DES COMPÉTENCES (la fiche intégrale, de la 1re ligne aux mots-clés
     SEO, avec forfait/zone, options et page Conclusion à l'intérieur) → 4. Mot de passe.
     Soumission : storeSupplierFull (handler combiné, délègue à storeStep6). --}}
<section class="ck-auth ck-auth--wide">
    <div class="optimal-content-width">
        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">{{ __('auth.register.title.step1') }}</h3>
                    <div class="content-card__label">{{ app()->getLocale() === 'en' ? 'Everything on one page.' : 'Tout sur une seule page.' }}</div>
                </div>
                <a href="{{ urlRouteName('home') }}" class="call-to-action" style="white-space:nowrap">← {{ app()->getLocale() === 'en' ? 'Back to site' : 'Retour au site' }}</a>
            </div>

            @if(session('error'))
                <div class="form__column"><div class="ui info message" style="background:#fff8e1;border:1px solid #ffd200;border-radius:10px;padding:12px 16px">{{ session('error') }}</div></div>
            @endif

            {{-- Sommaire d'erreurs EN HAUT : après un envoi refusé, la page recharge en haut
                 et les messages de champ sont loin plus bas — Denis : « je clique enter et
                 plus rien d'inscrit ». Ici il voit immédiatement CE QUI a bloqué. --}}
            @if($errors->any())
                <div class="form__column" id="ck-error-summary">
                    <div style="background:#fdecea;border:2px solid #d93025;border-radius:10px;padding:14px 18px">
                        <p style="margin:0 0 8px;font-weight:700;color:#b00020">
                            {{ app()->getLocale() === 'en'
                                ? '⚠ Your registration was NOT saved — please correct the following and submit again:'
                                : "⚠ Votre inscription n'a PAS été enregistrée — corrigez les points suivants puis soumettez à nouveau :" }}
                        </p>
                        <ul style="margin:0;padding-left:1.2em;color:#b00020">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {!! Form::open(['url' => urlRouteName('subscriber.register.storeSupplierFull')]) !!}
                <input type="hidden" name="preference_language" value="{{ App::getLocale() }}">

                {{-- ───────────── 1) COORDONNÉES ─────────────
                     Denis 28.06 : les TITRES restent et le fournisseur remplit À CÔTÉ du titre. --}}
                @php $t = fn ($fr, $en) => app()->getLocale() === 'en' ? $en : $fr; @endphp
                <style>
                    /* .form__column du thème = flex COLONNE + centré → on force LIGNE + gauche.
                       Titre à gauche, champ rempli À CÔTÉ (Denis). */
                    .ck-coord { display:flex !important; flex-direction:row !important; align-items:center !important;
                        justify-content:flex-start !important; gap:12px; flex-wrap:wrap; margin-bottom:6px; text-align:left; }
                    .ck-coord > label { flex:0 0 210px; max-width:210px; font-weight:600; text-align:left !important; margin:0; }
                    .ck-coord > input, .ck-coord > select, .ck-coord > sl-select { flex:1 1 240px; min-width:0; max-width:none; }
                    section.ck-auth .ck-coord > input, section.ck-auth .ck-coord > select {
                        height:38px !important; min-height:38px !important; max-height:38px !important;
                        padding:2px 12px !important; font-size:.95rem !important; line-height:1.2 !important;
                        box-shadow:none !important; box-sizing:border-box !important;
                    }
                    section.ck-auth .ck-coord > sl-select::part(combobox) { min-height:38px !important; }
                    /* Heures d'affaires : jour + début + fin sur UNE ligne, dropdowns compacts. */
                    .ck-coord.ck-hours { gap:8px; }
                    .ck-coord.ck-hours > label { flex:0 0 90px; max-width:90px; }
                    .ck-hours select { flex:0 0 auto !important; width:120px !important; max-width:120px !important; height:38px !important; min-height:38px !important; }
                    .ck-hours span { color:#666; flex:0 0 auto; }
                    @media (max-width:560px){
                        .ck-coord { flex-direction:column !important; align-items:stretch !important; }
                        .ck-coord > label { flex-basis:auto; max-width:100%; }
                    }
                </style>
                <div class="registration-title">1. {{ $t('Coordonnées', 'Contact details') }}</div>

                <div class="form__column ck-coord"><label>{{ __('auth.register.company_name') }}</label><input type="text" name="company_name" value="{{ old('company_name') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.owner_names') }}</label><input type="text" name="owner_names" value="{{ old('owner_names') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.legal_form_id') }}</label>
                    <sl-select name="legal_form_id" value="{{ old('legal_form_id') }}" placeholder="{{ __('main.choose') }}">
                        @foreach ($legalForms as $lf)<sl-option value="{{ $lf->id }}">{{ $lf->title }}</sl-option>@endforeach
                    </sl-select>
                </div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.federal_tax_number') }}</label><input type="text" name="federal_tax_number" value="{{ old('federal_tax_number') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.street') }}</label><input type="text" name="street" value="{{ old('street') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.city') }}</label><input type="text" name="city" value="{{ old('city') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.postal_code') }}</label><input type="text" name="postal_code" value="{{ old('postal_code') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.phone') }}</label><input type="text" name="phone" value="{{ old('phone') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.toll_free_phone') }}</label><input type="text" name="toll_free_phone" value="{{ old('toll_free_phone') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.fax') }}</label><input type="text" name="fax" value="{{ old('fax') }}"></div>
                {{-- autocomplete off : le navigateur de Denis remplissait « demo.fourn… » tout seul --}}
                <div class="form__column ck-coord"><label>{{ $t('Adresse courriel', 'Email address') }}</label><input type="email" name="email" value="{{ old('email') }}" autocomplete="off"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.start_date') }}</label><input type="date" name="start_date" value="{{ old('start_date') }}"></div>
                <div class="form__column ck-coord"><label>{{ __('auth.register.insurance_coverage') }}</label><input type="text" name="insurance_coverage" value="{{ old('insurance_coverage') }}"></div>

                {{-- Heures d'affaires : 7 jours, heure de début → heure de fin (dropdowns 15 min). --}}
                @php
                    $times = [];
                    for ($h = 0; $h < 24; $h++) {
                        for ($m = 0; $m < 60; $m += 15) {
                            $ap = $h < 12 ? 'am' : 'pm';
                            $hh = ($h % 12) ?: 12;
                            $times[] = sprintf('%d:%02d %s', $hh, $m, $ap);
                        }
                    }
                @endphp
                <div class="form__column">
                    <label style="font-weight:700;display:block;margin-bottom:6px">{{ $t("Heures d'affaires", 'Business hours') }}</label>
                    {{-- 1er choix = FERMÉ/CLOSED (Denis 03.07) : un jour sans heures = fermé
                         (le serveur ignore les jours dont début OU fin est vide). --}}
                    @foreach(['Lundi'=>'Monday','Mardi'=>'Tuesday','Mercredi'=>'Wednesday','Jeudi'=>'Thursday','Vendredi'=>'Friday','Samedi'=>'Saturday','Dimanche'=>'Sunday'] as $fr => $en)
                        <div class="ck-coord ck-hours" style="margin-bottom:6px">
                            <label>{{ $t($fr, $en) }} :</label>
                            <select name="business_hours[{{ $fr }}][start]">
                                <option value="" @selected(!old('business_hours.'.$fr.'.start'))>{{ $t('FERMÉ', 'CLOSED') }}</option>
                                @foreach($times as $tm)<option value="{{ $tm }}" @selected(old('business_hours.'.$fr.'.start') === $tm)>{{ $tm }}</option>@endforeach
                            </select>
                            <span>{{ $t('à', 'to') }}</span>
                            <select name="business_hours[{{ $fr }}][end]">
                                <option value="" @selected(!old('business_hours.'.$fr.'.end'))>{{ $t('FERMÉ', 'CLOSED') }}</option>
                                @foreach($times as $tm)<option value="{{ $tm }}" @selected(old('business_hours.'.$fr.'.end') === $tm)>{{ $tm }}</option>@endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                {{-- ───────────── 2) VOTRE 2350 ───────────── --}}
                <div class="registration-title">2. {{ app()->getLocale() === 'en' ? 'Your 2350' : 'Votre 2350' }}</div>

                <div class="form__column">
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:0 0 6px">{{ __('auth.register.provider_type') }}</div>
                    <sl-radio-group name="provider_type" id="provider_type" value="{{ old('provider_type') ?: 'residential' }}">
                        <div class="form__row">
                            <sl-radio value="residential">{{ __('providers.provider-type.residential') }}</sl-radio>
                            <sl-radio value="business">{{ __('providers.provider-type.business') }}</sl-radio>
                        </div>
                    </sl-radio-group>
                </div>

                <div class="form__column">
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:0 0 6px">{{ __('auth.register.service_category_id') }}</div>
                    {{-- La liste ne montre que les professions de la plateforme choisie (Résidentiel/B2B) :
                         chaque profession existe une fois par plateforme, donc sans filtre elle apparaîtrait
                         en double. Le filtre est appliqué côté client selon le bouton provider_type. --}}
                    <sl-select
                        data-url="{{ urlRouteName('subscriber.register.step2-service-form-inline') }}"
                        name="service_category_id" id="service_category_id"
                        value="{{ old('service_category_id') }}" placeholder="{{ __('main.choose') }}">
                        @foreach ($subcategories as $category)
                            @if (!$category->title) @continue @endif
                            <sl-option value="{{ $category->id }}" data-provider-type="{{ $category->provider_type ?: 'residential' }}">{{ $category->title }}</sl-option>
                        @endforeach
                    </sl-select>
                    {{-- Message si aucune profession n'existe pour la plateforme/langue choisie
                         (ex. plateformes françaises « À VENIR ») — évite un menu vide et déroutant. --}}
                    <div id="no-profession-note" style="display:none;background:#fff8e1;border:1px solid #ffe082;border-left:4px solid #ffc107;border-radius:8px;padding:.7em 1em;margin-top:8px;color:#5a4d00;font-size:.92rem">
                        {{ app()->getLocale() === 'en'
                            ? 'No profession is available for this platform yet — recruitment is underway. Please check back soon.'
                            : "Aucune profession n'est encore disponible pour cette plateforme — le recrutement est en cours. Revenez bientôt." }}
                    </div>
                </div>
                <div class="form__column">
                    <div class="ui info message" style="background:#fff9e6;border:1px solid #e6b800;border-radius:10px;padding:10px 14px;font-size:.92rem">
                        💡 {{ app()->getLocale() === 'en'
                            ? 'A one-time competence-sheet fee applies (residential $75 / B2B $100), added at payment.'
                            : 'Des frais uniques de fiche s\'appliquent (résidentiel 75 $ / B2B 100 $), ajoutés au paiement.' }}
                    </div>
                    <div style="margin-top:8px">
                        <sl-checkbox name="accept_fee" value="1" @if(old('accept_fee')) checked @endif>
                            {{ app()->getLocale() === 'en' ? 'I accept these competence-sheet fees.' : "J'accepte ces frais de fiche." }}
                        </sl-checkbox>
                    </div>
                    <div class="form__row--error">
                        @foreach($errors->get('accept_fee', '<small style="color: red">:message</small>') as $error){!! $error !!}@endforeach
                    </div>
                </div>
                {{-- ───────────── 3) FICHE DES COMPÉTENCES (Denis 01.07 : remplace les
                     anciennes sections « Zone & forfait » et « Options » — tout fait partie
                     de la fiche, présentée intégralement de la 1re ligne aux mots-clés SEO) ───────────── --}}
                <div class="registration-title">3. {{ app()->getLocale() === 'en' ? 'Competence sheet' : 'Fiche des compétences' }}</div>

                <div class="form__column" id="fiche_placeholder_note">
                    <div class="ui info message" style="background:#f2f8f2;border:1px solid #bcd9bc;border-radius:10px;padding:10px 14px;font-size:.92rem">
                        {{ app()->getLocale() === 'en'
                            ? 'Choose your platform and profession above — your full competence sheet (2350) will appear here, to be filled from the first line to the SEO keywords.'
                            : 'Choisissez votre plateforme et votre profession ci-dessus — votre fiche de compétences (2350) intégrale s\'affichera ici, à remplir de la première ligne jusqu\'aux mots-clés SEO.' }}
                    </div>
                </div>

                <div id="service-container"></div>

                {{-- Le reste de la fiche (forfait/zone, options, conclusion, SEO) n'apparaît
                     qu'avec la fiche — comme dans le fichier Excel de Denis. --}}
                <div id="fiche_extras" style="display:none">

                <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:12px 0 6px">{{ app()->getLocale() === 'en' ? 'Plan & service area' : 'Forfait & zone desservie' }}</div>

                <div class="form__column">
                    <label for="subscription_id">{{ __('auth.register.subscription_id') }}</label>
                    <sl-select name="subscription_id" id="subscription_id" value="{{ old('subscription_id') }}">
                        @foreach($subscriptions as $subscription)
                            <sl-option value="{{ $subscription->id }}" data-max-postal-codes="{{ $subscription->max_postal_codes }}">{{ $subscription->title }}</sl-option>
                        @endforeach
                    </sl-select>
                </div>

                {{-- Deux SECTIONS visibles (Denis 02.07 : « 1 section pour code postal,
                     1 section pour province ») — comme dans son 2350. Le radio de chaque
                     section choisit la zone; la section non retenue reste visible, estompée. --}}
                <style>
                    .zone-section { border:2px solid #d9d9d9; border-radius:10px; padding:12px 16px; margin-bottom:10px; cursor:pointer; }
                    .zone-section.is-active { border-color:#ffd200; background:#fffdf2; cursor:auto; }
                    /* .65 (pas .45) : la section inactive restait lisible mais « blurry » (Denis 03.07) */
                    .zone-section:not(.is-active) .zone-body { opacity:.65; }
                    .zone-section .zone-head { display:flex; align-items:center; gap:10px; font-weight:700; cursor:pointer; margin:0 0 8px; }
                    .postal-code-input { text-transform:uppercase; }
                </style>

                <div class="form__column zone-section is-active" id="postal_section">
                    <label class="zone-head"><input type="radio" name="zone_type" value="postal" checked>
                        📍 {{ app()->getLocale() === 'en' ? 'BY POSTAL CODE' : 'PAR CODE POSTAL' }}</label>
                    <div class="zone-body">
                        {{-- Denis 02-03.07 : « ENTER 6 DIGITS », l'ESPACE au milieu du code
                             (H9P 2T2), 10 boîtes vides, la 1re boîte seule puis un espace
                             franc, les 9 autres ensuite. --}}
                        <div style="margin-bottom:6px">{{ app()->getLocale() === 'en'
                            ? 'ENTER 6 CHARACTERS per postal code (ex.: H9P 2T2) — 1 to 10 postal codes, billed per code.'
                            : 'ENTREZ 6 CARACTÈRES par code postal (ex. : H9P 2T2) — 1 à 10 codes postaux, facturés par code.' }}</div>
                        {{-- Boîtes 100 % VIDES, rien d'offert (Denis 04.07) : pas de placeholder,
                             et « readonly » retiré au focus — le remplissage automatique du
                             navigateur ignore les champs readonly au chargement. --}}
                        <div style="margin-bottom:18px">
                            <input style="width:10ch" class="postal-code-input" data-i="0" type="text" maxlength="7"
                                   name="postal_codes[0]" value="{{ old('postal_codes.0') }}" autocomplete="off"
                                   readonly onfocus="this.removeAttribute('readonly')">
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            @for ($i = 1; $i < 10; $i++)
                                <input style="width:10ch" class="postal-code-input" data-i="{{$i}}" type="text" maxlength="7"
                                       name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i) }}" autocomplete="off"
                                       readonly onfocus="this.removeAttribute('readonly')">
                            @endfor
                        </div>
                        <div class="ui segment" style="display:flex;justify-content:space-between;align-items:center;border:1px solid #e2e2e2;border-radius:10px;padding:10px 14px;margin-top:10px;background:#fff">
                            <strong>{{ app()->getLocale() === 'en' ? 'Price' : 'Prix' }}</strong>
                            <strong id="postal_price_display">—</strong>
                        </div>
                    </div>
                </div>

                @if($provinces->isNotEmpty())
                    <div class="form__column zone-section" id="province_section">
                        <label class="zone-head"><input type="radio" name="zone_type" value="province">
                            🍁 {{ app()->getLocale() === 'en' ? 'BY PROVINCE' : 'PAR PROVINCE' }}</label>
                        <div class="zone-body">
                            <sl-select name="subscription_state_id" id="subscription_state_id" value="{{ old('subscription_state_id') }}" placeholder="{{ __('main.choose') }}">
                                @foreach($provinces as $province)<sl-option value="{{ $province->id }}">{{ $province->title }}</sl-option>@endforeach
                            </sl-select>
                            <div class="ui segment" style="display:flex;justify-content:space-between;align-items:center;border:1px solid #e2e2e2;border-radius:10px;padding:10px 14px;margin-top:10px;background:#fff">
                                <strong>{{ app()->getLocale() === 'en' ? 'Price' : 'Prix' }}</strong>
                                <strong id="province_price_display">—</strong>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Option SITE WEB — tout de suite après les provinces, comme dans le 2350
                     (Denis 02.07 : « WEBSITE après les provinces — et pourquoi plus tard ?? »). --}}
                @php $wfTiers = \App\Support\WebsiteForfait::tiers(); @endphp
                @if(!empty($wfTiers))
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:12px 0 6px">{{ app()->getLocale() === 'en' ? 'Supplier website option' : 'Option site web du fournisseur' }}</div>
                    <div class="form__column">
                        <div style="margin-bottom:6px">
                            <sl-checkbox name="url" id="url_option_checkbox" value="1" @if(old('url')) checked @endif>
                                {{ app()->getLocale() === 'en' ? 'Display my website address on my sheet (choose ONE option)' : 'Afficher l\'adresse de mon site web sur ma fiche (choisissez UNE seule option)' }}
                            </sl-checkbox>
                        </div>
                        <div id="url_option_body" style="display:{{ old('url') ? 'block' : 'none' }};padding-left:4px">
                            {{-- Select natif stylé (hauteur/largeur explicites : la SCSS gelée
                                 laissait un select minuscule au texte tronqué) + un optgroup par
                                 palier (100 $ / 150 $) — sinon « 1 MOIS — 100 $ » et
                                 « 1 MOIS — 150 $ » se confondent. --}}
                            <label for="url_forfait" style="display:block;font-weight:600;margin-bottom:4px">
                                {{ app()->getLocale() === 'en' ? 'Website plan — duration and price (ONE choice)' : 'Forfait site web — durée et prix (UN seul choix)' }}
                            </label>
                            <select name="url_forfait" id="url_forfait"
                                    style="display:block;width:100%;max-width:420px;height:44px;padding:8px 12px;font-size:1rem;font-weight:600;color:#222;line-height:1.3;border:1px solid #b9b9b9;border-radius:8px;background:#fff;margin-bottom:10px;box-sizing:border-box">
                                <option value="">— {{ mb_strtoupper(__('main.choose')) }} —</option>
                                @foreach($wfTiers as $tier => $durations)
                                    {{-- data-tier : la fiche choisie détermine le palier (filtré en JS) --}}
                                    <optgroup data-tier="{{ $tier }}" label="{{ app()->getLocale() === 'en' ? 'WEBSITE PLAN' : 'FORFAIT SITE WEB' }} {{ $tier }} $">
                                        @foreach($durations as $months => $price)
                                            <option value="{{ $tier }}-{{ $months }}" @selected(old('url_forfait') === $tier.'-'.$months)>
                                                {{ $months }} {{ app()->getLocale() === 'en' ? ($months > 1 ? 'MONTHS' : 'MONTH') : 'MOIS' }} — {{ number_format($price, 0) }} $
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="text" name="website_url" value="{{ old('website_url') }}" autocomplete="off"
                                   placeholder="{{ app()->getLocale() === 'en' ? 'Your website address (https://…)' : 'L\'adresse de votre site web (https://…)' }}"
                                   style="display:block;height:44px;padding:8px 12px;width:100%;max-width:420px;border:1px solid #d9d9d9;border-radius:8px;box-sizing:border-box">
                        </div>
                        <div class="form__row--error">
                            @foreach($errors->get('url_forfait', '<small style="color: red">:message</small>') as $error){!! $error !!}@endforeach
                        </div>
                    </div>
                @endif

                {{-- Options payantes — dans la fiche (ligne 455+ du 2350 de Denis).
                     MAJUSCULES (Denis 02.07) + lien « ▸ OUVRIR » par option vers la page
                     publique des options (Denis : « clic ch option et pas de lien ???? »). --}}
                <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:12px 0 6px">{{ app()->getLocale() === 'en' ? 'Options' : 'Options' }}</div>
                {{-- Cocher une option OUVRE son panneau : le fournisseur AJOUTE / MODIFIE /
                     SUPPRIME ses permis, diplômes, promotions, photos (≤12), offres d'emploi
                     PENDANT l'inscription (cahier de charges — comme l'étape 5 de l'ancien
                     assistant, mais en AJAX : aucun rechargement qui perdrait le formulaire).
                     Les items vivent en session (profile_*) et storeStep6 les enregistre au
                     compte à la soumission. « ▸ OUVRIR » déplie la description de l'option. --}}
                @php
                    $en2 = app()->getLocale() === 'en';
                    // option → type d'endpoint (slug des routes profile-option.add / option-delete)
                    $optTypeMap = ['license' => 'license', 'diploma' => 'diploma', 'promotion' => 'promotions', 'image' => 'subscriber_images', 'job_offer' => 'job_offers'];
                @endphp
                <style>
                    .ck-opt-panel { display:none; background:#fbfbf6; border:1px solid #e0e0d2; border-radius:10px; padding:12px 14px; margin:6px 0 10px; max-width:680px; }
                    /* Le FORMULAIRE de Denis (08F à 13F) affiché en tête de chaque panneau */
                    .ck-optform { background:#fff; border:1px solid #e2e2d8; border-left:4px solid #157a47; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:.9rem; }
                    .ck-optform h4 { margin:0 0 8px; color:#157a47; font-size:.95rem; }
                    .ck-optform p { margin:0 0 8px; }
                    .ck-optform ul { margin:0 0 8px; padding-left:1.3em; columns:2; column-gap:24px; }
                    .ck-optform li { margin-bottom:2px; }
                    .ck-optform .ck-optform-avis { background:#fdf6e3; border-radius:6px; padding:8px 10px; font-size:.85rem; }
                    @media (max-width:560px){ .ck-optform ul { columns:1; } }
                    .ck-opt-panel label { display:block; font-weight:600; margin:6px 0 2px; font-size:.9rem; }
                    .ck-opt-panel input[type=text], .ck-opt-panel input[type=number], .ck-opt-panel textarea {
                        width:100%; box-sizing:border-box; height:36px; padding:6px 10px; border:1px solid #ccc; border-radius:6px; font:inherit; }
                    .ck-opt-panel textarea { height:60px; resize:vertical; }
                    .ck-opt-items { display:flex; flex-direction:column; gap:6px; margin-bottom:10px; }
                    .ck-opt-item { display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #e2e2e2; border-radius:8px; padding:8px 12px; }
                    .ck-opt-item img { width:64px; height:48px; object-fit:cover; border-radius:6px; }
                    .ck-opt-item .grow { flex:1 1 auto; min-width:0; overflow:hidden; text-overflow:ellipsis; }
                    .ck-opt-item button { background:#fff; border:1px solid #bbb; border-radius:6px; padding:4px 10px; cursor:pointer; font-weight:600; }
                    .ck-opt-item button.del { border-color:#d33; color:#d33; }
                    .ck-opt-actions { display:flex; gap:8px; margin-top:10px; align-items:center; flex-wrap:wrap; }
                    .ck-opt-add { background:#ffd200; border:none; border-radius:8px; padding:9px 18px; font-weight:700; cursor:pointer; }
                    .ck-opt-cancel { background:#fff; border:1px solid #bbb; border-radius:8px; padding:8px 14px; cursor:pointer; display:none; }
                    .ck-opt-msg { color:#b00020; font-weight:600; }
                </style>
                <div class="form__column">
                    @foreach($profileOptions as $option)
                        @if($option === 'url') @continue @endif
                        @php $optType = $optTypeMap[$option] ?? null; @endphp
                        <div style="padding:3px 0">
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                <sl-checkbox name="{{ $option }}" value="1" class="ck-option-check" data-panel="opt_panel_{{ $option }}" @if(old($option)) checked @endif>
                                    <span style="text-transform:uppercase">{{ setting("{$option}_title", $option) }}</span>
                                </sl-checkbox>
                                {{-- ▸ OUVRIR ouvre LE FORMULAIRE de Denis (08F-13F) avec les
                                     espaces à remplir (Denis 04.07). --}}
                                <a href="#" class="ck-option-open" data-target="opt_panel_{{ $option }}" style="white-space:nowrap;font-weight:600">▸ {{ $en2 ? 'OPEN' : 'OUVRIR' }}</a>
                            </div>

                            {{-- Panneau de l'option : LE FORMULAIRE de Denis + les espaces à remplir
                                 (ouvert par OUVRIR, ou automatiquement quand l'option est cochée) --}}
                            <div class="ck-opt-panel" id="opt_panel_{{ $option }}" @if($optType) data-type="{{ $optType }}" @endif>
                                @include('partials.option-forms.' . $option)
                                @if ($option === 'estimation')
                                    {{-- Champs directs (soumis avec le formulaire principal) --}}
                                    <label>{{ $en2 ? 'Estimation cost ($ — leave empty if free)' : "Coût de l'estimation ($ — laisser vide si gratuite)" }}</label>
                                    <input type="number" step="0.01" min="0" name="estimation_cost" value="{{ old('estimation_cost') }}">
                                    <label style="margin-top:10px">{{ $en2 ? 'Accepted payment methods' : 'Modes de paiement acceptés' }}</label>
                                    <div style="display:flex;gap:14px;flex-wrap:wrap">
                                        <sl-checkbox name="accepts_cash" value="1" @if(old('accepts_cash')) checked @endif>{{ $en2 ? 'Cash' : 'Comptant' }}</sl-checkbox>
                                        <sl-checkbox name="accepts_check" value="1" @if(old('accepts_check')) checked @endif>{{ $en2 ? 'Cheque' : 'Chèque' }}</sl-checkbox>
                                        <sl-checkbox name="accepts_debit" value="1" @if(old('accepts_debit')) checked @endif>{{ $en2 ? 'Debit' : 'Débit' }}</sl-checkbox>
                                        <sl-checkbox name="accepts_credit" value="1" @if(old('accepts_credit')) checked @endif>{{ $en2 ? 'Credit' : 'Crédit' }}</sl-checkbox>
                                    </div>
                                @else
                                    <div class="ck-opt-items" data-type="{{ $optType }}"></div>

                                    @if ($option === 'image')
                                        <label>{{ $en2 ? 'Your photos (up to 12 in total)' : 'Vos photos (maximum 12 au total)' }}</label>
                                        <input type="file" data-name="images[]" accept="image/*" multiple>
                                        <label>{{ $en2 ? 'Caption (optional)' : 'Légende (facultatif)' }}</label>
                                        <input type="text" data-name="legend">
                                        <input type="hidden" data-name="is_photos" value="1">
                                    @elseif ($option === 'diploma')
                                        <label>{{ $en2 ? 'Diploma / training (FR)' : 'Diplôme / formation (FR)' }}</label>
                                        <input type="text" data-name="fr[title]">
                                        <label>{{ $en2 ? 'Diploma / training (EN)' : 'Diplôme / formation (EN)' }}</label>
                                        <input type="text" data-name="en[title]">
                                        <label>{{ $en2 ? 'School / institution' : 'École / institution' }}</label>
                                        <input type="text" data-name="school">
                                        <label>{{ $en2 ? 'Date obtained' : "Date d'obtention" }}</label>
                                        <input type="text" data-name="graduated_at" placeholder="2020/06">
                                    @else
                                        <label>{{ $en2 ? 'Title (FR)' : 'Titre (FR)' }}</label>
                                        <input type="text" data-name="fr[title]">
                                        <label>{{ $en2 ? 'Description (FR)' : 'Description (FR)' }}</label>
                                        <textarea data-name="fr[description]"></textarea>
                                        <label>{{ $en2 ? 'Title (EN)' : 'Titre (EN)' }}</label>
                                        <input type="text" data-name="en[title]">
                                        <label>{{ $en2 ? 'Description (EN)' : 'Description (EN)' }}</label>
                                        <textarea data-name="en[description]"></textarea>
                                        @if ($option === 'promotion')
                                            <label>{{ $en2 ? 'Image (optional)' : 'Image (facultatif)' }}</label>
                                            <input type="file" data-name="image" accept="image/*">
                                        @endif
                                    @endif

                                    <div class="ck-opt-actions">
                                        <button type="button" class="ck-opt-add" data-type="{{ $optType }}">＋ {{ $en2 ? 'Add' : 'Ajouter' }}</button>
                                        <button type="button" class="ck-opt-cancel">{{ $en2 ? 'Cancel edit' : 'Annuler la modification' }}</button>
                                        <span class="ck-opt-msg"></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div style="font-size:.85rem;color:#777;margin-top:6px">{{ $en2 ? 'You can also complete or modify each option later from your profile.' : 'Vous pourrez aussi compléter ou modifier chaque option plus tard depuis votre profil.' }}</div>
                </div>

                <script>
                    // « ▸ OUVRIR / ▾ FERMER » : déplie la description de l'option sur place.
                    document.addEventListener('click', function (e) {
                        var link = e.target.closest('.ck-option-open');
                        if (!link) return;
                        e.preventDefault();
                        var box = document.getElementById(link.getAttribute('data-target'));
                        if (!box) return;
                        var open = box.style.display !== 'none';
                        box.style.display = open ? 'none' : 'block';
                        var en = document.documentElement.lang === 'en';
                        link.textContent = open ? ('▸ ' + (en ? 'OPEN' : 'OUVRIR')) : ('▾ ' + (en ? 'CLOSE' : 'FERMER'));
                    });
                </script>

                {{-- Gestionnaire des CONTENUS d'options (ajout / modification / suppression
                     en AJAX pendant l'inscription — items en session, enregistrés au compte
                     par storeStep6 à la soumission). --}}
                <script>
                (function () {
                    var state = {!! json_encode($optionItems, JSON_UNESCAPED_UNICODE) !!};
                    var urls  = {!! json_encode($optionUrls, JSON_UNESCAPED_UNICODE) !!};
                    var en = document.documentElement.lang === 'en';
                    var T = {
                        add: en ? '＋ Add' : '＋ Ajouter',
                        save: en ? '✔ Save changes' : '✔ Enregistrer la modification',
                        edit: en ? 'Edit' : 'Modifier',
                        del: en ? 'Delete' : 'Supprimer',
                        empty: en ? 'Nothing added yet.' : 'Rien d\'ajouté pour le moment.',
                        err: en ? 'Error — please try again.' : 'Erreur — réessayez.'
                    };
                    var token = document.querySelector('input[name="_token"]').value;

                    function panelOf(type) { return document.querySelector('.ck-opt-panel[data-type="' + type + '"]'); }
                    function get(obj, path) {
                        // 'fr[title]' → obj.fr.title
                        var keys = path.replace(/\]/g, '').split('[');
                        var v = obj;
                        for (var i = 0; i < keys.length; i++) { if (v == null) return ''; v = v[keys[i]]; }
                        return v == null ? '' : v;
                    }
                    function labelOf(type, item) {
                        if (type === 'subscriber_images') { return get(item, 'legend'); }
                        var t = get(item, 'fr[title]') || get(item, 'en[title]');
                        if (type === 'diploma') {
                            var extra = [get(item, 'school'), get(item, 'graduated_at')].filter(Boolean).join(', ');
                            return t + (extra ? ' — ' + extra : '');
                        }
                        return t;
                    }
                    function render(type) {
                        var list = document.querySelector('.ck-opt-items[data-type="' + type + '"]');
                        if (!list) return;
                        list.innerHTML = '';
                        var items = state[type] || [];
                        if (!items.length) {
                            var p = document.createElement('div');
                            p.style.cssText = 'color:#888;font-size:.9rem';
                            p.textContent = T.empty;
                            list.appendChild(p);
                            return;
                        }
                        items.forEach(function (item, i) {
                            var row = document.createElement('div');
                            row.className = 'ck-opt-item';
                            if (type === 'subscriber_images' && item.image) {
                                var img = document.createElement('img');
                                img.src = item.image;
                                img.alt = '';
                                row.appendChild(img);
                            }
                            var span = document.createElement('span');
                            span.className = 'grow';
                            span.textContent = labelOf(type, item) || ((en ? 'Item ' : 'Élément ') + (i + 1));
                            row.appendChild(span);
                            if (type !== 'subscriber_images') {
                                var eb = document.createElement('button');
                                eb.type = 'button';
                                eb.textContent = '✎ ' + T.edit;
                                eb.addEventListener('click', function () { beginEdit(type, i); });
                                row.appendChild(eb);
                            }
                            var db = document.createElement('button');
                            db.type = 'button';
                            db.className = 'del';
                            db.textContent = '✕ ' + T.del;
                            db.addEventListener('click', function () { remove(type, i); });
                            row.appendChild(db);
                            list.appendChild(row);
                        });
                    }
                    function inputsOf(type) {
                        var panel = panelOf(type);
                        return panel ? Array.prototype.slice.call(panel.querySelectorAll('[data-name]')) : [];
                    }
                    function clearInputs(type) {
                        inputsOf(type).forEach(function (el) {
                            if (el.type === 'file') { el.value = ''; }
                            else if (el.type !== 'hidden') { el.value = ''; }
                        });
                        var panel = panelOf(type);
                        panel.removeAttribute('data-edit-index');
                        panel.querySelector('.ck-opt-add').textContent = T.add;
                        panel.querySelector('.ck-opt-cancel').style.display = 'none';
                        panel.querySelector('.ck-opt-msg').textContent = '';
                    }
                    function beginEdit(type, i) {
                        var item = (state[type] || [])[i];
                        if (!item) return;
                        var panel = panelOf(type);
                        inputsOf(type).forEach(function (el) {
                            if (el.type === 'file' || el.type === 'hidden') return;
                            el.value = get(item, el.getAttribute('data-name'));
                        });
                        panel.setAttribute('data-edit-index', i + 1);
                        panel.querySelector('.ck-opt-add').textContent = T.save;
                        panel.querySelector('.ck-opt-cancel').style.display = '';
                        panel.querySelector('.ck-opt-msg').textContent = '';
                    }
                    function submit(type) {
                        var panel = panelOf(type);
                        var msg = panel.querySelector('.ck-opt-msg');
                        var fd = new FormData();
                        fd.append('_token', token);
                        var hasContent = false;
                        inputsOf(type).forEach(function (el) {
                            var name = el.getAttribute('data-name');
                            if (el.type === 'file') {
                                for (var j = 0; j < el.files.length; j++) {
                                    fd.append(name.endsWith('[]') ? name : name, el.files[j]);
                                    hasContent = true;
                                }
                            } else {
                                fd.append(name, el.value);
                                if (el.type !== 'hidden' && el.value.trim() !== '') { hasContent = true; }
                            }
                        });
                        if (!hasContent) {
                            msg.textContent = en ? 'Please fill in at least one field.' : 'Veuillez remplir au moins un champ.';
                            return;
                        }
                        var editIdx = panel.getAttribute('data-edit-index');
                        if (editIdx) { fd.append('session_index', editIdx); }
                        msg.textContent = '…';
                        fetch(urls[type].add, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                            .then(function (res) {
                                if (!res.ok || res.j.error) { msg.textContent = res.j.error || T.err; return; }
                                if (res.j.items) { state[type] = res.j.items; }
                                clearInputs(type);
                                render(type);
                            })
                            .catch(function () { msg.textContent = T.err; });
                    }
                    function remove(type, i) {
                        fetch(urls[type].del + '/' + (i + 1), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(function (r) { return r.ok ? r.json().catch(function(){ return {}; }) : null; })
                            .then(function (j) {
                                if (j === null) return;
                                if (j.items) { state[type] = j.items; }
                                else { (state[type] || []).splice(i, 1); }
                                var panel = panelOf(type);
                                if (panel.getAttribute('data-edit-index')) { clearInputs(type); }
                                render(type);
                            });
                    }

                    document.querySelectorAll('.ck-opt-add').forEach(function (b) {
                        b.addEventListener('click', function () { submit(b.getAttribute('data-type')); });
                    });
                    document.querySelectorAll('.ck-opt-cancel').forEach(function (b) {
                        b.addEventListener('click', function () {
                            var type = b.closest('.ck-opt-panel').getAttribute('data-type');
                            clearInputs(type);
                        });
                    });

                    // Le panneau suit la case de l'option (ouvert quand cochée).
                    document.querySelectorAll('sl-checkbox.ck-option-check').forEach(function (cb) {
                        var panel = document.getElementById(cb.getAttribute('data-panel'));
                        if (!panel) return;
                        function sync() { panel.style.display = cb.checked ? 'block' : 'none'; }
                        cb.addEventListener('sl-change', sync);
                        if (window.customElements) { customElements.whenDefined('sl-checkbox').then(sync); }
                    });

                    Object.keys(urls).forEach(render);
                })();
                </script>

                {{-- Lien OBLIGATOIRE vers la page CONCLUSION au bas du 2350, AVANT la liste
                     des SEO (Denis 24.06). Porté de l'assistant 6 étapes (step-2). --}}
                @php $ccLoc = app()->getLocale(); @endphp
                <div class="form__column" id="conclusion_block" style="margin-top:1.5em;padding:14px 16px;border:2px solid #ffd200;border-radius:10px;background:#fffbe9">
                    <p style="margin:0 0 10px;font-weight:700">
                        {{ $ccLoc === 'en' ? 'Before continuing, please open and read the Conclusion page.' : 'Avant de continuer, veuillez ouvrir et lire la page Conclusion.' }}
                    </p>
                    <a href="{{ urlRouteName('conclusion') }}" target="_blank" class="call-to-action">
                        {{ $ccLoc === 'en' ? 'Open the CONCLUSION page' : 'Ouvrir la page CONCLUSION' }}
                    </a>
                    <div style="margin-top:12px">
                        <sl-checkbox name="conclusion_read" value="1" @if(old('conclusion_read')) checked @endif>
                            {{ $ccLoc === 'en' ? 'I have read the Conclusion page.' : "J'ai lu la page Conclusion." }}
                        </sl-checkbox>
                    </div>
                    <div id="conclusion_error" style="display:none;color:#b00020;font-weight:700;margin-top:10px">
                        {{ $ccLoc === 'en' ? 'Please open the Conclusion page and tick the box before continuing.' : 'Veuillez ouvrir la page Conclusion et cocher la case avant de continuer.' }}
                    </div>
                </div>

                {{-- Mots-clés SEO — dernières lignes de la fiche (lecture seule : programmés
                     automatiquement avec la fiche, rien à remplir par le fournisseur). --}}
                <div class="form__column" id="seo_block" style="display:none">
                    <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:12px 0 6px">{{ $ccLoc === 'en' ? 'Keywords (SEO)' : 'Mots-clés (SEO)' }}</div>
                    <div style="font-size:.85rem;color:#777;margin-bottom:6px">
                        {{ $ccLoc === 'en' ? 'Programmed automatically with your sheet — nothing to fill in.' : 'Programmés automatiquement avec votre fiche — rien à remplir.' }}
                    </div>
                    <div id="seo_list" style="display:flex;flex-wrap:wrap;gap:6px"></div>
                </div>

                </div>{{-- /#fiche_extras --}}

                {{-- ───────────── 4) MOT DE PASSE & CONDITIONS ───────────── --}}
                <div class="registration-title">4. {{ app()->getLocale() === 'en' ? 'Password & terms' : 'Mot de passe & conditions' }}</div>
                <div class="form__column"><input type="password" name="password" placeholder="{{ __('auth.register.password') }}" autocomplete="new-password"></div>
                <div class="form__column"><input type="password" name="password_confirmation" placeholder="{{ __('auth.register.password_confirmation') }}" autocomplete="new-password"></div>
                <div class="form__column">
                    <sl-checkbox name="accept_condition" value="1"><a href="{!! urlRouteName('term-of-use') !!}" target="_blank">{{ __('auth.register.terms') }}</a></sl-checkbox>
                </div>

                <div class="content-card__footer">
                    <a href="{{ urlRouteName('home') }}" class="call-to-action">← {{ app()->getLocale() === 'en' ? 'Back to site' : 'Retour au site' }}</a>
                    <button type="submit" class="call-to-action">{{ __('main.submit') }}</button>
                </div>
            {!! Form::close() !!}

        </div>
    </div>
</section>

@include('partials.password-eye')

{{-- Prix du forfait calculé côté client : priceMap[catégorie][abonnement][zone].
     Code postal : prix PAR code postal × nombre de codes saisis (Denis 02.07). --}}
<script>
(function () {
    var priceMap = {!! json_encode($priceMap, JSON_UNESCAPED_UNICODE) !!};
    var cat  = document.getElementById('service_category_id');
    var sub  = document.getElementById('subscription_id');
    var prov = document.getElementById('subscription_state_id');
    var postalSection = document.getElementById('postal_section');
    var provSection   = document.getElementById('province_section');
    var postalPriceEl = document.getElementById('postal_price_display');
    var provPriceEl   = document.getElementById('province_price_display');
    var postalInputs  = document.querySelectorAll('.postal-code-input');
    var en = document.documentElement.lang === 'en';
    var fmt = new Intl.NumberFormat(en ? 'en-CA' : 'fr-CA', { style:'currency', currency:'CAD' });
    function val(el){ return el ? (el.value || '') : ''; }
    function zone(){ var r = document.querySelector('input[name="zone_type"]:checked'); return r ? r.value : 'postal'; }
    function subTitle(){
        var opt = sub ? sub.querySelector('sl-option[value="'+val(sub)+'"]') : null;
        return opt ? opt.textContent.trim() : '';
    }
    function filledCodes(){
        var n = 0;
        postalInputs.forEach(function(i){ if (i.value.trim() !== '') n++; });
        return n;
    }
    function update(){
        var z = zone();
        if (postalSection) postalSection.classList.toggle('is-active', z === 'postal');
        if (provSection)   provSection.classList.toggle('is-active', z === 'province');
        var map = ((priceMap[val(cat)]||{})[val(sub)]) || {};
        var dur = subTitle();
        // Code postal : prix unitaire × nombre de codes
        var unit = map['postal'];
        if (postalPriceEl) {
            if (unit || unit === 0) {
                var n = Math.max(1, filledCodes());
                postalPriceEl.textContent = fmt.format(unit)
                    + (en ? ' per postal code' : ' par code postal')
                    + ' × ' + n + (en ? ' code(s) = ' : ' code(s) = ') + fmt.format(unit * n)
                    + (dur ? ' (' + dur + ')' : '');
            } else {
                postalPriceEl.textContent = '—';
            }
        }
        // Province : prix du couple province × durée
        if (provPriceEl) {
            var pcost = map[val(prov)];
            provPriceEl.textContent = (pcost || pcost === 0)
                ? fmt.format(pcost) + (dur ? ' (' + dur + ')' : '')
                : '—';
        }
    }
    document.querySelectorAll('input[name="zone_type"]').forEach(function(r){ r.addEventListener('change', update); });
    // Anti-remplissage automatique du navigateur (Denis 04.07 : « j'inscris 1 code
    // postal, tous les blocs répètent le même ») : un code en double est vidé —
    // chaque boîte doit contenir un code DIFFÉRENT (facturation par code).
    function dedupePostals() {
        var seen = {};
        postalInputs.forEach(function (i) {
            var v = i.value.replace(/\s+/g, '').toUpperCase();
            if (v === '') return;
            if (seen[v]) { i.value = ''; }
            else { seen[v] = true; }
        });
    }
    postalInputs.forEach(function(i){
        i.addEventListener('input', function(){ dedupePostals(); update(); });
        i.addEventListener('change', function(){ dedupePostals(); update(); });
    });
    [cat, sub, prov].forEach(function(el){ if (el) el.addEventListener('sl-change', update); });
    if (window.customElements && customElements.whenDefined) { customElements.whenDefined('sl-select').then(function(){ setTimeout(update,0); }); }
    setTimeout(update, 0);

    // Cliquer N'IMPORTE OÙ dans une section de zone l'ACTIVE (Denis 03.07 : la
    // section province semblait « blurry » — il cliquait dedans sans que le radio
    // ne s'active, donc elle restait estompée).
    [postalSection, provSection].forEach(function (sec) {
        if (!sec) return;
        sec.addEventListener('click', function () {
            var r = sec.querySelector('input[name="zone_type"]');
            if (r && !r.checked) { r.checked = true; update(); }
        });
    });
})();
</script>

{{-- Option site web : le choix forfait + adresse n'apparaissent que si l'option est cochée. --}}
<script>
(function () {
    var box = document.getElementById('url_option_checkbox');
    var body = document.getElementById('url_option_body');
    if (!box || !body) return;
    function sync(){ body.style.display = box.checked ? 'block' : 'none'; }
    box.addEventListener('sl-change', sync);
    if (window.customElements) { customElements.whenDefined('sl-checkbox').then(sync); }
})();
</script>

{{-- Chargement FIABLE du 2350 : on n'utilise PAS le composant gelé (qui ne se déclenchait
     pas toujours). Au choix de la profession, on charge directement la fiche du fournisseur
     (ses services AVEC ses couleurs) dans #service-container. --}}
<script>
(function () {
    var sel = document.getElementById('service_category_id');
    var container = document.getElementById('service-container');
    if (!sel || !container) return;
    var url = sel.getAttribute('data-url');
    var loadingTxt = (document.documentElement.lang === 'en') ? 'Loading the 2350 form…' : 'Chargement du formulaire 2350…';
    var errTxt = (document.documentElement.lang === 'en') ? 'Loading error — please pick the profession again.' : 'Erreur de chargement — choisissez à nouveau la profession.';
    var extras = document.getElementById('fiche_extras');
    var placeholderNote = document.getElementById('fiche_placeholder_note');
    var seoBlock = document.getElementById('seo_block');
    var seoList = document.getElementById('seo_list');
    // La suite de la fiche (forfait/zone, options, conclusion, SEO) n'apparaît qu'avec la fiche.
    function ficheShown(shown) {
        if (extras) extras.style.display = shown ? '' : 'none';
        if (placeholderNote) placeholderNote.style.display = shown ? 'none' : '';
    }
    // Métadonnées transmises avec la fiche (bloc JSON en fin de réponse) :
    // mots-clés SEO + palier site web propre à la fiche.
    function ficheMeta() {
        var data = container.querySelector('#fiche-meta-json');
        if (!data) return { keywords: [], website_tier: null };
        try { return JSON.parse(data.textContent) || { keywords: [], website_tier: null }; }
        catch (e) { return { keywords: [], website_tier: null }; }
    }
    function fillSeo(meta) {
        if (!seoBlock || !seoList) return;
        var words = meta.keywords || [];
        seoList.innerHTML = '';
        words.forEach(function (w) {
            var chip = document.createElement('span');
            chip.style.cssText = 'background:#eef3ee;border:1px solid #cfdccf;border-radius:14px;padding:2px 10px;font-size:.85rem;color:#444';
            chip.textContent = w;
            seoList.appendChild(chip);
        });
        seoBlock.style.display = words.length ? '' : 'none';
    }
    // Palier site web : la FICHE détermine 100 $ ou 150 $ (Denis 03.07) — on ne
    // propose que les durées de ce palier. Palier inconnu → tout est offert.
    function filterWebsiteTier(meta) {
        var wsel = document.getElementById('url_forfait');
        if (!wsel) return;
        var tier = meta.website_tier ? String(meta.website_tier) : null;
        wsel.querySelectorAll('optgroup').forEach(function (g) {
            var show = !tier || g.getAttribute('data-tier') === tier;
            g.style.display = show ? '' : 'none';
            g.querySelectorAll('option').forEach(function (o) { o.disabled = !show; o.hidden = !show; });
        });
        var cur = wsel.options[wsel.selectedIndex];
        if (cur && cur.disabled) { wsel.value = ''; }
    }
    // Les <script> injectés via innerHTML ne s'exécutent JAMAIS : le verrouillage
    // des « Précisez » (débloqués quand le O est coché) restait mort — Denis 03.07.
    // On ré-exécute les scripts de la fiche (les blocs JSON restent inertes).
    function runFicheScripts() {
        container.querySelectorAll('script').forEach(function (old) {
            var type = old.getAttribute('type');
            if (type && type.indexOf('javascript') === -1) return; // données JSON : ne pas exécuter
            var s = document.createElement('script');
            s.textContent = old.textContent;
            old.parentNode.replaceChild(s, old);
        });
    }
    function load() {
        var id = sel.value;
        if (!id) { container.innerHTML = ''; ficheShown(false); return; }
        container.innerHTML = '<p style="padding:14px;color:#666">' + loadingTxt + '</p>';
        fetch(url + '?service_category_id=' + encodeURIComponent(id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                container.innerHTML = html;
                runFicheScripts();
                ficheShown(true);
                var meta = ficheMeta();
                fillSeo(meta);
                filterWebsiteTier(meta);
            })
            .catch(function () { container.innerHTML = '<p style="padding:14px;color:#b00020">' + errTxt + '</p>'; ficheShown(false); });
    }
    sel.addEventListener('sl-change', load);
    // Si une profession est déjà choisie (retour avec erreurs), on charge tout de suite.
    if (window.customElements && customElements.whenDefined) {
        customElements.whenDefined('sl-select').then(function () { if (sel.value) load(); });
    }
})();
</script>

{{-- Filtre de la liste des professions selon la plateforme choisie (Résidentiel/B2B).
     Chaque profession existe une fois par plateforme (WW0001RE / WW0001B2BE…) : on n'affiche
     que celles de la plateforme sélectionnée pour supprimer les doublons. Si la plateforme n'a
     aucune profession (ex. français « À VENIR »), on montre une note au lieu d'un menu vide. --}}
<script>
(function () {
    var group = document.getElementById('provider_type');
    var sel   = document.getElementById('service_category_id');
    var note  = document.getElementById('no-profession-note');
    var container = document.getElementById('service-container');
    if (!group || !sel) return;

    function apply() {
        var type = group.value || 'residential';
        var visible = 0;
        sel.querySelectorAll('sl-option').forEach(function (opt) {
            var pt = opt.getAttribute('data-provider-type') || 'residential';
            var show = (pt === type || pt === 'both');
            opt.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        // Si la profession déjà choisie n'appartient plus à la plateforme, on la réinitialise
        // (et on vide le 2350 chargé en dessous).
        if (sel.value) {
            var chosen = sel.querySelector('sl-option[value="' + sel.value + '"]');
            if (chosen && chosen.style.display === 'none') {
                sel.value = '';
                if (container) container.innerHTML = '';
                var ex = document.getElementById('fiche_extras');
                var ph = document.getElementById('fiche_placeholder_note');
                if (ex) ex.style.display = 'none';
                if (ph) ph.style.display = '';
            }
        }
        if (note) note.style.display = (visible === 0) ? 'block' : 'none';
    }

    group.addEventListener('sl-change', apply);
    if (window.customElements && customElements.whenDefined) {
        customElements.whenDefined('sl-radio-group').then(apply);
        customElements.whenDefined('sl-select').then(apply);
    }
    apply();
})();
</script>

{{-- Conclusion OBLIGATOIRE : bloque « S'enregistrer » avec un message clair si pas cochée
     (même mécanique que l'assistant 6 étapes). --}}
<script>
(function () {
    var cc = document.querySelector('sl-checkbox[name="conclusion_read"]');
    if (!cc) return;
    var form = cc.closest('form');
    if (!form) return;
    var err = document.getElementById('conclusion_error');
    form.addEventListener('submit', function (e) {
        if (!cc.checked) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (err) { err.style.display = 'block'; }
            var block = document.getElementById('conclusion_block');
            (block || cc).scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);
    cc.addEventListener('sl-change', function () {
        if (cc.checked && err) { err.style.display = 'none'; }
    });
})();
</script>
