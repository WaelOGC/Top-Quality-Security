<?php
/**
 * Homepage cinematic scroll (Lenis + GSAP ScrollTrigger).
 *
 * Loaded only on the live front page — never in Elementor editor / preview.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Elementor editor or preview iframe is active.
 *
 * @return bool
 */
function tqs_is_elementor_active_editor() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	$plugin = \Elementor\Plugin::$instance;

	if ( isset( $plugin->editor ) && method_exists( $plugin->editor, 'is_edit_mode' ) && $plugin->editor->is_edit_mode() ) {
		return true;
	}

	if ( isset( $plugin->preview ) && method_exists( $plugin->preview, 'is_preview_mode' ) && $plugin->preview->is_preview_mode() ) {
		return true;
	}

	return false;
}

/**
 * Whether cinematic scroll assets should load.
 *
 * @return bool
 */
function tqs_should_load_cinematic_scroll() {
	if ( is_admin() ) {
		return false;
	}

	if ( ! is_front_page() ) {
		return false;
	}

	if ( tqs_is_elementor_active_editor() ) {
		return false;
	}

	return true;
}

/**
 * Enqueue Lenis, GSAP, ScrollTrigger, and theme cinematic assets.
 */
function tqs_enqueue_cinematic_scroll() {
	if ( ! tqs_should_load_cinematic_scroll() ) {
		return;
	}

	$ver = tqs_theme_version();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'lenis',
		'https://unpkg.com/lenis@1.3.26/dist/lenis.css',
		array(),
		'1.3.26'
	);

	wp_enqueue_style(
		'tqs-cinematic-scroll',
		$uri . '/assets/css/tqs-cinematic-scroll.css',
		array( 'lenis', 'tqs-theme-style' ),
		$ver
	);

	wp_enqueue_style(
		'tqs-hero-slider',
		$uri . '/assets/css/tqs-hero-slider.css',
		array( 'tqs-theme-style', 'tqs-cinematic-scroll' ),
		$ver
	);

	wp_enqueue_script(
		'lenis',
		'https://unpkg.com/lenis@1.3.26/dist/lenis.min.js',
		array(),
		'1.3.26',
		true
	);

	wp_enqueue_script(
		'gsap',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
		array(),
		'3.12.5',
		true
	);

	wp_enqueue_script(
		'gsap-scrolltrigger',
		'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
		array( 'gsap' ),
		'3.12.5',
		true
	);

	wp_enqueue_script(
		'tqs-cinematic-scroll',
		$uri . '/assets/js/tqs-cinematic-scroll.js',
		array( 'lenis', 'gsap', 'gsap-scrolltrigger' ),
		$ver,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tqs_enqueue_cinematic_scroll', 20 );

/**
 * Body class so CSS reveal base states only apply when assets are present.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function tqs_cinematic_body_class( $classes ) {
	if ( tqs_should_load_cinematic_scroll() ) {
		$classes[] = 'tqs-cinematic';
	}
	return $classes;
}
add_filter( 'body_class', 'tqs_cinematic_body_class' );

/**
 * No-JS fallback: keep reveal targets visible if scripts never run.
 */
function tqs_cinematic_noscript_fallback() {
	if ( ! tqs_should_load_cinematic_scroll() ) {
		return;
	}
	echo '<noscript><style>.tqs-reveal,.tqs-reveal-left,.tqs-reveal-right{opacity:1!important;transform:none!important}</style></noscript>' . "\n";
}
add_action( 'wp_head', 'tqs_cinematic_noscript_fallback', 5 );
