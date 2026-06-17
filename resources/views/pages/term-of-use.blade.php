@php($locale = app()->getLocale())

{{-- Conditions d'utilisation : termes propres à CIRKLE (objet, comptes, frais, avis) +
     clauses juridiques fournies par CIRKLE — doc « 20F » (responsabilité, indemnisation,
     droit applicable, consentement). Rendu versionné (pas via les blocs CMS). --}}
<section>
    <div class="optimal-content-width content-writable">
        <a href="{{ urlRouteName('home') }}" class="ck-back-btn" onclick="if(history.length>1){history.back();return false;}"
           style="display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;padding:8px 16px;border:1px solid #d9d9d9;border-radius:8px;text-decoration:none;color:#1f2430;font-weight:600;background:#fff;">← {{ $locale === 'fr' ? 'Retour' : 'Back' }}</a>

        @if ($locale === 'fr')
            <h1>Conditions d'utilisation</h1>
            <p><em>Conforme à la Loi 25 du Québec — Dernière mise à jour : 17 janvier 2026</em></p>
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
            <p>Le Site est fourni « tel quel » et « selon sa disponibilité ».</p>
            <p>En aucun cas, le Site ne pourra être tenu responsable de tout dommage indirect, consécutif ou spécial, incluant notamment toute perte de données, de revenus ou de profits, découlant de l'utilisation ou de l'impossibilité d'utiliser le Site.</p>

            <h2>6. Indemnisation</h2>
            <p>L'utilisateur accepte d'indemniser et de tenir indemne le Site, ses dirigeants, représentants et mandataires, de toute réclamation, poursuite, perte ou dommage découlant :</p>
            <ul>
                <li>de l'utilisation non conforme du Site;</li>
                <li>du non-respect des présentes conditions;</li>
                <li>d'un manquement légal imputable à l'utilisateur.</li>
            </ul>

            <h2>7. Protection des renseignements personnels (Loi 25)</h2>
            <p>Le traitement de vos renseignements personnels est décrit dans notre <a href="{{ urlRouteName('privacy-policy') }}">Politique de confidentialité</a>.</p>

            <h2>8. Modifications</h2>
            <p>Le Site se réserve le droit de modifier les présentes conditions et la politique de confidentialité sans préavis. Les modifications entrent en vigueur dès leur publication sur le Site.</p>

            <h2>9. Droit applicable et juridiction</h2>
            <p>Les présentes sont régies par les lois de la province de Québec et les lois fédérales applicables du Canada.</p>
            <p>Tout litige relève exclusivement des tribunaux compétents du Québec.</p>

            <h2>10. Consentement final</h2>
            <p>En accédant au Site et en poursuivant sa navigation, l'utilisateur reconnaît avoir lu, compris et accepté sans réserve les présentes conditions d'utilisation et la politique de confidentialité.</p>
        @else
            <h1>Terms of Use</h1>
            <p><em>Compliant with Québec Law 25 — Last updated: January 17, 2026</em></p>
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
            <p>The Site is provided "as is" and "as available."</p>
            <p>Under no circumstances shall the Site be liable for any indirect, consequential or special damages, including loss of data, revenue or profits, resulting from the use or inability to use the Site.</p>

            <h2>6. Indemnification</h2>
            <p>The User agrees to indemnify and hold harmless the Site, its officers, representatives and agents from any claim, loss, damage or expense arising from:</p>
            <ul>
                <li>improper use of the Site;</li>
                <li>breach of these terms;</li>
                <li>any legal violation attributable to the User.</li>
            </ul>

            <h2>7. Personal information (Law 25)</h2>
            <p>How we handle your personal information is described in our <a href="{{ urlRouteName('privacy-policy') }}">Privacy Policy</a>.</p>

            <h2>8. Modifications</h2>
            <p>The Site reserves the right to modify these Terms of Use and Privacy Policy at any time without prior notice. Modifications take effect upon publication on the Site.</p>

            <h2>9. Governing law and jurisdiction</h2>
            <p>These Terms and Policies are governed by the laws of the Province of Québec and applicable federal laws of Canada.</p>
            <p>Any dispute shall fall under the exclusive jurisdiction of the competent courts of Québec.</p>

            <h2>10. Final consent</h2>
            <p>By accessing and continuing to use the Site, the User acknowledges having read, understood and accepted without reservation these Terms of Use and the Privacy Policy.</p>
        @endif
    </div>
</section>
