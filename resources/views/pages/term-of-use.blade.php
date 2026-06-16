@php($locale = app()->getLocale())

<section>
    <div class="optimal-content-width content-writable">
        <a href="{{ urlRouteName('home') }}" class="ck-back-btn" onclick="if(history.length>1){history.back();return false;}"
           style="display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;padding:8px 16px;border:1px solid #d9d9d9;border-radius:8px;text-decoration:none;color:#1f2430;font-weight:600;background:#fff;">← Retour</a>

        {{-- Si le client a saisi le texte réel via l'admin (blocs CMS), on l'affiche.
             Sinon, on présente une ÉBAUCHE structurée conforme à la Loi 25 (feature #13). --}}
        @if (trim(strip_tags($blocs ?? '')) !== '')
            {!! $blocs !!}
        @else
            <div style="border-left:3px solid #a85c14;background:#fbf0e2;padding:.8em 1.1em;border-radius:0 8px 8px 0;margin-bottom:1.5em">
                @if ($locale === 'fr')
                    <strong>Ébauche</strong> — texte de référence en attente de la version juridique finale fournie par CIRKLE.
                @else
                    <strong>Draft</strong> — placeholder text pending the final legal version provided by CIRKLE.
                @endif
            </div>

            @if ($locale === 'fr')
                <h1>Conditions d'utilisation</h1>
                <p>Les présentes conditions encadrent l'utilisation de la plateforme CIRKLE (cirkleservices.com). En créant un compte ou en utilisant le site, vous acceptez ces conditions.</p>

                <h2>1. Objet de la plateforme</h2>
                <p>CIRKLE est un annuaire qui met en relation des clients et des fournisseurs de services. CIRKLE ne participe pas aux ententes conclues entre les parties et décline toute responsabilité au-delà de la mise en relation.</p>

                <h2>2. Comptes et adhésion</h2>
                <p>Le membre est responsable de l'exactitude des renseignements fournis et de la confidentialité de son mot de passe.</p>

                <h2>3. Frais et paiements</h2>
                <p>Les fournisseurs paient des frais de fiche, d'abonnement et d'options. Les modalités de facturation, de renouvellement et d'annulation sont décrites au moment de l'achat.</p>

                <h2>4. Évaluations et contenu</h2>
                <p>Seuls les clients connectés peuvent laisser un avis. Les réponses des fournisseurs sont publiées après approbation. Tout contenu injurieux peut être retiré.</p>

                <h2>5. Limitation de responsabilité</h2>
                <p>CIRKLE n'est pas garant des travaux, paiements, garanties ou engagements réalisés à l'extérieur de la plateforme.</p>

                <h2>6. Protection des renseignements personnels (Loi 25)</h2>
                <p>Le traitement de vos renseignements personnels est décrit dans notre <a href="{{ urlRouteName('privacy-policy') }}">Politique de confidentialité</a>.</p>

                <h2>7. Modifications</h2>
                <p>CIRKLE peut modifier ces conditions; la version en vigueur est celle publiée sur cette page.</p>
            @else
                <h1>Terms of Use</h1>
                <p>These terms govern the use of the CIRKLE platform (cirkleservices.com). By creating an account or using the site, you accept these terms.</p>

                <h2>1. Purpose of the platform</h2>
                <p>CIRKLE is a directory that connects clients with service suppliers. CIRKLE is not a party to any agreement between them and disclaims responsibility beyond the introduction.</p>

                <h2>2. Accounts and membership</h2>
                <p>Members are responsible for the accuracy of the information provided and for keeping their password confidential.</p>

                <h2>3. Fees and payments</h2>
                <p>Suppliers pay fiche fees, subscriptions and options. Billing, renewal and cancellation terms are shown at the time of purchase.</p>

                <h2>4. Reviews and content</h2>
                <p>Only logged-in clients may leave a review. Supplier replies are published after approval. Abusive content may be removed.</p>

                <h2>5. Limitation of liability</h2>
                <p>CIRKLE does not guarantee work, payments, warranties or commitments made off the platform.</p>

                <h2>6. Personal information (Law 25)</h2>
                <p>How we handle your personal information is described in our <a href="{{ urlRouteName('privacy-policy') }}">Privacy Policy</a>.</p>

                <h2>7. Changes</h2>
                <p>CIRKLE may modify these terms; the version in force is the one published on this page.</p>
            @endif
        @endif
    </div>
</section>
