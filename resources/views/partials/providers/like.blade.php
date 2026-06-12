<span data-component="like">
    <script type="application/json">{!! json_encode([
                'url' => urlRouteName('like-provider'),
                'id' => $provider->id,
            ], JSON_THROW_ON_ERROR) !!}</script>
    <i class="fas fa-heart @if (!$provider->likedByLoggedInUser) hide @endif" data-ref="likeTrue"></i>
    <i class="far fa-heart @if ($provider->likedByLoggedInUser) hide @endif" data-ref="likeFalse"></i>
</span>
