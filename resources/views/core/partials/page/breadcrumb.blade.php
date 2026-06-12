@if(!empty($breadcrumb))
	<nav class="breadcrumb full-content-width">
		<ul class="optimal-content-width">
            <li><a href="/" class="home" aria-label="Accueil"></a></li>
			@foreach($breadcrumb as $item)
				<li>
					@if($item->isActive || empty($item->url))
						<span>{{ $item->title }}</span>
					@else
						<a href="{{ $item->url }}"> {{ $item->title }}</a>
					@endif
				</li>
			@endforeach
		</ul>
	</nav>
@endif
