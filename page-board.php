<?php
/**
 * Template Name: Unterkunftsbörse
 * Template Post Type: page
 *
 * Direktaufruf der Seite: zeigt nur den Seiteninhalt.
 * Auf der Startseite rendert front-page.php die volle Börse.
 *
 * @package MyM_Hochzeit
 */
get_header();
?>
<div class="mym-wrap" style="padding:60px 0">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="mym-section-content"><?php the_content(); ?></div>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
