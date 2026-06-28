<?php
/**
 * Front Page - restliche Sektionen.
 * Anreise, Uebernachtung+Boerse, Galerie, Geschenke, FAQ.
 *
 * Jede Sektion prueft zuerst ob eine WP-Seite mit passendem Slug existiert.
 * Falls ja: Seitentitel als h2, Seiteninhalt als HTML (mym-section-content).
 * Falls nein: Fallback-Texte aus inc/content.php.
 *
 * Slugs:
 *   Anreise:      "anreise"       / "como-llegar"
 *   Uebernachtung:"uebernachtung" / "alojamiento"
 *   Galerie:      "galerie"       / "galeria"
 *   Geschenke:    "geschenke"     / "regalos"
 *   FAQ:          "faq"           / "faq"
 *
 * @package MyM_Hochzeit
 */
$args = wp_parse_args( $args ?? array(), array() );
$c    = isset( $args['c'] ) ? $args['c'] : mym_content();
$lang = isset( $args['lang'] ) ? $args['lang'] : mym_preview_lang();

$map_embed  = get_theme_mod( 'mym_map_embed', '' );
$immich_url = get_theme_mod( 'mym_immich_url', '' );
$offers     = mym_board_entries( 'offer' );
$seeks      = mym_board_entries( 'seek' );

/* Alle Sektion-Seiten auf einmal laden */
$sect_travel  = mym_section_page( 'anreise',       'como-llegar' );
$sect_stay    = mym_section_page( 'uebernachtung', 'alojamiento' );
$sect_gallery = mym_section_page( 'galerie',        'galeria' );
$sect_gifts   = mym_section_page( 'geschenke',      'regalos' );
$sect_faq     = mym_section_page( 'faq', 'preguntas' );

?>

<!-- ============ ANREISE ============ -->
<section id="travel" class="mym-section mym-bg-sand" data-screen-label="Anreise">
	<div class="mym-travel-grid">
		<div>
			<?php if ( $sect_travel ) : ?>
				<div class="mym-section-content">
					<?php echo $sect_travel['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<?php if ( $sect_travel['edit_url'] ) : ?><?php mym_edit_btn( $sect_travel ); ?><?php endif; ?>
			<?php else : ?>
				<div class="mym-kicker"><?php echo esc_html( $c['travel']['kicker'] ); ?></div>
				<h2 class="mym-h2"><?php echo esc_html( $c['travel']['title'] ); ?></h2>
				<p class="mym-travel-body"><?php echo esc_html( $c['travel']['body'] ); ?></p>
				<div class="mym-travel-legs">
					<?php foreach ( $c['travel']['legs'] as $leg ) : ?>
						<div class="mym-leg">
							<span class="dot"></span>
							<div>
								<h4><?php echo esc_html( $leg[0] ); ?></h4>
								<p><?php echo esc_html( $leg[1] ); ?></p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Karte erscheint immer (Embed oder Platzhalter) -->
		<div class="mym-map">
			<?php if ( $map_embed ) : ?>
				<iframe src="<?php echo esc_url( $map_embed ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( $c['travel']['maplabel'] ); ?>"></iframe>
			<?php else : ?>
				<svg viewBox="0 0 400 300" preserveAspectRatio="none" aria-hidden="true">
					<path d="M0,210 L70,160 L140,200 L220,130 L300,185 L400,120" fill="none" stroke="#7d9080" stroke-width="1.4"></path>
					<path d="M0,250 L90,215 L170,245 L260,190 L340,235 L400,200" fill="none" stroke="#a7b3a0" stroke-width="1.2"></path>
					<path d="M40,40 C120,20 160,90 260,60 C320,42 360,70 390,55" fill="none" stroke="#b7c0ad" stroke-width="1" stroke-dasharray="3 5"></path>
				</svg>
				<div class="center"><div class="pin"></div><span class="label"><?php echo esc_html( $c['travel']['maplabel'] ); ?></span></div>
				<span class="mapnote"><?php echo esc_html( $c['travel']['mapnote'] ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>

<!-- ============ UEBERNACHTUNG + BOERSE ============ -->
<section id="stay" class="mym-section mym-bg-cream" data-screen-label="Uebernachtung">
	<div class="mym-stay-inner">
		<?php if ( $sect_stay ) : ?>
			<div class="mym-section-content" style="margin-bottom:32px">
				<?php echo $sect_stay['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<?php mym_edit_btn( $sect_stay ); ?>
		<?php else : ?>
			<div class="mym-head-block">
				<div class="mym-kicker"><?php echo esc_html( $c['stay']['kicker'] ); ?></div>
				<h2 class="mym-h2"><?php echo esc_html( $c['stay']['title'] ); ?></h2>
			</div>
			<h3 class="mym-sub-label"><?php echo esc_html( $c['stay']['hotels_title'] ); ?></h3>
			<div class="mym-hotels">
				<?php foreach ( $c['stay']['hotels'] as $h ) : ?>
					<div class="mym-hotel">
						<div class="mym-hotel-head">
							<h4><?php echo esc_html( $h[0] ); ?></h4>
							<span class="tag"><?php echo esc_html( $h[2] ); ?></span>
						</div>
						<p><?php echo esc_html( $h[1] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="mym-note" style="margin-top:14px"><?php echo esc_html( $c['stay']['hotel_note'] ); ?></p>
		<?php endif; ?>

		<?php if ( get_theme_mod( 'mym_board_enabled', true ) ) : ?>
		<!-- Boerse (nur sichtbar, wenn im Customizer aktiviert) -->
		<div class="mym-board" id="boerse">
			<div class="mym-board-head">
				<h3><?php echo esc_html( $c['stay']['board_title'] ); ?></h3>
				<p><?php echo esc_html( $c['stay']['board_sub'] ); ?></p>
			</div>

			<form class="mym-board-form" id="mym-board-form">
				<!-- Zeile 1: Name + Art -->
				<div class="mym-board-row">
					<label class="mym-board-col-2"><?php echo esc_html( $c['stay']['f_name'] ); ?> <span class="req">*</span>
						<input type="text" name="name" required maxlength="80" placeholder="z.B. Familie Müller">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $c['stay']['f_type'] ); ?>
						<select name="type">
							<option value="offer"><?php echo esc_html( $c['stay']['offer'] ); ?></option>
							<option value="seek"><?php echo esc_html( $c['stay']['seek'] ); ?></option>
						</select>
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $c['stay']['f_places'] ); ?>
						<input type="number" name="places" min="1" max="20" value="1">
					</label>
				</div>
				<!-- Zeile 2: Ort + Zeitraum -->
				<div class="mym-board-row">
					<label class="mym-board-col-2"><?php echo esc_html( $c['stay']['f_location'] ); ?>
						<input type="text" name="location" maxlength="80" placeholder="<?php echo $lang === 'es' ? 'p.ej. centro' : 'z.B. Stadtzentrum'; ?>">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $c['stay']['f_date_from'] ); ?>
						<input type="text" name="date_from" maxlength="12" placeholder="TT.MM.JJJJ" pattern="\d{2}\.\d{2}\.\d{4}">
					</label>
					<label class="mym-board-col-1"><?php echo esc_html( $c['stay']['f_date_to'] ); ?>
						<input type="text" name="date_to" maxlength="12" placeholder="TT.MM.JJJJ" pattern="\d{2}\.\d{2}\.\d{4}">
					</label>
				</div>
				<!-- Zeile 3: Sprachen -->
				<div class="mym-board-row">
					<fieldset class="mym-board-col-full mym-board-langs">
						<legend><?php echo esc_html( $c['stay']['f_langs'] ); ?></legend>
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
					<label class="mym-board-col-full"><?php echo esc_html( $c['stay']['f_note'] ); ?>
						<textarea name="note" maxlength="300" rows="2" placeholder="<?php echo $lang === 'es' ? 'Breve presentación, características...' : 'Kurze Vorstellung, Besonderheiten...'; ?>"></textarea>
					</label>
				</div>
				<!-- Zeile 5: Kontakt (privat) + Submit -->
				<div class="mym-board-row">
					<label class="mym-board-col-3">
						<?php echo esc_html( $c['stay']['f_contact'] ); ?>
						<span class="mym-board-private"><?php echo esc_html( $c['stay']['f_contact_note'] ); ?></span>
						<input type="text" name="contact" maxlength="120" placeholder="name@beispiel.ch / +41 79 ...">
					</label>
					<div class="mym-board-col-1 mym-board-submit-wrap">
						<button type="submit" class="mym-board-submit"><?php echo esc_html( $c['stay']['add'] ); ?></button>
					</div>
				</div>
				<!-- Honeypot -->
				<label class="mym-hp" aria-hidden="true">Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
			</form>
			<p class="mym-board-msg" id="mym-board-msg" role="status" aria-live="polite"></p>

			<!-- Eintraege -->
			<div class="mym-board-cols">
				<!-- Angebote -->
				<div>
					<div class="mym-board-coltitle">
						<span class="d offer"></span>
						<span class="t"><?php echo esc_html( $c['stay']['offer'] ); ?></span>
					</div>
					<div class="mym-board-list" data-list="offer">
						<?php if ( ! $offers ) : ?>
							<p class="mym-board-empty" data-empty="offer"><?php echo esc_html( $c['stay']['empty_offer'] ); ?></p>
						<?php else : foreach ( $offers as $e ) : ?>
							<?php mym_board_entry_html( $e, 'offer' ); ?>
						<?php endforeach; endif; ?>
					</div>
				</div>
				<!-- Gesuche -->
				<div>
					<div class="mym-board-coltitle">
						<span class="d seek"></span>
						<span class="t"><?php echo esc_html( $c['stay']['seek'] ); ?></span>
					</div>
					<div class="mym-board-list" data-list="seek">
						<?php if ( ! $seeks ) : ?>
							<p class="mym-board-empty" data-empty="seek"><?php echo esc_html( $c['stay']['empty_seek'] ); ?></p>
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


<!-- ============ GALERIE ============ -->
<section id="gallery" class="mym-section mym-bg-sand" data-screen-label="Galerie">
	<?php if ( $sect_gallery ) : ?>
		<div class="mym-section-content mym-wrap">
			<?php echo $sect_gallery['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $immich_url ) : ?>
				<p class="mym-center" style="margin-top:24px"><a class="mym-gallery-cta" href="<?php echo esc_url( $immich_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $c['gallery']['cta'] ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php if ( $sect_gallery['edit_url'] ) : ?><div class="mym-wrap" style="padding-top:0"><?php mym_edit_btn( $sect_gallery ); ?></div><?php endif; ?>
	<?php else : ?>
		<div class="mym-gallery-inner">
			<div class="mym-gallery-head">
				<div class="mym-kicker"><?php echo esc_html( $c['gallery']['kicker'] ); ?></div>
				<h2 class="mym-h2"><?php echo esc_html( $c['gallery']['title'] ); ?></h2>
				<p><?php echo esc_html( $c['gallery']['body'] ); ?></p>
				<?php if ( $immich_url ) : ?>
					<a class="mym-gallery-cta" href="<?php echo esc_url( $immich_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $c['gallery']['cta'] ); ?></a>
				<?php endif; ?>
			</div>
			<div class="mym-gallery-grid">
				<?php for ( $i = 1; $i <= 4; $i++ ) : ?><div class="cell"><div class="mym-photo-ph">+</div></div><?php endfor; ?>
			</div>
			<p class="mym-note mym-center" style="margin-top:20px"><?php echo esc_html( $c['gallery']['note'] ); ?></p>
		</div>
	<?php endif; ?>
</section>

<!-- ============ GESCHENKE ============ -->
<section id="gifts" class="mym-section mym-bg-cream" data-screen-label="Geschenke">
	<?php if ( $sect_gifts ) : ?>
		<div class="mym-section-content mym-wrap">
			<?php echo $sect_gifts['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $sect_gifts['edit_url'] ) : ?><div class="mym-wrap" style="padding-top:0"><?php mym_edit_btn( $sect_gifts ); ?></div><?php endif; ?>
	<?php else : ?>
		<div class="mym-gifts-inner">
			<div class="mym-kicker"><?php echo esc_html( $c['gifts']['kicker'] ); ?></div>
			<h2 class="mym-h2"><?php echo esc_html( $c['gifts']['title'] ); ?></h2>
			<p class="mym-gifts-body"><?php echo esc_html( $c['gifts']['body'] ); ?></p>
			<div class="mym-gifts-grid">
				<?php foreach ( $c['gifts']['items'] as $g ) : ?>
					<div class="mym-gift">
						<span class="num"><?php echo esc_html( $g[0] ); ?></span>
						<h3><?php echo esc_html( $g[1] ); ?></h3>
						<p><?php echo esc_html( $g[2] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="mym-note" style="margin-top:24px"><?php echo esc_html( $c['gifts']['note'] ); ?></p>
		</div>
	<?php endif; ?>
</section>

<!-- ============ FAQ ============ -->
<section id="faq" class="mym-section mym-bg-forest mym-faq" data-screen-label="FAQ">
	<?php if ( $sect_faq ) : ?>
		<div class="mym-section-content mym-wrap">
			<?php echo $sect_faq['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $sect_faq['edit_url'] ) : ?><div class="mym-wrap" style="padding-top:0"><?php mym_edit_btn( $sect_faq ); ?></div><?php endif; ?>
		<div class="mym-wrap"><p class="mym-faq-disclaimer"><?php echo esc_html( $c['faq']['disclaimer'] ); ?></p></div>
	<?php else : ?>
		<div class="mym-faq-inner">
			<div class="mym-head-block">
				<div class="mym-kicker"><?php echo esc_html( $c['faq']['kicker'] ); ?></div>
				<h2 class="mym-h2"><?php echo esc_html( $c['faq']['title'] ); ?></h2>
			</div>
			<div class="mym-faq-list">
				<?php foreach ( $c['faq']['items'] as $f ) : ?>
					<details class="mym-faq-item">
						<summary class="mym-faq-q">
							<span class="q"><?php echo esc_html( $f[0] ); ?></span>
							<span class="plus" aria-hidden="true">+</span>
						</summary>
						<div class="mym-faq-a">
							<p><?php echo esc_html( $f[1] ); ?></p>
						</div>
					</details>
				<?php endforeach; ?>
			</div>
			<p class="mym-faq-disclaimer"><?php echo esc_html( $c['faq']['disclaimer'] ); ?></p>
		</div>
	<?php endif; ?>
</section>
