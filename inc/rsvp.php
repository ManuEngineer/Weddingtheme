<?php
/**
 * Cordillera - RSVP (Zu-/Absage).
 *
 * Rein privates Custom Post Type: keine öffentliche Anzeige, anders als
 * die Unterkunfts-Börse. Eine Anmeldung = ein Post, Kontaktperson im
 * Titel, Gästeliste (Name/Kind/Vegi/Allergien/Sprachen pro Person) als
 * serialisiertes Array in mym_rsvp_guests.
 *
 * Änderung durch den Gast: über einen persönlichen Link mit Token
 * (mym_rsvp_token), kein Login nötig. Token identifiziert die Anmeldung
 * eindeutig, wird beim Erstellen per random_bytes() generiert.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ============================================================
 * 1) Custom Post Type
 * ========================================================== */
function mym_rsvp_cpt() {
	register_post_type( 'mym_rsvp', array(
		'labels' => array(
			'name'          => __( 'RSVP', 'mym-hochzeit' ),
			'singular_name' => __( 'Anmeldung', 'mym-hochzeit' ),
			'menu_name'     => __( 'RSVP', 'mym-hochzeit' ),
			'all_items'     => __( 'Alle Anmeldungen', 'mym-hochzeit' ),
			'edit_item'     => __( 'Anmeldung bearbeiten', 'mym-hochzeit' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-yes-alt',
		'menu_position'   => 26,
		'supports'        => array( 'title' ),
		'capability_type' => 'post',
	) );
}
add_action( 'init', 'mym_rsvp_cpt' );

/* ============================================================
 * 2) Hilfsfunktionen: Gästeliste lesen/schreiben, Stats, Token
 * ========================================================== */

/**
 * Bereinigt eine einzelne Gast-Zeile aus dem Frontend-JSON.
 *
 * @param array $g Rohdaten einer Person.
 * @return array{name:string,child:bool,veggie:bool,allergies:string,langs:string[]}
 */
function mym_rsvp_sanitize_guest( $g ) {
	$allowed_langs = array( 'de', 'es', 'fr', 'en' );
	$langs_raw     = isset( $g['langs'] ) && is_array( $g['langs'] ) ? $g['langs'] : array();
	return array(
		'name'      => isset( $g['name'] ) ? sanitize_text_field( (string) $g['name'] ) : '',
		'child'     => ! empty( $g['child'] ),
		'veggie'    => ! empty( $g['veggie'] ),
		'allergies' => isset( $g['allergies'] ) ? sanitize_text_field( (string) $g['allergies'] ) : '',
		'langs'     => array_values( array_intersect( array_map( 'sanitize_key', $langs_raw ), $allowed_langs ) ),
	);
}

/**
 * Zählt Erwachsene/Kinder/Vegetarier innerhalb EINER Anmeldung.
 *
 * @param array $guests Gästeliste (siehe mym_rsvp_sanitize_guest()).
 * @return array{total:int,adults:int,children:int,veggies:int}
 */
function mym_rsvp_count_guests( $guests ) {
	$total = count( $guests );
	$children = 0;
	$veggies  = 0;
	foreach ( $guests as $g ) {
		if ( ! empty( $g['child'] ) )  { $children++; }
		if ( ! empty( $g['veggie'] ) ) { $veggies++; }
	}
	return array(
		'total'    => $total,
		'adults'   => $total - $children,
		'children' => $children,
		'veggies'  => $veggies,
	);
}

/**
 * Gesamtübersicht über alle Anmeldungen, für Admin-Liste + E-Mail.
 *
 * @return array
 */
function mym_rsvp_stats() {
	$q = new WP_Query( array(
		'post_type'      => 'mym_rsvp',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );
	$stats = array(
		'yes' => 0, 'no' => 0,
		'adults' => 0, 'children' => 0, 'veggies' => 0, 'guests_total' => 0,
	);
	foreach ( $q->posts as $p ) {
		$status = get_post_meta( $p->ID, 'mym_rsvp_status', true );
		if ( $status === 'yes' ) {
			$stats['yes']++;
			$guests = get_post_meta( $p->ID, 'mym_rsvp_guests', true );
			$guests = is_array( $guests ) ? $guests : array();
			$c = mym_rsvp_count_guests( $guests );
			$stats['adults']       += $c['adults'];
			$stats['children']     += $c['children'];
			$stats['veggies']      += $c['veggies'];
			$stats['guests_total'] += $c['total'];
		} elseif ( $status === 'no' ) {
			$stats['no']++;
		}
	}
	wp_reset_postdata();
	return $stats;
}

function mym_rsvp_generate_token() {
	return bin2hex( random_bytes( 16 ) );
}

/**
 * Slug der veröffentlichten Seite mit dem RSVP-Template, in der aktuellen
 * Sprache (falls Polylang aktiv) — für den "Jetzt zusagen"-Sprung-Anker
 * im Startbild. Leerstring, wenn keine solche Seite existiert.
 *
 * @return string
 */
function mym_rsvp_page_slug() {
	$args = array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_wp_page_template',
		'meta_value'     => 'page-rsvp.php',
		'no_found_rows'  => true,
	);
	if ( function_exists( 'pll_current_language' ) ) {
		$args['lang'] = mym_current_lang();
	}
	$q    = new WP_Query( $args );
	$slug = $q->posts ? $q->posts[0]->post_name : '';
	wp_reset_postdata();
	return $slug;
}

/**
 * Findet eine Anmeldung anhand ihres Änderungs-Tokens.
 *
 * @param string $token
 * @return WP_Post|null
 */
function mym_rsvp_get_by_token( $token ) {
	if ( ! $token || ! preg_match( '/^[a-f0-9]{32}$/', $token ) ) { return null; }
	$q = new WP_Query( array(
		'post_type'      => 'mym_rsvp',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => 'mym_rsvp_token',
		'meta_value'     => $token,
		'no_found_rows'  => true,
	) );
	$post = $q->posts ? $q->posts[0] : null;
	wp_reset_postdata();
	return $post;
}

/**
 * Liest eine Anmeldung in ein flaches Array (fürs Vorbefüllen im Frontend
 * und für die E-Mail-Zusammenfassung).
 *
 * @param int $post_id
 * @return array
 */
function mym_rsvp_get_entry( $post_id ) {
	$guests = get_post_meta( $post_id, 'mym_rsvp_guests', true );
	return array(
		'name'    => get_the_title( $post_id ),
		'email'   => get_post_meta( $post_id, 'mym_rsvp_email', true ),
		'phone'   => get_post_meta( $post_id, 'mym_rsvp_phone', true ),
		'status'  => get_post_meta( $post_id, 'mym_rsvp_status', true ),
		'guests'  => is_array( $guests ) ? $guests : array(),
		'message' => get_post_meta( $post_id, 'mym_rsvp_message', true ),
		'token'   => get_post_meta( $post_id, 'mym_rsvp_token', true ),
		'lang'    => get_post_meta( $post_id, 'mym_rsvp_lang', true ),
	);
}

/* ============================================================
 * 3) Admin-Spalten
 * ========================================================== */
function mym_rsvp_columns( $cols ) {
	return array(
		'cb'            => $cols['cb'],
		'title'         => __( 'Kontaktperson', 'mym-hochzeit' ),
		'mym_status'    => __( 'Zu-/Absage', 'mym-hochzeit' ),
		'mym_adults'    => __( 'Erw.', 'mym-hochzeit' ),
		'mym_children'  => __( 'Kinder', 'mym-hochzeit' ),
		'mym_veggies'   => __( 'Vegi', 'mym-hochzeit' ),
		'mym_contact'   => __( 'Kontakt (privat)', 'mym-hochzeit' ),
		'date'          => __( 'Eingereicht', 'mym-hochzeit' ),
	);
}
add_filter( 'manage_mym_rsvp_posts_columns', 'mym_rsvp_columns' );

function mym_rsvp_column_content( $col, $pid ) {
	$status = get_post_meta( $pid, 'mym_rsvp_status', true );
	$guests = get_post_meta( $pid, 'mym_rsvp_guests', true );
	$guests = is_array( $guests ) ? $guests : array();
	$c      = mym_rsvp_count_guests( $guests );
	switch ( $col ) {
		case 'mym_status':
			echo $status === 'yes'
				? '<span style="color:#7fae8a">&#9679; ' . esc_html__( 'Zusage', 'mym-hochzeit' ) . '</span>'
				: '<span style="color:#c96a4e">&#9679; ' . esc_html__( 'Absage', 'mym-hochzeit' ) . '</span>';
			break;
		case 'mym_adults':
			echo $status === 'yes' ? esc_html( $c['adults'] ) : '—';
			break;
		case 'mym_children':
			echo $status === 'yes' ? esc_html( $c['children'] ) : '—';
			break;
		case 'mym_veggies':
			echo $status === 'yes' ? esc_html( $c['veggies'] ) : '—';
			break;
		case 'mym_contact':
			$email = get_post_meta( $pid, 'mym_rsvp_email', true );
			$phone = get_post_meta( $pid, 'mym_rsvp_phone', true );
			$contact = trim( $email . ( $phone ? ' · ' . $phone : '' ) );
			if ( $contact ) {
				echo '<span style="background:#f0f0f0;padding:2px 6px;border-radius:3px;font-family:monospace;font-size:12px">' . esc_html( $contact ) . '</span>';
			}
			break;
	}
}
add_action( 'manage_mym_rsvp_posts_custom_column', 'mym_rsvp_column_content', 10, 2 );

add_filter( 'manage_edit-mym_rsvp_sortable_columns', function ( $cols ) {
	$cols['mym_status'] = 'mym_status';
	return $cols;
} );

/* ============================================================
 * 3b) Meta-Box: vollständige Anmeldung (nur Lesen) — der CPT hat
 * keinen Editor, sonst wäre beim Öffnen eines Eintrags nur der Titel
 * sichtbar. Bearbeiten der Gästeliste läuft über den Gast selbst
 * (persönlicher Link); hier geht es nur ums Nachschlagen im Backend.
 * ========================================================== */
function mym_rsvp_add_metabox() {
	add_meta_box( 'mym_rsvp_details', __( 'Anmeldung-Details', 'mym-hochzeit' ), 'mym_rsvp_metabox_html', 'mym_rsvp', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mym_rsvp_add_metabox' );

function mym_rsvp_metabox_html( $post ) {
	$entry     = mym_rsvp_get_entry( $post->ID );
	$all_langs = array( 'de' => 'Deutsch', 'es' => 'Español', 'fr' => 'Français', 'en' => 'English' );
	?>
	<style>
		.mym-rsvp-mb dl{display:grid;grid-template-columns:140px 1fr;gap:6px 12px;margin:0 0 20px}
		.mym-rsvp-mb dt{font-weight:600}
		.mym-rsvp-mb dd{margin:0}
		.mym-rsvp-mb table{width:100%;border-collapse:collapse;margin-bottom:20px}
		.mym-rsvp-mb th,.mym-rsvp-mb td{border:1px solid #dcdcde;padding:8px 10px;text-align:left;vertical-align:top;font-size:13px}
		.mym-rsvp-mb th{background:#f6f7f7}
		.mym-rsvp-mb .msg{white-space:pre-wrap;background:#f6f7f7;padding:10px 12px;border-radius:3px;max-width:640px}
	</style>
	<div class="mym-rsvp-mb">
		<dl>
			<dt><?php esc_html_e( 'E-Mail', 'mym-hochzeit' ); ?></dt>
			<dd><a href="mailto:<?php echo esc_attr( $entry['email'] ); ?>"><?php echo esc_html( $entry['email'] ); ?></a></dd>
			<dt><?php esc_html_e( 'Telefon', 'mym-hochzeit' ); ?></dt>
			<dd><?php echo esc_html( $entry['phone'] ); ?></dd>
			<dt><?php esc_html_e( 'Status', 'mym-hochzeit' ); ?></dt>
			<dd><?php echo $entry['status'] === 'yes' ? esc_html__( 'Zusage', 'mym-hochzeit' ) : esc_html__( 'Absage', 'mym-hochzeit' ); ?></dd>
		</dl>

		<?php if ( $entry['status'] === 'yes' && $entry['guests'] ) : ?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Kind', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Vegi', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Allergien / Wünsche', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Sprachen', 'mym-hochzeit' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $entry['guests'] as $g ) : ?>
				<tr>
					<td><?php echo esc_html( $g['name'] ); ?></td>
					<td><?php echo ! empty( $g['child'] ) ? esc_html__( 'ja', 'mym-hochzeit' ) : '—'; ?></td>
					<td><?php echo ! empty( $g['veggie'] ) ? esc_html__( 'ja', 'mym-hochzeit' ) : '—'; ?></td>
					<td><?php echo esc_html( $g['allergies'] ?: '—' ); ?></td>
					<td><?php
						$labels = array_map( function ( $code ) use ( $all_langs ) { return $all_langs[ $code ] ?? $code; }, $g['langs'] ?? array() );
						echo esc_html( $labels ? implode( ', ', $labels ) : '—' );
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

		<?php if ( $entry['message'] ) : ?>
		<p><strong><?php esc_html_e( 'Nachricht ans Brautpaar', 'mym-hochzeit' ); ?></strong></p>
		<p class="msg"><?php echo esc_html( $entry['message'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/* ============================================================
 * 4) Admin-Zusammenfassung oberhalb der Liste
 * ========================================================== */
function mym_rsvp_admin_summary() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'edit-mym_rsvp' ) { return; }
	$s = mym_rsvp_stats();
	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=mym_rsvp_export' ), 'mym_rsvp_export' );
	printf(
		'<div class="notice notice-info"><p><strong>%s</strong> %d %s (%d %s, %d %s, %d %s) · <strong>%s</strong> %d %s</p><p><a href="%s" class="button">%s</a></p></div>',
		esc_html__( 'Zusagen:', 'mym-hochzeit' ), (int) $s['yes'], esc_html__( 'Anmeldungen', 'mym-hochzeit' ),
		(int) $s['adults'], esc_html__( 'Erwachsene', 'mym-hochzeit' ),
		(int) $s['children'], esc_html__( 'Kinder', 'mym-hochzeit' ),
		(int) $s['veggies'], esc_html__( 'davon Vegi', 'mym-hochzeit' ),
		esc_html__( 'Absagen:', 'mym-hochzeit' ), (int) $s['no'], esc_html__( 'Anmeldungen', 'mym-hochzeit' ),
		esc_url( $export_url ), esc_html__( 'CSV exportieren (eine Zeile pro Gast)', 'mym-hochzeit' )
	);
}
add_action( 'admin_notices', 'mym_rsvp_admin_summary' );

/* ============================================================
 * 5) CSV-Export — eine Zeile pro Gast
 * ========================================================== */
function mym_rsvp_export_csv() {
	if ( ! current_user_can( 'edit_others_posts' ) ) { wp_die( 'Keine Berechtigung' ); }
	check_admin_referer( 'mym_rsvp_export' );

	$q = new WP_Query( array(
		'post_type'      => 'mym_rsvp',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="rsvp-gaesteliste.csv"' );

	$out = fopen( 'php://output', 'w' );
	fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM, sonst zerschiesst Excel Umlaute.
	fputcsv( $out, array( 'Anmeldung', 'Kontaktperson', 'Status', 'Gastname', 'Kind', 'Vegi', 'Allergien', 'Sprachen', 'E-Mail', 'Telefon', 'Nachricht', 'Eingereicht' ) );

	foreach ( $q->posts as $p ) {
		$entry  = mym_rsvp_get_entry( $p->ID );
		$status = $entry['status'] === 'yes' ? 'Zusage' : 'Absage';
		$date   = get_the_date( 'd.m.Y H:i', $p );

		if ( $entry['status'] === 'yes' && $entry['guests'] ) {
			foreach ( $entry['guests'] as $g ) {
				fputcsv( $out, array(
					$p->ID, $entry['name'], $status, $g['name'],
					! empty( $g['child'] ) ? 'ja' : 'nein',
					! empty( $g['veggie'] ) ? 'ja' : 'nein',
					$g['allergies'], implode( '/', $g['langs'] ),
					$entry['email'], $entry['phone'], $entry['message'], $date,
				) );
			}
		} else {
			fputcsv( $out, array( $p->ID, $entry['name'], $status, '', '', '', '', '', $entry['email'], $entry['phone'], $entry['message'], $date ) );
		}
	}
	fclose( $out );
	wp_reset_postdata();
	exit;
}
add_action( 'admin_post_mym_rsvp_export', 'mym_rsvp_export_csv' );

require get_template_directory() . '/inc/rsvp-email.php';
require get_template_directory() . '/inc/rsvp-ajax.php';
