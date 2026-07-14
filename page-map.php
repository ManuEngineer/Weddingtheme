<?php
/**
 * Template Name: Anreise & Karte
 * Template Post Type: page
 *
 * Breiter Seitenrahmen (.mym-wrap, 1040px) statt des schmalen page.php-Fallbacks
 * (.mym-page, 760px), damit ein per Block-Pattern eingefügtes ".mym-travel-grid"
 * (Text + Karte nebeneinander) genug Platz hat. Die Karte selbst kommt über das
 * Block-Pattern "Karte mit Text" im Seiteninhalt, nicht mehr über dieses Template.
 *
 * @package MyM_Hochzeit
 */
get_header(); ?>
<div class="mym-section">
<div class="mym-wrap">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="mym-entry"><?php the_content(); ?></div>
<?php endwhile; endif; ?>
</div>
</div>
<?php get_footer(); ?>
