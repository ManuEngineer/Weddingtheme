<?php
/**
 * Cordillera - Seiten-Sektionen Helper.
 *
 * Seit v2.0 (menügesteuerter Onepager) liest front-page.php die Sektionen
 * direkt aus dem primären Navigationsmenü (jeder Menüpunkt = eine Seite,
 * das Seiten-Template bestimmt section-default/-board/-gallery.php).
 * Diese Datei enthält nur noch einen Hilfs-Helper für eigene Erweiterungen
 * (siehe unten) und den Dashboard-Einrichtungshinweis.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Gibt Titel + HTML-Inhalt einer Seite anhand ihrer ID zurück.
 * Wird intern von front-page.php nicht mehr benötigt (Menüloop liest
 * die Seite direkt), steht aber für eigene Erweiterungen bereit.
 *
 * @param int $page_id  WordPress-Seiten-ID.
 * @return array|null  Array mit 'title','content','edit_url' oder null.
 */
function mym_section_by_page_id( $page_id ) {
	$page = get_post( $page_id );
	if ( ! $page || $page->post_status !== 'publish' ) {
		return null;
	}
	return array(
		'title'    => get_the_title( $page ),
		'content'  => apply_filters( 'the_content', $page->post_content ),
		'edit_url' => current_user_can( 'edit_post', $page->ID )
			? get_edit_post_link( $page->ID )
			: '',
	);
}

/* ============================================================
 * Admin-Hinweis: Theme-Einrichtung (v2.0)
 * ========================================================== */
function mym_sections_admin_notice() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'dashboard' ) { return; }

	/* Prüfen ob ein primäres Menü gesetzt ist */
	$menu_locs = get_nav_menu_locations();
	$has_menu  = ! empty( $menu_locs['primary'] );

	if ( $has_menu ) {
		/* Menü vorhanden: Board-Template-Hinweis prüfen */
		$raw = wp_get_nav_menu_items( $menu_locs['primary'] );
		if ( ! is_array( $raw ) ) { return; }
		$has_board_section = false;
		foreach ( $raw as $item ) {
			if ( $item->object === 'page' && (int) $item->menu_item_parent === 0 ) {
				$tpl = get_post_meta( (int) $item->object_id, '_wp_page_template', true );
				if ( $tpl === 'page-board.php' ) { $has_board_section = true; break; }
			}
		}
		if ( $has_board_section ) { return; }

		echo '<div class="notice notice-info is-dismissible"><p>';
		echo '<strong>' . esc_html__( 'Hochzeit (Unterkunftsbörse)', 'mym-hochzeit' ) . ':</strong> ';
		esc_html_e( 'Damit die Unterkunftsbörse erscheint, öffne die Übernachtungs-Seite im Editor und setze das Seiten-Template auf „Unterkunftsbörse".', 'mym-hochzeit' );
		echo '</p></div>';
		return;
	}

	/* Kein Menü gesetzt */
	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo '<strong>' . esc_html__( 'Hochzeit: Theme einrichten', 'mym-hochzeit' ) . ':</strong> ';
	esc_html_e( 'Erstelle ein primäres Navigationsmenü und füge deine Sektionsseiten hinzu:', 'mym-hochzeit' );
	echo '</p><ol style="list-style:decimal;margin-left:24px">';
	echo '<li>' . esc_html__( 'Seiten für jede Sektion anlegen (Geschichte, Programm, Anreise …)', 'mym-hochzeit' ) . '</li>';
	echo '<li>' . esc_html__( 'Für die Übernachtungsseite: Seiten-Template „Unterkunftsbörse" wählen', 'mym-hochzeit' ) . '</li>';
	echo '<li>' . esc_html__( 'Für die Anreise-Seite mit Karte: Template „Anreise & Karte" wählen', 'mym-hochzeit' ) . '</li>';
	echo '<li>' . esc_html__( 'Für die Galerie-Seite: Template „Foto-Galerie" wählen', 'mym-hochzeit' ) . '</li>';
	echo '<li>' . esc_html__( 'Unter Design → Menüs ein primäres Menü erstellen und die Seiten hinzufügen', 'mym-hochzeit' ) . '</li>';
	echo '</ol>';
	printf(
		'<p><a href="%s" class="button button-primary" style="margin-right:8px">%s</a><a href="%s" class="button">%s</a></p>',
		esc_url( admin_url( 'nav-menus.php' ) ),
		esc_html__( 'Menü bearbeiten', 'mym-hochzeit' ),
		esc_url( admin_url( 'post-new.php?post_type=page' ) ),
		esc_html__( 'Neue Seite anlegen', 'mym-hochzeit' )
	);
	echo '</div>';
}
add_action( 'admin_notices', 'mym_sections_admin_notice' );
