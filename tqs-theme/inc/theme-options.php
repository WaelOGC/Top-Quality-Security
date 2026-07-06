<?php
/**
 * Theme options helpers — defaults, getters, dynamic CSS, analytics.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand color defaults (match style.css :root).
 */
function tqs_get_brand_color_defaults() {
	return array(
		'tqs_color_primary'     => '#2D0A4E',
		'tqs_color_secondary'   => '#8B2FC9',
		'tqs_color_gold'        => '#C9973A',
		'tqs_color_gold_light'  => '#E8C06A',
		'tqs_color_dark'        => '#1A0533',
		'tqs_color_light'       => '#F9F6FF',
		'tqs_color_text'        => '#4a3a5e',
		'tqs_color_muted'       => '#9080A8',
	);
}

/**
 * Default hero slide data (homepage slider).
 */
function tqs_get_default_hero_slides() {
	return array(
		array(
			'enabled'   => true,
			'gradient'  => 'linear-gradient(135deg, #1A0533 0%, #2D0A4E 55%, #4a1b7a 100%)',
			'icon'      => '🛡',
			'badge'     => 'ND 7099 GECERTIFICEERD',
			'title'     => 'Uw veiligheid is onze',
			'highlight' => 'topprioriteit',
			'subtitle'  => 'Wij staan voor kwaliteit en betrouwbaarheid in beveiliging. Van retail tot evenementen — TQS levert professionele beveiligingsoplossingen door heel Nederland, vanuit Den Haag.',
		),
		array(
			'enabled'   => true,
			'gradient'  => 'linear-gradient(135deg, #2D0A4E 0%, #6a2499 55%, #8B2FC9 100%)',
			'icon'      => '🏬',
			'badge'     => 'RETAIL & HORECA',
			'title'     => 'Specialisten in',
			'highlight' => 'winkel- en horecabeveiliging',
			'subtitle'  => 'Onze beveiligers zorgen voor een veilige, gastvrije omgeving in winkels, bars en restaurants — alert, benaderbaar en professioneel.',
		),
		array(
			'enabled'   => true,
			'gradient'  => 'linear-gradient(135deg, #1A0533 0%, #3d1466 50%, #8B2FC9 100%)',
			'icon'      => '🎪',
			'badge'     => 'EVENEMENTEN & OBJECTEN',
			'title'     => 'Veiligheid voor elk',
			'highlight' => 'evenement en pand',
			'subtitle'  => 'Van festivals tot hotels en bedrijfspanden: wij bieden beveiligingsoplossingen die passen bij uw specifieke situatie.',
		),
	);
}

/**
 * Default "Why Us" cards.
 */
function tqs_get_default_why_us_cards() {
	return array(
		array( 'icon' => 'fa-shield-halved', 'title' => 'ND 7099 Gecertificeerd', 'desc' => 'Voldoen aan de hoogste kwaliteitsnorm in de beveiligingsbranche.' ),
		array( 'icon' => 'fa-graduation-cap', 'title' => 'Ervaren Personeel', 'desc' => 'Goed opgeleide, representatieve beveiligers met jarenlange ervaring.' ),
		array( 'icon' => 'fa-clock', 'title' => '24/7 Beschikbaar', 'desc' => 'Altijd bereikbaar, ook buiten kantoortijden en in het weekend.' ),
		array( 'icon' => 'fa-gear', 'title' => 'Maatwerk Oplossingen', 'desc' => 'Beveiligingsplannen afgestemd op de specifieke situatie van uw organisatie.' ),
	);
}

function tqs_sanitize_hex_color( $color ) {
	$color = sanitize_hex_color( $color );
	return $color ? $color : '';
}

function tqs_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === $checked ) ? true : false;
}

function tqs_sanitize_url_or_path( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) && '/' !== $url ) {
		return sanitize_text_field( $url );
	}
	return esc_url_raw( $url );
}

/**
 * Resolve theme mod URL (absolute or site-relative).
 */
function tqs_theme_mod_url( $key, $default = '' ) {
	$url = get_theme_mod( $key, $default );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}
	return $url;
}

function tqs_get_stats() {
	$stats = array();
	for ( $i = 0; $i < 4; $i++ ) {
		$stats[] = array(
			'value' => get_theme_mod( "tqs_stat_{$i}_value", '' ),
			'label' => get_theme_mod( "tqs_stat_{$i}_label", '' ),
		);
	}
	return $stats;
}

/**
 * Active homepage hero slides from Customizer.
 */
function tqs_get_hero_slides() {
	$defaults = tqs_get_default_hero_slides();
	$slides   = array();

	for ( $i = 0; $i < 3; $i++ ) {
		$default = $defaults[ $i ];
		if ( ! get_theme_mod( "tqs_slide_{$i}_enabled", $default['enabled'] ) ) {
			continue;
		}

		$image_id  = absint( get_theme_mod( "tqs_slide_{$i}_image", 0 ) );
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'tqs-hero' ) : '';

		$slides[] = array(
			'gradient'  => get_theme_mod( "tqs_slide_{$i}_gradient", $default['gradient'] ),
			'image_url' => $image_url,
			'icon'      => get_theme_mod( "tqs_slide_{$i}_icon", $default['icon'] ),
			'badge'     => get_theme_mod( "tqs_slide_{$i}_badge", $default['badge'] ),
			'title'     => get_theme_mod( "tqs_slide_{$i}_title", $default['title'] ),
			'highlight' => get_theme_mod( "tqs_slide_{$i}_highlight", $default['highlight'] ),
			'subtitle'  => get_theme_mod( "tqs_slide_{$i}_subtitle", $default['subtitle'] ),
		);
	}

	return ! empty( $slides ) ? $slides : $defaults;
}

/**
 * "Why Us" section cards from Customizer.
 */
function tqs_get_why_us_cards() {
	$defaults = tqs_get_default_why_us_cards();
	$cards    = array();

	for ( $i = 0; $i < 4; $i++ ) {
		$default = $defaults[ $i ];
		$cards[] = array(
			'icon'  => get_theme_mod( "tqs_why_{$i}_icon", $default['icon'] ),
			'title' => get_theme_mod( "tqs_why_{$i}_title", $default['title'] ),
			'desc'  => get_theme_mod( "tqs_why_{$i}_desc", $default['desc'] ),
		);
	}

	return $cards;
}

function tqs_show_breadcrumbs() {
	return (bool) get_theme_mod( 'tqs_show_breadcrumbs', true );
}

function tqs_show_home_section( $section ) {
	$key = 'tqs_show_' . $section;
	return (bool) get_theme_mod( $key, true );
}

/**
 * Output brand color overrides as CSS custom properties.
 */
function tqs_output_brand_css() {
	$map = array(
		'tqs_color_primary'    => '--tqs-primary',
		'tqs_color_secondary'  => '--tqs-secondary',
		'tqs_color_gold'       => '--tqs-gold',
		'tqs_color_gold_light' => '--tqs-gold-light',
		'tqs_color_dark'       => '--tqs-dark',
		'tqs_color_light'      => '--tqs-light',
		'tqs_color_text'       => '--tqs-text',
		'tqs_color_muted'      => '--tqs-muted',
	);

	$defaults = tqs_get_brand_color_defaults();
	$rules    = array();

	foreach ( $map as $mod_key => $css_var ) {
		$value = get_theme_mod( $mod_key, $defaults[ $mod_key ] );
		if ( $value && $value !== $defaults[ $mod_key ] ) {
			$rules[] = sprintf( '%s: %s', $css_var, $value );
		}
	}

	if ( empty( $rules ) ) {
		return;
	}

	echo '<style id="tqs-brand-colors">:root{' . esc_html( implode( ';', $rules ) ) . ';}</style>' . "\n";
}
add_action( 'wp_head', 'tqs_output_brand_css', 5 );

/**
 * Google Analytics / GTM snippet.
 */
function tqs_output_analytics() {
	$ga_id  = trim( (string) get_theme_mod( 'tqs_ga_id', '' ) );
	$gtm_id = trim( (string) get_theme_mod( 'tqs_gtm_id', '' ) );

	if ( $gtm_id ) {
		$gtm_id = preg_replace( '/[^A-Za-z0-9\-]/', '', $gtm_id );
		if ( $gtm_id ) {
			echo "\n<!-- TQS GTM -->\n";
			echo "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . esc_js( $gtm_id ) . "');</script>\n";
		}
	}

	if ( $ga_id ) {
		$ga_id = preg_replace( '/[^A-Za-z0-9\-]/', '', $ga_id );
		if ( $ga_id ) {
			echo "\n<!-- TQS GA4 -->\n";
			echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga_id ) . '"></script>' . "\n";
			echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga_id ) . "');</script>\n";
		}
	}
}
add_action( 'wp_head', 'tqs_output_analytics', 20 );

function tqs_output_gtm_noscript() {
	$gtm_id = trim( (string) get_theme_mod( 'tqs_gtm_id', '' ) );
	$gtm_id = preg_replace( '/[^A-Za-z0-9\-]/', '', $gtm_id );
	if ( ! $gtm_id ) {
		return;
	}
	echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n";
}
add_action( 'wp_body_open', 'tqs_output_gtm_noscript', 1 );

/**
 * Built-in theme logo assets (fallback when no Customizer upload exists).
 */
function tqs_get_theme_logo_asset_url( $filename ) {
	return get_template_directory_uri() . '/assets/images/' . ltrim( $filename, '/' );
}

function tqs_theme_logo_asset_exists( $filename ) {
	return file_exists( get_template_directory() . '/assets/images/' . ltrim( $filename, '/' ) );
}

/**
 * Attachment URL from a theme mod (full size — preserves logo proportions).
 */
function tqs_get_logo_url_from_mod( $mod_key ) {
	$image_id = absint( get_theme_mod( $mod_key, 0 ) );
	if ( ! $image_id ) {
		return '';
	}
	$url = wp_get_attachment_image_url( $image_id, 'full' );
	return $url ? $url : '';
}

/**
 * Header logo: TQS setting → WordPress Site Identity logo → bundled top-logo.png.
 */
function tqs_get_header_logo_url() {
	$url = tqs_get_logo_url_from_mod( 'tqs_header_logo' );
	if ( $url ) {
		return $url;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$url = wp_get_attachment_image_url( absint( $custom_logo_id ), 'full' );
		if ( $url ) {
			return $url;
		}
	}

	if ( tqs_theme_logo_asset_exists( 'top-logo.png' ) ) {
		return tqs_get_theme_logo_asset_url( 'top-logo.png' );
	}

	return '';
}

/**
 * Footer logo: footer override → header logo chain.
 */
function tqs_get_footer_logo_url() {
	$url = tqs_get_logo_url_from_mod( 'tqs_footer_logo' );
	if ( $url ) {
		return $url;
	}

	return tqs_get_header_logo_url();
}

/**
 * Identity / SEO logo (search engines, JSON-LD): identity setting → Site Identity → bundled asset.
 */
function tqs_get_identity_logo_url() {
	$url = tqs_get_logo_url_from_mod( 'tqs_identity_logo' );
	if ( $url ) {
		return $url;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$url = wp_get_attachment_image_url( absint( $custom_logo_id ), 'full' );
		if ( $url ) {
			return $url;
		}
	}

	if ( tqs_theme_logo_asset_exists( 'top-Identity-logo.png' ) ) {
		return tqs_get_theme_logo_asset_url( 'top-Identity-logo.png' );
	}

	return tqs_get_header_logo_url();
}

/**
 * Render header or footer logo markup (plain <img>, no nested links).
 */
function tqs_render_site_logo( $context = 'header' ) {
	$url = 'footer' === $context ? tqs_get_footer_logo_url() : tqs_get_header_logo_url();
	$alt = get_bloginfo( 'name', 'display' );

	if ( ! $url ) {
		if ( 'footer' === $context ) {
			echo '<div class="tqs-footer-brand-logo tqs-footer-brand-logo--fallback">TQS</div>';
		} else {
			echo '<div class="tqs-brand-logo tqs-brand-logo--fallback">TQS</div>';
		}
		return;
	}

	$wrapper_class = 'footer' === $context
		? 'tqs-footer-brand-logo tqs-footer-brand-logo--has-image'
		: 'tqs-brand-logo tqs-brand-logo--has-image';

	printf(
		'<div class="%1$s"><img src="%2$s" alt="%3$s" class="tqs-site-logo-img" decoding="async" loading="eager"></div>',
		esc_attr( $wrapper_class ),
		esc_url( $url ),
		esc_attr( $alt )
	);
}

/**
 * Keep header logo and WordPress Site Identity logo in sync.
 */
function tqs_sync_custom_logo_from_header_setting() {
	$header_logo_id = absint( get_theme_mod( 'tqs_header_logo', 0 ) );
	$custom_logo_id = absint( get_theme_mod( 'custom_logo', 0 ) );

	if ( $header_logo_id && $header_logo_id !== $custom_logo_id ) {
		set_theme_mod( 'custom_logo', $header_logo_id );
	} elseif ( ! $header_logo_id && $custom_logo_id ) {
		set_theme_mod( 'tqs_header_logo', $custom_logo_id );
	}
}
add_action( 'customize_save_after', 'tqs_sync_custom_logo_from_header_setting' );

/**
 * Default OG image URL from Customizer.
 */
function tqs_get_default_og_image_url() {
	$image_id = absint( get_theme_mod( 'tqs_og_default_image', 0 ) );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $url ) {
			return $url;
		}
	}

	$identity_logo = tqs_get_identity_logo_url();
	if ( $identity_logo ) {
		return $identity_logo;
	}

	return '';
}

/**
 * Customizer selective refresh for logo areas.
 */
function tqs_customize_selective_refresh( $wp_customize ) {
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial(
		'tqs_header_logo',
		array(
			'selector'            => '.tqs-brand-logo',
			'container_inclusive' => true,
			'settings'            => array( 'tqs_header_logo', 'custom_logo' ),
			'render_callback'     => function () {
				tqs_render_site_logo( 'header' );
			},
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'tqs_footer_logo',
		array(
			'selector'            => '.tqs-footer-brand-logo',
			'container_inclusive' => true,
			'settings'            => array( 'tqs_footer_logo', 'tqs_header_logo', 'custom_logo' ),
			'render_callback'     => function () {
				tqs_render_site_logo( 'footer' );
			},
		)
	);
}
add_action( 'customize_register', 'tqs_customize_selective_refresh', 20 );
