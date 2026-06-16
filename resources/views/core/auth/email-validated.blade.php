{!! $blocs !!}
@include('core.partials.spacing', ['spacing' => $default_bloc_spacing])
<section>
	<div class="optimal-content-width">
		@if(session('success') || session('info'))
			<div style="margin-bottom:18px;padding:14px 18px;border-radius:10px;border:1px solid #e2e2e2;background:{{ session('success') ? '#eafaf0' : '#eef4fb' }};color:#1f2430;font-weight:600;">
				{{ session('success') ?? session('info') }}
			</div>
		@endif
		<a class="call-to-action" href="{{urlRouteName('profile')}}">@lang('auth.profile.go-to')</a>
	</div>
</section>
