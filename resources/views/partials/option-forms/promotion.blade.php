{{-- Formulaire de Denis « WEWB 13F PROMOTION (promo) 010626 » — texte intégral (sans
     les lignes de coût/ACCEPTER; les paliers photos A/B ne sont pas encore facturables). --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'SUPPLIER PROMOTION' : 'PROMOTION FOURNISSEUR' }}</h4>
    <p>{{ $en
        ? 'This space is reserved for you to showcase your business, promote your services and increase your business opportunities. It is a strategic tool designed to help you grow your clientele, retain your current clients and increase your visibility on the CIRKLE platform.'
        : "Cet espace vous est réservé afin de mettre en valeur votre entreprise, promouvoir vos services et augmenter vos opportunités d'affaires. Il s'agit d'un outil stratégique conçu pour vous aider à développer votre clientèle, fidéliser vos clients actuels et accroître votre visibilité sur la plateforme CIRKLE." }}</p>
    <p>{{ $en
        ? 'Businesses that regularly offer promotions or advantages to their clientele considerably increase their chances of repeat orders while attracting new clients.'
        : "Les entreprises qui offrent régulièrement des promotions ou des avantages à leur clientèle augmentent considérablement leurs chances d'obtenir des commandes récurrentes tout en attirant de nouveaux clients." }}</p>
    <p><strong>{{ $en ? 'Target clienteles (choose in your text):' : 'Clientèles ciblées (à préciser dans votre texte) :' }}</strong>
        {{ $en
            ? 'general public · students · teachers · seniors · athletes · businesses · associations · women · men · children · teenagers · other.'
            : 'grand public · étudiants · enseignants · aînés · sportifs · entreprises · associations · femmes · hommes · enfants · adolescents · autres.' }}</p>
    <p><strong>{{ $en ? 'Promotion ideas:' : 'Suggestions de promotions :' }}</strong>
        {{ $en
            ? '20% off our services · free estimate · limited-time special offer · purchase discount · combined-services discount · reduced-price services · weekly special · monthly special · coupon for the next service · promotional gift · referral discount · loyalty program · seasonal promotion · your own custom promotion.'
            : '20 % de rabais sur nos services · estimation gratuite · offre spéciale à durée limitée · rabais sur achat · rabais sur services combinés · services à prix réduit · spécial de la semaine · spécial du mois · coupon applicable au prochain service · cadeau promotionnel · rabais de référencement · programme de fidélité · promotion saisonnière · autre promotion personnalisée.' }}</p>
    <p><strong>{{ $en
        ? 'Describe your promotion below (details + start and end dates). Only your final promotion will be shown to clients on your CIRKLE sheet.'
        : 'Décrivez votre promotion ci-dessous (détails + dates de début et de fin). Seul le contenu de votre promotion finale sera affiché aux clients sur votre fiche CIRKLE.' }}</strong></p>
    <p class="ck-optform-avis"><strong>{{ $en ? 'Notice to clients:' : 'Note aux clients — avis important :' }}</strong>
        {{ $en
            ? 'Promotions, discounts, special offers, prices, warranties, photos, documents and information displayed are published under the sole responsibility of the supplier. CIRKLE acts solely as a platform connecting clients and suppliers and does not guarantee the accuracy, availability or execution of the promotions, products or services advertised.'
            : "Les promotions, rabais, offres spéciales, prix, garanties, photos, documents et renseignements affichés sont publiés sous l'entière responsabilité du fournisseur. CIRKLE agit uniquement à titre de plateforme de mise en relation entre les clients et les fournisseurs et ne garantit ni l'exactitude, ni la disponibilité, ni l'exécution des promotions, produits ou services annoncés." }}</p>
</div>
