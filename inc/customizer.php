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
		'description' => __( 'Datum, Karte, Galerie-Link und Hotels anpassen.', 'mym-hochzeit' ),
		'priority'    => 5,
	) );

	/* ---- Brautpaar & Ort ---- */
	$wp_customize->add_section( 'mym_couple', array(
		'title' => __( 'Brautpaar & Ort', 'mym-hochzeit' ), 'panel' => 'mym_panel',
		'description' => __( 'Namen, Verbinder und Ort. Leer = der Seitentitel wird verwendet.', 'mym-hochzeit' ),
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
		'label' => __( 'Verbinder der Namen — Deutsch', 'mym-hochzeit' ),
		'description' => __( 'Zwischen den Namen, z.B. & oder und. Leer = Standard „&".', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_connector_es', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_connector_es', array(
		'label' => __( 'Verbinder der Namen — Español', 'mym-hochzeit' ),
		'description' => __( 'z.B. y. Leer = Standard „y".', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_place', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_place', array(
		'label' => __( 'Ort — Deutsch (Untertitel im Startbild)', 'mym-hochzeit' ),
		'description' => __( 'z.B. „Region · Land". Leer = wird ausgeblendet. Für eine andere Sprache das Feld darunter nutzen.', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );
	$wp_customize->add_setting( 'mym_place_es', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_place_es', array(
		'label' => __( 'Ort — Español', 'mym-hochzeit' ),
		'description' => __( 'Wird auf der spanischen Seite angezeigt. Leer = es gilt der deutsche Ort.', 'mym-hochzeit' ),
		'section' => 'mym_couple', 'type' => 'text',
	) );

	/* ---- Allgemein ---- */
	$wp_customize->add_section( 'mym_general', array(
		'title' => __( 'Allgemein & Datum', 'mym-hochzeit' ), 'panel' => 'mym_panel',
	) );

	$wp_customize->add_setting( 'mym_wedding_date', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'mym_wedding_date', array(
		'label' => __( 'Hochzeitsdatum (fuer Countdown)', 'mym-hochzeit' ),
		'description' => __( 'Format JJJJ-MM-TT', 'mym-hochzeit' ),
		'section' => 'mym_general', 'type' => 'date',
	) );

	$wp_customize->add_setting( 'mym_wedding_time', array( 'default' => '11:00', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mym_wedding_time', array(
		'label' => __( 'Uhrzeit (HH:MM)', 'mym-hochzeit' ), 'section' => 'mym_general', 'type' => 'time',
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

	/* ---- Foto-Galerie (Immich) ---- */
	$wp_customize->add_section( 'mym_gallery', array(
		'title' => __( 'Foto-Galerie (Immich)', 'mym-hochzeit' ), 'panel' => 'mym_panel',
	) );
	$wp_customize->add_setting( 'mym_immich_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'mym_immich_url', array(
		'label' => __( 'Immich-Galerie-Link', 'mym-hochzeit' ),
		'description' => __( 'Link zum geteilten Album / Upload (z.B. https://example.com/share/...)', 'mym-hochzeit' ),
		'section' => 'mym_gallery', 'type' => 'url',
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

	/* ---- Karte / Anreise ---- */
	$wp_customize->add_section( 'mym_map', array(
		'title' => __( 'Karte & Ort', 'mym-hochzeit' ), 'panel' => 'mym_panel',
	) );
	$wp_customize->add_setting( 'mym_map_embed', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'mym_map_embed', array(
		'label' => __( 'Karten-Embed-URL (OpenStreetMap/Google "src")', 'mym-hochzeit' ),
		'description' => __( 'Nur die src-URL des iframes einfuegen. Leer = stilisierte Platzhalter-Karte.', 'mym-hochzeit' ),
		'section' => 'mym_map', 'type' => 'url',
	) );

	/* ---- Hotels (3 Karten) ---- */
	$wp_customize->add_section( 'mym_hotels', array(
		'title' => __( 'Hotels (Links)', 'mym-hochzeit' ), 'panel' => 'mym_panel',
		'description' => __( 'Optionale Links fuer die drei Hotel-Karten. Texte stammen aus der Sprachdatei.', 'mym-hochzeit' ),
	) );
	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( "mym_hotel{$i}_url", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "mym_hotel{$i}_url", array(
			/* translators: %d hotel number */
			'label' => sprintf( __( 'Hotel %d — Link', 'mym-hochzeit' ), $i ),
			'section' => 'mym_hotels', 'type' => 'url',
		) );
	}

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
