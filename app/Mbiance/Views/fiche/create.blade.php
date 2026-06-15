@extends('_layouts.admin')

@section('sec-content')

	<div class="admin-fiche">

		{{-- ── Import automatique MASTER 2350 (le « moteur ») ──────────────────────
		     Téléverse un classeur Excel; ExcelImport crée/maj la fiche, ses services,
		     capacités, mots-clés, frais et forfaits (code postal + provinces). --}}
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h2><i class="fa fa-upload"></i> Importer une fiche MASTER 2350 (Excel)</h2>
					</div>
					<div class="panel-body">
						<p style="margin-bottom: 15px;">
							Téléversez le fichier Excel d'une fiche (format «&nbsp;MASTER 2350 COLONNE ABCD&nbsp;»).
							Le moteur crée <strong>automatiquement</strong> la fiche, ses services et
							capacités, les mots-clés, les frais de fiche et les <strong>forfaits</strong>
							(code postal + provinces). Réimporter le même fichier met la fiche à jour.
						</p>
						<form action="{{ route('admin.excel.import') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<div class="form-group row">
								<div class="col-md-6">
									<input type="file" name="file" id="file" class="form-control"
										   accept=".xlsx, .xls, .csv" required>
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-cogs"></i> Importer
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		{{-- ── Création manuelle (optionnelle) ─────────────────────────────────────
		     Alternative au fichier Excel : saisir une fiche à la main. --}}
		{{ Form::open(['url' => urlRouteName('admin.fiche.store')]) }}

		<div class="panel panel-default">
			<div class="panel-heading">
				<h2><i class="fa fa-pencil"></i> {{ __('fiche.title') }} — création manuelle (optionnel)</h2>
			</div>
			<div class="panel-body">
				<div class="form-group row">
					<label for="category_label" class="col-md-2 control-label">{{ __('fiche.label.label') }}</label>
					<div class="col-md-4">
						<input class="form-control" id="category_label" name="category_label" type="text">
					</div>
				</div>

				<div class="form-group row">
					<label for="category_fr" class="col-md-2 control-label fr">{{ __('fiche.label.category') }}</label>
					<div class="col-md-4">
						<input class="form-control" id="category_fr" name="category[fr][title]" type="text">
					</div>
					<label for="category_en" class="col-md-2 control-label en">{{ __('fiche.label.category') }}</label>
					<div class="col-md-4">
						<input class="form-control" id="category_en" name="category[en][title]" type="text">
					</div>
				</div>

				<div class="form-group row">
					<label for="profession_fr" class="col-md-2 control-label fr">{{ __('fiche.label.profession') }}</label>
					<div class="col-md-4">
						<input class="form-control" id="profession_title_fr" name="profession[fr][title]" type="text">
					</div>
					<label for="profession_en" class="col-md-2 control-label en">{{ __('fiche.label.profession') }}</label>
					<div class="col-md-4">
						<input class="form-control" id="profession_en" name="profession[en][title]" type="text">
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-body">
				<div id="services"></div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-body">
				<div class="form-group row">
					<label for="provider_description_fr" class="col-md-2 control-label fr">{{ __('fiche.label.provider_description') }}</label>
					<div class="col-md-4">
						<textarea rows="4" class="form-control" id="provider_description_fr" name="profession[fr][provider_description]"></textarea>
					</div>
					<label for="provider_description_en" class="col-md-2 control-label en">{{ __('fiche.label.provider_description') }}</label>
					<div class="col-md-4">
						<textarea rows="4" class="form-control" id="provider_description_en" name="profession[en][provider_description]"></textarea>
					</div>
				</div>

				<div class="form-group row">
					<label for="client_description_fr" class="col-md-2 control-label fr">{{ __('fiche.label.client_description') }}</label>
					<div class="col-md-4">
						<textarea rows="4" class="form-control" id="client_description_fr" name="profession[fr][client_description]"></textarea>
					</div>
					<label for="client_description_en" class="col-md-2 control-label en">{{ __('fiche.label.client_description') }}</label>
					<div class="col-md-4">
						<textarea rows="4" class="form-control" id="client_description_en" name="profession[en][client_description]"></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-primary" type="submit">{{ __('fiche.submit') }}</button>
			</div>
		</div>

		{{ Form::close() }}

		<template id="serviceInputTemplate">
			<div class="form-group row">
				<label class="col-md-2 control-label fr">{{ __('fiche.label.service') }}</label>
				<div class="col-md-4">
					<input class="form-control fr" type="text">
				</div>
				<label class="col-md-2 control-label en">{{ __('fiche.label.service') }}</label>
				<div class="col-md-4">
					<input class="form-control en" type="text">
				</div>
			</div>
		</template>

		{!! Html::script('dist/admin/app/fiche.js') !!}
	</div>
@stop
