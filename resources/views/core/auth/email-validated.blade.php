{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		<a class="call-to-action" href="{{urlRouteName('profile')}}">@lang('auth.profile.go-to')</a>
	</div>
</section>
