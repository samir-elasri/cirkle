<div class="bloc-pubs">
	@foreach($pubs as $item)
		@if(!empty($item->url))
			<a href="{{ $item->url }}" aria-label="{{ (isset($isTargetBlank) && $isTargetBlank == true) ?  $item->title . ' – Nouvelle fenêtre' : $item->title }}" <?php if(isset($isTargetBlank) && $isTargetBlank == true) echo 'target="_blank"'; ?>  class="pub">
		@else
			<div class="pub">
		@endif

					@if(!empty($item->imageSrc))
						<span class="cell img">
							{{Html::image($item->imageSrc, isset($item->title) ? $item->title : '', ['width' => 300])}}
						</span>
					@endif

					@if(!empty($item->title) || !empty($item->content))
						<div class="cell">
							<div class="container <?php echo (!empty($item->imageSrc)) ? 'onImage' : '';?>">
								@if(!empty($item->title))
									<div class="title">{!! $item->title !!}</div>
								@endif
								@if(!empty($item->content))
									<div class="content">{!! $item->content !!}</div>
								@endif
							</div>
						</div>
					@endif


		@if(!empty($item->url))
			</a>
		@else
			</div>
		@endif

	@endforeach
</div>