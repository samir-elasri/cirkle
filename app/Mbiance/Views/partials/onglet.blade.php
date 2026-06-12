@if (isset($onglets))

	<ul class="nav tab-menu nav-tabs">

		@foreach($onglets as $onglet)

			<li @if($onglet['active']) class="active" @endif>

				@if (!$onglet['create'] && PageUtility::isCreate())
					<a href="#" style="cursor: no-drop;">{{$onglet->nom}}</a>

				@elseif($relation = Arr::get($onglet, 'relation'))
					<a href="{{ adminRouteName(Route::currentRouteName(), [Request::segment(3), $relation]) }}">{{$onglet['nom']}}</a>

				@elseif(Arr::get($onglet, 'identifiant') === 'general')
					<a href="{{ adminRouteName(Route::currentRouteName(), [Request::segment(3)]) }}">{{$onglet['nom']}}</a>
				@endif

			</li>

		@endforeach

	</ul>

@endif
