{{-- PAGE RÉSILIATION — procédure d'annulation d'abonnement (Denis B7, 30.06).
     Politique « continuité » : le numéro de membre est CONSERVÉ; au réabonnement on ne
     repart pas à zéro. Rendu dans la mise en page du site. --}}
@php $loc = app()->getLocale(); $t = fn ($fr, $en) => $loc === 'en' ? $en : $fr; @endphp

<section class="ck-resil">
    <div class="optimal-content-width">
        <style>
            .ck-resil { padding: 8px 0 40px; }
            .ck-resil__title { text-align:center; font-size:1.8rem; font-weight:900; color:#00993a; margin:.3em 0 1em; }
            .ck-resil h2 { color:#0070C0; font-weight:800; font-size:1.1rem; margin:1.3em 0 .4em; }
            .ck-resil p { line-height:1.65; margin:.5em 0; color:#1f2733; }
            .ck-resil ol, .ck-resil ul { margin:.4em 0 .8em 1.2em; line-height:1.7; }
            .ck-resil .keep { background:#eafaf0; border:1px solid #b8e6c8; border-radius:10px; padding:12px 16px; margin:1em 0; }
            .ck-resil .keep strong { color:#0f7a3f; }
            .ck-resil a.mail { color:#0070C0; font-weight:700; }
        </style>

        <h1 class="ck-resil__title">{{ $t('Résiliation de votre abonnement', 'Cancelling your membership') }}</h1>

        <p>{{ $t("Vous pouvez résilier votre abonnement à tout moment, que vous soyez membre client ou membre fournisseur.", 'You may cancel your membership at any time, whether you are a client member or a supplier member.') }}</p>

        <h2>{{ $t('Comment résilier', 'How to cancel') }}</h2>
        <ol>
            <li>{{ $t('Connectez-vous à votre compte.', 'Log in to your account.') }}</li>
            <li>{!! $t('Écrivez-nous à <a class="mail" href="mailto:servclient@cirkleservices.com?subject=Résiliation">servclient@cirkleservices.com</a> depuis l\'adresse courriel de votre compte, en indiquant « Résiliation » et votre numéro de membre.', 'Email us at <a class="mail" href="mailto:servclient@cirkleservices.com?subject=Cancellation">servclient@cirkleservices.com</a> from your account email, with “Cancellation” and your member number.') !!}</li>
            <li>{{ $t('Notre équipe confirme votre résiliation par courriel.', 'Our team confirms your cancellation by email.') }}</li>
        </ol>

        <h2>{{ $t('Ce qui se passe ensuite', 'What happens next') }}</h2>
        <ul>
            <li>{{ $t('Votre fiche / profil cesse d\'être visible aux clients.', 'Your profile is no longer visible to clients.') }}</li>
            <li>{{ $t('La résiliation prend effet à la fin de votre période déjà payée (aucun remboursement au prorata).', 'Cancellation takes effect at the end of your already-paid period (no prorated refund).') }}</li>
        </ul>

        <div class="keep">
            <p style="margin:0">{!! $t('<strong>Bonne nouvelle :</strong> votre <strong>numéro de membre est conservé</strong> — il ne disparaît pas.', '<strong>Good news:</strong> your <strong>member number is kept</strong> — it does not disappear.') !!}</p>
        </div>

        <h2>{{ $t('Si vous revenez plus tard', 'If you come back later') }}</h2>
        <ul>
            <li>{!! $t('En vous réabonnant, vous <strong>retrouvez votre même numéro de membre</strong> et vos informations de base — vous ne repartez pas à zéro.', 'When you re-subscribe, you <strong>get your same member number</strong> and your basic information back — you do not start from zero.') !!}</li>
            <li>{{ $t('Vos fiches de compétences et options (photos, promotions, etc.) peuvent être réactivées.', 'Your competence sheets and options (photos, promotions, etc.) can be reactivated.') }}</li>
        </ul>

        <p style="margin-top:1.4em">{{ $t('Une question avant de résilier ?', 'A question before cancelling?') }} <a class="mail" href="mailto:servclient@cirkleservices.com">servclient@cirkleservices.com</a></p>
    </div>
</section>

{!! $blocs ?? '' !!}
