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
                <div class="form__column ck-coord"><label>{{ $t('Adresse courriel', 'Email address') }}</label><input type="email" name="email" value="{{ old('email') }}"></div>
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
                    @foreach(['Lundi'=>'Monday','Mardi'=>'Tuesday','Mercredi'=>'Wednesday','Jeudi'=>'Thursday','Vendredi'=>'Friday','Samedi'=>'Saturday','Dimanche'=>'Sunday'] as $fr => $en)
                        <div class="ck-coord ck-hours" style="margin-bottom:6px">
                            <label>{{ $t($fr, $en) }} :</label>
                            <select name="business_hours[{{ $fr }}][start]">
                                @foreach($times as $tm)<option value="{{ $tm }}" @selected(old('business_hours.'.$fr.'.start') === $tm)>{{ $tm }}</option>@endforeach
                            </select>
                            <span>{{ $t('à', 'to') }}</span>
                            <select name="business_hours[{{ $fr }}][end]">
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

                <style>
                    .zone-toggle { display:inline-flex; border:2px solid #d9d9d9; border-radius:10px; overflow:hidden; margin-top:4px; }
                    .zone-toggle .zone-opt { display:flex; align-items:center; gap:8px; padding:10px 18px; cursor:pointer; font-weight:600; color:#555; background:#fff; user-select:none; }
                    .zone-toggle .zone-opt + .zone-opt { border-left:2px solid #d9d9d9; }
                    .zone-toggle .zone-opt input { position:absolute; opacity:0; pointer-events:none; }
                    .zone-toggle .zone-opt.is-active { background:#ffd200; color:#222; }
                </style>
                <div class="form__column">
                    <label style="font-weight:700">{{ app()->getLocale() === 'en' ? 'Service area' : 'Zone desservie' }}</label>
                    <div class="zone-toggle">
                        <label class="zone-opt is-active"><input type="radio" name="zone_type" value="postal" checked><span>📍 {{ app()->getLocale() === 'en' ? 'By postal code' : 'Par code postal' }}</span></label>
                        @if($provinces->isNotEmpty())
                            <label class="zone-opt"><input type="radio" name="zone_type" value="province"><span>🍁 {{ app()->getLocale() === 'en' ? 'By province' : 'Par province' }}</span></label>
                        @endif
                    </div>
                </div>

                <div class="form__column" id="postal_block">
                    <label for="postal_codes">{{ __('auth.register.postal_codes') }}</label>
                    <div>{{ app()->getLocale() === 'en' ? 'The plan includes' : 'Le forfait inclut' }} <span id="max_postal_codes">—</span> {{ app()->getLocale() === 'en' ? 'postal codes.' : 'codes postaux.' }}</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        @for ($i = 0; $i < ($subscriptions->max('max_postal_codes') ?: 10); $i++)
                            <input style="width:7ch" class="postal-code-input" data-i="{{$i}}" type="text" name="postal_codes[{{ $i }}]" value="{{ old('postal_codes.'.$i) }}">
                        @endfor
                    </div>
                </div>

                @if($provinces->isNotEmpty())
                    <div class="form__column" id="province_block" style="display:none">
                        <label for="subscription_state_id">Province</label>
                        <sl-select name="subscription_state_id" id="subscription_state_id" value="{{ old('subscription_state_id') }}">
                            @foreach($provinces as $province)<sl-option value="{{ $province->id }}">{{ $province->title }}</sl-option>@endforeach
                        </sl-select>
                    </div>
                @endif

                <div class="form__column">
                    <div class="ui segment" style="display:flex;justify-content:space-between;align-items:center;border:1px solid #e2e2e2;border-radius:10px;padding:12px 16px">
                        <strong>{{ app()->getLocale() === 'en' ? 'Plan price' : 'Prix du forfait' }}</strong><strong id="forfait_price_display">—</strong>
                    </div>
                </div>

                {{-- Options payantes — dans la fiche (ligne 455+ du 2350 de Denis). --}}
                <div class="registration-title" style="font-size:1rem !important;border:none !important;margin:12px 0 6px">{{ app()->getLocale() === 'en' ? 'Options' : 'Options' }}</div>
                <div class="form__column">
                    @foreach($profileOptions as $option)
                        @if($option === 'url') @continue @endif
                        <div style="padding:3px 0"><sl-checkbox name="{{ $option }}" value="1" @if(old($option)) checked @endif>{{ setting("{$option}_title", $option) }}</sl-checkbox></div>
                    @endforeach
                    <div style="font-size:.85rem;color:#777;margin-top:6px">{{ app()->getLocale() === 'en' ? 'Details (and the website option) can be added from your profile after signup.' : 'Les détails (et l\'option site web) s\'ajoutent depuis votre profil après l\'inscription.' }}</div>
                </div>

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
                <div class="form__column"><input type="password" name="password" placeholder="{{ __('auth.register.password') }}"></div>
                <div class="form__column"><input type="password" name="password_confirmation" placeholder="{{ __('auth.register.password_confirmation') }}"></div>
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

{{-- Prix du forfait calculé côté client : priceMap[catégorie][abonnement][zone]. --}}
<script>
(function () {
    var priceMap = {!! json_encode($priceMap, JSON_UNESCAPED_UNICODE) !!};
    var cat  = document.getElementById('service_category_id');
    var sub  = document.getElementById('subscription_id');
    var prov = document.getElementById('subscription_state_id');
    var postalBlock = document.getElementById('postal_block');
    var provBlock   = document.getElementById('province_block');
    var priceEl = document.getElementById('forfait_price_display');
    var maxEl   = document.getElementById('max_postal_codes');
    var postalInputs = document.querySelectorAll('.postal-code-input');
    var fmt = new Intl.NumberFormat('fr-CA', { style:'currency', currency:'CAD' });
    function val(el){ return el ? (el.value || '') : ''; }
    function zone(){ var r = document.querySelector('input[name="zone_type"]:checked'); return r ? r.value : 'postal'; }
    function update(){
        var z = zone();
        document.querySelectorAll('.zone-toggle .zone-opt').forEach(function(l){ var i=l.querySelector('input'); l.classList.toggle('is-active', !!(i&&i.checked)); });
        if (postalBlock) postalBlock.style.display = (z==='province') ? 'none' : '';
        if (provBlock)   provBlock.style.display   = (z==='province') ? '' : 'none';
        var opt = sub ? sub.querySelector('sl-option[value="'+val(sub)+'"]') : null;
        var maxPC = opt ? parseInt(opt.getAttribute('data-max-postal-codes')||'0',10) : 0;
        if (maxEl) maxEl.textContent = maxPC || '—';
        postalInputs.forEach(function(input){ input.style.display = (parseInt(input.dataset.i,10) >= maxPC) ? 'none' : ''; });
        var map = ((priceMap[val(cat)]||{})[val(sub)]) || {};
        var cost = (z==='province') ? map[val(prov)] : map['postal'];
        priceEl.textContent = (cost || cost===0) ? fmt.format(cost) : '—';
    }
    document.querySelectorAll('input[name="zone_type"]').forEach(function(r){ r.addEventListener('change', update); });
    [cat, sub, prov].forEach(function(el){ if (el) el.addEventListener('sl-change', update); });
    if (window.customElements && customElements.whenDefined) { customElements.whenDefined('sl-select').then(function(){ setTimeout(update,0); }); }
    setTimeout(update, 0);
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
    // Mots-clés SEO transmis avec la fiche (bloc JSON en fin de réponse).
    function fillSeo() {
        if (!seoBlock || !seoList) return;
        var data = container.querySelector('#fiche-keywords-json');
        var words = [];
        if (data) { try { words = JSON.parse(data.textContent) || []; } catch (e) { words = []; } }
        seoList.innerHTML = '';
        words.forEach(function (w) {
            var chip = document.createElement('span');
            chip.style.cssText = 'background:#eef3ee;border:1px solid #cfdccf;border-radius:14px;padding:2px 10px;font-size:.85rem;color:#444';
            chip.textContent = w;
            seoList.appendChild(chip);
        });
        seoBlock.style.display = words.length ? '' : 'none';
    }
    function load() {
        var id = sel.value;
        if (!id) { container.innerHTML = ''; ficheShown(false); return; }
        container.innerHTML = '<p style="padding:14px;color:#666">' + loadingTxt + '</p>';
        fetch(url + '?service_category_id=' + encodeURIComponent(id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) { container.innerHTML = html; ficheShown(true); fillSeo(); })
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
