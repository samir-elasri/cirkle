@component('mail::message')
# @lang('auth.profile.greetings') {{ $subscriber->name }}

@component('mail::raw')
	{!! $text !!}
@endcomponent

@component('mail::raw')
	{!! $footer !!}
@endcomponent
@endcomponent
