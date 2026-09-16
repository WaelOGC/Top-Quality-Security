<?php
/**
 * Custom primary-menu walker — matches tqs_fallback_primary_menu markup.
 *
 * @package tqs-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs flat TQS nav classes (no &lt;ul&gt;/&lt;li&gt;) for the primary location.
 */
class TQS_Primary_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Open the dropdown panel after a parent item with children.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   wp_nav_menu() args.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth ) {
			$output .= '<div class="tqs-nav-dropdown">';
		}
	}

	/**
	 * Close dropdown + wrap after children of a top-level parent.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   wp_nav_menu() args.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === (int) $depth ) {
			$output .= '</div></div>';
		}
	}

	/**
	 * Start menu item — flat anchors / dropdown wrap matching the fallback.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   wp_nav_menu() args.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$is_current   = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true )
			|| in_array( 'current-menu-parent', $classes, true );

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		$url   = ! empty( $item->url ) ? $item->url : '';

		if ( 0 === (int) $depth ) {
			if ( $has_children ) {
				$output .= '<div class="tqs-nav-dropdown-wrap">';
				$output .= '<a href="' . esc_url( $url ) . '" class="tqs-navlink' . ( $is_current ? ' is-current' : '' ) . '" aria-expanded="false">';
				$output .= esc_html( $title );
				$output .= ' <span style="font-size:10px;color:#C9973A;">▼</span></a>';
			} else {
				$output .= '<a href="' . esc_url( $url ) . '" class="tqs-navlink' . ( $is_current ? ' is-current' : '' ) . '">' . esc_html( $title ) . '</a>';
			}
			return;
		}

		// One level of children only.
		if ( 1 === (int) $depth ) {
			$output .= '<a href="' . esc_url( $url ) . '" class="tqs-drop-item">' . esc_html( $title ) . '</a>';
		}
	}

	/**
	 * No closing &lt;li&gt; — markup is flat.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   wp_nav_menu() args.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		// Intentionally empty.
	}
}
