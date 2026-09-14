<?php
/**
 * Front-end hero rendering from Hero Settings post meta.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a page uses an Elementor Canvas (or similar) full-layout template.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function tqs_is_elementor_canvas_page( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}

	$slug = get_page_template_slug( $post_id );
	if ( $slug && false !== strpos( $slug, 'elementor_canvas' ) ) {
		return true;
	}

	/* Elementor stores canvas as page template value `elementor_canvas`. */
	$template = get_post_meta( $post_id, '_wp_page_template', true );
	if ( is_string( $template ) && false !== strpos( $template, 'elementor_canvas' ) ) {
		return true;
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		if ( $document && method_exists( $document, 'get_template_type' ) ) {
			$type = $document->get_template_type();
			if ( in_array( $type, array( 'canvas', 'elementor_canvas' ), true ) ) {
				return true;
			}
		}
		if ( $document && method_exists( $document, 'is_built_with_elementor' ) && $document->is_built_with_elementor() ) {
			$page_settings = method_exists( $document, 'get_settings' ) ? $document->get_settings( 'template' ) : '';
			if ( 'elementor_canvas' === $page_settings ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Resolve a hero URL (path or absolute) for front-end output.
 *
 * @param string $url Sanitized URL or path.
 * @return string
 */
function tqs_hero_resolve_url( $url ) {
	$url = function_exists( 'tqs_hero_sanitize_url_or_path' )
		? tqs_hero_sanitize_url_or_path( $url )
		: (string) $url;

	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}
	return $url;
}

/**
 * Active homepage hero slides (enabled + non-blank), re-indexed from 0.
 *
 * @param int $post_id Post ID.
 * @return array{slides: array<int, array<string, mixed>>, used_fallback: bool}
 */
function tqs_get_active_homepage_slides( $post_id ) {
	$post_id = absint( $post_id );
	$all     = function_exists( 'tqs_get_stored_homepage_hero_slides' )
		? tqs_get_stored_homepage_hero_slides( $post_id )
		: array();

	$active = array();
	foreach ( $all as $slide ) {
		if ( empty( $slide['enabled'] ) ) {
			continue;
		}
		$has_image = ! empty( $slide['image_id'] );
		$has_title = isset( $slide['title'] ) && '' !== trim( (string) $slide['title'] );
		if ( ! $has_image && ! $has_title ) {
			continue;
		}
		$active[] = $slide;
	}

	$used_fallback = false;
	if ( empty( $active ) ) {
		$used_fallback = true;
		$fallback      = isset( $all[0] ) && is_array( $all[0] )
			? $all[0]
			: ( function_exists( 'tqs_hero_default_homepage_slides' ) ? tqs_hero_default_homepage_slides()[0] : array() );
		$active = array( $fallback );
	}

	return array(
		'slides'        => array_values( $active ),
		'used_fallback' => $used_fallback,
	);
}

/**
 * Resolve button labels/URLs for a homepage hero slide.
 *
 * @param array<string, mixed> $slide Slide data.
 * @return array{btn1_text: string, btn1_url: string, btn2_text: string, btn2_url: string}
 */
function tqs_get_hero_slide_buttons( $slide ) {
	$defaults = array(
		'btn1_text' => 'Offerte Aanvragen',
		'btn1_url'  => '/contact',
		'btn2_text' => 'Onze Diensten',
		'btn2_url'  => '/onze-diensten',
	);

	if ( ! empty( $slide['override_buttons'] ) ) {
		return array(
			'btn1_text' => isset( $slide['btn1_text'] ) ? (string) $slide['btn1_text'] : '',
			'btn1_url'  => isset( $slide['btn1_url'] ) ? (string) $slide['btn1_url'] : '',
			'btn2_text' => isset( $slide['btn2_text'] ) ? (string) $slide['btn2_text'] : '',
			'btn2_url'  => isset( $slide['btn2_url'] ) ? (string) $slide['btn2_url'] : '',
		);
	}

	return array(
		'btn1_text' => (string) get_theme_mod( 'tqs_hero_default_btn1_text', $defaults['btn1_text'] ),
		'btn1_url'  => (string) get_theme_mod( 'tqs_hero_default_btn1_url', $defaults['btn1_url'] ),
		'btn2_text' => (string) get_theme_mod( 'tqs_hero_default_btn2_text', $defaults['btn2_text'] ),
		'btn2_url'  => (string) get_theme_mod( 'tqs_hero_default_btn2_url', $defaults['btn2_url'] ),
	);
}

/**
 * Render the page hero from Hero Settings meta (homepage or page hero).
 *
 * When `_tqs_hero_type` is missing, defaults to page_hero so new pages work
 * before the editor has been saved.
 *
 * @param int|null $post_id Post ID.
 * @param array    $args    Optional. breadcrumbs trail override.
 */
function tqs_render_hero( $post_id = null, $args = array() ) {
	$post_id = $post_id ? absint( $post_id ) : (int) get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	if ( tqs_is_elementor_canvas_page( $post_id ) ) {
		return;
	}

	if ( metadata_exists( 'post', $post_id, '_tqs_hero_type' ) ) {
		$hero_type = function_exists( 'tqs_hero_sanitize_type' )
			? tqs_hero_sanitize_type( get_post_meta( $post_id, '_tqs_hero_type', true ) )
			: (string) get_post_meta( $post_id, '_tqs_hero_type', true );
	} else {
		$hero_type = 'page_hero';
	}

	if ( 'none' === $hero_type || '' === $hero_type ) {
		return;
	}

	$enabled = get_post_meta( $post_id, '_tqs_hero_enabled', true );
	if ( '' === $enabled || '0' === $enabled || false === $enabled ) {
		/* Explicitly hidden — distinct from missing meta (defaults to shown when type is set). */
		if ( metadata_exists( 'post', $post_id, '_tqs_hero_enabled' ) && '1' !== $enabled ) {
			return;
		}
	}

	if ( 'homepage_hero' === $hero_type ) {
		$result = tqs_get_active_homepage_slides( $post_id );
		get_template_part(
			'template-parts/hero/homepage',
			'hero',
			array(
				'slides'        => $result['slides'],
				'used_fallback' => $result['used_fallback'],
			)
		);
		return;
	}

	if ( 'page_hero' === $hero_type ) {
		$page_hero = function_exists( 'tqs_get_stored_page_hero' )
			? tqs_get_stored_page_hero( $post_id )
			: array();
		$tpl_args  = array(
			'hero' => $page_hero,
		);
		if ( ! empty( $args['breadcrumbs'] ) && is_array( $args['breadcrumbs'] ) ) {
			$tpl_args['breadcrumbs'] = $args['breadcrumbs'];
		}
		get_template_part( 'template-parts/hero/page', 'hero', $tpl_args );
	}
}

/**
 * Render a page-hero template from an explicit data array (archives, etc.).
 *
 * @param array<string, mixed> $hero         Hero field array.
 * @param array                $breadcrumbs  Optional breadcrumb trail.
 */
function tqs_render_page_hero_data( $hero, $breadcrumbs = array() ) {
	$args = array(
		'hero' => is_array( $hero ) ? $hero : array(),
	);
	if ( ! empty( $breadcrumbs ) ) {
		$args['breadcrumbs'] = $breadcrumbs;
	}
	get_template_part( 'template-parts/hero/page', 'hero', $args );
}

/**
 * Services archive hero (theme mods → unified page-hero markup).
 */
function tqs_render_services_archive_hero() {
	$title = (string) get_theme_mod( 'tqs_archive_services_title', 'Onze Diensten' );
	$lead  = (string) get_theme_mod(
		'tqs_archive_services_lead',
		'Van winkelvloer tot evenemententerrein — TQS levert beveiligingsoplossingen op maat voor iedere sector, met gecertificeerd en professioneel personeel.'
	);

	tqs_render_page_hero_data(
		array(
			'image_id' => 0,
			'title'    => $title,
			'subtitle' => $lead,
			'btn_text' => '',
			'btn_url'  => '',
		),
		array( array( 'label' => $title ) )
	);
}
