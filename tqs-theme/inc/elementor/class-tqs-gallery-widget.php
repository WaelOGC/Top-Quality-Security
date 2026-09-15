<?php
/**
 * Elementor widget: TQS Gallery Grid
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gallery grid widget for Elementor (free).
 */
class TQS_Gallery_Widget extends \Elementor\Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'tqs_gallery_grid';
	}

	/**
	 * Widget title in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'TQS Gallery Grid', 'tqs-theme' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Custom widget category.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'top-quality-security' );
	}

	/**
	 * Theme styles required for correct preview.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'tqs-fontawesome', 'tqs-theme-style' );
	}

	/**
	 * Theme scripts (lightbox, filters, before/after).
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'tqs-main' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'tqs-theme' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$category_options = array(
			'' => __( 'All', 'tqs-theme' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'tqs_gallery_category',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$category_options[ $term->slug ] = $term->name;
			}
		}

		$this->add_control(
			'category_filter',
			array(
				'label'   => __( 'Category filter', 'tqs-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $category_options,
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Number of columns', 'tqs-theme' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => (string) tqs_sanitize_gallery_columns( get_theme_mod( 'tqs_gallery_columns', 4 ) ),
				'options' => array(
					'3' => '3',
					'4' => '4',
				),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Show filter buttons', 'tqs-theme' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'tqs-theme' ),
				'label_off'    => __( 'No', 'tqs-theme' ),
				'return_value' => 'yes',
				'default'      => get_theme_mod( 'tqs_gallery_show_filters', true ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'lightbox_enabled',
			array(
				'label'        => __( 'Enable lightbox', 'tqs-theme' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'tqs-theme' ),
				'label_off'    => __( 'No', 'tqs-theme' ),
				'return_value' => 'yes',
				'default'      => get_theme_mod( 'tqs_gallery_lightbox_enabled', true ) ? 'yes' : '',
			)
		);

		$this->add_control(
			'max_images',
			array(
				'label'       => __( 'Maximum number of images', 'tqs-theme' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => __( '0 = show all images.', 'tqs-theme' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build gallery tile arrays from query results (same logic as page-fotogalerij.php).
	 *
	 * @param array $gallery_items WP_Post objects.
	 * @return array{ tiles: array, filter_terms: array }
	 */
	private function build_gallery_tiles( $gallery_items ) {
		$gallery_tiles = array();
		$filter_terms  = array();

		foreach ( $gallery_items as $item ) {
			if ( ! has_post_thumbnail( $item->ID ) ) {
				continue;
			}

			$grid_url = get_the_post_thumbnail_url( $item->ID, 'tqs-gallery' );
			$full_url = get_the_post_thumbnail_url( $item->ID, 'full' );
			if ( ! $grid_url ) {
				continue;
			}

			$term_slugs = array();
			$terms      = get_the_terms( $item->ID, 'tqs_gallery_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$term_slugs[]                = $term->slug;
					$filter_terms[ $term->slug ] = $term;
				}
			}

			$style      = tqs_sanitize_gallery_display_style( get_post_meta( $item->ID, '_tqs_gallery_display_style', true ) );
			$after_grid = '';
			$after_full = '';

			if ( 'before_after' === $style ) {
				$after_id = absint( get_post_meta( $item->ID, '_tqs_gallery_after_image_id', true ) );
				if ( $after_id ) {
					$after_grid = wp_get_attachment_image_url( $after_id, 'tqs-gallery' );
					$after_full = wp_get_attachment_image_url( $after_id, 'full' );
				}
				if ( ! $after_grid ) {
					$style = 'grid';
				}
			}

			$gallery_tiles[] = array(
				'title'      => get_the_title( $item ),
				'grid'       => $grid_url,
				'full'       => $full_url ? $full_url : $grid_url,
				'cats'       => implode( ' ', $term_slugs ),
				'style'      => $style,
				'after_grid' => $after_grid,
				'after_full' => $after_full ? $after_full : $after_grid,
			);
		}

		if ( ! empty( $filter_terms ) ) {
			uasort(
				$filter_terms,
				function ( $a, $b ) {
					return strcasecmp( $a->name, $b->name );
				}
			);
		}

		return array(
			'tiles'        => $gallery_tiles,
			'filter_terms' => $filter_terms,
		);
	}

	/**
	 * Render a single gallery tile (matches page-fotogalerij.php markup).
	 *
	 * @param array $tile  Tile data.
	 * @param int   $index Tile index.
	 */
	private function render_gallery_tile( $tile, $index ) {
		$tile_classes = array( 'tqs-gallery-tile' );
		if ( 'featured' === $tile['style'] ) {
			$tile_classes[] = 'tqs-gallery-tile--featured';
		} elseif ( 'before_after' === $tile['style'] ) {
			$tile_classes[] = 'tqs-gallery-tile--before-after';
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $tile_classes ) ); ?>" data-style="<?php echo esc_attr( $tile['style'] ); ?>" data-index="<?php echo esc_attr( $index ); ?>" data-full="<?php echo esc_url( $tile['full'] ); ?>"<?php echo $tile['cats'] ? ' data-category="' . esc_attr( $tile['cats'] ) . '"' : ''; ?>>
			<?php if ( 'before_after' === $tile['style'] ) : ?>
				<div class="tqs-ba-slider" style="--ba-pos: 50%;">
					<img class="tqs-ba-img tqs-ba-img--before" src="<?php echo esc_url( $tile['grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
					<div class="tqs-ba-after-wrap">
						<img class="tqs-ba-img tqs-ba-img--after" src="<?php echo esc_url( $tile['after_grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
					</div>
					<button type="button" class="tqs-ba-handle" style="left: 50%;" aria-label="<?php esc_attr_e( 'Versleep om voor en na te vergelijken', 'tqs-theme' ); ?>">
						<span class="tqs-ba-handle-grip" aria-hidden="true"></span>
					</button>
				</div>
			<?php else : ?>
				<img class="tqs-gallery-tile-img" src="<?php echo esc_url( $tile['grid'] ); ?>" alt="<?php echo esc_attr( $tile['title'] ); ?>" loading="lazy">
				<span class="tqs-gallery-tile-zoom"><i class="fa-solid fa-magnifying-glass"></i></span>
			<?php endif; ?>
			<div class="tqs-gallery-tile-caption"><?php echo esc_html( $tile['title'] ); ?></div>
		</div>
		<?php
	}

	/**
	 * Frontend output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$category_slug = isset( $settings['category_filter'] ) ? sanitize_title( $settings['category_filter'] ) : '';
		$columns       = tqs_sanitize_gallery_columns( isset( $settings['columns'] ) ? $settings['columns'] : 4 );
		$show_filters  = ( 'yes' === ( $settings['show_filters'] ?? '' ) );
		$lightbox_on   = ( 'yes' === ( $settings['lightbox_enabled'] ?? '' ) );
		$max_images    = isset( $settings['max_images'] ) ? absint( $settings['max_images'] ) : 0;

		$query_args = array(
			'post_type'      => 'tqs_gallery_item',
			'post_status'    => 'publish',
			'posts_per_page' => $max_images > 0 ? $max_images : -1,
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		);

		if ( $category_slug ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'tqs_gallery_category',
					'field'    => 'slug',
					'terms'    => $category_slug,
				),
			);
		}

		$gallery_items = get_posts( $query_args );

		if ( empty( $gallery_items ) ) {
			return;
		}

		$built         = $this->build_gallery_tiles( $gallery_items );
		$gallery_tiles = $built['tiles'];
		$filter_terms  = $built['filter_terms'];

		if ( empty( $gallery_tiles ) ) {
			return;
		}

		if ( $lightbox_on ) {
			tqs_render_gallery_lightbox_markup();
		}

		$widget_id = 'tqs-gallery-widget-' . $this->get_id();
		?>
		<section class="tqs-gallery-section">
			<?php if ( $show_filters && ! empty( $filter_terms ) ) : ?>
			<div class="tqs-gallery-filters" id="<?php echo esc_attr( $widget_id ); ?>-filters" role="toolbar" aria-label="<?php esc_attr_e( 'Filter galerij op categorie', 'tqs-theme' ); ?>">
				<button type="button" class="tqs-gallery-filter is-active" data-filter="all"><?php esc_html_e( 'Alle', 'tqs-theme' ); ?></button>
				<?php foreach ( $filter_terms as $term ) : ?>
					<button type="button" class="tqs-gallery-filter" data-filter="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="tqs-gallery-grid cols-<?php echo esc_attr( $columns ); ?>" id="<?php echo esc_attr( $widget_id ); ?>-grid" data-lightbox="<?php echo $lightbox_on ? '1' : '0'; ?>">
				<?php
				foreach ( $gallery_tiles as $i => $tile ) {
					$this->render_gallery_tile( $tile, $i );
				}
				?>
			</div>
		</section>
		<?php
	}
}
