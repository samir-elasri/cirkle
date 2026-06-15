# Cirkle — Guide de test complet (site en ligne)

How to test every v1 feature on the **live site**: <https://cirkleservices.com>

- The site opens in **English** at the root; the default content language is **French**.
- Add **`/fr`** or **`/en`** in front of any path to force a language, e.g. `/fr/profil` vs `/en/profile`.
- Tip: use a **private/incognito window** so you can be logged out, and a second normal window logged in — handy for the "logged-in vs anonymous" tests.

---

## Denis's feedback (round 1) — what it meant & status

| Denis said (FR) | What it means | Status |
|-----------------|---------------|--------|
| *« je me suis inscrit comme client … pas reçu de confirmation et de no »* | His client signup **worked** (account `darryn1968data@gmail.com` → **C02361** was created), but he got no confirmation email and never saw his number. | ✅ **Fixed & live.** (1) Email was completely unconfigured on the server (`MAIL_HOST`/`FROM` empty) → switched to the server's sendmail, emails now send. (2) His **member number now shows** on his profile + in the success message after signup. |
| *« je n'ai pas vu les options »* | The 5 paid options weren't visible to him. They only appeared once a supplier was **published** (`is_public`), and were buried/mislabelled. | ✅ **Fixed & live.** A supplier can now access **« Mon formulaire 2350 »** + their options **anytime** from the profile, published or not. |
| *« mon master 2350 sera le form à remplir par le fourn … colonne B et C as is … pas remplir diff form … accès en tout temps »* | The supplier should fill **the 2350 itself** (the « O » ticks = col B, the texts = col C), as **one form**, re-editable anytime; his copy stays intact. | 🟢 **Largely done + reframed.** The competence form **is** the literal 2350 (B+C, O ticks + « AUTRE PAR FOURNISSEUR » fields), now clearly labelled **« Mon formulaire 2350 »** and **editable anytime**. **Next iteration (verify once live):** folding the 5 options in as inline 2350 sections. |
| *« tous les services liés mis à jour automatiquement ? » → **non*** | When a master template changes, should existing suppliers' fiches auto-update? **No.** | ✅ Already the case — each supplier's filled form is independent; editing a template never overwrites a supplier's saved copy. |
| *« utilise l'ancien [2350] et on vérifie après »* | Use the existing master 2350 file for now; refine after launch. | ✅ Using the existing `1 WEB MASTER 2350 … 010626.xlsx`. |
| *« la 1ère mise en ligne doit être PARFAITE »* | First launch must be perfect. | 🎯 Ongoing — this guide + his round-by-round testing is how we get there. |
| **(found during testing)** supplier registration: step 3 → *Suivant* → **« Page introuvable »** | Step 4 crashed for every profession — a bad translation key blew up once the registration fee was > 0, so **no supplier could finish signing up**. | ✅ **Fixed & live.** Full flow re-tested end-to-end on prod (steps 1→6 → cart, incl. the auto-client). Suppliers can now register all the way through, and the **options are visible at step 5**. |

> **How to delete a member account** (he asked): log in to **/admin** → **Subscribers** → search the email → **Delete**. The email is unique, so deleting frees it for a fresh signup.

---

## 0. Test accounts (ready to use)

> ⚠️ **These are temporary TEST credentials on the live site.** Rotate or delete them before the public launch (Aug 1). Don't share publicly.

| Role | Login URL | Email | Password |
|------|-----------|-------|----------|
| **Admin** (back office) | <https://cirkleservices.com/admin> | `admin-test@cirkle.test` | `Cirkle#Admin2026` |
| **Client** (normal user) | <https://cirkleservices.com/fr/profil> | `client-test@cirkle.test` | `Cirkle#Client2026` |
| **Fournisseur** (supplier) | <https://cirkleservices.com/fr/profil> | `demo-arboriste@cirkleservices.com` | `Cirkle#Fourn2026` |

There are only **two kinds of user**: the **admin** (back office, `users` table) and **members** (`subscribers` — each is either a *client* or a *fournisseur*, flagged by `is_provider`). The fournisseur test account above is the fully-populated **demo fiche** (member **F02362**, profession *Arboriste*, postal code **H2X 1Y4**), so it already has competences, paid tabs (Permis / Diplômes / Estimation) and 2 reviews to look at.

Useful demo URLs:
- Demo fournisseur fiche: <https://cirkleservices.com/fr/fournisseur/78>
- Terms: <https://cirkleservices.com/fr/conditions-d-utilisation> · Privacy: <https://cirkleservices.com/fr/politique-de-confidentialite>

---

## 1. Logging in

### 1.1 As a client or a fournisseur (members)
1. Go to **<https://cirkleservices.com/fr/profil>** (or click **« Se connecter »** top-right).
2. Enter the email + password from the table above → **Se connecter**.
3. **Client** → lands on the member profile ("Mon espace") with the *Client* tabs.
   **Fournisseur** → same profile but also shows a *Fournisseur* top-tab.
4. Log out from the top-right button (**Déconnexion**).

### 1.2 As admin (back office)
1. Go to **<https://cirkleservices.com/admin>** → you're redirected to the login page.
2. Email `admin-test@cirkle.test` / password `Cirkle#Admin2026` → **Login**.
3. You land on the admin dashboard (left sidebar = all manageable entities: subscribers, evaluations, categories, services, settings, etc.).

---

## 2. Signing up (create your own accounts)

### 2.1 New client (quick)
1. **<https://cirkleservices.com/fr/sinscrire>**.
2. Fill name, email, address, password (must contain an uppercase, a lowercase and a digit, 8+ chars), confirm, accept the terms.
3. **Notice the 👁 eye icon** on each password field — click it to show/hide the password (feature #5).
4. Submit → you're logged in and a confirmation email is queued.

### 2.2 New fournisseur (full multi-step flow) — tests features #5, #6, #7
1. **<https://cirkleservices.com/fr/sinscrire/fournisseur>** (Step 1).
2. **Step 1 — legal identity:** company legal name, **Noms des propriétaires** (owner names, feature #5), juridical form, **federal tax number**, address, phone, email, business hours. → *Suivant*.
3. **Step 2 — competence:** choose **Résidentiel/Business**, then a **profession** (e.g. *Arboriste*).
   - ➡️ **Fee gate (feature #6):** before the checklist appears, a yellow box shows the **fiche fee** (e.g. **75,00 $**) and the warning text. The services checklist stays hidden until you click **« J'accepte ces frais »**.
   - After accepting, the **competence checklist** appears (feature #7) with the literal Excel formatting (colours, spacing, indentation preserved). Tick the services you offer; some rows reveal a free-text field.
4. **Steps 3–4:** subscription package + postal codes (1 to 10).
5. **Step 5 — paid options:** optionally tick Permis / Diplômes / Photos / Estimation / Recrutement and fill them in.
6. **Step 6:** set the password (👁 eye toggle here too), accept terms → submit → you're taken to the **cart** with the fiche fee + chosen options.
   - 🔎 **Auto-client (feature #5):** behind the scenes this supplier also creates a **paired client account** with the next number — so the member counter jumps by **two** (one **F**, one **C**).

> Payment at the cart uses Stripe. One-time payment works; the **automatic recurring charge** is the one piece still waiting on Denis's Stripe account (see §14).

---

## 3. Bilingual homepage + 4-platform selector  *(feature #1)*
1. Open **<https://cirkleservices.com>** → it lands in **English**.
2. Top of the homepage: **4 tiles** — *Residential English · Résidentiel Français · B2B English · B2B Français*.
3. Click one → it turns **yellow** (selected) and the page switches to that language/platform; the search below is pre-set to Residential or B2B accordingly.
4. ✅ Expected: each tile is in its own language; only the clicked one is highlighted; reload keeps your choice.

---

## 4. Postal-code search — catalogue + green professions  *(feature #2)*
1. On the homepage, in the search box enter postal code **`H2X 1Y4`**, type **Résidentiel**, **Rechercher**.
2. Scroll to **« Catalogue des services »**: the **full catalogue** is always shown in **black** (categories on the left, professions on the right, in columns).
3. ✅ Expected: **Arboriste** (under *Paysagiste*) appears in **green and clickable**, with a count **(1)** = number of suppliers there. Everything else stays black/non-clickable.
4. Switch the toggle to **Business** and search again → Arboriste turns **black** (the demo supplier is residential). Try a random postal code (e.g. `Z9Z 9Z9`) → all black, catalogue still shown.

---

## 5. Member numbers + live counter  *(feature #4)*
1. Look **top-right of any page** → the **member counter** (e.g. *Membres 2364*) = the most recent member number.
2. On a fournisseur fiche or in a search result, member IDs show as **F02362** (fournisseur) / **C02364** (client) — shared sequence starting at **2350**.
3. ✅ Expected: registering a new supplier increases the counter by 2 (F + auto-client C).

---

## 6. Profession → supplier list → fiche → mailto  *(feature #3)*
1. From the catalogue (§4), click the green **Arboriste**.
2. You get the **list of suppliers** for that profession (random order each visit for fairness), each card showing the member number + what the supplier offers.
3. Click **« Voir »** → the supplier **fiche** opens.
4. Log in as the **client** first, then on the fiche **Profil** tab click **« Contacter le fournisseur »** → it opens your email app (**mailto**) addressed to the supplier.

---

## 7. The FICHE 2350 + fiche tabs  *(features #7, #8)*
Open the demo fiche: **<https://cirkleservices.com/fr/fournisseur/78>**.
1. **Tabs** across the top: **Profil · Compétence · Permis · Diplômes · Estimation** (paid tabs appear only because this supplier activated them). **Compétence is open by default.**
2. **Compétence tab:** the services the supplier ticked, rendered **exactly like the Excel** — coloured words (orange/blue), bold, and the literal indentation/spacing. Unticked services do **not** appear. Lines with a supplier note show it in green italic.
3. **Profil tab:** company, **owner names**, address, phone, business hours, etc.
4. Switch languages with `/en/provider/78` → the fiche is the French master; the English twin would be its own imported fiche.

---

## 8. The 5 paid options  *(feature #9)*
On the demo fiche, the **Permis**, **Diplômes** and **Estimation** tabs are filled in.
- **Diplômes** tab → a table: *Cours / formation · École · Date* (the new PDIPOMECK option).
- **Permis** tab → the licence (RBQ…).
- **Estimation** tab → estimated cost + accepted payment methods.

To test **buying/managing** an option as a supplier:
1. Log in as the **fournisseur**, go to **Mon espace** → the *Fournisseur* tab → **profile options**.
2. Each option shows its **price** (Permis 50 $ · Diplômes 50 $ · Photos 100 $ · Estimation 50 $ · Recrutement 100 $). Tick one, add its content (e.g. add a diploma: course / school / date), and it goes to the cart to pay → once paid, its tab appears on the public fiche.

---

## 9. Google-style evaluations + supplier reply  *(features #10, #14)*
**See existing reviews (anyone):** on the demo fiche, scroll to **Évaluations** → average ⭐ + count, two reviews, one with an approved **« Réponse du fournisseur »**.

**Leave a review (client):**
1. Log in as the **client**, open the demo fiche.
2. In the Évaluations box: pick a **star rating (1–5)** + a comment → **« Laisser un avis »**.
3. ✅ A 1- or 2-star review is automatically routed to Cirkle by email (low-rating alert).
4. Anonymous (logged out) users see the reviews but get a **"login required"** message instead of the form, and **cannot review their own** fiche.

**Reply as the supplier:**
1. Log in as the **fournisseur**, open your own fiche (`/fr/fournisseur/78`).
2. Under a review without a reply, type a **reply** → submit → you see **« Réponse en attente d'approbation »** (it is NOT public yet).

**Approve the reply (admin):**
1. Log in to **/admin** → left menu **Évaluations** → open that evaluation (Edit).
2. Toggle **« Réponse approuvée »** = yes → Save.
3. Reload the public fiche → the reply now shows publicly under the review.

---

## 10. Favorites + consultation history  *(feature #11)*
Log in as the **client**:
1. **Favourite a supplier:** on a fiche, click the **heart** → it fills in.
2. **Favourite a profession:** open a profession page (§6) → click the **heart** next to the profession title.
3. **Consultation history:** every fiche you open as a logged-in client is recorded.
4. Go to **Mon espace** → tabs:
   - **Fournisseurs favoris** → the suppliers you hearted.
   - **Professions favorites** → the professions you hearted.
   - **Historique des consultations** → fiches you recently viewed.

---

## 11. PDF invoices + subscription lifecycle  *(feature #12)*
**Invoices:**
1. Log in (a member who has at least one paid order), **Mon espace** → **Mes factures**.
2. Click **« Télécharger la facture »** → a **PDF** downloads (CIRKLE header, line items, TPS/TVQ, total).

**Cancel at term end:** as a fournisseur with an active subscription, **Mon espace → Fournisseur → Annuler mon abonnement** → it stays active until the end date (no refund), and won't renew.

**Renewal reminders & grace (automatic, server-side):** 7 days before expiry the member gets a renewal email; after expiry there's a 7-day grace, then the fiche is hidden. This runs from the daily cron — needs the N0C cron set to `php artisan schedule:run` (see §14).

---

## 12. Legal pages — Law 25  *(feature #13)*
1. **Terms:** <https://cirkleservices.com/fr/conditions-d-utilisation>
2. **Privacy:** <https://cirkleservices.com/fr/politique-de-confidentialite>
3. ✅ Both render structured **Law 25** content (consent, access/rectification/deletion, CAI breach notice, automated decisions, cookies) in FR and EN, with a visible **"Draft"** banner. When Denis enters the final legal text via the admin CMS, it replaces the draft automatically.

---

## 13. Admin back office  *(feature #14 + content management)*
Logged into **/admin** you can:
- **Approve supplier replies** (§9).
- **Manage categories & professions** (left menu → *Service categories* / *Services*).
- **Import a MASTER 2350 fiche:** the Excel importer turns one of Denis's `.xlsx` files into a profession with its competences/prices. (Each of the ~200 fiches imports the same way; the engine is done, the data entry is separate.)
- Manage subscribers, settings (option prices/titles, emails), etc.

---

## 14. What is *not* testable yet (waiting on Denis / external)
These are **not bugs** — they need things only the client can provide:
1. **Automatic recurring Stripe charge** — needs Denis's **Stripe account** (recurring price IDs + webhook secret + URL). One-time payments, invoices, renewal emails, grace and cancel-at-term-end are all built; only the *auto-charge* is pending.
2. **Final Law 25 legal text** — pages are live with drafts; the real wording comes from Denis/his lawyer.
3. **Cookie-consent tool** (~$200/yr) — a paid external service to install.
4. **The daily cron** — N0C must run `* * * * * php artisan schedule:run` so renewal/grace fires.
5. **The ~200 fiches** — data entry (the import engine is ready).

---

### Quick reference

| Action | URL |
|--------|-----|
| Home | <https://cirkleservices.com> |
| Member login | `/fr/profil` |
| Admin login | `/admin` |
| Client signup | `/fr/sinscrire` |
| Fournisseur signup | `/fr/sinscrire/fournisseur` |
| Demo fiche | `/fr/fournisseur/78` |
| Terms / Privacy | `/fr/conditions-d-utilisation` · `/fr/politique-de-confidentialite` |
