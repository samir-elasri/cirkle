{{-- Formulaire de Denis « WEB 10F ESTIMATION DU FOURNISSEUR 290126 » — texte intégral.
     Les rubriques « spécifiez » plus fines (méthode de production, rendez-vous, frais de
     cancellation…) sont présentées ici; les champs persistés restent coût + paiements. --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'ESTIMATE OF THE WORK BY THE SUPPLIER' : 'ESTIMATION DES TRAVAUX PAR LE FOURNISSEUR' }}</h4>
    <p><strong>{{ $en ? 'The estimate can be produced:' : "L'estimation sera produite :" }}</strong>
        {{ $en
            ? 'at the client\'s home · via the "GPS map" · via the client\'s photos · via the client\'s video.'
            : 'chez le client · via la « carte GPS » · via photos du client · via vidéo du client.' }}</p>
    <p><strong>{{ $en ? 'Cost of the estimate:' : "Coût de l'estimation :" }}</strong>
        {{ $en
            ? 'free — or payable on site — or payable on site and credited on the work invoice — or another method specified by the supplier.'
            : 'gratuit — ou payable sur place — ou payable sur place et crédité sur la facture des travaux — ou autre méthode précisée par le fournisseur.' }}</p>
    <p><strong>{{ $en ? 'Accepted payments:' : 'Nous acceptons le paiement :' }}</strong>
        {{ $en
            ? 'cash · cheque · Interac · debit cards · credit cards (specify the accepted card names).'
            : 'en argent · par chèque · via Interac · cartes de débit · cartes de crédit (précisez les noms des cartes acceptées).' }}</p>
    <p><strong>{{ $en ? 'For appointments and discussions:' : 'Pour rendez-vous et discussions :' }}</strong>
        {{ $en
            ? 'call or e-mail the supplier. Also specify, if applicable: contract cancellation fees and any other conditions.'
            : "appelez ou courriellez le fournisseur. Précisez aussi, s'il y a lieu : les frais de cancellation d'un contrat et toute autre condition." }}</p>
    <p style="color:#777;font-size:.85rem">{{ $en
        ? 'Fill in the estimate cost and accepted payment methods below — the other details can be specified in your 2350 sheet (PRÉCISEZ fields).'
        : "Remplissez ci-dessous le coût de l'estimation et les modes de paiement acceptés — les autres détails se précisent dans votre fiche 2350 (champs PRÉCISEZ)." }}</p>
</div>
