<?php
/**
 * Generic page template
 * About layout when `_tqs_about_layout` / about meta is set; legal pages by slug.
 *
 * @package tqs-theme
 */
get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$slug    = get_post_field( 'post_name', $post_id );

	if ( function_exists( 'tqs_is_about_layout_page' ) && tqs_is_about_layout_page( $post_id ) ) :

		$values     = function_exists( 'tqs_get_about_values' ) ? tqs_get_about_values( $post_id ) : array();
		$guarantees = function_exists( 'tqs_get_about_guarantees' ) ? tqs_get_about_guarantees( $post_id ) : array();
		$story_img  = function_exists( 'tqs_get_story_image_url' ) ? tqs_get_story_image_url( $post_id ) : '';
		$page_title = get_the_title();

		tqs_render_hero( $post_id );
		?>

		<section class="tqs-story-section">
			<div class="tqs-story-grid">
				<div class="tqs-story-media">
					<?php if ( $story_img ) : ?>
						<img src="<?php echo esc_url( $story_img ); ?>" alt="<?php echo esc_attr( $page_title ); ?>">
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

		<?php if ( ! empty( $values ) ) : ?>
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
		<?php endif; ?>

		<?php if ( ! empty( $guarantees ) ) : ?>
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
		<?php endif; ?>

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

		<?php tqs_render_hero( $post_id ); ?>

		<section class="tqs-legal-section">
			<div class="tqs-legal-content">
				<div class="tqs-legal-updated">Laatst bijgewerkt: <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></div>
				<?php the_content(); ?>
			</div>
		</section>

	<?php else : ?>

		<?php tqs_render_hero( $post_id ); ?>

		<section class="tqs-legal-section">
			<div class="tqs-legal-content" style="max-width:880px;">
				<?php the_content(); ?>
			</div>
		</section>

	<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
