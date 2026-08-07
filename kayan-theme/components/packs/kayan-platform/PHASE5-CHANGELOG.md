# KAYAN Platform — Phase 5 Changelog

**Theme version:** 2.3.0
**Platform version:** 5.0.0
**Scope:** AI, Workflow & Quality Platform — the final major implementation
phase before the complete project audit (delivered as one milestone).

## Explicitly NOT implemented

- The final project-wide audit (only after the final phase, as instructed)
- Analytics / Performance / Security dashboards (not part of this phase)
- A visual workflow/kanban board UI (Blueprints module gained inline
  actions instead — list/detail, not a new page type)
- Replacing Rank Math or any existing KAYAN system

## AI Platform (`includes/ai/`)

Generic, interchangeable provider architecture — application code never
touches a vendor SDK directly:

- `Kayan_AI_Provider_Interface` — `id()`, `label()`, `is_available()`,
  `capabilities()`, `complete()`, `translate()`
- Real providers: `Kayan_AI_Provider_OpenAI`, `_Claude`, `_Gemini`,
  `_Mistral` (thin `wp_remote_post()` adapters — all orchestration stays
  in `Kayan_AI_Platform`), plus `Kayan_AI_Provider_Null` as the safe
  no-op fallback
- `Kayan_AI_Platform` (facade: `kayan_ai()`) — provider registry, default
  provider resolution (via existing Settings Engine, module scope
  `ai_{provider}` — no new options table), `complete()`, `translate()`,
  `is_any_available()`
- Future providers register via `kayan_ai_register_providers` — zero
  changes to calling code
- **PSEO bridge**: the existing Phase 2.5 `Kayan_PSEO_AI` contract now
  defaults to `Kayan_PSEO_AI_Bridge_Provider`, which maps block
  regeneration requests into `kayan_ai()->complete()` calls and the text
  response back into each block's data shape — the only place PSEO knows
  "AI" exists at all
- New admin module: **AI** (provider status + API key/model configuration)

## Content Workflow (`includes/workflow/`)

`Kayan_Content_Workflow` (facade: `kayan_workflow()`) — every PSEO-managed
post gets an explicit `kayan_workflow_state`:

`draft` → `ai_draft` → `human_review` → `approved` → `scheduled` /
`published`, plus `needs_update`, `needs_regeneration`, `archived`,
`failed`.

- Transitions validated against a fixed, filterable map; every transition
  is recorded in a history log (`kayan_workflow_history`)
- Publishing/scheduling is **quality-gated** unless explicitly forced — a
  failed check routes the page to `human_review` instead of failing silently
- Transitions are the single source of truth for `post_status` — the
  Generator now always writes a fresh/updated post as `draft` first, then
  hands off to the Workflow

## Quality Engine (`includes/quality/`)

`Kayan_Quality_Engine` (facade: `kayan_quality()`) — 18 checks per
generated page: content length, heading structure, duplicate detection,
internal/external links, image ALT coverage, schema source completeness,
dynamic tag resolution, country consistency, language consistency,
blueprint completeness, broken relationships, missing entities, missing
CTA/FAQ/Reviews/Pricing (only when the assigned template requires them),
and SEO completeness. Posts without a PSEO blueprint always pass — zero
impact on existing manually-authored content.

## Dependency Graph (`includes/dependency/`)

`Kayan_Dependency_Graph` (facade: `kayan_dependencies()`) — backed by a
new `kayan_pseo_dependencies` table (via the Migration Engine,
`pseo_dependencies_table_v1`). Every `materialize()` call records which
entities a generated page depends on. `save_post`/`edited_term` hooks
(scoped to post types/taxonomies already in the Programmatic SEO entity
registry — no per-pack wiring) call `mark_affected()`, flagging **only**
the pages that actually depend on the changed entity as
`needs_regeneration` — never a full-site sweep.

## AI Translation

`Kayan_PSEO_Generator::translate_post( $post_id, $target_lang )` —
translates title + all non-locked block text via `kayan_ai()->translate()`,
reuses `materialize()` for the actual write (same fingerprint/workflow/
dependency-graph plumbing — a translation's fingerprint naturally differs
because it includes the language), and links source ↔ translation via the
**existing** `kayan_translation_group` Content Locale meta — no second
linking system. New Queue job type: `translate` (same chunked/resumable
runner as `bulk`/`regenerate`). `Kayan_PSEO_Engine::translate_bulk()` is
the single entry point.

## Safety

- **Manual override**: `kayan_pseo_manual_override` post meta — when set,
  `materialize()` skips (never overwrites) and `regenerate()` refuses
  outright, both without `force`
- **Locked blocks**: never sent for AI regeneration or translation (both
  the bridge provider and `translate_post()` check `locked` before touching
  a block)
- **Confirmation required**: a full-mode `regenerate()` on an
  `approved`/`published` page requires `confirm: true` (or `force`) —
  surfaced as a JS confirm() in the Blueprints admin screen
- A page flagged `needs_regeneration`/`needs_update`/`failed` is never
  silently republished by `regenerate()` — it lands back in
  `human_review` unless `auto_republish` is explicitly requested (and even
  then, still quality-gated)

## Admin Platform

- **AI** module is now functional (was a Phase 3.0 placeholder)
- **Blueprints** module gained: Workflow state column + quick-transition
  buttons, Quality score badge + full report view, per-post Translate
  action (target language dropdown, only shown when AI is configured)
- Dashboard: new `ai` widget; System Health: new AI + migration-aware cards

## Testing

No WordPress/MySQL install is available in this environment. Extended the
existing functional harnesses (real class logic, not just `php -l`) with
~30 new assertions covering: quality-gated publish (and its human-review
downgrade), force-publish override, partial quality pass after filling
specific fields, the Dependency Graph auto-flagging a page when its source
service is saved, safe re-review after a dependency flag (never silent
republish), manual-override protection on both `regenerate()` and
`materialize()`, the full quality report shape, AI provider registration/
interchangeability/graceful-failure-when-unconfigured, a complete
translation flow (via a deterministic fake provider) proving source↔
translation linking and that locked blocks are copied verbatim, and that
PSEO block regeneration always goes through the central AI bridge rather
than a vendor class. All pre-existing Phase 3/4 smoke tests still pass.

## Docs regenerated

New `AIPlatform.md`, `ContentWorkflow.md`, `QualityEngine.md`,
`DependencyGraph.md`; updated `Architecture.md`, `API.md`,
`ProgrammaticSEO.md`, `AdminPlatform.md`, `AdminModules.md`,
`DeveloperGuide.md`, `README.md`.

## Next

Per the approved roadmap, the final project-wide audit (architecture,
performance, security, SEO, code cleanup, duplicate removal, final report)
runs only in the final phase — not performed here.
