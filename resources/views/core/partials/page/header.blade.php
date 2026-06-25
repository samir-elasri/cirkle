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
			@if(!empty($latestMemberNumber))
				{{-- Compteur de membres : plus récent numéro de la séquence partagée C/F, sans la lettre --}}
				{{-- Styles inline temporaires : la rebuild SCSS est gelée (voir docs/gap-map.md) --}}
				<style>
					.header__member-counter {
						display: inline-flex;
						align-items: baseline;
						gap: .35em;
						margin-right: 1em;
						white-space: nowrap;
					}
					.header__member-counter-label {
						font-size: .8em;
						text-transform: uppercase;
						letter-spacing: .04em;
					}
					.header__member-counter-value {
						font-weight: 700;
						font-variant-numeric: tabular-nums;
					}
				</style>
				<div class="header__member-counter" title="@lang('main.memberCounterTitle')">
					<span class="header__member-counter-label">@lang('main.memberCounter')</span>
					<span class="header__member-counter-value">{{ number_format((int) $latestMemberNumber) }}</span>
				</div>
			@endif

			{{-- Raccourci panier : visible tant que le panier contient des articles
			     (de l'inscription jusqu'au paiement), pour voir/accéder à ce qui a été ajouté. --}}
			@php $ckCart = logged_in() ? Session::get('cart') : null; $ckCartCount = $ckCart ? count($ckCart) : 0; @endphp
			@if($ckCartCount > 0)
				<a href="{{ urlRouteName('cart') }}" class="header__cart" title="{{ __('cart.title') }}">
					<span aria-hidden="true">🛒</span>
					<span class="header__cart-text">{{ __('cart.title') }}</span>
					<span class="header__cart-count">{{ $ckCartCount }}</span>
				</a>
			@endif

			@if(logged_in())
				<a href="{{urlRouteName('subscriber.logout')}}" class="call-to-action">{{ __('auth.profile.logout') }}</a>
			@else
				<a href="{{urlRouteName('profile')}}" class="call-to-action">{{ __('auth.profile.login') }}</a>
			@endif
		</div>
	</div>
</header>
