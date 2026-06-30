@extends('_layouts.admin')

@section('sec-content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><i class="fa fa-flag"></i> Plateformes — « À VENIR »</h2>
                </div>
                <div class="panel-body">
                    <p style="margin-bottom:15px;">
                        Cochez les plateformes à afficher en rouge <strong>« À VENIR »</strong> sur la page
                        d'accueil (non cliquables). Décochez une plateforme pour l'<strong>activer</strong>.
                    </p>

                    <form action="{{ route('admin.plateformes.update') }}" method="POST">
                        @csrf
                        @foreach($platforms as $key => $label)
                            <div class="form-group" style="margin-bottom:10px;">
                                <label style="font-weight:600; cursor:pointer;">
                                    <input type="checkbox" name="coming_soon[]" value="{{ $key }}"
                                           {{ in_array($key, $comingSoon) ? 'checked' : '' }}>
                                    &nbsp;{{ $label }}
                                    <span style="color:#888; font-weight:400;">— afficher « À VENIR »</span>
                                </label>
                            </div>
                        @endforeach

                        <div style="color:#888; font-size:.9em; margin:8px 0 16px;">
                            Cochée = bloquée (« À VENIR »). &nbsp;|&nbsp; Décochée = activée (cliquable).
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
