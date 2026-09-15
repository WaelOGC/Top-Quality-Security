<?php
/**
 * Service content image effects — CSS/JS on tqs_service singles only.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed animation effect values for the service content image.
 *
 * @return array<string, string>
 */
function tqs_service_content_image_effect_options() {
	return array(
		'none'               => __( 'None', 'tqs-theme' ),
		'dual-layer-reveal'  => __( 'Dual-Layer Color Reveal', 'tqs-theme' ),
		'slice-reveal'       => __( 'Slice Reveal (Load-In)', 'tqs-theme' ),
		'tilt-3d'            => __( '3D Tilt', 'tqs-theme' ),
		'spotlight-follow'   => __( 'Spotlight Follow', 'tqs-theme' ),
		'wave-distort'       => __( 'Wave Distortion', 'tqs-theme' ),
		'particle-sparkle'   => __( 'Particle Sparkle', 'tqs-theme' ),
		'glass-shatter'      => __( 'Glass Shatter', 'tqs-theme' ),
		'security-scanline'  => __( 'Security Scanline', 'tqs-theme' ),
		'clarity-focus'      => __( 'Clarity Focus', 'tqs-theme' ),
	);
}

/**
 * Sanitize content-image effect select value.
 *
 * @param string $value Raw value.
 * @return string
 */
function tqs_sanitize_service_content_image_effect( $value ) {
	$value   = sanitize_text_field( (string) $value );
	$allowed = array_keys( tqs_service_content_image_effect_options() );
	return in_array( $value, $allowed, true ) ? $value : 'none';
}

/**
 * Curated Font Awesome icon options for the content-image badge.
 *
 * @return array<string, string>
 */
function tqs_service_content_image_badge_icon_options() {
	return array(
		''                     => __( 'None', 'tqs-theme' ),
		'fa-shield-halved'     => __( 'Shield', 'tqs-theme' ),
		'fa-user-shield'       => __( 'Guard', 'tqs-theme' ),
		'fa-store'             => __( 'Retail / Store', 'tqs-theme' ),
		'fa-building'          => __( 'Building', 'tqs-theme' ),
		'fa-hotel'             => __( 'Hotel', 'tqs-theme' ),
		'fa-champagne-glasses' => __( 'Casino / Event', 'tqs-theme' ),
		'fa-cart-shopping'     => __( 'Supermarket', 'tqs-theme' ),
		'fa-key'               => __( 'Key / Access', 'tqs-theme' ),
		'fa-lock'              => __( 'Lock / Security', 'tqs-theme' ),
		'fa-eye'               => __( 'Surveillance', 'tqs-theme' ),
		'fa-star'              => __( 'Star / Featured', 'tqs-theme' ),
	);
}

/**
 * Sanitize badge icon select value.
 *
 * @param string $value Raw value.
 * @return string
 */
function tqs_sanitize_service_content_image_badge_icon( $value ) {
	$value   = sanitize_text_field( (string) $value );
	$allowed = array_keys( tqs_service_content_image_badge_icon_options() );
	return in_array( $value, $allowed, true ) ? $value : '';
}

/**
 * Enqueue service image FX assets on service singles only.
 */
function tqs_enqueue_service_image_fx() {
	if ( ! is_singular( 'tqs_service' ) ) {
		return;
	}

	$ver = tqs_theme_version();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'tqs-service-image-fx',
		$uri . '/assets/css/tqs-service-image-fx.css',
		array( 'tqs-theme-style' ),
		$ver
	);

	wp_enqueue_script(
		'tqs-service-image-fx',
		$uri . '/assets/js/tqs-service-image-fx.js',
		array(),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tqs_enqueue_service_image_fx' );
