<?php
/**
 * Cordillera - RSVP: Bestätigungs-/Benachrichtigungs-E-Mails.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Baut die vollständige Zusammenfassung einer Anmeldung als Text
 * (für Bestätigungsmail an den Gast UND Benachrichtigung ans Brautpaar).
 *
 * @param array $entry Rückgabe von mym_rsvp_get_entry().
 * @return string
 */
function mym_rsvp_summary_text( $entry ) {
	$lang = ( $entry['lang'] === 'es' ) ? 'es' : 'de';
	$L    = ( $lang === 'es' )
		? array(
			'status_yes' => 'Confirmación', 'status_no' => 'Cancelación',
			'contact' => 'Kontaktperson', 'status' => 'Status', 'guests' => 'Gäste',
			'child' => 'Kind', 'veggie' => 'Vegetarisch/Vegan', 'allergies' => 'Allergien',
			'langs' => 'Sprachen', 'message' => 'Nachricht', 'email' => 'E-Mail', 'phone' => 'Telefon',
		)
		: array(
			'status_yes' => 'Zusage', 'status_no' => 'Absage',
			'contact' => 'Kontaktperson', 'status' => 'Status', 'guests' => 'Gäste',
			'child' => 'Kind', 'veggie' => 'Vegetarisch/Vegan', 'allergies' => 'Allergien',
			'langs' => 'Sprachen', 'message' => 'Nachricht', 'email' => 'E-Mail', 'phone' => 'Telefon',
		);
	// Nur Status-Label ist tatsächlich zweisprachig, der Rest bleibt als Feldbezeichnung
	// bewusst simpel/technisch — die Mail ist eine Quittung, kein Fliesstext.
	$status_label = $entry['status'] === 'yes' ? $L['status_yes'] : $L['status_no'];

	$lines   = array();
	$lines[] = $L['contact'] . ': ' . $entry['name'];
	$lines[] = $L['status'] . ': ' . $status_label;
	$lines[] = $L['email'] . ': ' . $entry['email'];
	$lines[] = $L['phone'] . ': ' . $entry['phone'];

	if ( $entry['status'] === 'yes' && $entry['guests'] ) {
		$lines[] = '';
		$lines[] = $L['guests'] . ' (' . count( $entry['guests'] ) . '):';
		foreach ( $entry['guests'] as $i => $g ) {
			$bits = array();
			if ( ! empty( $g['child'] ) )  { $bits[] = $L['child']; }
			if ( ! empty( $g['veggie'] ) ) { $bits[] = $L['veggie']; }
			if ( ! empty( $g['langs'] ) )  { $bits[] = $L['langs'] . ': ' . strtoupper( implode( '/', $g['langs'] ) ); }
			if ( $g['allergies'] )         { $bits[] = $L['allergies'] . ': ' . $g['allergies']; }
			$lines[] = '  ' . ( $i + 1 ) . '. ' . $g['name'] . ( $bits ? ' (' . implode( ', ', $bits ) . ')' : '' );
		}
	}

	if ( $entry['message'] ) {
		$lines[] = '';
		$lines[] = $L['message'] . ': ' . $entry['message'];
	}

	return implode( "\n", $lines );
}

/**
 * Bestätigungsmail an den Gast — bei Neuanmeldung UND bei jeder Änderung,
 * damit klar ist "es hat funktioniert" (statt Mehrfach-Einreichung aus Unsicherheit).
 * Enthält den persönlichen Änderungslink.
 *
 * @param int $post_id
 */
function mym_rsvp_send_guest_confirmation( $post_id ) {
	$entry = mym_rsvp_get_entry( $post_id );
	if ( ! is_email( $entry['email'] ) ) { return; }

	$lang     = ( $entry['lang'] === 'es' ) ? 'es' : 'de';
	$edit_url = mym_rsvp_edit_url( $entry['token'], $lang );

	if ( $lang === 'es' ) {
		$subject = __( 'Confirmación de tu inscripción a la boda', 'mym-hochzeit' );
		$intro   = "¡Gracias por tu respuesta! Aquí un resumen de lo que registramos:\n\n";
		$outro   = "\n\n¿Algo que corregir? Usa este enlace personal en cualquier momento:\n" . $edit_url;
	} else {
		$subject = __( 'Bestätigung deiner Hochzeits-Anmeldung', 'mym-hochzeit' );
		$intro   = "Danke für deine Rückmeldung! Hier eine Zusammenfassung dessen, was wir erfasst haben:\n\n";
		$outro   = "\n\nEtwas falsch oder soll sich ändern? Nutze jederzeit deinen persönlichen Link:\n" . $edit_url;
	}

	$body = $intro . mym_rsvp_summary_text( $entry ) . $outro;
	wp_mail( $entry['email'], $subject, $body );
}

/**
 * Benachrichtigung ans Brautpaar bei neuer/geänderter Anmeldung.
 *
 * @param int  $post_id
 * @param bool $is_update
 */
function mym_rsvp_notify_couple( $post_id, $is_update ) {
	/* get_theme_mod() liefert nur dann den Default, wenn der Mod NIE gespeichert
	 * wurde — ein bereits gespeicherter, aber leerer Wert kommt als '' zurück,
	 * nicht als Fallback. Deshalb hier explizit auf Leerstring prüfen. */
	$notify = get_theme_mod( 'mym_rsvp_notify', '' );
	if ( ! is_email( $notify ) ) {
		$notify = get_theme_mod( 'mym_board_notify', get_option( 'admin_email' ) );
	}
	if ( ! is_email( $notify ) ) { return; }

	$entry   = mym_rsvp_get_entry( $post_id );
	$subject = $is_update
		? __( 'RSVP geändert', 'mym-hochzeit' )
		: __( 'Neue RSVP-Anmeldung', 'mym-hochzeit' );
	$body = mym_rsvp_summary_text( $entry ) . "\n\n" . admin_url( 'edit.php?post_type=mym_rsvp' );
	wp_mail( $notify, $subject . ': ' . $entry['name'], $body );
}

/**
 * Baut die Bearbeiten-URL für den persönlichen Änderungslink.
 * Sucht die veröffentlichte Seite mit Template page-rsvp.php in der
 * aktuellen Sprache; Fallback: Startseite.
 *
 * @param string $token
 * @param string $lang
 * @return string
 */
function mym_rsvp_edit_url( $token, $lang = '' ) {
	$page_url = home_url( '/' );
	$args = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-rsvp.php',
		'no_found_rows'  => true,
	);
	/* Auf Sprachversion einschränken, falls Polylang aktiv ist — sonst
	 * könnte bei zwei Sprachseiten mit demselben Template die falsche
	 * (andersprachige) Seite zurückkommen. */
	if ( $lang && function_exists( 'pll_get_post_language' ) ) {
		$args['lang'] = $lang;
	}
	$q = new WP_Query( $args );
	if ( $q->posts ) {
		$page_url = get_permalink( $q->posts[0] );
	}
	wp_reset_postdata();
	return add_query_arg( 'rsvp_token', $token, $page_url );
}
