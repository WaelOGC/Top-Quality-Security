<?php
/**
 * Homepage multi-slide hero (no chrome: arrows/dots).
 *
 * @package tqs-theme
 *
 * @var array $args {
 *     @type array $slides        Active slides.
 *     @type bool  $used_fallback Whether slide 1 was force-included.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides        = isset( $args['slides'] ) && is_array( $args['slides'] ) ? $args['slides'] : array();
$used_fallback = ! empty( $args['used_fallback'] );

if ( empty( $slides ) ) {
	return;
}

$count = count( $slides );

if ( $used_fallback ) {
	echo "\n<!-- tqs-hero: fallback to slide 1, no enabled slides found -->\n";
}
?>
<section
	class="tqs-hero"
	id="tqsHero"
	data-slide-count="<?php echo esc_attr( (string) $count ); ?>"
	data-tqs-hero-slider="1"
	aria-roledescription="carousel"
	aria-label="<?php esc_attr_e( 'Homepage hero', 'tqs-theme' ); ?>"
>
	<?php foreach ( $slides as $i => $slide ) :
		$image_id  = isset( $slide['image_id'] ) ? absint( $slide['image_id'] ) : 0;
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
		$badge     = isset( $slide['badge'] ) ? (string) $slide['badge'] : '';
		$icon      = isset( $slide['icon'] ) ? (string) $slide['icon'] : '';
		$title     = isset( $slide['title'] ) ? (string) $slide['title'] : '';
		$highlight = isset( $slide['highlight'] ) ? (string) $slide['highlight'] : '';
		$subtitle  = isset( $slide['subtitle'] ) ? (string) $slide['subtitle'] : '';
		$buttons   = function_exists( 'tqs_get_hero_slide_buttons' ) ? tqs_get_hero_slide_buttons( $slide ) : array();
		$btn1_text = isset( $buttons['btn1_text'] ) ? $buttons['btn1_text'] : '';
		$btn1_url  = isset( $buttons['btn1_url'] ) ? tqs_hero_resolve_url( $buttons['btn1_url'] ) : '';
		$btn2_text = isset( $buttons['btn2_text'] ) ? $buttons['btn2_text'] : '';
		$btn2_url  = isset( $buttons['btn2_url'] ) ? tqs_hero_resolve_url( $buttons['btn2_url'] ) : '';
		$is_first  = ( 0 === (int) $i );
		?>
	<div
		class="tqs-hero-slide<?php echo $is_first ? ' is-active' : ''; ?>"
		data-slide-index="<?php echo esc_attr( (string) $i ); ?>"
		<?php echo $is_first ? '' : ' aria-hidden="true"'; ?>
	>
		<div class="tqs-hero-slide-bg"<?php echo $image_url ? '' : ' data-fallback="1"'; ?>>
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" decoding="async"<?php echo $is_first ? ' fetchpriority="high"' : ' loading="lazy"'; ?>>
			<?php endif; ?>
		</div>
		<div class="tqs-hero-overlay" aria-hidden="true"></div>
		<div class="tqs-hero-content">
			<?php if ( '' !== trim( $badge ) ) : ?>
				<div class="tqs-hero-badge">
					<?php if ( '' !== $icon ) : ?>
						<span class="tqs-hero-icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
					<?php endif; ?>
					<?php echo esc_html( $badge ); ?>
				</div>
			<?php elseif ( '' !== $icon ) : ?>
				<div class="tqs-hero-badge tqs-hero-badge--icon-only">
					<span class="tqs-hero-icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
				</div>
			<?php endif; ?>

			<h1 class="tqs-hero-h1">
				<?php echo esc_html( $title ); ?>
				<?php if ( '' !== trim( $highlight ) ) : ?>
					<span><?php echo esc_html( $highlight ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( '' !== trim( $subtitle ) ) : ?>
				<p class="tqs-hero-p"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>

			<?php if ( ( '' !== $btn1_text && '' !== $btn1_url ) || ( '' !== $btn2_text && '' !== $btn2_url ) ) : ?>
			<div class="tqs-hero-buttons">
				<?php if ( '' !== $btn1_text && '' !== $btn1_url ) : ?>
					<a href="<?php echo esc_url( $btn1_url ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( $btn1_text ); ?></a>
				<?php endif; ?>
				<?php if ( '' !== $btn2_text && '' !== $btn2_url ) : ?>
					<a href="<?php echo esc_url( $btn2_url ); ?>" class="tqs-btn tqs-btn-outline"><?php echo esc_html( $btn2_text ); ?></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endforeach; ?>

	<div class="tqs-hero-burn-canvas-wrap" aria-hidden="true">
		<canvas class="tqs-hero-burn-canvas"></canvas>
	</div>
</section>
