<?php
/**
 * Sektion-Template: Musikwünsche.
 * Zeigt den Seiteninhalt und darunter das Formular zum Einreichen von
 * Songwünschen (Titel/Interpret, beliebig viele pro Absenden). Rein
 * privat wie RSVP — nichts wird öffentlich angezeigt.
 * Seiten-Template: page-songs.php
 *
 * @package MyM_Hochzeit
 */
$args       = wp_parse_args( $args ?? array(), array() );
$page_id    = (int) ( $args['page_id'] ?? 0 );
$page       = $args['page'] ?? get_post( $page_id );
$bg         = $args['bg'] ?? 'mym-bg-cream';
$section_id = sanitize_html_class( $args['section_id'] ?? '' );
$content    = apply_filters( 'the_content', $page->post_content );
$edit_url   = current_user_can( 'edit_post', $page_id ) ? get_edit_post_link( $page_id ) : '';

/* Card-Theme je nach Sektions-Hintergrund — wie bei RSVP: kein fest dunkler
 * äusserer Container, sondern .mym-songs-dark/-light je nach $bg. */
$songs_card_theme = ( $bg === 'mym-bg-forest' ) ? 'mym-songs-dark' : 'mym-songs-light';

/* Strings */
$s_title     = mym_s( 'mym_songs_title',      'Song requests' );
$s_intro     = mym_s( 'mym_songs_intro',      'What song can\'t be missing at our party? Send us your wishes — feel free to add more than one.' );
$s_name      = mym_s( 'mym_songs_f_name',     'Your name' );
$s_name_hint = mym_s( 'mym_songs_f_name_hint', '(optional)' );
$s_f_title   = mym_s( 'mym_songs_f_title',    'Song title' );
$s_f_artist  = mym_s( 'mym_songs_f_artist',   'Artist' );
$s_add       = mym_s( 'mym_songs_f_add',      '+ Add another song' );
$s_remove    = mym_s( 'mym_songs_f_remove',   'Remove' );
$s_submit    = mym_s( 'mym_songs_f_submit',   'Send wishes' );
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="mym-section <?php echo esc_attr( $bg ); ?>" data-screen-label="<?php echo esc_attr( get_the_title( $page_id ) ); ?>">
	<div class="mym-stay-inner">
		<?php if ( $content ) : ?>
		<div class="mym-section-content" style="margin-bottom:32px">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $edit_url ) : ?>
		<div style="margin-bottom:24px">
			<a class="mym-edit-link" href="<?php echo esc_url( $edit_url ); ?>">&#9999; <?php esc_html_e( 'Seite bearbeiten', 'mym-hochzeit' ); ?></a>
		</div>
		<?php endif; ?>
		<?php endif; ?>

		<?php if ( get_theme_mod( 'mym_songs_enabled', true ) ) : ?>
		<div class="mym-songs <?php echo esc_attr( $songs_card_theme ); ?>" id="musikwuensche">
			<h3 class="mym-songs-title"><?php echo esc_html( $s_title ); ?></h3>
			<p class="mym-songs-intro"><?php echo esc_html( $s_intro ); ?></p>

			<form class="mym-songs-form" id="mym-songs-form">
				<label class="mym-songs-name-field">
					<?php echo esc_html( $s_name ); ?> <span class="hint"><?php echo esc_html( $s_name_hint ); ?></span>
					<input type="text" name="name" maxlength="80">
				</label>

				<div class="mym-songs-list" id="mym-songs-list"></div>

				<template id="mym-songs-row-tpl">
					<div class="mym-songs-row">
						<label class="mym-songs-col-2"><?php echo esc_html( $s_f_title ); ?>
							<input type="text" data-s="title" maxlength="120">
						</label>
						<label class="mym-songs-col-2"><?php echo esc_html( $s_f_artist ); ?>
							<input type="text" data-s="artist" maxlength="120">
						</label>
						<button type="button" class="mym-songs-remove" data-remove-song><?php echo esc_html( $s_remove ); ?></button>
					</div>
				</template>

				<button type="button" class="mym-songs-add" id="mym-songs-add"><?php echo esc_html( $s_add ); ?></button>

				<div class="mym-songs-submit-wrap">
					<button type="submit" class="mym-songs-submit"><?php echo esc_html( $s_submit ); ?></button>
				</div>

				<!-- Honeypot -->
				<label class="mym-hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
			</form>
			<p class="mym-songs-msg" id="mym-songs-msg" role="status" aria-live="polite"></p>
		</div>
		<?php endif; /* mym_songs_enabled */ ?>
	</div>
</section>
