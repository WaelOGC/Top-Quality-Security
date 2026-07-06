<?php
/**
 * 404 error page template
 *
 * @package tqs-theme
 */
get_header();
?>

<section class="tqs-404">
	<div>
		<div class="code">404</div>
		<h1><?php echo esc_html( get_theme_mod( 'tqs_404_title', 'Pagina Niet Gevonden' ) ); ?></h1>
		<p><?php echo esc_html( get_theme_mod( 'tqs_404_message', 'De pagina die u zoekt bestaat niet (meer) of is verplaatst.' ) ); ?></p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tqs-btn tqs-btn-gold"><?php echo esc_html( get_theme_mod( 'tqs_404_btn_text', 'Terug Naar Home' ) ); ?></a>
	</div>
</section>

<?php get_footer(); ?>
