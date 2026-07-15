<?php
/**
 * Front Page v2.0 — menügesteuerter Onepager.
 *
 * Sektionen kommen aus dem primären Navigationsmenü. Jeder Menüpunkt
 * muss auf eine WordPress-Seite zeigen. Der Sektionstyp wird über das
 * Seiten-Template bestimmt:
 *   (Standard/page-map.php) → section-default  (reiner Seiteninhalt;
 *                              page-map.php hat kein eigenes Section-Template,
 *                              die Karte kommt über das "Karte mit Text"-Block-
 *                              Pattern im Seiteninhalt selbst)
 *   page-board.php          → section-board    (Seiteninhalt + Unterkunftsbörse)
 *   page-gallery.php        → section-gallery  (Seiteninhalt + Galerie-CTA)
 *   page-rsvp.php           → section-rsvp     (Seiteninhalt + Zu-/Absage-Formular)
 *
 * Hintergründe wechseln automatisch: Index 0,2,4 = forest · 1,3,5 = cream.
 *
 * @package MyM_Hochzeit
 */
get_header();

$couple       = mym_couple();
$variant      = mym_opt( 'mym_hero_variant', 'horizont' );
if ( isset( $_GET['hero'] ) && in_array( $_GET['hero'], array( 'horizont', 'editorial', 'bogen' ), true ) ) {
	$variant = sanitize_text_field( wp_unslash( $_GET['hero'] ) );
}
$featured   = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
$hero_photo = get_theme_mod( 'mym_hero_photo', '' );
$hero_img   = $hero_photo ?: $featured;

$wedding_date = mym_opt( 'mym_wedding_date', '' );
$show_dates   = get_theme_mod( 'mym_dates_visible', true );
$dates_raw    = get_theme_mod( 'mym_dates_list', '' );
$dates        = array_filter( array_map( 'trim', explode( "\n", $dates_raw ) ) );

$conn       = mym_opt( 'mym_connector', '&' ) ?: '&';
$place      = mym_opt( 'mym_place', '' );
$couple_alt = trim( $couple['a'] . ' ' . $conn . ' ' . $couple['b'], " $conn" );

$ts             = strtotime( $wedding_date );
$exact_date     = get_theme_mod( 'mym_date_exact', false );
$hero_when      = $ts ? ( $exact_date ? date_i18n( 'j. F Y', $ts ) : date_i18n( 'F Y', $ts ) ) : '';
$show_countdown = get_theme_mod( 'mym_countdown_enabled', true );
$cal            = mym_calendar_links();
$rsvp_slug      = get_theme_mod( 'mym_rsvp_cta_enabled', true ) ? mym_rsvp_page_slug() : '';

$lang = mym_current_lang();
$day_names_map = array(
	'de' => array( 0 => 'So', 1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa' ),
	'es' => array( 0 => 'Do', 1 => 'Lu', 2 => 'Ma', 3 => 'Mi', 4 => 'Ju', 5 => 'Vi', 6 => 'Sá' ),
);
$day_names = isset( $day_names_map[ $lang ] )
	? $day_names_map[ $lang ]
	: array( 0 => 'Su', 1 => 'Mo', 2 => 'Tu', 3 => 'We', 4 => 'Th', 5 => 'Fr', 6 => 'Sa' );

if ( ! function_exists( 'mym_photo' ) ) :
function mym_photo( $url, $alt = '' ) {
	if ( $url ) {
		return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
	}
	return '<div class="mym-photo-ph">' . esc_html__( 'Foto folgt · Foto pronto', 'mym-hochzeit' ) . '</div>';
}
endif;

/* ---------- Sektionen aus Menü ---------- */
$menu_locs   = get_nav_menu_locations();
$raw_items   = array();
if ( ! empty( $menu_locs['primary'] ) ) {
	$raw_items = wp_get_nav_menu_items( $menu_locs['primary'] );
}
$raw_items     = is_array( $raw_items ) ? $raw_items : array();
$section_items = array_values( array_filter( $raw_items, function ( $item ) {
	return $item->object === 'page' && (int) $item->menu_item_parent === 0;
} ) );

$eyebrow   = mym_s( 'mym_hero_eyebrow', 'We are getting married' );
$cd_save   = mym_s( 'mym_hero_save',   'Save the date' );
$cd_until  = mym_s( 'mym_hero_until',  'until the big day' );
$cd_note   = mym_s( 'mym_hero_dates_note', 'Save one of these dates:' );
?>

<!-- ============ HERO ============ -->
<section id="top" class="mym-hero" data-variant="<?php echo esc_attr( $variant ); ?>">

	<!-- Horizont -->
	<div class="mym-hero-pane mym-hero--horizont" data-pane="horizont" style="<?php echo $variant === 'horizont' ? '' : 'display:none'; ?>">
		<div class="mym-hero-inner">
			<div class="mym-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $couple['a'] ); ?> <br class="mym-hero-break"><span class="conn"><?php echo esc_html( $conn ); ?></span> <br class="mym-hero-break"><?php echo esc_html( $couple['b'] ); ?></h1>
			<div class="mym-hero-rule"><span class="line"></span><span class="mym-hero-date"><?php echo esc_html( $hero_when ); ?></span><span class="line"></span></div>
			<div class="mym-hero-place"><?php echo esc_html( $place ); ?></div>
		</div>
		<?php if ( $rsvp_slug ) : ?>
		<p class="mym-rsvp-cta-wrap mym-rsvp-cta-wrap--horizont"><a class="mym-gallery-cta" href="#<?php echo esc_attr( $rsvp_slug ); ?>"><?php echo esc_html( mym_s( 'mym_rsvp_cta', 'RSVP now' ) ); ?></a></p>
		<?php endif; ?>
		<?php include get_template_directory() . '/assets/svg/hero-mountains.svg'; ?>
	</div>

	<!-- Editorial -->
	<div class="mym-hero-pane mym-hero--editorial" data-pane="editorial" style="<?php echo $variant === 'editorial' ? '' : 'display:none'; ?>">
		<div class="col-text">
			<div class="mym-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $couple['a'] ); ?><br><span class="conn"><?php echo esc_html( $conn ); ?></span><br><?php echo esc_html( $couple['b'] ); ?></h1>
			<div class="rule"></div>
			<div class="d"><?php echo esc_html( $hero_when ); ?></div>
			<div class="pl"><?php echo esc_html( $place ); ?></div>
			<?php if ( $rsvp_slug ) : ?>
			<p class="mym-rsvp-cta-wrap"><a class="mym-gallery-cta" href="#<?php echo esc_attr( $rsvp_slug ); ?>"><?php echo esc_html( mym_s( 'mym_rsvp_cta', 'RSVP now' ) ); ?></a></p>
			<?php endif; ?>
		</div>
		<div class="col-photo"><div class="frame"><?php echo mym_photo( $hero_img, $couple_alt ); // phpcs:ignore ?></div></div>
	</div>

	<!-- Bogen -->
	<div class="mym-hero-pane mym-hero--bogen" data-pane="bogen" style="<?php echo $variant === 'bogen' ? '' : 'display:none'; ?>">
		<div class="mym-hero-inner">
			<div class="mym-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $couple['a'] ); ?> <span class="conn"><?php echo esc_html( $conn ); ?></span> <?php echo esc_html( $couple['b'] ); ?></h1>
			<div class="arch"><?php echo mym_photo( $hero_img, $couple_alt ); // phpcs:ignore ?></div>
			<div class="mym-hero-rule"><span class="line"></span><span class="mym-hero-date"><?php echo esc_html( $hero_when ); ?></span><span class="line"></span></div>
			<div class="mym-hero-place"><?php echo esc_html( $place ); ?></div>
			<?php if ( $rsvp_slug ) : ?>
			<p class="mym-rsvp-cta-wrap"><a class="mym-gallery-cta" href="#<?php echo esc_attr( $rsvp_slug ); ?>"><?php echo esc_html( mym_s( 'mym_rsvp_cta', 'RSVP now' ) ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>

</section>

<!-- ============ COUNTDOWN ============ -->
<?php if ( $show_countdown ) : ?>
<section class="mym-section--pad-sm mym-bg-cream mym-center">
	<div class="mym-cd-label"><?php echo esc_html( $cd_save ); ?></div>
	<div class="mym-cd-sub"><?php echo esc_html( $cd_until ); ?></div>
	<div class="mym-cd-grid" id="mym-countdown"
		data-date="<?php echo esc_attr( $wedding_date ); ?>"
		data-time="<?php echo esc_attr( mym_opt( 'mym_wedding_time', '11:00' ) ); ?>">
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="d">--</span><span class="mym-cd-unit"><?php echo esc_html( mym_s( 'mym_cd_days',  'Days' ) ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="h">--</span><span class="mym-cd-unit"><?php echo esc_html( mym_s( 'mym_cd_hours', 'Hours' ) ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="m">--</span><span class="mym-cd-unit"><?php echo esc_html( mym_s( 'mym_cd_mins',  'Minutes' ) ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num sec" data-cd="s">--</span><span class="mym-cd-unit"><?php echo esc_html( mym_s( 'mym_cd_secs', 'Seconds' ) ); ?></span></div>
	</div>
	<?php if ( $show_dates && $dates ) : ?>
	<p class="mym-cd-note"><?php echo esc_html( $cd_note ); ?></p>
	<div class="mym-cd-dates">
		<?php foreach ( $dates as $d ) :
			$dts = strtotime( $d );
			if ( ! $dts ) { continue; }
			$label = $day_names[ (int) date_i18n( 'w', $dts ) ] . ' · ' . date_i18n( 'd.m.Y', $dts );
		?>
			<span class="mym-cd-date"><?php echo esc_html( $label ); ?></span>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php if ( $cal ) : ?>
	<div class="mym-cal-links">
		<a href="<?php echo esc_url( $cal['google'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( '+ Google Kalender', 'mym-hochzeit' ); ?></a>
		<a href="<?php echo esc_attr( $cal['ics'] ); ?>" download="hochzeit.ics"><?php esc_html_e( '+ Apple / Outlook', 'mym-hochzeit' ); ?></a>
	</div>
	<?php endif; ?>
</section>
<?php endif; ?>

<!-- ============ SEKTIONEN (aus primärem Menü) ============ -->
<?php if ( ! empty( $section_items ) ) : ?>
	<?php
	/* Der dunkle Hero geht direkt in die erste Menü-Sektion über, wenn der
	 * (immer helle) Countdown fehlt — Start-Index dann um 1 verschieben,
	 * sonst stehen zwei dunkle Flächen hintereinander. */
	$sect_index = $show_countdown ? 0 : 1;
	foreach ( $section_items as $item ) :
		$page_id = (int) $item->object_id;
		$page    = get_post( $page_id );
		if ( ! $page || $page->post_status !== 'publish' ) { continue; }

		$template   = get_post_meta( $page_id, '_wp_page_template', true );
		$bg         = ( $sect_index % 2 === 0 ) ? 'mym-bg-forest' : 'mym-bg-cream';
		$tpl_args   = array(
			'page_id'    => $page_id,
			'page'       => $page,
			'index'      => $sect_index,
			'bg'         => $bg,
			'section_id' => $page->post_name,
		);

		if ( $template === 'page-board.php' ) {
			get_template_part( 'template-parts/section', 'board', $tpl_args );
		} elseif ( $template === 'page-gallery.php' ) {
			get_template_part( 'template-parts/section', 'gallery', $tpl_args );
		} elseif ( $template === 'page-rsvp.php' ) {
			get_template_part( 'template-parts/section', 'rsvp', $tpl_args );
		} else {
			get_template_part( 'template-parts/section', 'default', $tpl_args );
		}
		$sect_index++;
	endforeach;
	?>
<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
	<!-- Admin-Hinweis: kein Menü gesetzt -->
	<section class="mym-section mym-bg-cream mym-center" style="padding:80px 24px">
		<h2 style="font-family:var(--font-serif);margin-bottom:16px"><?php esc_html_e( 'Theme einrichten', 'mym-hochzeit' ); ?></h2>
		<p style="max-width:560px;margin:0 auto 24px"><?php esc_html_e( 'Erstelle ein primäres Navigationsmenü und füge deine Sektionsseiten hinzu, um die Startseite aufzubauen.', 'mym-hochzeit' ); ?></p>
		<a class="button button-primary" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Menü bearbeiten', 'mym-hochzeit' ); ?></a>
	</section>
<?php endif; ?>

<?php get_footer(); ?>
