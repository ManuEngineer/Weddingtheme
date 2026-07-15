<?php
/**
 * Cordillera - functions.php
 * Theme-Setup, Customizer, Assets, Unterkunfts-Boerse (CPT + AJAX), i18n-Helfer.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'MYM_VERSION', '2.4.0' );

/* ============================================================
 * 1) THEME SETUP
 * ========================================================== */
function mym_setup() {
	load_theme_textdomain( 'mym-hochzeit', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	register_nav_menus( array(
		'primary' => __( 'Hauptmenue', 'mym-hochzeit' ),
		'footer'  => __( 'Footer-Menue (Impressum / Datenschutz)', 'mym-hochzeit' ),
	) );
}
add_action( 'after_setup_theme', 'mym_setup' );

/* ============================================================
 * 2) ASSETS (Fonts, Style, Script)
 * ========================================================== */
function mym_assets() {
	wp_enqueue_style( 'mym-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500&display=swap', array(), null );
	wp_enqueue_style( 'mym-style', get_stylesheet_uri(), array( 'mym-fonts' ), MYM_VERSION );

	/* Bergketten-Regler: Prozent-Eingabe aus dem Customizer -> viewBox-Einheiten (viewBox ist 5780.6 x 300). */
	$mtn_vb_width  = 5780.6;
	$mtn_vb_height = 300;
	$mtn_pct_to_vbu = function ( $key, $vb_axis_length, $default = 0, $sanitizer = 'mym_sanitize_mtn_shift' ) {
		$pct = call_user_func( $sanitizer, mym_opt( $key, $default ) );
		return round( $pct / 100 * $vb_axis_length, 1 );
	};
	$shift_schweiz_d   = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_schweiz_desktop', $mtn_vb_width, 0, 'mym_sanitize_mtn_shift_schweiz_h' );
	$shift_chile_d     = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_chile_desktop', $mtn_vb_width );
	$shift_schweiz_m   = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_schweiz_mobile', $mtn_vb_width, 0, 'mym_sanitize_mtn_shift_schweiz_h' );
	$shift_chile_m     = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_chile_mobile', $mtn_vb_width );
	$shift_schweiz_d_y = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_schweiz_desktop_y', $mtn_vb_height );
	$shift_chile_d_y   = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_chile_desktop_y', $mtn_vb_height );
	$shift_schweiz_m_y = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_schweiz_mobile_y', $mtn_vb_height );
	$shift_chile_m_y   = $mtn_pct_to_vbu( 'mym_hero_mtn_shift_chile_mobile_y', $mtn_vb_height );
	$mtn_scale_d = mym_sanitize_mtn_scale( mym_opt( 'mym_hero_mtn_scale_desktop', 100 ) ) / 100;
	$mtn_scale_m = mym_sanitize_mtn_scale( mym_opt( 'mym_hero_mtn_scale_mobile', 100 ) ) / 100;
	/* Seiteninhalt-Breite: EINE Quelle für Startbild-Sektionen + alle Unterseiten-Templates. */
	$content_width = mym_sanitize_content_width( mym_opt( 'mym_content_width', 1040 ) );
	wp_add_inline_style( 'mym-style',
		":root{--mym-mtn-shift-schweiz:{$shift_schweiz_d}px;--mym-mtn-shift-schweiz-y:{$shift_schweiz_d_y}px;--mym-mtn-shift-chile:{$shift_chile_d}px;--mym-mtn-shift-chile-y:{$shift_chile_d_y}px;--mym-mtn-scale:{$mtn_scale_d};--mym-content-width:{$content_width}px}" .
		"@media(max-width:880px){:root{--mym-mtn-shift-schweiz:{$shift_schweiz_m}px;--mym-mtn-shift-schweiz-y:{$shift_schweiz_m_y}px;--mym-mtn-shift-chile:{$shift_chile_m}px;--mym-mtn-shift-chile-y:{$shift_chile_m_y}px;--mym-mtn-scale:{$mtn_scale_m}}}"
	);

	wp_enqueue_script( 'mym-script', get_template_directory_uri() . '/assets/js/main.js', array(), MYM_VERSION, true );
	wp_localize_script( 'mym-script', 'MYM', array(
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
		'nonce'       => wp_create_nonce( 'mym_board' ),
		'rsvpNonce'   => wp_create_nonce( 'mym_rsvp' ),
		'weddingDate' => mym_opt( 'mym_wedding_date', '' ),
		'weddingTime' => mym_opt( 'mym_wedding_time', '11:00' ),
		'defaultHero' => mym_opt( 'mym_hero_variant', 'horizont' ),
		'isEditor'    => current_user_can( 'edit_posts' ),
		'i18n'        => array(
			'sending'  => mym_s( 'mym_js_sending',  'Sending …' ),
			'thanks'   => mym_s( 'mym_js_thanks',   'Thank you! Your entry will be visible after review.' ),
			'errName'  => mym_s( 'mym_js_err_name', 'Please enter your name.' ),
			'error'    => mym_s( 'mym_js_error',    'Something went wrong. Please try again later.' ),
		),
		'rsvpI18n'    => array(
			'sending'  => mym_s( 'mym_rsvp_js_sending',   'Sending …' ),
			'thanks'   => mym_s( 'mym_rsvp_js_thanks',    'Thank you for your reply! We\'ve sent you a confirmation email.' ),
			'updated'  => mym_s( 'mym_rsvp_js_updated',   'Updated! We\'ve sent you a new confirmation email.' ),
			'errName'  => mym_s( 'mym_rsvp_js_err_name',  'Please enter a name.' ),
			'errEmail' => mym_s( 'mym_rsvp_js_err_email', 'Please enter a valid email address.' ),
			'errPhone' => mym_s( 'mym_rsvp_js_err_phone', 'Please enter a phone number with country code, e.g. +41 79 123 45 67.' ),
			'errGuest' => mym_s( 'mym_rsvp_js_err_guest', 'Please add at least one person.' ),
			'error'    => mym_s( 'mym_rsvp_js_error',     'Something went wrong. Please try again later.' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'mym_assets' );

/* Favicon */
function mym_favicon() {
	$icon = get_template_directory_uri() . '/assets/favicon.svg';
	echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( $icon ) . '">' . "\n";
}
add_action( 'wp_head', 'mym_favicon' );

/* ============================================================
 * 3) OPTIONEN-HELFER (Customizer + Polylang-faehig)
 * ========================================================== */
/**
 * Liest eine Theme-Option. Wenn Polylang aktiv ist und ein
 * sprachspezifischer String registriert wurde, wird dieser bevorzugt.
 */
function mym_opt( $key, $default = '' ) {
	$val = get_theme_mod( $key, $default );
	/* Wenn Customizer-Wert leer ist, Default als Polylang-Quelle verwenden —
	 * so kann z.B. mym_connector leer bleiben und trotzdem per Sprache übersetzt werden. */
	$src = ( $val !== '' ) ? $val : $default;
	if ( function_exists( 'pll__' ) && is_string( $src ) && $src !== '' ) {
		$translated = pll__( $src );
		if ( $translated && $translated !== $src ) { return $translated; }
	}
	return $src;
}

/* ============================================================
 * 4) SPRACHE / POLYLANG-HELFER
 * ========================================================== */
function mym_current_lang() {
	if ( function_exists( 'pll_current_language' ) ) {
		$l = pll_current_language();
		if ( $l ) { return $l; }
	}
	/* Derive 2-letter code from WP locale (e.g. de_DE → de, es_CL → es, en_US → en). */
	return substr( get_locale(), 0, 2 );
}

/**
 * Gibt die Sprachumschalter-Links aus.
 * Mit Polylang: echte übersetzte Permalinks für jede aktive Sprache.
 * Ohne Polylang: kein Umschalter (Mono-Sprach-Installation).
 */
function mym_language_switcher() {
	if ( ! function_exists( 'pll_the_languages' ) ) { return ''; }
	$langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0 ) );
	if ( empty( $langs ) ) { return ''; }
	$out = '<ul class="mym-lang">';
	foreach ( $langs as $lang ) {
		$cls  = $lang['current_lang'] ? ' current-lang' : '';
		$slug = strtoupper( $lang['slug'] );
		$out .= '<li class="lang-item' . $cls . '"><a href="' . esc_url( $lang['url'] ) . '" lang="' . esc_attr( $lang['locale'] ) . '">' . esc_html( $slug ) . '</a></li>';
	}
	$out .= '</ul>';
	return $out;
}

function mym_preview_lang() {
	return mym_current_lang();
}

/* ============================================================
 * 4b) BRAUTPAAR / NAMEN (aus Backend, generisch im Code)
 * ========================================================== */
/**
 * Namen des Paares aus dem Customizer. Keine Namen sind im Code fest verankert.
 * Fallback, wenn nichts gepflegt ist: der Seitentitel wird an einem Verbinder
 * (&, y, und, +) in zwei Namen zerlegt.
 *
 * @return array{a:string,b:string}
 */
function mym_couple() {
	$a = trim( (string) get_theme_mod( 'mym_partner_a', '' ) );
	$b = trim( (string) get_theme_mod( 'mym_partner_b', '' ) );

	if ( $a === '' && $b === '' ) {
		$name = trim( (string) get_bloginfo( 'name' ) );
		foreach ( array( ' & ', ' y ', ' und ', ' + ' ) as $sep ) {
			if ( $name !== '' && strpos( $name, $sep ) !== false ) {
				$parts = explode( $sep, $name, 2 );
				$a = trim( $parts[0] );
				$b = trim( $parts[1] );
				break;
			}
		}
		if ( $a === '' && $b === '' ) { $a = $name; }
	}
	return array( 'a' => $a, 'b' => $b );
}

/**
 * Monogramm fuers Logo (Header/Footer). Eine FESTE Marke, sprachunabhaengig und
 * unabhaengig vom Namen-Verbinder. Quelle: Customizer-Feld `mym_logo_text`
 * (z.B. "MyM"). Leer = aus den Initialen der Namen mit "&" als Mitte.
 *
 * Darstellung: erstes Zeichen gross (l1), Mitte klein (ly), letztes gross (l3).
 */
function mym_monogram() {
	$logo = trim( (string) get_theme_mod( 'mym_logo_text', '' ) );

	if ( $logo === '' ) {
		$c = mym_couple();
		if ( $c['a'] !== '' && $c['b'] !== '' ) {
			$logo = mb_strtoupper( mb_substr( $c['a'], 0, 1 ) ) . '&' . mb_strtoupper( mb_substr( $c['b'], 0, 1 ) );
		} elseif ( $c['a'] !== '' ) {
			$logo = mb_strtoupper( mb_substr( $c['a'], 0, 1 ) );
		} else {
			return '<span class="ly">&amp;</span>';
		}
	}

	$len = mb_strlen( $logo );
	if ( $len >= 3 ) {
		return '<span class="l1">' . esc_html( mb_substr( $logo, 0, 1 ) ) . '</span>'
			. '<span class="ly">' . esc_html( mb_substr( $logo, 1, $len - 2 ) ) . '</span>'
			. '<span class="l3">' . esc_html( mb_substr( $logo, $len - 1, 1 ) ) . '</span>';
	}
	if ( $len === 2 ) {
		return '<span class="l1">' . esc_html( mb_substr( $logo, 0, 1 ) ) . '</span>'
			. '<span class="l3">' . esc_html( mb_substr( $logo, 1, 1 ) ) . '</span>';
	}
	return '<span class="l1">' . esc_html( $logo ) . '</span>';
}

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/board.php';
require get_template_directory() . '/inc/rsvp.php';
require get_template_directory() . '/inc/content.php'; /* v1 compat — mym_content() still available for child themes */
require get_template_directory() . '/inc/sections.php';
require get_template_directory() . '/inc/strings.php';

/* ============================================================
 * 4c) NAVIGATIONS-ANKER-FILTER
 * Konvertiert Seiten-Permalinks im Hauptmenü zu Onepager-Sprungzielen,
 * damit das Hauptmenü von JEDER Seite aus einheitlich zur Startseite
 * mit dem passenden Abschnitt zurückführt (nicht zur einzelnen Seite):
 * auf der Startseite selbst ein reiner Intra-Page-Anker (#slug), auf
 * allen anderen Seiten eine absolute URL zur Startseite mit Anker
 * (home_url('/#slug')).
 * ========================================================== */
add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
	if ( ! isset( $args->theme_location ) || $args->theme_location !== 'primary' ) {
		return $items;
	}
	$is_front = is_front_page();
	foreach ( $items as $item ) {
		if ( $item->object === 'page' && (int) $item->menu_item_parent === 0 ) {
			$slug = get_post_field( 'post_name', (int) $item->object_id );
			if ( $slug ) {
				$item->url = $is_front ? ( '#' . $slug ) : home_url( '/#' . $slug );
			}
		}
	}
	return $items;
}, 10, 2 );

/* ============================================================
 * 4d) SEITEN-VORLAGEN registrieren
 * Bestimmt den Sektionstyp auf der Startseite:
 *   page-board.php   → Unterkunftsbörse (Seiteninhalt + Formular)
 *   page-gallery.php → Galerie (Seiteninhalt + CTA-Button)
 *   page-map.php     → breiter Rahmen für Seiten mit "Karte mit Text"-Pattern
 *   page-rsvp.php    → RSVP (Seiteninhalt + Zu-/Absage-Formular)
 * ========================================================== */
add_filter( 'theme_page_templates', function ( $templates ) {
	$templates['page-board.php']   = __( 'Unterkunftsbörse', 'mym-hochzeit' );
	$templates['page-gallery.php'] = __( 'Foto-Galerie', 'mym-hochzeit' );
	$templates['page-map.php']     = __( 'Anreise & Karte', 'mym-hochzeit' );
	$templates['page-rsvp.php']    = __( 'RSVP (Zu-/Absage)', 'mym-hochzeit' );
	return $templates;
} );

/* ============================================================
 * 4e) OPEN GRAPH / SOCIAL-PREVIEW META-TAGS
 * Für eine ansehnliche Linkvorschau beim Teilen (WhatsApp, iMessage,
 * Facebook, ...). Kein SEO-Plugin nötig, nur die og:/twitter:-Tags.
 * Startseite: Namen + Datum/Ort. Andere Seiten: normaler Titel.
 * Bild: Startbild-Foto aus dem Customizer, sonst Beitragsbild der
 * Startseite, sonst kein Bild-Tag (kein SVG als og:image geeignet).
 * ========================================================== */
function mym_social_meta_tags() {
	$is_front = is_front_page();
	$couple   = mym_couple();
	$conn     = mym_opt( 'mym_connector', '&' ) ?: '&';
	$title    = $is_front
		? trim( $couple['a'] . ' ' . $conn . ' ' . $couple['b'] )
		: wp_get_document_title();
	if ( $title === '' ) {
		$title = get_bloginfo( 'name' );
	}

	$description = '';
	if ( $is_front ) {
		$ts    = strtotime( mym_opt( 'mym_wedding_date', '' ) );
		$exact = get_theme_mod( 'mym_date_exact', false );
		$when  = $ts ? date_i18n( $exact ? 'j. F Y' : 'F Y', $ts ) : '';
		$place = mym_opt( 'mym_place', '' );
		$description = trim( implode( ' · ', array_filter( array( $when, $place ) ) ) );
	}
	if ( $description === '' ) {
		$description = get_bloginfo( 'description' );
	}

	$image = get_theme_mod( 'mym_hero_photo', '' );
	if ( ! $image && $is_front && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	}

	$url    = $is_front ? home_url( '/' ) : get_permalink();
	$lang   = mym_current_lang();
	$locale = array( 'de' => 'de_DE', 'es' => 'es_ES' );
	$locale = isset( $locale[ $lang ] ) ? $locale[ $lang ] : $lang;
	?>
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( $locale ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<?php if ( $image ) : ?>
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>">
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<?php if ( $description ) : ?>
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php endif; ?>
	<?php if ( $image ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
	<?php endif; ?>
	<?php
}
add_action( 'wp_head', 'mym_social_meta_tags', 1 );

/* ============================================================
 * 4f) "ZUM KALENDER HINZUFÜGEN"-LINKS
 * Nur wenn ein EXAKTES Datum gesetzt ist (mym_date_exact) — bei nur
 * grob bekanntem Monat wäre ein Kalendereintrag irreführend. Dauer
 * mangels Detailangabe pauschal 8 Std. ab Startzeit.
 * Google: direkter Render-Link. Alle anderen (Apple/Outlook/...):
 * generierte .ics-Datei als data-URI, kein Server-Endpunkt nötig.
 * ========================================================== */
function mym_ics_escape( $text ) {
	return str_replace( array( '\\', ',', ';', "\n" ), array( '\\\\', '\\,', '\\;', '\\n' ), $text );
}

function mym_calendar_links() {
	if ( ! get_theme_mod( 'mym_date_exact', false ) ) {
		return null;
	}
	$tz       = wp_timezone();
	$dt_str   = mym_opt( 'mym_wedding_date', '' ) . ' ' . mym_opt( 'mym_wedding_time', '11:00' );
	try {
		$dt = new DateTimeImmutable( $dt_str, $tz );
		$ts = $dt->getTimestamp();
	} catch ( Exception $e ) {
		return null;
	}
	if ( ! $ts ) {
		return null;
	}
	$start = $ts;
	$end   = $ts + 8 * HOUR_IN_SECONDS;

	$couple = mym_couple();
	$conn   = mym_opt( 'mym_connector', '&' ) ?: '&';
	$title  = trim( $couple['a'] . ' ' . $conn . ' ' . $couple['b'] );
	$place  = mym_opt( 'mym_place', '' );
	$url    = home_url( '/' );

	$google = add_query_arg( array(
		'action'   => 'TEMPLATE',
		'text'     => $title,
		'dates'    => wp_date( 'Ymd\THis', $start ) . '/' . wp_date( 'Ymd\THis', $end ),
		'ctz'      => wp_timezone_string(),
		'details'  => $url,
		'location' => $place,
	), 'https://www.google.com/calendar/render' );

	$ics  = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//" . sanitize_title( get_bloginfo( 'name' ) ) . "//DE\r\n";
	$ics .= "BEGIN:VEVENT\r\nUID:" . md5( $title . $start ) . '@' . wp_parse_url( $url, PHP_URL_HOST ) . "\r\n";
	$ics .= 'DTSTART:' . wp_date( 'Ymd\THis', $start ) . "\r\n";
	$ics .= 'DTEND:' . wp_date( 'Ymd\THis', $end ) . "\r\n";
	$ics .= 'SUMMARY:' . mym_ics_escape( $title ) . "\r\n";
	if ( $place ) {
		$ics .= 'LOCATION:' . mym_ics_escape( $place ) . "\r\n";
	}
	$ics .= 'DESCRIPTION:' . mym_ics_escape( $url ) . "\r\n";
	$ics .= "END:VEVENT\r\nEND:VCALENDAR\r\n";

	return array(
		'google' => esc_url_raw( $google ),
		'ics'    => 'data:text/calendar;charset=utf8;base64,' . base64_encode( $ics ),
	);
}

/* ============================================================
 * Template-Hilfsfunktionen (hier definiert, nicht im Template,
 * damit sie bei mehrfachem Include nicht redeklariiert werden)
 * ========================================================== */
if ( ! function_exists( 'mym_edit_btn' ) ) :
function mym_edit_btn( $sect ) {
	if ( $sect && ! empty( $sect['edit_url'] ) ) {
		printf(
			'<a class="mym-edit-link" href="%s">&#9999; %s</a>',
			esc_url( $sect['edit_url'] ),
			esc_html__( 'Seite bearbeiten', 'mym-hochzeit' )
		);
	}
}
endif;

if ( ! function_exists( 'mym_board_entry_html' ) ) :
function mym_board_entry_html( $e, $type ) {
	$lang_labels = array( 'de' => 'DE', 'es' => 'ES', 'fr' => 'FR', 'en' => 'EN' );
	$cls         = $e['vermittelt'] ? ' vermittelt' : '';
	$type_cls    = esc_attr( $type . $cls );
	$out = '<div class="mym-board-item ' . $type_cls . '">';
	$out .= '<div class="mym-board-item-head">';
	$out .= '<span class="name">' . esc_html( $e['name'] ) . '</span>';
	if ( $e['places'] ) {
		$out .= '<span class="pl">' . esc_html( $e['places'] ) . ' ' . esc_html__( 'Pl.', 'mym-hochzeit' ) . '</span>';
	}
	if ( $e['vermittelt'] ) {
		$out .= '<span class="mym-vermittelt-badge">' . esc_html__( 'Vermittelt', 'mym-hochzeit' ) . '</span>';
	}
	$out .= '</div>';
	if ( $e['location'] || $e['date_from'] || $e['date_to'] ) {
		$out .= '<div class="mym-board-item-meta">';
		if ( $e['location'] ) {
			$out .= '<span class="meta-loc">&#128205; ' . esc_html( $e['location'] ) . '</span>';
		}
		if ( $e['date_from'] || $e['date_to'] ) {
			$out .= '<span class="meta-dates">&#128197; ' . esc_html( $e['date_from'] );
			if ( $e['date_to'] ) { $out .= ' &ndash; ' . esc_html( $e['date_to'] ); }
			$out .= '</span>';
		}
		$out .= '</div>';
	}
	if ( ! empty( $e['langs'] ) ) {
		$out .= '<div class="mym-board-item-langs">';
		foreach ( $e['langs'] as $l ) {
			$l    = trim( $l );
			$label = isset( $lang_labels[ $l ] ) ? $lang_labels[ $l ] : strtoupper( $l );
			$out .= '<span class="mym-lang-badge ' . esc_attr( $l ) . '">' . esc_html( $label ) . '</span>';
		}
		$out .= '</div>';
	}
	if ( $e['note'] ) {
		$out .= '<div class="note">' . esc_html( $e['note'] ) . '</div>';
	}
	$out .= '</div>';
	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput
}
endif;

/* ============================================================
 * 5) BLOCK-EDITOR: Stile, Muster, Editor-CSS
 * ========================================================== */

/* Editor-Stylesheet (Vorschau im Editor = Frontend) */
function mym_editor_assets() {
	add_editor_style( get_template_directory_uri() . '/assets/css/editor-style.css' );
	wp_enqueue_style( 'mym-editor-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Jost:wght@300;400;500&display=swap', array(), null );
}
add_action( 'after_setup_theme', function() { add_theme_support( 'editor-styles' ); } );
add_action( 'admin_init', 'mym_editor_assets' );

/* Block-Stile registrieren (erscheinen als klickbare Varianten im Editor) */
function mym_register_block_styles() {
	/* Paragraph → Kicker */
	register_block_style( 'core/paragraph', array(
		'name'  => 'kicker',
		'label' => '✦ Kicker (kleiner Obertitel)',
	) );
	/* Heading → Grosse H2 */
	register_block_style( 'core/heading', array(
		'name'  => 'h2-gross',
		'label' => 'MyM Gross',
	) );
	/* Image → Foto mit Rahmen */
	register_block_style( 'core/image', array(
		'name'  => 'foto-rahmen',
		'label' => 'Foto mit Rahmen (Geschichte)',
	) );
	/* Separator → dezent */
	register_block_style( 'core/separator', array(
		'name'  => 'dezent',
		'label' => 'Dezent',
	) );
}
add_action( 'init', 'mym_register_block_styles' );

/* Block-Muster-Kategorie */
function mym_register_pattern_category() {
	register_block_pattern_category( 'mym-hochzeit', array(
		'label' => 'Hochzeit',
	) );
}
add_action( 'init', 'mym_register_pattern_category' );

/* Block-Muster registrieren.
 * Alle Muster sind generische Gerueste mit Platzhalter-Text — keine echten
 * Namen, Daten oder Orte. Sie dienen als Startpunkt im Editor und werden auf
 * der jeweiligen Sektion-Seite mit eigenen Inhalten gefuellt. */
function mym_register_block_patterns() {

	/* ---- Geschichte ---- */
	register_block_pattern( 'mym-hochzeit/geschichte', array(
		'title'       => 'Geschichte / Historia',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Zwei Spalten: links Foto + Bildunterschrift, rechts Texte + Signatur.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Kicker / Obertitel</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"is-style-h2-gross"} -->
<h2 class="is-style-h2-gross">Unsere Geschichte</h2>
<!-- /wp:heading -->

<!-- wp:columns {"style":{"spacing":{"blockGap":"clamp(28px,5vw,64px)"}}} -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:image {"className":"is-style-foto-rahmen"} -->
<figure class="wp-block-image is-style-foto-rahmen"><img src="" alt=""/></figure>
<!-- /wp:image -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:paragraph -->
<p>Hier erzählt ihr eure gemeinsame Geschichte.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Zweiter Absatz: wo ihr euch kennengelernt habt, was euch verbindet.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="mym-story-sign">
  <span class="line"></span>
  <span class="names">Name <span class="conn">&amp;</span> Name</span>
</div>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->',
	) );

	/* ---- Programm ---- */
	register_block_pattern( 'mym-hochzeit/programm', array(
		'title'       => 'Programm des Tages',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Zeitplan als stilisierte Tabelle.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Der Tag</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"is-style-h2-gross"} -->
<h2 class="is-style-h2-gross">Programm des Tages</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<table class="mym-prog-table">
  <tr><td class="zeit">11:00</td><td><h3>Programmpunkt</h3><p>Kurzbeschreibung</p></td></tr>
  <tr><td class="zeit">14:00</td><td><h3>Programmpunkt</h3><p>Kurzbeschreibung</p></td></tr>
  <tr><td class="zeit">15:30</td><td><h3>Apéro</h3><p>Anstossen &amp; Begegnen</p></td></tr>
  <tr><td class="zeit">18:00</td><td><h3>Festessen</h3><p>Gemeinsam an der Tafel</p></td></tr>
  <tr><td class="zeit">21:00</td><td><h3>Tanz &amp; Fest</h3><p>Bis tief in die Nacht</p></td></tr>
</table>
<!-- /wp:html -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"}},"className":"mym-note"} -->
<p class="mym-note" style="font-style:italic">Die Zeiten sind vorläufig — die Details folgen.</p>
<!-- /wp:paragraph -->',
	) );

	/* ---- Anreise ---- */
	register_block_pattern( 'mym-hochzeit/anreise', array(
		'title'       => 'Anreise / Cómo llegar',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Kicker, Titel und Reise-Abschnitte mit Goldpunkt-Stil.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Ort &amp; Weg</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"is-style-h2-gross"} -->
<h2 class="wp-block-heading is-style-h2-gross">Ort &amp; Anreise</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hier beschreibt ihr den Veranstaltungsort und die Anreise.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="mym-travel-legs">
  <div class="mym-leg">
    <span class="dot"></span>
    <div><h4>In Coche</h4><p>Hinweise zur Anfahrt mit dem Auto.</p></div>
  </div>
  <div class="mym-leg">
    <span class="dot"></span>
    <div><h4>Öffentlich</h4><p>Hinweise zu Zug und Bus.</p></div>
  </div>
  <div class="mym-leg">
    <span class="dot"></span>
    <div><h4>Vor Ort</h4><p>Parken, letzte Meter, Treffpunkt.</p></div>
  </div>
</div>
<!-- /wp:html -->',
	) );

	/* ---- FAQ (Akkordeon, reines HTML <details> — kein JS noetig) ---- */
	register_block_pattern( 'mym-hochzeit/faq', array(
		'title'       => 'FAQ (Akkordeon)',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Aufklappbare Fragen/Antworten. Jede Frage ist ein <details> — klappt per Klick auf das „+" auf, ganz ohne JavaScript.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Gut zu wissen</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"is-style-h2-gross"} -->
<h2 class="is-style-h2-gross">Häufige Fragen</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<div class="mym-faq-list">
  <details class="mym-faq-item">
    <summary class="mym-faq-q"><span class="q">Eure Frage hier?</span><span class="plus">+</span></summary>
    <div class="mym-faq-a"><p>Eure Antwort hier.</p></div>
  </details>
  <details class="mym-faq-item">
    <summary class="mym-faq-q"><span class="q">Eure Frage hier?</span><span class="plus">+</span></summary>
    <div class="mym-faq-a"><p>Eure Antwort hier.</p></div>
  </details>
  <details class="mym-faq-item">
    <summary class="mym-faq-q"><span class="q">Eure Frage hier?</span><span class="plus">+</span></summary>
    <div class="mym-faq-a"><p>Eure Antwort hier.</p></div>
  </details>
</div>
<!-- /wp:html -->',
	) );

	/* ---- Geschenke ---- */
	register_block_pattern( 'mym-hochzeit/geschenke', array(
		'title'       => 'Geschenke / Regalos',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Einleitung + drei Geschenk-Karten im Box-Stil.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Schenken</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"is-style-h2-gross"} -->
<h2 class="is-style-h2-gross">Geschenke</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Eure Anwesenheit ist das schönste Geschenk. Wer uns darüber hinaus etwas schenken möchte, findet hier vielleicht eine Idee:</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="mym-gifts-grid">
  <div class="mym-gift">
    <span class="num">01</span>
    <h3>Idee</h3>
    <p>Kurzbeschreibung.</p>
  </div>
  <div class="mym-gift">
    <span class="num">02</span>
    <h3>Idee</h3>
    <p>Kurzbeschreibung.</p>
  </div>
  <div class="mym-gift">
    <span class="num">03</span>
    <h3>Idee</h3>
    <p>Kurzbeschreibung.</p>
  </div>
</div>
<!-- /wp:html -->',
	) );
	/* ---- Übernachtung ---- */
	register_block_pattern( 'mym-hochzeit/uebernachtung', array(
		'title'       => 'Übernachtung / Alojamiento',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Kicker, Titel, Hotel-Karten (3 Spalten) und Hinweis-Text.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Schlafen</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"wp-block-heading is-style-h2-gross"} -->
<h2 class="wp-block-heading is-style-h2-gross">Übernachtung</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hier empfehlt ihr Unterkünfte in der Nähe. Eine rechtzeitige Buchung lohnt sich.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<div class="mym-hotels">
  <div class="mym-hotel">
    <div class="mym-hotel-head">
      <h4>Unterkunft 1</h4>
      <span class="tag">€€</span>
    </div>
    <p>Kurze Beschreibung, Lage, Besonderheit.</p>
    <a class="link" href="https://" target="_blank" rel="noopener">Ansehen →</a>
  </div>
  <div class="mym-hotel">
    <div class="mym-hotel-head">
      <h4>Unterkunft 2</h4>
      <span class="tag">€€</span>
    </div>
    <p>Kurze Beschreibung, Lage, Besonderheit.</p>
    <a class="link" href="https://" target="_blank" rel="noopener">Ansehen →</a>
  </div>
  <div class="mym-hotel">
    <div class="mym-hotel-head">
      <h4>Unterkunft 3</h4>
      <span class="tag">€</span>
    </div>
    <p>Kurze Beschreibung, Lage, Besonderheit.</p>
    <a class="link" href="https://" target="_blank" rel="noopener">Ansehen →</a>
  </div>
</div>
<!-- /wp:html -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"}}} -->
<p style="font-style:italic">Weitere Empfehlungen folgen. Bei Fragen meldet euch gerne direkt bei uns.</p>
<!-- /wp:paragraph -->',
	) );

	/* ---- Galerie ---- */
	register_block_pattern( 'mym-hochzeit/galerie', array(
		'title'       => 'Galerie / Galería',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Kicker, Titel, Text und Button zur Galerie. Den Link im Button-Block anpassen.',
		'content'     => '<!-- wp:paragraph {"className":"is-style-kicker"} -->
<p class="is-style-kicker">Erinnerungen</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"className":"wp-block-heading is-style-h2-gross"} -->
<h2 class="wp-block-heading is-style-h2-gross">Foto-Galerie</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hier sammeln wir die Fotos vom grossen Tag. Ihr könnt Bilder ansehen — und eure eigenen hochladen.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Einfach auf den Button klicken, Fotos hochladen und mit allen Gästen teilen.</p>
<!-- /wp:paragraph -->

<!-- wp:html -->
<p class="mym-center"><a class="mym-gallery-cta" href="https://dein-galerie-link.ch" target="_blank" rel="noopener">Zur Galerie &amp; Upload →</a></p>
<!-- /wp:html -->',
	) );

	/* ---- Karte (nur Karte) ---- */
	register_block_pattern( 'mym-hochzeit/karte', array(
		'title'       => 'Karte',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Eingebettete Karte. Solange src="" ist, erscheint der Platzhalter. Einfach die URL zwischen den Anführungszeichen bei src= eintragen und der Platzhalter wird automatisch durch die echte Karte ersetzt.',
		'content'     => '<!-- wp:html -->
<div class="mym-map">
  <iframe src="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Karte"></iframe>
  <svg viewBox="0 0 400 300" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0,210 L70,160 L140,200 L220,130 L300,185 L400,120" fill="none" stroke="#7d9080" stroke-width="1.4"></path>
    <path d="M0,250 L90,215 L170,245 L260,190 L340,235 L400,200" fill="none" stroke="#a7b3a0" stroke-width="1.2"></path>
    <path d="M40,40 C120,20 160,90 260,60 C320,42 360,70 390,55" fill="none" stroke="#b7c0ad" stroke-width="1" stroke-dasharray="3 5"></path>
  </svg>
  <div class="center"><div class="pin"></div><span class="label">Veranstaltungsort</span></div>
  <span class="mapnote">Karte folgt</span>
</div>
<!-- /wp:html -->',
	) );

	/* ---- Karte mit Text (zwei Spalten, wie Anreise-Sektion) ---- */
	register_block_pattern( 'mym-hochzeit/karte-mit-text', array(
		'title'       => 'Karte mit Text',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Zwei Spalten: links Kicker, Titel, Text und Anreise-Abschnitte — rechts Karte. Platzhalter erscheint solange src="" ist; URL eintragen und die echte Karte erscheint.',
		'content'     => '<!-- wp:html -->
<div class="mym-travel-grid">
  <div>
    <p class="is-style-kicker">Ort &amp; Weg</p>
    <h2 class="is-style-h2-gross">Ort &amp; Anreise</h2>
    <p>Beschreibung des Veranstaltungsorts und Anfahrtshinweise.</p>
    <div class="mym-travel-legs">
      <div class="mym-leg"><span class="dot"></span><div><h4>Anreise 1</h4><p>Beschreibung.</p></div></div>
      <div class="mym-leg"><span class="dot"></span><div><h4>Anreise 2</h4><p>Beschreibung.</p></div></div>
      <div class="mym-leg"><span class="dot"></span><div><h4>Vor Ort</h4><p>Beschreibung.</p></div></div>
    </div>
  </div>
  <div class="mym-map">
    <iframe src="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Karte"></iframe>
    <svg viewBox="0 0 400 300" preserveAspectRatio="none" aria-hidden="true">
      <path d="M0,210 L70,160 L140,200 L220,130 L300,185 L400,120" fill="none" stroke="#7d9080" stroke-width="1.4"></path>
      <path d="M0,250 L90,215 L170,245 L260,190 L340,235 L400,200" fill="none" stroke="#a7b3a0" stroke-width="1.2"></path>
      <path d="M40,40 C120,20 160,90 260,60 C320,42 360,70 390,55" fill="none" stroke="#b7c0ad" stroke-width="1" stroke-dasharray="3 5"></path>
    </svg>
    <div class="center"><div class="pin"></div><span class="label">Veranstaltungsort</span></div>
    <span class="mapnote">Karte folgt</span>
  </div>
</div>
<!-- /wp:html -->',
	) );

	/* ---- CTA-Button ---- */
	register_block_pattern( 'mym-hochzeit/cta-button', array(
		'title'       => 'CTA-Button',
		'categories'  => array( 'mym-hochzeit' ),
		'description' => 'Zentrierter Button im Theme-Stil (z. B. für Galerie, Formular, externe Links). Link und Text anpassen.',
		'content'     => '<!-- wp:html -->
<p class="mym-center"><a class="mym-gallery-cta" href="https://dein-link.ch" target="_blank" rel="noopener">Button-Text →</a></p>
<!-- /wp:html -->',
	) );
}
add_action( 'init', 'mym_register_block_patterns' );
