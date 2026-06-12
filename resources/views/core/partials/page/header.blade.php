@include('core.partials.page.menu-mobile')
<header class="header">
	<div class="header__content wide-content-width">
		<div>
			<a href="{{ urlRouteName('home') }}" aria-label="Accueil">
				<img class="header__logo" src="{{ setting('main_logo_image') }}" alt="@lang('main.companyName')">
			</a>
		</div>
		<div>
			@foreach($mainMenu as $item)
				<div class="header-menu-item on-desktop">
					<a href="{{ $item->url }}" target="{{ $item->target_blank ? '_blank' : '_self' }}" class="{{ $item->class }}">
						{{ str_replace("\\n", "<br/>", $item->title) }}
					</a>

					<div class="header-menu-item__children">
						@if($item->hasChildren)
							@foreach($item->children as $item2)
								<a href="{{ $item2->url }}" target="{{ $item2->target_blank ? '_blank' : '_self' }}" class="{{ $item2->class }}">
									{{ str_replace("\\n", "<br/>", $item2->title) }}
								</a>

								@if($item2->hasChildren)
									@foreach($item2->children as $item3)
										<a href="{{ $item3->url}}" target="{{ $item3->target_blank ? '_blank' : '_self' }}" class="{{ $item3->class }}">
											{{ str_replace("\\n", "<br/>", $item3->title) }}
										</a>
									@endforeach
								@endif
							@endforeach
						@endif
					</div>
				</div>
			@endforeach
		</div>

		<div class="header__buttons">
			@if(logged_in())
				<a href="{{urlRouteName('subscriber.logout')}}" class="call-to-action">{{ __('auth.profile.logout') }}</a>
			@else
				<a href="{{urlRouteName('profile')}}" class="call-to-action">{{ __('auth.profile.login') }}</a>
			@endif
		</div>
	</div>
</header>
