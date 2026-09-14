<?php
/**
 * About page content meta box — kernwaarden, guarantees, story image.
 *
 * Post meta:
 *   _tqs_about_layout       string  '1' when about layout should render
 *   _tqs_about_values       array   { icon, title, desc }[]
 *   _tqs_about_guarantees   array   { icon, title, desc }[]
 *   _tqs_story_image_id     int     attachment ID for Ons Verhaal media
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default Onze Kernwaarden items.
 *
 * @return array<int, array{icon: string, title: string, desc: string}>
 */
function tqs_about_default_values() {
	return array(
		array(
			'icon'  => 'fa-handshake',
			'title' => 'Betrouwbaarheid',
			'desc'  => 'Wij komen afspraken na en zijn er wanneer het erop aankomt.',
		),
		array(
			'icon'  => 'fa-user-graduate',
			'title' => 'Professionaliteit',
			'desc'  => 'Opgeleid, gecertificeerd en representatief in elke situatie.',
		),
		array(
			'icon'  => 'fa-eye',
			'title' => 'Alertheid',
			'desc'  => 'Scherp opmerkzaam, zonder de sfeer te verstoren.',
		),
		array(
			'icon'  => 'fa-comments',
			'title' => 'Klantgerichtheid',
			'desc'  => 'Maatwerk oplossingen die aansluiten bij uw organisatie.',
		),
	);
}

/**
 * Default guarantees bar items.
 *
 * @return array<int, array{icon: string, title: string, desc: string}>
 */
function tqs_about_default_guarantees() {
	return array(
		array(
			'icon'  => 'fa-user-shield',
			'title' => 'Gescreend Personeel',
			'desc'  => 'Alle beveiligers doorlopen een strenge screening.',
		),
		array(
			'icon'  => 'fa-file-shield',
			'title' => 'ND 7099 Gecertificeerd',
			'desc'  => 'Voldoen aan de hoogste kwaliteitsnorm in de branche.',
		),
		array(
			'icon'  => 'fa-clock',
			'title' => '24/7 Bereikbaarheid',
			'desc'  => 'Altijd te bereiken, ook buiten kantoortijden.',
		),
		array(
			'icon'  => 'fa-gears',
			'title' => 'Maatwerk Aanpak',
			'desc'  => 'Elke opdracht krijgt een plan op maat.',
		),
	);
}

/**
 * Sanitize a list of icon/title/desc items.
 *
 * @param mixed $raw Raw value.
 * @return array<int, array{icon: string, title: string, desc: string}>
 */
function tqs_about_sanitize_items( $raw ) {
	$out = array();
	if ( ! is_array( $raw ) ) {
		return $out;
	}

	foreach ( $raw as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$icon  = isset( $row['icon'] ) ? sanitize_text_field( (string) $row['icon'] ) : '';
		$title = isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '';
		$desc  = isset( $row['desc'] ) ? sanitize_textarea_field( (string) $row['desc'] ) : '';
		if ( '' === $icon && '' === $title && '' === $desc ) {
			continue;
		}
		$out[] = array(
			'icon'  => $icon,
			'title' => $title,
			'desc'  => $desc,
		);
	}

	return $out;
}

/**
 * Stored kernwaarden (empty array if never saved).
 *
 * @param int $post_id Post ID.
 * @return array<int, array{icon: string, title: string, desc: string}>
 */
function tqs_get_about_values( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! metadata_exists( 'post', $post_id, '_tqs_about_values' ) ) {
		return array();
	}
	$stored = get_post_meta( $post_id, '_tqs_about_values', true );
	return tqs_about_sanitize_items( $stored );
}

/**
 * Stored guarantee items.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{icon: string, title: string, desc: string}>
 */
function tqs_get_about_guarantees( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! metadata_exists( 'post', $post_id, '_tqs_about_guarantees' ) ) {
		return array();
	}
	$stored = get_post_meta( $post_id, '_tqs_about_guarantees', true );
	return tqs_about_sanitize_items( $stored );
}

/**
 * Whether this page should use the about layout.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function tqs_is_about_layout_page( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}
	if ( metadata_exists( 'post', $post_id, '_tqs_about_layout' ) ) {
		return '1' === (string) get_post_meta( $post_id, '_tqs_about_layout', true );
	}
	return metadata_exists( 'post', $post_id, '_tqs_about_values' );
}

/**
 * Story image URL for Ons Verhaal media.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string
 */
function tqs_get_story_image_url( $post_id, $size = 'large' ) {
	$post_id   = absint( $post_id );
	$image_id  = absint( get_post_meta( $post_id, '_tqs_story_image_id', true ) );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, $size );
		if ( $url ) {
			return $url;
		}
	}
	return '';
}

/**
 * Register about content meta box on pages.
 */
function tqs_register_about_meta_box() {
	add_meta_box(
		'tqs_about_content_box',
		__( 'About Page Content', 'tqs-theme' ),
		'tqs_render_about_meta_box',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'tqs_register_about_meta_box' );

/**
 * Enqueue media assets when editing pages (shared with hero box).
 *
 * @param string $hook Admin hook.
 */
function tqs_enqueue_about_meta_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	if ( ! wp_style_is( 'tqs-admin-hero-metabox', 'enqueued' ) ) {
		wp_enqueue_style(
			'tqs-admin-hero-metabox',
			get_template_directory_uri() . '/assets/css/admin-hero-metabox.css',
			array(),
			tqs_theme_version()
		);
	}
	if ( ! wp_script_is( 'tqs-admin-hero-metabox', 'enqueued' ) ) {
		wp_enqueue_script(
			'tqs-admin-hero-metabox',
			get_template_directory_uri() . '/assets/js/admin-hero-metabox.js',
			array( 'jquery' ),
			tqs_theme_version(),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'tqs_enqueue_about_meta_assets' );

/**
 * Render one repeatable item row.
 *
 * @param string               $name_prefix Field name prefix.
 * @param int                  $index       Row index.
 * @param array<string,string> $item        Item data.
 */
function tqs_about_render_item_row( $name_prefix, $index, $item ) {
	$icon  = isset( $item['icon'] ) ? $item['icon'] : '';
	$title = isset( $item['title'] ) ? $item['title'] : '';
	$desc  = isset( $item['desc'] ) ? $item['desc'] : '';
	?>
	<div class="tqs-about-item" style="border:1px solid #dcdcde;padding:12px;margin-bottom:10px;background:#fff;">
		<p>
			<label><strong><?php esc_html_e( 'Icoon (Font Awesome class)', 'tqs-theme' ); ?></strong></label><br>
			<input type="text" class="widefat" name="<?php echo esc_attr( $name_prefix . '[' . $index . '][icon]' ); ?>" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-handshake">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Titel', 'tqs-theme' ); ?></strong></label><br>
			<input type="text" class="widefat" name="<?php echo esc_attr( $name_prefix . '[' . $index . '][title]' ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Beschrijving', 'tqs-theme' ); ?></strong></label><br>
			<textarea class="widefat" rows="2" name="<?php echo esc_attr( $name_prefix . '[' . $index . '][desc]' ); ?>"><?php echo esc_textarea( $desc ); ?></textarea>
		</p>
	</div>
	<?php
}

/**
 * Render the About Page Content meta box.
 *
 * @param WP_Post $post Current post.
 */
function tqs_render_about_meta_box( $post ) {
	wp_nonce_field( 'tqs_save_about_content', 'tqs_about_content_nonce' );

	$layout_on   = '1' === (string) get_post_meta( $post->ID, '_tqs_about_layout', true ) || metadata_exists( 'post', $post->ID, '_tqs_about_values' );
	$values      = metadata_exists( 'post', $post->ID, '_tqs_about_values' )
		? tqs_get_about_values( $post->ID )
		: tqs_about_default_values();
	$guarantees  = metadata_exists( 'post', $post->ID, '_tqs_about_guarantees' )
		? tqs_get_about_guarantees( $post->ID )
		: tqs_about_default_guarantees();
	$story_id    = absint( get_post_meta( $post->ID, '_tqs_story_image_id', true ) );

	if ( empty( $values ) ) {
		$values = tqs_about_default_values();
	}
	if ( empty( $guarantees ) ) {
		$guarantees = tqs_about_default_guarantees();
	}
	?>
	<div id="tqs-about-content" class="tqs-about-content tqs-hero-settings">
		<p>
			<label>
				<input type="checkbox" name="tqs_about_layout" value="1" <?php checked( $layout_on ); ?>>
				<strong><?php esc_html_e( 'Gebruik About-pagina layout (verhaal + kernwaarden + garanties)', 'tqs-theme' ); ?></strong>
			</label>
		</p>

		<h3><?php esc_html_e( 'Ons Verhaal — afbeelding', 'tqs-theme' ); ?></h3>
		<p class="description"><?php esc_html_e( 'Afbeelding links naast de verhaaltekst.', 'tqs-theme' ); ?></p>
		<?php
		if ( function_exists( 'tqs_hero_render_image_field' ) ) {
			tqs_hero_render_image_field(
				'tqs_story_image_id',
				'tqs_story_image_id',
				$story_id,
				__( 'Kies afbeelding', 'tqs-theme' )
			);
		}
		?>

		<h3><?php esc_html_e( 'Onze Kernwaarden', 'tqs-theme' ); ?></h3>
		<?php foreach ( $values as $i => $item ) : ?>
			<?php tqs_about_render_item_row( 'tqs_about_values', (int) $i, $item ); ?>
		<?php endforeach; ?>

		<h3><?php esc_html_e( 'Garanties-balk', 'tqs-theme' ); ?></h3>
		<?php foreach ( $guarantees as $i => $item ) : ?>
			<?php tqs_about_render_item_row( 'tqs_about_guarantees', (int) $i, $item ); ?>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Save about content meta.
 *
 * @param int $post_id Post ID.
 */
function tqs_save_about_meta_box( $post_id ) {
	if ( ! isset( $_POST['tqs_about_content_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tqs_about_content_nonce'] ) ), 'tqs_save_about_content' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$layout = isset( $_POST['tqs_about_layout'] ) ? '1' : '';
	update_post_meta( $post_id, '_tqs_about_layout', $layout );

	/*
	 * Avoid writing default kernwaarden/guarantees onto every page save.
	 * Only persist item arrays / story image when About layout is on, or when
	 * this page already had about content (so edits still save).
	 */
	$had_about = metadata_exists( 'post', $post_id, '_tqs_about_values' );
	if ( '1' !== $layout && ! $had_about ) {
		return;
	}

	$values_raw = isset( $_POST['tqs_about_values'] ) ? wp_unslash( $_POST['tqs_about_values'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	update_post_meta( $post_id, '_tqs_about_values', tqs_about_sanitize_items( is_array( $values_raw ) ? $values_raw : array() ) );

	$guarantees_raw = isset( $_POST['tqs_about_guarantees'] ) ? wp_unslash( $_POST['tqs_about_guarantees'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	update_post_meta( $post_id, '_tqs_about_guarantees', tqs_about_sanitize_items( is_array( $guarantees_raw ) ? $guarantees_raw : array() ) );

	$story_id = isset( $_POST['tqs_story_image_id'] ) ? absint( wp_unslash( $_POST['tqs_story_image_id'] ) ) : 0;
	update_post_meta( $post_id, '_tqs_story_image_id', $story_id );
}
add_action( 'save_post_page', 'tqs_save_about_meta_box' );
