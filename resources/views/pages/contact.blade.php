{{-- PAGE CONTACT — mise en page dédiée (Denis 22.07).
     Rendue via PageController@standard (customStandardViews['contact'] → cette vue),
     donc l'URL existante /{id}/contact (page 29) reste valide.
     Le formulaire poste vers contactForm.post → PageController@contactUs
     (anti-bot : honeypot ck_website + middleware recaptcha si activé plus tard).
     Styles inline : la rebuild SCSS est gelée (voir docs/gap-map.md). --}}
@php $loc = app()->getLocale(); $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr; @endphp

<section class="ck-contact">
	<div class="optimal-content-width">
		<style>
			.ck-contact { padding: 8px 0 48px; }
			.ck-contact__title { text-align:center; font-size:2rem; font-weight:900; color:#00993a; margin:.3em 0 .7em; }
			.ck-contact__flash { max-width:820px; margin:0 auto 20px; border-radius:12px; padding:14px 18px; font-weight:600; line-height:1.5; }
			.ck-contact__flash--ok { background:#e7f7ec; border:1px solid #00993a; color:#0b5127; }
			.ck-contact__flash--err { background:#fff8e1; border:1px solid #ffd200; color:#7a5b00; }

			.ck-contact__top { display:grid; grid-template-columns:1fr 1fr; gap:28px; align-items:stretch; margin-bottom:34px; }
			.ck-contact__info { background:#f6faf7; border:1px solid #e2efe6; border-radius:16px; padding:26px 26px 22px; }
			.ck-contact__op { display:flex; align-items:center; gap:14px; margin-bottom:14px; }
			.ck-contact__op-badge { flex:0 0 auto; width:66px; height:66px; border-radius:50%; background:#00993a; display:flex; align-items:center; justify-content:center; box-shadow:0 6px 18px rgba(0,153,58,.28); }
			.ck-contact__op-badge svg { width:38px; height:38px; }
			.ck-contact__op-title { font-weight:800; font-size:1.15rem; color:#0b3d29; line-height:1.2; }
			.ck-contact__op-title span { display:block; font-weight:600; font-size:.9rem; color:#4a6152; }
			.ck-contact__welcome { line-height:1.6; color:#1f2733; margin:.2em 0 1.1em; }
			.ck-contact__meta { list-style:none; margin:0; padding:0; }
			.ck-contact__meta li { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-top:1px solid #e7efe9; line-height:1.45; color:#1f2733; }
			.ck-contact__meta li:first-child { border-top:none; }
			.ck-contact__meta .ic { flex:0 0 auto; font-size:1.1rem; line-height:1.3; }
			.ck-contact__meta a { color:#00993a; font-weight:700; text-decoration:none; }
			.ck-contact__meta a:hover { text-decoration:underline; }

			.ck-contact__map { border-radius:16px; overflow:hidden; border:1px solid #e2efe6; min-height:320px; box-shadow:0 6px 22px rgba(10,20,14,.08); }
			.ck-contact__map iframe { display:block; width:100%; height:100%; min-height:320px; border:0; }

			.ck-contact__formwrap { max-width:760px; margin:0 auto; background:#fff; border:1px solid #e2efe6; border-radius:16px; padding:28px 26px; box-shadow:0 8px 26px rgba(10,20,14,.06); }
			.ck-contact__formtitle { text-align:center; font-size:1.5rem; font-weight:900; color:#0b3d29; margin:.1em 0 1em; }
			.ck-contact__errors { background:#fdecec; border:1px solid #e0b4b4; color:#8a1f1f; border-radius:10px; padding:12px 16px; margin-bottom:18px; }
			.ck-contact__errors ul { margin:.2em 0 0 1.1em; }
			.ck-contact__field { margin-bottom:16px; }
			.ck-contact__field label { display:block; font-weight:700; color:#243b30; margin-bottom:6px; font-size:.95rem; }
			.ck-contact__field .req { color:#d33; }
			.ck-contact__field input,
			.ck-contact__field textarea { width:100%; border:1px solid #cfe0d6; border-radius:10px; padding:12px 14px; font:inherit; color:#1f2733; background:#fcfffd; }
			.ck-contact__field input:focus,
			.ck-contact__field textarea:focus { outline:none; border-color:#00993a; box-shadow:0 0 0 3px rgba(0,153,58,.15); }
			.ck-contact__field textarea { resize:vertical; min-height:150px; }
			.ck-contact__hint { font-size:.82rem; color:#6a7d71; margin-top:5px; }
			.ck-contact__submit { text-align:center; margin-top:8px; }
			.ck-contact__submit button { cursor:pointer; border:none; background:#00993a; color:#fff; font-weight:800; font-size:1.05rem; padding:14px 34px; border-radius:12px; box-shadow:0 6px 18px rgba(0,153,58,.28); }
			.ck-contact__submit button:hover { background:#0b7f36; }
			.ck-contact__note { text-align:center; font-size:.82rem; color:#6a7d71; margin-top:12px; }
			/* Honeypot anti-robot : hors écran, jamais rempli par un humain. */
			.ck-hp { position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden; }

			@media (max-width:768px) {
				.ck-contact__top { grid-template-columns:1fr; }
				.ck-contact__title { font-size:1.6rem; }
				.ck-contact__map { min-height:260px; }
				.ck-contact__map iframe { min-height:260px; }
			}
		</style>

		{{-- Le titre « Contact » est déjà rendu par l'entête de page du CMS ; on n'en
		     remet pas un deuxième ici pour éviter le doublon. --}}
		@if(session('success'))
			<div class="ck-contact__flash ck-contact__flash--ok">{{ session('success') }}</div>
		@endif
		@if(session('error'))
			<div class="ck-contact__flash ck-contact__flash--err">{{ session('error') }}</div>
		@endif

		<div class="ck-contact__top">
			{{-- Coordonnées + téléphoniste --}}
			<div class="ck-contact__info">
				<div class="ck-contact__op">
					<span class="ck-contact__op-badge" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M4 13v-1a8 8 0 0 1 16 0v1"/>
							<path d="M20 15a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2z"/>
							<path d="M4 15a2 2 0 0 0 2 2h1v-5H6a2 2 0 0 0-2 2z"/>
							<path d="M18 17v1a3 3 0 0 1-3 3h-2"/>
							<circle cx="12" cy="21" r="1" fill="#fff" stroke="none"/>
						</svg>
					</span>
					<span class="ck-contact__op-title">{{ $t('Service à la clientèle', 'Customer service') }}
						<span>{{ $t('Pour toutes questions et urgences', 'For all questions and emergencies') }}</span>
					</span>
				</div>

				<p class="ck-contact__welcome">{{ $t(
					'Pour toutes questions et urgences. 24h à 48h pour une réponse par courriel ou téléphone. Selon les jours fériés ou en raison de mère nature.',
					'For all questions and emergencies. 24 to 48h for a reply by email or phone. Subject to holidays or acts of nature.'
				) }}</p>

				<ul class="ck-contact__meta">
					<li><span class="ic">📍</span><span>1550&nbsp;Place&nbsp;Hutchins, Dorval, QC&nbsp;H9P&nbsp;1S2</span></li>
					<li><span class="ic">📞</span><a href="tel:+15149495611">(514)&nbsp;949-5611</a></li>
					<li><span class="ic">✉️</span><a href="mailto:servclient@cirkleservices.com">servclient@cirkleservices.com</a></li>
				</ul>
			</div>

			{{-- Carte Google (intégration sans clé API) --}}
			<div class="ck-contact__map">
				<iframe
					title="{{ $t('Carte — 1550 Place Hutchins, Dorval', 'Map — 1550 Place Hutchins, Dorval') }}"
					src="https://maps.google.com/maps?q=1550+Place+Hutchins,+Dorval,+QC+H9P+1S2&z=15&output=embed"
					loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
			</div>
		</div>

		{{-- Formulaire « Contact rapide » --}}
		<div class="ck-contact__formwrap">
			<h2 class="ck-contact__formtitle">{{ $t('Contact rapide', 'Quick contact') }}</h2>

			@if($errors->any())
				<div class="ck-contact__errors">
					<strong>{{ $t('Veuillez corriger :', 'Please fix:') }}</strong>
					<ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
				</div>
			@endif

			<form method="POST" action="{{ urlRouteName('contactForm.post') }}" class="ck-contact__form">
				@csrf

				{{-- Honeypot : laissé vide par les humains ; rempli par les robots → rejeté. --}}
				<div class="ck-hp" aria-hidden="true">
					<label>{{ $t('Ne pas remplir', 'Do not fill') }}
						<input type="text" name="ck_website" tabindex="-1" autocomplete="off">
					</label>
				</div>

				<div class="ck-contact__field">
					<label for="ck-name">{{ $t('Nom et prénom', 'Full name') }} <span class="req">*</span></label>
					<input id="ck-name" type="text" name="name" value="{{ old('name') }}" required maxlength="150">
				</div>

				<div class="ck-contact__field">
					<label for="ck-email">{{ $t('Courriel', 'Email') }} <span class="req">*</span></label>
					<input id="ck-email" type="email" name="email" value="{{ old('email') }}" required maxlength="190">
				</div>

				<div class="ck-contact__field">
					<label for="ck-phone">{{ $t('Téléphone', 'Phone') }}</label>
					<input id="ck-phone" type="tel" name="phone" value="{{ old('phone') }}" maxlength="50">
				</div>

				<div class="ck-contact__field">
					<label for="ck-message">{{ $t('Message', 'Message') }} <span class="req">*</span></label>
					<textarea id="ck-message" name="message" required maxlength="6000">{{ old('message') }}</textarea>
					<div class="ck-contact__hint">{{ $t('Maximum 1000 mots.', 'Maximum 1000 words.') }}</div>
				</div>

				<div class="ck-contact__submit">
					<button type="submit">{{ $t('Envoyer', 'Send') }}</button>
				</div>

				<p class="ck-contact__note">{{ $t(
					'Merci de prendre contact avec nous ! 24h à 48h pour une réponse par courriel ou téléphone.',
					'Thank you for reaching out! 24 to 48h for a reply by email or phone.'
				) }}</p>
			</form>
		</div>
	</div>
</section>
