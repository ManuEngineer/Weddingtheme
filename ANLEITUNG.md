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
  - *Startbild-Variante*: Horizont / Editorial / Bogen
  - *Datums-Auswahl anzeigen* — falls das Datum noch offen ist: mehrere mögliche Tage zur Auswahl zeigen (eines pro Zeile, `JJJJ-MM-TT`). Ausschalten, sobald das Datum fix ist.
- **Startbild „Horizont": Bergketten-Regler** (nur relevant bei dieser Variante)
  - Je Bergkette (Schweiz / Chile) getrennt für Desktop (ab 881px) und Mobil (bis 880px):
    - *verschieben* — horizontale Position. Achtung: Die Schweizer Kette hat rechts vom
      Zentrum kaum noch echte Bilddaten, ihr Regler geht deshalb nur von 0 bis -20% (nach
      links). Die chilenische Kette darf in beide Richtungen bis ±20%.
    - *Höhenlage* — verschiebt die Kette relativ zur anderen nach oben/unten (±15%).
  - *Höhe (%)* — skaliert **beide** Ketten gemeinsam (60-150%). Bewusst kein getrennter
    Zoom pro Kette, sonst stimmt das Grössenverhältnis zwischen den beiden Bergketten
    nicht mehr.
  - Alle Regler wirken sich sofort in der Live-Vorschau des Customizers aus.
- **Foto-Galerie** — Link zu eurem geteilten Album / Upload
- **Fotos** — Startbild- und Geschichte-Foto direkt hochladen
- **Karte & Ort** — Embed-`src`-URL einer OpenStreetMap/Google-Karte. Leer = stilisierte Platzhalter-Karte
- **Hotels (Links)** — optionale Links für die drei Hotel-Karten
- **Unterkunfts-Börse** — An/Aus, Moderation, Benachrichtigungs-E-Mail

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

Jede Sektion der Startseite kann als eigene WordPress-Seite gepflegt werden — Texte, Bilder und Formatierungen bequem im Editor, ohne Code.

**So funktioniert es:**
1. Unter **Seiten → Erstellen** eine neue Seite anlegen.
2. Den passenden **Permalink-Slug** setzen.
3. Veröffentlichen → der Inhalt erscheint sofort auf der Startseite.
4. Solange keine Seite existiert, zeigt das Theme die generischen Platzhaltertexte.

**Slugs für jede Sektion:**

| Sektion         | Slug (Deutsch)    | Slug (Español)   |
|-----------------|-------------------|------------------|
| Geschichte      | `geschichte`      | `historia`       |
| Programm        | `programm`        | `programa`       |
| Anreise         | `anreise`         | `como-llegar`    |
| Übernachtung    | `uebernachtung`   | `alojamiento`    |
| Galerie         | `galerie`         | `galeria`        |
| Geschenke       | `geschenke`       | `regalos`        |
| FAQ             | `faq`             | `faq`            |

Passende **Block-Muster** (Kategorie „Hochzeit") liefern für jede Sektion ein fertiges Gerüst zum Befüllen.

**Mit Polylang:** Die deutsche Seite übersetzen → Polylang zeigt automatisch die richtige Version je Sprache.

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

## 7. Foto-Galerie

Im Customizer den Galerie-Link eintragen → der Button „Zur Galerie & Upload" verlinkt darauf.

---

## 8. Impressum / Datenschutz

Normale WordPress-Seiten anlegen. Für den Footer-Link: **Design → Menüs** → Menü erstellen → der Position **„Footer-Menü"** zuweisen.

---

## 9. Struktur (für Entwickler)

```
cordillera/
├── style.css              Design-Tokens + komplettes CSS
├── functions.php          Setup, Assets, i18n-Helfer, Block-Muster
├── front-page.php         Startseite: Hero, Countdown, Story, Programm
├── header.php / footer.php
├── index.php / page.php / single.php / 404.php
├── inc/
│   ├── content.php        Generische Standardtexte (DE/ES) + Backend-Overlay
│   ├── customizer.php     Customizer-Optionen
│   ├── sections.php       Sektion-Seiten-Lookup
│   └── board.php          Unterkunfts-Börse (CPT + AJAX)
├── template-parts/
│   └── front-rest.php     Anreise, Übernachtung+Börse, Galerie, Geschenke, FAQ
├── assets/
│   ├── css/editor-style.css
│   ├── js/main.js         Countdown, FAQ, Variantenwechsel, Börse
│   └── favicon.svg
└── screenshot.png
```

Farben: Tannengrün `#2F4339`, Creme `#F4EEE2`, Gold `#A9823F`, Terracotta `#BB6244`.
Schriften: Cormorant Garamond (Überschriften), Jost (Text).

> Hinweis: Code-Präfix und Text-Domain lauten weiterhin `mym_` bzw. `mym-hochzeit`
> (interner Namespace) — sie enthalten keine personenbezogenen Daten.
