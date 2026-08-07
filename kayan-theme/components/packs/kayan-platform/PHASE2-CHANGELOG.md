# KAYAN Platform — Phase 2 Changelog

**Theme version:** 1.6.0  
**Platform version:** 2.0.0  
**Scope:** Country Routing Engine + Content Resolution Engine only.

## Architectural updates (approved before Phase 2)

1. Canonical URL mode is **language-first**: `/en/sa/…`
2. Legacy `/{country}/en/…` kept only as **301 redirects**
3. Hreflang data model supports **all public content types** (via Rank Math filters / data API)
4. Content model: shared + country variants + language translations (no forced duplication)
5. Programmatic SEO entity registry is first-class
6. **Rank Math is the only SEO engine** — KAYAN extends, never competes

## Added

| File | Purpose |
|------|---------|
| `includes/class-kayan-country-router.php` | Language-first rewrites for homes, CPT archives/singulars, taxonomies; legacy 301s; rewrite flush `2.0.0` |
| `includes/class-kayan-content-resolver.php` | Locale-aware singular/archive resolution (shared / variant / translation scoring) |
| `includes/class-kayan-programmatic-seo.php` | Entity registry (country, city, area, district, service, category, faq, landmark, pricing, …) |
| `PHASE2-CHANGELOG.md` | This file |

## Modified

| File | Why |
|------|-----|
| `setup.php` | Boot Phase 2 services; platform version `2.0.0` |
| `includes/class-kayan-platform.php` | Wire router, resolver, programmatic SEO |
| `includes/class-kayan-url.php` | Canonical builder = language-first |
| `includes/class-kayan-seo-bridge.php` | Remove competing head SEO tags; Rank Math filters only |
| `includes/helpers.php` | `kayan_platform_owns_routing()` |
| `includes/class-kayan-content-locale.php` | Docs aligned with resolver |
| `kayan-i18n/setup.php` | Gate legacy rewrites/resolver when platform owns routing (no duplication) |
| `kayan-i18n/helpers.php` | Language-first path detection; `build_url` delegates to platform |
| `kayan-i18n/switcher.php` | Detect/build canonical `/en/{country}/` URLs (routing correctness, not UI redesign) |
| `style.css` / `readme.txt` | Version `1.6.0` |

## Explicitly NOT changed

- Frontend visual templates / CSS design
- Admin / Theme Options UI
- Data migration of posts/meta
- Competing theme schema / sitemaps / breadcrumbs / meta tags
- Programmatic page generation (registry only)

## Rewrite ownership

- **Single owner:** `Kayan_Country_Router` when `kayan_platform_owns_routing()` is true
- **kayan-i18n** rewrite + `pre_get_posts` resolver are skipped (no duplicate rules/hooks)

## URL matrix (canonical)

```
/                         ae + ar
/sa/                      sa + ar
/en/                      ae + en
/en/sa/                   sa + en
/services/{slug}/         ae + ar + services
/sa/services/{slug}/      sa + ar + services
/en/services/{slug}/      ae + en + services
/en/sa/services/{slug}/   sa + en + services
```

Legacy:

```
/sa/en/…  →  301  →  /en/sa/…
```
