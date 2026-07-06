<?php
/**
 * Header template
 *
 * @package tqs-theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( ! tqs_should_hide_header() ) : ?>

	<?php if ( get_theme_mod( 'tqs_show_topbar', true ) ) : ?>
	<div class="tqs-topbar">
		<div style="display:flex; gap:24px; align-items:center;">
			<span class="tqs-topbar-item">
				<i class="fa-solid fa-phone"></i>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' ) ); ?></a>
			</span>
			<span class="tqs-topbar-item tqs-topbar-email">
				<i class="fa-solid fa-envelope"></i>
				<a href="mailto:<?php echo esc_attr( get_theme_mod( 'tqs_email', 'info@topqualitysecurity.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'tqs_email', 'info@topqualitysecurity.com' ) ); ?></a>
			</span>
		</div>
		<div class="tqs-topbar-location"><?php echo esc_html( get_theme_mod( 'tqs_topbar_location', 'Den Haag, Nederland' ) ); ?></div>
	</div>
	<?php endif; ?>

	<header class="tqs-header">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tqs-brand">
			<?php tqs_render_site_logo( 'header' ); ?>
			<div>
				<div class="tqs-brand-text"><?php bloginfo( 'name' ); ?></div>
				<div class="tqs-brand-sub"><?php echo esc_html( get_theme_mod( 'tqs_brand_sub', 'BEVEILIGINGSDIENSTEN' ) ); ?></div>
			</div>
		</a>

		<nav class="tqs-nav" aria-label="Primary">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'link_before'    => '',
					'fallback_cb'    => 'tqs_fallback_primary_menu',
				) );
			} else {
				tqs_fallback_primary_menu();
			}
			?>
		</nav>

		<a href="<?php echo esc_url( tqs_theme_mod_url( 'tqs_header_cta_url', '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold tqs-header-cta"><?php echo esc_html( get_theme_mod( 'tqs_header_cta_text', 'Offerte Aanvragen' ) ); ?></a>

		<button class="tqs-hamburger" id="tqsHamburger" aria-label="Menu" aria-expanded="false">
			<span></span><span></span><span></span>
		</button>
	</header>

	<div class="tqs-mobile-menu" id="tqsMobileMenu">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'is-current' : ''; ?>">Home</a>
		<a href="<?php echo esc_url( home_url( '/wie-zijn-wij' ) ); ?>">Wie Zijn Wij</a>
		<div>
			<button type="button" class="tqs-mobile-services-toggle" id="tqsMobileServicesToggle">
				Onze Diensten <span id="tqsMobileServicesChevron">▼</span>
			</button>
			<div class="tqs-mobile-services-sub" id="tqsMobileServicesSub">
				<?php foreach ( tqs_get_services() as $s ) : ?>
					<a href="<?php echo esc_url( get_permalink( $s ) ); ?>"><?php echo esc_html( $s->post_title ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<a href="<?php echo esc_url( home_url( '/fotogalerij' ) ); ?>">Fotogalerij</a>
		<a href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a>
		<a href="<?php echo esc_url( tqs_theme_mod_url( 'tqs_header_cta_url', '/contact' ) ); ?>" class="tqs-btn tqs-btn-gold tqs-header-cta"><?php echo esc_html( get_theme_mod( 'tqs_header_cta_text', 'Offerte Aanvragen' ) ); ?></a>
	</div>

<?php endif; ?>
