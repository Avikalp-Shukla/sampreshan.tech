<?php
/**
 * ShivBodh Custom Navigation Walker
 *
 * Properly declares the walker class BEFORE header.php is parsed,
 * so wp_nav_menu() never triggers "Class not found".
 *
 * @package ShivBodh_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ShivBodh_Nav_Walker' ) ) {

    class ShivBodh_Nav_Walker extends Walker_Nav_Menu {

        public function start_lvl( &$output, $depth = 0, $args = null ) {
            $indent  = str_repeat( "\t", $depth );
            $classes = array( 'sb-dropdown' );
            $output .= "\n{$indent}<div class=\"" . implode( ' ', $classes ) . "\">\n";
        }

        public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
            $classes      = empty( $item->classes ) ? array() : (array) $item->classes;
            $has_children = in_array( 'menu-item-has-children', $classes );

            if ( 0 === $depth ) {
                $li_classes = 'sb-nav-item' . ( $has_children ? ' sb-has-dropdown' : '' );
                $output    .= '<li class="' . esc_attr( $li_classes ) . '">';
                $output    .= '<a href="' . esc_url( $item->url ) . '" class="sb-nav-link">';
                $output    .= esc_html( $item->title );
                if ( $has_children ) {
                    $output .= ' <span class="sb-nav-arrow" aria-hidden="true">&#9662;</span>';
                }
                $output .= '</a>';
            } else {
                $output .= '<a href="' . esc_url( $item->url ) . '" class="sb-dropdown-link">';
                $output .= '<span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>';
                $output .= '<span>' . esc_html( $item->title ) . '</span>';
                $output .= '</a>';
            }
        }

        public function end_el( &$output, $item, $depth = 0, $args = null ) {
            if ( 0 === $depth ) {
                $output .= "</li>\n";
            }
        }

        public function end_lvl( &$output, $depth = 0, $args = null ) {
            $indent  = str_repeat( "\t", $depth );
            $output .= "{$indent}</div>\n";
        }
    }
}
