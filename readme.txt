=== Cordillera ===
Contributors: manuengineer
Tags: one-column, custom-menu, custom-colors, translation-ready, wedding
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 2.6.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bilingual (DE/ES, beliebig erweiterbar) Hochzeits-Onepager-Theme mit RSVP, Unterkunfts-Börse, Galerie und Countdown.

== Description ==

Cordillera ist ein eigenständiges One-Page-Theme (kein Build-Schritt): eine scrollende Startseite
mit Titelbild + Countdown, Geschichte, Programm, Anreise, Unterkunft, Galerie, Geschenke und FAQ —
plus einer gästegeführten Unterkunfts-Börse und einem RSVP-Formular (Zu-/Absage). Alle echten
Inhalte (Namen, Datum, Ort, Texte) werden im WordPress-Backend gepflegt, der Code enthält nur
generische Platzhalter.

**Wichtiger Hinweis zur Weiterverbreitung:** Dieses Theme registriert drei eigene Custom Post
Types (`mym_board` für die Unterkunfts-Börse, `mym_rsvp` für RSVP, `mym_song` für Musikwünsche).
Das ist laut den
WordPress.org-Theme-Richtlinien "Plugin-Territorium" (Datenverlust beim Theme-Wechsel) und daher
nicht für den offiziellen Theme-Verzeichnis-Upload geeignet. Diese `readme.txt` folgt trotzdem dem
WordPress.org-Format als Dokumentationsstandard — das Theme wird aber (vorerst) nur über GitHub
verteilt, nicht über wordpress.org/themes eingereicht.

= Funktionen =

* One-Page-Layout mit drei wählbaren Titelbild-Varianten (Horizont / Editorial / Bogen) und Live-Countdown
* "Horizont"-Titelbild: zweifarbige Bergketten-Silhouette mit unabhängig verschiebbaren Ketten
* Mehrsprachig ohne feste Sprachbindung: funktioniert eigenständig (`?lang=de|es`-Vorschau) oder, empfohlen, mit Polylang für echte übersetzte Permalinks
* Nichts Persönliches hartcodiert: Namen, Ort, Datum kommen aus dem Customizer, Langtexte aus normalen WordPress-Seiten
* Unterkunfts-Börse: Gäste bieten/suchen Unterkunft über ein moderiertes Formular, Kontaktdaten bleiben privat
* RSVP (Zu-/Absage): vollständige Gästeliste pro Anmeldung (Kind, vegetarisch/vegan, Allergien, Sprachen), Bearbeiten ohne Login über einen persönlichen Token-Link, Bestätigungsmail bei jeder Änderung, CSV-Export (eine Zeile pro Gast) fürs Catering/Tischkarten
* Musikwünsche: Gäste reichen beliebig viele Songwünsche (Titel/Interpret, Absender optional) ein, CSV-Export (eine Zeile pro Song) inkl. Spotify-Suchlink, eigener Ein-/Aus-Schalter
* Open-Graph-/Twitter-Card-Meta-Tags und Zum-Kalender-hinzufügen-Links (Google Kalender + .ics)
* Block-Editor-Unterstützung: generische Platzhalter-Blockmuster (u.a. Team/Trauzeugen-Profilkarten, eigenständiger Foto-Slider) und Block-Styles unter der Kategorie "Hochzeit"

== Installation ==

1. Theme-Ordner als ZIP packen.
2. WordPress-Admin → **Design → Themes → Neu hinzufügen → Theme hochladen**, ZIP auswählen, installieren, aktivieren.
3. Eine (leere) Seite anlegen und unter **Einstellungen → Lesen** als statische Startseite setzen — das Theme befüllt sie über `front-page.php`.
4. Namen, Datum und Inhalte unter **Design → Customizer → "Hochzeit: Einstellungen"** sowie über die Abschnittsseiten pflegen.
5. Optional, aber empfohlen: Polylang installieren für DE/ES (oder weitere Sprachen).

Die vollständige Einrichtungsanleitung steht in ANLEITUNG.md (Deutsch).

== Frequently Asked Questions ==

= Brauche ich Polylang? =

Nein. Das Theme funktioniert auch ohne Polylang (Sprachvorschau über `?lang=de|es`), für echte
übersetzte Permalinks und einen Sprachumschalter wird es aber empfohlen.

= Wo werden RSVP-, Börse- und Musikwunsch-Daten gespeichert? =

Als private Custom Post Types (`mym_board`, `mym_rsvp`, `mym_song`) im Admin-Bereich, nirgends
öffentlich sichtbar. RSVP und Musikwünsche haben zusätzlich einen CSV-Export (eine Zeile pro
Gast bzw. pro Song) unter der jeweiligen Beitragsliste.

= Was passiert mit den Gästedaten, wenn ich das Theme wechsle? =

Die Daten bleiben in der Datenbank erhalten, sind aber ohne das Theme (bzw. ohne die
CPT-Registrierung) nicht mehr über die normale WordPress-Oberfläche einsehbar. Vor einem
Theme-Wechsel unbedingt vorher den CSV-Export nutzen.

= Kann ich RSVP, die Unterkunfts-Börse oder die Musikwünsche einzeln deaktivieren? =

Ja, alle drei haben eigene Ein-/Aus-Schalter im Customizer-Panel "Hochzeit: Einstellungen".

== Screenshots ==

1. Startseite mit Bergketten-Titelbild und Countdown (screenshot.png)

== Changelog ==

= 2.6.0 =
* Neues Feature: Musikwünsche — Gäste reichen beliebig viele Songwünsche (Titel/Interpret, Absender optional) über ein eigenes Formular ein
* Admin-Detailansicht und CSV-Export (eine Zeile pro Song) inkl. automatischem Spotify-Suchlink pro Wunsch
* Eigener Ein-/Aus-Schalter und Benachrichtigungs-E-Mail im Customizer-Panel "Hochzeit: Einstellungen"

= 2.5.0 =
* Neue Block-Muster: Team/Trauzeugen-Profilkarten (Foto, Rolle, Aufgabenbereich, Sprachen, Kontakt inkl. Telefon) und ein eigenständiger Foto-Slider, beide frei im Seiteninhalt platzierbar
* Customizer überarbeitet: Startbild-Einstellungen in eigenen Abschnitt "Startbild" verschoben, visueller Titelbild-Varianten-Wähler mit Schema-Vorschau statt Auswahlliste, Bergketten-Regler unter Desktop-/Mobil-Überschriften gruppiert
* Neuer Abschnitt "Seiten-Layout" (Inhaltsbreite, vorher unter "Allgemein")

= 2.4.0 =
* RSVP-Feature: Zu-/Absage mit vollständiger Gästeliste, Token-basiertem Bearbeiten-Link ohne Login, Bestätigungsmail bei jeder Änderung, CSV-Export (eine Zeile pro Gast), Admin-Detailansicht
* Open-Graph-/Twitter-Card-Meta-Tags
* Zum-Kalender-hinzufügen-Links (Google Kalender + .ics) unter dem Countdown
* Fix: Sektions-Hintergrundfarben folgen jetzt durchgängig der Menü-Reihenfolge (FAQ, Geschichte, Programm-Tabelle, Countdown-Sonderfall)
* Fix: "Jetzt zusagen"-Button überdeckt auf Mobilgeräten nicht mehr die Bergkette
* Spanische Texte durchgängig auf chilenisches/lateinamerikanisches Register (ustedes statt vosotros) korrigiert, auch in bestehenden Seiteninhalten (Datenschutz, FAQ, Anreise, Unterkunft)

= 2.1.1 =
* Seitenbreite über einen zentralen Customizer-Regler vereinheitlicht (vorher an 5 Stellen im CSS hartcodiert)
* Fix: Anreise-Seite wieder in voller Breite mit Karte
* Fix: Hauptmenü springt jetzt von jeder Seite aus zurück zur Startseite mit Sprung zum Abschnitt
* Zuverlässige Titelbild-Höhe auf Mobilgeräten (dvh statt vh)
* Namensumbruch im Titelbild nur noch bei Bedarf (Desktop bleibt einzeilig)
* Blocksatz auf Textseiten optional über Zusätzliches CSS statt fest im Theme

= 2.1.0 =
* Bergketten-Regler für Schweiz/Chile getrennt statt gemeinsam
* Bergsilhouette überarbeitet, inkl. feinerer Gipfel-/Grat-Konturlinien

= 2.0.1 =
* Reale Bergketten-Skyline im Titelbild (Berner Oberland + Puerto Montt) statt generischer Formen
* Customizer-Regler für Zentrum-Verschiebung und Höhe/Zoom der Bergkette

= 2.0.0 =
* Menügesteuerter Onepager: Abschnitte und Reihenfolge kommen aus dem primären Navigationsmenü
* Neue Seiten-Templates (Unterkunftsbörse, Foto-Galerie, Anreise & Karte)
* Universelle Mehrsprachigkeit über Polylang, nicht mehr auf DE/ES beschränkt

= 1.2.1 =
* Hotel-Links werden direkt in der WP-Seite gepflegt statt im Customizer
* IP-Rate-Limiting für die Unterkunfts-Börse gegen Spam
* E-Mail-Benachrichtigung bei neuem Börsen-Eintrag
* Responsives Börsen-Formular
* Chilenisches Spanisch: vosotros/vuestra-Formen korrigiert
* FAQ-Akkordeon ohne JavaScript (natives details/summary)

== Upgrade Notice ==

= 2.6.0 =
Neues Musikwünsche-Feature — nach dem Update im Customizer-Panel "Hochzeit: Einstellungen" die
Sektion aktivieren und ggf. eine eigene Seite mit Template "Musikwünsche" anlegen und ins Menü einhängen.

= 2.4.0 =
Neues RSVP-Feature — nach dem Update im Customizer-Panel "Hochzeit: Einstellungen" die
RSVP-Sektion aktivieren und eine Anmeldefrist setzen, falls gewünscht.

== Resources / Credits ==

* Schriften: Cormorant Garamond, Jost — Google Fonts, SIL Open Font License 1.1, https://fonts.google.com/. Werden zur Laufzeit direkt von Google-Servern geladen (einzige erlaubte Ausnahme für externe Ressourcen bei WordPress.org-Themes).
* Keine gebündelten JavaScript-Bibliotheken (kein jQuery, kein Framework) — reines Vanilla-JS in assets/js/main.js.
* `screenshot.png`: eigene Aufnahme/Montage.

== Privacy / Data Collection ==

Dieses Theme bringt zwei Formulare mit, die personenbezogene Daten von Website-Besuchern erheben
und in der WordPress-Datenbank speichern (jeweils als privater Custom Post Type, nirgends
öffentlich angezeigt):

* **Unterkunfts-Börse** (`inc/board.php`): Name, Kontaktdaten (E-Mail und/oder Telefon), Ort,
  Zeitraum, gesprochene Sprachen, Freitext-Notiz. Kontaktdaten sind admin-only und werden nie ans
  Frontend zurückgegeben.
* **RSVP** (`inc/rsvp.php`): Name/Kontaktperson, E-Mail, Telefon, Zu-/Absage, bei Zusage eine
  vollständige Gästeliste (Name, Kind ja/nein, vegetarisch/vegan, Allergien/Wünsche, gesprochene
  Sprachen) sowie eine optionale Nachricht. Ein zufällig generiertes Token erlaubt dem Gast, seine
  eigene Anmeldung später ohne Login zu bearbeiten.

Beide Formulare versenden bei jeder Einreichung E-Mails über `wp_mail()` (Bestätigung an den
Absender bzw. Benachrichtigung an die im Customizer hinterlegte Admin-Adresse). Es werden keine
Analyse- oder Tracking-Tools eingesetzt. Betreiber, die dieses Theme einsetzen, müssen die
Datenerhebung in ihrer eigenen Datenschutzerklärung dokumentieren (siehe Beispieltext in der
mitgelieferten Datenschutzerklärung-Seite).
