@component('mail::message')
# @lang('auth.profile.greetings')

@component('mail::raw')
	{!! $text !!}
@endcomponent

@component('mail::raw')
	{!! $footer !!}
@endcomponent
@endcomponent
