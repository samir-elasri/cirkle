@extends('_layouts.admin')

@section('sec-content')

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><i class="fa fa-flag"></i> Plateformes françaises</h2>
                </div>
                <div class="panel-body">
                    <p style="margin-bottom:15px;">
                        Tant que les professions <strong>anglaises</strong> ne sont pas terminées, les
                        <strong>2 plateformes françaises</strong> (Résidentiel Français, B2B Français)
                        s'affichent en rouge <strong>« À VENIR »</strong> et ne sont pas cliquables sur
                        la page d'accueil. Décochez la case ci-dessous pour les <strong>activer</strong>.
                    </p>

                    <form action="{{ route('admin.plateformes.update') }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom:18px;">
                            <label style="font-weight:600; cursor:pointer;">
                                <input type="checkbox" name="french_platforms_coming_soon" value="1"
                                       {{ ($setting->french_platforms_coming_soon ?? true) ? 'checked' : '' }}>
                                &nbsp;Afficher les plateformes françaises comme « À VENIR » (bloquées)
                            </label>
                            <div style="color:#888; font-size:.9em; margin-top:4px;">
                                Cochée = bloquées (« À VENIR »). &nbsp;|&nbsp; Décochée = activées (cliquables).
                            </div>
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
