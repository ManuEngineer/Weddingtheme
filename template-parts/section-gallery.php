<?php
/**
 * Sektion-Template: Foto-Galerie.
 * Zeigt den Seiteninhalt und einen CTA-Button zur Immich-Galerie.
 * Seiten-Template: page-gallery.php
 *
 * @package MyM_Hochzeit
 */
$args       = wp_parse_args( $args ?? array(), array() );
$page_id    = (int) ( $args['page_id'] ?? 0 );
$page       = $args['page'] ?? get_post( $page_id );
$bg         = $args['bg'] ?? 'mym-bg-forest';
$section_id = sanitize_html_class( $args['section_id'] ?? '' );
$immich_url = $args['immich_url'] ?? '';
$content    = apply_filters( 'the_content', $page->post_content );
$edit_url   = current_user_can( 'edit_post', $page_id ) ? get_edit_post_link( $page_id ) : '';
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="mym-section <?php echo esc_attr( $bg ); ?>" data-screen-label="<?php echo esc_attr( get_the_title( $page_id ) ); ?>">
	<div class="mym-section-content mym-wrap">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<?php if ( $immich_url ) : ?>
		<p class="mym-center" style="margin-top:24px">
			<a class="mym-gallery-cta" href="<?php echo esc_url( $immich_url ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( mym_s( 'mym_gallery_cta', 'To the gallery & upload' ) ); ?> →
			</a>
		</p>
		<?php endif; ?>
	</div>
	<?php if ( $edit_url ) : ?>
	<div class="mym-wrap" style="padding-top:0">
		<a class="mym-edit-link" href="<?php echo esc_url( $edit_url ); ?>">&#9999; <?php esc_html_e( 'Seite bearbeiten', 'mym-hochzeit' ); ?></a>
	</div>
	<?php endif; ?>
</section>
