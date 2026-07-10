{{-- Caricatures « sur les côtés » (Denis 11.07) : les MÊMES caricatures que le
     carrousel du haut (les slides de l'admin), affichées en colonnes fixes à
     gauche et à droite, dans la marge des grands écrans seulement. Décoratives :
     n'interceptent pas les clics, cachées sous 1400px (pas de place à côté du
     contenu centré). Rien n'apparaît tant qu'aucune caricature n'est ajoutée. --}}
@php
    $caricatures = $caricatures ?? collect();
    // Réparties gauche/droite en alternance.
    $left  = $caricatures->values()->filter(fn ($c, $i) => $i % 2 === 0)->take(3);
    $right = $caricatures->values()->filter(fn ($c, $i) => $i % 2 === 1)->take(3);
@endphp
@if ($caricatures->isNotEmpty())
    <style>
        .ck-side-caricatures { display: none; }
        @media (min-width: 1400px) {
            .ck-side-caricatures {
                display: flex; flex-direction: column; gap: 16px;
                position: fixed; top: 140px; z-index: 5;
                width: calc((100vw - 1200px) / 2 - 32px); max-width: 220px;
                pointer-events: none;
            }
            .ck-side-caricatures--left  { left: 12px; }
            .ck-side-caricatures--right { right: 12px; }
            .ck-side-caricatures img {
                width: 100%; height: auto; border-radius: 14px;
                box-shadow: 0 4px 18px rgba(0,0,0,.12);
            }
        }
    </style>
    <div class="ck-side-caricatures ck-side-caricatures--left" aria-hidden="true">
        @foreach ($left as $img)<img src="{{ $img }}" alt="">@endforeach
    </div>
    <div class="ck-side-caricatures ck-side-caricatures--right" aria-hidden="true">
        @foreach ($right as $img)<img src="{{ $img }}" alt="">@endforeach
    </div>
@endif
