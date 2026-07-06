{{-- Formulaire de Denis « WEB 12F PERMIS, ASSOCIATION, LICENSE, UNION, SYNDICAT
     290126 » — texte intégral. Son tableau : type / émetteur / no de l'inscrit / date. --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'PERMITS – LICENCES – ASSOCIATIONS – UNIONS – ACCREDITATIONS AND PROFESSIONAL RECOGNITIONS' : 'PERMIS – LICENCES – ASSOCIATIONS – UNIONS – SYNDICATS – ACCRÉDITATIONS ET RECONNAISSANCES PROFESSIONNELLES' }}</h4>
    <p>{{ $en
        ? 'Permits, licences, affiliations and professional recognitions are important indicators of compliance, competence and credibility. Any relevant information regarding the legal, regulatory or professional obligations of your business helps reassure clients about your seriousness, your professionalism and your commitment to the standards of your field.'
        : "Les permis, licences, affiliations et reconnaissances professionnelles constituent des indicateurs importants de conformité, de compétence et de crédibilité. Toute information pertinente relative aux obligations légales, réglementaires ou professionnelles de votre entreprise contribue à rassurer les clients quant à votre sérieux, votre professionnalisme et votre engagement à respecter les normes applicables à votre domaine d'activité." }}</p>
    <p><strong>{{ $en ? 'Please declare, where applicable:' : 'Veuillez déclarer, le cas échéant :' }}</strong></p>
    <ul>
        <li>{{ $en ? 'Operating permits' : "Permis d'exploitation" }}</li>
        <li>{{ $en ? 'Professional licences' : 'Licences professionnelles' }}</li>
        <li>{{ $en ? 'Accreditations and certifications' : 'Accréditations et certifications' }}</li>
        <li>{{ $en ? 'Professional associations' : 'Associations professionnelles' }}</li>
        <li>{{ $en ? 'Professional orders' : 'Ordres professionnels' }}</li>
        <li>{{ $en ? 'Unions' : 'Unions ou syndicats' }}</li>
        <li>{{ $en ? 'Sector recognitions' : 'Reconnaissances sectorielles' }}</li>
        <li>{{ $en ? 'Specialized qualifications' : 'Qualifications spécialisées' }}</li>
        <li>{{ $en ? 'Government authorizations' : 'Autorisations gouvernementales' }}</li>
        <li>{{ $en ? 'Master titles or specializations' : 'Titres de maîtrise ou spécialisations' }}</li>
        <li>{{ $en ? 'Medical or technical accreditations' : 'Accréditations du domaine médical ou technique' }}</li>
        <li>{{ $en ? 'Any other relevant recognition related to your activities' : 'Toute autre reconnaissance pertinente liée à vos activités' }}</li>
    </ul>
    <p>{{ $en
        ? 'The more complete your profile, the more trust you inspire — and the better your chances of being selected by CIRKLE clients.'
        : "Plus votre profil est complet, plus vous inspirez confiance et augmentez vos chances d'être sélectionné par les clients de la plateforme CIRKLE." }}</p>
    <p><strong>{{ $en
        ? 'For each entry: type (permit, association, licence, union, other) — official name of the issuer — your registration number — start date (year/month).'
        : "Pour chaque entrée : type (permis, association, licence, union, syndicat, autre) — nom officiel de l'émetteur — no de l'inscrit — date de début (an/mois)." }}</strong></p>
    <p class="ck-optform-avis"><strong>{{ $en ? 'Important notice:' : 'Avis important :' }}</strong>
        {{ $en
            ? 'Each supplier remains fully responsible for the accuracy of the information, permits, licences, certifications or affiliations they declare. CIRKLE does not systematically verify the information submitted and cannot be held responsible for suppliers\' declarations.'
            : "Chaque fournisseur demeure entièrement responsable de l'exactitude des renseignements, permis, licences, certifications ou affiliations qu'il déclare. CIRKLE ne vérifie pas systématiquement les informations soumises et ne peut être tenu responsable des déclarations effectuées par les fournisseurs." }}</p>
</div>
