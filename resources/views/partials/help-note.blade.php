{{--
    Note explicative réutilisable (demande Denis 18.06 : « il faut que tout soit expliqué
    sur chaque page » — pour éviter les courriels de questions).
    Usage :  @include('partials.help-note', ['text' => __('main.help.home')])
    Option : ['title' => '...'] pour un titre en gras au-dessus du texte.
--}}
@php $hnText = trim($text ?? ''); @endphp
@if($hnText !== '')
    <div class="ck-help" role="note">
        <i class="fas fa-info-circle ck-help__icon" aria-hidden="true"></i>
        <div class="ck-help__body">
            @isset($title)<strong class="ck-help__title">{{ $title }}</strong>@endisset
            <span>{!! $hnText !!}</span>
        </div>
    </div>
@endif
