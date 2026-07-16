# Cordillera — WordPress-Theme

Zweisprachiges (Deutsch / Español) Hochzeits-Onepager-Theme. Alle Inhalte —
Namen, Datum, Ort, Texte — werden im Backend gepflegt; im Code stehen nur
generische Platzhalter.

---

## 1. Installation

1. Theme als ZIP packen (oder Repo-Ordner verwenden).
2. Im WordPress-Adminbereich: **Design → Themes → Theme hinzufügen → Theme hochladen**.
3. ZIP auswählen, **Jetzt installieren**, dann **Aktivieren**.

> Voraussetzungen: WordPress 6.0+, PHP 7.4+. Internetzugang für Google Fonts (Cormorant Garamond + Jost).

---

## 2. Startseite einrichten (wichtig!)

Damit der Hochzeits-Onepager als Startseite erscheint:

1. **Seiten → Erstellen**: eine leere Seite anlegen, z.B. „Start" (Inhalt kann leer bleiben — das Theme füllt sie über `front-page.php`).
2. **Einstellungen → Lesen → „Deine Homepage zeigt"** auf **„Eine statische Seite"** stellen und als *Homepage* die eben erstellte Seite wählen.
3. Optional: ein **Beitragsbild** auf dieser Seite setzen — es wird im Startbild (Varianten „Editorial" & „Bogen") und in der Sektion „Geschichte" als Foto verwendet.

---

## 3. Brautpaar, Datum & Co. — Customizer

**Design → Customizer → „Hochzeit: Einstellungen"**

- **Brautpaar & Ort**
  - *Name 1* / *Name 2* — die Namen des Paares (steuern Startbild, Signatur und Logo-Monogramm). Leer = der Seitentitel wird verwendet (an `&`/`y`/`und` zerlegt).
  - *Verbinder* — Zeichen zwischen den Namen (`&`, `y`, `und`). Leer = Sprach-Standard.
  - *Ort* — Untertitel im Startbild. Leer = ausgeblendet.
- **Allgemein & Datum**
  - *Hochzeitsdatum* (für den Countdown), Uhrzeit
  - *Datums-Auswahl anzeigen* — falls das Datum noch offen ist: mehrere mögliche Tage zur Auswahl zeigen (eines pro Zeile, `JJJJ-MM-TT`). Ausschalten, sobald das Datum fix ist.
- **Startbild**
  - *Startbild-Variante*: Horizont / Editorial / Bogen — Auswahl mit kleiner Vorschau je Variante.
  - Nur bei „Horizont": Bergketten-Regler, je Kette (Schweiz / Chile) getrennt für Desktop (ab 881px) und Mobil (bis 880px), unter eigenen Zwischenüberschriften gruppiert:
    - *verschieben* — horizontale Position. Achtung: Die Schweizer Kette hat rechts vom
      Zentrum kaum noch echte Bilddaten, ihr Regler geht deshalb nur von 0 bis -20% (nach
      links). Die chilenische Kette darf in beide Richtungen bis ±20%.
    - *Höhenlage* — verschiebt die Kette relativ zur anderen nach oben/unten (±15%).
    - *Höhe (%)* — skaliert **beide** Ketten gemeinsam (60-150%). Bewusst kein getrennter
      Zoom pro Kette, sonst stimmt das Grössenverhältnis zwischen den beiden Bergketten
      nicht mehr.
  - Alle Regler wirken sich sofort in der Live-Vorschau des Customizers aus.
- **Seiten-Layout**
  - *Seiteninhalt: Breite (px)* — EIN Wert (Standard 1040px, erlaubt 480–1400) für die
    gesamte Website: Startbild-Sektionen und alle Unterseiten (Anreise, Übernachtung, Galerie,
    Impressum, Datenschutz usw.) übernehmen ihn einheitlich. Kein Regler, sondern ein Zahlenfeld
    zum exakten Eintippen.
- **Fotos** — Startbild-Foto direkt hochladen
- **Unterkunfts-Börse** — An/Aus, Moderation, Benachrichtigungs-E-Mail
- **Musikwünsche** — An/Aus, Benachrichtigungs-E-Mail

> Tipp: Das Startbild lässt sich live testen, indem man als eingeloggter Nutzer unten im Startbild die Variante umschaltet (nur für euch sichtbar). Dauerhaft einstellen im Customizer.

---

## 4. Sprache & Übersetzung (Polylang)

Generische Standardtexte (DE + ES) sind im Theme hinterlegt und erscheinen je nach Sprache.

**Mit Polylang:**
1. Plugin **Polylang** installieren & aktivieren.
2. Sprachen anlegen: **Deutsch (de)** und **Español (es)**.
3. Domains/Slugs nach Wunsch konfigurieren. Der Sprachumschalter oben rechts nutzt automatisch Polylang.
4. Menüs: unter **Design → Menüs** je ein Menü pro Sprache der Position „Hauptmenü" zuweisen (optional).

Einzelne Customizer-Texte lassen sich über **Sprachen → Zeichenketten-Übersetzungen** sprachabhängig übersetzen.

> Ohne Polylang funktioniert die Seite ebenfalls — der Umschalter wechselt dann per `?lang=de` / `?lang=es` (nur für die Vorschau).

---

## 5. Sektionen als WordPress-Seiten bearbeiten

Jede Sektion der Startseite ist schlicht eine normale WordPress-Seite, die im **primären Menü**
steht — Reihenfolge im Menü = Reihenfolge auf der Startseite. Kein fester Slug nötig, keine
generischen Platzhaltertexte im Hintergrund: **ohne Menüeintrag erscheint schlicht keine
Sektion.**

**So funktioniert es:**
1. Unter **Seiten → Erstellen** eine neue Seite anlegen, Inhalt bequem im Editor gestalten.
2. Veröffentlichen.
3. Unter **Design → Menüs** die Seite zum **primären Menü** hinzufügen, an der gewünschten
   Position → erscheint sofort als Sektion auf der Startseite (Hintergrund wechselt automatisch
   zwischen zwei Farbtönen je nach Position).
4. Für die Unterkunftsbörse oder die Foto-Galerie zusätzlich das passende **Seiten-Template**
   setzen (siehe Abschnitte 6 und 7) — sonst wird die Seite als reiner Inhalt angezeigt.

Übliche Slugs (nur zur eigenen Orientierung, technisch nicht zwingend):

| Sektion         | Slug (Deutsch)    | Slug (Español)   |
|-----------------|-------------------|------------------|
| Geschichte      | `geschichte`      | `historia`       |
| Programm        | `programm`        | `programa`       |
| Anreise         | `anreise`         | `como-llegar`    |
| Übernachtung    | `uebernachtung`   | `alojamiento`    |
| Galerie         | `galerie`         | `galeria`        |
| Geschenke       | `geschenke`       | `regalos`        |
| FAQ             | `faq`             | `faq`            |

Passende **Block-Muster** (Kategorie „Hochzeit") liefern für jede Sektion ein fertiges Gerüst zum Befüllen — u.a. **Team / Trauzeugen** (Profilkarten mit Foto, Rolle, Aufgabenbereich, Sprachen, Kontakt inkl. Telefon) und **Foto-Slider** (eigenständiges Bildkarussell), beide frei auf jeder beliebigen Seite einsetzbar, unabhängig von einem Menüeintrag.

**Mit Polylang:** je ein eigenes primäres Menü pro Sprache pflegen (**Design → Menüs**), jedes mit
den übersetzten Seiten in gleicher Reihenfolge.

**Das Hauptmenü führt von überall zurück zur Startseite:** Ein Klick auf einen Menüpunkt springt
zur passenden Sektion — egal von welcher Unterseite aus (z. B. auch von Impressum/Datenschutz).

> Als eingeloggter Nutzer erscheint unter jeder Sektion ein kleiner „✏ Seite bearbeiten"-Link direkt auf der Startseite — nur für euch sichtbar.

---

## 6. Unterkunfts-Börse

Gäste tragen sich direkt auf der Seite ein („Wir bieten Unterkunft" / „Wir suchen Unterkunft").

- Neue Einträge erscheinen unter **Unterkunfts-Börse** (linkes WordPress-Menü).
- Bei aktiver **Moderation** (Standard) sind sie zuerst *Entwurf* — per **Veröffentlichen** freigeben.
- Bei jeder Einreichung geht eine E-Mail an die im Customizer hinterlegte Adresse.
- Das Kontaktfeld ist **privat** und erscheint nie öffentlich.
- Spamschutz via Honeypot ist eingebaut.
- **Teil-Vermittlung:** in der Eintrags-Liste auf **„Duplizieren"** → Original = vergebener Zeitraum, Kopie = Restzeitraum.

---

## 7. RSVP (Zu-/Absage)

Genau wie bei der Unterkunfts-Börse: eine WordPress-Seite anlegen, Seiten-Template
**„RSVP (Zu-/Absage)"** setzen, zum primären Menü hinzufügen.

- Gäste geben Kontaktdaten, Zu-/Absage und (bei Zusage) eine Gästeliste an — pro Person Name,
  Kind ja/nein, vegetarisch/vegan ja/nein, Allergien/Wünsche, gesprochene Sprachen.
- Neue Anmeldungen erscheinen unter **RSVP** (linkes WordPress-Menü). **Keine
  Freigabe/Moderation nötig** — anders als bei der Börse werden Anmeldungen nirgends öffentlich
  angezeigt, sie landen direkt vollständig im Backend.
- Oben in der RSVP-Liste: **Zusammenfassung** (Zusagen/Absagen, Erwachsene, Kinder, Vegi-Anzahl)
  sowie ein Button für den **CSV-Export** — eine Zeile pro Gast, direkt nutzbar für
  Tischkarten/Catering.
- Bei jeder Anmeldung **und** jeder späteren Änderung erhält der Gast eine Bestätigungsmail mit
  der vollständigen Zusammenfassung seiner Angaben **und einem persönlichen Link**, über den er
  seine Anmeldung jederzeit selbst ändern kann (keine erneute Anmeldung nötig — vermeidet
  Duplikate). Ihr bekommt bei jeder Anmeldung/Änderung ebenfalls eine Benachrichtigung.
- **Anmeldefrist** (Customizer, Format JJJJ-MM-TT): Nach diesem Datum verschwindet das Formular
  für *neue* Anmeldungen und zeigt einen Hinweistext. Bereits angemeldete Gäste können über
  ihren persönlichen Link **weiterhin** ändern.
- Zwei unabhängige Ein-/Ausschalter im Customizer: das Formular selbst, und separat der
  auffällige **„Jetzt zusagen"-Button** im Startbild (springt zur RSVP-Sektion).
- Schaltet ihr das Formular selbst aus, verschwindet die Seite komplett aus Menü und Startseite
  (wie bei Musikwünsche) — **nur** der persönliche Änderungslink bereits angemeldeter Gäste
  funktioniert weiterhin, damit niemand seine Anmeldung nicht mehr korrigieren kann.
- Kontaktdaten (E-Mail/Telefon) sind **privat** — wie beim Kontaktfeld der Börse nie öffentlich
  sichtbar.
- Spamschutz via Honeypot + IP-Rate-Limit (nur bei Neuanmeldungen, nicht bei Änderungen über den
  persönlichen Link).

---

## 8. Musikwünsche

Genau wie bei RSVP: eine WordPress-Seite anlegen, Seiten-Template **„Musikwünsche"** setzen, zum
primären Menü hinzufügen — Position im Menü bestimmt, wo die Sektion auf der Startseite erscheint.

- Gäste tragen ihren Namen (**optional**) sowie beliebig viele Songwünsche ein (Titel/Interpret
  über „+ weiteren Song hinzufügen").
- Neue Einreichungen erscheinen unter **Musikwünsche** (linkes WordPress-Menü). **Keine
  Freigabe/Moderation nötig** — wie bei RSVP werden Einreichungen nirgends öffentlich angezeigt.
- Oben in der Liste: **Zusammenfassung** (Anzahl Einreichungen/Songs) sowie ein Button für den
  **CSV-Export** — eine Zeile pro Song, inklusive einem fertigen **Spotify-Suchlink** pro Wunsch
  (öffnet die Trefferliste für Titel + Interpret, kein exakter Treffer garantiert, aber ein Klick
  entfernt — kein Spotify-Konto/API-Key nötig).
- Bei jeder Einreichung geht eine E-Mail an die im Customizer hinterlegte Adresse (leer = dieselbe
  wie bei der Unterkunfts-Börse).
- Spamschutz via Honeypot + IP-Rate-Limit.

---

## 9. Foto-Galerie

Im Customizer den Galerie-Link eintragen → der Button „Zur Galerie & Upload" verlinkt darauf.

---

## 10. Impressum / Datenschutz

Normale WordPress-Seiten anlegen. Für den Footer-Link: **Design → Menüs** → Menü erstellen → der Position **„Footer-Menü"** zuweisen.

---

## 11. Struktur (für Entwickler)

```
cordillera/
├── style.css              Design-Tokens + komplettes CSS
├── functions.php          Setup, Assets, i18n-Helfer, Block-Muster, Nav-Anker-Filter
├── front-page.php         Startseite: Hero, Countdown + Sektionen aus dem primären Menü
├── page-map.php           Seiten-Template "Anreise & Karte" (breiter Rahmen)
├── page-board.php         Seiten-Template "Unterkunftsbörse"
├── page-gallery.php       Seiten-Template "Foto-Galerie"
├── page-rsvp.php          Seiten-Template "RSVP (Zu-/Absage)"
├── page-songs.php         Seiten-Template "Musikwünsche"
├── header.php / footer.php
├── index.php / page.php / single.php / 404.php   (generische Seiten, z.B. Impressum/Datenschutz)
├── inc/
│   ├── content.php        v1-Kompatibilität, im aktuellen Rendering nicht mehr aktiv genutzt
│   ├── customizer.php     Customizer-Optionen
│   ├── customizer-controls.php  Eigene WP_Customize_Control-Klassen (Startbild-Wähler, Überschriften)
│   ├── sections.php       Dashboard-Einrichtungshinweis + ungenutzter Erweiterungs-Helfer
│   ├── board.php          Unterkunfts-Börse (CPT + AJAX)
│   ├── rsvp.php           RSVP: CPT, Admin-Übersicht, CSV-Export
│   ├── rsvp-ajax.php      RSVP: AJAX Neuanmeldung/Änderung via Token
│   ├── rsvp-email.php     RSVP: Bestätigungs-/Benachrichtigungs-E-Mails
│   └── songs.php          Musikwünsche: CPT, AJAX, Admin-Übersicht, CSV-Export, Spotify-Link
├── template-parts/
│   ├── section-default.php   Sektion: reiner Seiteninhalt
│   ├── section-board.php     Sektion: Seiteninhalt + Unterkunftsbörse
│   ├── section-rsvp.php      Sektion: Seiteninhalt + RSVP-Formular
│   ├── section-songs.php     Sektion: Seiteninhalt + Musikwünsche-Formular
│   └── section-gallery.php   Sektion: Seiteninhalt + Galerie-CTA
├── assets/
│   ├── svg/hero-mountains.svg
│   ├── css/editor-style.css
│   ├── js/main.js         Countdown, FAQ, Variantenwechsel, Börse, RSVP, Musikwünsche, Foto-Slider
│   └── favicon.svg
└── screenshot.png
```

Farben: Tannengrün `#2F4339`, Creme `#F4EEE2`, Gold `#A9823F`, Terracotta `#BB6244`.
Schriften: Cormorant Garamond (Überschriften), Jost (Text).

> Hinweis: Code-Präfix und Text-Domain lauten weiterhin `mym_` bzw. `mym-hochzeit`
> (interner Namespace) — sie enthalten keine personenbezogenen Daten.
