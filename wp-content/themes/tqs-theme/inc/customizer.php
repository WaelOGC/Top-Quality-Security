<?php
/**
 * WordPress Customizer — TQS Theme Settings panel.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

function tqs_customize_register( $wp_customize ) {
	$wp_customize->add_panel( 'tqs_theme_settings', array(
		'title'    => __( '🛡️ TQS Theme Settings', 'tqs-theme' ),
		'priority' => 30,
	) );

	/* --- Site Identity (brand extras) --- */
	$wp_customize->add_section( 'tqs_identity_settings', array(
		'title'       => __( 'Site Identity & Branding', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'description' => __( 'Upload hier de logo\'s voor header, footer en zoekmachines. Standaard worden top-logo.png en top-Identity-logo.png uit het thema gebruikt. Site-icoon (favicon): Appearance → Site Identity.', 'tqs-theme' ),
	) );
	tqs_add_image_setting( $wp_customize, 'tqs_header_logo', 'tqs_identity_settings', __( 'Header logo (zichtbaar op de site)', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_footer_logo', 'tqs_identity_settings', __( 'Footer logo (optioneel — anders zelfde als header)', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_identity_logo', 'tqs_identity_settings', __( 'Identity logo (SEO / zoekmachines)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_brand_sub', 'BEVEILIGINGSDIENSTEN', 'tqs_identity_settings', __( 'Tagline onder logo', 'tqs-theme' ) );
	tqs_add_image_setting( $wp_customize, 'tqs_og_default_image', 'tqs_identity_settings', __( 'Standaard social/OG afbeelding', 'tqs-theme' ) );

	/* --- Brand Colors --- */
	$wp_customize->add_section( 'tqs_brand_colors', array(
		'title'       => __( 'Brand Colors & Fonts', 'tqs-theme' ),
		'panel'       => 'tqs_theme_settings',
		'description' => __( 'Laat leeg of gebruik de standaard TQS-kleuren uit style.css. Wijzigingen worden als CSS-variabelen toegepast.', 'tqs-theme' ),
	) );
	$color_defaults = tqs_get_brand_color_defaults();
	$color_labels   = array(
		'tqs_color_primary'    => __( 'Primary (diep paars)', 'tqs-theme' ),
		'tqs_color_secondary'  => __( 'Secondary (vivid paars)', 'tqs-theme' ),
		'tqs_color_gold'       => __( 'Accent / CTA goud', 'tqs-theme' ),
		'tqs_color_gold_light' => __( 'Goud hover', 'tqs-theme' ),
		'tqs_color_dark'       => __( 'Donkere achtergronden', 'tqs-theme' ),
		'tqs_color_light'      => __( 'Lichte secties', 'tqs-theme' ),
		'tqs_color_text'       => __( 'Body tekst', 'tqs-theme' ),
		'tqs_color_muted'      => __( 'Gedempte tekst', 'tqs-theme' ),
	);
	foreach ( $color_labels as $key => $label ) {
		tqs_add_color_setting( $wp_customize, $key, $color_defaults[ $key ], 'tqs_brand_colors', $label );
	}

	/* --- Header --- */
	$wp_customize->add_section( 'tqs_header_settings', array(
		'title' => __( 'Header & Navigation', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_phone', '+31 (0)70 123 4567', 'tqs_header_settings', __( 'Telefoonnummer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_email', 'info@topqualitysecurity.com', 'tqs_header_settings', __( 'E-mailadres', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_topbar', true, 'tqs_header_settings', __( 'Toon topbalk', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_topbar_location', 'Den Haag, Nederland', 'tqs_header_settings', __( 'Locatietekst topbalk', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_header_cta_text', 'Offerte Aanvragen', 'tqs_header_settings', __( 'CTA knoptekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_header_cta_url', '/contact', 'tqs_header_settings', __( 'CTA knop URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_checkbox( $wp_customize, 'tqs_show_whatsapp_fab', true, 'tqs_header_settings', __( 'Toon WhatsApp-knop (rechtsonder)', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_breadcrumbs', true, 'tqs_header_settings', __( 'Toon breadcrumbs op binnenpagina\'s', 'tqs-theme' ) );

	/* --- Hero Slider --- */
	$wp_customize->add_section( 'tqs_hero_slider_settings', array(
		'title' => __( 'Homepage — Hero Slider', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_hero_autoplay', true, 'tqs_hero_slider_settings', __( 'Automatisch doorschuiven', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_autoplay_interval', '5500', 'tqs_hero_slider_settings', __( 'Interval (milliseconden)', 'tqs-theme' ), 'number', 'absint' );
	tqs_add_checkbox( $wp_customize, 'tqs_hero_show_arrows', true, 'tqs_hero_slider_settings', __( 'Toon pijlen', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_hero_show_dots', true, 'tqs_hero_slider_settings', __( 'Toon stippen', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_btn1_text', 'Offerte Aanvragen', 'tqs_hero_slider_settings', __( 'Knop 1 tekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_btn1_url', '/contact', 'tqs_hero_slider_settings', __( 'Knop 1 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_setting( $wp_customize, 'tqs_hero_btn2_text', 'Onze Diensten →', 'tqs_hero_slider_settings', __( 'Knop 2 tekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hero_btn2_url', '/onze-diensten', 'tqs_hero_slider_settings', __( 'Knop 2 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );

	$slide_defaults = tqs_get_default_hero_slides();
	foreach ( $slide_defaults as $i => $slide ) {
		$n = $i + 1;
		$wp_customize->add_section( "tqs_hero_slide_{$i}", array(
			'title' => sprintf( __( 'Hero Slide %d', 'tqs-theme' ), $n ),
			'panel' => 'tqs_theme_settings',
		) );
		tqs_add_checkbox( $wp_customize, "tqs_slide_{$i}_enabled", true, "tqs_hero_slide_{$i}", __( 'Slide inschakelen', 'tqs-theme' ) );
		tqs_add_image_setting( $wp_customize, "tqs_slide_{$i}_image", "tqs_hero_slide_{$i}", __( 'Achtergrondafbeelding (optioneel)', 'tqs-theme' ) );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_gradient", $slide['gradient'], "tqs_hero_slide_{$i}", __( 'Gradient fallback (CSS)', 'tqs-theme' ), 'textarea', 'sanitize_text_field' );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_icon", $slide['icon'], "tqs_hero_slide_{$i}", __( 'Icoon / emoji', 'tqs-theme' ) );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_badge", $slide['badge'], "tqs_hero_slide_{$i}", __( 'Badge tekst', 'tqs-theme' ) );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_title", $slide['title'], "tqs_hero_slide_{$i}", __( 'Titel', 'tqs-theme' ) );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_highlight", $slide['highlight'], "tqs_hero_slide_{$i}", __( 'Titel highlight (goud)', 'tqs-theme' ) );
		tqs_add_setting( $wp_customize, "tqs_slide_{$i}_subtitle", $slide['subtitle'], "tqs_hero_slide_{$i}", __( 'Subtitel', 'tqs-theme' ), 'textarea' );
	}

	// Legacy fallback hero image (inner pages).
	$wp_customize->add_section( 'tqs_hero_settings', array(
		'title' => __( 'Default Hero Image', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
		'description' => __( 'Standaard hero-achtergrond voor binnenpagina\'s wanneer geen pagina-specifieke afbeelding is ingesteld.', 'tqs-theme' ),
	) );
	tqs_add_image_setting( $wp_customize, 'tqs_hero_image', 'tqs_hero_settings', __( 'Standaard hero achtergrondafbeelding', 'tqs-theme' ) );

	/* --- Stats Bar --- */
	$wp_customize->add_section( 'tqs_stats_settings', array(
		'title' => __( 'Homepage — Stats Bar', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_stats', true, 'tqs_stats_settings', __( 'Toon stats bar', 'tqs-theme' ) );
	$default_stats = array(
		array( 'value' => '10+',  'label' => 'Jaar Ervaring' ),
		array( 'value' => '500+', 'label' => 'Tevreden Klanten' ),
		array( 'value' => '24/7', 'label' => 'Beschikbaar' ),
		array( 'value' => '7',    'label' => 'Sectoren' ),
	);
	foreach ( $default_stats as $i => $stat ) {
		tqs_add_setting( $wp_customize, "tqs_stat_{$i}_value", $stat['value'], 'tqs_stats_settings', sprintf( __( 'Statistiek %d — Waarde', 'tqs-theme' ), $i + 1 ) );
		tqs_add_setting( $wp_customize, "tqs_stat_{$i}_label", $stat['label'], 'tqs_stats_settings', sprintf( __( 'Statistiek %d — Label', 'tqs-theme' ), $i + 1 ) );
	}

	/* --- Services Section --- */
	$wp_customize->add_section( 'tqs_services_section', array(
		'title' => __( 'Homepage — Services Section', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_services', true, 'tqs_services_section', __( 'Toon diensten sectie', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_eyebrow', 'ONZE DIENSTEN', 'tqs_services_section', __( 'Eyebrow tekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_title', 'Beveiligingsoplossingen op maat', 'tqs_services_section', __( 'Sectietitel', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_services_lead', 'Voor elke sector een passende aanpak — professioneel, gecertificeerd en altijd paraat.', 'tqs_services_section', __( 'Intro tekst', 'tqs-theme' ), 'textarea' );

	/* --- Why Us --- */
	$wp_customize->add_section( 'tqs_why_us_section', array(
		'title' => __( 'Homepage — Why Us', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_why_us', true, 'tqs_why_us_section', __( 'Toon Why Us sectie', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_eyebrow', 'WAAROM TQS', 'tqs_why_us_section', __( 'Eyebrow tekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_title', 'Betrouwbaarheid die u kunt zien', 'tqs_why_us_section', __( 'Sectietitel', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_why_text', 'Al meer dan tien jaar biedt TQS professionele beveiliging aan bedrijven, evenementen en instellingen door heel Nederland. Onze medewerkers zijn opgeleid, gecertificeerd en altijd representatief.', 'tqs_why_us_section', __( 'Intro tekst', 'tqs-theme' ), 'textarea' );
	$why_defaults = tqs_get_default_why_us_cards();
	foreach ( $why_defaults as $i => $card ) {
		$n = $i + 1;
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_icon", $card['icon'], 'tqs_why_us_section', sprintf( __( 'Kaart %d — Font Awesome icoon', 'tqs-theme' ), $n ) );
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_title", $card['title'], 'tqs_why_us_section', sprintf( __( 'Kaart %d — Titel', 'tqs-theme' ), $n ) );
		tqs_add_setting( $wp_customize, "tqs_why_{$i}_desc", $card['desc'], 'tqs_why_us_section', sprintf( __( 'Kaart %d — Beschrijving', 'tqs-theme' ), $n ), 'textarea' );
	}

	/* --- CTA Banner --- */
	$wp_customize->add_section( 'tqs_cta_section', array(
		'title' => __( 'Homepage — CTA Banner', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_cta', true, 'tqs_cta_section', __( 'Toon CTA banner', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_title', 'Klaar voor professionele beveiliging?', 'tqs_cta_section', __( 'Titel', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_text', 'Vraag vandaag nog een vrijblijvende offerte aan en ontdek wat TQS voor u kan betekenen.', 'tqs_cta_section', __( 'Tekst', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn1_text', 'Offerte Aanvragen', 'tqs_cta_section', __( 'Knop 1 tekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn1_url', '/contact', 'tqs_cta_section', __( 'Knop 1 URL', 'tqs-theme' ), 'text', 'tqs_sanitize_url_or_path' );
	tqs_add_setting( $wp_customize, 'tqs_cta_btn2_text', 'Bel Ons Direct', 'tqs_cta_section', __( 'Knop 2 tekst', 'tqs-theme' ) );

	/* --- Company Information --- */
	$wp_customize->add_section( 'tqs_company_settings', array(
		'title' => __( 'Company Information', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_kvk', '', 'tqs_company_settings', __( 'KvK nummer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_btw', '', 'tqs_company_settings', __( 'BTW nummer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_nd_cert', 'ND 7099', 'tqs_company_settings', __( 'ND certificeringsnummer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_address', 'Spui 70, 2511 BT Den Haag', 'tqs_company_settings', __( 'Adres', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_hours', "Ma - Vr: 09:00 - 18:00\nWeekend: Op afspraak", 'tqs_company_settings', __( 'Openingstijden', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_whatsapp_number', '31636286183', 'tqs_company_settings', __( 'WhatsApp nummer (met landcode)', 'tqs-theme' ) );

	/* --- Contact & Forms --- */
	$wp_customize->add_section( 'tqs_contact_settings', array(
		'title' => __( 'Contact & Forms', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_contact_recipient', '', 'tqs_contact_settings', __( 'Formulier ontvanger (leeg = e-mail header)', 'tqs-theme' ), 'email', 'sanitize_email' );
	tqs_add_setting( $wp_customize, 'tqs_form_success_msg', 'Bedankt voor uw bericht! Wij nemen zo spoedig mogelijk contact met u op.', 'tqs_contact_settings', __( 'Succesmelding formulier', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_form_error_msg', 'Er ging iets mis bij het verzenden. Probeer het later opnieuw of bel ons direct.', 'tqs_contact_settings', __( 'Foutmelding formulier', 'tqs-theme' ), 'textarea' );
	tqs_add_checkbox( $wp_customize, 'tqs_show_contact_map', true, 'tqs_contact_settings', __( 'Toon kaart op contactpagina', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_maps_embed_url', '', 'tqs_contact_settings', __( 'Google Maps embed URL (optioneel)', 'tqs-theme' ), 'url', 'esc_url_raw' );

	/* --- Footer --- */
	$wp_customize->add_section( 'tqs_footer_settings', array(
		'title' => __( 'Footer Settings', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_footer_tagline', 'Professionele beveiligingsdiensten vanuit Den Haag, actief door heel Nederland. Gecertificeerd, betrouwbaar en 24/7 paraat.', 'tqs_footer_settings', __( 'Footer tagline', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_copyright', '© {year} Top Quality Security. Alle rechten voorbehouden.', 'tqs_footer_settings', __( 'Copyright tekst ({year} = huidig jaar)', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_social', true, 'tqs_footer_settings', __( 'Toon social media iconen', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_kvk', false, 'tqs_footer_settings', __( 'Toon KvK in footer', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_btw', false, 'tqs_footer_settings', __( 'Toon BTW in footer', 'tqs-theme' ) );
	tqs_add_checkbox( $wp_customize, 'tqs_show_footer_nd', true, 'tqs_footer_settings', __( 'Toon ND certificaat in footer', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_quick', 'SNELLE LINKS', 'tqs_footer_settings', __( 'Kolomtitel — Snelle links', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_services', 'DIENSTEN', 'tqs_footer_settings', __( 'Kolomtitel — Diensten', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_footer_col_contact', 'CONTACT', 'tqs_footer_settings', __( 'Kolomtitel — Contact', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_facebook_url', '', 'tqs_footer_settings', __( 'Facebook URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_instagram_url', '', 'tqs_footer_settings', __( 'Instagram URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_linkedin_url', '', 'tqs_footer_settings', __( 'LinkedIn URL', 'tqs-theme' ), 'url', 'esc_url_raw' );
	tqs_add_setting( $wp_customize, 'tqs_x_url', '', 'tqs_footer_settings', __( 'X (Twitter) URL', 'tqs-theme' ), 'url', 'esc_url_raw' );

	/* --- SEO & Analytics --- */
	$wp_customize->add_section( 'tqs_seo_settings', array(
		'title' => __( 'SEO & Analytics', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_home_meta_description', 'Top Quality Security levert professionele beveiligingsdiensten door heel Nederland, vanuit Den Haag. ND 7099 gecertificeerd, betrouwbaar en 24/7 beschikbaar.', 'tqs_seo_settings', __( 'Homepage meta description', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_ga_id', '', 'tqs_seo_settings', __( 'Google Analytics 4 ID (bv. G-XXXXXXXX)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_gtm_id', '', 'tqs_seo_settings', __( 'Google Tag Manager ID (bv. GTM-XXXXXXX)', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_archive_services_title', 'Onze Diensten', 'tqs_seo_settings', __( 'Diensten archief — titel', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_archive_services_lead', 'Van winkelvloer tot evenemententerrein — TQS levert beveiligingsoplossingen op maat voor iedere sector, met gecertificeerd en professioneel personeel.', 'tqs_seo_settings', __( 'Diensten archief — intro', 'tqs-theme' ), 'textarea' );

	/* --- 404 Page --- */
	$wp_customize->add_section( 'tqs_404_settings', array(
		'title' => __( '404 Page', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_setting( $wp_customize, 'tqs_404_title', 'Pagina Niet Gevonden', 'tqs_404_settings', __( 'Titel', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_404_message', 'De pagina die u zoekt bestaat niet (meer) of is verplaatst.', 'tqs_404_settings', __( 'Bericht', 'tqs-theme' ), 'textarea' );
	tqs_add_setting( $wp_customize, 'tqs_404_btn_text', 'Terug Naar Home', 'tqs_404_settings', __( 'Knoptekst', 'tqs-theme' ) );

	/* --- GDPR Cookie Banner --- */
	$wp_customize->add_section( 'tqs_cookie_settings', array(
		'title' => __( 'GDPR Cookie Banner', 'tqs-theme' ),
		'panel' => 'tqs_theme_settings',
	) );
	tqs_add_checkbox( $wp_customize, 'tqs_cookie_enabled', true, 'tqs_cookie_settings', __( 'Cookiebanner inschakelen', 'tqs-theme' ) );
	$wp_customize->add_setting( 'tqs_cookie_text', array(
		'default'           => 'Wij gebruiken cookies om uw ervaring te verbeteren. Lees ons <a href="/privacybeleid">privacybeleid</a> voor meer informatie.',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( 'tqs_cookie_text', array(
		'label'   => __( 'Cookiebanner tekst (HTML toegestaan)', 'tqs-theme' ),
		'section' => 'tqs_cookie_settings',
		'type'    => 'textarea',
	) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_accept_text', 'Accepteren', 'tqs_cookie_settings', __( 'Accepteer knoptekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_decline_text', 'Weigeren', 'tqs_cookie_settings', __( 'Weiger knoptekst', 'tqs-theme' ) );
	tqs_add_setting( $wp_customize, 'tqs_cookie_expiry_days', '180', 'tqs_cookie_settings', __( 'Cookie geldigheid (dagen)', 'tqs-theme' ), 'number', 'absint' );
}
add_action( 'customize_register', 'tqs_customize_register' );
