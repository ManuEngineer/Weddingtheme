<?php
/**
 * Footer
 * @package MyM_Hochzeit
 */
$couple = mym_couple();
$wd     = get_theme_mod( 'mym_wedding_date', '' );
$year   = ( $wd && strtotime( $wd ) ) ? date_i18n( 'Y', strtotime( $wd ) ) : '';
$conn   = mym_opt( 'mym_connector', '&' ) ?: '&';
$made   = trim( $couple['a'] . ' ' . $conn . ' ' . $couple['b'], " $conn" );
$made   = trim( $made . ( $year ? ' · ' . $year : '' ) );
?>
</main>

<footer class="mym-footer">
	<div class="mym-footer-logo">
		<?php echo mym_monogram(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<p class="mym-footer-tag"><?php echo esc_html( mym_s( 'mym_footer_tag', 'With love' ) ); ?></p>
	<?php if ( $made ) : ?>
	<p class="mym-footer-made"><?php echo esc_html( $made ); ?></p>
	<?php endif; ?>

	<?php if ( has_nav_menu( 'footer' ) ) : ?>
		<nav class="mym-footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'mym-hochzeit' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
		</nav>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
