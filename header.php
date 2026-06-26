<?php
/**
 * Header
 * @package MyM_Hochzeit
 */
$lang = mym_preview_lang();
$c    = mym_content( $lang );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="mym-skip-link" href="#mym-main"><?php esc_html_e( 'Zum Inhalt springen', 'mym-hochzeit' ); ?></a>

<header class="mym-header">
	<a class="mym-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<?php echo mym_monogram(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>

	<button class="mym-burger" aria-label="<?php esc_attr_e( 'Menue', 'mym-hochzeit' ); ?>" aria-expanded="false">&#9776;</button>

	<nav class="mym-nav" aria-label="<?php esc_attr_e( 'Hauptmenue', 'mym-hochzeit' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) );
		} else {
			$home = trailingslashit( home_url( '/' ) );
			printf( '<a href="%s#story">%s</a>',   esc_url( $home ), esc_html( $c['nav']['story'] ) );
			printf( '<a href="%s#program">%s</a>', esc_url( $home ), esc_html( $c['nav']['program'] ) );
			printf( '<a href="%s#travel">%s</a>',  esc_url( $home ), esc_html( $c['nav']['travel'] ) );
			printf( '<a href="%s#stay">%s</a>',    esc_url( $home ), esc_html( $c['nav']['stay'] ) );
			printf( '<a href="%s#gallery">%s</a>', esc_url( $home ), esc_html( $c['nav']['gallery'] ) );
			printf( '<a href="%s#gifts">%s</a>',   esc_url( $home ), esc_html( $c['nav']['gifts'] ) );
			printf( '<a href="%s#faq">%s</a>',     esc_url( $home ), esc_html( $c['nav']['faq'] ) );
		}
		?>
	</nav>

	<?php echo mym_language_switcher(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</header>

<main id="mym-main">
