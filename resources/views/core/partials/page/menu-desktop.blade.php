<div id="menu-desktop">

    <!-- Menu corpo -->
    <ul class="menu-corpo">

        @if (!empty($setting->corpo_statement))
            <li class="statement">
                {{ $setting->corpo_statement }}
            </li>
        @endif

        <li class="search">
            {!! Form::open(['url' => urlRouteName('search-results'), 'method' => 'GET']) !!}
            {{ Form::text('q', null, ['placeholder' => trans('main.searchPlaceholder')]) }}
            <input type="submit"
                   style="all:unset;width:0;height:0;">
            {!! Form::close() !!}
        </li>

        {{--		<li class="spacer on-desktop"></li>--}}

        {{--		@foreach($socials as $item)--}}
        {{--			<li class="social on-desktop">--}}
        {{--				<a href="{{ $item->call_to_action_url }}" target="_blank">--}}
        {{--					<img src="{{ $item->image }}" alt="Nous suivre sur {{ $item->title }} - Nouvelle fenêtre" title="{{ $item->title }}"/>--}}
        {{--				</a>--}}
        {{--			</li>--}}
        {{--		@endforeach--}}


        {{--		<li class="on-desktop" style="margin-left: 10px">--}}
        {{--			<div class="font-scaling"><a role="button" aria-label="Augmenter/réduire la taille du texte." href="{{ urlRouteName('fontScaled') }}"><span>A</span><span>A</span></a></div>--}}
        {{--		</li>--}}

        @php $cartCount = Session::get('cart') ? count(Session::get('cart')) : 0; @endphp
        @if($cartCount > 0)
            <li class="menu-corpo__item menu-corpo__item--cart">
                <a href="{{ urlRouteName('cart') }}" aria-label="{{ __('cart.title') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:5px 13px;background:#ffd200;color:#222;border-radius:18px;font-weight:700;text-decoration:none;line-height:1;">
                    <span aria-hidden="true">🛒</span>
                    <span>{{ __('cart.title') }}</span>
                    <span style="background:#222;color:#fff;border-radius:10px;min-width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;padding:0 5px;">{{ $cartCount }}</span>
                </a>
            </li>
        @endif

        {{--		<li class="spacer on-desktop"></li>--}}

        @foreach($corpoMenu as $item)
            <li>
                <a href="{{ $item->url }}"
                   target="{{ $item->target_blank ? '_blank' : '_self' }}"
                   class="{{ $item->class }}">
                    {{ $item->title }}
                </a>
            </li>
        @endforeach

        @if(isMultilingual() && count(getLocales()) > 1)
            @foreach($localeLinks as $locale => $url)
                <li class="locale">
                    <a href="{{ $url }}"
                       class="nav-link"
                       lang="{{ $locale }}"
                       aria-label="{{ __('properties.lang.' . $locale) }}">
                        {{ strtoupper($locale) }}
                    </a>
                </li>
            @endforeach
        @endif

        @if(logged_in())
            <li>
                <a href="{{urlRouteName('subscriber.logout')}}">
                  @lang('auth.profile.logout')
                </a>
            </li>
        @endif
    </ul>

    <!-- Menu principal -->
    <ul class="menu-main">
        @foreach($mainMenu as $item)
            <li class="{{ $item->class }}">
                <a href="{{ $item->url }}"
                   target="{{ $item->target_blank ? '_blank' : '_self' }}"
                   class="{{ $item->class }}">
                    {{ str_replace("\\n", "<br/>", $item->title) }}
                </a>
                <!-- Niveau 2 -->
                @if($item->hasChildren)
                    <ul>
                        @foreach($item->children as $item2)
                            <li class="{{ $item2->class }}">
                                <a href="{{ $item2->url }}"
                                   target="{{ $item2->target_blank ? '_blank' : '_self' }}"
                                   class="{{ $item2->class }}">
                                    {{ str_replace("\\n", "<br/>", $item2->title) }}
                                </a>
                                <!-- Niveau 3 -->
                                @if($item2->hasChildren)
                                    <ul>
                                        @foreach($item2->children as $item3)
                                            <li class="{{ $item3->class }}">
                                                <a href="{{ $item3->url}}"
                                                   target="{{ $item3->target_blank ? '_blank' : '_self' }}"
                                                   class="{{ $item3->class }}">
                                                    {{ str_replace("\\n", "<br/>", $item3->title) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>

</div>
