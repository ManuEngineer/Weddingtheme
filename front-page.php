<?php
/**
 * Front Page - Onepager mit allen Sektionen.
 * Inhalte kommen wahlweise aus einer WP-Seite (editierbar im Editor)
 * oder aus dem Fallback in inc/content.php.
 *
 * Seiten-Slugs fuer die editierbaren Sektionen:
 *   Geschichte: "geschichte" (DE) / "historia" (ES)
 *   Programm:   "programm"   (DE) / "programa" (ES)
 *
 * @package MyM_Hochzeit
 */
get_header();

$lang    = mym_preview_lang();
$c       = mym_content( $lang );
$variant = mym_opt( 'mym_hero_variant', 'horizont' );
if ( isset( $_GET['hero'] ) && in_array( $_GET['hero'], array( 'horizont', 'editorial', 'bogen' ), true ) ) {
	$variant = sanitize_text_field( wp_unslash( $_GET['hero'] ) );
}

$featured     = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
$hero_photo   = get_theme_mod( 'mym_hero_photo', '' );
$hero_img     = $hero_photo ? $hero_photo : $featured;
$story_img    = $hero_img;
$wedding_date = mym_opt( 'mym_wedding_date', '' );
$show_dates   = get_theme_mod( 'mym_dates_visible', true );
$dates_raw    = get_theme_mod( 'mym_dates_list', '' );
$dates        = array_filter( array_map( 'trim', explode( "\n", $dates_raw ) ) );
$couple       = mym_couple();
$couple_alt   = trim( $couple['a'] . ' ' . $c['hero']['conn'] . ' ' . $couple['b'], " {$c['hero']['conn']}" );
$day_names    = ( $lang === 'es' )
	? array( 0 => 'Do', 1 => 'Lu', 2 => 'Ma', 3 => 'Mi', 4 => 'Ju', 5 => 'Vi', 6 => 'Sá' )
	: array( 0 => 'So', 1 => 'Mo', 2 => 'Di', 3 => 'Mi', 4 => 'Do', 5 => 'Fr', 6 => 'Sa' );

$ts        = strtotime( $wedding_date );
$hero_when = $ts ? ( date_i18n( 'F', $ts ) . ' ' . date_i18n( 'Y', $ts ) ) : '';

if ( ! function_exists( 'mym_photo' ) ) :
function mym_photo( $url, $alt = '' ) {
	if ( $url ) {
		return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
	}
	return '<div class="mym-photo-ph">' . esc_html__( 'Foto folgt · Foto pronto', 'mym-hochzeit' ) . '</div>';
}
endif;

/* Sektion-Seiten laden */
$sect_story   = mym_section_page( 'geschichte', 'historia' );
$sect_program = mym_section_page( 'programm',   'programa' );
?>

<!-- ============ HERO ============ -->
<section id="top" class="mym-hero" data-variant="<?php echo esc_attr( $variant ); ?>">

	<!-- Horizont -->
	<div class="mym-hero-pane mym-hero--horizont" data-pane="horizont" style="<?php echo $variant === 'horizont' ? '' : 'display:none'; ?>">
		<div class="mym-hero-inner">
			<div class="mym-eyebrow"><?php echo esc_html( $c['hero']['eyebrow'] ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $c['hero']['first'] ); ?> <span class="conn"><?php echo esc_html( $c['hero']['conn'] ); ?></span> <?php echo esc_html( $c['hero']['second'] ); ?></h1>
			<div class="mym-hero-rule"><span class="line"></span><span class="mym-hero-date"><?php echo esc_html( $hero_when ); ?></span><span class="line"></span></div>
			<div class="mym-hero-place"><?php echo esc_html( $c['hero']['place'] ); ?></div>
		</div>
		<svg class="mym-hero-mountains" viewBox="0 0 1200 300" preserveAspectRatio="none" aria-hidden="true">
			<path d="M0,300 L0,150 L130,80 L220,160 L330,60 L450,160 L580,95 L700,175 L840,70 L980,155 L1090,105 L1200,165 L1200,300 Z" fill="#6e4632" opacity="0.55"></path>
			<path d="M0,300 L0,215 L160,150 L300,225 L430,140 L580,215 L720,150 L880,225 L1010,160 L1150,225 L1200,205 L1200,300 Z" fill="#202c25"></path>
		</svg>
	</div>

	<!-- Editorial -->
	<div class="mym-hero-pane mym-hero--editorial" data-pane="editorial" style="<?php echo $variant === 'editorial' ? '' : 'display:none'; ?>">
		<div class="col-text">
			<div class="mym-eyebrow"><?php echo esc_html( $c['hero']['eyebrow'] ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $c['hero']['first'] ); ?><br><span class="conn"><?php echo esc_html( $c['hero']['conn'] ); ?></span><br><?php echo esc_html( $c['hero']['second'] ); ?></h1>
			<div class="rule"></div>
			<div class="d"><?php echo esc_html( $hero_when ); ?></div>
			<div class="pl"><?php echo esc_html( $c['hero']['place'] ); ?></div>
		</div>
		<div class="col-photo"><div class="frame"><?php echo mym_photo( $hero_img, $couple_alt ); // phpcs:ignore ?></div></div>
	</div>

	<!-- Bogen -->
	<div class="mym-hero-pane mym-hero--bogen" data-pane="bogen" style="<?php echo $variant === 'bogen' ? '' : 'display:none'; ?>">
		<div class="mym-hero-inner">
			<div class="mym-eyebrow"><?php echo esc_html( $c['hero']['eyebrow'] ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $c['hero']['first'] ); ?> <span class="conn"><?php echo esc_html( $c['hero']['conn'] ); ?></span> <?php echo esc_html( $c['hero']['second'] ); ?></h1>
			<div class="arch"><?php echo mym_photo( $hero_img, $couple_alt ); // phpcs:ignore ?></div>
			<div class="mym-hero-rule"><span class="line"></span><span class="mym-hero-date"><?php echo esc_html( $hero_when ); ?></span><span class="line"></span></div>
			<div class="mym-hero-place"><?php echo esc_html( $c['hero']['place'] ); ?></div>
		</div>
	</div>

	<?php /* Variante nur im Customizer einstellbar (Design > Customizer > Hochzeit: Einstellungen) */ ?>
</section>

<!-- ============ COUNTDOWN ============ -->
<section class="mym-section--pad-sm mym-bg-cream mym-center">
	<div class="mym-cd-label"><?php echo esc_html( $c['hero']['save'] ); ?></div>
	<div class="mym-cd-sub"><?php echo esc_html( $c['hero']['until'] ); ?></div>
	<div class="mym-cd-grid" id="mym-countdown"
		data-date="<?php echo esc_attr( $wedding_date ); ?>"
		data-time="<?php echo esc_attr( mym_opt( 'mym_wedding_time', '11:00' ) ); ?>">
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="d">--</span><span class="mym-cd-unit"><?php echo esc_html( $c['cd']['days'] ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="h">--</span><span class="mym-cd-unit"><?php echo esc_html( $c['cd']['hours'] ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num" data-cd="m">--</span><span class="mym-cd-unit"><?php echo esc_html( $c['cd']['mins'] ); ?></span></div>
		<div class="mym-cd-cell"><span class="mym-cd-num sec" data-cd="s">--</span><span class="mym-cd-unit"><?php echo esc_html( $c['cd']['secs'] ); ?></span></div>
	</div>
	<?php if ( $show_dates && $dates ) : ?>
	<p class="mym-cd-note"><?php echo esc_html( $c['hero']['note'] ); ?></p>
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
</section>

<!-- ============ GESCHICHTE ============ -->
<section id="story" class="mym-section mym-bg-forest" data-screen-label="Geschichte">
	<?php if ( $sect_story ) : ?>
		<!-- Vollstaendig aus dem WP-Editor (inkl. eigenem Foto) -->
		<div class="mym-section-content mym-wrap">
			<?php echo $sect_story['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $sect_story['edit_url'] ) : ?>
			<div class="mym-wrap" style="padding-top:0"><a class="mym-edit-link" href="<?php echo esc_url( $sect_story['edit_url'] ); ?>">&#9999; <?php esc_html_e( 'Seite bearbeiten', 'mym-hochzeit' ); ?></a></div>
		<?php endif; ?>
	<?php else : ?>
		<!-- Fallback: Theme-Standardinhalt -->
		<div class="mym-story-grid">
			<div>
				<div class="mym-kicker"><?php echo esc_html( $c['story']['kicker'] ); ?></div>
				<h2 class="mym-h2"><?php echo esc_html( $c['story']['title'] ); ?></h2>
				<div class="mym-story-photo"><?php echo mym_photo( $story_img, '' ); // phpcs:ignore ?></div>
			</div>
			<div class="mym-story-body">
				<?php foreach ( $c['story']['body'] as $para ) : ?>
					<p><?php echo esc_html( $para ); ?></p>
				<?php endforeach; ?>
				<div class="mym-story-sign">
					<span class="line"></span>
					<span class="names"><?php echo esc_html( $couple['a'] ); ?> <span class="conn"><?php echo esc_html( $c['hero']['conn'] ); ?></span> <?php echo esc_html( $couple['b'] ); ?></span>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>

<!-- ============ PROGRAMM ============ -->
<section id="program" class="mym-section mym-bg-cream" data-screen-label="Programm">
	<?php if ( $sect_program ) : ?>
		<div class="mym-section-content mym-wrap">
			<?php echo $sect_program['content']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $sect_program['edit_url'] ) : ?>
			<div class="mym-wrap" style="padding-top:0"><a class="mym-edit-link" href="<?php echo esc_url( $sect_program['edit_url'] ); ?>">&#9999; <?php esc_html_e( 'Seite bearbeiten', 'mym-hochzeit' ); ?></a></div>
		<?php endif; ?>
	<?php else : ?>
		<div class="mym-head-block mym-narrow">
			<div class="mym-kicker"><?php echo esc_html( $c['program']['kicker'] ); ?></div>
			<h2 class="mym-h2"><?php echo esc_html( $c['program']['title'] ); ?></h2>
		</div>
		<div class="mym-prog-list">
			<?php foreach ( $c['program']['items'] as $it ) : ?>
				<div class="mym-prog-row">
					<div class="mym-prog-time"><?php echo esc_html( $it[0] ); ?></div>
					<div>
						<h3><?php echo esc_html( $it[1] ); ?></h3>
						<p><?php echo esc_html( $it[2] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="mym-note mym-center" style="margin-top:30px"><?php echo esc_html( $c['program']['note'] ); ?></p>
	<?php endif; ?>
</section>

<?php get_template_part( 'template-parts/front', 'rest', array( 'c' => $c, 'lang' => $lang ) ); ?>

<?php get_footer(); ?>
