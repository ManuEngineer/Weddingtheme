<?php
/**
 * Fallback-Template (z.B. Blog-Index). Die Hochzeitsseite nutzt front-page.php.
 * @package MyM_Hochzeit
 */
get_header(); ?>
<div class="mym-page">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="mym-entry"><?php the_content(); ?></div>
		</article>
	<?php endwhile; the_posts_navigation(); else : ?>
		<h1><?php esc_html_e( 'Nichts gefunden', 'mym-hochzeit' ); ?></h1>
		<p><?php esc_html_e( 'Hier gibt es noch keine Inhalte.', 'mym-hochzeit' ); ?></p>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
