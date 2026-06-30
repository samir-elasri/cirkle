<div class="sidebar ">

	<div class="sidebar-header">

		<h2>{{ $user->name }}</h2>
		<h3>{{ $user->email }}</h3>

		<div class="user-menu">
			<a id="sidebar-minify" href="#"><i class="fa fa-caret-square-o-left"></i></a>
		</div>

	</div>

	<div class="sidebar-footer">

		<ul class="sidebar-footer-menu">
			<li><a href="/admin/settings/1/edit"><i class="fa fa-cogs"></i> <span>Paramètres</span></a></li>
			<li><a href="/admin/logout/"><i class="fa fa-sign-out"></i> <span>Déconnexion</span></a></li>
		</ul>

		<div class="sidebar-brand">
			<a href="/" title="Cirkle">
				<img src="{{ setting('main_logo_image') }}" alt="Cirkle" style="max-width:180px" />
				<img src="/favicon-cirkle.png" alt="Cirkle" style="max-width:42px" />
			</a>
		</div>

	</div>

    <div class="sidebar-menu">

        <ul class="nav nav-sidebar">

                {{-- Lien fixe : import des fiches MASTER 2350 (hors arbre de menu en BD) --}}
                <li class="{{ Request::segment(2) === 'fiche' ? 'active' : '' }}">
                    <a href="/admin/fiche">
                        <i class="fa fa-upload"></i><span class="text">Fiches 2350</span>
                    </a>
                </li>

                {{-- Lien fixe : activer/bloquer les plateformes françaises (« À VENIR ») --}}
                <li class="{{ Request::segment(2) === 'plateformes' ? 'active' : '' }}">
                    <a href="/admin/plateformes">
                        <i class="fa fa-flag"></i><span class="text">Plateformes FR</span>
                    </a>
                </li>

                @foreach($menus as $menu)

					@if($menu->Autorisation === "hide")
						@continue
					@endif

                    <li class="
						{{ $menu->SideMenuItems->count() ? 'group' : '' }}
						{{ PageUtility::getUrlDestination($menu) ? ' active' : '' }}
						{{ $selected = PageUtility::checkIfChildrenSelected($menu) ? ' opened' : ''}}
					">


						@if($menu->SideMenuItems->count())
							<a class="dropmenu" href="#">
						@else
							<a href="{{$menu->UrlDestination}}">
						@endif

						<i class="fa {{ $menu->Icone }}"></i><span class="text">{{ $menu->Nom }} {{ $menu->sel }}</span>

						@if($menu->SideMenuItems->count() > 0)
							<span class="fa fa-plus"></span>
							<span class="fa fa-minus"></span>
							</a>

							<ul @if(PageUtility::checkIfChildrenSelected($menu)) style="display: block;" @endif >

							@foreach($menu->SideMenuItems->SideMenuItem as $item)

								<li @if(PageUtility::getUrlDestination($item)) class="active" @endif >
									<a class="submenu" href="{{ $item->UrlDestination }}">
										<i class="fa {{ $item->Icone }}"></i>
										<span class="text">{{ $item->Nom }}</span></a>
								</li>

							@endforeach

							</ul>

						@else
							</a>
						@endif

                    </li>
                @endforeach

        </ul>
    </div>

</div>
