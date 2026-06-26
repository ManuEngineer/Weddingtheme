<?php
/**
 * Cordillera - Unterkunfts-Boerse v2.
 *
 * Felder (oeffentlich): Name, Typ, Ort, Plaetze, Datum Von/Bis, Sprachen, Beschreibung.
 * Felder (nur Admin):   Kontakt (E-Mail / Tel.).
 * Admin-Aktion:         Als "vermittelt" markieren / demarkieren.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* ============================================================
 * 1) Custom Post Type
 * ========================================================== */
function mym_board_cpt() {
	register_post_type( 'mym_board', array(
		'labels' => array(
			'name'          => __( 'Boerse', 'mym-hochzeit' ),
			'singular_name' => __( 'Boersen-Eintrag', 'mym-hochzeit' ),
			'menu_name'     => __( 'Unterkunfts-Boerse', 'mym-hochzeit' ),
			'all_items'     => __( 'Alle Eintraege', 'mym-hochzeit' ),
			'edit_item'     => __( 'Eintrag bearbeiten', 'mym-hochzeit' ),
		),
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_icon'     => 'dashicons-bed',
		'menu_position' => 25,
		'supports'      => array( 'title' ),
		'capability_type' => 'post',
	) );
}
add_action( 'init', 'mym_board_cpt' );

/* ============================================================
 * 2) Admin-Spalten
 * ========================================================== */
function mym_board_columns( $cols ) {
	return array(
		'cb'             => $cols['cb'],
		'title'          => __( 'Name', 'mym-hochzeit' ),
		'mym_type'       => __( 'Art', 'mym-hochzeit' ),
		'mym_location'   => __( 'Ort', 'mym-hochzeit' ),
		'mym_places'     => __( 'Pl.', 'mym-hochzeit' ),
		'mym_dates'      => __( 'Zeitraum', 'mym-hochzeit' ),
		'mym_langs'      => __( 'Sprachen', 'mym-hochzeit' ),
		'mym_contact'    => __( 'Kontakt (privat)', 'mym-hochzeit' ),
		'mym_vermittelt' => __( 'Vermittelt', 'mym-hochzeit' ),
		'date'           => __( 'Eingereicht', 'mym-hochzeit' ),
	);
}
add_filter( 'manage_mym_board_posts_columns', 'mym_board_columns' );

function mym_board_column_content( $col, $pid ) {
	switch ( $col ) {
		case 'mym_type':
			$t = get_post_meta( $pid, 'mym_type', true );
			echo $t === 'offer'
				? '<span style="color:#7fae8a">&#9679; ' . esc_html__( 'Bietet', 'mym-hochzeit' ) . '</span>'
				: '<span style="color:#d9b873">&#9679; ' . esc_html__( 'Sucht', 'mym-hochzeit' ) . '</span>';
			break;
		case 'mym_location':
			echo esc_html( get_post_meta( $pid, 'mym_location', true ) );
			break;
		case 'mym_places':
			echo esc_html( get_post_meta( $pid, 'mym_places', true ) );
			break;
		case 'mym_dates':
			$from = get_post_meta( $pid, 'mym_date_from', true );
			$to   = get_post_meta( $pid, 'mym_date_to', true );
			if ( $from || $to ) {
				echo esc_html( $from ) . ( $to ? ' – ' . esc_html( $to ) : '' );
			}
			break;
		case 'mym_langs':
			$langs = get_post_meta( $pid, 'mym_langs', true );
			if ( $langs ) {
				$list = array_map( 'strtoupper', array_filter( explode( ',', $langs ) ) );
				echo esc_html( implode( ' · ', $list ) );
			}
			break;
		case 'mym_contact':
			$contact = get_post_meta( $pid, 'mym_contact', true );
			if ( $contact ) {
				echo '<span style="background:#f0f0f0;padding:2px 6px;border-radius:3px;font-family:monospace;font-size:12px">' . esc_html( $contact ) . '</span>';
			}
			break;
		case 'mym_vermittelt':
			$v       = get_post_meta( $pid, 'mym_vermittelt', true );
			$nonce   = wp_create_nonce( 'mym_toggle_vermittelt_' . $pid );
			$url     = admin_url( 'admin-post.php?action=mym_toggle_vermittelt&post_id=' . $pid . '&_wpnonce=' . $nonce );
			if ( $v ) {
				echo '<a href="' . esc_url( $url ) . '" style="color:#7fae8a;font-weight:600">' . esc_html__( '✓ Vermittelt', 'mym-hochzeit' ) . '</a>';
			} else {
				echo '<a href="' . esc_url( $url ) . '" style="color:#aaa">' . esc_html__( '— Als vermittelt markieren', 'mym-hochzeit' ) . '</a>';
			}
			break;
	}
}
add_action( 'manage_mym_board_posts_custom_column', 'mym_board_column_content', 10, 2 );

/* Sortierbare Spalten */
add_filter( 'manage_edit-mym_board_sortable_columns', function( $cols ) {
	$cols['mym_type'] = 'mym_type';
	$cols['mym_vermittelt'] = 'mym_vermittelt';
	return $cols;
} );

/* ============================================================
 * 3) Admin-Aktion: Vermittelt umschalten
 * ========================================================== */
function mym_toggle_vermittelt() {
	$pid = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
	if ( ! $pid || ! current_user_can( 'edit_post', $pid ) ) { wp_die( 'Keine Berechtigung' ); }
	check_admin_referer( 'mym_toggle_vermittelt_' . $pid );
	$current = get_post_meta( $pid, 'mym_vermittelt', true );
	update_post_meta( $pid, 'mym_vermittelt', $current ? '' : '1' );
	wp_safe_redirect( admin_url( 'edit.php?post_type=mym_board' ) );
	exit;
}
add_action( 'admin_post_mym_toggle_vermittelt', 'mym_toggle_vermittelt' );

/* ============================================================
 * 3b) Meta-Box: ALLE Felder im Backend bearbeitbar
 * ========================================================== */
function mym_board_add_metabox() {
	add_meta_box( 'mym_board_details', __( 'Eintrag-Details', 'mym-hochzeit' ), 'mym_board_metabox_html', 'mym_board', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'mym_board_add_metabox' );

/* DD.MM.YYYY -> YYYY-MM-DD (fuer das <input type=date>) */
function mym_date_to_iso( $d ) {
	if ( preg_match( '/^(\d{2})\.(\d{2})\.(\d{4})$/', trim( (string) $d ), $m ) ) {
		return $m[3] . '-' . $m[2] . '-' . $m[1];
	}
	return '';
}

function mym_board_metabox_html( $post ) {
	wp_nonce_field( 'mym_board_save_' . $post->ID, 'mym_board_nonce' );
	$type     = get_post_meta( $post->ID, 'mym_type', true );
	$type     = $type ? $type : 'offer';
	$location = get_post_meta( $post->ID, 'mym_location', true );
	$places   = get_post_meta( $post->ID, 'mym_places', true );
	$places   = $places ? $places : 1;
	$from     = get_post_meta( $post->ID, 'mym_date_from', true );
	$to       = get_post_meta( $post->ID, 'mym_date_to', true );
	$langs    = array_filter( explode( ',', (string) get_post_meta( $post->ID, 'mym_langs', true ) ) );
	$note     = get_post_meta( $post->ID, 'mym_note', true );
	$contact  = get_post_meta( $post->ID, 'mym_contact', true );
	$verm     = get_post_meta( $post->ID, 'mym_vermittelt', true );
	$all_langs = array( 'de' => 'Deutsch', 'es' => 'Español', 'fr' => 'Français', 'en' => 'English' );
	?>
	<style>
		.mym-mb label.fld{display:block;font-weight:600;margin:14px 0 4px}
		.mym-mb input[type=text],.mym-mb input[type=number],.mym-mb input[type=date],.mym-mb textarea,.mym-mb select{width:100%;max-width:440px}
		.mym-mb .row{display:flex;gap:24px;flex-wrap:wrap}.mym-mb .row>div{flex:1;min-width:170px}
		.mym-mb .langs label{display:inline-block;font-weight:400;margin-right:18px}
		.mym-mb .hint{color:#777;font-weight:400;font-size:12px}
	</style>
	<div class="mym-mb">
		<div class="row">
			<div>
				<label class="fld"><?php esc_html_e( 'Art', 'mym-hochzeit' ); ?></label>
				<select name="mym_type">
					<option value="offer" <?php selected( $type, 'offer' ); ?>><?php esc_html_e( 'Bietet Unterkunft', 'mym-hochzeit' ); ?></option>
					<option value="seek" <?php selected( $type, 'seek' ); ?>><?php esc_html_e( 'Sucht Unterkunft', 'mym-hochzeit' ); ?></option>
				</select>
			</div>
			<div>
				<label class="fld"><?php esc_html_e( 'Plätze', 'mym-hochzeit' ); ?></label>
				<input type="number" name="mym_places" min="1" max="20" value="<?php echo esc_attr( $places ); ?>">
			</div>
		</div>

		<label class="fld"><?php esc_html_e( 'Ort', 'mym-hochzeit' ); ?></label>
		<input type="text" name="mym_location" value="<?php echo esc_attr( $location ); ?>" maxlength="80">

		<div class="row">
			<div>
				<label class="fld"><?php esc_html_e( 'Datum von', 'mym-hochzeit' ); ?></label>
				<input type="date" name="mym_date_from" value="<?php echo esc_attr( mym_date_to_iso( $from ) ); ?>">
			</div>
			<div>
				<label class="fld"><?php esc_html_e( 'Datum bis', 'mym-hochzeit' ); ?></label>
				<input type="date" name="mym_date_to" value="<?php echo esc_attr( mym_date_to_iso( $to ) ); ?>">
			</div>
		</div>

		<label class="fld"><?php esc_html_e( 'Sprachen', 'mym-hochzeit' ); ?></label>
		<div class="langs">
			<?php foreach ( $all_langs as $code => $name ) : ?>
				<label><input type="checkbox" name="mym_langs[]" value="<?php echo esc_attr( $code ); ?>" <?php checked( in_array( $code, $langs, true ) ); ?>> <?php echo esc_html( $name ); ?></label>
			<?php endforeach; ?>
		</div>

		<label class="fld"><?php esc_html_e( 'Beschreibung', 'mym-hochzeit' ); ?></label>
		<textarea name="mym_note" rows="3" maxlength="300"><?php echo esc_textarea( $note ); ?></textarea>

		<label class="fld"><?php esc_html_e( 'Kontakt', 'mym-hochzeit' ); ?> <span class="hint"><?php esc_html_e( '(privat — nur hier im Backend sichtbar, nie öffentlich)', 'mym-hochzeit' ); ?></span></label>
		<input type="text" name="mym_contact" value="<?php echo esc_attr( $contact ); ?>" maxlength="120">

		<p style="margin-top:18px"><label style="font-weight:600"><input type="checkbox" name="mym_vermittelt" value="1" <?php checked( $verm, '1' ); ?>> <?php esc_html_e( 'Als vermittelt markiert', 'mym-hochzeit' ); ?></label></p>
	</div>
	<?php
}

function mym_board_save_meta( $post_id ) {
	if ( ! isset( $_POST['mym_board_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mym_board_nonce'] ) ), 'mym_board_save_' . $post_id ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	$type = ( isset( $_POST['mym_type'] ) && $_POST['mym_type'] === 'seek' ) ? 'seek' : 'offer';
	update_post_meta( $post_id, 'mym_type', $type );

	$places = isset( $_POST['mym_places'] ) ? absint( $_POST['mym_places'] ) : 1;
	$places = max( 1, min( 20, $places ) );
	update_post_meta( $post_id, 'mym_places', $places );

	update_post_meta( $post_id, 'mym_location', isset( $_POST['mym_location'] ) ? sanitize_text_field( wp_unslash( $_POST['mym_location'] ) ) : '' );
	update_post_meta( $post_id, 'mym_date_from', mym_validate_date( isset( $_POST['mym_date_from'] ) ? wp_unslash( $_POST['mym_date_from'] ) : '' ) );
	update_post_meta( $post_id, 'mym_date_to', mym_validate_date( isset( $_POST['mym_date_to'] ) ? wp_unslash( $_POST['mym_date_to'] ) : '' ) );

	$allowed   = array( 'de', 'es', 'fr', 'en' );
	$langs_raw = ( isset( $_POST['mym_langs'] ) && is_array( $_POST['mym_langs'] ) ) ? $_POST['mym_langs'] : array();
	$langs     = implode( ',', array_intersect( array_map( 'sanitize_key', $langs_raw ), $allowed ) );
	update_post_meta( $post_id, 'mym_langs', $langs );

	update_post_meta( $post_id, 'mym_note', isset( $_POST['mym_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mym_note'] ) ) : '' );
	update_post_meta( $post_id, 'mym_contact', isset( $_POST['mym_contact'] ) ? sanitize_text_field( wp_unslash( $_POST['mym_contact'] ) ) : '' );
	update_post_meta( $post_id, 'mym_vermittelt', ! empty( $_POST['mym_vermittelt'] ) ? '1' : '' );
}
add_action( 'save_post_mym_board', 'mym_board_save_meta' );

/* ============================================================
 * 3c) Eintrag duplizieren (z.B. Restzeitraum als neuer Eintrag)
 * ========================================================== */
function mym_board_row_actions( $actions, $post ) {
	if ( $post->post_type === 'mym_board' && current_user_can( 'edit_post', $post->ID ) ) {
		$nonce = wp_create_nonce( 'mym_board_dup_' . $post->ID );
		$url   = admin_url( 'admin-post.php?action=mym_board_duplicate&post_id=' . $post->ID . '&_wpnonce=' . $nonce );
		$actions['mym_dup'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Duplizieren', 'mym-hochzeit' ) . '</a>';
	}
	return $actions;
}
add_filter( 'post_row_actions', 'mym_board_row_actions', 10, 2 );

function mym_board_duplicate() {
	$pid = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
	if ( ! $pid || ! current_user_can( 'edit_post', $pid ) ) { wp_die( 'Keine Berechtigung' ); }
	check_admin_referer( 'mym_board_dup_' . $pid );
	$src = get_post( $pid );
	if ( ! $src || $src->post_type !== 'mym_board' ) { wp_die( 'Eintrag nicht gefunden' ); }

	$new_id = wp_insert_post( array(
		'post_type'   => 'mym_board',
		'post_status' => 'publish',
		'post_title'  => $src->post_title,
	), true );
	if ( is_wp_error( $new_id ) ) { wp_die( 'Fehler beim Duplizieren' ); }

	foreach ( array( 'mym_type', 'mym_location', 'mym_places', 'mym_date_from', 'mym_date_to', 'mym_langs', 'mym_note', 'mym_contact' ) as $k ) {
		update_post_meta( $new_id, $k, get_post_meta( $pid, $k, true ) );
	}
	update_post_meta( $new_id, 'mym_vermittelt', '' );

	/* Direkt in die Bearbeitung des neuen Eintrags springen, um die Restdaten anzupassen */
	wp_safe_redirect( admin_url( 'post.php?post=' . $new_id . '&action=edit' ) );
	exit;
}
add_action( 'admin_post_mym_board_duplicate', 'mym_board_duplicate' );

/* ============================================================
 * 4) Eintraege abrufen (fuer Frontend)
 * ========================================================== */
function mym_board_entries( $type ) {
	$q = new WP_Query( array(
		'post_type'      => 'mym_board',
		'post_status'    => 'publish',
		'posts_per_page' => 200,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'meta_key'       => 'mym_type',
		'meta_value'     => $type,
		'no_found_rows'  => true,
	) );
	$out = array();
	foreach ( $q->posts as $p ) {
		$langs_raw = get_post_meta( $p->ID, 'mym_langs', true );
		$langs     = $langs_raw ? array_filter( explode( ',', $langs_raw ) ) : array();
		$out[] = array(
			'name'       => get_the_title( $p ),
			'location'   => get_post_meta( $p->ID, 'mym_location', true ),
			'places'     => get_post_meta( $p->ID, 'mym_places', true ),
			'date_from'  => get_post_meta( $p->ID, 'mym_date_from', true ),
			'date_to'    => get_post_meta( $p->ID, 'mym_date_to', true ),
			'langs'      => $langs,
			'note'       => get_post_meta( $p->ID, 'mym_note', true ),
			'vermittelt' => (bool) get_post_meta( $p->ID, 'mym_vermittelt', true ),
			/* Kontakt wird NICHT zurueckgegeben — nur im WP-Admin sichtbar */
		);
	}
	wp_reset_postdata();
	return $out;
}

/* ============================================================
 * 5) AJAX: Neuer Eintrag
 * ========================================================== */
function mym_board_submit() {
	check_ajax_referer( 'mym_board', 'nonce' );

	/* Honeypot */
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'moderated' => true ) );
	}

	/* Felder einlesen + bereinigen */
	$name      = isset( $_POST['name'] )      ? sanitize_text_field( wp_unslash( $_POST['name'] ) )      : '';
	$type      = isset( $_POST['type'] ) && $_POST['type'] === 'seek' ? 'seek' : 'offer';
	$location  = isset( $_POST['location'] )  ? sanitize_text_field( wp_unslash( $_POST['location'] ) )  : '';
	$places    = isset( $_POST['places'] )    ? absint( $_POST['places'] )                               : 1;
	$date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) : '';
	$date_to   = isset( $_POST['date_to'] )   ? sanitize_text_field( wp_unslash( $_POST['date_to'] ) )   : '';
	$langs_raw = isset( $_POST['langs'] ) && is_array( $_POST['langs'] ) ? $_POST['langs'] : array();
	$allowed   = array( 'de', 'es', 'fr', 'en' );
	$langs     = implode( ',', array_intersect( array_map( 'sanitize_key', $langs_raw ), $allowed ) );
	$note      = isset( $_POST['note'] )      ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) )  : '';
	$contact   = isset( $_POST['contact'] )   ? sanitize_text_field( wp_unslash( $_POST['contact'] ) )   : '';

	if ( $name === '' ) {
		wp_send_json_error( array( 'message' => __( 'Bitte gib einen Namen an.', 'mym-hochzeit' ) ) );
	}
	if ( $places < 1 )  { $places = 1; }
	if ( $places > 20 ) { $places = 20; }

	/* Datum-Format validieren (TT.MM.JJJJ oder JJJJ-MM-TT) */
	$date_from = mym_validate_date( $date_from );
	$date_to   = mym_validate_date( $date_to );

	$moderate = get_theme_mod( 'mym_board_moderate', true );
	$status   = $moderate ? 'draft' : 'publish';

	$post_id = wp_insert_post( array(
		'post_type'   => 'mym_board',
		'post_status' => $status,
		'post_title'  => $name,
	), true );

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Etwas ist schiefgelaufen.', 'mym-hochzeit' ) ) );
	}

	update_post_meta( $post_id, 'mym_type',      $type );
	update_post_meta( $post_id, 'mym_location',  $location );
	update_post_meta( $post_id, 'mym_places',    $places );
	update_post_meta( $post_id, 'mym_date_from', $date_from );
	update_post_meta( $post_id, 'mym_date_to',   $date_to );
	update_post_meta( $post_id, 'mym_langs',     $langs );
	update_post_meta( $post_id, 'mym_note',      $note );
	update_post_meta( $post_id, 'mym_contact',   $contact );
	update_post_meta( $post_id, 'mym_vermittelt', '' );

	/* E-Mail-Benachrichtigung */
	$notify = get_theme_mod( 'mym_board_notify', get_option( 'admin_email' ) );
	if ( is_email( $notify ) ) {
		$label = $type === 'offer' ? __( 'bietet Unterkunft', 'mym-hochzeit' ) : __( 'sucht Unterkunft', 'mym-hochzeit' );
		$body  = sprintf(
			"%s %s\nOrt: %s | Plaetze: %d\nZeitraum: %s – %s\nSprachen: %s\nVorstellung: %s\nKontakt: %s\n\nFreigeben: %s",
			$name, $label, $location, $places, $date_from, $date_to,
			strtoupper( str_replace( ',', ' / ', $langs ) ),
			$note, $contact,
			admin_url( 'edit.php?post_type=mym_board' )
		);
		wp_mail( $notify, __( 'Neuer Eintrag: Unterkunfts-Boerse', 'mym-hochzeit' ), $body );
	}

	wp_send_json_success( array(
		'moderated' => (bool) $moderate,
		'entry'     => array(
			'name'      => $name, 'location' => $location, 'places' => $places,
			'date_from' => $date_from, 'date_to' => $date_to,
			'langs'     => array_filter( explode( ',', $langs ) ),
			'note'      => $note, 'type' => $type, 'vermittelt' => false,
		),
	) );
}
add_action( 'wp_ajax_mym_board_submit',        'mym_board_submit' );
add_action( 'wp_ajax_nopriv_mym_board_submit', 'mym_board_submit' );

/* Datum bereinigen: gibt DD.MM.YYYY oder leer zurueck */
function mym_validate_date( $raw ) {
	$raw = trim( $raw );
	if ( ! $raw ) { return ''; }
	/* YYYY-MM-DD → DD.MM.YYYY */
	if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $raw, $m ) ) {
		return $m[3] . '.' . $m[2] . '.' . $m[1];
	}
	/* DD.MM.YYYY bleibt */
	if ( preg_match( '/^\d{2}\.\d{2}\.\d{4}$/', $raw ) ) { return $raw; }
	return '';
}
