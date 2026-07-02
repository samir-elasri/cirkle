@if (!empty($serviceCategory->services_intro_text))
    <div class="form__column">
        <div class="master-2350-note">{!! $serviceCategory->services_intro_text !!}</div>
    </div>
@endif

{{-- Rendu littéral MASTER 2350 (couleurs/espaces du fichier Excel) — styles inline : rebuild SCSS gelée.
     Modèle visuel demandé par Denis (2 items) :
       1. le « O » (la case à cocher) + le texte permanent (colonne C) = inchangé, en rouge pour le O;
       2. les champs « AUTRE PAR FOURNISSEUR » (info saisie par le fournisseur) = EN GRIS PÂLE. --}}
<style>
    .service-literal { white-space: pre-wrap; }
    .form__column--gap-before { margin-top: 1.1em; }
    /* le « O » à cocher en rouge (marqueur du master) — scopé au formulaire 2350 */
    .form-2350 sl-checkbox::part(control) { border-color: #d33; }
    /* champ « PRÉCISEZ » : gris pâle, pour distinguer l'info du fournisseur du texte permanent.
       Textarea auto-extensible : démarre sur 1 ligne (sur la ligne du service) puis grandit au besoin. */
    .form-2350 .supplier-input {
        background: #f4f4f4 !important;
        color: #6b6b6b !important;
        border: 1px dashed #c9c9c9 !important;
    }
    .form-2350 textarea.supplier-input {
        min-height: 2.4em; line-height: 1.4; padding: 7px 10px; width: 100%;
        /* overflow VISIBLE en défilement : avec « hidden », si l'auto-agrandissement ne
           suivait pas, la 2e ligne devenait invisible — Denis : « je ne peux pas écrire
           dans plusieurs lignes ». */
        font: inherit; resize: vertical; overflow-y: auto; box-sizing: border-box;
    }
    .form-2350 .supplier-input::placeholder { color: #b0b0b0 !important; font-style: italic; }
    /* « Précisez » verrouillé tant que le « O » du service n'est pas coché (demande Denis 22.06) */
    .form-2350 .supplier-input--locked { opacity: .45 !important; cursor: not-allowed; background: #efefef !important; }
</style>

<div class="form-2350">

<div class="form__column">
    <div class="registration-title">{{ __('auth.register.services') }}</div>
    <div class="form__row--error">
        @foreach($errors->get('services', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    @foreach ($serviceCategory->services as $service)
        @if (!$service->title) @continue @endif
        <div class="form__column {{ $service->gap_before ? 'form__column--gap-before' : '' }}">
            <div class="form__row">
                @php $svcChecked = in_array($service->id, old('services') ?? $existingServices ?? (session('registerFormData.services') ?? [])); @endphp
                <sl-checkbox
                    name="services[]"
                    value="{{ $service->id }}"
                    @checked($svcChecked)
                >
                    <span class="service-literal">{!! $service->formatted_title ?: e($service->title) !!}</span>
                </sl-checkbox>
                @if ($service->has_input || !empty($service->input_label))
                    <textarea class="supplier-input @if(!$svcChecked) supplier-input--locked @endif" rows="1"
                           name="service_input[{{ $service->id }}]"
                           @if(!$svcChecked) disabled @endif
                           placeholder="{{ $service->input_label ?: __('form.specify') }}"
                           oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                    >{{ old('service_input.' . $service->id) ?? ($existingServiceInputs[$service->id] ?? (session('registerFormData.service_input.' . $service->id) ?? '')) }}</textarea>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="form__column">
    <div class="form__row--error">
        @foreach($errors->get('custom_services', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    <add-item-button
        class="addition-link"
        name="custom_services"
        items="{{ json_encode(old('custom_services') ?? $existingCustomServices ?? (session('registerFormData.custom_services') ?? [])) }}"
    >{{ __('main.add-service') }}</add-item-button>
</div>

@if (trim(strip_tags($serviceCategory->customers_text ?? '')) !== '')
    <div class="form__column">
        <div class="master-2350-note">{!! $serviceCategory->customers_text !!}</div>
    </div>
@endif

@if (trim(strip_tags($serviceCategory->capabilities_text ?? '')) !== '')
    <div class="form__column">
        <div class="master-2350-note">{!! $serviceCategory->capabilities_text !!}</div>
    </div>
@endif

<div class="form__column">
    <div class="registration-title">{{ __('auth.register.capabilities') }}</div>
    <div class="form__row--error">
        @foreach($errors->get('capabilities', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>

    @foreach ($serviceCategory->capabilities as $service)
        @if (!$service->title) @continue @endif
        <div class="form__column {{ $service->gap_before ? 'form__column--gap-before' : '' }}">
            <div class="form__row">
                @php $capChecked = in_array($service->id, old('capabilities') ?? $existingCapabilities ?? (session('registerFormData.capabilities') ?? [])); @endphp
                <sl-checkbox
                    name="capabilities[]"
                    value="{{ $service->id }}"
                    @checked($capChecked)
                >
                    <span class="service-literal">{!! $service->formatted_title ?: e($service->title) !!}</span>
                </sl-checkbox>
                @if ($service->has_input || !empty($service->input_label))
                    <textarea class="supplier-input @if(!$capChecked) supplier-input--locked @endif" rows="1"
                           name="capability_input[{{ $service->id }}]"
                           @if(!$capChecked) disabled @endif
                           placeholder="{{ $service->input_label ?: __('form.specify') }}"
                           oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                    >{{ old('capability_input.' . $service->id) ?? ($existingCapabilityInputs[$service->id] ?? (session('registerFormData.capability_input.' . $service->id) ?? '')) }}</textarea>
                @endif
            </div>
        </div>
    @endforeach
</div>

<div class="form__column">
    <div class="form__row--error">
        @foreach($errors->get('custom_capabilities', '<small style="color: red">:message</small>') as $error)
            {!! $error !!}
        @endforeach
    </div>
    <add-item-button
        class="addition-link"
        name="custom_capabilities"
        items="{{ json_encode(old('custom_capabilities') ?? $existingCustomCapabilities ?? (session('registerFormData.custom_capabilities') ?? [])) }}"
    >{{ __('main.add-capability') }}</add-item-button>
</div>

</div>{{-- /.form-2350 --}}

{{-- « Précisez » verrouillé tant que le « O » du service n'est pas coché (Denis 22.06) :
     - désactivé + grisé quand la case n'est pas cochée;
     - et son « name » est retiré pour qu'un service non coché n'envoie aucune précision.
     (Remplace l'ancien composant gelé step2ConditionnalCheckboxInput, cassé depuis le
     passage de <input> à <textarea>.) Ajuste aussi la hauteur des champs déjà remplis. --}}
<script>
    (function () {
        document.querySelectorAll('.form-2350 .form__row').forEach(function (row) {
            var box = row.querySelector('sl-checkbox');
            var input = row.querySelector('.supplier-input');
            if (!input) return;
            if (input.value.trim() !== '') { input.style.height = 'auto'; input.style.height = input.scrollHeight + 'px'; }
            if (!box) return;
            var originalName = input.getAttribute('name') || '';
            function sync() {
                if (box.checked) {
                    input.disabled = false;
                    if (originalName) input.setAttribute('name', originalName);
                    input.classList.remove('supplier-input--locked');
                } else {
                    input.disabled = true;
                    input.removeAttribute('name');
                    input.classList.add('supplier-input--locked');
                }
            }
            sync();
            box.addEventListener('sl-change', sync);
            if (window.customElements) { customElements.whenDefined('sl-checkbox').then(sync); }
        });
    })();
</script>
