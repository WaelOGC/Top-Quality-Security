<?php
/**
 * Template Name: Fotogalerij Page
 *
 * @package tqs-theme
 */
get_header();

$gallery_columns          = tqs_sanitize_gallery_columns( get_theme_mod( 'tqs_gallery_columns', 4 ) );
$gallery_show_filters     = (bool) get_theme_mod( 'tqs_gallery_show_filters', true );
$gallery_lightbox_enabled = (bool) get_theme_mod( 'tqs_gallery_lightbox_enabled', true );

$gallery_items = get_posts( array(
	'post_type'      => 'tqs_gallery_item',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order date',
	'order'          => 'ASC',
) );

$gallery_tiles = array();
$filter_terms  = array();

foreach ( $gallery_items as $item ) {
	if ( ! has_post_thumbnail( $item->ID ) ) {
		continue;
	}

	$grid_url = get_the_post_thumbnail_url( $item->ID, 'tqs-gallery' );
	$full_url = get_the_post_thumbnail_url( $item->ID, 'full' );
	if ( ! $grid_url ) {
		continue;
	}

	$term_slugs = array();
	$terms      = get_the_terms( $item->ID, 'tqs_gallery_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_slugs[]              = $term->slug;
			$filter_terms[ $term->slug ] = $term;
		}
	}

	$style = tqs_sanitize_gallery_display_style( get_post_meta( $item->ID, '_tqs_gallery_display_style', true ) );
	$after_grid = '';
	$after_full = '';

	if ( 'before_after' === $style ) {
		$after_id = absint( get_post_meta( $item->ID, '_tqs_gallery_after_image_id', true ) );
		if ( $after_id ) {
			$after_grid = wp_get_attachment_image_url( $after_id, 'tqs-gallery' );
			$after_full = wp_get_attachment_image_url( $after_id, 'full' );
		}
		if ( ! $after_grid ) {
			$style = 'grid';
		}
	}

	$gallery_tiles[] = array(
		'title'      => get_the_title( $item ),
		'grid'       => $grid_url,
		'full'       => $full_url ? $full_url : $grid_url,
		'cats'       => implode( ' ', $term_slugs ),
		'style'      => $style,
		'after_grid' => $after_grid,
		'after_full' => $after_full ? $after_full : $after_grid,
	);
}

if ( ! empty( $filter_terms ) ) {
	uasort(
		$filter_terms,
		function ( $a, $b ) {
			return strcasecmp( $a->name, $b->name );
		}
	);
}

// Fallback category labels/icons if no gallery items published yet (illustrated placeholders).
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
	<?php if ( $gallery_show_filters && ! empty( $gallery_tiles ) && ! empty( $filter_terms ) ) : ?>
	<div class="tqs-gallery-filters" id="tqsGalleryFilters" role="toolbar" aria-label="<?php esc_attr_e( 'Filter galerij op categorie', 'tqs-theme' ); ?>">
		<button type="button" class="tqs-gallery-filter is-active" data-filter="all"><?php esc_html_e( 'Alle', 'tqs-theme' ); ?></button>
		<?php foreach ( $filter_terms as $term ) : ?>
			<button type="button" class="tqs-gallery-filter" data-filter="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="tqs-gallery-grid cols-<?php echo esc_attr( $gallery_columns ); ?>" id="tqsGalleryGrid" data-lightbox="<?php echo $gallery_lightbox_enabled ? '1' : '0'; ?>">
		<?php if ( ! empty( $gallery_tiles ) ) : ?>
			<?php foreach ( $gallery_tiles as $i => $tile ) :
				$tile_classes = array( 'tqs-gallery-tile' );
				if ( 'featured' === $tile['style'] ) {
					$tile_classes[] = 'tqs-gallery-tile--featured';
				} elseif ( 'before_after' === $tile['style'] ) {
					$tile_classes[] = 'tqs-gallery-tile--before-after';
				}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $tile_classes ) ); ?>" data-style="<?php echo esc_attr( $tile['style'] ); ?>" data-index="<?php echo esc_attr( $i ); ?>" data-full="<?php echo esc_url( $tile['full'] ); ?>"<?php echo $tile['cats'] ? ' data-category="' . esc_attr( $tile['cats'] ) . '"' : ''; ?>>
				<?php if ( 'before_after' === $tile['style'] ) : ?>
					<div class="tqs-ba-slider" style="--ba-pos: 50%;">
						<img class="tqs-ba-img tqs-ba-img--before" src="<?php echo esc_url( $tile['grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
						<div class="tqs-ba-after-wrap">
							<img class="tqs-ba-img tqs-ba-img--after" src="<?php echo esc_url( $tile['after_grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
						</div>
						<button type="button" class="tqs-ba-handle" style="left: 50%;" aria-label="<?php esc_attr_e( 'Versleep om voor en na te vergelijken', 'tqs-theme' ); ?>">
							<span class="tqs-ba-handle-grip" aria-hidden="true"></span>
						</button>
					</div>
				<?php else : ?>
					<img class="tqs-gallery-tile-img" src="<?php echo esc_url( $tile['grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
					<span class="tqs-gallery-tile-zoom"><i class="fa-solid fa-magnifying-glass"></i></span>
				<?php endif; ?>
				<div class="tqs-gallery-tile-caption"><?php echo esc_html( $tile['title'] ); ?></div>
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
			<p class="tqs-gallery-empty-msg">
				<em><?php esc_html_e( 'Nog geen foto\'s beschikbaar. Voeg galerij items toe via Gallery in wp-admin (titel, uitgelichte afbeelding en categorie).', 'tqs-theme' ); ?></em>
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
