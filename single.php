<?php
/**
 * Einzelner Beitrag.
 * @package MyM_Hochzeit
 */
get_header(); ?>
<div class="mym-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="mym-entry"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</div>
<?php get_footer(); ?>
