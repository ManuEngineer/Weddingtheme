<?php
/**
 * Template Name: Musikwünsche
 * Template Post Type: page
 *
 * Direktaufruf der Seite: zeigt nur den Seiteninhalt.
 * Auf der Startseite rendert front-page.php die volle Sektion mit Formular.
 * Ist der Hauptschalter im Customizer aus, ist die Seite auch per
 * Direktaufruf nicht erreichbar (wie aus dem Menü ausgeblendet).
 *
 * @package MyM_Hochzeit
 */
if ( ! get_theme_mod( 'mym_songs_enabled', true ) ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}
get_header();
?>
<div class="mym-section">
<div class="mym-wrap">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="mym-section-content"><?php the_content(); ?></div>
<?php endwhile; endif; ?>
</div>
</div>
<?php get_footer(); ?>
