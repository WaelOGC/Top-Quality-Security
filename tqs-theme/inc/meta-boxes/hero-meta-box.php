<?php
/**
 * Hero Settings meta box — Pages (admin UI + storage only).
 *
 * Post meta:
 *   _tqs_hero_type              string  none|homepage_hero|page_hero
 *   _tqs_hero_enabled           string  '1'|'' (default enabled)
 *   _tqs_homepage_hero_slides   array   4 slides (serialized)
 *   _tqs_page_hero              array   single page hero (serialized)
 *
 * Theme mods (global default buttons — Customizer → Hero — Global Defaults):
 *   tqs_hero_default_btn1_text / tqs_hero_default_btn1_url
 *   tqs_hero_default_btn2_text / tqs_hero_default_btn2_url
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Curated emoji/icon options for homepage hero slides.
 *
 * @return array<string, string> emoji => label
 */
function tqs_hero_emoji_options() {
	return array(
		'🛡️' => '🛡️ Shield',
		'🔒' => '🔒 Lock',
		'📷' => '📷 Camera',
		'👮' => '👮 Guard',
		'✅' => '✅ Checkmark',
		'⭐' => '⭐ Star',
		'⚠️' => '⚠️ Warning',
		'🏢' => '🏢 Building',
		'🎖️' => '🎖️ Medal',
		'🔑' => '🔑 Key',
		'👁️' => '👁️ Eye',
		'🤝' => '🤝 Handshake',
		'🕐' => '🕐 Clock',
		'🎓' => '🎓 Graduation',
		'🚨' => '🚨 Siren',
		'🏬' => '🏬 Storefront',
		'🎪' => '🎪 Event',
	);
}

/**
 * Default empty structure for 4 homepage hero slides.
 *
 * @return array<int, array<string, mixed>>
 */
function tqs_hero_default_homepage_slides() {
	$slides = array();
	for ( $i = 0; $i < 4; $i++ ) {
		$slides[] = array(
			'enabled'          => ( 0 === $i ),
			'image_id'         => 0,
			'badge'            => '',
			'icon'             => '🛡️',
			'title'            => '',
			'highlight'        => '',
			'subtitle'         => '',
			'override_buttons' => false,
			'btn1_text'        => '',
			'btn1_url'         => '',
			'btn2_text'        => '',
			'btn2_url'         => '',
		);
	}
	return $slides;
}

/**
 * Default empty page hero structure.
 *
 * @return array<string, mixed>
 */
function tqs_hero_default_page_hero() {
	return array(
		'image_id' => 0,
		'title'    => '',
		'subtitle' => '',
		'btn_text' => '',
		'btn_url'  => '',
	);
}

/**
 * Sanitize URL or site-relative path (reuse theme helper when available).
 *
 * @param string $url Raw URL.
 * @return string
 */
function tqs_hero_sanitize_url_or_path( $url ) {
	if ( function_exists( 'tqs_sanitize_url_or_path' ) ) {
		return tqs_sanitize_url_or_path( $url );
	}
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
 * Sanitize and normalize homepage slides array to exactly 4 entries.
 *
 * @param mixed $raw Raw POST/meta value.
 * @return array<int, array<string, mixed>>
 */
function tqs_hero_sanitize_homepage_slides( $raw ) {
	$defaults = tqs_hero_default_homepage_slides();
	$allowed  = array_keys( tqs_hero_emoji_options() );
	$out      = array();

	if ( ! is_array( $raw ) ) {
		return $defaults;
	}

	for ( $i = 0; $i < 4; $i++ ) {
		$row  = isset( $raw[ $i ] ) && is_array( $raw[ $i ] ) ? $raw[ $i ] : array();
		$icon = isset( $row['icon'] ) ? sanitize_text_field( (string) $row['icon'] ) : $defaults[ $i ]['icon'];
		if ( ! in_array( $icon, $allowed, true ) ) {
			$icon = $defaults[ $i ]['icon'];
		}

		$out[] = array(
			'enabled'          => ! empty( $row['enabled'] ),
			'image_id'         => isset( $row['image_id'] ) ? absint( $row['image_id'] ) : 0,
			'badge'            => isset( $row['badge'] ) ? sanitize_text_field( (string) $row['badge'] ) : '',
			'icon'             => $icon,
			'title'            => isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '',
			'highlight'        => isset( $row['highlight'] ) ? sanitize_text_field( (string) $row['highlight'] ) : '',
			'subtitle'         => isset( $row['subtitle'] ) ? sanitize_textarea_field( (string) $row['subtitle'] ) : '',
			'override_buttons' => ! empty( $row['override_buttons'] ),
			'btn1_text'        => isset( $row['btn1_text'] ) ? sanitize_text_field( (string) $row['btn1_text'] ) : '',
			'btn1_url'         => isset( $row['btn1_url'] ) ? tqs_hero_sanitize_url_or_path( (string) $row['btn1_url'] ) : '',
			'btn2_text'        => isset( $row['btn2_text'] ) ? sanitize_text_field( (string) $row['btn2_text'] ) : '',
			'btn2_url'         => isset( $row['btn2_url'] ) ? tqs_hero_sanitize_url_or_path( (string) $row['btn2_url'] ) : '',
		);
	}

	return $out;
}

/**
 * Sanitize page hero array.
 *
 * @param mixed $raw Raw POST/meta value.
 * @return array<string, mixed>
 */
function tqs_hero_sanitize_page_hero( $raw ) {
	$defaults = tqs_hero_default_page_hero();
	if ( ! is_array( $raw ) ) {
		return $defaults;
	}

	return array(
		'image_id' => isset( $raw['image_id'] ) ? absint( $raw['image_id'] ) : 0,
		'title'    => isset( $raw['title'] ) ? sanitize_text_field( (string) $raw['title'] ) : '',
		'subtitle' => isset( $raw['subtitle'] ) ? sanitize_textarea_field( (string) $raw['subtitle'] ) : '',
		'btn_text' => isset( $raw['btn_text'] ) ? sanitize_text_field( (string) $raw['btn_text'] ) : '',
		'btn_url'  => isset( $raw['btn_url'] ) ? tqs_hero_sanitize_url_or_path( (string) $raw['btn_url'] ) : '',
	);
}

/**
 * Sanitize hero type.
 *
 * @param string $type Raw type.
 * @return string
 */
function tqs_hero_sanitize_type( $type ) {
	$type    = sanitize_text_field( (string) $type );
	$allowed = array( 'none', 'homepage_hero', 'page_hero' );
	return in_array( $type, $allowed, true ) ? $type : 'none';
}

/**
 * Get stored homepage slides (always 4 entries).
 *
 * @param int $post_id Post ID.
 * @return array<int, array<string, mixed>>
 */
function tqs_get_stored_homepage_hero_slides( $post_id ) {
	$stored = get_post_meta( $post_id, '_tqs_homepage_hero_slides', true );
	if ( empty( $stored ) || ! is_array( $stored ) ) {
		return tqs_hero_default_homepage_slides();
	}
	return tqs_hero_sanitize_homepage_slides( $stored );
}

/**
 * Get stored page hero.
 *
 * @param int $post_id Post ID.
 * @return array<string, mixed>
 */
function tqs_get_stored_page_hero( $post_id ) {
	$stored = get_post_meta( $post_id, '_tqs_page_hero', true );
	if ( empty( $stored ) || ! is_array( $stored ) ) {
		return tqs_hero_default_page_hero();
	}
	return tqs_hero_sanitize_page_hero( $stored );
}

/**
 * Register meta box under the editor (normal / high).
 */
function tqs_register_hero_settings_meta_box() {
	add_meta_box(
		'tqs_hero_settings_box',
		__( 'Hero Settings', 'tqs-theme' ),
		'tqs_render_hero_settings_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tqs_register_hero_settings_meta_box' );

/**
 * Enqueue media + admin script on page edit screens only.
 *
 * @param string $hook Admin hook suffix.
 */
function tqs_enqueue_hero_settings_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style(
		'tqs-admin-hero-metabox',
		get_template_directory_uri() . '/assets/css/admin-hero-metabox.css',
		array(),
		tqs_theme_version()
	);
	wp_enqueue_script(
		'tqs-admin-hero-metabox',
		get_template_directory_uri() . '/assets/js/admin-hero-metabox.js',
		array( 'jquery' ),
		tqs_theme_version(),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'tqs_enqueue_hero_settings_admin_assets' );

/**
 * Render a media image field row.
 *
 * @param string $input_name  Input name attribute.
 * @param string $input_id    Input id attribute.
 * @param int    $image_id    Attachment ID.
 * @param string $button_label Upload button label.
 */
function tqs_hero_render_image_field( $input_name, $input_id, $image_id, $button_label ) {
	$image_id  = absint( $image_id );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<div class="tqs-hero-image-field" data-tqs-media-field>
		<input type="hidden" name="<?php echo esc_attr( $input_name ); ?>" id="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_attr( (string) $image_id ); ?>" class="tqs-hero-image-id">
		<div class="tqs-hero-image-preview">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="">
			<?php endif; ?>
		</div>
		<p class="tqs-hero-image-actions">
			<button type="button" class="button tqs-hero-upload-btn"><?php echo esc_html( $button_label ); ?></button>
			<button type="button" class="button tqs-hero-remove-btn"<?php echo $image_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Afbeelding verwijderen', 'tqs-theme' ); ?></button>
		</p>
	</div>
	<?php
}

/**
 * Render the Hero Settings meta box.
 *
 * @param WP_Post $post Current post.
 */
function tqs_render_hero_settings_meta_box( $post ) {
	wp_nonce_field( 'tqs_save_hero_settings', 'tqs_hero_settings_nonce' );

	$hero_type    = tqs_hero_sanitize_type( get_post_meta( $post->ID, '_tqs_hero_type', true ) );
	$hero_enabled = get_post_meta( $post->ID, '_tqs_hero_enabled', true );
	/* Default enabled when meta never saved. */
	if ( '' === $hero_enabled && ! metadata_exists( 'post', $post->ID, '_tqs_hero_enabled' ) ) {
		$hero_enabled = '1';
	}
	$slides    = tqs_get_stored_homepage_hero_slides( $post->ID );
	$page_hero = tqs_get_stored_page_hero( $post->ID );
	$emojis    = tqs_hero_emoji_options();
	?>
	<div id="tqs-hero-settings" class="tqs-hero-settings">

		<div class="tqs-hero-settings-row tqs-hero-settings-top">
			<p>
				<label for="tqs_hero_type"><strong><?php esc_html_e( 'Hero Type', 'tqs-theme' ); ?></strong></label><br>
				<select name="tqs_hero_type" id="tqs_hero_type" class="tqs-hero-type-select">
					<option value="none" <?php selected( $hero_type, 'none' ); ?>><?php esc_html_e( 'Geen hero', 'tqs-theme' ); ?></option>
					<option value="homepage_hero" <?php selected( $hero_type, 'homepage_hero' ); ?>><?php esc_html_e( 'Homepage Hero (multi-slide)', 'tqs-theme' ); ?></option>
					<option value="page_hero" <?php selected( $hero_type, 'page_hero' ); ?>><?php esc_html_e( 'Page Hero (enkele afbeelding)', 'tqs-theme' ); ?></option>
				</select>
			</p>
			<p>
				<label>
					<input type="checkbox" name="tqs_hero_enabled" id="tqs_hero_enabled" value="1" <?php checked( $hero_enabled, '1' ); ?>>
					<strong><?php esc_html_e( 'Toon Hero', 'tqs-theme' ); ?></strong>
				</label>
				<br>
				<span class="description"><?php esc_html_e( 'Schakel tijdelijk uit zonder gegevens of Hero Type te wissen. Verschilt van “Geen hero”.', 'tqs-theme' ); ?></span>
			</p>
		</div>

		<!-- Field Group C — informational only (edited in Customizer) -->
		<div class="tqs-hero-field-group tqs-hero-global-buttons" id="tqs-hero-group-global-buttons">
			<h3><?php esc_html_e( 'Standaard hero-knoppen (globaal)', 'tqs-theme' ); ?></h3>
			<p class="description" style="font-style:italic;margin-bottom:0;">
				<?php esc_html_e( 'Deze standaardknoppen (tekst + URL) beheer je via Weergave → Customizer → TQS Theme Settings → Hero — Global Defaults. Ze gelden voor elke Homepage Hero-slide die knoppen hieronder niet overschrijft.', 'tqs-theme' ); ?>
			</p>
		</div>

		<!-- Field Group A — Homepage Hero -->
		<div class="tqs-hero-field-group" id="tqs-hero-group-homepage" data-tqs-hero-group="homepage_hero" hidden>
			<h3><?php esc_html_e( 'Homepage Hero', 'tqs-theme' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Tot 4 slides. Slide 1 staat standaard aan; schakel extra slides in naar wens.', 'tqs-theme' ); ?></p>

			<?php foreach ( $slides as $i => $slide ) :
				$n          = $i + 1;
				$prefix     = "tqs_homepage_hero_slides[{$i}]";
				$id_base    = "tqs_slide_{$i}";
				$open_attr  = ( 0 === $i ) ? ' open' : '';
				?>
			<details class="tqs-hero-slide-box"<?php echo $open_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<summary><strong><?php echo esc_html( sprintf( __( 'Slide %d', 'tqs-theme' ), $n ) ); ?></strong></summary>
				<div class="tqs-hero-slide-body">
					<p>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[enabled]" value="1" <?php checked( ! empty( $slide['enabled'] ) ); ?>>
							<?php esc_html_e( 'Deze slide inschakelen', 'tqs-theme' ); ?>
						</label>
					</p>

					<p><strong><?php esc_html_e( 'Afbeelding', 'tqs-theme' ); ?></strong></p>
					<?php
					tqs_hero_render_image_field(
						$prefix . '[image_id]',
						$id_base . '_image_id',
						isset( $slide['image_id'] ) ? (int) $slide['image_id'] : 0,
						__( 'Kies afbeelding', 'tqs-theme' )
					);
					?>

					<p>
						<label for="<?php echo esc_attr( $id_base ); ?>_badge"><strong><?php esc_html_e( 'Badge tekst', 'tqs-theme' ); ?></strong></label>
						<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_badge" name="<?php echo esc_attr( $prefix ); ?>[badge]" value="<?php echo esc_attr( $slide['badge'] ); ?>" placeholder="ND 7099 GECERTIFICEERD">
					</p>

					<p>
						<label for="<?php echo esc_attr( $id_base ); ?>_icon"><strong><?php esc_html_e( 'Icoon / emoji', 'tqs-theme' ); ?></strong></label><br>
						<select id="<?php echo esc_attr( $id_base ); ?>_icon" name="<?php echo esc_attr( $prefix ); ?>[icon]" class="tqs-hero-emoji-select">
							<?php foreach ( $emojis as $emoji => $label ) : ?>
								<option value="<?php echo esc_attr( $emoji ); ?>" <?php selected( $slide['icon'], $emoji ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>

					<div class="tqs-hero-grid-2">
						<p>
							<label for="<?php echo esc_attr( $id_base ); ?>_title"><strong><?php esc_html_e( 'Titel', 'tqs-theme' ); ?></strong></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_title" name="<?php echo esc_attr( $prefix ); ?>[title]" value="<?php echo esc_attr( $slide['title'] ); ?>">
						</p>
						<p>
							<label for="<?php echo esc_attr( $id_base ); ?>_highlight"><strong><?php esc_html_e( 'Titel highlight (goud)', 'tqs-theme' ); ?></strong></label>
							<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_highlight" name="<?php echo esc_attr( $prefix ); ?>[highlight]" value="<?php echo esc_attr( $slide['highlight'] ); ?>">
						</p>
					</div>

					<p>
						<label for="<?php echo esc_attr( $id_base ); ?>_subtitle"><strong><?php esc_html_e( 'Subtitel', 'tqs-theme' ); ?></strong></label>
						<textarea class="widefat" rows="3" id="<?php echo esc_attr( $id_base ); ?>_subtitle" name="<?php echo esc_attr( $prefix ); ?>[subtitle]"><?php echo esc_textarea( $slide['subtitle'] ); ?></textarea>
					</p>

					<p>
						<label>
							<input type="checkbox" class="tqs-hero-override-btns" name="<?php echo esc_attr( $prefix ); ?>[override_buttons]" value="1" <?php checked( ! empty( $slide['override_buttons'] ) ); ?> data-tqs-override-target="<?php echo esc_attr( $id_base ); ?>_btn_fields">
							<?php esc_html_e( 'Standaardknoppen overschrijven voor deze slide', 'tqs-theme' ); ?>
						</label>
					</p>

					<div class="tqs-hero-slide-btn-fields" id="<?php echo esc_attr( $id_base ); ?>_btn_fields" <?php echo ! empty( $slide['override_buttons'] ) ? '' : 'hidden'; ?>>
						<div class="tqs-hero-grid-2">
							<p>
								<label for="<?php echo esc_attr( $id_base ); ?>_btn1_text"><strong><?php esc_html_e( 'Knop 1 tekst', 'tqs-theme' ); ?></strong></label>
								<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_btn1_text" name="<?php echo esc_attr( $prefix ); ?>[btn1_text]" value="<?php echo esc_attr( $slide['btn1_text'] ); ?>">
							</p>
							<p>
								<label for="<?php echo esc_attr( $id_base ); ?>_btn1_url"><strong><?php esc_html_e( 'Knop 1 URL', 'tqs-theme' ); ?></strong></label>
								<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_btn1_url" name="<?php echo esc_attr( $prefix ); ?>[btn1_url]" value="<?php echo esc_attr( $slide['btn1_url'] ); ?>" placeholder="<?php esc_attr_e( 'bijv. /contact of https://…', 'tqs-theme' ); ?>">
							</p>
							<p>
								<label for="<?php echo esc_attr( $id_base ); ?>_btn2_text"><strong><?php esc_html_e( 'Knop 2 tekst', 'tqs-theme' ); ?></strong></label>
								<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_btn2_text" name="<?php echo esc_attr( $prefix ); ?>[btn2_text]" value="<?php echo esc_attr( $slide['btn2_text'] ); ?>">
							</p>
							<p>
								<label for="<?php echo esc_attr( $id_base ); ?>_btn2_url"><strong><?php esc_html_e( 'Knop 2 URL', 'tqs-theme' ); ?></strong></label>
								<input type="text" class="widefat" id="<?php echo esc_attr( $id_base ); ?>_btn2_url" name="<?php echo esc_attr( $prefix ); ?>[btn2_url]" value="<?php echo esc_attr( $slide['btn2_url'] ); ?>" placeholder="<?php esc_attr_e( 'bijv. /onze-diensten of https://…', 'tqs-theme' ); ?>">
							</p>
						</div>
					</div>
				</div>
			</details>
			<?php endforeach; ?>
		</div>

		<!-- Field Group B — Page Hero -->
		<div class="tqs-hero-field-group" id="tqs-hero-group-page" data-tqs-hero-group="page_hero" hidden>
			<h3><?php esc_html_e( 'Page Hero', 'tqs-theme' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Eenvoudige hero met één afbeelding voor een binnenpagina.', 'tqs-theme' ); ?></p>

			<p><strong><?php esc_html_e( 'Afbeelding', 'tqs-theme' ); ?></strong></p>
			<?php
			tqs_hero_render_image_field(
				'tqs_page_hero[image_id]',
				'tqs_page_hero_image_id',
				isset( $page_hero['image_id'] ) ? (int) $page_hero['image_id'] : 0,
				__( 'Kies afbeelding', 'tqs-theme' )
			);
			?>

			<p>
				<label for="tqs_page_hero_title"><strong><?php esc_html_e( 'Titel', 'tqs-theme' ); ?></strong></label>
				<input type="text" class="widefat" id="tqs_page_hero_title" name="tqs_page_hero[title]" value="<?php echo esc_attr( $page_hero['title'] ); ?>">
			</p>
			<p>
				<label for="tqs_page_hero_subtitle"><strong><?php esc_html_e( 'Subtitel', 'tqs-theme' ); ?></strong></label>
				<textarea class="widefat" rows="3" id="tqs_page_hero_subtitle" name="tqs_page_hero[subtitle]"><?php echo esc_textarea( $page_hero['subtitle'] ); ?></textarea>
			</p>
			<div class="tqs-hero-grid-2">
				<p>
					<label for="tqs_page_hero_btn_text"><strong><?php esc_html_e( 'CTA knoptekst (optioneel)', 'tqs-theme' ); ?></strong></label>
					<input type="text" class="widefat" id="tqs_page_hero_btn_text" name="tqs_page_hero[btn_text]" value="<?php echo esc_attr( $page_hero['btn_text'] ); ?>">
				</p>
				<p>
					<label for="tqs_page_hero_btn_url"><strong><?php esc_html_e( 'CTA knop URL', 'tqs-theme' ); ?></strong></label>
					<input type="text" class="widefat" id="tqs_page_hero_btn_url" name="tqs_page_hero[btn_url]" value="<?php echo esc_attr( $page_hero['btn_url'] ); ?>" placeholder="<?php esc_attr_e( 'bijv. /contact of https://…', 'tqs-theme' ); ?>">
				</p>
			</div>
		</div>

	</div>
	<?php
}

/**
 * Save Hero Settings meta + global default button theme mods.
 *
 * @param int $post_id Post ID.
 */
function tqs_save_hero_settings_meta_box( $post_id ) {
	if ( ! isset( $_POST['tqs_hero_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tqs_hero_settings_nonce'] ) ), 'tqs_save_hero_settings' ) ) {
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

	$hero_type = isset( $_POST['tqs_hero_type'] ) ? tqs_hero_sanitize_type( wp_unslash( $_POST['tqs_hero_type'] ) ) : 'none';
	update_post_meta( $post_id, '_tqs_hero_type', $hero_type );

	$hero_enabled = isset( $_POST['tqs_hero_enabled'] ) ? '1' : '';
	update_post_meta( $post_id, '_tqs_hero_enabled', $hero_enabled );

	$slides_raw = isset( $_POST['tqs_homepage_hero_slides'] ) ? wp_unslash( $_POST['tqs_homepage_hero_slides'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$slides     = tqs_hero_sanitize_homepage_slides( is_array( $slides_raw ) ? $slides_raw : array() );
	update_post_meta( $post_id, '_tqs_homepage_hero_slides', $slides );

	$page_raw  = isset( $_POST['tqs_page_hero'] ) ? wp_unslash( $_POST['tqs_page_hero'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$page_hero = tqs_hero_sanitize_page_hero( is_array( $page_raw ) ? $page_raw : array() );
	update_post_meta( $post_id, '_tqs_page_hero', $page_hero );
}
add_action( 'save_post_page', 'tqs_save_hero_settings_meta_box' );
