<?php
/**
 * Sektion-Template: Anreise & Karte.
 * Zeigt den Seiteninhalt links und das Karten-Embed (Customizer) rechts.
 * Seiten-Template: page-map.php
 *
 * @package MyM_Hochzeit
 */
$args       = wp_parse_args( $args ?? array(), array() );
$page_id    = (int) ( $args['page_id'] ?? 0 );
$page       = $args['page'] ?? get_post( $page_id );
$bg         = $args['bg'] ?? 'mym-bg-forest';
$section_id = sanitize_html_class( $args['section_id'] ?? '' );
$map_embed  = $args['map_embed'] ?? '';
$content    = apply_filters( 'the_content', $page->post_content );
$edit_url   = current_user_can( 'edit_post', $page_id ) ? get_edit_post_link( $page_id ) : '';
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="mym-section <?php echo esc_attr( $bg ); ?>" data-screen-label="<?php echo esc_attr( get_the_title( $page_id ) ); ?>">
	<div class="mym-travel-grid">
		<div class="mym-section-content">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $edit_url ) : ?>
			<a class="mym-edit-link" href="<?php echo esc_url( $edit_url ); ?>">&#9999; <?php esc_html_e( 'Seite bearbeiten', 'mym-hochzeit' ); ?></a>
			<?php endif; ?>
		</div>
		<div class="mym-map">
			<?php if ( $map_embed ) : ?>
				<iframe src="<?php echo esc_url( $map_embed ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php esc_attr_e( 'Karte', 'mym-hochzeit' ); ?>"></iframe>
			<?php else : ?>
				<svg viewBox="0 0 400 300" preserveAspectRatio="none" aria-hidden="true">
					<path d="M0,210 L70,160 L140,200 L220,130 L300,185 L400,120" fill="none" stroke="#7d9080" stroke-width="1.4"></path>
					<path d="M0,250 L90,215 L170,245 L260,190 L340,235 L400,200" fill="none" stroke="#a7b3a0" stroke-width="1.2"></path>
					<path d="M40,40 C120,20 160,90 260,60 C320,42 360,70 390,55" fill="none" stroke="#b7c0ad" stroke-width="1" stroke-dasharray="3 5"></path>
				</svg>
				<div class="center"><div class="pin"></div><span class="label"><?php esc_html_e( 'Veranstaltungsort', 'mym-hochzeit' ); ?></span></div>
				<span class="mapnote"><?php esc_html_e( 'Karte folgt', 'mym-hochzeit' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>
