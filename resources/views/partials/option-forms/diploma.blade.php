{{-- Formulaire de Denis « WEB 11F DIPLOME ACADEMIQUE 290126 » — texte intégral. Le
     tableau (cours / école / date) correspond exactement aux champs ci-dessous. --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'ACADEMIC DIPLOMAS, PROFESSIONAL SCHOOLS, TRADE SCHOOLS AND OTHER TRAINING' : 'DIPLÔMES ACADÉMIQUES, ÉCOLES PROFESSIONNELLES, ÉCOLES DE MÉTIER ET AUTRES FORMATIONS' }}</h4>
    <p>{{ $en
        ? 'The diplomas, certificates and training of your staff are an important sign of competence, professionalism and credibility. Any relevant academic information about your business, its managers or employees helps reassure clients, demonstrates your expertise and highlights your commitment to quality, compliance and the best practices of your profession.'
        : "Les diplômes, certificats et formations de votre personnel constituent un gage important de compétence, de professionnalisme et de crédibilité. Toute information académique pertinente concernant votre entreprise, ses dirigeants ou ses employés contribue à rassurer les clients, à démontrer votre expertise et à mettre en valeur votre engagement envers la qualité, la conformité et les bonnes pratiques de votre profession." }}</p>
    <p>{{ $en
        ? 'Please list all diplomas, certificates, attestations, specialized training or accreditations relevant to your business activities. This information helps clients better assess your qualifications and make an informed choice when selecting a supplier on the CIRKLE platform.'
        : "Veuillez indiquer tous les diplômes, certificats, attestations, formations spécialisées ou accréditations pertinentes liés aux activités de votre entreprise. Ces informations permettront aux clients de mieux évaluer vos qualifications et de faire un choix éclairé lors de la sélection d'un fournisseur sur la plateforme CIRKLE." }}</p>
    <p><strong>{{ $en
        ? 'For each entry: name of the course or academic training — official name of the school, university, trade or professional school — graduation date (year/month).'
        : "Pour chaque entrée : nom du cours ou de la formation académique — nom officiel de l'école, université, école de métiers ou professionnelle — date du diplôme (an/mois)." }}</strong></p>
</div>
