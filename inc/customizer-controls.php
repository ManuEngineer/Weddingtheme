<?php
/**
 * Cordillera - Eigene Customizer-Controls.
 * Kein Bild-Upload nötig: Vorschauen sind reines CSS/Inline-SVG, damit kein
 * zusätzliches Asset gepflegt werden muss, wenn sich eine Startbild-Variante ändert.
 *
 * @package MyM_Hochzeit
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Auf einer normalen Frontend-Anfrage ist WP_Customize_Control nie geladen (und wird auch nie
 * gebraucht, da mym_customize_register() nur im customize_register-Hook läuft). Im Customizer
 * selbst lädt WordPress diese Basisklasse zuverlässig erst später als das Theme-Bootstrap —
 * darum hier bei Bedarf gezielt nachladen statt bei Abwesenheit der Klasse einfach stumm
 * abzubrechen (das hätte die eigenen Controls unauffindbar gemacht). */
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	if ( ! file_exists( ABSPATH . WPINC . '/class-wp-customize-control.php' ) ) { return; }
	require_once ABSPATH . WPINC . '/class-wp-customize-control.php';
}

/**
 * Überschrift + Trennlinie innerhalb einer Sektion, um verwandte Einstellungen
 * optisch zu gruppieren (z.B. "Bergketten: Desktop" vs. "Bergketten: Mobil").
 * Erzeugt keine eigene Setting/keinen eigenen Wert — rein visuell.
 */
class Mym_Customize_Heading_Control extends WP_Customize_Control {
	public $type = 'mym-heading';

	public function render_content() {
		?>
		<div class="mym-customize-heading">
			<hr>
			<span class="mym-customize-heading-label"><?php echo esc_html( $this->label ); ?></span>
			<?php if ( $this->description ) : ?>
				<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}
}

/**
 * Visueller Auswahl-Control für die Startbild-Variante: statt eines reinen
 * Text-Dropdowns zeigt jede Option eine kleine schematische Vorschau
 * (Layout-Skizze, kein echtes Foto) zum Anklicken.
 */
class Mym_Customize_Hero_Variant_Control extends WP_Customize_Control {
	public $type = 'mym-hero-variant';

	private function preview_svg( $variant ) {
		switch ( $variant ) {
			case 'editorial':
				return '<svg viewBox="0 0 64 40" xmlns="http://www.w3.org/2000/svg"><rect width="64" height="40" fill="#F4EEE2"/><rect x="4" y="8" width="24" height="24" fill="#cdbf9f"/><rect x="34" y="14" width="26" height="3" fill="#2F4339"/><rect x="34" y="20" width="18" height="3" fill="#A9823F"/></svg>';
			case 'bogen':
				return '<svg viewBox="0 0 64 40" xmlns="http://www.w3.org/2000/svg"><rect width="64" height="40" fill="#F4EEE2"/><path d="M16 36 V20 A16 16 0 0 1 48 20 V36" fill="none" stroke="#2F4339" stroke-width="3"/><rect x="26" y="26" width="12" height="10" fill="#cdbf9f"/></svg>';
			default: // horizont
				return '<svg viewBox="0 0 64 40" xmlns="http://www.w3.org/2000/svg"><rect width="64" height="40" fill="#2F4339"/><path d="M0 30 L14 18 L26 26 L40 12 L52 22 L64 16 V40 H0 Z" fill="#3a5244"/><path d="M0 34 L10 26 L22 32 L36 20 L50 28 L64 24 V40 H0 Z" fill="#8a6f52"/><rect x="24" y="8" width="16" height="3" fill="#F4EEE2"/></svg>';
		}
	}

	public function render_content() {
		if ( empty( $this->choices ) ) { return; }
		$name = '_customize-radio-' . $this->id;
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<div class="mym-hero-variant-picker">
			<?php foreach ( $this->choices as $value => $label ) : ?>
				<label class="mym-hero-variant-choice">
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?>>
					<span class="mym-hero-variant-preview"><?php echo $this->preview_svg( $value ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed, non-dynamic SVG markup ?></span>
					<span class="mym-hero-variant-label"><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}
}

/**
 * Styling für die Controls oben — nur im Customizer selbst geladen.
 */
function mym_customize_controls_css() {
	?>
	<style>
		.mym-customize-heading{margin:18px 0 10px}
		.mym-customize-heading hr{border:none;border-top:1px solid #dcdcde;margin:0 0 8px}
		.mym-customize-heading-label{display:block;font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:.04em;color:#1d2327}
		.mym-hero-variant-picker{display:flex;flex-direction:column;gap:10px;margin-top:8px}
		.mym-hero-variant-choice{display:flex;align-items:center;gap:10px;cursor:pointer;padding:6px;border:1px solid transparent;border-radius:4px}
		.mym-hero-variant-choice:has(input:checked){border-color:#A9823F;background:#faf6ee}
		.mym-hero-variant-preview{display:block;width:64px;height:40px;flex:none;border-radius:2px;overflow:hidden;box-shadow:0 0 0 1px #dcdcde}
		.mym-hero-variant-preview svg{display:block;width:100%;height:100%}
		.mym-hero-variant-choice input{margin:0}
		.mym-hero-variant-label{font-size:13px}
	</style>
	<?php
}
add_action( 'customize_controls_print_styles', 'mym_customize_controls_css' );
