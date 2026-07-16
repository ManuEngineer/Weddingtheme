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
  (pattern category `mym-hochzeit`), and it `require`s the `inc/` modules — including
  `inc/customizer-controls.php` (own `WP_Customize_Control` subclasses, must be required *before*
  `inc/customizer.php`) and `inc/customizer.php` itself. Shared template helpers (`mym_edit_btn`,
  `mym_board_entry_html`) are defined here behind `function_exists` guards because they can be
  included from multiple contexts.
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
- **`inc/customizer.php`** — panel "Hochzeit: Einstellungen", split into focused sections rather
  than one large grab-bag: "Brautpaar & Ort", "Allgemein & Datum" (date/time/countdown only),
  "Startbild" (hero variant + all mountain-silhouette sliders, grouped under Desktop/Mobile
  headings via `Mym_Customize_Heading_Control`), "Seiten-Layout" (content width), "Fotos",
  "Unterkunfts-Boerse", "RSVP". The hero variant uses `Mym_Customize_Hero_Variant_Control`
  (`inc/customizer-controls.php`) — a visual picker with inline-SVG schematic previews per
  variant instead of a plain `<select>`, no image assets to maintain. All personal defaults are
  empty.
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
  frontend** — `mym_board_entries()` deliberately omits it. Preserve this when editing. Unlike
  RSVP/Musikwünsche, `mym_board_enabled` deliberately leaves the page/menu item in place when off
  (only the form+entries widget disappears) — a placeholder page content ("coming soon") stays
  reachable, which is the intended difference from the other two modules.
- **Card theming pattern (Börse/RSVP/Musikwünsche) — copy this for any new form/card module.**
  The outer wrapper (`.mym-board`/`.mym-rsvp`/`.mym-songs`) carries **no** background/border/
  padding, just spacing — it sits directly on the section's own alternating background. Only the
  inner form/item cards are visible boxes, and their colors come from a `-dark`/`-light` modifier
  class computed once per render: `$theme = ( $bg === 'mym-bg-forest' ) ? 'mym-X-dark' :
  'mym-X-light';`, added to the outer wrapper (`$bg` is already passed into every section template
  via `$tpl_args` from front-page.php). All colored CSS lives under `.mym-X-dark ...`/
  `.mym-X-light ...` selectors; base classes stay structural-only (layout, no `background`/`color`/
  `border`). Shipping a module with a hardcoded background instead of this split is a repeat
  mistake — check it against both `mym-bg-forest` and `mym-bg-cream` before calling it done.
- **RSVP (`inc/rsvp.php`, `inc/rsvp-ajax.php`, `inc/rsvp-email.php`)** — a private CPT `mym_rsvp`,
  one post per household. Unlike the Börse, **nothing is ever displayed publicly** — no
  moderation step, entries go straight to the admin list. Per-guest data (name, child, veggie,
  allergies, spoken languages) is a plain array in the `mym_rsvp_guests` postmeta (WP handles the
  (de)serialization); `mym_rsvp_sanitize_guest()` is the single place that sanitizes one guest
  row and must be used for any new guest-array write path. Editing without login: each entry gets
  a random 32-hex-char `mym_rsvp_token` (`mym_rsvp_generate_token()`, `random_bytes(16)`); the
  same AJAX action (`mym_rsvp_submit`) creates a new post when `token` is empty/invalid or updates
  the matching one (`mym_rsvp_get_by_token()`) when it's valid — rate-limiting only applies to the
  create path, since a valid token already proves legitimacy. `mym_rsvp_send_guest_confirmation()`
  fires on every create *and* update, always including the token-bearing edit link
  (`mym_rsvp_edit_url()`), specifically so repeat guests don't create duplicate entries out of
  uncertainty ("did it work?"). `page-rsvp.php` renders the *full form* on direct visit (not just
  page content like the other `page-*.php` templates) because the edit link points at the
  standalone page, not the homepage-embedded section. CSV export
  (`admin_post_mym_rsvp_export`) emits one row per **guest**, not per submission — that's the
  point, it's meant to be usable directly for seating/catering. `mym_rsvp_deadline` (Customizer)
  hides the form for new signups past that date but must keep working for edits via an existing
  token — don't let a deadline check block the token path. `mym_rsvp_enabled` off hides the page
  from every nav menu (`mym_rsvp_filter_menu_items()` on `wp_get_nav_menu_items`) and redirects
  direct visits home (`page-rsvp.php`) — **except** when a valid `rsvp_token` is in the URL, same
  reasoning as the deadline: existing guests must keep their edit link working. The hero "Jetzt
  zusagen" CTA slug lookup also respects this toggle, not just `mym_rsvp_cta_enabled`.
- **Musikwünsche (`inc/songs.php`)** — private CPT `mym_song`, one post per submission
  (submitter name optional), holding a list of song wishes (title required, artist optional) as
  a plain array in `mym_song_list` postmeta — same shape as RSVP's per-guest array. Like RSVP,
  **nothing is ever displayed publicly**, no moderation, straight to `publish`. `page-songs.php` +
  `template-parts/section-songs.php` mirror the Börse pair (content-only on direct visit, full
  form embedded on the homepage section) — this is what gives it an independent, menu-orderable
  position like RSVP/Börse, unlike the Team/Slider patterns below. CSV export
  (`admin_post_mym_songs_export`) emits one row per **song**, including a ready-made
  `mym_song_spotify_link()` search URL (`open.spotify.com/search/<urlencoded title+artist>`) —
  a zero-setup search link, not a resolved exact track; a real Spotify Web API integration would
  need the user to register a developer app and manage OAuth credentials, deliberately not built.
  `mym_songs_enabled` (Customizer) is the on/off toggle; `mym_songs_notify` falls back to
  `mym_board_notify` when empty, same pattern as `mym_rsvp_notify`. Off hides the page from every
  nav menu (`mym_songs_filter_menu_items()`) and redirects direct visits home (`page-songs.php`)
  — no token-bypass needed here (no persistent edit-link feature, unlike RSVP).
- **Team/Trauzeugen and Foto-Slider are plain block patterns, not custom post types or page
  templates** — both render via the existing `section-default.php` content path like
  Hotels/Gifts, so a page using either just needs to be added to (or left out of) the primary
  menu like any other content page. `.mym-team`/`.mym-team-member` (functions.php's
  `mym-hochzeit/team` pattern) is a self-contained light card — safe on either alternating
  section background without its own dark-mode variant, same reasoning as `.mym-hotel`/`.mym-gift`.
  `.mym-slider` (`mym-hochzeit/foto-slider` pattern) is a vanilla-JS carousel (no bundled library)
  auto-initialized in `main.js` for every `.mym-slider` found on the page — works standalone
  anywhere in page content, deliberately *not* wired into the hero. `data-autoplay="true"` on the
  wrapper enables autoplay (5s interval); touch-swipe and arrow-key navigation are built in.
- **Frontend JS** (`assets/js/main.js`) is configured via `wp_localize_script` as the global `MYM`
  (ajaxUrl, nonce, rsvpNonce, wedding date/time for the countdown, hero variant, editor flag,
  i18n/rsvpI18n strings). The countdown only runs when a date is set. The RSVP guest list is
  built by cloning a `<template>` element per row (`#mym-rsvp-guest-tpl`); guest data crosses the
  AJAX boundary as one JSON string (`guests` field), not parallel array fields — much less
  error-prone for a dynamically-sized list than index-matched arrays.

`ANLEITUNG.md` is the German end-user setup guide (install, front page, Polylang, Customizer).

## Conventions

- All user-facing strings go through the `mym-hochzeit` text domain (`__()`, `esc_html_e()`, …).
- Escape on output (`esc_html`, `esc_url`, `esc_attr`) and sanitize on input; the existing code
  uses targeted `phpcs:ignore` only where it builds pre-escaped HTML.
- Function/option/meta prefix is `mym_`. Match the surrounding German-commented style.
