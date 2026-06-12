<section>
    <div class="optimal-content-width">
        @if (!isset($registrationForm))
            {{ Form::open(['url' => urlRouteName('estimations.update')]) }}
        @endif

        <div class="form__column">
            <input
                type="number"
                step="0.01"
                name="estimation_cost"
                id="estimation_cost"
                value="{{ $data->estimation_cost }}"
                placeholder="{{ __('providers.estimation_cost') }}"
            >
        </div>
        <div class="form__column">
            <div class="ui toggle checkbox">
                <input type="hidden" name="accepts_cash" value="0">
                <input type="checkbox"
                       name="accepts_cash"
                       @if ((bool)$data->accepts_cash) checked @endif
                       value="1">
                <label for="accepts_cash">@lang('providers.accepts_cash')</label>
            </div>
        </div>
        <div class="form__column">
            <div class="ui toggle checkbox">
                <input type="hidden" name="accepts_check" value="0">
                <input type="checkbox"
                       name="accepts_check"
                       @if((bool)$data->accepts_check) checked @endif
                       value="1">
                <label for="accepts_check">@lang('providers.accepts_check')</label>
            </div>
        </div>
        <div class="form__column">
            <div class="ui toggle checkbox">
                <input type="hidden" name="accepts_debit" value="0">
                <input type="checkbox"
                       name="accepts_debit"
                       @if((bool)$data->accepts_debit) checked @endif
                       value="1">
                <label for="accepts_debit">@lang('providers.accepts_debit')</label>
            </div>
        </div>
        <div class="form__column">
            <div class="ui toggle checkbox">
                <input type="hidden" name="accepts_credit" value="0">
                <input type="checkbox"
                       name="accepts_credit"
                       @if((bool)$data->accepts_credit) checked @endif
                       value="1">
                <label for="accepts_credit">@lang('providers.accepts_credit')</label>
            </div>
        </div>

        @if(!isset($registrationForm))
            {{Form::submit(trans('profile.update-info'), ['class' => 'call-to-action'])}}
            {{Form::close()}}
        @endif
    </div>
</section>