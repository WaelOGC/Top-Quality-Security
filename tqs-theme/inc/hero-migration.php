<?php
/**
 * One-time hero meta migration (v2.4.0).
 *
 * Moves legacy `_tqs_hide_hero` / `_tqs_hero_title_override` onto the unified
 * Hero Settings keys, seeds page-hero defaults, and applies Wie Zijn Wij content.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure a theme asset file exists in the Media Library; return attachment ID.
 *
 * @param string $relative_path Path relative to the theme root (e.g. assets/images/foo.jpg).
 * @return int Attachment ID or 0.
 */
function tqs_ensure_theme_image_attachment( $relative_path ) {
	$relative_path = ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );
	if ( '' === $relative_path ) {
		return 0;
	}

	$option_key = 'tqs_theme_img_id_' . md5( $relative_path );
	$cached     = absint( get_option( $option_key, 0 ) );
	if ( $cached && wp_attachment_is_image( $cached ) ) {
		return $cached;
	}

	$source = get_template_directory() . '/' . $relative_path;
	if ( ! file_exists( $source ) || ! is_readable( $source ) ) {
		return 0;
	}

	if ( ! function_exists( 'wp_upload_bits' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
	}

	$filename = basename( $source );
	$bits     = wp_upload_bits( $filename, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! empty( $bits['error'] ) || empty( $bits['file'] ) ) {
		return 0;
	}

	$filetype   = wp_check_filetype( $filename, null );
	$attachment = array(
		'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/jpeg',
		'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $bits['file'] );
	if ( ! $attach_id || is_wp_error( $attach_id ) ) {
		return 0;
	}

	$meta = wp_generate_attachment_metadata( $attach_id, $bits['file'] );
	if ( $meta ) {
		wp_update_attachment_metadata( $attach_id, $meta );
	}

	update_option( $option_key, $attach_id, false );
	return (int) $attach_id;
}

/**
 * Default page-hero subtitle for known templates (content-preserving).
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function tqs_migration_default_page_subtitle( $post ) {
	$template = get_page_template_slug( $post );
	if ( 'page-contact.php' === $template ) {
		return (string) get_theme_mod(
			'tqs_contact_hero_subtitle',
			'Vragen over onze diensten of een offerte op maat? Neem contact op — wij reageren binnen één werkdag.'
		);
	}
	if ( 'page-fotogalerij.php' === $template ) {
		return (string) get_theme_mod(
			'tqs_gallery_hero_subtitle',
			'Een impressie van ons werk in de praktijk.'
		);
	}
	if ( 'wie-zijn-wij' === $post->post_name ) {
		return 'Maak kennis met TQS — een gecertificeerde beveiligingspartner met een persoonlijke aanpak.';
	}
	if ( 'tqs_service' === $post->post_type ) {
		return (string) $post->post_excerpt;
	}
	return '';
}

/**
 * Build initial `_tqs_page_hero` from legacy title override / post title.
 *
 * @param WP_Post $post Post object.
 * @return array<string, mixed>
 */
function tqs_migration_build_page_hero( $post ) {
	$override = get_post_meta( $post->ID, '_tqs_hero_title_override', true );
	$title    = is_string( $override ) && '' !== trim( $override ) ? trim( $override ) : $post->post_title;

	$legacy_image = absint( get_post_meta( $post->ID, '_tqs_hero_image_id', true ) );
	if ( ! $legacy_image && has_post_thumbnail( $post ) ) {
		/* Featured image stays for content media on services; only use as hero if page had explicit hero id. */
		$legacy_image = 0;
	}

	return array(
		'image_id' => $legacy_image,
		'title'    => $title,
		'subtitle' => tqs_migration_default_page_subtitle( $post ),
		'btn_text' => '',
		'btn_url'  => '',
	);
}

/**
 * Migrate a single post onto the unified hero meta keys.
 *
 * @param WP_Post $post Post object.
 */
function tqs_migrate_post_hero_meta( $post ) {
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$post_id = (int) $post->ID;

	/* C: hide_hero → hero_enabled (only when new key never saved). */
	if ( metadata_exists( 'post', $post_id, '_tqs_hide_hero' ) && ! metadata_exists( 'post', $post_id, '_tqs_hero_enabled' ) ) {
		$hidden = '1' === (string) get_post_meta( $post_id, '_tqs_hide_hero', true );
		update_post_meta( $post_id, '_tqs_hero_enabled', $hidden ? '' : '1' );
	}

	$front_id = (int) get_option( 'page_on_front' );
	$is_front = $front_id && $post_id === $front_id;

	if ( ! metadata_exists( 'post', $post_id, '_tqs_hero_type' ) ) {
		update_post_meta( $post_id, '_tqs_hero_type', $is_front ? 'homepage_hero' : 'page_hero' );
	}

	if ( $is_front ) {
		return;
	}

	$stored = get_post_meta( $post_id, '_tqs_page_hero', true );
	if ( empty( $stored ) || ! is_array( $stored ) ) {
		$hero = tqs_migration_build_page_hero( $post );
		if ( function_exists( 'tqs_hero_sanitize_page_hero' ) ) {
			$hero = tqs_hero_sanitize_page_hero( $hero );
		}
		update_post_meta( $post_id, '_tqs_page_hero', $hero );
	} else {
		/* Fill empty title/subtitle from live defaults so partial meta-box saves don't blank content. */
		$hero    = function_exists( 'tqs_hero_sanitize_page_hero' ) ? tqs_hero_sanitize_page_hero( $stored ) : $stored;
		$changed = false;
		if ( '' === trim( (string) $hero['title'] ) ) {
			$override = get_post_meta( $post_id, '_tqs_hero_title_override', true );
			$hero['title'] = ( is_string( $override ) && '' !== trim( $override ) ) ? trim( $override ) : $post->post_title;
			$changed       = true;
		}
		if ( '' === trim( (string) $hero['subtitle'] ) ) {
			$fallback = tqs_migration_default_page_subtitle( $post );
			if ( '' !== $fallback ) {
				$hero['subtitle'] = $fallback;
				$changed          = true;
			}
		}
		if ( empty( $hero['image_id'] ) ) {
			$legacy_image = absint( get_post_meta( $post_id, '_tqs_hero_image_id', true ) );
			if ( $legacy_image ) {
				$hero['image_id'] = $legacy_image;
				$changed          = true;
			}
		}
		if ( $changed ) {
			update_post_meta( $post_id, '_tqs_page_hero', $hero );
		}
	}

	if ( ! metadata_exists( 'post', $post_id, '_tqs_hero_enabled' ) ) {
		update_post_meta( $post_id, '_tqs_hero_enabled', '1' );
	}
}

/**
 * Apply final Wie Zijn Wij hero + about content (migration only; not slug-gated at runtime).
 */
function tqs_migrate_wie_zijn_wij_content() {
	$page = get_page_by_path( 'wie-zijn-wij' );
	if ( ! $page ) {
		return;
	}

	$hero_image_id  = tqs_ensure_theme_image_attachment( 'assets/images/tqs-hero-beveiligingsteam-dakterras-nacht.jpg' );
	$story_image_id = tqs_ensure_theme_image_attachment( 'assets/images/tqs-beveiligingsteam-lobby-avond.jpg' );

	update_post_meta( $page->ID, '_tqs_hero_type', 'page_hero' );
	update_post_meta( $page->ID, '_tqs_hero_enabled', '1' );
	update_post_meta(
		$page->ID,
		'_tqs_page_hero',
		function_exists( 'tqs_hero_sanitize_page_hero' )
			? tqs_hero_sanitize_page_hero(
				array(
					'image_id' => $hero_image_id,
					'title'    => 'Wie Zijn Wij',
					'subtitle' => 'Maak kennis met TQS — een gecertificeerde beveiligingspartner met een persoonlijke aanpak.',
					'btn_text' => '',
					'btn_url'  => '',
				)
			)
			: array(
				'image_id' => $hero_image_id,
				'title'    => 'Wie Zijn Wij',
				'subtitle' => 'Maak kennis met TQS — een gecertificeerde beveiligingspartner met een persoonlijke aanpak.',
				'btn_text' => '',
				'btn_url'  => '',
			)
	);

	if ( function_exists( 'tqs_about_default_values' ) ) {
		update_post_meta( $page->ID, '_tqs_about_values', tqs_about_default_values() );
		update_post_meta( $page->ID, '_tqs_about_guarantees', tqs_about_default_guarantees() );
	}
	update_post_meta( $page->ID, '_tqs_story_image_id', $story_image_id );
	update_post_meta( $page->ID, '_tqs_about_layout', '1' );
}

/**
 * Run v2.4.0 hero unification migration once.
 */
function tqs_run_hero_meta_migration_v240() {
	if ( get_option( 'tqs_hero_meta_migrated_v240' ) ) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => array( 'page', 'tqs_service' ),
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	foreach ( $posts as $post ) {
		tqs_migrate_post_hero_meta( $post );
	}

	tqs_migrate_wie_zijn_wij_content();

	update_option( 'tqs_hero_meta_migrated_v240', 1, false );
}
add_action( 'init', 'tqs_run_hero_meta_migration_v240', 20 );
