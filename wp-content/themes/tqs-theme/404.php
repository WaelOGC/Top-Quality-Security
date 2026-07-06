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
		<h1>Pagina Niet Gevonden</h1>
		<p>De pagina die u zoekt bestaat niet (meer) of is verplaatst.</p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tqs-btn tqs-btn-gold">Terug Naar Home</a>
	</div>
</section>

<?php get_footer(); ?>
