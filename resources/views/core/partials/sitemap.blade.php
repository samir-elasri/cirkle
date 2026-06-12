<div class="sitemap">
	<ul class="corpo">
		@foreach($corpoMenu as $item)
			<li>
				<a href="{{ $item->url }}">{{ $item->title }}</a>
			</li>
		@endforeach
	</ul>
	<ul class="main">
		@foreach($mainMenu as $item)
		<li>
			<a href="{{ $item->url }}">{{ str_replace("\\n", '', $item->title) }}</a>
			@if($item->hasChildren)
			<ul class="parent">
				@foreach($item->children as $item2)
				<li>
					<a href="{{ $item2->url }}">{{ $item2->title }}</a>
					@if($item2->hasChildren)
					<ul class="parent">
						@foreach($item2->children as $item3)
						<li>
							<a href="{{ $item3->url }}">{{ $item3->title }}</a>
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