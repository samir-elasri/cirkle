{{-- Cœur « favori profession » (feature #11) — réutilise le composant JS `like` (générique). --}}
<span data-component="like">
    <script type="application/json">{!! json_encode([
        'url' => urlRouteName('like-profession'),
        'id' => $profession->id,
    ], JSON_THROW_ON_ERROR) !!}</script>
    <i class="fas fa-heart @if (!$profession->likedByLoggedInUser) hide @endif" data-ref="likeTrue"></i>
    <i class="far fa-heart @if ($profession->likedByLoggedInUser) hide @endif" data-ref="likeFalse"></i>
</span>
