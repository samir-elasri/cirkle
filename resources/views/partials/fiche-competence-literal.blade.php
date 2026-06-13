{{--
    Rendu LITTÉRAL des lignes de compétence MASTER 2350 (spec : seules les colonnes B+C
    s'affichent; lignes non cochées disparaissent; espaces, couleurs et sauts de bloc
    du fichier Excel sont préservés).

    $rows      : SubscriberService cochés (relation service chargée), ordonnés par source_row
    $gapRows   : source_rows (toutes lignes de la fiche, cochées ou non) ayant un saut de bloc avant —
                 un saut reste visible même si la ligne qui le portait n'est pas cochée
--}}
@php($previousSourceRow = null)

<div class="fiche-literal">
    @foreach ($rows as $row)
        @php($service = $row->service)
        @php($sourceRow = $service->source_row)
        @php(
            $hasGap = $previousSourceRow !== null
                && $sourceRow !== null
                && collect($gapRows ?? [])->contains(
                    fn ($g) => $g > $previousSourceRow && $g <= $sourceRow
                )
        )

        <div class="fiche-literal__row {{ $hasGap ? 'fiche-literal__row--gap' : '' }}">{!! $service->formatted_title ?: e($service->title) !!}@if ($row->custom_value)<span class="fiche-literal__custom"> {{ $row->custom_value }}</span>@endif</div>

        @php($previousSourceRow = $sourceRow ?? $previousSourceRow)
    @endforeach
</div>
