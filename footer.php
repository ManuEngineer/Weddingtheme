<?php
/**
 * Footer
 * @package MyM_Hochzeit
 */
$lang = mym_preview_lang();
$c    = mym_content( $lang );
?>
</main>

<footer class="mym-footer">
	<div class="mym-footer-logo">
		<?php echo mym_monogram(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
	<p class="mym-footer-tag"><?php echo esc_html( $c['footer']['tag'] ); ?></p>
	<p class="mym-footer-made"><?php echo esc_html( $c['footer']['made'] ); ?></p>

	<?php if ( has_nav_menu( 'footer' ) ) : ?>
		<nav class="mym-footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'mym-hochzeit' ); ?>">
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'items_wrap' => '%3$s', 'depth' => 1 ) ); ?>
		</nav>
	<?php endif; ?>

	<p class="mym-footer-langnote"><?php echo esc_html( $c['footer']['langnote'] ); ?></p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
