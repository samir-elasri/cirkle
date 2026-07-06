{{-- Formulaire de Denis « WEB 09F RECRUTEMENT D'EMPLOYÉS 300126 » : le fournisseur
     publie son offre pour recruter (le doc prévoit aussi le téléversement de son propre
     formulaire — à venir; ici : titre + description de l'offre). --}}
@php $en = app()->getLocale() === 'en'; @endphp
<div class="ck-optform">
    <h4>{{ $en ? 'RECRUITMENT' : 'RECRUTEMENT' }} <img class="ck-e-badge" src="{{ asset_with_version('/dist/img/cirkle-e-badge.png') }}" alt="E" style="height:20px;vertical-align:middle"></h4>
    <p>{{ $en
        ? 'Post your job openings to recruit qualified staff. The E logo appears next to your name on the platform while this option is active, so clients and candidates immediately see that you are hiring.'
        : "Publiez vos offres d'emploi pour recruter du personnel qualifié. Le logo E apparaît à côté de votre nom sur la plateforme tant que cette option est active — clients et candidats voient immédiatement que vous embauchez." }}</p>
    <p><strong>{{ $en
        ? 'For each posting: the position title and a description (tasks, requirements, schedule, how to apply).'
        : "Pour chaque offre : le titre du poste et une description (tâches, exigences, horaire, comment postuler)." }}</strong></p>
</div>
