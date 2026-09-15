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
 * @return string[]
 */
function tqs_service_content_image_effect_options() {
	return array(
		'none'          => __( 'Geen', 'tqs-theme' ),
		'zoom-hover'    => __( 'Zoom bij hover', 'tqs-theme' ),
		'fade-scroll'   => __( 'Inlazen bij scrollen', 'tqs-theme' ),
		'slide-in'      => __( 'Inschuiven bij scrollen', 'tqs-theme' ),
		'ken-burns'     => __( 'Langzame continue zoom', 'tqs-theme' ),
		'gold-shimmer'  => __( 'Gouden lichtglans', 'tqs-theme' ),
		'border-reveal' => __( 'Gouden rand onthulling', 'tqs-theme' ),
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
