<?php
/**
 * Sektion-Template: Unterkunftsbörse.
 * Zeigt den Seiteninhalt (Übernachtungsempfehlungen) und darunter
 * das Börsen-Formular + Einträge.
 * Seiten-Template: page-board.php
 *
 * Sicherheitshinweis: mym_contact (E-Mail/Tel.) wird NIEMALS an den
 * Browser zurückgegeben — mym_board_entries() lässt es bewusst aus.
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

$offers = mym_board_entries( 'offer' );
$seeks  = mym_board_entries( 'seek' );

/* Strings */
$s_offer       = mym_s( 'mym_board_offer',          'We offer accommodation' );
$s_seek        = mym_s( 'mym_board_seek',            'We need accommodation' );
$s_name        = mym_s( 'mym_board_f_name',          'Name' );
$s_type        = mym_s( 'mym_board_f_type',          'Type' );
$s_places      = mym_s( 'mym_board_f_places',        'Spots' );
$s_location    = mym_s( 'mym_board_f_location',      'Location' );
$s_from        = mym_s( 'mym_board_f_from',          'From' );
$s_to          = mym_s( 'mym_board_f_to',            'To' );
$s_langs       = mym_s( 'mym_board_f_langs',         'Languages spoken' );
$s_note        = mym_s( 'mym_board_f_note',          'Brief description / note' );
$s_contact     = mym_s( 'mym_board_f_contact',       'Contact (email or phone)' );
$s_contact_n   = mym_s( 'mym_board_f_contact_note',  '— not published, for us only' );
$s_submit      = mym_s( 'mym_board_f_submit',        'Submit' );
$s_empty_off   = mym_s( 'mym_board_empty_offer',     'No offers yet — be the first!' );
$s_empty_seek  = mym_s( 'mym_board_empty_seek',      'No requests yet.' );
$s_loc_ph      = mym_s( 'mym_board_loc_ph',          'e.g. city centre' );
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

		<?php if ( get_theme_mod( 'mym_board_enabled', true ) ) : ?>
		<!-- Börse -->
		<div class="mym-board" id="boerse">
			<form class="mym-board-form" id="mym-board-form">
				<!-- Zeile 1: Name + Art + Plätze -->
				<div class="mym-board-row">
					<label class="mym-board-col-2"><?php echo esc_html( $s_name ); ?> <span class="req">*</span>
						<input type="text" name="name" required maxlength="80" placeholder="z.B. Familie Müller">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $s_type ); ?>
						<select name="type">
							<option value="offer"><?php echo esc_html( $s_offer ); ?></option>
							<option value="seek"><?php echo esc_html( $s_seek ); ?></option>
						</select>
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $s_places ); ?>
						<input type="number" name="places" min="1" max="20" value="1">
					</label>
				</div>
				<!-- Zeile 2: Ort + Zeitraum -->
				<div class="mym-board-row">
					<label class="mym-board-col-2"><?php echo esc_html( $s_location ); ?>
						<input type="text" name="location" maxlength="80" placeholder="<?php echo esc_attr( $s_loc_ph ); ?>">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $s_from ); ?>
						<input type="text" name="date_from" maxlength="12" placeholder="TT.MM.JJJJ" pattern="\d{2}\.\d{2}\.\d{4}">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $s_to ); ?>
						<input type="text" name="date_to" maxlength="12" placeholder="TT.MM.JJJJ" pattern="\d{2}\.\d{2}\.\d{4}">
					</label>
				</div>
				<!-- Zeile 3: Sprachen -->
				<div class="mym-board-row">
					<fieldset class="mym-board-col-full mym-board-langs">
						<legend><?php echo esc_html( $s_langs ); ?></legend>
						<div class="mym-lang-check-row">
							<label class="mym-lang-check"><input type="checkbox" name="langs[]" value="de"> Deutsch</label>
							<label class="mym-lang-check"><input type="checkbox" name="langs[]" value="es"> Español</label>
							<label class="mym-lang-check"><input type="checkbox" name="langs[]" value="fr"> Français</label>
							<label class="mym-lang-check"><input type="checkbox" name="langs[]" value="en"> English</label>
						</div>
					</fieldset>
				</div>
				<!-- Zeile 4: Beschreibung -->
				<div class="mym-board-row">
					<label class="mym-board-col-full"><?php echo esc_html( $s_note ); ?>
						<textarea name="note" maxlength="300" rows="2"></textarea>
					</label>
				</div>
				<!-- Zeile 5: Kontakt (privat) + Submit -->
				<div class="mym-board-row">
					<label class="mym-board-col-3">
						<?php echo esc_html( $s_contact ); ?>
						<span class="mym-board-private"><?php echo esc_html( $s_contact_n ); ?></span>
						<input type="text" name="contact" maxlength="120" placeholder="name@beispiel.ch / +41 79 ...">
					</label>
					<div class="mym-board-col-1 mym-board-submit-wrap">
						<button type="submit" class="mym-board-submit"><?php echo esc_html( $s_submit ); ?></button>
					</div>
				</div>
				<!-- Honeypot -->
				<label class="mym-hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
			</form>
			<p class="mym-board-msg" id="mym-board-msg" role="status" aria-live="polite"></p>

			<!-- Einträge -->
			<div class="mym-board-cols">
				<!-- Angebote -->
				<div>
					<div class="mym-board-coltitle">
						<span class="d offer"></span>
						<span class="t"><?php echo esc_html( $s_offer ); ?></span>
					</div>
					<div class="mym-board-list" data-list="offer">
						<?php if ( ! $offers ) : ?>
							<p class="mym-board-empty" data-empty="offer"><?php echo esc_html( $s_empty_off ); ?></p>
						<?php else : foreach ( $offers as $e ) : ?>
							<?php mym_board_entry_html( $e, 'offer' ); ?>
						<?php endforeach; endif; ?>
					</div>
				</div>
				<!-- Gesuche -->
				<div>
					<div class="mym-board-coltitle">
						<span class="d seek"></span>
						<span class="t"><?php echo esc_html( $s_seek ); ?></span>
					</div>
					<div class="mym-board-list" data-list="seek">
						<?php if ( ! $seeks ) : ?>
							<p class="mym-board-empty" data-empty="seek"><?php echo esc_html( $s_empty_seek ); ?></p>
						<?php else : foreach ( $seeks as $e ) : ?>
							<?php mym_board_entry_html( $e, 'seek' ); ?>
						<?php endforeach; endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; /* mym_board_enabled */ ?>
	</div>
</section>
