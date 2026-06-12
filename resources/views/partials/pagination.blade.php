<nav class="pagination">
	<div class="container-fluid site-width">
		{{-- PREVIOUS --}}
		@if(isset($prev->url))
		    <a href="{{$prev->url}}" data-page="prev"><span></span></a>
		@else
		    <a href="#" data-page="prev" class="disable"><span></span></a>
		@endif
		{{-- PAGES --}}
		@foreach($pages as $page)
		    <a href="{{$page->url}}" data-page="{{$page->label}}" class="{{!empty($page->active) ? 'active' : ''}}"><span>{{$page->label}}</span></a>
		@endforeach
		{{-- NEXT --}}
		@if(isset($next->url))
		    <a href="{{$next->url}}" data-page="next"><span></span></a>
		@else
			<a href="#" data-page="next" class="disable"><span></span></a>
		@endif
	</div>
</nav>
