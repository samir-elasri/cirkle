@if(config('google.maps.active'))
	<div data-component="googleMaps" class="google-maps {{ $bordered ?? false ? 'google-maps--bordered' : '' }}" style="width: {{ isset($width) ? $width . 'px' : '100%' }}; min-height: {{ $height ?? '400' }}px">
		<script type="application/json">{!! json_encode($data) !!}</script>
	</div>
@else
	@lang('main.google-maps.not-enabled')
@endif
