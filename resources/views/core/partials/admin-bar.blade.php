@if(is_admin())
	<div data-component="adminBar" class="admin-bar full-content-width">
		<div class="optimal-content-width">
			<div>
				<div class="admin-bar__title">@lang('admin.admin-bar.title')</div>
				<div class="admin-bar__user">{{ Auth::guard('users')->user()->name }}</div>
				<a class="admin-bar__button admin-bar__button--logout" href="/admin/logout"
				   target="_blank">@lang('admin.admin-bar.logout')</a>
			</div>
			@if(is_admin(true))
				<div class="admin-bar__label">
					<span data-ref="resolution" class="admin-bar__resolution"></span>
					&nbsp;
					<span class="on-bp-tn">(TN)</span>
					<span class="on-bp-sm">(SM)</span>
					<span class="on-bp-md">(MD)</span>
					<span class="on-bp-bg">(BG)</span>
				</div>
			@endif
			<div class="spacer"></div>
			<div>
				@if(is_admin(true))
					<div class="ui mini selection dropdown item" data-component="dropdown">
						<i class="dropdown icon"></i>
						<div class="default text">Impersonate</div>
						<div class="menu">
							@foreach($sids as $sid => $sname)
								<a class="item" href="/admin/impersonate/subscribers/{{$sid}}">{{$sname}} - {{$sid}}</a>
							@endforeach
						</div>
					</div>
					<div class="admin-bar__button admin-bar__button--guides">@lang('admin.admin-bar.guides')</div>
				@endif
				<a class="admin-bar__button admin-bar__button--inactive-blocs"
				   href="{{ urlRouteName('showInactive') }}">@lang('admin.admin-bar.inactive-blocs')</a>
				<a class="admin-bar__button admin-bar__button--clear-cache"
				   href="/admin/clearCache">@lang('admin.admin-bar.clear-cache')</a>
				@if(!empty($edit_url))
					<a class="admin-bar__button admin-bar__button--edit-page" href="{{$edit_url}}"
					   target="_blank">@lang('admin.admin-bar.edit-page')</a>
				@endif
				<a class="admin-bar__button admin-bar__button--settings" href="/admin/settings/1/edit"
				   target="_blank">@lang('admin.admin-bar.settings')</a>
			</div>
		</div>
	</div>
@endif
