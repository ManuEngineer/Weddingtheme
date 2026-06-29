<?php
/**
 * v2.0 UI-String-Helfer.
 * mym_s($key, $default_en) liefert einen übersetzten String.
 * Englisch ist die kanonische Basis; DE/ES sind als eingebaute Defaults enthalten,
 * sodass das Theme ohne Polylang-Übersetzungen in DE/ES direkt funktioniert.
 * Für alle anderen Sprachen (FR, IT, …) werden die Strings via Polylang
 * unter Sprachen → String-Übersetzungen gepflegt.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Gibt einen übersetzten UI-String zurück.
 * Reihenfolge: Polylang-Übersetzung → eingebauter Sprachdefault → Englisch.
 *
 * @param string $key        String-Schlüssel (mym_*).
 * @param string $default_en Englischer Quell-String (der in Polylang registrierte Wert).
 * @return string
 */
function mym_s( $key, $default_en ) {
	if ( function_exists( 'pll__' ) ) {
		$t = pll__( $default_en );
		if ( $t && $t !== $default_en ) {
			return $t;
		}
	}
	$lang = mym_current_lang();
	static $defaults = null;
	if ( null === $defaults ) {
		$defaults = mym_s_built_in_defaults();
	}
	return isset( $defaults[ $lang ][ $key ] ) ? $defaults[ $lang ][ $key ] : $default_en;
}

/**
 * Eingebaute DE/ES Defaults — kein Polylang nötig für diese Sprachen.
 */
function mym_s_built_in_defaults() {
	return array(
		'de' => array(
			'mym_hero_eyebrow'         => 'Wir heiraten',
			'mym_hero_save'            => 'Save the Date',
			'mym_hero_until'           => 'bis zum großen Tag',
			'mym_hero_dates_note'      => 'Reserviert euch schon einmal einen dieser Tage:',
			'mym_cd_days'              => 'Tage',
			'mym_cd_hours'             => 'Stunden',
			'mym_cd_mins'              => 'Minuten',
			'mym_cd_secs'              => 'Sekunden',
			'mym_board_offer'          => 'Wir bieten Unterkunft',
			'mym_board_seek'           => 'Wir suchen Unterkunft',
			'mym_board_f_name'         => 'Name',
			'mym_board_f_type'         => 'Art',
			'mym_board_f_places'       => 'Plätze',
			'mym_board_f_location'     => 'Ort',
			'mym_board_f_from'         => 'Von',
			'mym_board_f_to'           => 'Bis',
			'mym_board_f_langs'        => 'Gesprochene Sprachen',
			'mym_board_f_note'         => 'Kurze Vorstellung / Notiz',
			'mym_board_f_contact'      => 'Kontakt (E-Mail oder Tel.)',
			'mym_board_f_contact_note' => '— wird nicht veröffentlicht, nur für uns',
			'mym_board_f_submit'       => 'Eintragen',
			'mym_board_empty_offer'    => 'Noch keine Angebote — sei die/der Erste!',
			'mym_board_empty_seek'     => 'Noch keine Gesuche.',
			'mym_board_loc_ph'         => 'z.B. Stadtzentrum',
			'mym_gallery_cta'          => 'Zur Galerie & Upload',
			'mym_footer_tag'           => 'Mit Liebe',
			'mym_js_sending'           => 'Wird gesendet …',
			'mym_js_thanks'            => 'Danke! Dein Eintrag wird nach kurzer Prüfung sichtbar.',
			'mym_js_err_name'          => 'Bitte gib einen Namen an.',
			'mym_js_error'             => 'Etwas ist schiefgelaufen. Bitte versuche es später erneut.',
		),
		'es' => array(
			'mym_hero_eyebrow'         => 'Nos casamos',
			'mym_hero_save'            => 'Reserva la fecha',
			'mym_hero_until'           => 'para el gran día',
			'mym_hero_dates_note'      => 'Reserva desde ya uno de estos días:',
			'mym_cd_days'              => 'Días',
			'mym_cd_hours'             => 'Horas',
			'mym_cd_mins'              => 'Minutos',
			'mym_cd_secs'              => 'Segundos',
			'mym_board_offer'          => 'Ofrecemos alojamiento',
			'mym_board_seek'           => 'Buscamos alojamiento',
			'mym_board_f_name'         => 'Nombre',
			'mym_board_f_type'         => 'Tipo',
			'mym_board_f_places'       => 'Plazas',
			'mym_board_f_location'     => 'Lugar',
			'mym_board_f_from'         => 'Desde',
			'mym_board_f_to'           => 'Hasta',
			'mym_board_f_langs'        => 'Idiomas hablados',
			'mym_board_f_note'         => 'Breve presentación / nota',
			'mym_board_f_contact'      => 'Contacto (correo o tel.)',
			'mym_board_f_contact_note' => '— no se publica, solo para nosotros',
			'mym_board_f_submit'       => 'Añadir',
			'mym_board_empty_offer'    => '¡Aún no hay ofertas — sé el primero!',
			'mym_board_empty_seek'     => 'Aún no hay solicitudes.',
			'mym_board_loc_ph'         => 'p.ej. centro',
			'mym_gallery_cta'          => 'Ir a la galería y subir',
			'mym_footer_tag'           => 'Con cariño',
			'mym_js_sending'           => 'Enviando …',
			'mym_js_thanks'            => '¡Gracias! Tu entrada será visible tras una breve revisión.',
			'mym_js_err_name'          => 'Por favor indica tu nombre.',
			'mym_js_error'             => 'Algo salió mal. Por favor inténtalo más tarde.',
		),
	);
}

/**
 * Registriert alle UI-Strings bei Polylang (init, Prio 20).
 * Englisch ist die Quell-Sprache, alle anderen werden in
 * Sprachen → String-Übersetzungen gepflegt.
 */
function mym_register_ui_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) { return; }

	$strings = array(
		array( 'mym_hero_eyebrow',         'We are getting married' ),
		array( 'mym_hero_save',             'Save the date' ),
		array( 'mym_hero_until',            'until the big day' ),
		array( 'mym_hero_dates_note',       'Save one of these dates:' ),
		array( 'mym_cd_days',               'Days' ),
		array( 'mym_cd_hours',              'Hours' ),
		array( 'mym_cd_mins',               'Minutes' ),
		array( 'mym_cd_secs',               'Seconds' ),
		array( 'mym_board_offer',           'We offer accommodation' ),
		array( 'mym_board_seek',            'We need accommodation' ),
		array( 'mym_board_f_name',          'Name' ),
		array( 'mym_board_f_type',          'Type' ),
		array( 'mym_board_f_places',        'Spots' ),
		array( 'mym_board_f_location',      'Location' ),
		array( 'mym_board_f_from',          'From' ),
		array( 'mym_board_f_to',            'To' ),
		array( 'mym_board_f_langs',         'Languages spoken' ),
		array( 'mym_board_f_note',          'Brief description / note' ),
		array( 'mym_board_f_contact',       'Contact (email or phone)' ),
		array( 'mym_board_f_contact_note',  '— not published, for us only' ),
		array( 'mym_board_f_submit',        'Submit' ),
		array( 'mym_board_empty_offer',     'No offers yet — be the first!' ),
		array( 'mym_board_empty_seek',      'No requests yet.' ),
		array( 'mym_board_loc_ph',          'e.g. city centre' ),
		array( 'mym_gallery_cta',           'To the gallery & upload' ),
		array( 'mym_footer_tag',            'With love' ),
		array( 'mym_js_sending',            'Sending …' ),
		array( 'mym_js_thanks',             'Thank you! Your entry will be visible after review.' ),
		array( 'mym_js_err_name',           'Please enter your name.' ),
		array( 'mym_js_error',              'Something went wrong. Please try again later.' ),
	);
	foreach ( $strings as $s ) {
		pll_register_string( $s[0], $s[1], 'mym-hochzeit' );
	}

	/* Customizer-Werte als übersetzbare Strings registrieren.
	 * Connector: immer registrieren — '&' als Quelle wenn Customizer leer,
	 * damit er auch ohne Eintrag in Polylang → String-Übersetzungen erscheint. */
	$conn     = get_theme_mod( 'mym_connector', '' );
	$conn_src = ( $conn !== '' ) ? $conn : '&';
	pll_register_string( 'mym_connector', $conn_src, 'mym-hochzeit' );
	$place = get_theme_mod( 'mym_place', '' );
	if ( $place !== '' ) { pll_register_string( 'mym_place', $place, 'mym-hochzeit' ); }
}
add_action( 'init', 'mym_register_ui_strings', 20 );
