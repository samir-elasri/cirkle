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

			{{-- Onglet statique : Idéologie / Engagement (Denis 30.06) --}}
			<div class="header-menu-item on-desktop">
				<a href="{{ urlRouteName('ideologie') }}">{{ app()->getLocale() === 'en' ? 'Ideology / Commitment' : 'Idéologie / Engagement' }}</a>
			</div>
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

			{{-- Menu mobile (Denis/Steve 21.07) : le thème mmenu d'origine n'avait
			     jamais de bouton d'ouverture → pas de navigation sur téléphone. Menu
			     autonome (ck-mnav), scopé, qui n'entre pas en conflit. --}}
			<button type="button" class="ck-burger" aria-label="Menu" aria-expanded="false" aria-controls="ckMnav" onclick="ckMnavToggle()">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="ck-mnav-overlay" id="ckMnavOverlay" onclick="ckMnavClose()" hidden></div>
<nav class="ck-mnav" id="ckMnav" aria-label="{{ app()->getLocale()==='en'?'Mobile menu':'Menu mobile' }}" aria-hidden="true">
	<button type="button" class="ck-mnav__close" aria-label="{{ app()->getLocale()==='en'?'Close':'Fermer' }}" onclick="ckMnavClose()">&times;</button>
	<a href="{{ urlRouteName('home') }}">{{ app()->getLocale()==='en'?'Home':'Accueil' }}</a>
	@foreach($mainMenu as $item)
		@continue(\Illuminate\Support\Str::contains(strtolower(strip_tags($item->title)), ['accueil','home']))
		<a href="{{ $item->url }}" target="{{ $item->target_blank ? '_blank' : '_self' }}">{!! str_replace("\\n", " ", strip_tags($item->title)) !!}</a>
	@endforeach
	<a href="{{ urlRouteName('ideologie') }}">{{ app()->getLocale()==='en'?'Ideology / Commitment':'Idéologie / Engagement' }}</a>
	<a class="ck-mnav__cta" href="{{ logged_in() ? urlRouteName('subscriber.logout') : urlRouteName('profile') }}">{{ logged_in() ? __('auth.profile.logout') : __('auth.profile.login') }}</a>
</nav>

<style>
	/* ── Menu mobile + garde-fous responsives (Denis 21.07) ─────────────
	   La rebuild SCSS est gelée : tout est ajouté ici, scopé, sans toucher
	   au CSS compilé. --}} */
	.ck-burger{ display:none; }
	.ck-mnav{
		position:fixed; top:0; right:0; bottom:0; width:min(84vw,340px);
		background:#fff; transform:translateX(105%); transition:transform .25s ease;
		z-index:100000; box-shadow:-12px 0 40px rgba(10,20,14,.20);
		display:flex; flex-direction:column; padding:18px 20px calc(18px + env(safe-area-inset-bottom));
		overflow-y:auto; -webkit-overflow-scrolling:touch;
	}
	.ck-mnav.open{ transform:translateX(0); }
	.ck-mnav a{ display:block; padding:13px 6px; color:#16241b; text-decoration:none;
		font-weight:600; font-size:1.05rem; border-bottom:1px solid #eef2ee; }
	.ck-mnav a:active{ color:#00893e; }
	.ck-mnav__close{ align-self:flex-end; border:none; background:transparent; cursor:pointer;
		font-size:2rem; line-height:1; color:#16241b; padding:0 4px 6px; }
	.ck-mnav__spacer{ flex:1 1 auto; min-height:16px; }
	.ck-mnav__cta{ background:#ffd200; border-radius:10px; text-align:center; font-weight:800 !important;
		border-bottom:none !important; margin-top:8px; }
	.ck-mnav-overlay{ position:fixed; inset:0; background:rgba(10,20,14,.45);
		opacity:0; visibility:hidden; transition:opacity .25s, visibility .25s; z-index:99999; }
	.ck-mnav-overlay.open{ opacity:1; visibility:visible; }
	body.ck-noscroll{ overflow:hidden; }

	@media (max-width:1000px){
		.ck-burger{ display:inline-flex; flex-direction:column; justify-content:center; gap:5px;
			width:44px; height:44px; padding:11px; border:none; background:transparent; cursor:pointer; color:#16241b; }
		.ck-burger span{ display:block; height:2.5px; width:100%; background:currentColor; border-radius:2px; }
		/* on garde « Connexion » visible dans l'entête; on masque juste le compteur
		   de membres pour laisser la place au burger sur petit écran. */
		.header__member-counter{ display:none; }
	}

	/* ── Garde-fous overflow : autoriser les items grid/flex à rétrécir
	   (corrige le blowout min-width:auto qui rendait la page plus large que
	   l'écran) + colonnes uniques sur téléphone. --}} */
	@media (max-width:768px){
		html, body{ overflow-x:hidden; max-width:100%; }
		/* Carrousel : sans image, il ne laissait qu'une bande vide dont le texte
		   « Promotion » débordait sur le sélecteur. Le message promo reste affiché
		   dans la bannière juste en dessous. À revoir quand des caricatures y seront. */
		.slideshow{ display:none !important; }
		.ck-home__cols{ grid-template-columns:1fr !important; }
		.ck-home__col{ min-width:0 !important; max-width:100% !important; }
		.ck-cat-cards{ grid-template-columns:1fr !important; }
		.ck-cat-card{ min-width:0 !important; }
		.platform-selector__tile{ flex:1 1 100% !important; max-width:none !important; }
		.ck-home__col h3, .ck-home__col p, .ck-home__col li, .ck-cat-chip, .ck-cat-card__title{ overflow-wrap:anywhere; }
		.optimal-content-width, .wide-content-width{ min-width:0; }
		/* Recherche code postal : champ + bouton empilés (le bouton débordait). */
		.ck-home__postal .form__row{ flex-wrap:wrap; }
		.ck-home__postal .form__column{ flex:1 1 100%; min-width:0; }
		.ck-home__postal #postal_code{ width:100%; }
		.ck-home__postal .call-to-action{ width:100%; text-align:center; }
	}
</style>
<script>
	function ckMnavToggle(){ document.getElementById('ckMnav').classList.contains('open') ? ckMnavClose() : ckMnavOpen(); }
	function ckMnavOpen(){
		document.getElementById('ckMnav').classList.add('open');
		var o=document.getElementById('ckMnavOverlay'); o.hidden=false; requestAnimationFrame(function(){o.classList.add('open');});
		document.body.classList.add('ck-noscroll');
		document.querySelector('.ck-burger')?.setAttribute('aria-expanded','true');
		document.getElementById('ckMnav').setAttribute('aria-hidden','false');
	}
	function ckMnavClose(){
		document.getElementById('ckMnav').classList.remove('open');
		var o=document.getElementById('ckMnavOverlay'); o.classList.remove('open'); setTimeout(function(){o.hidden=true;},250);
		document.body.classList.remove('ck-noscroll');
		document.querySelector('.ck-burger')?.setAttribute('aria-expanded','false');
		document.getElementById('ckMnav').setAttribute('aria-hidden','true');
	}
	document.addEventListener('keydown', function(e){ if(e.key==='Escape') ckMnavClose(); });
</script>
