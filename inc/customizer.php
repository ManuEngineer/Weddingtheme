<?php
/**
 * Cordillera - Customizer.
 * Einstellbar unter: Design > Customizer > "Hochzeit: Einstellungen".
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function mym_customize_register( $wp_customize ) {

	$wp_customize->add_panel( 'mym_panel', array(
		'title'       => __( 'Hochzeit: Einstellungen', 'mym-hochzeit' ),
		'description' => __( 'Datum, Namen, Fotos und Grundeinstellungen anpassen. Karte, Galerie-Link usw. werden direkt auf der jeweiligen Seite als Block eingefügt.', 'mym-hochzeit' ),
		'priority'    => 5,
	) );

	/* ---- Brautpaar & Ort ---- */
	$wp_customize->add_section( 'mym_couple', array(
		'title' => __( 'Brautpaar & Ort', 'mym-hochzeit' ), 'panel' => 'mym_panel',
		'description' => __( 'Namen, Verbinder und Ort. Für mehrsprachige Versionen die Übersetzungen unter Sprachen → String-Übersetzungen pflegen.', 'mym-hochzeit' ),
	) );
	$wp_customize->add_setting( 'mym_partner_a', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_partner_a', array(
		'label' => __( 'Name 1', 'mym-hochzeit' ), 'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_partner_b', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_partner_b', array(
		'label' => __( 'Name 2', 'mym-hochzeit' ), 'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_logo_text', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_logo_text', array(
		'label' => __( 'Logo-Text (Monogramm)', 'mym-hochzeit' ),
		'description' => __( 'Feste Marke, in allen Sprachen gleich — z.B. „MyM". Leer = Initialen der Namen.', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_connector', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_connector', array(
		'label' => __( 'Verbinder der Namen', 'mym-hochzeit' ),
		'description' => __( 'z.B. & oder und. Leer = Standard „&". Für sprachspezifische Varianten Polylang → String-Übersetzungen verwenden.', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_place', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_place', array(
		'label' => __( 'Ort (Untertitel im Startbild)', 'mym-hochzeit' ),
		'description' => __( 'z.B. „Region · Land". Leer = wird ausgeblendet. Übersetzungen via Polylang → String-Übersetzungen.', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );

	/* ---- Allgemein ---- */
	$wp_customize->add_section( 'mym_general', array(
		'title' => __( 'Allgemein & Datum', 'mym-hochzeit' ), 'panel' => 'mym_panel',
	) );

	$wp_customize->add_setting( 'mym_wedding_date', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_wedding_date', array(
		'label'       => __( 'Hochzeitsdatum', 'mym-hochzeit' ),
		'description' => __( 'Format JJJJ-MM-TT. Erscheint vollständig im Startbild (z.B. „18. September 2027") und steuert den Countdown.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'date',
	) );

	$wp_customize->add_setting( 'mym_wedding_time', array( 'default' => '11:00', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_wedding_time', array(
		'label' => __( 'Uhrzeit (HH:MM)', 'mym-hochzeit' ), 'section' => 'mym_general', 'type' => 'time',
	) );

	$wp_customize->add_setting( 'mym_date_exact', array( 'default' => false, 'sanitize_callback' => 'mym_sanitize_bool' ) );
	$wp_customize->add_control( 'mym_date_exact', array(
		'label'       => __( 'Genaues Datum im Startbild anzeigen', 'mym-hochzeit' ),
		'description' => __( 'Einschalten sobald Tag, Monat und Jahr feststehen. Vorher: nur „September 2027". Nachher: „18. September 2027".', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'checkbox',
	) );

	$wp_customize->add_setting( 'mym_countdown_enabled', array( 'default' => true, 'sanitize_callback' => 'mym_sanitize_bool' ) );
	$wp_customize->add_control( 'mym_countdown_enabled', array(
		'label'       => __( 'Countdown anzeigen', 'mym-hochzeit' ),
		'description' => __( 'Ausschalten sobald der Countdown nicht mehr gebraucht wird — das Datum bleibt weiterhin im Startbild sichtbar.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'checkbox',
	) );

	$wp_customize->add_setting( 'mym_hero_variant', array( 'default' => 'horizont', 'sanitize_callback' => 'mym_sanitize_variant' ) );
	$wp_customize->add_control( 'mym_hero_variant', array(
		'label' => __( 'Startbild-Variante', 'mym-hochzeit' ), 'section' => 'mym_general', 'type' => 'select',
		'choices' => array(
			'horizont'  => __( 'Horizont (Bergsilhouette)', 'mym-hochzeit' ),
			'editorial' => __( 'Editorial (mit Foto)', 'mym-hochzeit' ),
			'bogen'     => __( 'Bogen (Zeremonie-Bogen mit Foto)', 'mym-hochzeit' ),
		),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_schweiz_desktop', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift_schweiz_h', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_schweiz_desktop', array(
		'label'       => __( 'Bergkette Schweiz (Desktop): verschieben', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", ab 881px Breite. Negativ = Kette nach links. Nach rechts ist praktisch kein Spielraum vorhanden (Originalpanorama endet dort), deshalb ist dort bei 0 Schluss.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -20, 'max' => 0, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_chile_desktop', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_chile_desktop', array(
		'label'       => __( 'Bergkette Chile (Desktop): verschieben', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", ab 881px Breite. Positiv = Kette nach rechts, negativ = nach links.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -20, 'max' => 20, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_schweiz_desktop_y', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_schweiz_desktop_y', array(
		'label'       => __( 'Bergkette Schweiz (Desktop): Höhenlage', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", ab 881px Breite. Verschiebt die Schweizer Kette relativ zur chilenischen nach oben (negativ) oder unten (positiv).', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -15, 'max' => 15, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_chile_desktop_y', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_chile_desktop_y', array(
		'label'       => __( 'Bergkette Chile (Desktop): Höhenlage', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", ab 881px Breite. Verschiebt die chilenische Kette relativ zur schweizerischen nach oben (negativ) oder unten (positiv).', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -15, 'max' => 15, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_scale_desktop', array( 'default' => 100, 'sanitize_callback' => 'mym_sanitize_mtn_scale', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_scale_desktop', array(
		'label'       => __( 'Bergketten (Desktop): Höhe (%)', 'mym-hochzeit' ),
		'description' => __( 'Ab 881px Breite. Skaliert Höhe und Breite BEIDER Bergketten gleichzeitig (Grössenverhältnis zwischen den beiden Ketten bleibt korrekt). 100% = Standard.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => 60, 'max' => 150, 'step' => 5 ),
	) );

	$wp_customize->add_setting( 'mym_content_width', array( 'default' => 1040, 'sanitize_callback' => 'mym_sanitize_content_width', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_content_width', array(
		'label'       => __( 'Seiteninhalt: Breite (px)', 'mym-hochzeit' ),
		'description' => __( 'Gilt einheitlich für die ganze Website: Startbild-Sektionen, alle Unterseiten (Anreise, Übernachtung, Galerie, Impressum, Datenschutz usw.). 1040 = Standard. Erlaubt: 480–1400.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'number',
		'input_attrs' => array( 'min' => 480, 'max' => 1400, 'step' => 10 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_schweiz_mobile', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift_schweiz_h', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_schweiz_mobile', array(
		'label'       => __( 'Bergkette Schweiz (Mobil): verschieben', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", bis 880px Breite (Handy/Tablet). Negativ = Kette nach links. Nach rechts ist praktisch kein Spielraum vorhanden (Originalpanorama endet dort), deshalb ist dort bei 0 Schluss.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -20, 'max' => 0, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_chile_mobile', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_chile_mobile', array(
		'label'       => __( 'Bergkette Chile (Mobil): verschieben', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", bis 880px Breite (Handy/Tablet). Positiv = Kette nach rechts, negativ = nach links.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -20, 'max' => 20, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_schweiz_mobile_y', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_schweiz_mobile_y', array(
		'label'       => __( 'Bergkette Schweiz (Mobil): Höhenlage', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", bis 880px Breite (Handy/Tablet). Verschiebt die Schweizer Kette relativ zur chilenischen nach oben (negativ) oder unten (positiv).', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -15, 'max' => 15, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_shift_chile_mobile_y', array( 'default' => 0, 'sanitize_callback' => 'mym_sanitize_mtn_shift', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_shift_chile_mobile_y', array(
		'label'       => __( 'Bergkette Chile (Mobil): Höhenlage', 'mym-hochzeit' ),
		'description' => __( 'Nur bei Startbild-Variante "Horizont", bis 880px Breite (Handy/Tablet). Verschiebt die chilenische Kette relativ zur schweizerischen nach oben (negativ) oder unten (positiv).', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => -15, 'max' => 15, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mym_hero_mtn_scale_mobile', array( 'default' => 100, 'sanitize_callback' => 'mym_sanitize_mtn_scale', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_hero_mtn_scale_mobile', array(
		'label'       => __( 'Bergketten (Mobil): Höhe (%)', 'mym-hochzeit' ),
		'description' => __( 'Bis 880px Breite (Handy/Tablet). Skaliert Höhe und Breite BEIDER Bergketten gleichzeitig (Grössenverhältnis zwischen den beiden Ketten bleibt korrekt). 100% = Standard.', 'mym-hochzeit' ),
		'section'     => 'mym_general', 'type' => 'range',
		'input_attrs' => array( 'min' => 60, 'max' => 150, 'step' => 5 ),
	) );

	$wp_customize->add_setting( 'mym_dates_visible', array( 'default' => true, 'sanitize_callback' => 'mym_sanitize_bool' ) );
	$wp_customize->add_control( 'mym_dates_visible', array(
		'label' => __( 'Datums-Auswahl (3 Samstage) anzeigen', 'mym-hochzeit' ),
		'description' => __( 'Ausschalten, sobald das Datum fix ist.', 'mym-hochzeit' ),
		'section' => 'mym_general', 'type' => 'checkbox',
	) );

	$wp_customize->add_setting( 'mym_dates_list', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'mym_dates_list', array(
		'label' => __( 'Moegliche Daten (eines pro Zeile, JJJJ-MM-TT)', 'mym-hochzeit' ),
		'section' => 'mym_general', 'type' => 'textarea',
	) );

	/* ---- Fotos ---- */
	$wp_customize->add_section( 'mym_photos', array(
		'title' => __( 'Fotos', 'mym-hochzeit' ), 'panel' => 'mym_panel',
		'description' => __( 'Startbild-Foto. Leer = Beitragsbild der Startseite, sonst Platzhalter "Foto folgt". (Bilder in den Sektionen wie Geschichte fuegt ihr direkt auf der jeweiligen Seite ein.)', 'mym-hochzeit' ),
	) );
	$wp_customize->add_setting( 'mym_hero_photo', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mym_hero_photo', array(
		'label'       => __( 'Startbild-Foto', 'mym-hochzeit' ),
		'description' => __( 'Wird bei den Startbild-Varianten "Editorial" und "Bogen" verwendet.', 'mym-hochzeit' ),
		'section'     => 'mym_photos',
	) ) );

	/* ---- Boerse ---- */
	$wp_customize->add_section( 'mym_board', array(
		'title' => __( 'Unterkunfts-Boerse', 'mym-hochzeit' ), 'panel' => 'mym_panel',
	) );
	$wp_customize->add_setting( 'mym_board_enabled', array( 'default' => true, 'sanitize_callback' => 'mym_sanitize_bool' ) );
	$wp_customize->add_control( 'mym_board_enabled', array(
		'label' => __( 'Boerse auf der Seite anzeigen', 'mym-hochzeit' ),
		'description' => __( 'Hauptschalter. Ausschalten, wenn ihr die Boerse (noch) nicht wollt — die Sektion verschwindet dann komplett von der Seite.', 'mym-hochzeit' ),
		'section' => 'mym_board', 'type' => 'checkbox',
	) );
	$wp_customize->add_setting( 'mym_board_moderate', array( 'default' => true, 'sanitize_callback' => 'mym_sanitize_bool' ) );
	$wp_customize->add_control( 'mym_board_moderate', array(
		'label' => __( 'Eintraege erst nach Freigabe anzeigen', 'mym-hochzeit' ),
		'description' => __( 'Empfohlen. Neue Eintraege erscheinen als Entwurf und muessen unter "Boerse" freigegeben werden.', 'mym-hochzeit' ),
		'section' => 'mym_board', 'type' => 'checkbox',
	) );
	$wp_customize->add_setting( 'mym_board_notify', array( 'default' => get_option( 'admin_email' ), 'sanitize_callback' => 'sanitize_email' ) );
	$wp_customize->add_control( 'mym_board_notify', array(
		'label' => __( 'Benachrichtigung an E-Mail', 'mym-hochzeit' ),
		'section' => 'mym_board', 'type' => 'email',
	) );
}
add_action( 'customize_register', 'mym_customize_register' );

function mym_sanitize_variant( $v ) {
	return in_array( $v, array( 'horizont', 'editorial', 'bogen' ), true ) ? $v : 'horizont';
}
function mym_sanitize_bool( $v ) { return (bool) $v; }
function mym_sanitize_mtn_shift( $v ) { return max( -20, min( 20, (int) $v ) ); }
function mym_sanitize_mtn_shift_schweiz_h( $v ) { return max( -20, min( 0, (int) $v ) ); }
function mym_sanitize_mtn_scale( $v ) { return max( 60, min( 150, (int) $v ) ); }
function mym_sanitize_content_width( $v ) { return max( 480, min( 1400, (int) $v ) ); }
