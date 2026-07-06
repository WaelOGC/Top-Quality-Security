<?php
/**
 * Single service page template
 *
 * @package tqs-theme
 */
get_header();

while ( have_posts() ) : the_post();
	$icon = get_post_meta( get_the_ID(), '_tqs_service_icon', true ) ?: 'fa-shield-halved';
	$hero_img = tqs_get_hero_image_url();

	// Related services: 3 others, excluding current.
	$related = get_posts( array(
		'post_type'      => 'tqs_service',
		'posts_per_page' => 4,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'rand',
	) );
	$related = array_slice( $related, 0, 3 );
	$related_icons = array();
	foreach ( $related as $r ) {
		$related_icons[ $r->ID ] = get_post_meta( $r->ID, '_tqs_service_icon', true ) ?: 'fa-shield-halved';
	}
?>

<section class="tqs-page-hero">
	<div class="tqs-page-hero-inner">
		<?php tqs_breadcrumbs( array(
			array( 'label' => 'Onze Diensten', 'url' => get_post_type_archive_link( 'tqs_service' ) ),
			array( 'label' => get_the_title() ),
		) ); ?>
		<h1 class="tqs-page-title"><?php the_title(); ?></h1>
		<?php if ( get_the_excerpt() ) : ?>
			<p class="tqs-page-subtitle"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="tqs-service-single">
	<div class="tqs-service-single-grid">
		<div class="tqs-service-content">
			<div class="tqs-service-hero-media">
				<?php if ( $hero_img ) : ?>
					<img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php the_title_attribute(); ?>">
				<?php else : ?>
					<div class="tqs-service-card-illustration" style="display:flex; align-items:center; justify-content:center;">
						<div class="tqs-illustration-float" style="position:relative;">
							<svg width="130" height="165" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg">
								<ellipse cx="100" cy="250" rx="66" ry="12" fill="#000000" opacity="0.18"></ellipse>
								<path d="M55 60 Q100 20 145 60 L150 78 L50 78 Z" fill="#C9973A"></path>
								<rect x="45" y="74" width="110" height="10" rx="5" fill="#E8C06A"></rect>
								<circle cx="100" cy="97" r="27" fill="#E8DFF5"></circle>
								<path d="M55 222 L60 142 Q100 120 140 142 L145 222 Z" fill="#F9F6FF" opacity="0.85"></path>
								<rect x="58" y="186" width="84" height="12" fill="#C9973A"></rect>
							</svg>
							<div class="tqs-badge-icon" style="width:40px;height:40px;font-size:18px;top:-6px;right:-14px;"><i class="fa-solid <?php echo esc_attr( $icon ); ?>"></i></div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php the_content(); ?>

			<?php
			$service_reviews = tqs_get_service_reviews( get_the_ID() );
			if ( ! empty( $service_reviews ) ) :
			?>
			<section class="tqs-service-reviews" aria-labelledby="tqs-service-reviews-heading">
				<h2 id="tqs-service-reviews-heading" class="tqs-service-reviews-heading">Wat Klanten Zeggen</h2>
				<div class="tqs-service-reviews-grid">
					<?php foreach ( $service_reviews as $review ) : ?>
						<?php tqs_render_review_card( $review ); ?>
					<?php endforeach; ?>
				</div>
			</section>
			<?php endif; ?>
		</div>

		<aside class="tqs-service-sidebar">
			<div class="tqs-cta-card">
				<h3>Offerte Aanvragen voor <?php the_title(); ?></h3>
				<p>Ontvang binnen één werkdag een vrijblijvend voorstel op maat.</p>
				<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold" style="width:100%; text-align:center; padding:14px;">Offerte Aanvragen</a>
			</div>

			<?php if ( ! empty( $related ) ) : ?>
			<div class="tqs-related-card">
				<h4>Gerelateerde Diensten</h4>
				<?php foreach ( $related as $r ) : ?>
					<a href="<?php echo esc_url( get_permalink( $r ) ); ?>" class="tqs-related-item">
						<span class="tqs-related-icon"><i class="fa-solid <?php echo esc_attr( $related_icons[ $r->ID ] ); ?>"></i></span>
						<?php echo esc_html( $r->post_title ); ?>
					</a>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</aside>
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

<?php endwhile; ?>

<?php get_footer(); ?>
