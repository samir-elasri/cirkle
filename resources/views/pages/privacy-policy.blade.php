@php($locale = app()->getLocale())

{{-- Texte juridique réel (Loi 25) fourni par CIRKLE — doc « 20F ENTENTE DE CONFIDENTIALITÉ »
     (version légale 17/01/2026). Rendu directement dans la vue (versionné), pas via les blocs CMS. --}}
<section>
    <div class="optimal-content-width content-writable">
        <a href="{{ urlRouteName('home') }}" class="ck-back-btn" onclick="if(history.length>1){history.back();return false;}"
           style="display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;padding:8px 16px;border:1px solid #d9d9d9;border-radius:8px;text-decoration:none;color:#1f2430;font-weight:600;background:#fff;">← {{ $locale === 'fr' ? 'Retour' : 'Back' }}</a>

        @if ($locale === 'fr')
            <h1>Politique de confidentialité</h1>
            <p><em>Conforme à la Loi 25 du Québec (projet de loi 25) — Dernière mise à jour : 17 janvier 2026</em></p>

            <h2>1. Identité du responsable des renseignements personnels</h2>
            <p>Le responsable de la protection des renseignements personnels pour le site CirkleServices.com (ci-après le « Site ») est le représentant légal du Site.</p>
            <p>Toute demande relative à la confidentialité, à l'accès, à la rectification ou à la suppression de renseignements personnels peut être transmise à l'adresse suivante :</p>
            <p>Courriel : <a href="mailto:confidentialite@cirkleservices.com">confidentialite@cirkleservices.com</a></p>

            <h2>2. Définitions</h2>
            <p>Aux fins des présentes :</p>
            <ul>
                <li><strong>Renseignement personnel :</strong> toute information concernant une personne physique permettant de l'identifier directement ou indirectement.</li>
                <li><strong>Utilisateur :</strong> toute personne qui accède au Site.</li>
                <li><strong>Consentement :</strong> consentement manifeste, libre, éclairé et donné à des fins spécifiques, conformément à la Loi 25.</li>
            </ul>

            <h2>3. Collecte des renseignements personnels</h2>
            <p>Le Site ne recueille aucun renseignement personnel à l'insu de l'utilisateur.</p>
            <p>Les renseignements personnels sont recueillis uniquement lorsque l'utilisateur les fournit volontairement, notamment lors :</p>
            <ul>
                <li>de la création d'un compte;</li>
                <li>de l'inscription à un service;</li>
                <li>d'un abonnement payant;</li>
                <li>d'une communication avec le Site.</li>
            </ul>
            <p>Les renseignements pouvant être recueillis incluent notamment :</p>
            <ul>
                <li>nom et prénom;</li>
                <li>adresse courriel;</li>
                <li>numéro de téléphone;</li>
                <li>renseignements de facturation;</li>
                <li>toute autre information nécessaire à la prestation des services.</li>
            </ul>

            <h2>4. Finalités de la collecte</h2>
            <p>Les renseignements personnels sont collectés exclusivement pour les finalités suivantes :</p>
            <ul>
                <li>fournir les services demandés;</li>
                <li>gérer les comptes et abonnements;</li>
                <li>traiter les paiements;</li>
                <li>communiquer avec l'utilisateur;</li>
                <li>améliorer le Site et ses fonctionnalités;</li>
                <li>respecter les obligations légales et contractuelles.</li>
            </ul>
            <p>Aucun renseignement personnel n'est utilisé à d'autres fins sans l'obtention d'un nouveau consentement explicite.</p>

            <h2>5. Consentement</h2>
            <p>En utilisant le Site et en fournissant volontairement ses renseignements personnels, l'utilisateur :</p>
            <ul>
                <li>reconnaît avoir été informé des finalités de la collecte;</li>
                <li>consent expressément à la collecte, à l'utilisation et à la conservation de ses renseignements personnels;</li>
                <li>comprend qu'il peut retirer son consentement en tout temps, sous réserve des obligations prévues par la loi.</li>
            </ul>

            <h2>6. Droits des utilisateurs (Loi 25)</h2>
            <p>Conformément à la Loi 25, l'utilisateur dispose des droits suivants :</p>
            <ul>
                <li>droit d'accès à ses renseignements personnels;</li>
                <li>droit de rectification;</li>
                <li>droit au retrait du consentement;</li>
                <li>droit à la suppression, sous réserve des obligations légales;</li>
                <li>droit à la portabilité, lorsque applicable.</li>
            </ul>
            <p>Toute demande doit être formulée par écrit au responsable désigné. Le Site s'engage à répondre dans les délais prescrits par la loi.</p>

            <h2>7. Conservation des renseignements</h2>
            <p>Les renseignements personnels sont conservés uniquement pour la durée nécessaire aux finalités pour lesquelles ils ont été collectés, puis sont détruits ou anonymisés de façon sécuritaire, conformément aux exigences légales.</p>

            <h2>8. Mesures de sécurité</h2>
            <p>Le Site applique des mesures de sécurité administratives, techniques et physiques raisonnables afin de protéger les renseignements personnels contre l'accès non autorisé, la perte, le vol ou la divulgation accidentelle.</p>
            <p>L'utilisateur reconnaît qu'aucun système informatique n'offre une sécurité absolue.</p>

            <h2>9. Incidents de confidentialité</h2>
            <p>En cas d'incident de confidentialité présentant un risque sérieux de préjudice, le Site s'engage à :</p>
            <ul>
                <li>prendre les mesures nécessaires pour réduire les risques;</li>
                <li>aviser la Commission d'accès à l'information du Québec (CAI);</li>
                <li>informer les personnes concernées conformément à la Loi 25;</li>
                <li>tenir un registre des incidents de confidentialité.</li>
            </ul>

            <h2>10. Communication des renseignements à des tiers</h2>
            <p>Les renseignements personnels ne sont ni vendus ni loués.</p>
            <p>Ils peuvent être communiqués uniquement :</p>
            <ul>
                <li>aux fournisseurs technologiques nécessaires à l'exploitation du Site (hébergement, paiement, sécurité);</li>
                <li>lorsque requis par la loi ou une ordonnance judiciaire;</li>
                <li>afin de protéger les droits, la sécurité ou l'intégrité du Site et de ses utilisateurs.</li>
            </ul>
            <p>Tout tiers est contractuellement tenu de respecter des obligations de confidentialité équivalentes.</p>

            <h2>11. Cookies et technologies de suivi</h2>
            <p>Le Site utilise des fichiers témoins (« cookies ») afin de :</p>
            <ul>
                <li>gérer les connexions sécurisées;</li>
                <li>analyser la fréquentation du Site (ex. Google Analytics);</li>
                <li>améliorer l'expérience utilisateur.</li>
            </ul>
            <p>L'utilisateur peut refuser ou limiter l'utilisation des cookies en modifiant les paramètres de son navigateur, sous réserve de certaines limitations fonctionnelles.</p>

            <h2>12. Décisions automatisées et profilage (Loi 25)</h2>
            <p>Le Site peut utiliser des procédés automatisés, incluant des algorithmes ou outils d'analyse, afin de :</p>
            <ul>
                <li>recommander des services ou fournisseurs;</li>
                <li>personnaliser l'affichage du contenu;</li>
                <li>optimiser l'expérience utilisateur.</li>
            </ul>
            <p>Ces procédés ne produisent aucun effet juridique ni décision ayant un impact significatif sur l'utilisateur sans intervention humaine.</p>
            <p>L'utilisateur peut, sur demande :</p>
            <ul>
                <li>être informé de l'existence de tels procédés;</li>
                <li>obtenir des renseignements sur les facteurs ayant mené à une décision automatisée;</li>
                <li>faire corriger les renseignements personnels utilisés.</li>
            </ul>

            <h2>13. Modifications</h2>
            <p>Le Site se réserve le droit de modifier les présentes conditions et la politique de confidentialité sans préavis. Les modifications entrent en vigueur dès leur publication sur le Site.</p>

            <h2>14. Droit applicable et juridiction</h2>
            <p>Les présentes sont régies par les lois de la province de Québec et les lois fédérales applicables du Canada.</p>
            <p>Tout litige relève exclusivement des tribunaux compétents du Québec.</p>

            <h2>15. Consentement final</h2>
            <p>En accédant au Site et en poursuivant sa navigation, l'utilisateur reconnaît avoir lu, compris et accepté sans réserve les présentes conditions d'utilisation et la politique de confidentialité.</p>
        @else
            <h1>Privacy Policy</h1>
            <p><em>Compliant with Québec Law 25 (Bill 25) — Last updated: January 17, 2026</em></p>

            <h2>1. Person in charge of the protection of personal information</h2>
            <p>The person responsible for the protection of personal information for the website CirkleServices.com (the "Site") is the legal representative of the Site.</p>
            <p>Any request relating to privacy, access, correction or deletion of personal information may be sent to:</p>
            <p>Email: <a href="mailto:confidentialite@cirkleservices.com">confidentialite@cirkleservices.com</a></p>

            <h2>2. Definitions</h2>
            <p>For the purposes of this policy:</p>
            <ul>
                <li><strong>Personal Information:</strong> any information relating to a natural person that allows that person to be identified, directly or indirectly.</li>
                <li><strong>User:</strong> any person who accesses the Site.</li>
                <li><strong>Consent:</strong> free, informed, and explicit consent given for specific purposes, as required under Québec Law 25.</li>
            </ul>

            <h2>3. Collection of personal information</h2>
            <p>The Site does not collect any personal information without the User's knowledge.</p>
            <p>Personal information is collected only when voluntarily provided by the User, including but not limited to:</p>
            <ul>
                <li>account creation;</li>
                <li>service registration;</li>
                <li>paid subscriptions;</li>
                <li>communications with the Site.</li>
            </ul>
            <p>The information collected may include:</p>
            <ul>
                <li>first and last name;</li>
                <li>email address;</li>
                <li>phone number;</li>
                <li>billing information;</li>
                <li>any other information necessary to provide the services.</li>
            </ul>

            <h2>4. Purposes of collection</h2>
            <p>Personal information is collected solely for the following purposes:</p>
            <ul>
                <li>providing requested services;</li>
                <li>managing accounts and subscriptions;</li>
                <li>processing payments;</li>
                <li>communicating with Users;</li>
                <li>improving the Site and its functionalities;</li>
                <li>complying with legal and contractual obligations.</li>
            </ul>
            <p>No personal information is used for other purposes without obtaining new explicit consent.</p>

            <h2>5. Consent</h2>
            <p>By using the Site and voluntarily providing personal information, the User:</p>
            <ul>
                <li>acknowledges having been informed of the purposes of collection;</li>
                <li>expressly consents to the collection, use and retention of personal information;</li>
                <li>understands that consent may be withdrawn at any time, subject to legal obligations.</li>
            </ul>

            <h2>6. User rights (Law 25)</h2>
            <p>In accordance with Québec Law 25, Users have the following rights:</p>
            <ul>
                <li>right of access to their personal information;</li>
                <li>right to rectification;</li>
                <li>right to withdraw consent;</li>
                <li>right to deletion, subject to legal obligations;</li>
                <li>right to data portability, where applicable.</li>
            </ul>
            <p>Requests must be submitted in writing to the designated contact. The Site undertakes to respond within the legally prescribed timeframes.</p>

            <h2>7. Retention of personal information</h2>
            <p>Personal information is retained only for as long as necessary to fulfill the purposes for which it was collected, after which it is securely destroyed or anonymized in accordance with applicable laws.</p>

            <h2>8. Security measures</h2>
            <p>The Site implements reasonable administrative, technical and physical security measures to protect personal information against unauthorized access, loss, theft or accidental disclosure.</p>
            <p>The User acknowledges that no information system can guarantee absolute security.</p>

            <h2>9. Privacy incidents</h2>
            <p>In the event of a privacy incident involving a risk of serious injury, the Site undertakes to:</p>
            <ul>
                <li>take reasonable measures to reduce the risk;</li>
                <li>notify the Commission d'accès à l'information du Québec (CAI);</li>
                <li>inform affected individuals as required by law;</li>
                <li>maintain a privacy incident register in accordance with Law 25.</li>
            </ul>

            <h2>10. Disclosure to third parties</h2>
            <p>Personal information is not sold or rented.</p>
            <p>It may be disclosed only:</p>
            <ul>
                <li>to essential technology service providers (hosting, payment processing, security);</li>
                <li>when required by law or court order;</li>
                <li>to protect the rights, security or integrity of the Site and its Users.</li>
            </ul>
            <p>All third parties are contractually bound to confidentiality obligations equivalent to those of the Site.</p>

            <h2>11. Cookies and tracking technologies</h2>
            <p>The Site uses cookies to:</p>
            <ul>
                <li>manage secure logins;</li>
                <li>analyze website traffic (e.g. Google Analytics);</li>
                <li>improve user experience.</li>
            </ul>
            <p>Users may disable cookies through their browser settings, subject to certain functional limitations.</p>

            <h2>12. Automated decision-making and profiling (Law 25)</h2>
            <p>The Site may use automated processes, including algorithms or analytical tools, in order to:</p>
            <ul>
                <li>recommend services or service providers;</li>
                <li>personalize content display;</li>
                <li>optimize user experience.</li>
            </ul>
            <p>These processes do not produce legal effects or decisions with significant impact on Users without human intervention.</p>
            <p>Upon request, Users may:</p>
            <ul>
                <li>be informed of the existence of such automated processes;</li>
                <li>receive information about the factors used in automated decisions;</li>
                <li>request correction of the personal information involved.</li>
            </ul>

            <h2>13. Modifications</h2>
            <p>The Site reserves the right to modify these Terms of Use and Privacy Policy at any time without prior notice. Modifications take effect upon publication on the Site.</p>

            <h2>14. Governing law and jurisdiction</h2>
            <p>These Terms and Policies are governed by the laws of the Province of Québec and applicable federal laws of Canada.</p>
            <p>Any dispute shall fall under the exclusive jurisdiction of the competent courts of Québec.</p>

            <h2>15. Final consent</h2>
            <p>By accessing and continuing to use the Site, the User acknowledges having read, understood and accepted without reservation these Terms of Use and the Privacy Policy.</p>
        @endif
    </div>
</section>
