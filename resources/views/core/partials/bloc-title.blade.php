@if(!empty($title))
	{{-- This is problematic because it is used illegally in a span --}}
	<h2 style="color:{{ empty($title_color) ? '' : $title_color }}" class="bloc__content-title" id="{{ $target }}">
		{{ $title }}
	</h2>
@endif
