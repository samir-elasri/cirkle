<div>
	{{-- Halo blanc sur le texte du carrousel (Denis 11.07) : le rend lisible
	     par-dessus une caricature claire et chargée. --}}
	<style>
		.slideshow__content h2,
		.slideshow__text,
		.slideshow__sub-text {
			text-shadow:
				0 0 6px #fff, 0 0 12px #fff,
				-1px -1px 1px #fff, 1px -1px 1px #fff,
				-1px 1px 1px #fff, 1px 1px 1px #fff;
		}
	</style>
	<section data-component="slideshow" data-slideshow-auto-play-speed="{{ $auto_play_speed }}" class="slideshow" >
		<div class="slides anim-fade-up" data-component="scrollfire" style="height: {{ round($height / $optimal_content_width * 100) }}vw; max-height: {{ $height }}px" >
			@foreach($slides as $slide)
				<div class="slide">
					@if(!empty($slide->filename_video))
						<video autoplay muted src="{{ $slide->filename_video }}" data-title="{{ $slide->title }}" data-video-id="{{ 'video_' . $slide->id }}"></video>				
					@elseif($slide->image)
						<img src="{{ $slide->image }}" alt="{{ $slide->title }}">
					@endif
					<div class="slideshow__content">
						<h2 class="on-desktop">{{ $slide->title }}</h2>
						
						<div class="slideshow__text on-desktop">
							{{ $slide->content }}
						</div>
						@if($slide->call_to_action_present)
							@if(!empty($slide->call_to_action_label))
								<a class="call-to-action on-desktop" href="{{ $slide->call_to_action_url }}">{{ $slide->call_to_action_label }}</a>
							@endif
						@endif
						<div class="slideshow__sub-text on-desktop">{{ $slide->sub_text }}</div>
					</div>
				</div>
			@endforeach
		</div>
		<div class="on-mobile slides-mobile">
			@foreach($slides as $slide)
			<div class="slide">
				<div class="slides-mobile__content on-mobile optimal-content-width">
					<h2>{{ $slide->mobile_title }}</h2>
					{{ $slide->content }}
					@if($slide->call_to_action_present)
						@if(!empty($slide->call_to_action_label_mobile))
							<a class="call-to-action" href="{{ $slide->call_to_action_url }}">{{ $slide->call_to_action_label_mobile }}</a>
						@endif
					@endif
				</div>
			</div>
			@endforeach
		</div>
		<hr class="on-mobile slideshow__end">
	</section>
</div>
