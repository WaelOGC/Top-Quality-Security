<?php
/**
 * Shared final CTA banner (Customizer-driven, with optional background image).
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cta_bg_id  = absint( get_theme_mod( 'tqs_cta_bg_image', 0 ) );
$cta_bg_url = $cta_bg_id ? wp_get_attachment_image_url( $cta_bg_id, 'full' ) : '';
?>
<section class="tqs-cta-banner tqs-reveal<?php echo $cta_bg_url ? ' has-bg-image' : ''; ?>">
	<?php if ( $cta_bg_url ) : ?>
		<div class="tqs-cta-bg-image" style="background-image:url('<?php echo esc_url( $cta_bg_url ); ?>');" aria-hidden="true"></div>
		<div class="tqs-cta-bg-overlay" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="tqs-cta-inner">
		<h2 class="tqs-cta-h2"><?php echo esc_html( get_theme_mod( 'tqs_cta_title', 'Klaar voor professionele beveiliging?' ) ); ?></h2>
		<p><?php echo esc_html( get_theme_mod( 'tqs_cta_text', 'Vraag vandaag nog een vrijblijvende offerte aan en ontdek wat TQS voor u kan betekenen.' ) ); ?></p>
		<div class="tqs-cta-buttons">
			<a href="<?php echo esc_url( tqs_theme_mod_url( 'tqs_cta_btn1_url', '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( get_theme_mod( 'tqs_cta_btn1_text', 'Offerte Aanvragen' ) ); ?></a>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ) ); ?>" class="tqs-btn tqs-btn-outline"><?php echo esc_html( get_theme_mod( 'tqs_cta_btn2_text', 'Bel Ons Direct' ) ); ?></a>
		</div>
	</div>
</section>
