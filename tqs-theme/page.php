<?php
/**
 * Generic page template
 * Special layout auto-applied for "Wie Zijn Wij" (about) and legal pages.
 *
 * @package tqs-theme
 */
get_header();

while ( have_posts() ) : the_post();

	$slug = get_post_field( 'post_name' );
	$hide_hero = '1' === get_post_meta( get_the_ID(), '_tqs_hide_hero', true );
	$title_override = get_post_meta( get_the_ID(), '_tqs_hero_title_override', true );
	$display_title = $title_override ?: get_the_title();

	if ( 'wie-zijn-wij' === $slug ) :

		$values = array(
			array( 'icon' => 'fa-handshake', 'title' => 'Betrouwbaarheid', 'desc' => 'Wij komen afspraken na en zijn er wanneer het erop aankomt.' ),
			array( 'icon' => 'fa-user-graduate', 'title' => 'Professionaliteit', 'desc' => 'Opgeleid, gecertificeerd en representatief in elke situatie.' ),
			array( 'icon' => 'fa-eye', 'title' => 'Alertheid', 'desc' => 'Scherp opmerkzaam, zonder de sfeer te verstoren.' ),
			array( 'icon' => 'fa-comments', 'title' => 'Klantgerichtheid', 'desc' => 'Maatwerk oplossingen die aansluiten bij uw organisatie.' ),
		);
		$guarantees = array(
			array( 'icon' => 'fa-user-shield', 'title' => 'Gescreend Personeel', 'desc' => 'Alle beveiligers doorlopen een strenge screening.' ),
			array( 'icon' => 'fa-file-shield', 'title' => 'ND 7099 Gecertificeerd', 'desc' => 'Voldoen aan de hoogste kwaliteitsnorm in de branche.' ),
			array( 'icon' => 'fa-clock', 'title' => '24/7 Bereikbaarheid', 'desc' => 'Altijd te bereiken, ook buiten kantoortijden.' ),
			array( 'icon' => 'fa-gears', 'title' => 'Maatwerk Aanpak', 'desc' => 'Elke opdracht krijgt een plan op maat.' ),
		);
		$hero_img = tqs_get_hero_image_url();
		?>

		<?php if ( ! $hide_hero ) : ?>
		<section class="tqs-page-hero">
			<div class="tqs-page-hero-inner">
				<?php tqs_breadcrumbs( array( array( 'label' => $display_title ) ) ); ?>
				<h1 class="tqs-page-title"><?php echo esc_html( $display_title ); ?></h1>
				<p class="tqs-page-subtitle">Maak kennis met TQS — een gecertificeerde beveiligingspartner met een persoonlijke aanpak.</p>
			</div>
		</section>
		<?php endif; ?>

		<section class="tqs-story-section">
			<div class="tqs-story-grid">
				<div class="tqs-story-media">
					<?php if ( $hero_img ) : ?>
						<img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php echo esc_attr( $display_title ); ?>">
					<?php else : ?>
						<div style="display:flex; align-items:center; justify-content:center; width:100%; height:100%;">
							<div class="tqs-illustration-float" style="position:relative; display:flex; gap:8px;">
								<svg width="90" height="115" viewBox="0 0 200 260" xmlns="http://www.w3.org/2000/svg">
									<ellipse cx="100" cy="250" rx="66" ry="12" fill="#000000" opacity="0.15"></ellipse>
									<path d="M55 60 Q100 20 145 60 L150 78 L50 78 Z" fill="#C9973A"></path>
									<rect x="45" y="74" width="110" height="10" rx="5" fill="#E8C06A"></rect>
									<circle cx="100" cy="97" r="27" fill="#E8DFF5"></circle>
									<path d="M55 222 L60 142 Q100 120 140 142 L145 222 Z" fill="#F9F6FF" opacity="0.9"></path>
									<rect x="58" y="186" width="84" height="12" fill="#C9973A"></rect>
								</svg>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div>
					<div class="tqs-section-eyebrow">ONS VERHAAL</div>
					<h2 class="tqs-section-h2" style="font-weight:800; font-size:34px; color:#2D0A4E; margin:0 0 20px; line-height:1.2;">Meer dan tien jaar toegewijd aan veiligheid</h2>
					<?php the_content(); ?>
				</div>
			</div>
		</section>

		<section class="tqs-values-section">
			<div class="tqs-values-header">
				<div class="tqs-section-eyebrow">ONZE KERNWAARDEN</div>
				<h2 class="tqs-section-h2" style="font-weight:800; font-size:34px; color:#2D0A4E; margin:0;">Waar wij voor staan</h2>
			</div>
			<div class="tqs-values-grid">
				<?php foreach ( $values as $val ) : ?>
					<div class="tqs-value-card">
						<div class="tqs-value-icon"><i class="fa-solid <?php echo esc_attr( $val['icon'] ); ?>" style="color:#fff;"></i></div>
						<h4><?php echo esc_html( $val['title'] ); ?></h4>
						<p><?php echo esc_html( $val['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="tqs-guarantee-band">
			<div class="tqs-guarantee-grid">
				<?php foreach ( $guarantees as $g ) : ?>
					<div class="tqs-guarantee-item">
						<i class="fa-solid <?php echo esc_attr( $g['icon'] ); ?>" style="color:#C9973A; font-size:24px;"></i>
						<h4><?php echo esc_html( $g['title'] ); ?></h4>
						<p><?php echo esc_html( $g['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="tqs-cta-banner">
			<div class="tqs-cta-inner">
				<h2 class="tqs-cta-h2"><?php echo esc_html( get_theme_mod( 'tqs_cta_title', 'Klaar voor professionele beveiliging?' ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'tqs_cta_text', 'Vraag vandaag nog een vrijblijvende offerte aan en ontdek wat TQS voor u kan betekenen.' ) ); ?></p>
				<div class="tqs-cta-buttons">
					<a href="<?php echo esc_url( tqs_theme_mod_url( 'tqs_cta_btn1_url', '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( get_theme_mod( 'tqs_cta_btn1_text', 'Offerte Aanvragen' ) ); ?></a>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ) ); ?>" class="tqs-btn tqs-btn-outline"><?php echo esc_html( get_theme_mod( 'tqs_cta_btn2_text', 'Bel Ons Direct' ) ); ?></a>
				</div>
			</div>
		</section>

	<?php elseif ( in_array( $slug, array( 'privacybeleid', 'algemene-voorwaarden' ), true ) ) : ?>

		<section class="tqs-page-hero" style="padding:44px 40px;">
			<div class="tqs-page-hero-inner">
				<?php tqs_breadcrumbs( array( array( 'label' => $display_title ) ) ); ?>
				<h1 class="tqs-page-title" style="font-size:38px;"><?php echo esc_html( $display_title ); ?></h1>
			</div>
		</section>

		<section class="tqs-legal-section">
			<div class="tqs-legal-content">
				<div class="tqs-legal-updated">Laatst bijgewerkt: <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></div>
				<?php the_content(); ?>
			</div>
		</section>

	<?php else : ?>

		<?php if ( ! $hide_hero ) :
			$hero_img = tqs_get_hero_image_url();
		?>
		<section class="tqs-page-hero" <?php if ( $hero_img ) : ?>style="background-image:url('<?php echo esc_url( $hero_img ); ?>'); background-size:cover; background-position:center;"<?php endif; ?>>
			<div class="tqs-page-hero-inner">
				<?php tqs_breadcrumbs( array( array( 'label' => $display_title ) ) ); ?>
				<h1 class="tqs-page-title"><?php echo esc_html( $display_title ); ?></h1>
			</div>
		</section>
		<?php endif; ?>

		<section class="tqs-legal-section">
			<div class="tqs-legal-content" style="max-width:880px;">
				<?php the_content(); ?>
			</div>
		</section>

	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
