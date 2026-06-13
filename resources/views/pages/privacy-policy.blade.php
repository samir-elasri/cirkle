@php($locale = app()->getLocale())

<section>
    <div class="optimal-content-width content-writable">
        {{-- Texte réel via blocs CMS si présent; sinon ébauche conforme à la Loi 25 (feature #13). --}}
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
                <h1>Politique de confidentialité</h1>
                <p>CIRKLE (cirkleservices.com) protège vos renseignements personnels conformément à la <strong>Loi 25</strong> (Loi modernisant des dispositions législatives en matière de protection des renseignements personnels) du Québec.</p>

                <h2>1. Responsable de la protection des renseignements personnels</h2>
                <p>Pour toute question, communiquez avec notre responsable à : <a href="mailto:servclient@cirkleservices.com">servclient@cirkleservices.com</a>.</p>

                <h2>2. Renseignements recueillis</h2>
                <p>Identité, coordonnées, adresse, renseignements d'entreprise (fournisseurs), historique de consultation et de favoris, et données de paiement traitées par notre prestataire (Stripe).</p>

                <h2>3. Finalités</h2>
                <p>Créer et gérer votre compte, permettre la mise en relation client-fournisseur, traiter les paiements, et améliorer le service.</p>

                <h2>4. Consentement</h2>
                <p>En utilisant la plateforme, vous consentez à la collecte et à l'utilisation décrites ici. Vous pouvez retirer votre consentement en tout temps.</p>

                <h2>5. Vos droits : accès, rectification, suppression</h2>
                <p>Vous pouvez demander l'accès, la rectification ou la suppression de vos renseignements en écrivant au responsable.</p>

                <h2>6. Conservation</h2>
                <p>Vos renseignements sont conservés le temps nécessaire aux finalités ci-dessus, puis détruits ou anonymisés.</p>

                <h2>7. Communication et hébergement</h2>
                <p>Vos renseignements ne sont pas vendus. Certains prestataires (paiement, hébergement) peuvent les traiter pour notre compte.</p>

                <h2>8. Incident de confidentialité</h2>
                <p>En cas d'incident présentant un risque de préjudice sérieux, CIRKLE avisera la Commission d'accès à l'information (CAI) et les personnes concernées.</p>

                <h2>9. Décisions automatisées</h2>
                <p>CIRKLE ne prend pas de décision fondée exclusivement sur un traitement automatisé ayant un effet sur vous.</p>

                <h2>10. Témoins (cookies)</h2>
                <p>Le site utilise des témoins nécessaires à son fonctionnement; un outil de consentement aux témoins est utilisé conformément à la Loi 25.</p>
            @else
                <h1>Privacy Policy</h1>
                <p>CIRKLE (cirkleservices.com) protects your personal information in accordance with Quebec's <strong>Law 25</strong> (Act to modernize legislative provisions respecting the protection of personal information).</p>

                <h2>1. Privacy officer</h2>
                <p>For any question, contact our privacy officer at: <a href="mailto:servclient@cirkleservices.com">servclient@cirkleservices.com</a>.</p>

                <h2>2. Information collected</h2>
                <p>Identity, contact details, address, business information (suppliers), consultation and favorites history, and payment data handled by our processor (Stripe).</p>

                <h2>3. Purposes</h2>
                <p>Create and manage your account, enable client-supplier introductions, process payments, and improve the service.</p>

                <h2>4. Consent</h2>
                <p>By using the platform you consent to the collection and use described here. You may withdraw your consent at any time.</p>

                <h2>5. Your rights: access, rectification, deletion</h2>
                <p>You may request access to, rectification of, or deletion of your information by writing to the privacy officer.</p>

                <h2>6. Retention</h2>
                <p>Your information is kept only as long as needed for the purposes above, then destroyed or anonymized.</p>

                <h2>7. Disclosure and hosting</h2>
                <p>Your information is not sold. Certain providers (payment, hosting) may process it on our behalf.</p>

                <h2>8. Privacy incident</h2>
                <p>In the event of an incident posing a risk of serious harm, CIRKLE will notify the Commission d'accès à l'information (CAI) and the affected individuals.</p>

                <h2>9. Automated decisions</h2>
                <p>CIRKLE does not make decisions based exclusively on automated processing that affect you.</p>

                <h2>10. Cookies</h2>
                <p>The site uses cookies necessary for its operation; a cookie-consent tool is used in accordance with Law 25.</p>
            @endif
        @endif
    </div>
</section>
