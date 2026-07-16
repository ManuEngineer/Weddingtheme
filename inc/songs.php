<?php
/**
 * Cordillera - Musikwünsche.
 *
 * Rein privates Custom Post Type, wie RSVP: keine öffentliche Anzeige,
 * keine Moderation. Ein Post = eine Einreichung (Absender optional),
 * mit einer Liste von Songwünschen (Titel/Interpret) als serialisiertes
 * Array in mym_song_list — analog zur RSVP-Gästeliste.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ============================================================
 * 1) Custom Post Type
 * ========================================================== */
function mym_songs_cpt() {
	register_post_type( 'mym_song', array(
		'labels' => array(
			'name'          => __( 'Musikwünsche', 'mym-hochzeit' ),
			'singular_name' => __( 'Einreichung', 'mym-hochzeit' ),
			'menu_name'     => __( 'Musikwünsche', 'mym-hochzeit' ),
			'all_items'     => __( 'Alle Einreichungen', 'mym-hochzeit' ),
			'edit_item'     => __( 'Einreichung bearbeiten', 'mym-hochzeit' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-format-audio',
		'menu_position'   => 27,
		'supports'        => array( 'title' ),
		'capability_type' => 'post',
	) );
}
add_action( 'init', 'mym_songs_cpt' );

/* ============================================================
 * 2) Hilfsfunktionen
 * ========================================================== */

/**
 * Bereinigt einen einzelnen Songwunsch aus dem Frontend-JSON.
 *
 * @param array $s Rohdaten eines Songs.
 * @return array{title:string,artist:string}
 */
function mym_song_sanitize_item( $s ) {
	return array(
		'title'  => isset( $s['title'] )  ? sanitize_text_field( (string) $s['title'] )  : '',
		'artist' => isset( $s['artist'] ) ? sanitize_text_field( (string) $s['artist'] ) : '',
	);
}

/**
 * Spotify-Suchlink für einen Songwunsch — kein API-Key/OAuth nötig,
 * öffnet Spotify direkt mit der Trefferliste für Titel + Interpret.
 * Kein exakter Treffer garantiert, aber ein Klick entfernt.
 *
 * @param string $title
 * @param string $artist
 * @return string
 */
function mym_song_spotify_link( $title, $artist ) {
	$q = trim( $title . ' ' . $artist );
	if ( $q === '' ) { return ''; }
	return 'https://open.spotify.com/search/' . rawurlencode( $q );
}

/**
 * Liest eine Einreichung in ein flaches Array.
 *
 * @param int $post_id
 * @return array
 */
function mym_songs_get_entry( $post_id ) {
	$list = get_post_meta( $post_id, 'mym_song_list', true );
	return array(
		'name' => get_post_meta( $post_id, 'mym_song_name', true ),
		'list' => is_array( $list ) ? $list : array(),
	);
}

/* ============================================================
 * 3) Admin-Spalten
 * ========================================================== */
function mym_songs_columns( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => __( 'Absender', 'mym-hochzeit' ),
		'mym_count'   => __( 'Anzahl Songs', 'mym-hochzeit' ),
		'mym_preview' => __( 'Wünsche', 'mym-hochzeit' ),
		'date'        => __( 'Eingereicht', 'mym-hochzeit' ),
	);
}
add_filter( 'manage_mym_song_posts_columns', 'mym_songs_columns' );

function mym_songs_column_content( $col, $pid ) {
	$list = get_post_meta( $pid, 'mym_song_list', true );
	$list = is_array( $list ) ? $list : array();
	switch ( $col ) {
		case 'mym_count':
			echo esc_html( count( $list ) );
			break;
		case 'mym_preview':
			$labels = array_map( function ( $s ) {
				return trim( $s['title'] . ( $s['artist'] ? ' – ' . $s['artist'] : '' ) );
			}, array_slice( $list, 0, 3 ) );
			echo esc_html( implode( ' · ', $labels ) . ( count( $list ) > 3 ? ' …' : '' ) );
			break;
	}
}
add_action( 'manage_mym_song_posts_custom_column', 'mym_songs_column_content', 10, 2 );

/* ============================================================
 * 3b) Meta-Box: vollständige Einreichung (nur Lesen) — der CPT hat
 * keinen Editor, sonst wäre beim Öffnen eines Eintrags nur der Titel
 * sichtbar.
 * ========================================================== */
function mym_songs_add_metabox() {
	add_meta_box( 'mym_songs_details', __( 'Einreichung-Details', 'mym-hochzeit' ), 'mym_songs_metabox_html', 'mym_song', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mym_songs_add_metabox' );

function mym_songs_metabox_html( $post ) {
	$entry = mym_songs_get_entry( $post->ID );
	?>
	<style>
		.mym-songs-mb table{width:100%;border-collapse:collapse}
		.mym-songs-mb th,.mym-songs-mb td{border:1px solid #dcdcde;padding:8px 10px;text-align:left;vertical-align:top;font-size:13px}
		.mym-songs-mb th{background:#f6f7f7}
	</style>
	<div class="mym-songs-mb">
		<?php if ( $entry['name'] ) : ?>
		<p><strong><?php esc_html_e( 'Absender', 'mym-hochzeit' ); ?>:</strong> <?php echo esc_html( $entry['name'] ); ?></p>
		<?php else : ?>
		<p><em><?php esc_html_e( 'Kein Absender angegeben (optional).', 'mym-hochzeit' ); ?></em></p>
		<?php endif; ?>

		<?php if ( $entry['list'] ) : ?>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Song-Titel', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Interpret', 'mym-hochzeit' ); ?></th>
					<th><?php esc_html_e( 'Spotify-Suche', 'mym-hochzeit' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $entry['list'] as $s ) :
					$link = mym_song_spotify_link( $s['title'], $s['artist'] );
				?>
				<tr>
					<td><?php echo esc_html( $s['title'] ); ?></td>
					<td><?php echo esc_html( $s['artist'] ?: '—' ); ?></td>
					<td><?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>" target="_blank" rel="noopener">Spotify ↗</a><?php endif; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
	<?php
}

/* ============================================================
 * 4) Admin-Zusammenfassung oberhalb der Liste
 * ========================================================== */
function mym_songs_admin_summary() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'edit-mym_song' ) { return; }

	$q = new WP_Query( array(
		'post_type'      => 'mym_song',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );
	$submissions = count( $q->posts );
	$songs_total = 0;
	foreach ( $q->posts as $p ) {
		$list = get_post_meta( $p->ID, 'mym_song_list', true );
		$songs_total += is_array( $list ) ? count( $list ) : 0;
	}
	wp_reset_postdata();

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=mym_songs_export' ), 'mym_songs_export' );
	printf(
		'<div class="notice notice-info"><p><strong>%s</strong> %d %s, %d %s</p><p><a href="%s" class="button">%s</a></p></div>',
		esc_html__( 'Musikwünsche:', 'mym-hochzeit' ),
		(int) $submissions, esc_html__( 'Einreichungen', 'mym-hochzeit' ),
		(int) $songs_total, esc_html__( 'Songs insgesamt', 'mym-hochzeit' ),
		esc_url( $export_url ), esc_html__( 'CSV exportieren (eine Zeile pro Song)', 'mym-hochzeit' )
	);
}
add_action( 'admin_notices', 'mym_songs_admin_summary' );

/* ============================================================
 * 5) CSV-Export — eine Zeile pro Song
 * ========================================================== */
function mym_songs_export_csv() {
	if ( ! current_user_can( 'edit_others_posts' ) ) { wp_die( 'Keine Berechtigung' ); }
	check_admin_referer( 'mym_songs_export' );

	$q = new WP_Query( array(
		'post_type'      => 'mym_song',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="musikwuensche.csv"' );

	$out = fopen( 'php://output', 'w' );
	fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM, sonst zerschiesst Excel Umlaute.
	fputcsv( $out, array( 'Absender', 'Song-Titel', 'Interpret', 'Spotify-Suchlink', 'Eingereicht' ) );

	foreach ( $q->posts as $p ) {
		$entry = mym_songs_get_entry( $p->ID );
		$date  = get_the_date( 'd.m.Y H:i', $p );
		foreach ( $entry['list'] as $s ) {
			fputcsv( $out, array(
				$entry['name'], $s['title'], $s['artist'],
				mym_song_spotify_link( $s['title'], $s['artist'] ), $date,
			) );
		}
	}
	fclose( $out );
	wp_reset_postdata();
	exit;
}
add_action( 'admin_post_mym_songs_export', 'mym_songs_export_csv' );

/* ============================================================
 * 6) AJAX: Neue Einreichung
 * ========================================================== */
function mym_songs_submit() {
	check_ajax_referer( 'mym_songs', 'nonce' );

	/* Honeypot */
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'ok' => true ) );
	}

	if ( ! get_theme_mod( 'mym_songs_enabled', true ) ) {
		wp_send_json_error( array( 'message' => __( 'Musikwünsche werden derzeit nicht angenommen.', 'mym-hochzeit' ) ) );
	}

	/* Rate-Limit: max. 5 Einreichungen pro IP pro 6 Stunden (wie RSVP) */
	$ip = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )
		? trim( explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) )[0] )
		: ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown' );
	$rl_key   = 'mym_songs_rl_' . md5( $ip );
	$rl_count = (int) get_transient( $rl_key );
	if ( $rl_count >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Zu viele Einreichungen von dieser Adresse. Bitte später erneut versuchen.', 'mym-hochzeit' ) ) );
	}
	set_transient( $rl_key, $rl_count + 1, 6 * HOUR_IN_SECONDS );

	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

	$list = array();
	$raw  = isset( $_POST['songs'] ) ? json_decode( wp_unslash( $_POST['songs'] ), true ) : array();
	if ( is_array( $raw ) ) {
		foreach ( array_slice( $raw, 0, 20 ) as $s ) {
			if ( is_array( $s ) && ! empty( $s['title'] ) ) {
				$list[] = mym_song_sanitize_item( $s );
			}
		}
	}
	if ( ! $list ) {
		wp_send_json_error( array( 'message' => __( 'Bitte trage mindestens einen Songtitel ein.', 'mym-hochzeit' ) ) );
	}

	$post_id = wp_insert_post( array(
		'post_type'   => 'mym_song',
		'post_status' => 'publish',
		'post_title'  => $name !== '' ? $name : __( 'Anonym', 'mym-hochzeit' ),
	), true );
	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Etwas ist schiefgelaufen.', 'mym-hochzeit' ) ) );
	}

	update_post_meta( $post_id, 'mym_song_name', $name );
	update_post_meta( $post_id, 'mym_song_list', $list );

	mym_songs_notify( $post_id );

	wp_send_json_success( array( 'count' => count( $list ) ) );
}
add_action( 'wp_ajax_mym_songs_submit',        'mym_songs_submit' );
add_action( 'wp_ajax_nopriv_mym_songs_submit', 'mym_songs_submit' );

/* ============================================================
 * 7) Deaktiviert = auch aus dem Menü und als Direktaufruf weg
 * ========================================================== */

/**
 * Blendet die Seite mit dem Musikwünsche-Template aus jedem Navigationsmenü
 * aus, solange der Hauptschalter im Customizer aus ist — anders als bei
 * RSVP/Börse (dort bleibt Seite/Menüpunkt bewusst bestehen, siehe deren
 * Customizer-Beschreibung), hier explizit auf Wunsch komplett unsichtbar.
 *
 * @param WP_Post[] $items
 * @return WP_Post[]
 */
function mym_songs_filter_menu_items( $items ) {
	if ( get_theme_mod( 'mym_songs_enabled', true ) ) { return $items; }
	return array_values( array_filter( $items, function ( $item ) {
		if ( $item->object !== 'page' ) { return true; }
		return get_post_meta( (int) $item->object_id, '_wp_page_template', true ) !== 'page-songs.php';
	} ) );
}
add_filter( 'wp_get_nav_menu_items', 'mym_songs_filter_menu_items' );

/**
 * Benachrichtigung ans Brautpaar bei neuer Einreichung.
 *
 * @param int $post_id
 */
function mym_songs_notify( $post_id ) {
	/* leer gespeicherter Mod kommt als '' zurück, nicht als Default —
	 * deshalb explizit auf Leerstring prüfen, dann auf Boerse-Adresse zurückfallen. */
	$notify = get_theme_mod( 'mym_songs_notify', '' );
	if ( ! is_email( $notify ) ) {
		$notify = get_theme_mod( 'mym_board_notify', get_option( 'admin_email' ) );
	}
	if ( ! is_email( $notify ) ) { return; }

	$entry = mym_songs_get_entry( $post_id );
	$lines = array();
	$lines[] = ( $entry['name'] ? $entry['name'] : __( 'Anonym', 'mym-hochzeit' ) ) . ':';
	foreach ( $entry['list'] as $s ) {
		$lines[] = '  - ' . $s['title'] . ( $s['artist'] ? ' – ' . $s['artist'] : '' );
	}
	$body = implode( "\n", $lines ) . "\n\n" . admin_url( 'edit.php?post_type=mym_song' );
	wp_mail( $notify, __( 'Neuer Musikwunsch', 'mym-hochzeit' ), $body );
}
