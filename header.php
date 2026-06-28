<?php
/**
 * Header
 * @package MyM_Hochzeit
 */
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
		} elseif ( current_user_can( 'edit_theme_options' ) ) {
			printf(
				'<a href="%s" style="font-size:.8em;opacity:.6">%s</a>',
				esc_url( admin_url( 'nav-menus.php' ) ),
				esc_html__( 'Menü einrichten', 'mym-hochzeit' )
			);
		}
		?>
	</nav>

	<?php echo mym_language_switcher(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</header>

<main id="mym-main">
