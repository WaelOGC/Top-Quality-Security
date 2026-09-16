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
 * Default "Why Us" cards.
 */
function tqs_get_default_why_us_cards() {
	return array(
		array( 'icon' => 'fa-shield-halved', 'title' => 'ND 7099 Gecertificeerd', 'desc' => 'Voldoen aan de hoogste kwaliteitsnorm in de beveiligingsbranche, jaarlijks getoetst en gecontroleerd door een erkende certificerende instelling.' ),
		array( 'icon' => 'fa-graduation-cap', 'title' => 'Ervaren Personeel', 'desc' => 'Goed opgeleide, representatieve beveiligers met jarenlange ervaring in uiteenlopende sectoren, van retail tot evenementen.' ),
		array( 'icon' => 'fa-clock', 'title' => '24/7 Beschikbaar', 'desc' => 'Altijd bereikbaar, ook buiten kantoortijden, in het weekend en tijdens feestdagen — voor spoedsituaties en vaste inzet.' ),
		array( 'icon' => 'fa-gear', 'title' => 'Maatwerk Oplossingen', 'desc' => 'Beveiligingsplannen volledig afgestemd op de specifieke situatie, risico\'s en wensen van uw organisatie of locatie.' ),
	);
}

/**
 * One-time: upgrade Why-Us card descriptions that still match the old short defaults.
 * Does not overwrite admin-edited Customizer values.
 */
function tqs_migrate_why_us_card_copy_v292() {
	if ( get_option( 'tqs_why_us_copy_migrated_v292' ) ) {
		return;
	}

	$old = array(
		0 => 'Voldoen aan de hoogste kwaliteitsnorm in de beveiligingsbranche.',
		1 => 'Goed opgeleide, representatieve beveiligers met jarenlange ervaring.',
		2 => 'Altijd bereikbaar, ook buiten kantoortijden en in het weekend.',
		3 => 'Beveiligingsplannen afgestemd op de specifieke situatie van uw organisatie.',
	);
	$new = tqs_get_default_why_us_cards();

	for ( $i = 0; $i < 4; $i++ ) {
		$key     = "tqs_why_{$i}_desc";
		$current = get_theme_mod( $key, null );
		if ( null === $current || false === $current || '' === $current ) {
			continue;
		}
		if ( (string) $current === $old[ $i ] ) {
			set_theme_mod( $key, $new[ $i ]['desc'] );
		}
	}

	update_option( 'tqs_why_us_copy_migrated_v292', 1, false );
}
add_action( 'init', 'tqs_migrate_why_us_card_copy_v292', 25 );

function tqs_sanitize_hex_color( $color ) {
	$color = sanitize_hex_color( $color );
	return $color ? $color : '';
}

function tqs_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === $checked ) ? true : false;
}

/**
 * Gallery grid column count (Customizer).
 *
 * @param mixed $value Raw value.
 * @return int 3 or 4.
 */
function tqs_sanitize_gallery_columns( $value ) {
	$value = absint( $value );
	return in_array( $value, array( 3, 4 ), true ) ? $value : 4;
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
