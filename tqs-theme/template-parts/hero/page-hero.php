<?php
/**
 * Simple inner-page hero (static).
 *
 * @package tqs-theme
 *
 * @var array $args {
 *     @type array $hero Page hero data.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero = isset( $args['hero'] ) && is_array( $args['hero'] ) ? $args['hero'] : array();

$image_id  = isset( $hero['image_id'] ) ? absint( $hero['image_id'] ) : 0;
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
$title     = isset( $hero['title'] ) ? (string) $hero['title'] : '';
$subtitle  = isset( $hero['subtitle'] ) ? (string) $hero['subtitle'] : '';
$btn_text  = isset( $hero['btn_text'] ) ? (string) $hero['btn_text'] : '';
$btn_url   = isset( $hero['btn_url'] ) && function_exists( 'tqs_hero_resolve_url' )
	? tqs_hero_resolve_url( $hero['btn_url'] )
	: '';
?>
<section class="tqs-page-hero tqs-page-hero--metabox"<?php if ( $image_url ) : ?> style="background-image:url('<?php echo esc_url( $image_url ); ?>');background-size:cover;background-position:center;"<?php endif; ?>>
	<div class="tqs-page-hero-inner">
		<?php if ( '' !== trim( $title ) ) : ?>
			<h1 class="tqs-page-title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( '' !== trim( $subtitle ) ) : ?>
			<p class="tqs-page-subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== trim( $btn_text ) && '' !== $btn_url ) : ?>
			<p class="tqs-page-hero-cta">
				<a href="<?php echo esc_url( $btn_url ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( $btn_text ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>
