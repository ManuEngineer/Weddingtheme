<?php
/**
 * 404
 * @package MyM_Hochzeit
 */
get_header(); ?>
<div class="mym-page mym-center">
	<h1>404</h1>
	<p><?php esc_html_e( 'Diese Seite gibt es nicht. Zurueck zur Startseite:', 'mym-hochzeit' ); ?></p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></p>
</div>
<?php get_footer(); ?>
