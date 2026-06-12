# Cirkle v1 — Gap Map

> Phase 0 audit. Maps the 14 v1 features (blueprint PART 3) against the **actual code** in this
> workspace (Adiciel build, Laravel 10.48.29, ~Nov 2025). Cross-checked against
> `docs/Claude_cirkle-blueprint-v2.html`, the MASTER 2350 xlsx, and the committed production dump.
>
> Legend: ✅ built · 🟡 partial · 🔴 missing · effort S (≤0.5d) / M (1–2d) / L (3–5d+)
>
> _Generated 2026-06-12. Forward-only migrations; never edit a shipped migration._

## Summary table

| # | v1 feature | Status | Where it lives (code) | Effort to finish |
|---|------------|:------:|-----------------------|:----------------:|
| 1 | Bilingual FR/EN homepage + 4-platform selector (res/B2B × FR/EN, **distinct category/profession sets**) | 🟡 | `routes/routes_core.php` (localization mw, `isMultilingual`), `resources/views/pages/home.blade.php`, `resources/views/partials/providers/public-search-filters.blade.php`, `lang/*.{fr,en}.json` | **M** |
| 2 | Postal-code search: black recursive catalogue always shown; available professions green, in columns, with member #s | 🟡 | `app/Http/Controllers/SearchController.php`, `app/Services/SearchService.php`, `app/Services/SearchDataService.php`, `resources/views/partials/search/search.blade.php` | **M** |
| 3 | Profession → **randomized** supplier list → fiche → mailto handoff | 🟡 | `SearchController@profession`, `app/Http/Controllers/ProviderController.php` (`show`, `contact`), `resources/views/pages/providers/show.blade.php`, `partials/providers/single.blade.php` | **S–M** |
| 4 | Member numbering: shared C/F sequence from **2350** + live counter top-right | 🔴 | _none_ — `subscribers.number` is the **civic address number**, not a member ID (`2024_01_24_145200_alter_subscribers…`). No sequence/counter. | **M** |
| 5 | Supplier registration: juridical form, federal tax #, owner names, password + eye toggle | 🟡 | `app/Http/Controllers/SubscriberController.php` (`storeStep1–6`), `resources/views/pages/register/step-1..6.blade.php`, `routes/routes_frontend.php` | **S** |
| 6 | Accept-the-fee gate at **top** of competence flow; fee **variable per profession**, default $75 | 🟡 | `SubscriberController@storeStep6` (flat `setting('registration_fee')` added to cart at the **end**), `2025_08_22_084710_add_registration_fee_to_settings_table.php` | **M** |
| 7 | MASTER 2350 engine: import → tick col B → hide unticked → render only B+C, formatting literal | 🟡 | `app/Imports/ExcelImport.php`, `app/Http/Controllers/Admin/AdminExcelController.php`, `AdminFicheController.php`, `app/Models/Service.php` + `ServiceCategory.php` (+`2025_03_27/28` import cols), `resources/views/fiche/` (**empty dir**), `partials/service-form.blade.php` | **L** |
| 8 | Supplier fiche with tabs: Profil, Compétence (default) + 5 conditional paid tabs | 🟡 | `resources/views/pages/providers/show.blade.php` (anchor **sections** gated by `profile_*_active`, not tabs), `partials/providers/single.blade.php` | **M** |
| 9 | The 5 add-ons (open form → accept → payment → unlock tab): Diplomas/Photos/Permits/Estimation/Recruitment | 🟡 | `app/Http/Controllers/ProfileOptionController.php`, `resources/views/pages/profile-options/*`, models `License`/`SubscriberImage`/`JobOffer`/`Promotion`, `Cart/BasicCartController.php`. **Diplomas (PDIPOMECK) has no analog.** | **L** |
| 10 | Google-style evaluations: logged-in clients, 1–5 + comment; reply needs admin approval; 1–2 → Cirkle | 🟡 | `app/Http/Controllers/EvaluationController.php`, `app/Models/Evaluation.php`, `2024_01_25_224543_create_evaluations_table.php` (**old 5-criteria model**), `pages/evaluation.blade.php`, `partials/starSelector|starDisplayer.blade.php` | **M–L** |
| 11 | Favorites (heart) for suppliers **and** professions + consultation history | 🟡 | `app/Models/LikedSubscriber.php`, `ProviderController@like`, `partials/providers/like.blade.php` (suppliers only). Profession favorites + history **missing**; `ContactedProvider` is the deprecated history analog. | **M** |
| 12 | Stripe (Cashier): one-time **+ recurring** (1/3/6/12 mo), 7-day pre-expiry email + 7-day grace, PDF invoices | 🟡 | `Cart/BasicCartController.php` (one-time Checkout, raw `StripeClient`, `mode=payment`, CAD+TPS/TVQ), `SubscriptionController.php` (pause/unpause/cancel **stubs**), `SubscriptionPrice`/`PurchasedSub`. **Cashier NOT installed.** `barryvdh/laravel-dompdf` available for invoices. | **L** |
| 13 | Law 25 Terms/Privacy (scaffold now; client supplies text) | 🔴 | `resources/views/pages/term-of-use.blade.php` exists but is **empty (0 lines)**; route `term-of-use`. No privacy page. | **S** + client text |
| 14 | Admin panel: approve evaluation replies + manage categories/professions | 🟡 | `app/Http/Controllers/Admin/*` (`AdminGenericController`, `GridController`, `AdminFicheController`, `AdminExcelController`), `routes/routes_backend.php`. Generic CRUD + fiche/excel import exist; **reply-approval workflow missing**. | **M** |

## What's genuinely already done (don't rebuild)

- **Excel → domain import** (`ExcelImport`): parses the MASTER sheet sections (langue, titre interne, catégorie, profession, services-offered list, capabilities, 1/3/6/12-mo prices, keywords) into `ServiceCategory` (category + profession) + `Service` rows (`type=service|capability`) + `SubscriptionPrice`. Wired at `POST /admin/excel/import`.
- **6-step supplier registration** with session-staged models, legal form / federal tax / address / business hours, postal-code capture (up to N codes), profile-option selection, password policy (`(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}`), recaptcha on every POST, confirmation email.
- **Postal-code search service** returning present/non-present **categories and professions** for a postal code + provider type, with per-request caching.
- **One-time Stripe Checkout** end-to-end (order → session → success/cancel → `buyCart` → receipt email), CAD with TPS/TVQ from settings.
- **Profile add-on infrastructure**: License, Photo (`SubscriberImage`), Estimation, Job offer, Promotion, URL — each with form, cart `Purchase`, `profile_*_active` unlock flag, and a public fiche section.
- **Evaluation pipeline** with profanity check, auto-validate, and **1–2 → Cirkle** email routing (`has_less_than_two`).
- **Supplier hearts** (`LikedSubscriber`) + saved searches (`SavedSearch`).
- **CMS/bloc system + generic admin grid CRUD** (Mbiance core) the homepage and pages render from.
- **Production data is present** in `cirkle_db.sql` (subscribers, categories, services, postal codes, subscriptions, prices, settings) — every milestone is demoable without waiting on the 200 fiches.

## Cross-cutting gaps & decisions baked into the code

- **`provider_type` ≠ `platform_id`.** The app models residential/business as `subscribers.provider_type` (`residential|business|both`) + `locale`. There is **no `platform_id`** and categories/professions are **not platform-scoped** — a profession appears in a platform only because a matching-`provider_type` supplier offers it in that postal code. Spec wants 4 platforms with *distinct* category/profession sets ⇒ this is a **data-model decision** (feature 1 + open question).
- **Postal availability is already decided in code = exact match** against the supplier-entered `postal_codes` (no radius, no prefix, no registered-address fallback). This silently answers the blueprint's biggest open question — but Denis's *intent* (service radius?) is unconfirmed. **Flag before building feature 2 styling.**
- **MASTER 2350 fidelity:** the importer **flattens** text (trims, concatenates with `<br>`/spaces) and stores B as `title`, C as `input_label`. The spec requires **literal colour/spacing preservation** and the **"O" red-circle tick** semantics from cols B+C. The xlsx confirms «SEULEMENT LES COLONNES B et C SERONT PRÉSENTÉES» and «cliquer sur "O"… les autres services n'apparaîtront pas». Reconcile importer ↔ public-fiche rendering.
- **Evaluations are the old multi-criteria model** (global/service/reliability/communication/hourly grades), not the re-specced single 1–5 + comment, no supplier-reply, no reply-approval, no logged-in-client gate.
- **`ContactedProvider` still wired** (`EvaluationController@create`, `Subscriber` relations, empty table in prod). Per the video it's dropped — deprecate/replace with consultation history.
- **`storage:link` not run** — `public_html/storage` is absent (and git-ignored). Add a `php artisan storage:link` step locally and in deploy if/when public disk assets are served.
- **Add-on ↔ spec mapping** (existing → v1 code): Photos→`image` (P12PICCK $100), Permits→`license` (PPERMITCK $50), Estimation→`estimation` (PESTCK2 $50), Recruitment→`job_offer` (PHIRECK $100), Promotion→`promotion` (**deferred**, PPROMOCK50). **Diplomas (PDIPOMECK $50) is new** — no existing option. Prices are currently flat `setting('{option}_price')`, not the per-code values.
- **Known-issue (do not touch yet):** `resources/sass/vendor/` missing ⇒ `npm run prod` fails; committed `public_html/dist/compiled/*.min.css|js` are authoritative. Deploy does **not** rebuild assets.

## Suggested build order (milestone-aligned)

1. **M1 ($75)** — member numbering (C/F 2350 + counter), reconcile schema, finish leaking lang keys, lock data-model decisions (platform_id vs provider_type; postal logic).
2. **M2 ($125)** — homepage 4-platform selector + counter; postal-code search styling (black catalogue / green available / columns / member #s); profession → **randomized** list → mailto; supplier heart.
3. **M3 ($150)** — registration polish (owner names, eye toggle), **accept-fee gate at top of competence flow**, per-profession fee, conclusion page.
4. **M4 ($200)** — MASTER 2350 fidelity (literal B+C render), fiche **tabs**, 5 add-on accept→pay→unlock, Google-style evaluations + admin-approved replies.
5. **M5 ($150)** — Stripe recurring + 7-day renewal/grace + PDF invoices, Law 25 text + cookie tool, QA, deploy.
