<?php
/**
 * Cordillera - Seiten-Sektionen Helper.
 *
 * Jede Sektion (Geschichte, Programm, Anreise …) kann als eigene
 * WordPress-Seite gepflegt werden. Der Inhalt wird dann als HTML
 * ausgegeben (apply_filters 'the_content' — Bloecke, Shortcodes etc.
 * funktionieren). Existiert keine passende Seite, greift der Fallback
 * aus inc/content.php.
 *
 * Slugs (DE → ES):
 *   geschichte   → historia
 *   programm     → programa
 *   anreise      → como-llegar
 *   uebernachtung → alojamiento
 *   galerie      → galeria
 *   geschenke    → regalos
 *   faq          → preguntas   (Polylang uebernimmt die Uebersetzung)
 *
 * Mit Polylang: get_page_by_path() liefert automatisch die
 * sprachrichtige Version — einfach jede Seite in Polylang uebersetzen.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Gibt Titel + HTML-Inhalt einer Sektion-Seite zurueck.
 *
 * @param string      $slug_de  Slug der deutschen Seite.
 * @param string|null $slug_es  Slug der spanischen Seite (optional, Fallback: $slug_de).
 * @return array|null  Array mit 'title','content','edit_url' oder null wenn keine Seite existiert.
 */
function mym_section_page( $slug_de, $slug_es = null ) {
	$lang = mym_preview_lang();
	$slug = ( $lang === 'es' && $slug_es ) ? $slug_es : $slug_de;

	$page = get_page_by_path( $slug );

	/* Wenn Polylang nicht aktiv und ES-Slug anders: DE-Seite als Fallback versuchen */
	if ( ! $page && $slug !== $slug_de ) {
		$page = get_page_by_path( $slug_de );
	}

	if ( ! $page || $page->post_status !== 'publish' ) {
		return null;
	}

	$content = apply_filters( 'the_content', $page->post_content );

	return array(
		'title'    => get_the_title( $page ),
		'content'  => $content,
		'edit_url' => current_user_can( 'edit_post', $page->ID )
			? get_edit_post_link( $page->ID )
			: '',
	);
}

/* ============================================================
 * Admin-Hinweis: Welche Seiten anlegen?
 * ========================================================== */
function mym_sections_admin_notice() {
	$screen = get_current_screen();
	if ( ! $screen || $screen->id !== 'dashboard' ) { return; }

	$sections = array(
		array( 'Geschichte', 'geschichte', 'historia' ),
		array( 'Programm',   'programm',   'programa' ),
		array( 'Anreise',    'anreise',    'como-llegar' ),
		array( 'Übernachtung', 'uebernachtung', 'alojamiento' ),
		array( 'Galerie',    'galerie',    'galeria' ),
		array( 'Geschenke',  'geschenke',  'regalos' ),
		array( 'FAQ',        'faq',        'preguntas' ),
	);

	$missing = array();
	foreach ( $sections as $s ) {
		$de = get_page_by_path( $s[1] );
		$es = get_page_by_path( $s[2] );
		if ( ! $de || ! $es ) {
			$missing[] = sprintf(
				'<strong>%s</strong> — Slug DE: <code>%s</code>, ES: <code>%s</code>%s',
				esc_html( $s[0] ),
				esc_html( $s[1] ),
				esc_html( $s[2] ),
				( ! $de ? '' : ' ✓ DE' ) . ( ! $es ? '' : ' ✓ ES' )
			);
		}
	}

	if ( empty( $missing ) ) { return; }

	echo '<div class="notice notice-info is-dismissible"><p>';
	echo '<strong>' . esc_html__( 'Hochzeit', 'mym-hochzeit' ) . ':</strong> ';
	esc_html_e( 'Lege folgende WordPress-Seiten an, damit du Inhalte direkt im Editor bearbeiten kannst:', 'mym-hochzeit' );
	echo '</p><ul style="list-style:disc;margin-left:24px">';
	foreach ( $missing as $m ) {
		echo '<li style="margin-bottom:4px">' . $m . '</li>'; // phpcs:ignore
	}
	echo '</ul>';
	printf(
		'<p><a href="%s" class="button">%s</a></p>',
		esc_url( admin_url( 'post-new.php?post_type=page' ) ),
		esc_html__( 'Neue Seite anlegen', 'mym-hochzeit' )
	);
	echo '</div>';
}
add_action( 'admin_notices', 'mym_sections_admin_notice' );
