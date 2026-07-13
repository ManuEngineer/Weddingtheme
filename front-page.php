<?php
/**
 * Front Page v2.0 — menügesteuerter Onepager.
 *
 * Sektionen kommen aus dem primären Navigationsmenü. Jeder Menüpunkt
 * muss auf eine WordPress-Seite zeigen. Der Sektionstyp wird über das
 * Seiten-Template bestimmt:
 *   (Standard)       → section-default  (reiner Seiteninhalt)
 *   page-board.php   → section-board    (Seiteninhalt + Unterkunftsbörse)
 *   page-gallery.php → section-gallery  (Seiteninhalt + Galerie-CTA)
 *   page-map.php     → section-map      (Seiteninhalt + Karten-Embed)
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
			<h1 class="mym-hero-title"><?php echo esc_html( $couple['a'] ); ?> <span class="conn"><?php echo esc_html( $conn ); ?></span> <?php echo esc_html( $couple['b'] ); ?></h1>
			<div class="mym-hero-rule"><span class="line"></span><span class="mym-hero-date"><?php echo esc_html( $hero_when ); ?></span><span class="line"></span></div>
			<div class="mym-hero-place"><?php echo esc_html( $place ); ?></div>
		</div>
		<svg class="mym-hero-mountains" viewBox="0 0 4197 300" preserveAspectRatio="xMidYMax meet" aria-hidden="true">
			<path d="M0,300 L0,165.9 L13,165.9 L26,167.8 L39,170.7 L52,170.7 L65,170.7 L78,169.7 L91,169.7 L104,169.7 L117,168.7 L130,167.8 L143,167.8 L156,167.8 L169,168.7 L182,168.7 L195,167.8 L208,167.8 L221,167.8 L234,167.8 L247,167.8 L260,166.8 L273,165.9 L286,165.9 L299,165.9 L312,165.9 L325,166.8 L338,166.8 L351,167.8 L364,168.7 L377,169.7 L390,169.7 L403,168.7 L416,168.7 L429,169.7 L442,169.7 L455,167.8 L468,166.8 L481,166.8 L494,166.8 L507,168.7 L520,171.6 L533,172.6 L546,171.6 L559,169.7 L572,168.7 L585,168.7 L598,168.7 L611,167.8 L624,161.1 L637,159.2 L650,159.2 L663,159.2 L676,159.2 L689,159.2 L702,157.3 L715,156.3 L728,158.2 L741,158.2 L754,157.3 L767,157.3 L780,155.4 L793,155.4 L806,157.3 L819,159.2 L832,160.1 L845,160.1 L858,159.2 L871,157.3 L884,156.3 L897,155.4 L910,151.5 L923,148.7 L936,146.8 L949,145.8 L962,147.7 L975,149.6 L988,149.6 L1001,149.6 L1014,147.7 L1027,143.9 L1040,143.9 L1053,143.9 L1066,145.8 L1079,145.8 L1092,141.0 L1105,141.0 L1118,141.0 L1131,142.0 L1144,142.9 L1157,142.9 L1170,143.9 L1183,144.8 L1196,145.8 L1209,145.8 L1222,144.8 L1235,143.9 L1248,143.9 L1261,143.9 L1274,146.8 L1287,145.8 L1300,144.8 L1313,142.9 L1326,140.1 L1339,141.0 L1352,140.1 L1365,131.5 L1378,130.5 L1391,131.5 L1404,133.4 L1417,133.4 L1430,136.2 L1443,138.2 L1456,139.1 L1469,138.2 L1482,134.3 L1495,134.3 L1508,136.2 L1521,136.2 L1534,137.2 L1547,137.2 L1560,134.3 L1573,133.4 L1586,130.5 L1599,124.8 L1612,124.8 L1625,124.8 L1638,126.7 L1651,129.6 L1664,130.5 L1677,123.8 L1690,123.8 L1703,125.7 L1716,124.8 L1729,122.9 L1742,117.1 L1755,109.5 L1768,105.7 L1781,110.4 L1794,113.3 L1807,111.4 L1820,110.4 L1833,116.2 L1846,121.9 L1859,124.8 L1872,124.8 L1885,119.0 L1898,114.3 L1911,106.6 L1924,98.0 L1937,100.9 L1950,115.2 L1963,118.1 L1976,119.0 L1989,120.0 L2002,120.0 L2015,119.0 L2028,119.0 L2041,118.1 L2054,114.3 L2067,101.8 L2080,96.1 L2093,93.2 L2106,84.6 L2119,84.6 L2132,92.3 L2145,92.3 L2158,91.3 L2171,89.4 L2184,80.8 L2197,80.8 L2210,90.4 L2223,97.1 L2236,99.9 L2249,99.9 L2262,98.0 L2275,91.3 L2288,83.7 L2301,79.9 L2314,77.0 L2327,81.8 L2340,93.2 L2353,95.1 L2366,91.3 L2379,88.5 L2392,88.5 L2405,88.5 L2418,87.5 L2431,87.5 L2444,88.5 L2457,91.3 L2470,89.4 L2483,87.5 L2496,87.5 L2509,90.4 L2522,93.2 L2535,95.1 L2548,96.1 L2561,94.2 L2574,94.2 L2587,98.0 L2600,99.0 L2613,97.1 L2626,97.1 L2639,96.1 L2652,88.5 L2665,81.8 L2678,81.8 L2691,88.5 L2704,89.4 L2717,88.5 L2730,80.8 L2743,80.8 L2756,79.9 L2769,76.0 L2782,76.0 L2795,79.9 L2808,80.8 L2821,80.8 L2834,78.9 L2847,72.2 L2860,71.2 L2873,71.2 L2886,77.0 L2899,79.9 L2912,72.2 L2925,68.4 L2938,76.0 L2951,77.9 L2964,63.6 L2977,57.9 L2990,59.8 L3003,61.7 L3016,68.4 L3029,69.3 L3042,69.3 L3055,68.4 L3068,64.6 L3081,56.9 L3094,56.0 L3107,57.9 L3120,56.9 L3133,56.0 L3146,56.0 L3159,59.8 L3172,61.7 L3185,55.0 L3198,55.0 L3211,59.8 L3224,61.7 L3237,61.7 L3250,62.6 L3263,64.6 L3276,65.5 L3289,66.5 L3302,65.5 L3315,66.5 L3328,69.3 L3341,70.3 L3354,68.4 L3367,68.4 L3380,73.2 L3393,77.9 L3406,77.9 L3419,75.1 L3432,66.5 L3445,65.5 L3458,65.5 L3471,63.6 L3484,65.5 L3497,70.3 L3510,71.2 L3523,71.2 L3536,66.5 L3549,64.6 L3562,67.4 L3575,71.2 L3588,74.1 L3601,74.1 L3614,77.9 L3627,83.7 L3640,83.7 L3653,77.9 L3666,71.2 L3679,68.4 L3692,70.3 L3705,74.1 L3718,81.8 L3731,82.7 L3744,80.8 L3757,80.8 L3770,79.9 L3783,73.2 L3796,65.5 L3809,65.5 L3822,68.4 L3835,68.4 L3848,65.5 L3861,64.6 L3874,60.7 L3887,59.8 L3900,59.8 L3913,65.5 L3926,66.5 L3939,66.5 L3952,66.5 L3965,66.5 L3978,56.9 L3991,56.9 L4004,60.7 L4017,61.7 L4030,60.7 L4043,59.8 L4056,61.7 L4069,62.6 L4082,62.6 L4095,67.4 L4108,68.4 L4121,77.0 L4134,86.5 L4147,85.6 L4160,82.7 L4173,80.8 L4186,79.9 L4196,79.9 L4197,300 Z" fill="#6e4632" opacity="0.55"></path>
			<path d="M0,300 L0,211.8 L13,210.8 L26,210.8 L39,210.8 L52,210.8 L65,210.8 L78,212.7 L91,212.7 L104,212.7 L117,212.7 L130,209.8 L143,209.8 L156,209.8 L169,209.8 L182,209.8 L195,209.8 L208,209.8 L221,209.8 L234,208.9 L247,208.9 L260,208.9 L273,208.9 L286,208.9 L299,208.9 L312,208.9 L325,208.9 L338,209.8 L351,209.8 L364,209.8 L377,209.8 L390,209.8 L403,209.8 L416,209.8 L429,209.8 L442,209.8 L455,209.8 L468,209.8 L481,209.8 L494,209.8 L507,212.7 L520,228.0 L533,228.0 L546,209.8 L559,209.8 L572,209.8 L585,209.8 L598,209.8 L611,209.8 L624,208.9 L637,209.8 L650,209.8 L663,209.8 L676,208.9 L689,208.9 L702,208.9 L715,208.9 L728,208.9 L741,208.9 L754,208.9 L767,208.9 L780,207.9 L793,207.9 L806,207.9 L819,207.9 L832,207.0 L845,207.0 L858,205.1 L871,205.1 L884,205.1 L897,207.0 L910,207.0 L923,206.0 L936,203.1 L949,203.1 L962,202.2 L975,202.2 L988,203.1 L1001,204.1 L1014,203.1 L1027,200.3 L1040,200.3 L1053,200.3 L1066,200.3 L1079,199.3 L1092,197.4 L1105,196.5 L1118,195.5 L1131,194.5 L1144,192.6 L1157,191.7 L1170,187.9 L1183,186.9 L1196,186.9 L1209,185.9 L1222,185.9 L1235,185.0 L1248,185.9 L1261,185.9 L1274,185.9 L1287,185.0 L1300,183.1 L1313,184.0 L1326,184.0 L1339,184.0 L1352,184.0 L1365,183.1 L1378,183.1 L1391,184.0 L1404,185.0 L1417,185.0 L1430,184.0 L1443,184.0 L1456,185.0 L1469,185.0 L1482,185.9 L1495,185.9 L1508,185.0 L1521,185.0 L1534,184.0 L1547,183.1 L1560,181.2 L1573,175.4 L1586,174.5 L1599,174.5 L1612,174.5 L1625,178.3 L1638,180.2 L1651,181.2 L1664,181.2 L1677,178.3 L1690,175.4 L1703,171.6 L1716,170.7 L1729,169.7 L1742,166.8 L1755,162.0 L1768,157.3 L1781,150.6 L1794,143.9 L1807,137.2 L1820,134.3 L1833,130.5 L1846,124.8 L1859,117.1 L1872,107.6 L1885,107.6 L1898,115.2 L1911,126.7 L1924,137.2 L1937,145.8 L1950,153.4 L1963,160.1 L1976,161.1 L1989,159.2 L2002,151.5 L2015,149.6 L2028,156.3 L2041,163.0 L2054,167.8 L2067,170.7 L2080,171.6 L2093,171.6 L2106,170.7 L2119,167.8 L2132,167.8 L2145,170.7 L2158,170.7 L2171,169.7 L2184,165.9 L2197,163.0 L2210,160.1 L2223,157.3 L2236,157.3 L2249,155.4 L2262,155.4 L2275,154.4 L2288,151.5 L2301,148.7 L2314,142.0 L2327,139.1 L2340,134.3 L2353,128.6 L2366,121.9 L2379,117.1 L2392,108.5 L2405,100.9 L2418,95.1 L2431,93.2 L2444,94.2 L2457,97.1 L2470,98.0 L2483,99.9 L2496,104.7 L2509,112.3 L2522,116.5 L2535,121.0 L2548,121.9 L2561,127.6 L2574,130.5 L2587,133.4 L2600,134.3 L2613,137.2 L2626,142.0 L2639,145.8 L2652,146.8 L2665,151.5 L2678,155.4 L2691,159.2 L2704,160.1 L2717,159.2 L2730,157.3 L2743,159.2 L2756,159.2 L2769,158.2 L2782,157.3 L2795,157.3 L2808,154.4 L2821,152.5 L2834,154.4 L2847,154.4 L2860,153.4 L2873,154.4 L2886,154.4 L2899,148.7 L2912,138.2 L2925,130.5 L2938,128.6 L2951,128.6 L2964,133.4 L2977,138.2 L2990,142.9 L3003,147.7 L3016,148.7 L3029,149.6 L3042,149.6 L3055,149.6 L3068,154.4 L3081,164.0 L3094,151.5 L3107,147.7 L3120,148.7 L3133,155.4 L3146,155.4 L3159,148.7 L3172,140.1 L3185,142.0 L3198,148.7 L3211,149.6 L3224,149.6 L3237,149.6 L3250,146.8 L3263,146.8 L3276,146.8 L3289,146.8 L3302,148.7 L3315,148.7 L3328,142.0 L3341,139.1 L3354,131.5 L3367,129.6 L3380,129.6 L3393,127.6 L3406,126.7 L3419,130.5 L3432,134.3 L3445,134.3 L3458,134.3 L3471,134.3 L3484,130.5 L3497,125.7 L3510,124.8 L3523,126.7 L3536,129.6 L3549,130.5 L3562,131.5 L3575,132.4 L3588,133.4 L3601,141.0 L3614,145.8 L3627,141.0 L3640,138.2 L3653,138.2 L3666,138.2 L3679,138.2 L3692,139.1 L3705,143.9 L3718,143.9 L3731,142.0 L3744,136.2 L3757,126.7 L3770,124.8 L3783,127.6 L3796,128.6 L3809,129.6 L3822,131.5 L3835,134.3 L3848,134.3 L3861,133.4 L3874,129.6 L3887,127.6 L3900,127.6 L3913,128.6 L3926,125.7 L3939,126.7 L3952,130.5 L3965,131.5 L3978,130.5 L3991,124.8 L4004,123.8 L4017,126.7 L4030,126.7 L4043,127.6 L4056,132.4 L4069,133.4 L4082,129.6 L4095,129.6 L4108,131.5 L4121,133.4 L4134,133.4 L4147,134.3 L4160,142.9 L4173,142.9 L4186,141.0 L4196,142.0 L4197,300 Z" fill="#202c25"></path>
		</svg>
	</div>

	<!-- Editorial -->
	<div class="mym-hero-pane mym-hero--editorial" data-pane="editorial" style="<?php echo $variant === 'editorial' ? '' : 'display:none'; ?>">
		<div class="col-text">
			<div class="mym-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="mym-hero-title"><?php echo esc_html( $couple['a'] ); ?><br><span class="conn"><?php echo esc_html( $conn ); ?></span><br><?php echo esc_html( $couple['b'] ); ?></h1>
			<div class="rule"></div>
			<div class="d"><?php echo esc_html( $hero_when ); ?></div>
			<div class="pl"><?php echo esc_html( $place ); ?></div>
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
</section>
<?php endif; ?>

<!-- ============ SEKTIONEN (aus primärem Menü) ============ -->
<?php if ( ! empty( $section_items ) ) : ?>
	<?php
	$sect_index = 0;
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
