# Cordillera — WordPress Theme

A bilingual (Deutsch / Español) one-page wedding theme.

A self-contained classic theme (no build step): a single scrolling front page with hero +
countdown, story, day programme, travel, accommodation, gallery, gifts and an FAQ — plus a
guest-run accommodation exchange ("Unterkunfts-Börse"). All real content — couple names, date,
location, texts — is set in the WordPress backend; the code ships only generic placeholders.

## Features

- **One-page layout** driven by `front-page.php`, with three selectable hero variants
  (Horizont / Editorial / Bogen) and a live countdown to the wedding.
- **Bilingual DE/ES.** Works standalone (`?lang=de|es` preview) or, recommended, with
  **Polylang** for real translated permalinks and a language switcher.
- **Nothing personal hardcoded.** Names, connector, location and date come from the Customizer
  (with the site title as a fallback); long-form sections come from normal WordPress pages.
- **Editable content two ways:** generic defaults out of the box, optionally overridden
  per-section by WordPress pages (Gutenberg) — matched by slug.
- **Customizer panel "Hochzeit: Einstellungen":** couple names + place, date/time, hero variant,
  candidate dates, gallery link, map embed, hotel links, board moderation.
- **Unterkunfts-Börse:** guests can offer or request a place to stay via a moderated form;
  contact details stay private (admin-only).
- **Block editor support:** generic placeholder block patterns and block styles under the
  "Hochzeit" category.

## Requirements

- WordPress 6.0+, PHP 7.4+
- Internet access for Google Fonts (Cormorant Garamond + Jost)
- Optional but recommended: [Polylang](https://wordpress.org/plugins/polylang/) for DE/ES

## Installation

1. Zip the theme folder.
2. WordPress admin → **Appearance → Themes → Add New → Upload Theme**, choose the zip, install,
   activate.
3. Create an (empty) page and set it as the static homepage under **Settings → Reading** — the
   theme fills it via `front-page.php`.
4. Set names, date and content under **Appearance → Customizer → "Hochzeit: Einstellungen"** and
   via section pages.

The full setup guide is in **[ANLEITUNG.md](ANLEITUNG.md)** (German).

## Development

Plain PHP/CSS/JS, no build tooling. See **[CLAUDE.md](CLAUDE.md)** for architecture and the
edit/deploy workflow. Bump `MYM_VERSION` in `functions.php` when changing enqueued CSS/JS.

The code prefix and text domain remain `mym_` / `mym-hochzeit` (internal namespace; no personal
data). `main` is the maintained branch; the `copilot/*` branch is an early draft kept only for
reference.
