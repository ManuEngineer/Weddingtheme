<?php
/**
 * Template Name: Anreise & Karte
 * Template Post Type: page
 *
 * @package MyM_Hochzeit
 */
get_header();
$map_embed = get_theme_mod( 'mym_map_embed', '' );
?>
<div class="mym-wrap" style="padding:60px 0">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<div class="mym-travel-grid" style="padding:0">
		<div class="mym-section-content"><?php the_content(); ?></div>
		<?php if ( $map_embed ) : ?>
		<div class="mym-map">
			<iframe src="<?php echo esc_url( $map_embed ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php esc_attr_e( 'Karte', 'mym-hochzeit' ); ?>"></iframe>
		</div>
		<?php endif; ?>
	</div>
<?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
