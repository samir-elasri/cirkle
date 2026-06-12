{!! $blocs !!}
<section>
    <div class="optimal-content-width">
        <h4>
            @lang('subscription.subscriber-intro')
        </h4>
        @include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
        <div>
            <table width="100%" class="subscription-table" cellpadding="0">
                @foreach($rows as $key => $row)
                    <tr>
                        <td>
                            @lang("subscription.$key")
                        </td>
                        @if ($key === 'subscriptionPrices')
                            @foreach($row as $col)
                                <td class="col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}}">
                                    <div> @if(!$col->first())
                                            @lang('subscription.price.free')
                                        @else
                                            <table width="100%">
                                                @foreach($col as $price)
                                                    <tr>
                                                        <td>
                                                            {{prettyPrice($price?->cost)}} / @if($price->month_duration)
                                                                {{($price->month_duration > 1) ?($price->month_duration . ' ' . trans('subscription.price.month')) : trans('subscription.price.month')}}
                                                            @elseif($price->year_duration)
                                                                {{($price->year_duration > 1) ?($price->year_duration. ' '. trans('subscription.price.years')) : trans('subscription.price.year')}}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{$price?->text}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        @else
                            @foreach($row as $col)
                                <td class="col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}} {{($key === 'title')? 'top-row' : ''}}">
                                    <div>
                                        {{$col}}
                                    </div>
                                </td>
                            @endforeach
                        @endif
                    </tr>
                @endforeach
                @if (isset($subscriber))
                    <tr>
                        <td></td>
                        @foreach($ids as $id)
                            <td class="col-{{$id}} {{$recommended[$loop->index] ? 'recommended':''}}">
                                <div>
                                    {{ Form::open(['url' => urlRouteName('cart.store')]) }}
                                    {{ Form::hidden('product_type', 'App\Models\Core\Subscription') }}
                                    {{ Form::hidden('product_id', $id) }}
                                    <button @if($activeSubs[$loop->index]) disabled @endif type="submit"
                                            class="call-to-action @if($activeSubs[$loop->index]) disabled @endif ">@lang('subscription.buy')</button>
                                    {{Form::close() }}
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endif
                <tr>
                    <td>@lang('subscription.zone_serviced')</td>
                    @foreach($types as $type)
                        <td class="bottom-row col-{{$ids[$loop->index]}} {{$recommended[$loop->index] ? 'recommended':''}}">
                            <div>
                                {{ setting("{$type}_serviced_label") }}
                            </div>
                        </td>
                    @endforeach
                </tr>
            </table>
        </div>

    </div>
</section>

<style>
    table {
        text-align: center;
        /*border: 2px solid rgb(140 140 140);*/

        border-collapse: collapse;

        table {
            border: none;
            text-align: center;
        }
    }

</style>
