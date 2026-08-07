@extends('_layouts.admin')

@section('sec-content')

    <div class="row">
        <div class="col-md-12">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Lancer un tirage --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><i class="fa fa-gift"></i> Tirage parmi les membres</h2>
                </div>
                <div class="panel-body">
                    <p style="margin-bottom:15px;">
                        Choisissez le <strong>bassin de participants</strong> et le nombre de gagnants.
                        Le tirage est <strong>aléatoire</strong> parmi les comptes actifs; les gagnants
                        sont conservés dans l'historique ci-dessous.
                    </p>

                    <form action="{{ route('admin.tirage.run') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Nom du tirage</label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="Ex. : Tirage de la rentrée 2026" maxlength="150">
                        </div>

                        <div class="form-group">
                            <label>Participants</label>
                            @foreach($pools as $key => $label)
                                <div style="margin:4px 0;">
                                    <label style="font-weight:600;cursor:pointer;">
                                        <input type="radio" name="pool" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                                        &nbsp;{{ $label }}
                                        <span style="color:#888;font-weight:400;">— {{ $counts[$key] }} membre(s) admissible(s)</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group" style="max-width:220px;">
                            <label>Nombre de gagnants</label>
                            <input type="number" name="winners_count" class="form-control" value="1" min="1" max="50">
                        </div>

                        <div class="form-group">
                            <label>Note (facultatif)</label>
                            <input type="text" name="note" class="form-control" maxlength="255"
                                   placeholder="Ex. : prix offert, conditions…">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-random"></i> Lancer le tirage
                        </button>
                    </form>
                </div>
            </div>

            {{-- Historique --}}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2><i class="fa fa-history"></i> Historique des tirages</h2>
                </div>
                <div class="panel-body">
                    @if($draws->isEmpty())
                        <p style="color:#888;">Aucun tirage effectué pour le moment.</p>
                    @else
                        @foreach($draws as $draw)
                            <div style="border:1px solid #e2e2e2;border-radius:6px;padding:12px 15px;margin-bottom:12px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                                    <div>
                                        <strong>{{ $draw->title }}</strong>
                                        &nbsp;<span class="label label-info">{{ $draw->pool_label }}</span>
                                        <div style="color:#888;font-size:.9em;">
                                            {{ optional($draw->drawn_at)->format('Y-m-d H:i') }}
                                            — {{ $draw->eligible_count }} participant(s) admissible(s)
                                            @if($draw->note) — {{ $draw->note }} @endif
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.tirage.destroy', ['id' => $draw->id]) }}" method="POST"
                                          onsubmit="return confirm('Supprimer ce tirage ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Supprimer</button>
                                    </form>
                                </div>

                                <table class="table table-striped table-bordered" style="margin:10px 0 0;">
                                    <thead>
                                        <tr><th style="width:60px;">#</th><th>No de membre</th><th>Nom</th><th>Courriel</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($draw->winners as $w)
                                            <tr>
                                                <td>{{ $w->position }}</td>
                                                <td><strong>{{ $w->member_number }}</strong></td>
                                                <td>{{ $w->name }}</td>
                                                <td>{{ $w->email }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

@endsection
