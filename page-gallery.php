<?php
/**
 * Template Name: Foto-Galerie
 * Template Post Type: page
 *
 * @package MyM_Hochzeit
 */
get_header();
$immich_url = get_theme_mod( 'mym_immich_url', '' );
?>
<div class="mym-wrap" style="padding:60px 0">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="mym-section-content"><?php the_content(); ?></div>
	<?php if ( $immich_url ) : ?>
	<p style="margin-top:24px">
		<a class="mym-gallery-cta" href="<?php echo esc_url( $immich_url ); ?>" target="_blank" rel="noopener">
			<?php echo esc_html( mym_s( 'mym_gallery_cta', 'To the gallery & upload' ) ); ?> →
		</a>
	</p>
	<?php endif; ?>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
