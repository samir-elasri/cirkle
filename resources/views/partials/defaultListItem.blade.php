<?php
// Use with defaultList & defaultImagesList

$showLearnMore = !isset($showLearnMore) ? true : $showLearnMore; // default
$lockState = '';
$lockMsg = '';
$restrictedClass = '';
if(isset($restricted) && $restricted == true){
    if((isset($unlocked) && $unlocked == true)){
        $lockState = 'unlocked';
    } else {
        $lockState = 'locked';
        $restrictedClass = 'restricted';
        $lockMsg = 'Ce contenu est réservé aux membres de l\'AQPM';
    }
}
if(isset($image)) $imageLeft = $image;
?>

<article class="{{$cssClass ?? ''}}">
    @if(!empty($url))
        <a href="{{$url}}" class="{{$restrictedClass}}" title="{{$lockMsg}}" <?php if(isset($isTargetBlank) && $isTargetBlank == true) echo 'target="_blank"';?>>
    @else
        <div>
    @endif

        @if(!empty($imageLeft))
            <div class="image left">
                <div class="container">
                    {!! imageCache($imageLeft, ['width' => 300]) !!}
                </div>
            </div>
        @endif
        @if(!empty($docType))
            <div class="picto">
                <div class="container">
                    <span class="{{$lockState}}"></span>
                    <span class="{{$docType}}"></span>
                </div>
            </div>
        @endif
        <div class="main">
            @if(!empty($title))
                <header>
                    <h3>{{$title}}</h3>
                </header>
            @endif
            @if(!empty($date))
                <div class="time">
                    {{getHTMLTagTime($date, (!empty($dateEnd) ? $dateEnd : null))}}
                </div>
            @endif
            @if(!empty($content))
                <div class="description">{{$content}}</div>
            @endif
            @if(!empty($url) && $showLearnMore)
                <p>
                    <span class="learnMore">{{__('main.learnMore')}}</span>
                </p>
            @endif
            @if(!empty($urlBtn))
                <p>
                    <a href="{{$urlBtn}}" class="learnMore">{{__('main.learnMore')}}</a>
                </p>
            @endif
        </div>
        @if(!empty($imageRight))
            <div class="image right">
                <div class="container">
                    {!! Html::image($imageRight, null, ['width' => 300]) !!}
                </div>
            </div>
        @endif

    @if(!empty($url))
        </a>
    @else
        </div>
    @endif
</article>
