<?php
/**
 * Template Name: Contact Page
 *
 * @package tqs-theme
 */
get_header();

$services = tqs_get_services();
$address  = get_theme_mod( 'tqs_address', 'Spui 70, 2511 BT Den Haag' );
$phone    = get_theme_mod( 'tqs_phone', '+31 (0)70 123 4567' );
$email    = get_theme_mod( 'tqs_email', 'info@topqualitysecurity.com' );
$hours    = get_theme_mod( 'tqs_hours', "Ma - Vr: 09:00 - 18:00\nWeekend: Op afspraak" );
$hours_lines = array_filter( array_map( 'trim', explode( "\n", $hours ) ) );
$wa       = preg_replace( '/[^0-9]/', '', get_theme_mod( 'tqs_whatsapp_number', '31636286183' ) );
?>

<section class="tqs-page-hero">
	<div class="tqs-page-hero-inner">
		<?php tqs_breadcrumbs( array( array( 'label' => 'Contact' ) ) ); ?>
		<h1 class="tqs-page-title">Contact</h1>
		<p class="tqs-page-subtitle">Vragen over onze diensten of een offerte op maat? Neem contact op — wij reageren binnen één werkdag.</p>
	</div>
</section>

<div class="tqs-contact-info-row">
	<div class="tqs-contact-info-card">
		<div class="tqs-contact-info-icon"><i class="fa-solid fa-location-dot" style="color:#fff;"></i></div>
		<div>
			<h4>Adres</h4>
			<p><?php echo esc_html( $address ); ?></p>
		</div>
	</div>
	<div class="tqs-contact-info-card">
		<div class="tqs-contact-info-icon"><i class="fa-solid fa-phone" style="color:#fff;"></i></div>
		<div>
			<h4>Telefoon</h4>
			<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
		</div>
	</div>
	<div class="tqs-contact-info-card">
		<div class="tqs-contact-info-icon"><i class="fa-solid fa-envelope" style="color:#fff;"></i></div>
		<div>
			<h4>E-mail</h4>
			<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
		</div>
	</div>
</div>

<div class="tqs-contact-main">
	<div>
		<h2 class="tqs-contact-form-title">Stuur Ons Een Bericht</h2>
		<p class="tqs-contact-form-sub">Vul het formulier in en wij nemen zo spoedig mogelijk contact met u op.</p>

		<div class="tqs-form-message" id="tqsFormMessage"></div>

		<?php
		// If a CF7/WPForms/Gravity Forms shortcode is present in the page content, use that instead.
		$page_content = get_the_content();
		$tqs_has_form_shortcode = has_shortcode( $page_content, 'contact-form-7' ) || has_shortcode( $page_content, 'wpforms' ) || has_shortcode( $page_content, 'gravityform' );
		if ( $tqs_has_form_shortcode ) :
			echo do_shortcode( $page_content );
		else :
		?>
		<form id="tqsContactForm" novalidate>
			<div class="tqs-form-row">
				<div class="tqs-form-group">
					<label for="tqs_first_name">Voornaam*</label>
					<input type="text" id="tqs_first_name" name="first_name" class="tqs-input" required>
				</div>
				<div class="tqs-form-group">
					<label for="tqs_last_name">Achternaam*</label>
					<input type="text" id="tqs_last_name" name="last_name" class="tqs-input" required>
				</div>
			</div>
			<div class="tqs-form-row">
				<div class="tqs-form-group">
					<label for="tqs_email_field">E-mailadres*</label>
					<input type="email" id="tqs_email_field" name="email" class="tqs-input" required>
				</div>
				<div class="tqs-form-group">
					<label for="tqs_phone_field">Telefoonnummer</label>
					<input type="text" id="tqs_phone_field" name="phone" class="tqs-input">
				</div>
			</div>
			<div class="tqs-form-group" style="margin-bottom:20px;">
				<label for="tqs_service_field">Soort dienst</label>
				<select id="tqs_service_field" name="service" class="tqs-select">
					<?php foreach ( $services as $s ) : ?>
						<option value="<?php echo esc_attr( $s->post_title ); ?>"><?php echo esc_html( $s->post_title ); ?></option>
					<?php endforeach; ?>
					<option value="Overig">Overig</option>
				</select>
			</div>
			<div class="tqs-form-group" style="margin-bottom:20px;">
				<label for="tqs_message_field">Uw Bericht*</label>
				<textarea id="tqs_message_field" name="message" rows="5" class="tqs-textarea" required></textarea>
			</div>
			<button type="submit" class="tqs-btn tqs-btn-gold tqs-form-submit" id="tqsFormSubmit">Bericht Versturen</button>
		</form>
		<?php endif; ?>

		<div class="tqs-review-form-section" style="margin-top:48px;">
			<h2 class="tqs-contact-form-title"><?php esc_html_e( 'Deel Je Ervaring', 'tqs-theme' ); ?></h2>
			<p class="tqs-contact-form-sub"><?php esc_html_e( 'Heeft u met ons samengewerkt? Deel uw ervaring — uw beoordeling wordt eerst door ons gecontroleerd.', 'tqs-theme' ); ?></p>
			<?php echo do_shortcode( '[tqs_review_form]' ); ?>
		</div>
	</div>

	<aside class="tqs-contact-sidebar">
		<div class="tqs-sidebar-card">
			<h4>OPENINGSTIJDEN</h4>
			<?php foreach ( $hours_lines as $line ) :
				$parts = explode( ':', $line, 2);
			?>
				<div class="tqs-hours-row"><span><?php echo esc_html( trim( $parts[0] ) ); ?></span> <strong><?php echo esc_html( isset( $parts[1] ) ? trim( $parts[1] ) : '' ); ?></strong></div>
			<?php endforeach; ?>
			<div class="tqs-hours-note">24/7 bereikbaar voor lopende opdrachten</div>
		</div>

		<a href="https://wa.me/<?php echo esc_attr( $wa ); ?>" target="_blank" rel="noopener" class="tqs-whatsapp-card">
			<div class="tqs-whatsapp-card-icon"><i class="fa-brands fa-whatsapp" style="color:#fff;"></i></div>
			<div class="tqs-whatsapp-card-text">
				<div class="title">Chat via WhatsApp</div>
				<div class="sub">Snel antwoord tijdens kantooruren</div>
			</div>
		</a>

		<?php if ( get_theme_mod( 'tqs_show_contact_map', true ) ) : ?>
		<div class="tqs-map-embed">
			<?php
			$maps_url = get_theme_mod( 'tqs_maps_embed_url', '' );
			if ( ! $maps_url ) {
				$maps_url = 'https://www.google.com/maps?q=' . rawurlencode( $address ) . '&output=embed';
			}
			?>
			<iframe src="<?php echo esc_url( $maps_url ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps"></iframe>
		</div>
		<?php endif; ?>
	</aside>
</div>

<div style="height:64px;"></div>

<?php get_footer(); ?>
