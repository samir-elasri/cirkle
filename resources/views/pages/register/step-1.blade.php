{!! $blocs !!}

<section class="ck-auth">
    <div class="optimal-content-width">
  
        <div class="content-card">

            <div class="content-card__header">
                <div>
                    <h3 class="content-card__header--title">
                        {{ __('auth.register.title.step1') }}
                    </h3>
                    <div class="content-card__label">
                        {{ __('auth.register.title.step1-subtitle') }}
                    </div>
                </div>
                @if(!isset($isEdit) || !$isEdit)
                    @include('partials.progress', ['length' => 5, 'active' => 1])
                @endif
            </div>

            @include('partials.help-note', ['text' => __('main.help.register-step1')])

            {!! Form::open([
                'url' => isset($isEdit) && $isEdit
                    ? urlRouteName('subscriber.profile.updateStep1')
                    : urlRouteName('subscriber.register.storeStep1')
            ]) !!}
                <input type="hidden" name="preference_language" value="{{ App::getLocale() }}">

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('company_name', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="company_name" id="company_name" 
                           value="{{ old('company_name') ?? (isset($subscriber) ? $subscriber->company_name : (session('registerFormData.company_name') ?? '')) }}" 
                           placeholder="{{ __('auth.register.company_name') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('owner_names', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="owner_names" id="owner_names"
                           value="{{ old('owner_names') ?? (isset($subscriber) ? $subscriber->owner_names : (session('registerFormData.owner_names') ?? '')) }}"
                           placeholder="{{ __('auth.register.owner_names') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('legal_form_id', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <sl-select name="legal_form_id"
                               value="{{ old('legal_form_id') ?? (isset($subscriber) ? $subscriber->legal_form_id : (session('registerFormData.legal_form_id') ?? '')) }}" 
                               placeholder="{{ __('auth.register.legal_form_id') }}">
                        @foreach ($legalForms as $category)
                            <sl-option value="{{ $category->id }}">{{ $category->title }}</sl-option>
                        @endforeach
                    </sl-select>
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('federal_tax_number', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="federal_tax_number" id="federal_tax_number" 
                           value="{{ old('federal_tax_number') ?? (isset($subscriber) ? $subscriber->federal_tax_number : (session('registerFormData.federal_tax_number') ?? '')) }}" 
                           placeholder="{{ __('auth.register.federal_tax_number') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('street', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="street" id="street" 
                           value="{{ old('street') ?? (isset($subscriber) ? $subscriber->street : (session('registerFormData.street') ?? '')) }}" 
                           placeholder="{{ __('auth.register.street') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('city', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="city" id="city" 
                           value="{{ old('city') ?? (isset($subscriber) ? $subscriber->city : (session('registerFormData.city') ?? '')) }}" 
                           placeholder="{{ __('auth.register.city') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('postal_code', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="postal_code" id="postal_code" 
                           value="{{ old('postal_code') ?? (isset($subscriber) ? $subscriber->postal_code : (session('registerFormData.postal_code') ?? '')) }}" 
                           placeholder="{{ __('auth.register.postal_code') }}">
                </div>

                <div class="form__column ">
                    <div class="form__row--error">
                        @foreach($errors->get('phone', '<small style="color: red">:message</small>') as $error)
                            {!! $error !!}
                        @endforeach
                    </div>
                    <input type="text" name="phone" id="phone" 
                           value="{{ old('phone') ?? (isset($subscriber) ? $subscriber->phone : (session('registerFormData.phone') ?? '')) }}" 
                           placeholder="{{ __('auth.register.phone') }}">
                </div>

                <div class="form__column ">
                    @foreach($errors->get('toll_free_phone', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="toll_free_phone" id="toll_free_phone" 
                           value="{{ old('toll_free_phone') ?? (isset($subscriber) ? $subscriber->toll_free_phone : (session('registerFormData.toll_free_phone') ?? '')) }}" 
                           placeholder="{{ __('auth.register.toll_free_phone') }}">
                </div>

                <div class="form__column ">
                    @foreach($errors->get('fax', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="fax" id="fax" 
                           value="{{ old('fax') ?? (isset($subscriber) ? $subscriber->fax : (session('registerFormData.fax') ?? '')) }}" 
                           placeholder="{{ __('auth.register.fax') }}">
                </div>

                <div class="form__column ">
                    @foreach($errors->get('email', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input pattern=".*@.*\..*" type="email" name="email" id="email" 
                           value="{{ old('email') ?? (isset($subscriber) ? $subscriber->email : (session('registerFormData.email') ?? '')) }}" 
                           placeholder="{{ __('auth.register.email') }}">
                </div>

                <div class="form__column ">
                    <label for="start_date">{{ __('auth.register.start_date') }}</label>
                    @foreach($errors->get('start_date', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ old('start_date') ?? (isset($subscriber) ? $subscriber->start_date : (session('registerFormData.start_date') ?? '')) }}" 
                           placeholder="{{ __('auth.register.start_date') }}">
                </div>

                <div class="form__column ">
                    @foreach($errors->get('insurance_coverage', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <input type="text" name="insurance_coverage" id="insurance_coverage" 
                           value="{{ old('insurance_coverage') ?? (isset($subscriber) ? $subscriber->insurance_coverage : (session('registerFormData.insurance_coverage') ?? '')) }}" 
                           placeholder="{{ __('auth.register.insurance_coverage') }}">
                </div>

                <div class="form__column ">
                    @foreach($errors->get('business_hours', '<small style="color: red">:message</small>') as $error)
                        {!! $error !!}
                    @endforeach
                    <textarea id="business_hours" name="business_hours" rows="6" placeholder="{{ __('auth.register.business_hours') }}">{{ old('business_hours') ?? (isset($subscriber) ? $subscriber->business_hours : (session('registerFormData.business_hours') ?? '')) }}</textarea>
                </div>

                <div class="content-card__footer">
                    <button type="submit" class="call-to-action">
                        @if(isset($isEdit) && $isEdit)
                            {{ __('form.save') }}
                        @else
                            {{ __('main.next') }}
                        @endif
                    </button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</section>
