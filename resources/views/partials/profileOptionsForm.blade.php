<div x-data="data()" class="profile-options-form">
    @foreach($profileOptions as $option)
        <div x-data="{ price: '{{setting("{$option}_price")}}', name: '{{$option}}' }">
            <h3>
                {{setting("{$option}_title", "{$option}_title")}}
            </h3>
            <div class="profile-options-form__description">
                {{setting("{$option}_description", "{$option}_description")}}
            </div>
			<label>
				<input @change="toggleOption($data)" type="checkbox" class="checkbox">
				{{ prettyPrice(setting("{$option}_price")) }}
			</label>
        </div>
    @endforeach

	<div>
		<h3>@lang('profile.profile_options.price_section')</h3>
		<div class="cart__summary">
			<div>@lang('cart.totals.sub_total')</div> <span x-text="subTotal"></span>
			<div>@lang('cart.totals.tps')</div> <span x-text="tpsTotal"></span>
			<div>@lang('cart.totals.tvq')</div> <span x-text="tvqTotal"></span>
			<div>@lang('cart.totals.total')</div> <span x-text="grandTotal"></span>
		</div>
	</div>

	<div class="profile-options-form__submit">
		{{Form::open(['url' => urlRouteName('cart.store')])}}
		<input name="items" x-model="inputItems" type="hidden">
			<button type="submit" class="call-to-action" :class="payable ?'' : 'disabled'"
					:disabled="!payable">
				@lang('profile.profile_options.pay')
			</button>
		{{Form::close()}}
	</div>
</div>

@push('profileOptionsScript')
    <script>
        function data() {
            let money = (number) => {
                number = number.toLocaleString(`{{app()->getLocale()}}-CA`, {
                    style: 'currency',
                    currency: 'CAD',
                });
                return number;
            };
            return {
                locale: '{{app()->getLocale()}}',
                subTotal: money(0),
                tpsTotal: money(0),
                tvqTotal: money(0),
                grandTotal: money(0),
                tps: {{setting('platform_tps')?? 0}},
                tvq: {{setting('platform_tvq')?? 0}},
                inputItems: [],
                items: {},
                payable: false,
                toggleOption({name, price}) {
                    this.items.hasOwnProperty(name) ? delete this.items[name] : this.items[name] = price;
                    this.subTotal = 0;
                    for (let key in this.items) {
                        this.subTotal += Number(this.items[key]);
                    }
                    this.payable = this.subTotal > 0;
                    let tpsAmount = this.subTotal * this.tps / 100;
                    let tvqAmount = this.subTotal * this.tvq / 100;
                    this.tpsTotal = money(tpsAmount);
                    this.tvqTotal = money(tvqAmount);
                    this.grandTotal = money(this.subTotal + tpsAmount + tvqAmount);
                    this.subTotal = money(this.subTotal);
                    this.inputItems = Object.keys(this.items);
                },
            };
        }
    </script>
@endpush
