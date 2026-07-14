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

- **One-pager, menu-driven (v2.0+):** `front-page.php` requires a static front page to be set in
  WP (Settings → Reading). It reads the **primary nav menu** and renders one section per top-level
  page item, in menu order, via `get_template_part('template-parts/section', …)`. The page's own
  `_wp_page_template` picks the variant: `page-board.php` → `section-board.php` (content +
  Unterkunftsbörse), `page-gallery.php` → `section-gallery.php` (content + gallery CTA), anything
  else (including `page-map.php`) → `section-default.php` (plain content). Each section's DOM
  `id` is the page's own slug (`$page->post_name`), which is what the nav-anchor filter (below)
  jumps to. `header.php`/`footer.php` wrap the page; `single.php`/`page.php`/`404.php` are minimal
  fallbacks for content outside this flow (e.g. Impressum/Datenschutz, using `.mym-page`).
- **Nav-anchor filter** (`wp_nav_menu_objects` in `functions.php`) rewrites primary-menu page
  links to jump to their homepage section from *any* page: `#slug` while already on the front
  page, `home_url('/#slug')` everywhere else — so the header menu always returns to the
  onepager instead of opening the page's own standalone (differently-styled) permalink.
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
- **`inc/content.php`'s `mym_content($lang)` is a v1-compat shim, not part of the live render
  path.** It still returns generic default DE/ES copy (kept for child themes / possible future
  use — see the "v1 compat" comment where it's `require`d), but `front-page.php` doesn't call it;
  section content comes straight from the menu-selected WP pages (see above). Don't assume
  editing it changes anything visible on the site. `inc/sections.php` similarly keeps one unused
  helper (`mym_section_by_page_id`) for the same reason, plus the still-active dashboard
  setup-notice (`mym_sections_admin_notice`).
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
