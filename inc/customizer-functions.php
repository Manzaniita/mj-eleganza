<?php
/**
 * Helper functions for MJ Eleganza custom options.
 *
 * @package MJEleganza
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'mj_get_option' ) ) {
    /**
     * Retrieve an option stored with the MJ Eleganza prefix.
     *
     * @param string     $key     Option key without prefix.
     * @param mixed|null $default Default value when option is not set.
     *
     * @return mixed
     */
    function mj_get_option( $key, $default = null ) {
        $option_name = 'mj_eleganza_' . $key;
        $value       = get_option( $option_name, null );

        if ( null === $value || '' === $value ) {
            return $default;
        }

        return $value;
    }
}

if ( ! function_exists( 'mj_eleganza_get_font_options' ) ) {
    /**
     * Provide the supported font options for the theme settings.
     *
     * @return array
     */
    function mj_eleganza_get_font_options() {
        return array(
            'inherit'    => array(
                'label'      => __( 'Predeterminada del tema', 'mjeleganza' ),
                'stack'      => '',
                'stylesheet' => '',
            ),
            'montserrat' => array(
                'label'      => 'Montserrat',
                'stack'      => "'Montserrat', sans-serif",
                'stylesheet' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap',
            ),
            'roboto'     => array(
                'label'      => 'Roboto',
                'stack'      => "'Roboto', sans-serif",
                'stylesheet' => 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
            ),
            'open_sans'  => array(
                'label'      => 'Open Sans',
                'stack'      => "'Open Sans', sans-serif",
                'stylesheet' => 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap',
            ),
            'playfair'   => array(
                'label'      => 'Playfair Display',
                'stack'      => "'Playfair Display', serif",
                'stylesheet' => 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap',
            ),
        );
    }
}

if ( ! function_exists( 'mj_eleganza_get_product_categories' ) ) {
    /**
     * Fetch WooCommerce product categories.
     *
     * @return array<int, WP_Term>|WP_Error
     */
    function mj_eleganza_get_product_categories() {
        if ( ! taxonomy_exists( 'product_cat' ) ) {
            return array();
        }

        $terms = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );

        if ( is_wp_error( $terms ) ) {
            return array();
        }

        return $terms;
    }
}

if ( ! function_exists( 'mj_eleganza_get_image_url' ) ) {
    /**
     * Retrieve the URL for a stored attachment ID or URL string.
     *
     * @param int|string $value Attachment ID or direct URL.
     * @param string     $size  Image size.
     *
     * @return string
     */
    function mj_eleganza_get_image_url( $value, $size = 'full' ) {
        if ( empty( $value ) ) {
            return '';
        }

        if ( is_numeric( $value ) ) {
            $url = wp_get_attachment_image_url( (int) $value, $size );
            return $url ? $url : '';
        }

        return esc_url_raw( $value );
    }
}

if ( ! function_exists( 'mj_eleganza_get_products_shortcode' ) ) {
    /**
     * Build the WooCommerce products shortcode based on settings.
     *
     * @param string $type  Product type key.
     * @param int    $limit Number of products to display.
     *
     * @return string
     */
    function mj_eleganza_get_products_shortcode( $type, $limit ) {
        $allowed_types = array( 'best_selling', 'recent', 'featured' );
        if ( ! in_array( $type, $allowed_types, true ) ) {
            $type = 'best_selling';
        }

        $limit = absint( $limit );
        if ( $limit < 1 ) {
            $limit = 4;
        }

        $atts = array(
            'limit'   => $limit,
            'columns' => 4,
        );

        switch ( $type ) {
            case 'featured':
                $atts['visibility'] = 'featured';
                break;
            case 'recent':
                $atts['orderby'] = 'date';
                $atts['order']   = 'DESC';
                break;
            default:
                $atts['best_selling'] = 'true';
                break;
        }

        $attributes = array();
        foreach ( $atts as $key => $value ) {
            $attributes[] = sprintf( '%s="%s"', sanitize_key( $key ), esc_attr( $value ) );
        }

        return '[products ' . implode( ' ', $attributes ) . ']';
    }
}

if ( ! function_exists( 'mj_eleganza_enqueue_custom_font' ) ) {
    /**
     * Enqueue the selected Google Font when necessary.
     */
    function mj_eleganza_enqueue_custom_font() {
        $fonts   = mj_eleganza_get_font_options();
        $current = mj_get_option( 'primary_font', 'inherit' );

        if ( ! isset( $fonts[ $current ] ) ) {
            return;
        }

        $stylesheet = $fonts[ $current ]['stylesheet'];
        if ( $stylesheet ) {
            wp_enqueue_style( 'mj-eleganza-custom-font', $stylesheet, array(), null );
        }
    }
    add_action( 'wp_enqueue_scripts', 'mj_eleganza_enqueue_custom_font' );
}

if ( ! function_exists( 'mj_eleganza_apply_customizer_styles' ) ) {
    /**
     * Inject dynamic CSS variables and rules based on saved settings.
     */
    function mj_eleganza_apply_customizer_styles() {
        $primary_color   = mj_get_option( 'primary_color', '' );
        $secondary_color = mj_get_option( 'secondary_color', '' );
        $primary_font    = mj_get_option( 'primary_font', 'inherit' );
        $header_height   = mj_get_option( 'header_height', '' );
        $header_padding  = mj_get_option( 'header_padding', '' );

        $fonts = mj_eleganza_get_font_options();
        $css   = '';

        $root_vars = array();
        if ( $primary_color && sanitize_hex_color( $primary_color ) ) {
            $root_vars[] = '--color-primary: ' . $primary_color . ';';
        }
        if ( $secondary_color && sanitize_hex_color( $secondary_color ) ) {
            $root_vars[] = '--color-secondary: ' . $secondary_color . ';';
        }
        if ( isset( $fonts[ $primary_font ] ) && $fonts[ $primary_font ]['stack'] ) {
            $root_vars[] = '--font-primary: ' . $fonts[ $primary_font ]['stack'] . ';';
        }

        if ( $root_vars ) {
            $css .= ':root{' . implode( '', $root_vars ) . '}';
        }

        if ( $header_height ) {
            $height = absint( $header_height );
            if ( $height > 0 ) {
                $css .= '.site-header{min-height:' . $height . 'px;}';
            }
        }

        if ( $header_padding || '0' === $header_padding ) {
            $padding = absint( $header_padding );
            $css    .= '.site-header{padding-top:' . $padding . 'px;padding-bottom:' . $padding . 'px;}';
        }

        if ( ! $css ) {
            return;
        }

        wp_add_inline_style( 'mj-eleganza-style', $css );
    }
    add_action( 'wp_enqueue_scripts', 'mj_eleganza_apply_customizer_styles', 20 );
}