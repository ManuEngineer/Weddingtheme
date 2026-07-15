<?php
/**
 * Cordillera - RSVP: AJAX-Handler (Neuanmeldung + Änderung via Token).
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function mym_rsvp_submit() {
	check_ajax_referer( 'mym_rsvp', 'nonce' );

	/* Honeypot */
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'ok' => true ) );
	}

	$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
	$existing = $token ? mym_rsvp_get_by_token( $token ) : null;
	$is_update = (bool) $existing;

	/* Rate-Limit nur für NEUE Anmeldungen (Änderungen über gültigen Token sind bereits legitimiert) */
	if ( ! $is_update ) {
		$ip = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )
			? trim( explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) )[0] )
			: ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
		$rl_key   = 'mym_rsvp_rl_' . md5( $ip );
		$rl_count = (int) get_transient( $rl_key );
		if ( $rl_count >= 5 ) {
			wp_send_json_error( array( 'message' => __( 'Zu viele Anmeldungen von dieser Adresse. Bitte später erneut versuchen.', 'mym-hochzeit' ) ) );
		}
		set_transient( $rl_key, $rl_count + 1, 6 * HOUR_IN_SECONDS );
	}
	if ( $token && ! $existing ) {
		wp_send_json_error( array( 'message' => __( 'Dieser Änderungslink ist ungültig oder abgelaufen.', 'mym-hochzeit' ) ) );
	}

	/* Felder einlesen */
	$email  = isset( $_POST['email'] )  ? sanitize_email( wp_unslash( $_POST['email'] ) )        : '';
	$phone  = isset( $_POST['phone'] )  ? sanitize_text_field( wp_unslash( $_POST['phone'] ) )   : '';
	$status = ( isset( $_POST['status'] ) && $_POST['status'] === 'no' ) ? 'no' : 'yes';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Bitte gib eine gültige E-Mail-Adresse an.', 'mym-hochzeit' ) ) );
	}
	/* Gäste kommen aus mehreren Ländern (CH/CL/DE/FR/NL) — Landesvorwahl (führendes "+") zwingend. */
	if ( ! preg_match( '/^\+[0-9 ()-]{7,20}$/', $phone ) ) {
		wp_send_json_error( array( 'message' => __( 'Bitte gib eine Telefonnummer mit Landesvorwahl an, z. B. +41 79 123 45 67.', 'mym-hochzeit' ) ) );
	}

	/* Gästeliste (nur bei Zusage relevant) — als JSON-String eines Arrays gesendet */
	$guests = array();
	if ( $status === 'yes' ) {
		$guests_raw = isset( $_POST['guests'] ) ? json_decode( wp_unslash( $_POST['guests'] ), true ) : array();
		if ( is_array( $guests_raw ) ) {
			foreach ( array_slice( $guests_raw, 0, 30 ) as $g ) {
				if ( is_array( $g ) && ! empty( $g['name'] ) ) {
					$guests[] = mym_rsvp_sanitize_guest( $g );
				}
			}
		}
		if ( ! $guests ) {
			wp_send_json_error( array( 'message' => __( 'Bitte trage mindestens eine Person ein.', 'mym-hochzeit' ) ) );
		}
	}

	/* Kontaktperson (Titel, fürs Admin-Listing/CSV/E-Mails): bei Zusage kommt sie
	 * automatisch vom ersten Gast der Liste (kein doppeltes Namensfeld nötig); bei
	 * Absage gibt es keine Gästeliste, dort wird sie explizit abgefragt. */
	if ( $status === 'yes' ) {
		$name = ! empty( $guests[0]['name'] ) ? $guests[0]['name'] : $email;
	} else {
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		if ( $name === '' ) {
			wp_send_json_error( array( 'message' => __( 'Bitte gib deinen Namen an.', 'mym-hochzeit' ) ) );
		}
	}

	$lang = function_exists( 'mym_current_lang' ) ? mym_current_lang() : 'de';

	if ( $is_update ) {
		$post_id = $existing->ID;
		wp_update_post( array( 'ID' => $post_id, 'post_title' => $name ) );
	} else {
		$post_id = wp_insert_post( array(
			'post_type'   => 'mym_rsvp',
			'post_status' => 'publish',
			'post_title'  => $name,
		), true );
		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Etwas ist schiefgelaufen.', 'mym-hochzeit' ) ) );
		}
		$token = mym_rsvp_generate_token();
		update_post_meta( $post_id, 'mym_rsvp_token', $token );
	}

	update_post_meta( $post_id, 'mym_rsvp_email',   $email );
	update_post_meta( $post_id, 'mym_rsvp_phone',   $phone );
	update_post_meta( $post_id, 'mym_rsvp_status',  $status );
	update_post_meta( $post_id, 'mym_rsvp_guests',  $guests );
	update_post_meta( $post_id, 'mym_rsvp_message', $message );
	update_post_meta( $post_id, 'mym_rsvp_lang',    $lang );

	mym_rsvp_send_guest_confirmation( $post_id );
	mym_rsvp_notify_couple( $post_id, $is_update );

	wp_send_json_success( array(
		'updated' => $is_update,
		'token'   => $token,
	) );
}
add_action( 'wp_ajax_mym_rsvp_submit',        'mym_rsvp_submit' );
add_action( 'wp_ajax_nopriv_mym_rsvp_submit', 'mym_rsvp_submit' );
