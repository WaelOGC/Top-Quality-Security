<?php
/**
 * TQS Theme functions and definitions
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'TQS_THEME_VERSION', '2.0.1' );

require get_template_directory() . '/inc/theme-options.php';
require get_template_directory() . '/inc/customizer.php';

/* ==========================================================================
   1. THEME SETUP
   ========================================================================== */
function tqs_theme_setup() {
	load_theme_textdomain( 'tqs-theme', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'custom-logo', array(
		'height'               => 120,
		'width'                => 400,
		'flex-height'          => true,
		'flex-width'           => true,
		'unlink-homepage-logo' => true,
	) );

	// Elementor compatibility.
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-header-footer' );

	add_image_size( 'tqs-hero', 1600, 700, true );
	add_image_size( 'tqs-card', 700, 500, true );
	add_image_size( 'tqs-gallery', 600, 600, true );

	register_nav_menus( array(
		'primary'        => __( 'Primary Navigation', 'tqs-theme' ),
		'footer-quick'   => __( 'Footer — Quick Links', 'tqs-theme' ),
		'footer-services'=> __( 'Footer — Services', 'tqs-theme' ),
		'footer-legal'   => __( 'Footer — Legal', 'tqs-theme' ),
	) );
}
add_action( 'after_setup_theme', 'tqs_theme_setup' );

/* ==========================================================================
   2. ENQUEUE STYLES & SCRIPTS
   ========================================================================== */
function tqs_theme_enqueue_assets() {
	wp_enqueue_style( 'tqs-google-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'tqs-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );
	wp_enqueue_style( 'tqs-theme-style', get_stylesheet_uri(), array(), TQS_THEME_VERSION );

	wp_enqueue_script( 'tqs-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), TQS_THEME_VERSION, true );

	wp_localize_script( 'tqs-main', 'tqsData', array(
		'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
		'nonce'              => wp_create_nonce( 'tqs_contact_nonce' ),
		'reviewNonce'        => wp_create_nonce( 'tqs_review_nonce' ),
		'whatsapp'           => preg_replace( '/[^0-9]/', '', get_theme_mod( 'tqs_whatsapp_number', '31636286183' ) ),
		'cookieExpiry'       => absint( get_theme_mod( 'tqs_cookie_expiry_days', 180 ) ),
		'heroAutoplay'       => (bool) get_theme_mod( 'tqs_hero_autoplay', true ),
		'heroAutoplayMs'     => absint( get_theme_mod( 'tqs_hero_autoplay_interval', 5500 ) ),
		'formErrorMsg'       => get_theme_mod( 'tqs_form_error_msg', __( 'Er ging iets mis. Probeer het later opnieuw of bel ons direct.', 'tqs-theme' ) ),
		'reviewSuccessMsg'   => __( 'Bedankt voor je beoordeling! Deze wordt binnenkort gecontroleerd en gepubliceerd.', 'tqs-theme' ),
		'reviewErrorMsg'     => __( 'Er ging iets mis. Probeer het later opnieuw.', 'tqs-theme' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'tqs_theme_enqueue_assets' );

// Performance: remove emoji scripts + generator tag.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );

/* ==========================================================================
   3. CUSTOM POST TYPE: tqs_service
   ========================================================================== */
function tqs_register_service_cpt() {
	$labels = array(
		'name'          => __( 'Diensten', 'tqs-theme' ),
		'singular_name' => __( 'Dienst', 'tqs-theme' ),
		'add_new_item'  => __( 'Nieuwe Dienst Toevoegen', 'tqs-theme' ),
		'edit_item'     => __( 'Dienst Bewerken', 'tqs-theme' ),
		'all_items'     => __( 'Alle Diensten', 'tqs-theme' ),
	);

	register_post_type( 'tqs_service', array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => 'onze-diensten',
		'rewrite'       => array( 'slug' => 'onze-diensten', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-shield',
		'menu_position' => 5,
	) );
}
add_action( 'init', 'tqs_register_service_cpt' );

/* ==========================================================================
   3b. CUSTOM POST TYPE: tqs_review
   ========================================================================== */
function tqs_register_review_cpt() {
	$labels = array(
		'name'                  => __( 'Beoordelingen', 'tqs-theme' ),
		'singular_name'         => __( 'Beoordeling', 'tqs-theme' ),
		'add_new'               => __( 'Beoordeling toevoegen', 'tqs-theme' ),
		'add_new_item'          => __( 'Beoordeling toevoegen', 'tqs-theme' ),
		'edit_item'             => __( 'Beoordeling bewerken', 'tqs-theme' ),
		'new_item'              => __( 'Nieuwe beoordeling', 'tqs-theme' ),
		'view_item'             => __( 'Beoordeling bekijken', 'tqs-theme' ),
		'search_items'          => __( 'Beoordelingen zoeken', 'tqs-theme' ),
		'not_found'             => __( 'Geen beoordelingen gevonden', 'tqs-theme' ),
		'not_found_in_trash'    => __( 'Geen beoordelingen in prullenbak', 'tqs-theme' ),
		'all_items'             => __( 'Alle beoordelingen', 'tqs-theme' ),
		'menu_name'             => __( 'Beoordelingen', 'tqs-theme' ),
	);

	register_post_type( 'tqs_review', array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title' ),
		'menu_icon'           => 'dashicons-star-filled',
		'menu_position'       => 6,
	) );
}
add_action( 'init', 'tqs_register_review_cpt' );

/**
 * Insert a review from a front-end submission (Phase 2).
 * Always creates posts with status "pending" for admin approval.
 *
 * @param array $postarr Post data for wp_insert_post (post_title = reviewer name).
 * @param array $meta    Review meta: rating, service_id, email, consent, text.
 * @return int|WP_Error Post ID on success.
 */
function tqs_insert_review_from_submission( $postarr = array(), $meta = array() ) {
	$postarr = wp_parse_args( $postarr, array(
		'post_type'   => 'tqs_review',
		'post_status' => 'pending',
	) );

	if ( 'tqs_review' !== $postarr['post_type'] ) {
		$postarr['post_type'] = 'tqs_review';
	}

	$postarr['post_status'] = 'pending';

	$post_id = wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	if ( $meta ) {
		tqs_save_review_meta_fields( $post_id, $meta );
	}

	return $post_id;
}

function tqs_get_review_status_label( $status ) {
	$labels = array(
		'publish' => __( 'Gepubliceerd', 'tqs-theme' ),
		'pending' => __( 'In afwachting', 'tqs-theme' ),
		'draft'   => __( 'Concept', 'tqs-theme' ),
		'private' => __( 'Privé', 'tqs-theme' ),
		'trash'   => __( 'Prullenbak', 'tqs-theme' ),
	);

	return isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
}

function tqs_render_review_stars( $rating ) {
	$rating = absint( $rating );
	if ( $rating < 1 || $rating > 5 ) {
		return '—';
	}

	return str_repeat( '★', $rating );
}

function tqs_add_review_meta_box() {
	add_meta_box(
		'tqs_review_details_box',
		__( 'Beoordeling Details', 'tqs-theme' ),
		'tqs_render_review_meta_box',
		'tqs_review',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tqs_add_review_meta_box' );

function tqs_render_review_meta_box( $post ) {
	wp_nonce_field( 'tqs_save_review_meta', 'tqs_review_meta_nonce' );

	$rating     = absint( get_post_meta( $post->ID, '_tqs_review_rating', true ) );
	$service_id = absint( get_post_meta( $post->ID, '_tqs_review_service_id', true ) );
	$email      = get_post_meta( $post->ID, '_tqs_review_email', true );
	$consent    = (bool) get_post_meta( $post->ID, '_tqs_review_consent', true );
	$text       = get_post_meta( $post->ID, '_tqs_review_text', true );
	$services   = tqs_get_services();

	echo '<table class="form-table" role="presentation">';

	echo '<tr><th scope="row"><label for="tqs_review_text">' . esc_html__( 'Beoordelingstekst', 'tqs-theme' ) . '</label></th>';
	echo '<td><textarea id="tqs_review_text" name="tqs_review_text" class="large-text" rows="5">' . esc_textarea( $text ) . '</textarea>';
	echo '<p class="description">' . esc_html__( 'De tekst van de klantbeoordeling (niet via de hoofd-editor).', 'tqs-theme' ) . '</p></td></tr>';

	echo '<tr><th scope="row">' . esc_html__( 'Sterrenbeoordeling', 'tqs-theme' ) . '</th><td>';
	for ( $i = 5; $i >= 1; $i-- ) {
		echo '<label style="margin-right:12px;">';
		echo '<input type="radio" name="tqs_review_rating" value="' . esc_attr( $i ) . '" ' . checked( $rating, $i, false ) . '> ';
		echo esc_html( str_repeat( '★', $i ) );
		echo '</label><br>';
	}
	echo '</td></tr>';

	echo '<tr><th scope="row"><label for="tqs_review_service_id">' . esc_html__( 'Gekoppelde dienst', 'tqs-theme' ) . '</label></th><td>';
	echo '<select id="tqs_review_service_id" name="tqs_review_service_id">';
	echo '<option value="0"' . selected( $service_id, 0, false ) . '>' . esc_html__( 'Algemeen', 'tqs-theme' ) . '</option>';
	foreach ( $services as $service ) {
		echo '<option value="' . esc_attr( $service->ID ) . '"' . selected( $service_id, $service->ID, false ) . '>' . esc_html( $service->post_title ) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><th scope="row"><label for="tqs_review_email">' . esc_html__( 'E-mail reviewer', 'tqs-theme' ) . '</label></th>';
	echo '<td><input type="email" id="tqs_review_email" name="tqs_review_email" class="regular-text" value="' . esc_attr( $email ) . '">';
	echo '<p class="description">' . esc_html__( 'Alleen zichtbaar in wp-admin; wordt nooit op de website getoond.', 'tqs-theme' ) . '</p></td></tr>';

	echo '<tr><th scope="row">' . esc_html__( 'AVG-toestemming', 'tqs-theme' ) . '</th><td>';
	if ( $consent ) {
		echo '<label><input type="checkbox" checked disabled> ' . esc_html__( 'Toestemming gegeven bij inzending', 'tqs-theme' ) . '</label>';
		echo '<input type="hidden" name="tqs_review_consent" value="1">';
	} else {
		echo '<label><input type="checkbox" name="tqs_review_consent" value="1"' . checked( $consent, true, false ) . '> ';
		echo esc_html__( 'Toestemming gegeven (handmatig instellen voor tests)', 'tqs-theme' ) . '</label>';
	}
	echo '</td></tr>';

	echo '</table>';
}

/**
 * Sanitize and persist review meta fields.
 *
 * @param int   $post_id Post ID.
 * @param array $data    Raw field values.
 */
function tqs_save_review_meta_fields( $post_id, $data ) {
	if ( isset( $data['text'] ) ) {
		update_post_meta( $post_id, '_tqs_review_text', sanitize_textarea_field( $data['text'] ) );
	}

	if ( isset( $data['rating'] ) ) {
		$rating = absint( $data['rating'] );
		if ( $rating >= 1 && $rating <= 5 ) {
			update_post_meta( $post_id, '_tqs_review_rating', $rating );
		}
	}

	if ( isset( $data['service_id'] ) ) {
		$service_id = absint( $data['service_id'] );
		if ( $service_id > 0 && 'tqs_service' !== get_post_type( $service_id ) ) {
			$service_id = 0;
		}
		update_post_meta( $post_id, '_tqs_review_service_id', $service_id );
	}

	if ( isset( $data['email'] ) ) {
		update_post_meta( $post_id, '_tqs_review_email', sanitize_email( $data['email'] ) );
	}

	$consent = ! empty( $data['consent'] );
	update_post_meta( $post_id, '_tqs_review_consent', $consent ? '1' : '' );
}

function tqs_save_review_meta( $post_id ) {
	if ( ! isset( $_POST['tqs_review_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tqs_review_meta_nonce'] ) ), 'tqs_save_review_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$rating = isset( $_POST['tqs_review_rating'] ) ? absint( $_POST['tqs_review_rating'] ) : 0;

	tqs_save_review_meta_fields( $post_id, array(
		'text'       => isset( $_POST['tqs_review_text'] ) ? wp_unslash( $_POST['tqs_review_text'] ) : '',
		'rating'     => $rating,
		'service_id' => isset( $_POST['tqs_review_service_id'] ) ? absint( $_POST['tqs_review_service_id'] ) : 0,
		'email'      => isset( $_POST['tqs_review_email'] ) ? wp_unslash( $_POST['tqs_review_email'] ) : '',
		'consent'    => isset( $_POST['tqs_review_consent'] ),
	) );
}
add_action( 'save_post_tqs_review', 'tqs_save_review_meta' );

function tqs_review_admin_columns( $columns ) {
	$new = array();
	if ( isset( $columns['cb'] ) ) {
		$new['cb'] = $columns['cb'];
	}
	$new['title']              = __( 'Naam', 'tqs-theme' );
	$new['tqs_review_rating']  = __( 'Rating', 'tqs-theme' );
	$new['tqs_review_service'] = __( 'Gekoppelde Dienst', 'tqs-theme' );
	$new['tqs_review_status']  = __( 'Status', 'tqs-theme' );
	$new['date']               = __( 'Datum', 'tqs-theme' );
	return $new;
}
add_filter( 'manage_tqs_review_posts_columns', 'tqs_review_admin_columns' );

function tqs_review_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'tqs_review_rating':
			echo esc_html( tqs_render_review_stars( get_post_meta( $post_id, '_tqs_review_rating', true ) ) );
			break;

		case 'tqs_review_service':
			$service_id = absint( get_post_meta( $post_id, '_tqs_review_service_id', true ) );
			if ( 0 === $service_id ) {
				echo esc_html__( 'Algemeen', 'tqs-theme' );
			} elseif ( get_post_type( $service_id ) === 'tqs_service' ) {
				echo esc_html( get_the_title( $service_id ) );
			} else {
				echo '—';
			}
			break;

		case 'tqs_review_status':
			echo esc_html( tqs_get_review_status_label( get_post_status( $post_id ) ) );
			break;
	}
}
add_action( 'manage_tqs_review_posts_custom_column', 'tqs_review_admin_column_content', 10, 2 );

/* ==========================================================================
   4. CONTENT SEEDING (Services + Pages)
   ========================================================================== */
function tqs_get_seed_services() {
	return array(
		array(
			'slug'     => 'retailbeveiliging',
			'title'    => 'Retailbeveiliging',
			'icon'     => 'fa-store',
			'excerpt'  => 'Preventie van diefstal en overlast in winkels, met een gastvrije en alerte uitstraling.',
			'content'  => "Winkeldiefstal en overlast kosten de Nederlandse detailhandel jaarlijks miljoenen euro's. TQS levert getrainde, representatieve beveiligers die deze risico's verkleinen zonder de winkelbeleving van uw klanten te verstoren. Onze aanpak combineert zichtbare alertheid met een gastvrije, benaderbare houding.\n\n<h2>Wat Wij Bieden</h2>\nOnze retailbeveiligers houden toezicht op winkelvloer, paskamers en in- en uitgangen, en werken nauw samen met uw winkelpersoneel. Zij herkennen verdacht gedrag vroegtijdig, treden op bij winkeldiefstal en zorgen voor een veilige afhandeling van incidenten — altijd binnen de grenzen van de wet.\n\nDeze dienst is geschikt voor modewinkels, elektronicazaken, supermarkten, warenhuizen en winkelcentra — zowel voor structurele inzet als voor piekmomenten zoals koopavonden, sale-periodes en feestdagen.\n\n<h2>Waarom TQS Kiezen</h2>\nTQS is ND 7099 gecertificeerd, de kwaliteitsnorm voor particuliere beveiligingsorganisaties in Nederland. Al onze beveiligers zijn opgeleid, gescreend en representatief. Wij stemmen de inzet af op uw specifieke winkelformule en leveren rapportages die u helpen risico's structureel te verminderen.\n\nMet meer dan tien jaar ervaring in de retailsector begrijpen wij de balans tussen beveiliging en klantbeleving — en leveren wij een dienst waar zowel uw personeel als uw klanten baat bij hebben.",
		),
		array(
			'slug'    => 'horecabeveiliging',
			'title'   => 'Horecabeveiliging',
			'icon'    => 'fa-martini-glass',
			'excerpt' => 'Professionele beveiliging voor bars, clubs en restaurants — rustig optreden, sterk in de-escalatie.',
			'content' => "Bars, clubs en restaurants vragen om beveiligers die de sfeer bewaken zonder gasten af te schrikken. TQS levert horecabeveiligers die rustig optreden, sterk zijn in de-escalatie en precies weten wanneer wél of niet in te grijpen.\n\n<h2>Wat Wij Bieden</h2>\nOnze beveiligers controleren de toegang, houden toezicht op de zaal en grijpen professioneel in bij incidenten of overlast. Zij werken nauw samen met uw personeel en zorgen voor een veilige, gastvrije avond voor iedereen.\n\nGeschikt voor cafés, clubs, restaurants en feestlocaties — zowel structureel in het weekend als voor eenmalige evenementen en feestdagen.\n\n<h2>Waarom TQS Kiezen</h2>\nOnze horecabeveiligers zijn ND 7099 gecertificeerd, ervaren in conflicthantering en getraind om escalatie te voorkomen in plaats van op te zoeken. Representatief, benaderbaar en altijd alert.\n\nWij leveren maatwerk per locatie: van één beveiliger aan de deur tot een volledig team voor grotere evenementenlocaties.",
		),
		array(
			'slug'    => 'evenementenbeveiliging',
			'title'   => "Evenementenbeveiliging",
			'icon'    => 'fa-tent',
			'excerpt' => 'Van festival tot bedrijfsevenement: veiligheid en toegangscontrole voor elk publiek.',
			'content' => "Van festival tot bedrijfsevenement: TQS verzorgt toegangscontrole, crowd management en incidentbeheersing voor evenementen van elke omvang. Onze teams schalen mee met het risicoprofiel en de grootte van uw evenement.\n\n<h2>Wat Wij Bieden</h2>\nWij regelen toegangscontrole en ticketcheck, crowd management op drukke momenten, en snelle, professionele incidentafhandeling. Onze beveiligers werken nauw samen met organisatoren, EHBO en indien nodig de politie.\n\nGeschikt voor festivals, concerten, beurzen, bedrijfsevenementen en sportevenementen.\n\n<h2>Waarom TQS Kiezen</h2>\nTQS is ND 7099 gecertificeerd en heeft ruime ervaring met evenementen van uiteenlopende schaal. Wij stellen samen met u een veiligheidsplan op, afgestemd op het risicoprofiel en de verwachte bezoekersaantallen van uw evenement.",
		),
		array(
			'slug'    => 'hotelbeveiliging',
			'title'   => 'Hotelbeveiliging',
			'icon'    => 'fa-hotel',
			'excerpt' => 'Discrete, representatieve beveiliging die de gastervaring van uw hotel versterkt.',
			'content' => "Discrete, representatieve beveiligers die de gastervaring van uw hotel versterken in plaats van verstoren. TQS bewaakt lobby, parkeergelegenheid en gangen zonder afbreuk te doen aan de gastvrijheid.\n\n<h2>Wat Wij Bieden</h2>\nOnze beveiligers houden toezicht op de lobby, entree en gangen, controleren toegang tot personeels- en opslagruimtes, en ondersteunen de front office bij incidenten of overlast van gasten of bezoekers.\n\nGeschikt voor hotels, resorts en accommodaties van elke omvang — zowel 24/7 als voor specifieke diensten zoals nachtsurveillance.\n\n<h2>Waarom TQS Kiezen</h2>\nOnze hotelbeveiligers zijn geselecteerd op representativiteit en klantgerichtheid, naast hun beveiligingsexpertise. Wij begrijpen dat gastbeleving voorop staat, en leveren beveiliging die dat versterkt in plaats van ondermijnt.",
		),
		array(
			'slug'    => 'objectbeveiliging',
			'title'   => 'Objectbeveiliging',
			'icon'    => 'fa-building',
			'excerpt' => 'Continue bewaking van panden en terreinen, met surveillance en toegangsbeheer.',
			'content' => "Wij bieden continue bewaking van bedrijfspanden, terreinen en gebouwen, met surveillance, toegangsbeheer en alarmopvolging. Ideaal voor kantoren, magazijnen en industriële locaties.\n\n<h2>Wat Wij Bieden</h2>\nOnze objectbeveiligers voeren surveillancerondes uit, beheren toegang voor personeel en bezoekers, en volgen alarmmeldingen direct op. Wij werken met vaste of flexibele diensten, afgestemd op uw risicoprofiel.\n\nGeschikt voor kantoorpanden, bedrijventerreinen, magazijnen, bouwplaatsen en industriële locaties.\n\n<h2>Waarom TQS Kiezen</h2>\nTQS is ND 7099 gecertificeerd en levert objectbeveiliging met heldere rapportages en vaste aanspreekpunten. Wij denken mee over uw totale beveiligingsplan, inclusief camerasystemen en toegangscontrole.",
		),
		array(
			'slug'    => 'supermarktbeveiliging',
			'title'   => 'Supermarktbeveiliging',
			'icon'    => 'fa-cart-shopping',
			'excerpt' => 'Zichtbare aanwezigheid ter voorkoming van winkeldiefstal en agressie richting personeel.',
			'content' => "Onze beveiligers zijn zichtbaar aanwezig om winkeldiefstal te voorkomen en agressie richting personeel tegen te gaan. Zij ondersteunen het winkelteam en bewaken de veiligheid van klanten en medewerkers.\n\n<h2>Wat Wij Bieden</h2>\nOnze supermarktbeveiligers surveilleren op de winkelvloer, ondersteunen bij verdenking van diefstal en treden kalm en professioneel op bij agressie richting personeel. Zij werken nauw samen met de winkelleiding.\n\nGeschikt voor supermarkten en winkelketens van elke omvang, structureel of op piekmomenten zoals feestdagen.\n\n<h2>Waarom TQS Kiezen</h2>\nWij begrijpen de specifieke druk in de supermarktbranche — van winkeldiefstal tot agressie aan de kassa. Onze beveiligers zijn getraind om zowel preventief als reactief op te treden, met respect voor klanten en personeel.",
		),
		array(
			'slug'    => 'casinobeveiliging',
			'title'   => "Casino's Beveiliging",
			'icon'    => 'fa-dice',
			'excerpt' => "Gespecialiseerde beveiliging voor casino's, met oog voor discretie en snelle interventie.",
			'content' => "Gespecialiseerde beveiliging voor casino's, met oog voor discretie, snelle interventie en nauwe samenwerking met surveillanceteams. Onze mensen zijn getraind in de specifieke risico's van de kansspelbranche.\n\n<h2>Wat Wij Bieden</h2>\nOnze casinobeveiligers werken nauw samen met interne surveillance en toezichthouders, houden toezicht op de speelvloer en treden discreet maar doortastend op bij incidenten.\n\nGeschikt voor casino's en kansspellocaties die hoge eisen stellen aan discretie, alertheid en snelle interventie.\n\n<h2>Waarom TQS Kiezen</h2>\nTQS is ND 7099 gecertificeerd en levert beveiligers met specifieke kennis van de kansspelbranche. Wij begrijpen de balans tussen een uitnodigende sfeer voor gasten en strikte veiligheidsprotocollen.",
		),
	);
}

function tqs_seed_services() {
	if ( get_option( 'tqs_services_seeded_v2' ) ) {
		return;
	}
	foreach ( tqs_get_seed_services() as $svc ) {
		if ( get_page_by_path( $svc['slug'], OBJECT, 'tqs_service' ) ) {
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_type'    => 'tqs_service',
			'post_title'   => $svc['title'],
			'post_name'    => $svc['slug'],
			'post_content' => wpautop( $svc['content'] ),
			'post_excerpt' => $svc['excerpt'],
			'post_status'  => 'publish',
		) );
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_tqs_service_icon', $svc['icon'] );
		}
	}
	update_option( 'tqs_services_seeded_v2', 1 );
}

function tqs_get_seed_pages() {
	$wie_zijn_wij_content = "<p>Top Quality Security is opgericht in Den Haag met een duidelijke missie: beveiliging leveren die net zo professioneel oogt als hij daadwerkelijk is. Wat begon als een lokale dienstverlener is uitgegroeid tot een landelijk actieve partner voor bedrijven in retail, horeca, evenementen en meer.</p>\n<p>Vandaag de dag is TQS actief in zeven sectoren door heel Nederland, met een team van opgeleide en gescreende beveiligers die dagelijks het verschil maken voor onze opdrachtgevers.</p>\n<p>Wij zijn ND 7099 gecertificeerd — de kwaliteitsnorm voor particuliere beveiligingsorganisaties in Nederland — en investeren continu in de opleiding en screening van ons personeel.</p>";

	$privacybeleid_content = "<h2>Inleiding</h2>\n<p>Top Quality Security (\"TQS\", \"wij\", \"ons\"), gevestigd te Den Haag, hecht grote waarde aan de bescherming van uw persoonsgegevens. In dit privacybeleid leggen wij uit welke gegevens wij verzamelen wanneer u onze website bezoekt of gebruikmaakt van onze diensten, waarom wij dat doen en welke rechten u heeft op grond van de Algemene Verordening Gegevensbescherming (AVG).</p>\n<h2>Welke Gegevens Wij Verzamelen</h2>\n<p>Wanneer u een offerte aanvraagt, contact met ons opneemt of gebruikmaakt van onze website, kunnen wij de volgende gegevens verwerken:</p>\n<ul>\n<li>Naam en contactgegevens (e-mailadres, telefoonnummer)</li>\n<li>Bedrijfsgegevens, indien u namens een organisatie contact opneemt</li>\n<li>Inhoud van uw bericht of offerteaanvraag</li>\n<li>Technische gegevens over uw websitebezoek (zoals IP-adres en browsertype)</li>\n</ul>\n<h2>Waarom Wij Uw Gegevens Verzamelen</h2>\n<p>Wij gebruiken uw gegevens uitsluitend om contact met u op te nemen naar aanleiding van uw aanvraag, om offertes en overeenkomsten voor te bereiden, om onze dienstverlening uit te voeren en om te voldoen aan wettelijke verplichtingen. Wij verkopen uw gegevens nooit aan derden.</p>\n<h2>Hoe Wij Uw Gegevens Bewaren En Beveiligen</h2>\n<p>TQS neemt passende technische en organisatorische maatregelen om uw persoonsgegevens te beveiligen tegen verlies, misbruik of onbevoegde toegang. Wij bewaren uw gegevens niet langer dan noodzakelijk voor de doeleinden waarvoor ze zijn verzameld, of zolang de wet dit vereist.</p>\n<h2>Uw Rechten (AVG)</h2>\n<p>Op grond van de AVG heeft u het recht om:</p>\n<ul>\n<li>Inzage te vragen in de persoonsgegevens die wij van u verwerken</li>\n<li>Onjuiste gegevens te laten corrigeren</li>\n<li>Uw gegevens te laten verwijderen, voor zover wettelijk toegestaan</li>\n<li>Bezwaar te maken tegen de verwerking van uw gegevens</li>\n<li>Uw gegevens over te laten dragen aan een andere partij</li>\n</ul>\n<p>U kunt deze rechten uitoefenen door contact met ons op te nemen via de gegevens onderaan deze pagina. U heeft tevens het recht om een klacht in te dienen bij de Autoriteit Persoonsgegevens.</p>\n<h2>Cookies</h2>\n<p>Onze website maakt gebruik van functionele en analytische cookies om de website goed te laten functioneren en het gebruik ervan te analyseren. Deze cookies verzamelen geen gegevens die herleidbaar zijn tot individuele personen zonder uw toestemming. U kunt cookies via uw browserinstellingen weigeren of verwijderen.</p>\n<h2>Contact Voor Privacyvragen</h2>\n<p>Heeft u vragen over dit privacybeleid of over de verwerking van uw persoonsgegevens? Neem dan contact met ons op via <a href=\"mailto:info@topqualitysecurity.com\">info@topqualitysecurity.com</a> of telefonisch via +31 (0)70 123 4567.</p>";

	$voorwaarden_content = "<h2>Artikel 1 – Definities</h2>\n<p>In deze algemene voorwaarden wordt verstaan onder: \"TQS\" of \"opdrachtnemer\": Top Quality Security, gevestigd te Den Haag; \"opdrachtgever\": de natuurlijke of rechtspersoon die aan TQS een opdracht verstrekt; \"overeenkomst\": iedere overeenkomst tussen TQS en de opdrachtgever met betrekking tot de levering van beveiligingsdiensten.</p>\n<h2>Artikel 2 – Toepasselijkheid</h2>\n<p>Deze algemene voorwaarden zijn van toepassing op alle offertes, overeenkomsten en dienstverlening van TQS, tenzij schriftelijk anders is overeengekomen. Afwijkingen van deze voorwaarden zijn slechts geldig indien deze uitdrukkelijk en schriftelijk door TQS zijn bevestigd.</p>\n<h2>Artikel 3 – Offertes En Overeenkomsten</h2>\n<p>Alle offertes van TQS zijn vrijblijvend en geldig gedurende 30 dagen, tenzij anders vermeld. Een overeenkomst komt tot stand op het moment dat de opdrachtgever een offerte schriftelijk of per e-mail heeft geaccepteerd, of zodra TQS met de uitvoering van de dienstverlening is begonnen.</p>\n<h2>Artikel 4 – Uitvoering Van De Dienstverlening</h2>\n<p>TQS voert de overeengekomen dienstverlening naar beste inzicht en vermogen uit, met inachtneming van de geldende wet- en regelgeving, waaronder de ND 7099-norm. TQS heeft het recht om, in overleg met de opdrachtgever, personeel in te zetten dat voldoet aan de eisen van de betreffende opdracht.</p>\n<h2>Artikel 5 – Betaling</h2>\n<p>Facturen dienen binnen 14 dagen na factuurdatum te worden voldaan, tenzij schriftelijk anders overeengekomen. Bij overschrijding van de betalingstermijn is de opdrachtgever van rechtswege in verzuim en is TQS gerechtigd wettelijke rente en incassokosten in rekening te brengen.</p>\n<h2>Artikel 6 – Aansprakelijkheid</h2>\n<p>De aansprakelijkheid van TQS voor schade voortvloeiend uit de uitvoering van de overeenkomst is beperkt tot het bedrag dat in het desbetreffende geval door de aansprakelijkheidsverzekering van TQS wordt uitgekeerd. Indien de verzekeraar in enig geval niet tot uitkering overgaat, is de aansprakelijkheid beperkt tot het factuurbedrag van de betreffende opdracht.</p>\n<h2>Artikel 7 – Geschillen En Toepasselijk Recht</h2>\n<p>Op alle overeenkomsten tussen TQS en de opdrachtgever is uitsluitend Nederlands recht van toepassing. Geschillen die voortvloeien uit of verband houden met deze overeenkomst worden in eerste instantie voorgelegd aan de bevoegde rechter in het arrondissement Den Haag, tenzij partijen anders overeenkomen.</p>";

	return array(
		'wie-zijn-wij' => array(
			'title'   => 'Wie Zijn Wij',
			'content' => $wie_zijn_wij_content,
			'template'=> '',
		),
		'fotogalerij' => array(
			'title'   => 'Fotogalerij',
			'content' => '',
			'template'=> 'page-fotogalerij.php',
		),
		'contact' => array(
			'title'   => 'Contact',
			'content' => '',
			'template'=> 'page-contact.php',
		),
		'privacybeleid' => array(
			'title'   => 'Privacybeleid',
			'content' => $privacybeleid_content,
			'template'=> '',
		),
		'algemene-voorwaarden' => array(
			'title'   => 'Algemene Voorwaarden',
			'content' => $voorwaarden_content,
			'template'=> '',
		),
		'home' => array(
			'title'   => 'Home',
			'content' => '',
			'template'=> '',
		),
	);
}

function tqs_seed_pages() {
	if ( get_option( 'tqs_pages_seeded_v2' ) ) {
		return;
	}
	$created = array();
	foreach ( tqs_get_seed_pages() as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$created[ $slug ] = $existing->ID;
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_title'   => $page['title'],
			'post_name'    => $slug,
			'post_content' => $page['content'],
			'post_status'  => 'publish',
		) );
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			if ( ! empty( $page['template'] ) ) {
				update_post_meta( $post_id, '_wp_page_template', $page['template'] );
			}
			$created[ $slug ] = $post_id;
		}
	}

	// Set static front page.
	if ( ! empty( $created['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $created['home'] );
	}

	update_option( 'tqs_pages_seeded_v2', 1 );
}

function tqs_run_seeding() {
	tqs_seed_services();
	tqs_seed_pages();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tqs_run_seeding' );

// Development convenience: visit ?tqs_reseed=1 as admin to re-run seeding.
function tqs_maybe_reseed() {
	if ( isset( $_GET['tqs_reseed'] ) && current_user_can( 'manage_options' ) ) {
		delete_option( 'tqs_services_seeded_v2' );
		delete_option( 'tqs_pages_seeded_v2' );
		tqs_run_seeding();
		wp_safe_redirect( remove_query_arg( 'tqs_reseed' ) );
		exit;
	}
}
add_action( 'init', 'tqs_maybe_reseed' );

/* ==========================================================================
   TEMPORARY — Remove after review display is verified in production.
   Seeds 3 sample published tqs_review posts (admin visit, once only).
   ========================================================================== */
function tqs_seed_sample_reviews() {
	if ( get_option( 'tqs_reviews_display_seeded_v1' ) ) {
		return;
	}

	$services = tqs_get_services();
	if ( count( $services ) < 2 ) {
		return;
	}

	$samples = array(
		array(
			'title'      => 'Jan de Vries',
			'text'       => 'Zeer professionele beveiligers die discreet en alert aanwezig waren. Onze winkel voelt zich een stuk veiliger sinds de samenwerking met TQS.',
			'rating'     => 5,
			'service_id' => $services[0]->ID,
			'email'      => 'jan.devries@example.test',
		),
		array(
			'title'      => 'Maria Jansen',
			'text'       => 'Tijdens ons evenement was het team rustig, vriendelijk en doortastend. Alles verliep vlekkeloos — absoluut een aanrader.',
			'rating'     => 4,
			'service_id' => $services[1]->ID,
			'email'      => 'maria.jansen@example.test',
		),
		array(
			'title'      => 'Pieter Bakker',
			'text'       => 'Snelle reactie, duidelijke communicatie en representatief personeel. Wij zijn zeer tevreden over de algehele service van TQS.',
			'rating'     => 5,
			'service_id' => 0,
			'email'      => 'pieter.bakker@example.test',
		),
	);

	foreach ( $samples as $sample ) {
		$post_id = wp_insert_post( array(
			'post_type'   => 'tqs_review',
			'post_title'  => $sample['title'],
			'post_status' => 'publish',
		), true );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		tqs_save_review_meta_fields( $post_id, array(
			'text'       => $sample['text'],
			'rating'     => $sample['rating'],
			'service_id' => $sample['service_id'],
			'email'      => $sample['email'],
			'consent'    => true,
		) );
	}

	update_option( 'tqs_reviews_display_seeded_v1', 1 );
}

function tqs_maybe_seed_sample_reviews() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	tqs_seed_sample_reviews();
}
add_action( 'admin_init', 'tqs_maybe_seed_sample_reviews' );

/* ==========================================================================
   5. META BOXES — Page Hero Image + Page Options
   ========================================================================== */
function tqs_add_meta_boxes() {
	$screens = array( 'page', 'tqs_service' );
	foreach ( $screens as $screen ) {
		add_meta_box( 'tqs_hero_image_box', __( '🖼️ Page Hero Image', 'tqs-theme' ), 'tqs_render_hero_image_box', $screen, 'side', 'default' );
		add_meta_box( 'tqs_page_options_box', __( '⚙️ Page Options', 'tqs-theme' ), 'tqs_render_page_options_box', $screen, 'side', 'default' );
	}
	add_meta_box( 'tqs_gallery_box', __( '🖼️ Galerij Afbeeldingen', 'tqs-theme' ), 'tqs_render_gallery_box', 'page', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'tqs_add_meta_boxes' );

function tqs_render_hero_image_box( $post ) {
	wp_nonce_field( 'tqs_save_meta', 'tqs_meta_nonce' );
	$image_id = get_post_meta( $post->ID, '_tqs_hero_image_id', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<div class="tqs-hero-meta-box">
		<input type="hidden" name="tqs_hero_image_id" id="tqs_hero_image_id" value="<?php echo esc_attr( $image_id ); ?>">
		<div id="tqs_hero_image_preview" style="margin-bottom:10px;">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" style="max-width:100%;height:auto;border-radius:6px;">
			<?php endif; ?>
		</div>
		<button type="button" class="button" id="tqs_upload_hero_btn"><?php esc_html_e( 'Kies Afbeelding', 'tqs-theme' ); ?></button>
		<button type="button" class="button" id="tqs_remove_hero_btn" <?php echo $image_id ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Verwijderen', 'tqs-theme' ); ?></button>
		<p class="description"><?php esc_html_e( 'Valt terug op: uitgelichte afbeelding → Customizer hero → gradient achtergrond. Aanbevolen: min. 1280×500px.', 'tqs-theme' ); ?></p>
	</div>
	<script>
	jQuery(function($){
		var frame;
		$('#tqs_upload_hero_btn').on('click', function(e){
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media({ title: 'Kies Hero Afbeelding', multiple: false, library: { type: 'image' } });
			frame.on('select', function(){
				var att = frame.state().get('selection').first().toJSON();
				$('#tqs_hero_image_id').val(att.id);
				$('#tqs_hero_image_preview').html('<img src="'+att.url+'" style="max-width:100%;height:auto;border-radius:6px;">');
				$('#tqs_remove_hero_btn').show();
			});
			frame.open();
		});
		$('#tqs_remove_hero_btn').on('click', function(e){
			e.preventDefault();
			$('#tqs_hero_image_id').val('');
			$('#tqs_hero_image_preview').html('');
			$(this).hide();
		});
	});
	</script>
	<?php
}

function tqs_render_page_options_box( $post ) {
	$hero_title_override = get_post_meta( $post->ID, '_tqs_hero_title_override', true );
	$hide_hero   = get_post_meta( $post->ID, '_tqs_hide_hero', true );
	$hide_header = get_post_meta( $post->ID, '_tqs_hide_header', true );
	$hide_footer = get_post_meta( $post->ID, '_tqs_hide_footer', true );
	?>
	<p>
		<label for="tqs_hero_title_override"><strong><?php esc_html_e( 'Hero titel override', 'tqs-theme' ); ?></strong></label><br>
		<input type="text" class="widefat" id="tqs_hero_title_override" name="tqs_hero_title_override" value="<?php echo esc_attr( $hero_title_override ); ?>">
	</p>
	<p><label><input type="checkbox" name="tqs_hide_hero" value="1" <?php checked( $hide_hero, '1' ); ?>> <?php esc_html_e( 'Verberg hero sectie', 'tqs-theme' ); ?></label></p>
	<p><label><input type="checkbox" name="tqs_hide_header" value="1" <?php checked( $hide_header, '1' ); ?>> <?php esc_html_e( 'Verberg header (Elementor full-canvas)', 'tqs-theme' ); ?></label></p>
	<p><label><input type="checkbox" name="tqs_hide_footer" value="1" <?php checked( $hide_footer, '1' ); ?>> <?php esc_html_e( 'Verberg footer (Elementor full-canvas)', 'tqs-theme' ); ?></label></p>
	<?php
}

function tqs_render_gallery_box( $post ) {
	if ( 'fotogalerij' !== $post->post_name ) {
		echo '<p>' . esc_html__( 'Deze meta box is alleen van toepassing op de Fotogalerij pagina.', 'tqs-theme' ) . '</p>';
		return;
	}
	$ids = get_post_meta( $post->ID, '_tqs_gallery_ids', true );
	$ids = $ids ? explode( ',', $ids ) : array();
	?>
	<div id="tqs_gallery_wrap">
		<input type="hidden" name="tqs_gallery_ids" id="tqs_gallery_ids" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
		<div id="tqs_gallery_preview" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:14px;">
			<?php foreach ( $ids as $id ) :
				$url = wp_get_attachment_image_url( $id, 'thumbnail' );
				if ( ! $url ) continue; ?>
				<div class="tqs-gallery-thumb" data-id="<?php echo esc_attr( $id ); ?>" style="position:relative;width:110px;height:110px;">
					<img src="<?php echo esc_url( $url ); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
					<span class="tqs-remove-thumb" style="position:absolute;top:-6px;right:-6px;background:#a32626;color:#fff;border-radius:50%;width:20px;height:20px;text-align:center;line-height:20px;cursor:pointer;font-size:12px;">×</span>
				</div>
			<?php endforeach; ?>
		</div>
		<button type="button" class="button button-primary" id="tqs_add_gallery_images"><?php esc_html_e( 'Afbeeldingen Toevoegen', 'tqs-theme' ); ?></button>
	</div>
	<script>
	jQuery(function($){
		var frame;
		function syncIds(){
			var ids = [];
			$('#tqs_gallery_preview .tqs-gallery-thumb').each(function(){ ids.push($(this).data('id')); });
			$('#tqs_gallery_ids').val(ids.join(','));
		}
		$('#tqs_add_gallery_images').on('click', function(e){
			e.preventDefault();
			frame = wp.media({ title: 'Selecteer Afbeeldingen', multiple: true, library: { type: 'image' } });
			frame.on('select', function(){
				var selection = frame.state().get('selection');
				selection.each(function(att){
					att = att.toJSON();
					if ( $('#tqs_gallery_preview .tqs-gallery-thumb[data-id="'+att.id+'"]').length ) return;
					var thumbUrl = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
					$('#tqs_gallery_preview').append('<div class="tqs-gallery-thumb" data-id="'+att.id+'" style="position:relative;width:110px;height:110px;"><img src="'+thumbUrl+'" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"><span class="tqs-remove-thumb" style="position:absolute;top:-6px;right:-6px;background:#a32626;color:#fff;border-radius:50%;width:20px;height:20px;text-align:center;line-height:20px;cursor:pointer;font-size:12px;">×</span></div>');
				});
				syncIds();
			});
			frame.open();
		});
		$(document).on('click', '.tqs-remove-thumb', function(){
			$(this).closest('.tqs-gallery-thumb').remove();
			syncIds();
		});
	});
	</script>
	<?php
}

function tqs_save_meta_boxes( $post_id ) {
	if ( ! isset( $_POST['tqs_meta_nonce'] ) || ! wp_verify_nonce( $_POST['tqs_meta_nonce'], 'tqs_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['tqs_hero_image_id'] ) ) {
		update_post_meta( $post_id, '_tqs_hero_image_id', absint( $_POST['tqs_hero_image_id'] ) );
	}
	if ( isset( $_POST['tqs_hero_title_override'] ) ) {
		update_post_meta( $post_id, '_tqs_hero_title_override', sanitize_text_field( $_POST['tqs_hero_title_override'] ) );
	}
	update_post_meta( $post_id, '_tqs_hide_hero', isset( $_POST['tqs_hide_hero'] ) ? '1' : '' );
	update_post_meta( $post_id, '_tqs_hide_header', isset( $_POST['tqs_hide_header'] ) ? '1' : '' );
	update_post_meta( $post_id, '_tqs_hide_footer', isset( $_POST['tqs_hide_footer'] ) ? '1' : '' );

	if ( isset( $_POST['tqs_gallery_ids'] ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', $_POST['tqs_gallery_ids'] ) ) );
		update_post_meta( $post_id, '_tqs_gallery_ids', implode( ',', $ids ) );
	}

	if ( isset( $_POST['tqs_service_icon'] ) ) {
		update_post_meta( $post_id, '_tqs_service_icon', sanitize_text_field( $_POST['tqs_service_icon'] ) );
	}
}
add_action( 'save_post', 'tqs_save_meta_boxes' );

// Extra meta box: service icon field on tqs_service edit screen.
function tqs_add_service_icon_box() {
	add_meta_box( 'tqs_service_icon_box', __( 'Service Icoon', 'tqs-theme' ), 'tqs_render_service_icon_box', 'tqs_service', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'tqs_add_service_icon_box' );

function tqs_render_service_icon_box( $post ) {
	$icon = get_post_meta( $post->ID, '_tqs_service_icon', true );
	echo '<p><label>' . esc_html__( 'Font Awesome class (bv. fa-store)', 'tqs-theme' ) . '</label><br>';
	echo '<input type="text" class="widefat" name="tqs_service_icon" value="' . esc_attr( $icon ) . '"></p>';
}

/* ==========================================================================
   7. THEME HELPER FUNCTIONS
   ========================================================================== */
function tqs_get_hero_image_url( $post_id = null, $size = 'tqs-hero' ) {
	$post_id = $post_id ?: get_the_ID();

	$hero_id = get_post_meta( $post_id, '_tqs_hero_image_id', true );
	if ( $hero_id ) {
		$url = wp_get_attachment_image_url( $hero_id, $size );
		if ( $url ) return $url;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, $size );
		if ( $url ) return $url;
	}

	$customizer_id = get_theme_mod( 'tqs_hero_image' );
	if ( $customizer_id ) {
		$url = wp_get_attachment_image_url( $customizer_id, $size );
		if ( $url ) return $url;
	}

	return '';
}

function tqs_breadcrumbs( $trail = array() ) {
	if ( ! tqs_show_breadcrumbs() ) {
		return;
	}
	echo '<nav class="tqs-breadcrumb" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
	foreach ( $trail as $i => $item ) {
		echo '<span class="sep">/</span>';
		if ( ! empty( $item['url'] ) && $i < count( $trail ) - 1 ) {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
		} else {
			echo '<span class="current">' . esc_html( $item['label'] ) . '</span>';
		}
	}
	echo '</nav>';
}

function tqs_get_services( $limit = -1 ) {
	return get_posts( array(
		'post_type'      => 'tqs_service',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	) );
}

/**
 * Query published reviews for front-end display.
 *
 * @param array $args Optional get_posts overrides.
 * @return WP_Post[]
 */
function tqs_get_published_reviews( $args = array() ) {
	$defaults = array(
		'post_type'      => 'tqs_review',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	return get_posts( wp_parse_args( $args, $defaults ) );
}

/**
 * Published reviews linked to a specific service.
 *
 * @param int $service_id tqs_service post ID.
 * @return WP_Post[]
 */
function tqs_get_service_reviews( $service_id ) {
	$service_id = absint( $service_id );
	if ( ! $service_id ) {
		return array();
	}

	return tqs_get_published_reviews( array(
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => '_tqs_review_service_id',
				'value'   => $service_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			),
		),
	) );
}

/**
 * Front-end star markup (filled / empty).
 *
 * @param int $rating Rating 1–5.
 * @return string
 */
function tqs_render_review_stars_markup( $rating ) {
	$rating = absint( $rating );
	$rating = max( 0, min( 5, $rating ) );

	$html = '<span class="tqs-review-stars" aria-label="' . esc_attr( sprintf( __( '%d van 5 sterren', 'tqs-theme' ), $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$class = $i <= $rating ? 'tqs-review-star is-filled' : 'tqs-review-star is-empty';
		$html .= '<span class="' . esc_attr( $class ) . '" aria-hidden="true">★</span>';
	}
	$html .= '</span>';

	return $html;
}

/**
 * Render a single review card (no email).
 *
 * @param WP_Post $review Review post object.
 */
function tqs_render_review_card( $review ) {
	$rating = absint( get_post_meta( $review->ID, '_tqs_review_rating', true ) );
	$text   = get_post_meta( $review->ID, '_tqs_review_text', true );
	?>
	<article class="tqs-review-card">
		<?php echo tqs_render_review_stars_markup( $rating ); ?>
		<?php if ( $text ) : ?>
			<blockquote class="tqs-review-text"><?php echo esc_html( $text ); ?></blockquote>
		<?php endif; ?>
		<cite class="tqs-review-author"><?php echo esc_html( get_the_title( $review ) ); ?></cite>
	</article>
	<?php
}

function tqs_social_links() {
	$links = array(
		'linkedin' => get_theme_mod( 'tqs_linkedin_url' ),
		'facebook' => get_theme_mod( 'tqs_facebook_url' ),
		'instagram'=> get_theme_mod( 'tqs_instagram_url' ),
		'x'        => get_theme_mod( 'tqs_x_url' ),
	);
	return array_filter( $links );
}

/* ==========================================================================
   8. SEO — Built-in fallback (defers to Yoast / RankMath if active)
   ========================================================================== */
function tqs_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' );
}

function tqs_output_seo_meta() {
	if ( tqs_seo_plugin_active() ) {
		return;
	}

	$title = wp_get_document_title();
	$description = tqs_generate_meta_description();
	$url = tqs_canonical_url();
	$site_name = get_bloginfo( 'name' );
	$image = tqs_get_hero_image_url();
	if ( ! $image ) {
		$og_default = tqs_get_default_og_image_url();
		if ( $og_default ) {
			$image = $og_default;
		}
	}

	echo "\n<!-- TQS Theme SEO -->\n";
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:locale" content="nl_NL">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:type" content="website">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	if ( is_front_page() ) {
		tqs_output_schema();
	}
}
add_action( 'wp_head', 'tqs_output_seo_meta', 1 );

function tqs_generate_meta_description() {
	if ( is_front_page() ) {
		return get_theme_mod( 'tqs_home_meta_description', 'Top Quality Security levert professionele beveiligingsdiensten door heel Nederland, vanuit Den Haag. ND 7099 gecertificeerd, betrouwbaar en 24/7 beschikbaar.' );
	}
	if ( is_post_type_archive( 'tqs_service' ) ) {
		return 'Ontdek onze beveiligingsoplossingen: retail, horeca, evenementen, hotels, objecten, supermarkten en casino\'s. Professioneel en ND 7099 gecertificeerd.';
	}
	if ( is_singular( 'tqs_service' ) ) {
		$excerpt = get_the_excerpt();
		return $excerpt ? wp_trim_words( $excerpt, 30 ) : get_the_title() . ' — Top Quality Security';
	}
	if ( is_singular() ) {
		$excerpt = get_the_excerpt();
		if ( $excerpt ) return wp_trim_words( $excerpt, 30 );
	}
	return get_bloginfo( 'description' );
}

function tqs_canonical_url() {
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

function tqs_output_schema() {
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'SecurityService',
		'name'       => get_bloginfo( 'name' ),
		'url'        => home_url( '/' ),
		'telephone'  => get_theme_mod( 'tqs_phone', '' ),
		'email'      => get_theme_mod( 'tqs_email', '' ),
		'address'    => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => get_theme_mod( 'tqs_address', '' ),
			'addressLocality' => 'Den Haag',
			'addressCountry'  => 'NL',
		),
		'areaServed' => 'NL',
	);

	$identity_logo = tqs_get_identity_logo_url();
	if ( $identity_logo ) {
		$schema['logo'] = $identity_logo;
		$schema['image'] = $identity_logo;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}

// Excerpt length: 25 words for card previews.
function tqs_custom_excerpt_length( $length ) {
	return 25;
}
add_filter( 'excerpt_length', 'tqs_custom_excerpt_length' );

/* ==========================================================================
   9. AJAX CONTACT FORM HANDLER
   ========================================================================== */
function tqs_handle_contact_submit() {
	check_ajax_referer( 'tqs_contact_nonce', 'nonce' );

	$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
	$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
	$service    = isset( $_POST['service'] ) ? sanitize_text_field( $_POST['service'] ) : '';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

	if ( ! $first_name || ! $last_name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Vul alle verplichte velden correct in.', 'tqs-theme' ) ) );
	}

	$to = get_theme_mod( 'tqs_contact_recipient', '' );
	if ( ! $to || ! is_email( $to ) ) {
		$to = get_theme_mod( 'tqs_email', get_option( 'admin_email' ) );
	}
	$subject = sprintf( 'Contactformulier: %s %s', $first_name, $last_name );
	$body  = "Naam: {$first_name} {$last_name}\n";
	$body .= "E-mail: {$email}\n";
	$body .= "Telefoon: {$phone}\n";
	$body .= "Soort dienst: {$service}\n\n";
	$body .= "Bericht:\n{$message}\n";

	$headers = array( 'Reply-To: ' . $first_name . ' ' . $last_name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => get_theme_mod( 'tqs_form_success_msg', __( 'Bedankt voor uw bericht! Wij nemen zo spoedig mogelijk contact met u op.', 'tqs-theme' ) ) ) );
	} else {
		wp_send_json_error( array( 'message' => get_theme_mod( 'tqs_form_error_msg', __( 'Er ging iets mis bij het verzenden. Probeer het later opnieuw of bel ons direct.', 'tqs-theme' ) ) ) );
	}
}
add_action( 'wp_ajax_tqs_contact_submit', 'tqs_handle_contact_submit' );
add_action( 'wp_ajax_nopriv_tqs_contact_submit', 'tqs_handle_contact_submit' );

/* ==========================================================================
   9b. REVIEW FORM SHORTCODE + AJAX HANDLER
   ========================================================================== */
function tqs_render_review_form_shortcode() {
	$services = tqs_get_services();

	ob_start();
	?>
	<div class="tqs-review-form-wrap">
		<div class="tqs-form-message" id="tqsReviewFormMessage" hidden></div>
		<form id="tqsReviewForm" class="tqs-review-form" novalidate>
			<div class="tqs-hp-field" aria-hidden="true">
				<label for="tqs_review_hp"><?php esc_html_e( 'Laat dit veld leeg', 'tqs-theme' ); ?></label>
				<input type="text" id="tqs_review_hp" name="tqs_review_hp" tabindex="-1" autocomplete="off">
			</div>

			<div class="tqs-form-group" style="margin-bottom:20px;">
				<label for="tqs_review_name"><?php esc_html_e( 'Naam', 'tqs-theme' ); ?>*</label>
				<input type="text" id="tqs_review_name" name="name" class="tqs-input" required>
			</div>

			<div class="tqs-form-group" style="margin-bottom:20px;">
				<label for="tqs_review_email"><?php esc_html_e( 'E-mailadres', 'tqs-theme' ); ?>*</label>
				<input type="email" id="tqs_review_email" name="email" class="tqs-input" required>
			</div>

			<div class="tqs-form-group" style="margin-bottom:20px;">
				<label for="tqs_review_service"><?php esc_html_e( 'Gekoppelde dienst', 'tqs-theme' ); ?></label>
				<select id="tqs_review_service" name="service_id" class="tqs-select">
					<option value="0"><?php esc_html_e( 'Algemeen', 'tqs-theme' ); ?></option>
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service->ID ); ?>"><?php echo esc_html( $service->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<fieldset class="tqs-star-rating" style="margin-bottom:20px;">
				<legend class="tqs-star-rating__legend"><?php esc_html_e( 'Beoordeling', 'tqs-theme' ); ?>*</legend>
				<div class="tqs-star-rating__group" role="radiogroup" aria-label="<?php esc_attr_e( 'Beoordeling', 'tqs-theme' ); ?>">
					<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
						<input type="radio" id="tqs_review_rating_<?php echo esc_attr( $i ); ?>" name="rating" value="<?php echo esc_attr( $i ); ?>" required>
						<label for="tqs_review_rating_<?php echo esc_attr( $i ); ?>" title="<?php echo esc_attr( sprintf( __( '%d sterren', 'tqs-theme' ), $i ) ); ?>">
							<span class="screen-reader-text"><?php echo esc_html( sprintf( __( '%d sterren', 'tqs-theme' ), $i ) ); ?></span>
							<i class="fa-solid fa-star" aria-hidden="true"></i>
						</label>
					<?php endfor; ?>
				</div>
			</fieldset>

			<div class="tqs-form-group" style="margin-bottom:8px;">
				<label for="tqs_review_text"><?php esc_html_e( 'Uw ervaring', 'tqs-theme' ); ?>*</label>
				<textarea id="tqs_review_text" name="text" rows="5" class="tqs-textarea" maxlength="1000" required></textarea>
			</div>
			<p class="tqs-char-counter" aria-live="polite">
				<span id="tqsReviewCharCount">0</span> / 1000
			</p>

			<div class="tqs-form-group tqs-form-group--consent" style="margin-bottom:20px;">
				<label class="tqs-consent-label">
					<input type="checkbox" id="tqs_review_consent" name="consent" value="1" required>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %s: privacy policy link */
							__( 'Ik ga akkoord met het %s voor het verwerken van mijn gegevens.', 'tqs-theme' ),
							'<a href="' . esc_url( home_url( '/privacybeleid' ) ) . '">' . esc_html__( 'Privacybeleid', 'tqs-theme' ) . '</a>'
						),
						array( 'a' => array( 'href' => array() ) )
					);
					?>
				</label>
			</div>

			<button type="submit" class="tqs-btn tqs-btn-gold tqs-form-submit" id="tqsReviewFormSubmit">
				<?php esc_html_e( 'Beoordeling Versturen', 'tqs-theme' ); ?>
			</button>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tqs_review_form', 'tqs_render_review_form_shortcode' );

function tqs_handle_review_submit() {
	check_ajax_referer( 'tqs_review_nonce', 'nonce' );

	$honeypot = isset( $_POST['tqs_review_hp'] ) ? sanitize_text_field( wp_unslash( $_POST['tqs_review_hp'] ) ) : '';
	if ( '' !== $honeypot ) {
		wp_send_json_success( array(
			'message' => __( 'Bedankt voor je beoordeling! Deze wordt binnenkort gecontroleerd en gepubliceerd.', 'tqs-theme' ),
		) );
	}

	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$service_id = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;
	$rating     = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
	$text       = isset( $_POST['text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['text'] ) ) : '';
	$consent    = ! empty( $_POST['consent'] );

	if ( ! $name ) {
		wp_send_json_error( array( 'message' => __( 'Vul uw naam in.', 'tqs-theme' ) ) );
	}
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Vul een geldig e-mailadres in.', 'tqs-theme' ) ) );
	}
	if ( $service_id > 0 && 'tqs_service' !== get_post_type( $service_id ) ) {
		$service_id = 0;
	}
	if ( $rating < 1 || $rating > 5 ) {
		wp_send_json_error( array( 'message' => __( 'Selecteer een beoordeling van 1 tot 5 sterren.', 'tqs-theme' ) ) );
	}
	if ( ! $text ) {
		wp_send_json_error( array( 'message' => __( 'Vul uw ervaring in.', 'tqs-theme' ) ) );
	}
	if ( mb_strlen( $text ) > 1000 ) {
		wp_send_json_error( array( 'message' => __( 'Uw ervaring mag maximaal 1000 tekens bevatten.', 'tqs-theme' ) ) );
	}
	if ( ! $consent ) {
		wp_send_json_error( array( 'message' => __( 'U moet akkoord gaan met het Privacybeleid.', 'tqs-theme' ) ) );
	}

	$post_id = tqs_insert_review_from_submission(
		array( 'post_title' => $name ),
		array(
			'text'       => $text,
			'rating'     => $rating,
			'service_id' => $service_id,
			'email'      => $email,
			'consent'    => true,
		)
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( array( 'message' => __( 'Er ging iets mis bij het opslaan van uw beoordeling. Probeer het later opnieuw.', 'tqs-theme' ) ) );
	}

	wp_send_json_success( array(
		'message' => __( 'Bedankt voor je beoordeling! Deze wordt binnenkort gecontroleerd en gepubliceerd.', 'tqs-theme' ),
	) );
}
add_action( 'wp_ajax_tqs_submit_review', 'tqs_handle_review_submit' );
add_action( 'wp_ajax_nopriv_tqs_submit_review', 'tqs_handle_review_submit' );

/* ==========================================================================
   10. FALLBACK MENUS (if no menu assigned in Appearance → Menus)
   ========================================================================== */
function tqs_fallback_primary_menu() {
	$services = tqs_get_services();
	echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="tqs-navlink' . ( is_front_page() ? ' is-current' : '' ) . '">Home</a>';
	echo '<a href="' . esc_url( home_url( '/wie-zijn-wij' ) ) . '" class="tqs-navlink">Wie Zijn Wij</a>';
	echo '<div class="tqs-nav-dropdown-wrap">';
	echo '<a href="' . esc_url( home_url( '/onze-diensten' ) ) . '" class="tqs-navlink' . ( is_post_type_archive( 'tqs_service' ) || is_singular( 'tqs_service' ) ? ' is-current' : '' ) . '" aria-expanded="false">Onze Diensten <span style="font-size:10px;color:#C9973A;">▼</span></a>';
	echo '<div class="tqs-nav-dropdown">';
	foreach ( $services as $s ) {
		echo '<a href="' . esc_url( get_permalink( $s ) ) . '" class="tqs-drop-item">' . esc_html( $s->post_title ) . '</a>';
	}
	echo '</div></div>';
	echo '<a href="' . esc_url( home_url( '/fotogalerij' ) ) . '" class="tqs-navlink">Fotogalerij</a>';
	echo '<a href="' . esc_url( home_url( '/contact' ) ) . '" class="tqs-navlink">Contact</a>';
}

/* ==========================================================================
   11. ELEMENTOR: hide header/footer per meta box option
   ========================================================================== */
function tqs_should_hide_header() {
	return is_singular() && '1' === get_post_meta( get_the_ID(), '_tqs_hide_header', true );
}
function tqs_should_hide_footer() {
	return is_singular() && '1' === get_post_meta( get_the_ID(), '_tqs_hide_footer', true );
}
