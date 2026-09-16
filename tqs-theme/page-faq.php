<?php
/**
 * Template Name: FAQ Page
 *
 * @package tqs-theme
 */
get_header();

$faq_items = function_exists( 'tqs_get_faq_items' )
	? tqs_get_faq_items( get_the_ID() )
	: array();
?>

<?php tqs_render_hero( get_the_ID() ); ?>

<section class="tqs-faq-section">
	<div class="tqs-faq-header">
		<div class="tqs-section-eyebrow">VEELGESTELDE VRAGEN</div>
		<h2 class="tqs-section-h2" style="font-weight:800; font-size:34px; color:var(--tqs-primary); margin:0 0 12px;">Alles wat u wilt weten</h2>
		<p class="tqs-faq-lead">Van certificering tot dekkingsgebied — hier vindt u snel antwoord.</p>
	</div>

	<div class="tqs-faq-list">
		<?php foreach ( $faq_items as $item ) :
			if ( empty( $item['question'] ) ) {
				continue;
			}
			?>
			<div class="tqs-faq-item">
				<div class="tqs-faq-q"><?php echo esc_html( $item['question'] ); ?><span class="tqs-faq-icon">+</span></div>
				<div class="tqs-faq-a"><?php echo wp_kses_post( $item['answer'] ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="tqs-faq-review-section">
	<div class="tqs-faq-review-inner">
		<h2 class="tqs-contact-form-title"><?php esc_html_e( 'Deel Uw Ervaring', 'tqs-theme' ); ?></h2>
		<p class="tqs-contact-form-sub"><?php esc_html_e( 'Heeft u met TQS gewerkt? Deel uw ervaring — uw beoordeling wordt eerst door ons gecontroleerd.', 'tqs-theme' ); ?></p>
		<?php echo do_shortcode( '[tqs_review_form]' ); ?>
	</div>
</section>

<?php get_template_part( 'template-parts/sections/cta', 'section' ); ?>

<?php get_footer(); ?>
