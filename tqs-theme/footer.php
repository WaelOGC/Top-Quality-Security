<?php
/**
 * Footer template
 *
 * @package tqs-theme
 */

$legal_bits = array();

if ( get_theme_mod( 'tqs_show_footer_kvk', false ) && get_theme_mod( 'tqs_kvk', '' ) ) {
	$legal_bits[] = 'KvK: ' . get_theme_mod( 'tqs_kvk' );
}
if ( get_theme_mod( 'tqs_show_footer_btw', false ) && get_theme_mod( 'tqs_btw', '' ) ) {
	$legal_bits[] = 'BTW: ' . get_theme_mod( 'tqs_btw' );
}
if ( get_theme_mod( 'tqs_show_footer_nd', true ) && get_theme_mod( 'tqs_nd_cert', 'ND 7099' ) ) {
	$legal_bits[] = get_theme_mod( 'tqs_nd_cert', 'ND 7099' );
}
?>

<?php if ( ! tqs_should_hide_footer() ) : ?>
	<footer class="tqs-footer">
		<div class="tqs-footer-grid">
			<div>
				<div class="tqs-footer-brand">
					<?php tqs_render_site_logo( 'footer' ); ?>
					<div class="tqs-footer-brand-name"><?php bloginfo( 'name' ); ?></div>
				</div>
				<p class="tqs-footer-desc"><?php echo esc_html( get_theme_mod( 'tqs_footer_tagline', 'Professionele beveiligingsdiensten vanuit Den Haag, actief door heel Nederland. Gecertificeerd, betrouwbaar en 24/7 paraat.' ) ); ?></p>
				<?php if ( ! empty( $legal_bits ) ) : ?>
					<p class="tqs-footer-legal-bits" style="font-size:12px;color:var(--tqs-muted);margin:0 0 14px;"><?php echo esc_html( implode( ' · ', $legal_bits ) ); ?></p>
				<?php endif; ?>
				<?php if ( get_theme_mod( 'tqs_show_footer_social', true ) ) : ?>
				<div class="tqs-footer-socials">
					<?php foreach ( tqs_social_links() as $key => $url ) :
						$icon_map = array( 'linkedin' => 'fa-brands fa-linkedin-in', 'facebook' => 'fa-brands fa-facebook-f', 'instagram' => 'fa-brands fa-instagram', 'x' => 'fa-brands fa-x-twitter' ); ?>
						<a href="<?php echo esc_url( $url ); ?>" class="tqs-social" target="_blank" rel="noopener"><i class="<?php echo esc_attr( $icon_map[ $key ] ?? 'fa-solid fa-link' ); ?>"></i></a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</div>

			<div>
				<h5><?php echo esc_html( get_theme_mod( 'tqs_footer_col_quick', 'SNELLE LINKS' ) ); ?></h5>
				<div class="tqs-footer-links">
					<?php if ( has_nav_menu( 'footer-quick' ) ) :
						wp_nav_menu( array( 'theme_location' => 'footer-quick', 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'depth' => 1 ) );
					else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
						<a href="<?php echo esc_url( home_url( '/wie-zijn-wij' ) ); ?>">Wie Zijn Wij</a>
						<a href="<?php echo esc_url( home_url( '/fotogalerij' ) ); ?>">Fotogalerij</a>
						<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a>
					<?php endif; ?>
				</div>
			</div>

			<div>
				<h5><?php echo esc_html( get_theme_mod( 'tqs_footer_col_services', 'DIENSTEN' ) ); ?></h5>
				<div class="tqs-footer-links">
					<?php if ( has_nav_menu( 'footer-services' ) ) :
						wp_nav_menu( array( 'theme_location' => 'footer-services', 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'depth' => 1 ) );
					else :
						foreach ( tqs_get_services( 4 ) as $s ) : ?>
							<a href="<?php echo esc_url( get_permalink( $s ) ); ?>"><?php echo esc_html( $s->post_title ); ?></a>
						<?php endforeach;
					endif; ?>
				</div>
			</div>

			<div>
				<h5><?php echo esc_html( get_theme_mod( 'tqs_footer_col_contact', 'CONTACT' ) ); ?></h5>
				<div class="tqs-footer-contact-row"><span class="ic"><i class="fa-solid fa-location-dot"></i></span> <?php echo esc_html( get_theme_mod( 'tqs_address', 'Spui 70, 2511 BT Den Haag' ) ); ?></div>
				<div class="tqs-footer-contact-row"><span class="ic"><i class="fa-solid fa-phone"></i></span> <?php echo esc_html( get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ); ?></div>
				<div class="tqs-footer-contact-row"><span class="ic"><i class="fa-solid fa-envelope"></i></span> <?php echo esc_html( get_theme_mod( 'tqs_email', 'info@topqualitysecurity.com' ) ); ?></div>
			</div>
		</div>

		<div class="tqs-footer-bottom">
			<div class="tqs-footer-copy"><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), get_theme_mod( 'tqs_copyright', '© {year} Top Quality Security. Alle rechten voorbehouden.' ) ) ); ?></div>
			<div class="tqs-footer-legal-links">
				<?php if ( has_nav_menu( 'footer-legal' ) ) :
					wp_nav_menu( array( 'theme_location' => 'footer-legal', 'container' => false, 'items_wrap' => '%3$s', 'link_before' => '', 'depth' => 1 ) );
				else : ?>
					<a href="<?php echo esc_url( home_url( '/privacybeleid' ) ); ?>">Privacybeleid</a>
					<a href="<?php echo esc_url( home_url( '/algemene-voorwaarden' ) ); ?>">Algemene Voorwaarden</a>
				<?php endif; ?>
			</div>
		</div>
	</footer>

	<?php if ( get_theme_mod( 'tqs_cookie_enabled', true ) ) : ?>
	<div class="tqs-cookie-banner" id="tqsCookieBanner">
		<p><?php echo wp_kses_post( get_theme_mod( 'tqs_cookie_text', 'Wij gebruiken cookies om uw ervaring te verbeteren. Lees ons <a href="/privacybeleid">privacybeleid</a> voor meer informatie.' ) ); ?></p>
		<div class="tqs-cookie-actions">
			<button type="button" class="tqs-cookie-accept" id="tqsCookieAccept"><?php echo esc_html( get_theme_mod( 'tqs_cookie_accept_text', 'Accepteren' ) ); ?></button>
			<button type="button" class="tqs-cookie-decline" id="tqsCookieDecline"><?php echo esc_html( get_theme_mod( 'tqs_cookie_decline_text', 'Weigeren' ) ); ?></button>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'tqs_show_whatsapp_fab', true ) ) :
		$wa = preg_replace( '/[^0-9]/', '', get_theme_mod( 'tqs_whatsapp_number', '31636286183' ) ); ?>
	<a href="https://wa.me/<?php echo esc_attr( $wa ); ?>" class="tqs-whatsapp-fab" target="_blank" rel="noopener" aria-label="WhatsApp">
		<i class="fa-brands fa-whatsapp"></i>
	</a>
	<?php endif; ?>

	<button type="button" class="tqs-back-to-top" id="tqsBackToTop" aria-label="Terug naar boven">
		<i class="fa-solid fa-arrow-up"></i>
	</button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
