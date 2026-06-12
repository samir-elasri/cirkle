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
			<a href="https://mbiance.com" alt="mbiance inc." title="mbiance">
				<img src="/dist/admin/img/mbiance-logo.png" style="max-width:200px" />
				<img src="/dist/admin/img/m-logo.png" style="max-width:60px" />
			</a>
		</div>

	</div>

    <div class="sidebar-menu">

        <ul class="nav nav-sidebar">

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
