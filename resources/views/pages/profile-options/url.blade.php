<section>
    <div class="optimal-content-width">
        @if (!isset($registrationForm))
            {{Form::open(['url' => urlRouteName('url.update')])}}
        @endif

        {{-- Forfait site web (MASTER 2350) : choisir UN SEUL couple palier × durée --}}
        @php $forfaitOld = old('url_forfait') ?? ($data->url_forfait ?? '') ?? ''; @endphp
        <div class="form__column">
            <label for="url_forfait">Forfait site web — choisissez une seule option</label>
            <select name="url_forfait" id="url_forfait" class="form-control">
                <option value="">—</option>
                @foreach(\App\Support\WebsiteForfait::tiers() as $tier => $durations)
                    <optgroup label="Forfait {{ $tier }} $">
                        @foreach($durations as $months => $price)
                            @php $val = $tier . '-' . $months; @endphp
                            <option value="{{ $val }}" {{ $forfaitOld === $val ? 'selected' : '' }}>
                                {{ $months }} mois — {{ prettyPrice($price) }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="form__column">
            {{Form::label('fr[url]', trans('profile.options.fields.fr_url'))}}
            {{Form::input('text', 'fr[url]', $data->translate('fr')?->url)}}
        </div>
        <div class="form__column">
            {{Form::label('en[url]', trans('profile.options.fields.en_url'))}}
            {{Form::input('text', 'en[url]', $data->translate('en')?->url)}}
        </div>

        @if (!isset($registrationForm))
            {{Form::submit(trans('profile.options.update-url'), ['class' => 'call-to-action'])}}
            {{Form::close()}}
        @endif
    </div>
</section>
