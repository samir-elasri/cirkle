@extends('_layouts.simple')

@section('content')

	<div class="row">

		<div class="login-box">

			<h1>Accès refusé</h1>

			<p>Votre session a expiré ou vous n'êtes pas autorisé à accéder à cette ressource</p>

			@if(!Auth::guard('users')->check())
				<p>
					pour vous connecter à nouveau
					<a href="/admin/login" title="aller au formulaire d'identification">
						cliquez ici
					</a>
				</p>
			@endif
		</div>

	</div>

@stop