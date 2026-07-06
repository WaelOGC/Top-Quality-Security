<?php
/**
 * Homepage template
 *
 * @package tqs-theme
 */
get_header();

$services = tqs_get_services();

$hero_slides = array(
	array(
		'gradient'  => 'linear-gradient(135deg, #1A0533 0%, #2D0A4E 55%, #4a1b7a 100%)',
		'icon'      => '🛡',
		'badge'     => 'ND 7099 GECERTIFICEERD',
		'title'     => 'Uw veiligheid is onze',
		'highlight' => 'topprioriteit',
		'subtitle'  => "Wij staan voor kwaliteit en betrouwbaarheid in beveiliging. Van retail tot evenementen — TQS levert professionele beveiligingsoplossingen door heel Nederland, vanuit Den Haag.",
	),
	array(
		'gradient'  => 'linear-gradient(135deg, #2D0A4E 0%, #6a2499 55%, #8B2FC9 100%)',
		'icon'      => '🏬',
		'badge'     => 'RETAIL & HORECA',
		'title'     => 'Specialisten in',
		'highlight' => 'winkel- en horecabeveiliging',
		'subtitle'  => 'Onze beveiligers zorgen voor een veilige, gastvrije omgeving in winkels, bars en restaurants — alert, benaderbaar en professioneel.',
	),
	array(
		'gradient'  => 'linear-gradient(135deg, #1A0533 0%, #3d1466 50%, #8B2FC9 100%)',
		'icon'      => '🎪',
		'badge'     => 'EVENEMENTEN & OBJECTEN',
		'title'     => 'Veiligheid voor elk',
		'highlight' => 'evenement en pand',
		'subtitle'  => 'Van festivals tot hotels en bedrijfspanden: wij bieden beveiligingsoplossingen die passen bij uw specifieke situatie.',
	),
);

$why_us = array(
	array( 'icon' => 'fa-shield-halved', 'title' => 'ND 7099 Gecertificeerd', 'desc' => 'Voldoen aan de hoogste kwaliteitsnorm in de beveiligingsbranche.' ),
	array( 'icon' => 'fa-graduation-cap', 'title' => 'Ervaren Personeel', 'desc' => 'Goed opgeleide, representatieve beveiligers met jarenlange ervaring.' ),
	array( 'icon' => 'fa-clock', 'title' => '24/7 Beschikbaar', 'desc' => 'Altijd bereikbaar, ook buiten kantoortijden en in het weekend.' ),
	array( 'icon' => 'fa-gear', 'title' => 'Maatwerk Oplossingen', 'desc' => 'Beveiligingsplannen afgestemd op de specifieke situatie van uw organisatie.' ),
);
?>

<!-- HERO SLIDER -->
<section class="tqs-hero" id="tqsHero">
	<?php foreach ( $hero_slides as $i => $slide ) : ?>
	<div class="tqs-hero-slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-slide="<?php echo esc_attr( $i ); ?>">
		<div class="tqs-hero-slide-bg" style="background: <?php echo esc_attr( $slide['gradient'] ); ?>;"></div>
		<div class="tqs-hero-illustration">
			<div class="tqs-illustration-float" style="position:relative;">
				<svg width="230" height="290" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg" style="opacity:0.92;">
					<ellipse cx="100" cy="250" rx="66" ry="12" fill="#000000" opacity="0.18"></ellipse>
					<path d="M55 60 Q100 20 145 60 L150 78 L50 78 Z" fill="#C9973A"></path>
					<rect x="45" y="74" width="110" height="10" rx="5" fill="#E8C06A"></rect>
					<circle cx="100" cy="97" r="27" fill="#E8DFF5"></circle>
					<path d="M55 222 L60 142 Q100 120 140 142 L145 222 Z" fill="#B255DE"></path>
					<rect x="58" y="186" width="84" height="12" fill="#C9973A"></rect>
				</svg>
				<div class="tqs-badge-icon"><?php echo esc_html( $slide['icon'] ); ?></div>
			</div>
		</div>
		<div class="tqs-hero-overlay"></div>
		<div class="tqs-hero-content">
			<div class="tqs-hero-badge"><?php echo esc_html( $slide['badge'] ); ?></div>
			<h1 class="tqs-hero-h1"><?php echo esc_html( $slide['title'] ); ?> <span><?php echo esc_html( $slide['highlight'] ); ?></span></h1>
			<p class="tqs-hero-p"><?php echo esc_html( $slide['subtitle'] ); ?></p>
			<div class="tqs-hero-buttons">
				<a href="<?php echo esc_url( get_theme_mod( 'tqs_hero_btn1_url', '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( get_theme_mod( 'tqs_hero_btn1_text', 'Offerte Aanvragen' ) ); ?></a>
				<a href="<?php echo esc_url( get_theme_mod( 'tqs_hero_btn2_url', '/onze-diensten' ) ); ?>" class="tqs-btn tqs-btn-outline"><?php echo esc_html( get_theme_mod( 'tqs_hero_btn2_text', 'Onze Diensten →' ) ); ?></a>
			</div>
		</div>
	</div>
	<?php endforeach; ?>

	<button class="tqs-slide-arrow tqs-arrow-prev" id="tqsHeroPrev" aria-label="Vorige">‹</button>
	<button class="tqs-slide-arrow tqs-arrow-next" id="tqsHeroNext" aria-label="Volgende">›</button>
	<div class="tqs-hero-dots" id="tqsHeroDots">
		<?php foreach ( $hero_slides as $i => $slide ) : ?>
			<button type="button" class="tqs-slide-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-goto="<?php echo esc_attr( $i ); ?>"></button>
		<?php endforeach; ?>
	</div>
</section>

<!-- STATS BAR -->
<section class="tqs-stats">
	<?php foreach ( tqs_get_stats() as $stat ) : if ( empty( $stat['value'] ) ) continue; ?>
		<div class="tqs-stat">
			<div class="tqs-stat-value"><?php echo esc_html( $stat['value'] ); ?></div>
			<div class="tqs-stat-label"><?php echo esc_html( $stat['label'] ); ?></div>
		</div>
	<?php endforeach; ?>
</section>

<!-- SERVICES SLIDER -->
<section id="diensten" class="tqs-services-section">
	<div class="tqs-section-intro">
		<div class="tqs-section-eyebrow">ONZE DIENSTEN</div>
		<h2 class="tqs-section-h2">Beveiligingsoplossingen op maat</h2>
		<p class="tqs-section-lead">Voor elke sector een passende aanpak — professioneel, gecertificeerd en altijd paraat.</p>
	</div>

	<div class="tqs-services-slider-wrap">
		<div style="position:relative;">
			<div id="tqsServicesTrack">
				<?php
				$chunk_size = 2; // desktop/tablet default; JS switches to 1 on mobile
				$pages = array_chunk( $services, $chunk_size );
				foreach ( $pages as $page_i => $page_services ) :
				?>
				<div class="tqs-services-track<?php echo 0 === $page_i ? ' is-active' : ''; ?>" data-page="<?php echo esc_attr( $page_i ); ?>">
					<?php foreach ( $page_services as $svc ) :
						$icon = get_post_meta( $svc->ID, '_tqs_service_icon', true ) ?: 'fa-shield-halved';
						$img = get_the_post_thumbnail_url( $svc, 'tqs-card' );
					?>
					<div class="tqs-service-slide-card">
						<div class="tqs-service-slide-bg" style="background: linear-gradient(150deg, #2D0A4E 0%, #6a2499 100%);">
							<?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $svc->post_title ); ?>" style="width:100%;height:100%;object-fit:cover;"><?php endif; ?>
						</div>
						<?php if ( ! $img ) : ?>
						<div class="tqs-service-slide-illustration">
							<div class="tqs-illustration-float" style="position:relative; opacity:0.9;">
								<svg width="110" height="140" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg">
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
						<div class="tqs-service-slide-overlay"></div>
						<div class="tqs-service-slide-content">
							<h3><?php echo esc_html( $svc->post_title ); ?></h3>
							<p><?php echo esc_html( $svc->post_excerpt ); ?></p>
							<a href="<?php echo esc_url( get_permalink( $svc ) ); ?>">Meer Info →</a>
						</div>
					</div>
					<?php endforeach; ?>
					<?php if ( count( $page_services ) < 2 ) : ?>
					<div class="tqs-service-contact-slide">
						<div style="font-size:36px;">📞</div>
						<h3>Andere sector?</h3>
						<p>Neem contact op voor meer informatie over een oplossing op maat.</p>
						<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold" style="padding:12px 26px;font-size:14px;">Neem Contact Op</a>
					</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>

			<button class="tqs-service-arrow tqs-service-arrow-prev" id="tqsServicePrev" aria-label="Vorige">‹</button>
			<button class="tqs-service-arrow tqs-service-arrow-next" id="tqsServiceNext" aria-label="Volgende">›</button>
		</div>

		<div class="tqs-service-dots-row">
			<div class="tqs-service-dots" id="tqsServiceDots">
				<?php foreach ( $pages as $page_i => $page_services ) : ?>
					<button type="button" class="tqs-service-dot<?php echo 0 === $page_i ? ' is-active' : ''; ?>" data-goto="<?php echo esc_attr( $page_i ); ?>"></button>
				<?php endforeach; ?>
			</div>
			<div class="tqs-service-counter"><span id="tqsServiceCounter">1</span> / <span id="tqsServiceTotal"><?php echo esc_html( count( $pages ) ); ?></span></div>
		</div>
	</div>
</section>

<!-- WHY US -->
<section class="tqs-whyus">
	<div class="tqs-whyus-outer">
		<div>
			<div class="tqs-section-eyebrow">WAAROM TQS</div>
			<h2 class="tqs-whyus-h2">Betrouwbaarheid die u kunt zien</h2>
			<p class="tqs-whyus-text">Al meer dan tien jaar biedt TQS professionele beveiliging aan bedrijven, evenementen en instellingen door heel Nederland. Onze medewerkers zijn opgeleid, gecertificeerd en altijd representatief.</p>
		</div>
		<div class="tqs-whyus-cards">
			<?php foreach ( $why_us as $item ) : ?>
				<div class="tqs-whyus-card">
					<div class="tqs-whyus-card-icon"><i class="fa-solid <?php echo esc_attr( $item['icon'] ); ?>"></i></div>
					<h4><?php echo esc_html( $item['title'] ); ?></h4>
					<p><?php echo esc_html( $item['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- FINAL CTA -->
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
