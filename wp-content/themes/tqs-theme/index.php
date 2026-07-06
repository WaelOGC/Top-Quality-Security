<?php
/**
 * The main template file — mandatory fallback required by WordPress.
 *
 * This is used whenever a more specific template (front-page.php, page.php,
 * single-tqs_service.php, archive-tqs_service.php, 404.php, etc.) does not
 * match the current request. On a fully built-out TQS site this template
 * should rarely render, but WordPress requires it to exist for the theme
 * to be considered valid and installable.
 *
 * @package tqs-theme
 */

get_header();
?>

<section class="tqs-legal-section" style="min-height: 50vh;">
	<div class="tqs-legal-content">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
					<h1 class="tqs-page-title" style="color: var(--tqs-primary); margin-bottom: 20px;"><?php the_title(); ?></h1>
					<div class="tqs-entry-content">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>

			<?php
			the_posts_pagination( array(
				'prev_text' => __( '← Vorige', 'tqs-theme' ),
				'next_text' => __( 'Volgende →', 'tqs-theme' ),
			) );
			?>
		<?php else : ?>
			<h1 class="tqs-page-title" style="color: var(--tqs-primary);"><?php esc_html_e( 'Niets Gevonden', 'tqs-theme' ); ?></h1>
			<p><?php esc_html_e( 'Er is geen inhoud gevonden op dit adres.', 'tqs-theme' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tqs-btn tqs-btn-gold"><?php esc_html_e( 'Terug Naar Home', 'tqs-theme' ); ?></a>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
