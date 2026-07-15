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
 * @package MyM_Hochzeit
 */
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
