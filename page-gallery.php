<?php
/**
 * Template Name: Foto-Galerie
 * Template Post Type: page
 *
 * @package MyM_Hochzeit
 */
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
