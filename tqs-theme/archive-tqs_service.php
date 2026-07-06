<?php
/**
 * Onze Diensten — Services archive template
 *
 * @package tqs-theme
 */
get_header();
$services = tqs_get_services();
?>

<section class="tqs-page-hero">
	<div class="tqs-page-hero-inner">
		<?php tqs_breadcrumbs( array( array( 'label' => 'Onze Diensten' ) ) ); ?>
		<h1 class="tqs-page-title"><?php echo esc_html( get_theme_mod( 'tqs_archive_services_title', 'Onze Diensten' ) ); ?></h1>
		<p class="tqs-page-subtitle"><?php echo esc_html( get_theme_mod( 'tqs_archive_services_lead', 'Van winkelvloer tot evenemententerrein — TQS levert beveiligingsoplossingen op maat voor iedere sector, met gecertificeerd en professioneel personeel.' ) ); ?></p>
	</div>
</section>

<section class="tqs-services-archive">
	<div class="tqs-services-grid">
		<?php foreach ( $services as $svc ) :
			$icon = get_post_meta( $svc->ID, '_tqs_service_icon', true ) ?: 'fa-shield-halved';
			$img  = get_the_post_thumbnail_url( $svc, 'tqs-card' );
		?>
		<div class="tqs-service-card tqs-card">
			<div class="tqs-service-card-media">
				<?php if ( $img ) : ?>
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $svc->post_title ); ?>">
				<?php else : ?>
					<div class="tqs-service-card-illustration" style="background: linear-gradient(150deg, #2D0A4E 0%, #6a2499 100%); display:flex; align-items:center; justify-content:center;">
						<div class="tqs-illustration-float" style="position:relative;">
							<svg width="90" height="115" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg">
								<ellipse cx="100" cy="250" rx="66" ry="12" fill="#000000" opacity="0.18"></ellipse>
								<path d="M55 60 Q100 20 145 60 L150 78 L50 78 Z" fill="#C9973A"></path>
								<rect x="45" y="74" width="110" height="10" rx="5" fill="#E8C06A"></rect>
								<circle cx="100" cy="97" r="27" fill="#E8DFF5"></circle>
								<path d="M55 222 L60 142 Q100 120 140 142 L145 222 Z" fill="#F9F6FF" opacity="0.85"></path>
								<rect x="58" y="186" width="84" height="12" fill="#C9973A"></rect>
							</svg>
							<div class="tqs-badge-icon" style="width:34px;height:34px;font-size:16px;top:-6px;right:-12px;"><i class="fa-solid <?php echo esc_attr( $icon ); ?>"></i></div>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="tqs-service-card-body">
				<h3 class="tqs-service-card-title"><?php echo esc_html( $svc->post_title ); ?></h3>
				<p class="tqs-service-card-desc"><?php echo esc_html( $svc->post_excerpt ); ?></p>
				<a href="<?php echo esc_url( get_permalink( $svc ) ); ?>" class="tqs-service-card-link tqs-link">Meer Info →</a>
			</div>
		</div>
		<?php endforeach; ?>
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

<?php get_footer(); ?>
