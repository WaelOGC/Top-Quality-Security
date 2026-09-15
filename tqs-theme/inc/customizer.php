<?php
/**
 * WordPress Customizer — TQS Theme Settings.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a text/textarea/number/email/url setting + control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param mixed                $default      Default value.
 * @param string               $section      Section ID.
 * @param string               $label        Control label.
 * @param string               $type         Control type.
 * @param callable|string|null $sanitize     Sanitize callback.
 */
function tqs_add_setting( $wp_customize, $id, $default, $section, $label, $type = 'text', $sanitize = null ) {
	if ( null === $sanitize ) {
		$sanitize = 'textarea' === $type ? 'wp_kses_post' : 'sanitize_text_field';
	}
	$wp_customize->add_setting( $id, array(
		'default'           => $default,
		'sanitize_callback' => $sanitize,
	) );
	$wp_customize->add_control( $id, array(
		'label'   => $label,
		'section' => $section,
		'type'    => $type,
	) );
}

/**
 * Register a color setting with live preview transport.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param string               $default      Default hex.
 * @param string               $section      Section ID.
 * @param string               $label        Control label.
 */
function tqs_add_color_setting( $wp_customize, $id, $default, $section, $label ) {
	$wp_customize->add_setting( $id, array(
		'default'           => $default,
		'sanitize_callback' => 'tqs_sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
		'label'   => $label,
		'section' => $section,
	) ) );
}

/**
 * Register a media image setting.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param string               $section      Section ID.
 * @param string               $label        Control label.
 */
function tqs_add_image_setting( $wp_customize, $id, $section, $label ) {
	$wp_customize->add_setting( $id, array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array(
		'label'     => $label,
		'section'   => $section,
		'mime_type' => 'image',
	) ) );
}

/**
 * Register a checkbox setting.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 * @param string               $id           Setting ID.
 * @param bool                 $default      Default.
 * @param string               $section      Section ID.
 * @param string               $label        Control label.
 */
function tqs_add_checkbox( $wp_customize, $id, $default, $section, $label ) {
	$wp_customize->add_setting( $id, array(
		'default'           => $default,
		'sanitize_callback' => 'tqs_sanitize_checkbox',
	) );
	$wp_customize->add_control( $id, array(
		'label'   => $label,
		'section' => $section,
		'type'    => 'checkbox',
	) );
}

/**
 * Resolve hero autoplay interval to milliseconds.
 *
 * Supports legacy stored millisecond values (> 120) and new second-based values.
 *
 * @return int
 */
function tqs_get_hero_autoplay_ms() {
	$raw = absint( get_theme_mod( 'tqs_hero_autoplay_interval', 6 ) );
	if ( $raw > 120 ) {
		return max( 2000, $raw );
	}
	return max( 2, $raw ) * 1000;
}

/**
 * Register all Customizer panels, sections, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function tqs_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'tqs_theme_settings', array(
		'title'    => __( '🛡️ TQS Theme Settings', 'tqs-theme' ),
		'priority' => 30,
	) );

	$wp_customize->add_panel( 'tqs_homepage_panel', array(
		'title'       => __( 'Homepage Sections', 'tqs-theme' ),
		'description' => __( 'Settings for homepage sections (below the hero). Manage the hero itself per page via Hero Settings in the page editor.', 'tqs-theme' ),
		'priority'    => 31,
	) );

	/* --- Site Identity & Branding --- */
	$wp_customize->add_section( 'tqs_identity_settings', array(
		'title'       => __( 'Site Identity & Branding', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'priority'    => 10,
		'description' => __( 'Upload logos for the header, footer, and search engines. By default the theme uses top-logo.png and top-Identity-logo.png. Site icon (favicon): Appearance → Site Identity.', 'tqs-theme' ),
	) );
	tqs_add_image_setting( $wp_customize, 'tqs_header_logo', 'tqs_identity_settings', __( 'Header logo (visible on the site)', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_footer_logo', 'tqs_identity_settings', __( 'Footer logo (optional — otherwise same as header)', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_identity_logo', 'tqs_identity_settings', __( 'Identity logo (SEO / search engines)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_brand_sub', 'BEVEILIGINGSDIENSTEN', 'tqs_identity_settings', __( 'Tagline under logo', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_og_default_image', 'tqs_identity_settings', __( 'Default social/OG image', 'tqs-theme' ) );

	/* --- Brand Colors & Fonts --- */
	$wp_customize->add_section( 'tqs_brand_colors', array(
		'title'       => __( 'Brand Colors & Fonts', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'priority'    => 20,
		'description' => __( 'Leave empty or use the default TQS colors from style.css. Changes are applied as CSS variables.', 'tqs-theme' ),
	) );
	$color_defaults = tqs_get_brand_color_defaults();
	$color_labels   = array(
		'tqs_color_primary'    => __( 'Primary (deep purple)', 'tqs-theme' ),
		'tqs_color_secondary'  => __( 'Secondary (vivid purple)', 'tqs-theme' ),
		'tqs_color_gold'       => __( 'Accent / CTA gold', 'tqs-theme' ),
		'tqs_color_gold_light' => __( 'Gold hover', 'tqs-theme' ),
		'tqs_color_dark'       => __( 'Dark backgrounds', 'tqs-theme' ),
		'tqs_color_light'      => __( 'Light sections', 'tqs-theme' ),
		'tqs_color_text'       => __( 'Body text', 'tqs-theme' ),
		'tqs_color_muted'      => __( 'Muted text', 'tqs-theme' ),
	);
	foreach ( $color_labels as $key => $label ) {
		tqs_add_color_setting( $wp_customize, $key, $color_defaults[ $key ], 'tqs_brand_colors', $label );
	}

	/* --- Header & Navigation --- */
	$wp_customize->add_section( 'tqs_header_settings', array(
		'title'    => __( 'Header & Navigation', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 30,
	) );
	tqs_add_setting( $wp_customize, 'tqs_phone', '+31 (0)70 123 4567', 'tqs_header_settings', __( 'Phone number', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_email', 'info@topqualitysecurity.com', 'tqs_header_settings', __( 'Email address', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_topbar', true, 'tqs_header_settings', __( 'Show top bar', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_topbar_location', 'Den Haag, Nederland', 'tqs_header_settings', __( 'Top bar location text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_header_cta_text', 'Offerte Aanvragen', 'tqs_header_settings', __( 'CTA button text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_header_cta_url', '/contact', 'tqs_header_settings', __( 'CTA button URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_checkbox( $wp_customize, 'tqs_show_whatsapp_fab', true, 'tqs_header_settings', __( 'Show WhatsApp button (bottom right)', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_breadcrumbs', true, 'tqs_header_settings', __( 'Show breadcrumbs on inner pages', 'tqs-theme' ) );

	/* --- Hero — Global Defaults --- */
	$wp_customize->add_section( 'tqs_hero_global_defaults', array(
		'title'       => __( 'Hero — Global Defaults', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'priority'    => 40,
		'description' => __( 'Global default buttons for Homepage Hero slides that do not override buttons. Manage slide content per page via Hero Settings in the page editor. The default hero image is a fallback for inner pages without their own image.', 'tqs-theme' ),
	) );
	tqs_add_setting( $wp_customize, 'tqs_hero_default_btn1_text', 'Offerte Aanvragen', 'tqs_hero_global_defaults', __( 'Default button 1 text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_default_btn1_url', '/contact', 'tqs_hero_global_defaults', __( 'Default button 1 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_setting( $wp_customize, 'tqs_hero_default_btn2_text', 'Onze Diensten', 'tqs_hero_global_defaults', __( 'Default button 2 text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_default_btn2_url', '/onze-diensten', 'tqs_hero_global_defaults', __( 'Default button 2 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_checkbox( $wp_customize, 'tqs_hero_autoplay', true, 'tqs_hero_global_defaults', __( 'Homepage hero autoplay', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_autoplay_interval', '6', 'tqs_hero_global_defaults', __( 'Hero autoplay interval (seconds)', 'tqs-theme' ), 'number', 'absint' );
	tqs_add_image_setting( $wp_customize, 'tqs_hero_image', 'tqs_hero_global_defaults', __( 'Default hero background image (inner pages)', 'tqs-theme' ) );

	/* --- Company Information --- */
	$wp_customize->add_section( 'tqs_company_settings', array(
		'title'    => __( 'Company Information', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 50,
	) );
	tqs_add_setting( $wp_customize, 'tqs_kvk', '', 'tqs_company_settings', __( 'Chamber of Commerce (KvK) number', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_btw', '', 'tqs_company_settings', __( 'VAT (BTW) number', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_nd_cert', 'ND 7099', 'tqs_company_settings', __( 'ND certification number', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_address', 'Spui 70, 2511 BT Den Haag', 'tqs_company_settings', __( 'Address', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hours', "Ma - Vr: 09:00 - 18:00\nWeekend: Op afspraak", 'tqs_company_settings', __( 'Opening hours', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_whatsapp_number', '31636286183', 'tqs_company_settings', __( 'WhatsApp number (with country code)', 'tqs-theme' ) );

	/* --- Contact & Forms --- */
	$wp_customize->add_section( 'tqs_contact_settings', array(
		'title'    => __( 'Contact & Forms', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 60,
	) );
	tqs_add_setting( $wp_customize, 'tqs_contact_recipient', '', 'tqs_contact_settings', __( 'Form recipient (empty = header email)', 'tqs-theme' ), 'email', 'sanitize_email' );
	tqs_add_setting( $wp_customize, 'tqs_contact_hero_subtitle', 'Vragen over onze diensten of een offerte op maat? Neem contact op — wij reageren binnen één werkdag.', 'tqs_contact_settings', __( 'Contact page — hero subtitle', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_contact_form_title', 'Stuur Ons Een Bericht', 'tqs_contact_settings', __( 'Contact form — title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_contact_form_sub', 'Vul het formulier in en wij nemen zo spoedig mogelijk contact met u op.', 'tqs_contact_settings', __( 'Contact form — intro', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_contact_hours_note', '24/7 bereikbaar voor lopende opdrachten', 'tqs_contact_settings', __( 'Opening hours — note', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_contact_whatsapp_title', 'Chat via WhatsApp', 'tqs_contact_settings', __( 'WhatsApp card — title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_contact_whatsapp_sub', 'Snel antwoord tijdens kantooruren', 'tqs_contact_settings', __( 'WhatsApp card — subtitle', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_form_success_msg', 'Bedankt voor uw bericht! Wij nemen zo spoedig mogelijk contact met u op.', 'tqs_contact_settings', __( 'Form success message', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_form_error_msg', 'Er ging iets mis bij het verzenden. Probeer het later opnieuw of bel ons direct.', 'tqs_contact_settings', __( 'Form error message', 'tqs-theme' ), 'textarea' );
	tqs_add_checkbox( $wp_customize, 'tqs_show_contact_map', true, 'tqs_contact_settings', __( 'Show map on contact page', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_maps_embed_url', '', 'tqs_contact_settings', __( 'Google Maps embed URL (optional)', 'tqs-theme' ), 'url', 'esc_url_raw' );

	/* --- Gallery --- */
	$wp_customize->add_section( 'tqs_gallery_settings', array(
		'title'       => __( 'Gallery Settings', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'priority'    => 70,
		'description' => __( 'Default display for the photo gallery page. Per item you can still set a custom display style in Gallery.', 'tqs-theme' ),
	) );
	$wp_customize->add_setting( 'tqs_gallery_columns', array(
		'default'           => 4,
		'sanitize_callback' => 'tqs_sanitize_gallery_columns',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'tqs_gallery_columns', array(
		'label'   => __( 'Number of columns', 'tqs-theme' ),
		'section' => 'tqs_gallery_settings',
		'type'    => 'select',
		'choices' => array(
			4 => __( '4 columns', 'tqs-theme' ),
			3 => __( '3 columns', 'tqs-theme' ),
		),
	) );
	$wp_customize->add_setting( 'tqs_gallery_lightbox_enabled', array(
		'default'           => true,
		'sanitize_callback' => 'tqs_sanitize_checkbox',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'tqs_gallery_lightbox_enabled', array(
		'label'   => __( 'Enable lightbox', 'tqs-theme' ),
		'section' => 'tqs_gallery_settings',
		'type'    => 'checkbox',
	) );
	$wp_customize->add_setting( 'tqs_gallery_show_filters', array(
		'default'           => true,
		'sanitize_callback' => 'tqs_sanitize_checkbox',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'tqs_gallery_show_filters', array(
		'label'   => __( 'Show category filters', 'tqs-theme' ),
		'section' => 'tqs_gallery_settings',
		'type'    => 'checkbox',
	) );
	tqs_add_setting( $wp_customize, 'tqs_gallery_hero_subtitle', 'Een impressie van ons werk in de praktijk.', 'tqs_gallery_settings', __( 'Photo gallery — hero subtitle', 'tqs-theme' ), 'textarea' );

	/* --- Footer --- */
	$wp_customize->add_section( 'tqs_footer_settings', array(
		'title'    => __( 'Footer Settings', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 80,
	) );
	tqs_add_setting( $wp_customize, 'tqs_footer_tagline', 'Professionele beveiligingsdiensten vanuit Den Haag, actief door heel Nederland. Gecertificeerd, betrouwbaar en 24/7 paraat.', 'tqs_footer_settings', __( 'Footer tagline', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_copyright', '© {year} Top Quality Security. Alle rechten voorbehouden.', 'tqs_footer_settings', __( 'Copyright text ({year} = current year)', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_social', true, 'tqs_footer_settings', __( 'Show social media icons', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_kvk', false, 'tqs_footer_settings', __( 'Show KvK in footer', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_btw', false, 'tqs_footer_settings', __( 'Show VAT in footer', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_nd', true, 'tqs_footer_settings', __( 'Show ND certificate in footer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_quick', 'SNELLE LINKS', 'tqs_footer_settings', __( 'Column title — Quick links', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_services', 'DIENSTEN', 'tqs_footer_settings', __( 'Column title — Services', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_contact', 'CONTACT', 'tqs_footer_settings', __( 'Column title — Contact', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_facebook_url', '', 'tqs_footer_settings', __( 'Facebook URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_instagram_url', '', 'tqs_footer_settings', __( 'Instagram URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_linkedin_url', '', 'tqs_footer_settings', __( 'LinkedIn URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_x_url', '', 'tqs_footer_settings', __( 'X (Twitter) URL', 'tqs-theme' ), 'url', 'esc_url_raw' );

	/* --- SEO & Analytics --- */
	$wp_customize->add_section( 'tqs_seo_settings', array(
		'title'    => __( 'SEO & Analytics', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 90,
	) );
	tqs_add_setting( $wp_customize, 'tqs_home_meta_description', 'Top Quality Security levert professionele beveiligingsdiensten door heel Nederland, vanuit Den Haag. ND 7099 gecertificeerd, betrouwbaar en 24/7 beschikbaar.', 'tqs_seo_settings', __( 'Homepage meta description', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_ga_id', '', 'tqs_seo_settings', __( 'Google Analytics 4 ID (e.g. G-XXXXXXXX)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_gtm_id', '', 'tqs_seo_settings', __( 'Google Tag Manager ID (e.g. GTM-XXXXXXX)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_archive_services_title', 'Onze Diensten', 'tqs_seo_settings', __( 'Services archive — title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_archive_services_lead', 'Van winkelvloer tot evenemententerrein — TQS levert beveiligingsoplossingen op maat voor iedere sector, met gecertificeerd en professioneel personeel.', 'tqs_seo_settings', __( 'Services archive — intro', 'tqs-theme' ), 'textarea' );

	/* --- 404 Page --- */
	$wp_customize->add_section( 'tqs_404_settings', array(
		'title'    => __( '404 Page', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 100,
	) );
	tqs_add_setting( $wp_customize, 'tqs_404_title', 'Pagina Niet Gevonden', 'tqs_404_settings', __( 'Title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_404_message', 'De pagina die u zoekt bestaat niet (meer) of is verplaatst.', 'tqs_404_settings', __( 'Message', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_404_btn_text', 'Terug Naar Home', 'tqs_404_settings', __( 'Button text', 'tqs-theme' ) );

	/* --- GDPR Cookie Banner --- */
	$wp_customize->add_section( 'tqs_cookie_settings', array(
		'title'    => __( 'GDPR Cookie Banner', 'tqs-theme' ),
		'panel'    => 'tqs_theme_settings',
		'priority' => 110,
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_cookie_enabled', true, 'tqs_cookie_settings', __( 'Enable cookie banner', 'tqs-theme' ) );
	$wp_customize->add_setting( 'tqs_cookie_text', array(
		'default'           => 'Wij gebruiken cookies om uw ervaring te verbeteren. Lees ons <a href="/privacybeleid">privacybeleid</a> voor meer informatie.',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'tqs_cookie_text', array(
		'label'   => __( 'Cookie banner text (HTML allowed)', 'tqs-theme' ),
		'section' => 'tqs_cookie_settings',
		'type'    => 'textarea',
	) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_accept_text', 'Accepteren', 'tqs_cookie_settings', __( 'Accept button text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_decline_text', 'Weigeren', 'tqs_cookie_settings', __( 'Decline button text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_expiry_days', '180', 'tqs_cookie_settings', __( 'Cookie expiry (days)', 'tqs-theme' ), 'number', 'absint' );

	/* ================================================================== */
	/* Homepage Sections panel                                            */
	/* ================================================================== */

	$wp_customize->add_section( 'tqs_stats_settings', array(
		'title'    => __( 'Stats Bar', 'tqs-theme' ),
		'panel'    => 'tqs_homepage_panel',
		'priority' => 10,
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_stats', true, 'tqs_stats_settings', __( 'Show stats bar', 'tqs-theme' ) );
	$default_stats = array(
		array( 'value' => '10+',  'label' => 'Jaar Ervaring' ),
		array( 'value' => '500+', 'label' => 'Tevreden Klanten' ),
		array( 'value' => '24/7', 'label' => 'Beschikbaar' ),
		array( 'value' => '7',    'label' => 'Sectoren' ),
	);
	foreach ( $default_stats as $i => $stat ) {
		tqs_add_setting( $wp_customize, "tqs_stat_{$i}_value", $stat['value'], 'tqs_stats_settings', sprintf( __( 'Stat %d — Value', 'tqs-theme' ), $i + 1 ) );
		tqs_add_setting( $wp_customize, "tqs_stat_{$i}_label", $stat['label'], 'tqs_stats_settings', sprintf( __( 'Stat %d — Label', 'tqs-theme' ), $i + 1 ) );
	}

	$wp_customize->add_section( 'tqs_services_section', array(
		'title'    => __( 'Services Section', 'tqs-theme' ),
		'panel'    => 'tqs_homepage_panel',
		'priority' => 20,
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_services', true, 'tqs_services_section', __( 'Show services section', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_eyebrow', 'ONZE DIENSTEN', 'tqs_services_section', __( 'Eyebrow text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_title', 'Beveiligingsoplossingen op maat', 'tqs_services_section', __( 'Section title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_lead', 'Voor elke sector een passende aanpak — professioneel, gecertificeerd en altijd paraat.', 'tqs_services_section', __( 'Intro text', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_services_filler_title', 'Andere sector?', 'tqs_services_section', __( 'Filler slide — title (odd number of services)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_filler_text', 'Neem contact op voor meer informatie over een oplossing op maat.', 'tqs_services_section', __( 'Filler slide — text', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_services_filler_btn_text', 'Neem Contact Op', 'tqs_services_section', __( 'Filler slide — button text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_filler_btn_url', '/contact', 'tqs_services_section', __( 'Filler slide — button URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );

	$wp_customize->add_section( 'tqs_why_us_section', array(
		'title'    => __( 'Why Us', 'tqs-theme' ),
		'panel'    => 'tqs_homepage_panel',
		'priority' => 30,
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_why_us', true, 'tqs_why_us_section', __( 'Show Why Us section', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_whyus_manager_image', 'tqs_why_us_section', __( 'Operations manager portrait (light background)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_eyebrow', 'WAAROM TQS', 'tqs_why_us_section', __( 'Eyebrow text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_title', 'Betrouwbaarheid die u kunt zien', 'tqs_why_us_section', __( 'Section title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_text', 'Al meer dan tien jaar biedt TQS professionele beveiliging aan bedrijven, evenementen en instellingen door heel Nederland. Onze medewerkers zijn opgeleid, gecertificeerd en altijd representatief.', 'tqs_why_us_section', __( 'Intro text', 'tqs-theme' ), 'textarea' );
	$why_defaults = tqs_get_default_why_us_cards();
	foreach ( $why_defaults as $i => $card ) {
		$n = $i + 1;
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_icon", $card['icon'], 'tqs_why_us_section', sprintf( __( 'Card %d — Font Awesome icon', 'tqs-theme' ), $n ) );
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_title", $card['title'], 'tqs_why_us_section', sprintf( __( 'Card %d — Title', 'tqs-theme' ), $n ) );
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_desc", $card['desc'], 'tqs_why_us_section', sprintf( __( 'Card %d — Description', 'tqs-theme' ), $n ), 'textarea' );
	}

	$wp_customize->add_section( 'tqs_cta_section', array(
		'title'    => __( 'CTA Banner', 'tqs-theme' ),
		'panel'    => 'tqs_homepage_panel',
		'priority' => 40,
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_cta', true, 'tqs_cta_section', __( 'Show CTA banner', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_cta_bg_image', 'tqs_cta_section', __( 'CTA background image (optional)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_title', 'Klaar voor professionele beveiliging?', 'tqs_cta_section', __( 'Title', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_text', 'Vraag vandaag nog een vrijblijvende offerte aan en ontdek wat TQS voor u kan betekenen.', 'tqs_cta_section', __( 'Text', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn1_text', 'Offerte Aanvragen', 'tqs_cta_section', __( 'Button 1 text', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn1_url', '/contact', 'tqs_cta_section', __( 'Button 1 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn2_text', 'Bel Ons Direct', 'tqs_cta_section', __( 'Button 2 text', 'tqs-theme' ) );
}
add_action( 'customize_register', 'tqs_customize_register' );
