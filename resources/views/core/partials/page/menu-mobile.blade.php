<div id="menu-mobile" style="display:none;">
    <div class="container">
        <h2>MENU</h2>
        <div class="menu-mobile__close-icon"></div>
        <nav>
			<ul>
				<li class="search">
					{!! Form::open(['url' => urlRouteName('search-results'), 'method' => 'GET']) !!}
						{{ Form::text('q', null, ['placeholder' => trans('main.searchPlaceholder')]) }}
						<input type="submit" style="all:unset;width:0;height:0;">
					{!! Form::close() !!}
				</li>
				<!-- Menu principal -->
				@foreach($mainMenu as $item)
				<li class="{{ $item->class }}">
					<a href="{{ $item->url }}" target="{{ $item->target_blank ? '_blank' : '_self' }}" class="{{ $item->class }}">
						{{ str_replace("\\n", "", $item->title) }}
					</a>
					<!-- Niveau 2 -->
					@if($item->hasChildren)
					<ul>
						@foreach($item->children as $item2)
						<li class="{{ $item2->class }}">
							<a href="{{ $item2->url }}" target="{{ $item2->target_blank ? '_blank' : '_self' }}" class="{{ $item2->class }}">
								{{ str_replace("\\n", "", $item2->title) }}
							</a>
							<!-- Niveau 3 -->
							@if($item2->hasChildren)
							<ul>
								@foreach($item2->children as $item3)
								<li class="{{ $item3->class }}">
									<a href="{{ $item3->url}}" target="{{ $item3->target_blank ? '_blank' : '_self' }}" class="{{ $item3->class }}">
										{{ str_replace("\\n", "", $item3->title) }}
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
				<!-- Menu corpo -->
				@foreach($corpoMenu as $item)
					<li class="{{ $item->class }}">
						<a href="{{ $item->url }}" target="{{ $item->target_blank ? '_blank' : '_self' }}" class="{{ $item->class }}">
							{{ $item->title }}
						</a>
						<!-- Niveau 2 -->
					@if($item->hasChildren)
					<ul>
						@foreach($item->children as $item2)
						<li class="{{ $item2->class }}">
							<a href="{{ $item2->url }}" target="{{ $item2->target_blank ? '_blank' : '_self' }}" class="{{ $item2->class }}">
								{{ str_replace("\\n", "", $item2->title) }}
							</a>
							<!-- Niveau 3 -->
							@if($item2->hasChildren)
							<ul>
								@foreach($item2->children as $item3)
								<li class="{{ $item3->class }}">
									<a href="{{ $item3->url}}" target="{{ $item3->target_blank ? '_blank' : '_self' }}" class="{{ $item3->class }}">
										{{ str_replace("\\n", "", $item3->title) }}
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
				@if(isMultilingual() && count(getLocales()) > 1)
					<li class="locales">
						{{ trans_choice('main.menu-mobile.locales', count(getLocales())) }}
						@foreach($localeLinks as $locale => $url)
							<a href="{{ $url }}" class="nav-link" lang="{{ $locale }}" aria-label="{{ __('properties.lang.' . $locale) }}">
								{{ strtoupper($locale) }}
							</a>
						@endforeach
					</li>
				@endif
				@if (App\Models\Core\BasicCart::isActive())
					<li><a href="{{urlRouteName('cart')}}">
							<i class="fas fa-shopping-cart"></i>&nbsp;
							<span class="cart-nb-items">{{Session::get('cart') ? count(Session::get('cart')) : 0}}</span></a>
					</li>
				@endif

                <li class="on-mobile">
					<div class="font-scaling"><a role="button" aria-label="Augmenter/réduire la taille du texte." href="{{ urlRouteName('fontScaled') }}"><span>A</span><span>A</span></a></div>
				</li>

				{{--<a
					style="display: none"
					data-component="addToHomeScreen"
				>
					<script type="application/json">
						{!!
							json_encode([
								'options' => [
								   'showCloseButton' => true,
								   'showCancelButton' => false,
								   'showConfirmButton' => true,
								   'confirmButtonText' => __('main.confirm'),
								   'customClass' => [
									   'confirmButton' => 'call-to-action'
									]
								],
								'title' => __('main.installation.title'),
								'androidContent' => $setting->android_install_text,
								'iosContent' => $setting->apple_install_text
							], JSON_THROW_ON_ERROR)
						!!}
					</script>
				</a>--}}
			</ul>
		</nav>
    </div>
</div>
<div class="menu-mobile__shadow" style="display:none;"></div>
