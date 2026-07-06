<?php
/**
 * Template Name: Fotogalerij Page
 *
 * @package tqs-theme
 */
get_header();

$ids = get_post_meta( get_the_ID(), '_tqs_gallery_ids', true );
$ids = $ids ? array_filter( explode( ',', $ids ) ) : array();

// Fallback category labels/icons if no images uploaded yet (illustrated placeholders).
$placeholder_tiles = array(
	array( 'label' => 'Retailbeveiliging', 'icon' => 'fa-store' ),
	array( 'label' => 'Horecabeveiliging', 'icon' => 'fa-martini-glass' ),
	array( 'label' => 'Evenementenbeveiliging', 'icon' => 'fa-tent' ),
	array( 'label' => 'Objectbeveiliging', 'icon' => 'fa-building' ),
	array( 'label' => 'Supermarktbeveiliging', 'icon' => 'fa-cart-shopping' ),
	array( 'label' => "Casino's Beveiliging", 'icon' => 'fa-dice' ),
	array( 'label' => 'Nachtelijke Surveillance', 'icon' => 'fa-flashlight' ),
	array( 'label' => 'Mobiele Surveillance', 'icon' => 'fa-car' ),
	array( 'label' => 'Toegangscontrole', 'icon' => 'fa-clipboard-check' ),
	array( 'label' => 'Teamoverleg', 'icon' => 'fa-people-group' ),
	array( 'label' => 'Hotelbeveiliging', 'icon' => 'fa-hotel' ),
	array( 'label' => 'ND 7099 Certificering', 'icon' => 'fa-graduation-cap' ),
);
?>

<section class="tqs-page-hero">
	<div class="tqs-page-hero-inner">
		<?php tqs_breadcrumbs( array( array( 'label' => 'Fotogalerij' ) ) ); ?>
		<h1 class="tqs-page-title">Fotogalerij</h1>
		<p class="tqs-page-subtitle">Een impressie van ons werk in de praktijk.</p>
	</div>
</section>

<section class="tqs-gallery-section">
	<div class="tqs-gallery-grid" id="tqsGalleryGrid">
		<?php if ( ! empty( $ids ) ) : ?>
			<?php foreach ( $ids as $i => $id ) :
				$url  = wp_get_attachment_image_url( $id, 'tqs-gallery' );
				$full = wp_get_attachment_image_url( $id, 'large' );
				$alt  = get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: get_the_title();
				if ( ! $url ) continue;
			?>
			<div class="tqs-gallery-tile" data-index="<?php echo esc_attr( $i ); ?>" data-full="<?php echo esc_url( $full ?: $url ); ?>">
				<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
				<span class="tqs-gallery-tile-zoom"><i class="fa-solid fa-magnifying-glass"></i></span>
				<div class="tqs-gallery-tile-caption"><?php echo esc_html( $alt ); ?></div>
			</div>
			<?php endforeach; ?>
		<?php else : ?>
			<?php foreach ( $placeholder_tiles as $i => $tile ) : ?>
			<div class="tqs-gallery-tile" data-index="<?php echo esc_attr( $i ); ?>" data-placeholder="1">
				<div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center;">
					<div class="tqs-illustration-float" style="position:relative; opacity:0.9;">
						<svg width="80" height="100" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg">
							<ellipse cx="100" cy="250" rx="66" ry="12" fill="#000000" opacity="0.18"></ellipse>
							<path d="M55 60 Q100 20 145 60 L150 78 L50 78 Z" fill="#C9973A"></path>
							<rect x="45" y="74" width="110" height="10" rx="5" fill="#E8C06A"></rect>
							<circle cx="100" cy="97" r="27" fill="#E8DFF5"></circle>
							<path d="M55 222 L60 142 Q100 120 140 142 L145 222 Z" fill="#F9F6FF" opacity="0.85"></path>
							<rect x="58" y="186" width="84" height="12" fill="#C9973A"></rect>
						</svg>
						<div class="tqs-badge-icon" style="width:30px;height:30px;font-size:14px;top:-4px;right:-10px;"><i class="fa-solid <?php echo esc_attr( $tile['icon'] ); ?>"></i></div>
					</div>
				</div>
				<span class="tqs-gallery-tile-zoom"><i class="fa-solid fa-magnifying-glass"></i></span>
				<div class="tqs-gallery-tile-caption"><?php echo esc_html( $tile['label'] ); ?></div>
			</div>
			<?php endforeach; ?>
			<p style="grid-column:1/-1; text-align:center; color:#9080A8; font-size:14px; margin-top:10px;">
				<em>Nog geen foto's geüpload. Ga naar de pagina-editor van Fotogalerij → "🖼️ Galerij Afbeeldingen" om echte foto's toe te voegen.</em>
			</p>
		<?php endif; ?>
	</div>
</section>

<section class="tqs-cta-banner">
	<div class="tqs-cta-inner">
		<h2 class="tqs-cta-h2">Klaar voor professionele beveiliging?</h2>
		<p>Vraag vandaag nog een vrijblijvende offerte aan en ontdek wat TQS voor u kan betekenen.</p>
		<div class="tqs-cta-buttons">
			<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold">Offerte Aanvragen</a>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ) ); ?>" class="tqs-btn tqs-btn-outline">Bel Ons Direct</a>
		</div>
	</div>
</section>

<!-- LIGHTBOX -->
<div class="tqs-lightbox" id="tqsLightbox">
	<button class="tqs-lightbox-close" id="tqsLightboxClose" aria-label="Sluiten">&times;</button>
	<button class="tqs-lightbox-prev" id="tqsLightboxPrev" aria-label="Vorige">‹</button>
	<img src="" alt="" id="tqsLightboxImg">
	<button class="tqs-lightbox-next" id="tqsLightboxNext" aria-label="Volgende">›</button>
</div>

<?php get_footer(); ?>
