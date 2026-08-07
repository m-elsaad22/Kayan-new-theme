# KAYAN Theme — v2.4.1 Integration & Release Engineering Report

**Date:** 2026-08-07
**Theme version:** 2.4.1 (was 2.4.0)
**Platform version:** 6.0.0 (unchanged — no platform architecture was modified)
**Type:** Integration / release engineering only. No new features, no
redesign, no new architecture, no Phase 7.

---

## 0. Why this phase exists

After Phase 6 shipped, the repository's `main` branch ended up containing
**two independent generations of the KAYAN Theme** at the same time:

- `kayan-theme/` — the theme this whole project (Phases 1–6) was built
  in: `kayan-platform` (Admin Platform, Programmatic SEO, AI, Workflow,
  Quality, Migration Engine) plus the base RUKN v3 theme, version 2.4.0.
- `kayan-theme/kayan-theme/` — a **parallel, disconnected** production
  line (v1.4.0–v1.4.11) that fixed real bugs in the same base theme
  (Rank Math activation, header/logo, booking injection, icon picker,
  interactive price booking) but never knew `kayan-platform` existed.

Both lines descended from the same original theme files but diverged
after that. Merging the four open pull requests into `main` (a separate,
already-completed task) landed both trees side by side without
reconciling them — which is exactly the "two generations" problem this
report resolves.

This phase performed a **real, file-by-file, line-by-line comparison**
of every difference between the two trees, merged genuine fixes into the
canonical `kayan-theme/` tree, rejected changes that would have
regressed platform architecture or reintroduced duplicate SEO output,
and removed the now-fully-reconciled duplicate tree.

---

## 1. Exact files merged (and how)

Comparison scope: `kayan-theme/` vs `kayan-theme/kayan-theme/` as they
stood before this phase — **26 files with the same name but different
content**, **2 packs unique to the legacy tree** (`kayan-price-pay`,
`kayan-seo`), and a handful of top-level files (`functions.php`,
`syntax.php`, `index.php`, `readme.txt`, `style.css`) not previously
counted in the "26".

### Adopted wholesale (legacy version is a strict, self-contained fix — no platform touch-points)
| File | What it fixes |
|---|---|
| `components/packs/@Mega-Menu/Taxonomy-posts.php` | **Fatal PHP parse error** (broken `if/elseif`, mismatched `get_term_by()` arity, literal `echo 'fsdfj'` placeholder). Replaced with the legacy tree's clean, escaped rewrite. |
| `components/packs/modify_img/setup.php` | Missing-metadata fallback, real `src` even under lazy-load (no more broken-image icon before JS runs), proper `esc_attr()` on every output attribute (previously unescaped). |
| `components/packs/SvgCenter/icon-helpers.php` (new file) | `kayan_icon_html()` + a 140+ entry Font Awesome 6 Free icon catalog — the single source of truth for every icon field in the theme. |
| `components/packs/FieldsMachine/FieldsContext/SVG-Icon.php` | Replaces the near-empty legacy SVG picker with a real, searchable FA6 Free picker (uses `icon-helpers.php`). |
| `components/packs/FieldsMachine/UI/Custom-Style.css`, `Custom-Setup.js` | Text-wrapping fixes (`white-space: normal` instead of `nowrap` truncating admin field labels), icon weight fixes (`800`→`900`, the actual FA6 Free bold weight), sortable-widget re-init after adding a new item, removes a stray `console.log`. |
| `components/packs/FieldsMachine/UI/css/admin-ui-fixes.css` (new file) | Small additional admin CSS overrides referenced by the enqueue fix below. |
| `components/packs/kayan-booking/assets/css/kayan-booking.css` | Mobile breakpoints (single-column forms, sticky nav buttons, tap-target sizing). |
| `components/styles/fa-free-fixes.css` (new file) | Global Font Awesome weight/rendering fixes, loaded from both the frontend header and the admin enqueue. |
| `components/packs/kayan-price-pay/` (new pack, 3 files) | See §5 — ported wholesale, genuinely new/non-duplicate functionality. |
| `components/packs/shortcodes/codes/price_list.php` | Full rewrite: `[post_prices]` now renders selectable package buttons wired to `Kayan_Price_Pay`, instead of a static price table. |

### Merged surgically (kept platform-specific parts, added the legacy fix on top)
| File | What was kept from the platform tree | What was merged in from the legacy tree |
|---|---|---|
| `functions.php` | Everything | One-line bug fix: `$this->TempURL` now uses `get_template_directory_uri()` instead of `get_template_directory()` (was returning a filesystem path where a URL was expected). |
| `syntax.php` | Everything | Guard `is_array( $category )` before `foreach` — `get_the_terms()` can return `false`/`WP_Error`. |
| `index.php` | — | Replaced the fallback template's call to an **undefined method** (`$ThemeTree->TemplatePart()`, a certain fatal if ever reached) with a working `ThemeStatic::Blade('index')` call, matching `Locate()`'s own routing for `is_home()`. |
| `components/packs/#header/part.php` | The Rank-Math-gated meta-description/title fallback logic (never overrides Rank Math) | **Critical fix**: removed a stray extra `}` that made this file a PHP fatal parse error on every page load (see §8). Added `fa-free-fixes.css` + a small critical CSS block that guarantees the logo is visible even before JS/lazy-load runs. Replaced `rukn_v3_render_logo()` with a version that supports every historical logo-storage format, prefers the original full-size image, adds `loading="eager"`/`fetchpriority="high"`/lazy-load-skip attributes, and escapes every output (`esc_url`/`esc_attr`/`wp_kses`) where the old code echoed raw values. |
| `components/packs/#footer/part.php` | Everything else (menu, mobile panel, inline JS) | Floating call/WhatsApp buttons now show immediately instead of only after 500px of scroll; a conditional "احجز الآن" (Book now) floating button is injected **only** on pages that actually render a `kayan-price-pay` booking block; fixed `AjaxCenter` URL casing (`/ajaxcenter/` → `/AjaxCenter/`, matching the actual registered rewrite endpoint). |
| `components/packs/AjaxCenter/setup.php` | The LFI allow-list defense itself | Fixed a real bug in that defense: `sanitize_key()` lowercases/strips characters, so exact-case filenames like `TabsActions.php` never matched the allow-list and always 404'd. Now resolves the sanitized key back to the real filename via a lookup map. |
| `components/packs/Enqueues/setup.php` | The overall "protect Rank Math assets" intent | Replaced a single `strpos($handle,'rank-math')` check with a real allow-list covering Rank Math's own handles, its Gutenberg/WP-core dependencies (`wp-i18n`, `wp-hooks`, `wp-element`, `wp-data`, `lodash`, …), and the theme's own `kayan-*`/`yourcolor-*` assets — the old check would dequeue a shared WP-core script the moment Rank Math also depended on it. Also stopped stripping `?ver=` from Rank Math's own asset URLs (cache-busting safety), and switched from `wp_deregister_script()` to `wp_dequeue_script()` only (deregistering could break other code's `wp_add_inline_script()` calls to the same handle). |
| `components/packs/FieldsMachine/Enqueues.php` | Everything else | Scoped all of FieldsMachine's heavy admin assets (Bootstrap, owl-carousel, CodeMirror, jQuery UI CDN) to actual theme-settings/post-edit screens only, instead of loading them on every wp-admin page including Rank Math's own settings screens. Added a shared `window.kayanInitSortables()` bootstrap (used by the homepage-widget drag-reorder fix) and explicit `wp_enqueue_script('jquery-ui-sortable'/'draggable'/'droppable')`. |
| `components/packs/kayan-booking/setup.php` | Everything else | **Real functional bug fix**: removed `! in_the_loop()` from the injection guard — this theme's `@single` templates call `the_content()` outside WordPress's classic loop (`Locate()` → `Blade()`, no `the_post()`), so `in_the_loop()` is always `false` there and the booking wizard **never injected at all** on real service/post pages. Added a `static $injected` guard to prevent double-injection from secondary `the_content` calls. Icon field now goes through `kayan_icon_html()`. |
| `components/packs/theme-seo/setup.php` | Rank Math detection via `class_exists('RankMath')` (not the rejected `kayan-seo` bridge, see §4) | Extracted `resolve_title()` as its own method, added null-safety (`$post &&`, `$obj &&`, ternary fallbacks) to prevent notices/fatals on edge cases (deleted parent post, deleted author), and added a `current_theme_supports('title-tag')` early return in `Title()` so it can never double-print `<title>` now that `title-tag` support is always declared. |
| `components/packs/SvgCenter/setup.php` | Everything else | `sanitize_file_name()` on the icon slug before using it in a `require()` path (defense-in-depth path-traversal hardening, matching the same pattern already used by `AjaxCenter`). |
| `components/packs/@models/offers.php`, `services.php`, `components/packs/YourColorWidgets/model-widgets/Standard/price.php`, `components/packs/#PriceBoxes/part.php` | Layout/markup | Icon fields now render via `kayan_icon_html()`; price cards/boxes gained `kpp-selectable`/`data-package`/`data-amount`/`data-currency` attributes and `esc_html()`/`esc_attr()` on previously-unescaped title output, wiring them to `kayan-price-pay` (see §5). |
| `components/styles/rukn-v3.css` | Everything else | `.fab-stack` now defaults to visible (`opacity:1;visibility:visible`) instead of hidden-until-scroll, matching the footer JS change. |
| `components/packs/kayan-version/setup.php` | Everything else | Added two new self-check rows (`fa-free-fixes.css` and `icon-helpers.php` existence) to the existing "is the upload complete?" diagnostic. |

### Rejected (legacy version would have been a regression against platform architecture — kept the platform version as-is)
| File | Why rejected |
|---|---|
| `components/packs/kayan-i18n/helpers.php`, `setup.php`, `switcher.php` | The legacy tree's versions predate `kayan-platform`'s Country Router (Phase 2) and implement **country-first** URLs (`/{country}/en/…`). The platform tree's versions correctly delegate to `kayan_platform()->urls->build()` and explicitly no-op when `kayan_platform_owns_routing()` is true, to avoid duplicate rewrite rules/localized-query logic. Adopting the legacy version would have reintroduced a second, conflicting routing scheme. **Kept the platform version unchanged.** |
| `components/packs/schema/setup.php` | The legacy tree's diff made the theme's own schema output the default (printing unless a `kayan_seo_disable` option was set) — the opposite of the platform's already-correct behavior, where `Kayan_Adapter_Schema` (a Phase 3.1 adapter) forces the theme's own schema **off** whenever Rank Math is active, via the existing `validate__schema` kill-switch. **Kept the platform version unchanged** — this is Rank Math remaining the schema authority, per this phase's explicit requirement. |
| `components/packs/FieldsMachine/SetupFields/ThemeOptions/theme__seo.php` | Only additive change was a `kayan_seo_disable` admin toggle field for the now-rejected `kayan-seo` pack (§4). Not added. |
| `components/packs/kayan-stabilization/lockdown.php` | The legacy diff updated a documentation string to describe `kayan-seo` disabling Rank Math's frontend. The platform tree's existing string ("Rank Math injects meta/canonical/OG/schema via `wp_head`… KEEP plugin Active") was **already correct** for the final architecture. No change needed. |

---

## 2. Exact old functionality recovered

- **A site-wide fatal PHP error was recovered from** — see §8. This is
  the single most important thing this phase did.
- Booking wizard injection on single service/post pages (was silently
  broken — see the `kayan-booking/setup.php` row above).
- Header/footer logo rendering across every historical storage format,
  with LiteSpeed/lazy-load-safe attributes.
- AjaxCenter case-sensitive filename matching (several legacy AJAX
  actions were silently 404ing).
- FieldsMachine admin assets no longer load on every wp-admin screen.
- Homepage widget drag-to-reorder now survives adding a new widget.
- Font Awesome 6 Free icon picker with search/preview, replacing a
  near-empty legacy picker.
- Interactive price/package selection + external payment redirect
  (`kayan-price-pay` — see §5).
- Instant (non-scroll-gated) floating call/WhatsApp buttons, plus a
  conditional "Book now" floating button.

## 3. Exact conflicts resolved

The "conflicts" in this integration were never git merge conflicts (the
two trees lived at non-overlapping paths, so a naive merge would have
produced zero conflict markers while still leaving two incompatible
theme copies side by side). The real conflicts were **architectural**:
which of two different implementations of the same responsibility should
win. Every one is listed in §1's "Rejected" table and in §4 below —
routing (platform wins), schema (platform wins), SEO/title output
(platform wins), and the AI-key-masking / duplicate-detection items are
not part of this integration (those belong to the earlier Phase 6 pack,
untouched here).

## 4. Packages retained vs. removed, and why

| Package | Decision | Reason |
|---|---|---|
| `kayan-price-pay` | **Retained** (ported into the canonical tree) | See §5 — genuinely new, non-duplicate functionality; no equivalent exists in `kayan-platform` or elsewhere in the theme. |
| `kayan-seo` | **Removed — not ported.** | Its actual behavior (confirmed by reading all four of its files) is to **disable Rank Math's own frontend output by default** (`kayan-seo/compatibility.php` unhooks `rank_math/head`, `rank_math/json_ld`, both OpenGraph hooks, and `RankMath\Frontend\Head::head` from `wp_head`, active unless an admin explicitly sets `kayan_seo_disable=1`) and print its own title/description/schema instead, reading only the *stored* `rank_math_title`/`rank_math_description` postmeta. This is a direct conflict with this phase's explicit, non-negotiable requirement that **Rank Math remains the SEO authority responsible for title, meta description, canonical, Open Graph, Twitter, sitemap, and schema** — and it duplicates output Rank Math already produces. Documented here rather than silently deleted, per instruction. Nothing of value is lost: the theme's existing, pre-existing `#header/part.php` fallback (prints a manual `<meta description>`/`<title>` **only** when Rank Math is entirely absent) already covers the "no Rank Math installed" case without ever fighting Rank Math when it's active — which is the correct behavior this phase requires. |

No packages were removed from the **platform** tree — this phase did
not touch `kayan-platform` at all (confirmed: `git status` shows zero
changed files under `includes/kayan-platform/` from this phase; the
Phase 6 pack's own commits are untouched).

## 5. `kayan-price-pay` — inspected, integrated (not blindly copied)

- **What it does:** a self-contained pack (`setup.php` + one CSS + one
  JS file) that renders a booking form (`Kayan_Price_Pay::render_form()`)
  and redirects to `Kayan_Price_Pay::PAY_BASE` (a fixed external gateway
  URL) after a customer selects a price/package button and fills in
  name/phone/address/date/time.
- **Does an equivalent already exist?** No. `kayan-booking` (already in
  the platform tree) is a different, in-site multi-step wizard for
  service categories with its own DB tables; `kayan-payment` handles
  actual card/wallet/cash charge processing in-site. `kayan-price-pay`
  is neither — it is a lightweight "pick a listed price, hand off to an
  external payment page" flow used specifically by `#PriceBoxes` cards,
  the `[post_prices]` shortcode, and the Standard price widget. No
  overlap, no duplication.
- **Decision:** integrated as-is (its own code needed no fixes), and
  wired the three price-display surfaces (`#PriceBoxes/part.php`,
  `shortcodes/codes/price_list.php`, `YourColorWidgets/.../price.php`)
  and the footer's conditional "Book now" button to it, matching what
  the legacy tree already did.
- **Known site-specific detail:** `Kayan_Price_Pay::PAY_BASE` is a
  hardcoded URL (`https://rukn-eltatawer-pay.tanceq.com/`) belonging to
  this theme's original client. Documented in `AGENTS.md` so a future
  deployment for a different client knows to update it.

## 6. Adapters created

**None.** No new adapter classes were created in this phase — every
integration point either (a) already had the correct adapter from Phase
3.1 (`Kayan_Adapter_Schema`, the Country Router's own
`kayan_platform_owns_routing()` guard), or (b) needed a direct,
surgical code fix rather than an abstraction layer (e.g., the
`in_the_loop()` bug, the AjaxCenter case-sensitivity bug). This matches
the instruction to use adapters "only when technically necessary."

## 7. Database / options / meta compatibility status

**Fully backward compatible — no schema or data changes in this phase.**

- No new database tables, no changes to existing table schemas.
- No renamed or removed WordPress options, post meta, or term meta keys.
  Every option this phase touches (`phonenumber`, `whatsapp_number`,
  `logo__data`, `hide__theme_seo`, `hide__description_show`,
  `post__price_list__data`, `currency`, etc.) is read with the exact
  same key names as before, in both trees.
- `kayan-price-pay` introduces no new persistent storage of its own — it
  is stateless (reads existing price/service data at render time, posts
  the booking form directly to the customer's browser/external gateway,
  nothing is written to the WordPress database by this pack).
- The Migration Engine (`kayan-platform`) was not touched and required
  no new migration for this phase — nothing here changes any table this
  project owns.

## 8. URL compatibility status

**Fully backward compatible.**

- No rewrite rules were added, removed, or reordered.
- The AjaxCenter fix (§1) makes previously-404ing legacy AJAX action
  URLs work again — a strict improvement, not a URL scheme change.
- Country/Language URL building was explicitly **not** changed (the
  legacy tree's regression was rejected, see §1) — canonical
  language-first URLs (`/en/{country}/…`) continue to work exactly as
  before.
- `home_url('/AjaxCenter/')` casing fix corrects a client-side JS
  variable to match the endpoint's actual registered case
  (`add_rewrite_endpoint('AjaxCenter', EP_ROOT)`); this does not change
  the endpoint itself, only a JS reference to it.

## 9. Rank Math compatibility status

**Rank Math is the sole SEO authority. Verified with a repo-wide search
after this phase's changes:**

- Zero occurrences of `rank_math/frontend/disable`,
  `rank_math/json_ld` + `__return_false`, or
  `remove_all_actions('rank_math/...')` anywhere in the theme (these
  were exclusively inside the now-removed `kayan-seo` pack).
- `Kayan_Adapter_Schema` (kayan-platform, untouched by this phase)
  continues to disable the theme's own JSON-LD schema output whenever
  Rank Math is active, via the pre-existing `validate__schema`
  kill-switch — verified the filter and the schema pack's own gate are
  both still present and wired together.
- `ThemeSeo::Title()` (theme-seo pack) returns immediately whenever
  Rank Math is active OR `title-tag` support is declared (it always is)
  — it cannot print a competing `<title>`.
- The header's meta-description fallback only ever prints when Rank
  Math is **absent** — verified unchanged from the platform tree.
- All of the "protect Rank Math's own assets from being dequeued" fixes
  (§1, `Enqueues/setup.php`) make Rank Math's admin/editor experience
  *more* reliable, not less — they only affect which scripts/styles get
  removed on the **frontend**, and Rank Math's own handles (plus its
  Gutenberg/WP-core dependencies) are now explicitly protected instead
  of relying on a single substring match.

## 10. Test results

Five independent test suites were run against the final, consolidated
`kayan-theme/` tree — **all pass**:

1. **Full-repository PHP syntax lint** (`php -l` on every `.php` file
   under `kayan-theme/`, ~600+ files): **0 errors** (down from 2 fatal
   parse errors before this phase — see §11).
2. **`tests/kayan-smoke.php`** (repo-committed, updated this phase to
   point at the single consolidated theme path and to assert the final
   Rank-Math/kayan-seo/version policy instead of the old one): **62/62
   assertions pass.** Covers: critical file existence, full lint,
   header brace-glitch absence, Rank Math asset protection, booking
   injection fix, icon-catalog integrity (every catalog icon confirmed
   present in the bundled Font Awesome 6 Free CSS), mobile CSS
   breakpoints, `kayan-price-pay` wiring, and the Rank Math /
   `kayan-seo`-removal policy itself.
3. **`kayan-platform` Admin Platform functional suite** (20+ admin
   modules, dashboard, compatibility report, save handlers): pass,
   unaffected (this phase never touched `kayan-platform`).
4. **`kayan-platform` Migration Engine suite** (idempotency, rollback,
   incremental migrations, failure recording): pass, unaffected.
5. **`kayan-platform` PSEO end-to-end suite** (generation, translation,
   queue/scheduler, workflow, quality, dependency graph, Rank Math
   bridge, zero-breaking-changes check on manually-created posts): pass,
   unaffected.

Additional manual verification performed:
- Duplicate-implementation sweep: exactly one `register_post_type()`
  call per CPT, exactly one `cities` taxonomy registration, zero
  `rank_math/frontend/disable`-style filters anywhere, `kayan-i18n`'s
  `kayan_platform_owns_routing()` routing-ownership guards confirmed
  intact and untouched.
- Global function/class duplicate-declaration sweep across the whole
  theme: **zero duplicate class names**; the only duplicate *function*
  names found are either (a) same-named methods on different classes
  (not a real collision — PHP scopes methods per class) or (b)
  pre-existing, already-documented legacy duplication between
  `YC-Scrape` and `export-import` unrelated to this phase's changes.
- Confirmed no dangling `require`/`include` of the removed `kayan-seo`
  pack anywhere in the codebase (the two remaining `kayan-seo`-adjacent
  matches are the pre-existing, unrelated
  `includes/class-kayan-seo-bridge.php` in `kayan-platform`, and two
  filter names in `kayan-i18n` that were already unused before this
  phase — pre-existing, out of scope).

## 11. Critical finding: the pre-existing `kayan-theme/` tree could not boot

Before this phase, `kayan-theme/` (the tree carrying all of
Phases 1–6's work) had **two fatal PHP parse errors**:

1. `components/packs/#header/part.php` — an extra, unmatched closing
   brace made `php -l` fail with `Unmatched '}' in ... on line 123`.
   Every request would have fataled while rendering `<head>`, before any
   page content, admin screen, or REST/AJAX request could execute.
2. `components/packs/@Mega-Menu/Taxonomy-posts.php` — a broken
   `if`/`elseif` structure (duplicated blocks, an `elseif` with no
   matching earlier branch) made `php -l` fail with
   `Unclosed '{' on line 9 ... on line 58`.

Neither was caught by this project's own four functional test suites,
because those suites exercise `kayan-platform`'s PHP classes directly
against a WordPress stub — they never load or render the base theme's
own template files (`#header`, `#footer`, `@Mega-Menu`, etc.). This is a
real, disclosed gap in the previous phases' testing (now partially
closed by `tests/kayan-smoke.php`'s full-repository lint, item 1 in §10)
rather than something this integration phase caused. Both are fixed —
see §1.

## 12. Final theme version

**2.4.1** (`kayan-theme/style.css`, `kayan-theme/readme.txt`).
`2.4.0` → `2.4.1` per this phase's explicit instruction: a
compatibility/integration release after the completed `2.4.0` platform
release, not a new platform version.

## 13. Final platform version

**6.0.0 — unchanged.** No file under
`kayan-theme/components/packs/kayan-platform/` was modified in this
phase (verified via `git status`); the platform's own architecture,
engines, and admin modules are exactly as Phase 6 left them.

## 14. Final ZIP path

`kayan-theme-v2.4.1-ready.zip` (repository root), containing a single
top-level `kayan-theme/` directory with the complete, consolidated
implementation — directly installable via
**Appearance → Themes → Add New → Upload Theme**, or by extracting to
`wp-content/themes/`.

## 15. Git commit / PR

See the branch `cursor/theme-integration-consolidation-39f8` (based on
`main`) for the exact commit(s) covering this integration, and its
associated pull request.

## 16. Confirmation

**`kayan-theme-v2.4.1-ready.zip` is the one canonical, production-ready
package for this project.** `kayan-theme/kayan-theme/` no longer exists
anywhere in the repository (removed after every piece of required
functionality from it was individually inspected and either merged,
surgically integrated, or explicitly documented as rejected). There is
now exactly one theme source in this repository: `kayan-theme/`.
