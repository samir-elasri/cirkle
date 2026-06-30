<footer class="page-footer">
	<div class="page-footer__content wide-content-width">
		<div class="page-footer__about">
			{!! setting('footer_about') !!}

			@if(setting()->socialsMiniCardGroup?->count())
				<div class="page-footer__socials">
					@foreach(setting()->socialsMiniCardGroup->cards as $item)
						<a href="{{ $item->call_to_action_url }}" target="_blank">
							<img
								src="{{ $item->image }}"
								alt="{{ $item->title }}"
								title="{{ $item->title }}"
								style="width:{{setting()->socialsMiniCardGroup->width}}px"
							/>
						</a>
					@endforeach
				</div>
			@endif
		</div>

		@foreach($footerMenu as $item)
			<div class="page-footer__menu">
				<h6 class="{{ $item->class }}">
					@if ($item->url !== '#undefined')
						<a href="{{ $item->url }}" target="{{ $item->target_blank ? '_blank' : '_self' }}" class="{{ $item->class }}">
					@endif
						{{ $item->title }}
					@if ($item->url !== '#undefined')
						</a>
					@endif
				</h6>

				@foreach($item->children as $item2)
					<a href="{{ $item2->url }}" target="{{ $item2->target_blank ? '_blank' : '_self' }}" class="{{ $item2->class }}">
						{{ str_replace("\\n", "<br/>", $item2->title) }}
					</a>
				@endforeach
			</div>
		@endforeach

		{{-- Lien statique : Résiliation (procédure d'annulation — Denis B7) --}}
		<div class="page-footer__menu">
			<a href="{{ urlRouteName('resiliation') }}">{{ app()->getLocale() === 'en' ? 'Cancellation' : 'Résiliation' }}</a>
		</div>
	</div>

	<div class="page-footer__copyright wide-content-width">
		<div>
			@php
				$copyright = str_replace('{{year}}', now()->year, setting('copyright_notice'));
			@endphp
			{{ $copyright }}
		</div>
	</div>
</footer>
