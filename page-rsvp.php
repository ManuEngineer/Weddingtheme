<?php
/**
 * Template Name: RSVP (Zu-/Absage)
 * Template Post Type: page
 *
 * Direktaufruf der Seite zeigt das VOLLE Formular (nicht nur den
 * Seiteninhalt wie bei den anderen Vorlagen) — der persönliche
 * Änderungslink aus der Bestätigungsmail führt genau hierher.
 * Auf der Startseite rendert front-page.php dieselbe Sektion eingebettet.
 *
 * Ist der Hauptschalter im Customizer aus, ist die Seite für NEUE Besucher
 * nicht erreichbar (wie aus dem Menü ausgeblendet) — ein gültiger
 * persönlicher Token in der URL funktioniert aber weiterhin, sonst könnten
 * bereits angemeldete Gäste ihre eigene Anmeldung nicht mehr ändern.
 *
 * @package MyM_Hochzeit
 */
if ( ! get_theme_mod( 'mym_rsvp_enabled', true ) ) {
	$rsvp_token = isset( $_GET['rsvp_token'] ) ? sanitize_text_field( wp_unslash( $_GET['rsvp_token'] ) ) : '';
	if ( ! $rsvp_token || ! mym_rsvp_get_by_token( $rsvp_token ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
get_header();
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/section', 'rsvp', array(
			'page_id'    => get_the_ID(),
			'page'       => get_post(),
			'bg'         => 'mym-bg-cream',
			'section_id' => get_post_field( 'post_name', get_the_ID() ),
		) );
	endwhile;
endif;
get_footer();
