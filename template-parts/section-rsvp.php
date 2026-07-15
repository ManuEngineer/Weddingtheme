<?php
/**
 * Sektion-Template: RSVP (Zu-/Absage).
 * Zeigt den Seiteninhalt und darunter das Anmeldeformular.
 * Seiten-Template: page-rsvp.php
 *
 * Bearbeiten via persönlichem Link: ?rsvp_token=... in der URL lädt die
 * bestehende Anmeldung und befüllt das Formular vor (siehe main.js).
 * Sicherheitshinweis: E-Mail/Telefon werden NIE ans Frontend zurückgegeben
 * ausser als Vorbefüllung der EIGENEN Anmeldung bei gültigem Token.
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

$enabled  = get_theme_mod( 'mym_rsvp_enabled', true );
$deadline = get_theme_mod( 'mym_rsvp_deadline', '' );
$deadline_ts = $deadline ? strtotime( $deadline . ' 23:59:59' ) : 0;
$past_deadline = $deadline_ts && time() > $deadline_ts;

/* Bearbeiten-Modus: gültiger Token in der URL? */
$edit_token = isset( $_GET['rsvp_token'] ) ? sanitize_text_field( wp_unslash( $_GET['rsvp_token'] ) ) : '';
$edit_entry = $edit_token ? mym_rsvp_get_by_token( $edit_token ) : null;
$prefill    = $edit_entry ? mym_rsvp_get_entry( $edit_entry->ID ) : null;

/* Strings */
$s_title      = mym_s( 'mym_rsvp_title',        'Are you coming?' );
$s_intro      = mym_s( 'mym_rsvp_intro',        'Please let us know by the date below whether you can make it.' );
$s_name       = mym_s( 'mym_rsvp_f_name',       'Contact name' );
$s_email      = mym_s( 'mym_rsvp_f_email',      'Email' );
$s_phone      = mym_s( 'mym_rsvp_f_phone',      'Phone' );
$s_phone_hint = mym_s( 'mym_rsvp_f_phone_hint', 'Please include the country code, e.g. +41 79 123 45 67.' );
$s_yes        = mym_s( 'mym_rsvp_yes',          'Yes, we\'ll be there' );
$s_no         = mym_s( 'mym_rsvp_no',           'Sorry, we can\'t make it' );
$s_guests     = mym_s( 'mym_rsvp_f_guests',     'Who\'s coming?' );
$s_contact_hint = mym_s( 'mym_rsvp_contact_hint', 'The first person listed here is our contact for questions.' );
$s_add_guest  = mym_s( 'mym_rsvp_f_add_guest',  '+ Add person' );
$s_remove     = mym_s( 'mym_rsvp_f_remove',     'Remove' );
$s_g_name     = mym_s( 'mym_rsvp_f_g_name',     'Name' );
$s_g_child    = mym_s( 'mym_rsvp_f_g_child',    'Child' );
$s_g_veggie   = mym_s( 'mym_rsvp_f_g_veggie',   'Vegetarian/vegan' );
$s_g_allerg   = mym_s( 'mym_rsvp_f_g_allerg',   'Allergies / notes' );
$s_g_langs    = mym_s( 'mym_rsvp_f_g_langs',    'Languages spoken' );
$s_message    = mym_s( 'mym_rsvp_f_message',    'Message to the couple' );
$s_submit     = mym_s( 'mym_rsvp_f_submit',     'Send RSVP' );
$s_update     = mym_s( 'mym_rsvp_f_update',     'Update RSVP' );
$s_deadline_p = mym_s( 'mym_rsvp_deadline_note', 'Please reply by %s.' );
$s_closed     = mym_s( 'mym_rsvp_closed',        'The RSVP deadline has passed. If you still need to let us know something, please contact us directly.' );

/* Karte folgt der alternierenden Sektionsfarbe: dunkel auf Wald-Sektion, hell/sandfarben auf Creme-Sektion. */
$rsvp_card_theme = ( $bg === 'mym-bg-forest' ) ? 'mym-rsvp-dark' : 'mym-rsvp-light';
?>
<section id="<?php echo esc_attr( $section_id ); ?>" class="mym-section <?php echo esc_attr( $bg ); ?>" data-screen-label="<?php echo esc_attr( get_the_title( $page_id ) ); ?>">
	<div class="mym-wrap">
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

		<?php if ( $enabled ) : ?>
		<div class="mym-rsvp <?php echo esc_attr( $rsvp_card_theme ); ?>" id="mym-rsvp-box" data-token="<?php echo esc_attr( $edit_token ); ?>">
			<?php if ( $past_deadline && ! $prefill ) : ?>
				<p class="mym-rsvp-closed"><?php echo esc_html( $s_closed ); ?></p>
			<?php else : ?>
				<div class="mym-rsvp-head">
					<h2><?php echo esc_html( $s_title ); ?></h2>
					<p><?php echo esc_html( $s_intro ); ?></p>
					<?php if ( $deadline_ts && ! $prefill ) : ?>
					<p class="mym-rsvp-deadline"><?php echo esc_html( sprintf( $s_deadline_p, date_i18n( 'j. F Y', $deadline_ts ) ) ); ?></p>
					<?php endif; ?>
				</div>
				<form class="mym-rsvp-form" id="mym-rsvp-form">
					<div class="mym-rsvp-row">
						<fieldset class="mym-rsvp-col-full mym-rsvp-status">
							<label class="mym-rsvp-radio"><input type="radio" name="status" value="yes" <?php checked( ( $prefill['status'] ?? 'yes' ), 'yes' ); ?>> <?php echo esc_html( $s_yes ); ?></label>
							<label class="mym-rsvp-radio"><input type="radio" name="status" value="no" <?php checked( ( $prefill['status'] ?? '' ), 'no' ); ?>> <?php echo esc_html( $s_no ); ?></label>
						</fieldset>
					</div>

					<div class="mym-rsvp-row" data-declined-name-wrap>
						<label class="mym-rsvp-col-full"><span class="mym-rsvp-label-text"><?php echo esc_html( $s_name ); ?> <span class="req">*</span></span>
							<input type="text" name="name" maxlength="80" value="<?php echo esc_attr( $prefill['name'] ?? '' ); ?>">
						</label>
					</div>

					<div class="mym-rsvp-guests-wrap" data-guests-wrap>
						<label class="mym-rsvp-guests-label"><?php echo esc_html( $s_guests ); ?></label>
						<p class="mym-rsvp-contact-hint"><?php echo esc_html( $s_contact_hint ); ?></p>
						<div id="mym-rsvp-guests" data-guest-list></div>
						<button type="button" class="mym-rsvp-add-guest" id="mym-rsvp-add-guest"><?php echo esc_html( $s_add_guest ); ?></button>
					</div>

					<div class="mym-rsvp-row">
						<label class="mym-rsvp-col-1"><span class="mym-rsvp-label-text"><?php echo esc_html( $s_email ); ?> <span class="req">*</span></span>
							<input type="email" name="email" required maxlength="120" value="<?php echo esc_attr( $prefill['email'] ?? '' ); ?>">
						</label>
						<label class="mym-rsvp-col-1"><span class="mym-rsvp-label-text"><?php echo esc_html( $s_phone ); ?> <span class="req">*</span></span>
							<input type="tel" name="phone" required maxlength="40" placeholder="+41 79 123 45 67" pattern="\+[0-9 ()-]{7,20}" title="<?php echo esc_attr( $s_phone_hint ); ?>" value="<?php echo esc_attr( $prefill['phone'] ?? '' ); ?>">
						</label>
					</div>
					<p class="mym-rsvp-phone-hint"><?php echo esc_html( $s_phone_hint ); ?></p>

					<div class="mym-rsvp-row">
						<label class="mym-rsvp-col-full"><?php echo esc_html( $s_message ); ?>
							<textarea name="message" maxlength="500" rows="2"><?php echo esc_textarea( $prefill['message'] ?? '' ); ?></textarea>
						</label>
					</div>

					<label class="mym-hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>

					<div class="mym-rsvp-submit-wrap">
						<button type="submit" class="mym-rsvp-submit"><?php echo esc_html( $prefill ? $s_update : $s_submit ); ?></button>
					</div>
				</form>
				<p class="mym-rsvp-msg" id="mym-rsvp-msg" role="status" aria-live="polite"></p>
			<?php endif; ?>
		</div>

		<!-- Vorlage für eine Gast-Zeile, per JS geklont (siehe main.js) -->
		<template id="mym-rsvp-guest-tpl">
			<div class="mym-rsvp-guest-row">
				<label class="mym-rsvp-col-2"><?php echo esc_html( $s_g_name ); ?>
					<input type="text" data-g="name" maxlength="80">
				</label>
				<label class="mym-rsvp-check"><input type="checkbox" data-g="child"> <?php echo esc_html( $s_g_child ); ?></label>
				<label class="mym-rsvp-check"><input type="checkbox" data-g="veggie"> <?php echo esc_html( $s_g_veggie ); ?></label>
				<label class="mym-rsvp-col-2"><?php echo esc_html( $s_g_allerg ); ?>
					<input type="text" data-g="allergies" maxlength="140">
				</label>
				<fieldset class="mym-rsvp-guest-langs">
					<legend><?php echo esc_html( $s_g_langs ); ?></legend>
					<label class="mym-rsvp-check"><input type="checkbox" data-g="langs" value="de"> Deutsch</label>
					<label class="mym-rsvp-check"><input type="checkbox" data-g="langs" value="es"> Español</label>
					<label class="mym-rsvp-check"><input type="checkbox" data-g="langs" value="fr"> Français</label>
					<label class="mym-rsvp-check"><input type="checkbox" data-g="langs" value="en"> English</label>
				</fieldset>
				<button type="button" class="mym-rsvp-remove-guest" data-remove-guest><?php echo esc_html( $s_remove ); ?></button>
			</div>
		</template>

		<?php if ( $prefill ) : ?>
		<script type="application/json" id="mym-rsvp-prefill"><?php echo wp_json_encode( $prefill['guests'] ); ?></script>
		<?php endif; ?>
		<?php endif; /* mym_rsvp_enabled */ ?>
	</div>
</section>
