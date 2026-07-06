{{-- Formulaire de Denis « WEB 08F PHOTOS DU FOURN 010626 » — texte intégral (sans les
     lignes de coût/ACCEPTER : le paiement passe par le panier). --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'SUPPLIER PHOTOS — 1 PHOTO = 1,000 WORDS' : 'PHOTOS DU FOURNISSEUR — 1 PHOTO = 1 000 MOTS' }}</h4>
    <p><strong>{{ $en ? 'Showcase your business.' : 'Mettez votre entreprise en valeur.' }}</strong>
        {{ $en
            ? 'CIRKLE lets you upload your own photos to present your business in a professional, transparent and credible way. Quality photos help clients get to know your business, see your expertise and trust your services. A well-illustrated profile increases your visibility and your chances of being selected.'
            : "CIRKLE vous offre la possibilité de téléverser vos propres photos afin de présenter votre entreprise de façon professionnelle, transparente et crédible. Des photos de qualité permettent aux clients de mieux connaître votre entreprise, de constater votre expertise et d'avoir davantage confiance en vos services. Un profil bien illustré augmente votre visibilité et améliore vos chances d'être sélectionné." }}</p>
    <p><strong>{{ $en ? 'Examples of photos to upload:' : 'Exemples de photos à téléverser :' }}</strong></p>
    <ul>
        <li>{{ $en ? 'The front of your business' : 'La façade ou la devanture de votre entreprise' }}</li>
        <li>{{ $en ? 'Your professional photo' : 'Votre photo professionnelle' }}</li>
        <li>{{ $en ? 'Your company vehicles' : "Vos véhicules d'entreprise" }}</li>
        <li>{{ $en ? 'Your machinery and specialized equipment' : 'Votre machinerie et vos équipements spécialisés' }}</li>
        <li>{{ $en ? 'Your work tools' : 'Vos outils de travail' }}</li>
        <li>{{ $en ? 'Your team' : 'Votre équipe' }}</li>
        <li>{{ $en ? 'Your recent work' : 'Vos réalisations récentes' }}</li>
        <li>{{ $en ? 'Before/after photos of your work' : 'Des photos avant et après vos travaux' }}</li>
        <li>{{ $en ? 'Your certificates, permits or accreditations' : 'Vos certificats, permis ou accréditations' }}</li>
        <li>{{ $en ? 'Examples of estimates, quotes or contracts' : "Des exemples d'estimés, soumissions ou contrats" }}</li>
        <li>{{ $en ? 'Any other relevant photo showing your professionalism' : 'Toute autre photo pertinente démontrant votre professionnalisme' }}</li>
    </ul>
    <p><strong>{{ $en ? 'The advantages:' : 'Les avantages des photos :' }}</strong>
        {{ $en
            ? '✔ Build client trust ✔ Show your experience and know-how ✔ Concretely illustrate the quality of your services ✔ Increase your visibility ✔ Help clients choose you ✔ Set you apart from the competition'
            : '✔ Renforcent la confiance des clients ✔ Démontrent votre expérience et votre savoir-faire ✔ Illustrent concrètement la qualité de vos services ✔ Augmentent votre visibilité ✔ Favorisent votre sélection par les clients ✔ Vous distinguent de la concurrence' }}</p>
    <p class="ck-optform-avis"><strong>{{ $en ? 'Important notice:' : 'Avis important :' }}</strong>
        {{ $en
            ? 'All published content must be accurate, respectful and representative of your business. Uploading false, misleading, offensive, illegal or inappropriate content is prohibited. Each supplier remains fully responsible for the photos, documents and information they publish. CIRKLE cannot be held responsible for content displayed by suppliers or the representations they make of their services.'
            : "Tous les contenus publiés doivent être exacts, respectueux et représentatifs de votre entreprise. Il est interdit de téléverser du contenu faux, trompeur, offensant, illégal ou inapproprié. Chaque fournisseur demeure entièrement responsable des photos, documents et informations qu'il publie. CIRKLE ne peut être tenu responsable du contenu affiché par les fournisseurs ni des représentations qu'ils font de leurs services." }}</p>
</div>
