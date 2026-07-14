# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

This repository **is** the WordPress theme `Cordillera` (repo root = theme root; folder slug and
text domain remain `mym-hochzeit`). A bilingual (DE/ES) one-page wedding theme.

## No personal data in this repo

This is a **public** repo intended for publishing. Keep it free of personal data: **no real
names, dates, locations, private URLs, or photos**. The code ships only generic German/Spanish
placeholders. All real values are entered in the WordPress backend:

- Couple names, connector, location, date → **Customizer** ("Hochzeit: Einstellungen").
- Long-form sections (story, programme, travel, gallery, gifts, FAQ) → **WordPress pages** by slug.

When editing, never reintroduce a concrete name/date/place into the code or docs.

## Branches

- **`main` is the only branch we work on.** It is the mature, hand-built theme.
- `copilot/create-wedding-theme-templates` is an older Copilot-generated draft, far less polished.
  Ignore it by default; we may cherry-pick ideas from it in the future, but never treat it as the
  source of truth.

## Deployment / dev workflow

The theme is developed against a self-hosted WordPress (Docker). The live copy is a **clone of
this repo** placed in `wp-content/themes/mym-hochzeit/`, so changes pushed to `main` can be
pulled and tested directly. Workflow: edit here → commit/push → `git pull` in the live theme dir.

There is no build step, package manager, linter, or test suite — plain PHP/CSS/JS loaded directly
by WordPress. `style.css` carries the theme header; `assets/` is served as-is. Bump `MYM_VERSION`
in `functions.php` when changing enqueued CSS/JS so browsers pick up the new files.

Requirements: WordPress 6.0+, PHP 7.4+, internet access for Google Fonts (Cormorant Garamond + Jost).

## Architecture

A bilingual one-pager. Points that span multiple files:

- **One-pager:** `front-page.php` renders every section and requires a static front page to be set
  in WP (Settings → Reading). `header.php`/`footer.php` wrap it; `single.php`/`page.php`/`404.php`
  are minimal fallbacks. `template-parts/front-rest.php` holds the lower sections.
- **`functions.php` is the hub:** theme setup, asset enqueue, block-editor patterns + styles
  (pattern category `mym-hochzeit`), and it `require`s the four `inc/` modules. Shared template
  helpers (`mym_edit_btn`, `mym_board_entry_html`) are defined here behind `function_exists`
  guards because they can be included from multiple contexts.
- **Couple names are backend-driven:** `mym_couple()` reads `mym_partner_a`/`mym_partner_b` from
  the Customizer; if empty it splits the **site title** on a connector (`&`/`y`/`und`/`+`).
  `mym_monogram()` builds the header/footer logo from the initials. No name is hardcoded.
- **i18n is custom, not just gettext.** The theme works **with or without Polylang**:
  - `mym_opt($key, $default)` — reads a Customizer mod, then runs it through Polylang's `pll__()`
    when available.
  - `mym_preview_lang()` / `mym_current_lang()` — resolve the active language from Polylang,
    falling back to `?lang=de|es` for preview when Polylang isn't installed.
- **Content has a two-tier fallback** (read both to know where on-page text comes from):
  - `inc/content.php` — `mym_content($lang)` returns the **generic** default DE/ES copy as a nested
    array, then overlays the Customizer couple/place/connector values and a derived footer line.
  - `inc/sections.php` — `mym_section_page($slug_de, $slug_es)` looks up an editable WP page by slug
    and, if it exists, its rendered content overrides the default; otherwise the default wins.
    DE→ES slug map: `geschichte→historia`, `programm→programa`, `anreise→como-llegar`,
    `uebernachtung→alojamiento`, `galerie→galeria`, `geschenke→regalos`, `faq→faq`.
- **`inc/customizer.php`** — panel "Hochzeit: Einstellungen": couple names + connector + place,
  wedding date/time, hero variant (`horizont`/`editorial`/`bogen`), optional candidate dates,
  gallery link, photos, map embed, hotel links, Unterkunfts-Börse moderation/notify. All personal
  defaults are empty.
- **Hero "Horizont" mountain silhouette** (`assets/svg/hero-mountains.svg`) — a two-tone SVG with
  two groups, `<g class="mym-mtn-schweiz">` and `<g class="mym-mtn-chile">`, each holding a filled
  base path plus several unfilled ridge-contour stroke paths for texture. The geometry was
  one-off generated from two real 90°-panorama line-art exports (no build step regenerates it in
  this repo), manually overlaid/cropped around a fixed anchor point, then color-styled to match
  the site palette. `functions.php`'s `mtn_pct_to_vbu` converts the Customizer's percentage
  shift/scale settings into viewBox units (`$mtn_vb_width` must match the SVG's actual `viewBox`
  width) and injects them as CSS custom properties (`--mym-mtn-shift-*`, `--mym-mtn-scale`)
  consumed by `style.css`'s `.mym-mtn-schweiz`/`.mym-mtn-chile` transforms. The Schweiz range has
  almost no real image data to its right past the crop window, so its horizontal shift is
  deliberately capped to 0..-20% via `mym_sanitize_mtn_shift_schweiz_h` (Chile is safe across the
  full ±20%) — don't widen that range without re-checking the source data's real extent. If the
  SVG geometry is ever regenerated, extend the fill/line paths beyond the visible crop window on
  each color's own real data (not just the shared window) so the shift sliders don't reveal blank
  edges.
- **Unterkunfts-Börse (`inc/board.php`)** — guest accommodation exchange: a private CPT
  `mym_board` (offer/seek) with admin columns, a meta box, "mark as placed" + duplicate row
  actions, and email notification. Public submissions arrive via AJAX
  (`wp_ajax[_nopriv]_mym_board_submit`) with nonce + honeypot; entries are saved as `draft` when
  moderation is on. **The `mym_contact` (email/phone) field is admin-only and must never reach the
  frontend** — `mym_board_entries()` deliberately omits it. Preserve this when editing.
- **Frontend JS** (`assets/js/main.js`) is configured via `wp_localize_script` as the global `MYM`
  (ajaxUrl, nonce, wedding date/time for the countdown, hero variant, editor flag, i18n strings).
  The countdown only runs when a date is set.

`ANLEITUNG.md` is the German end-user setup guide (install, front page, Polylang, Customizer).

## Conventions

- All user-facing strings go through the `mym-hochzeit` text domain (`__()`, `esc_html_e()`, …).
- Escape on output (`esc_html`, `esc_url`, `esc_attr`) and sanitize on input; the existing code
  uses targeted `phpcs:ignore` only where it builds pre-escaped HTML.
- Function/option/meta prefix is `mym_`. Match the surrounding German-commented style.
