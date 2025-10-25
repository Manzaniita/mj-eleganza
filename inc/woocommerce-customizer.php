<?php
/**
 * WooCommerce Customizer Settings
 *
 * @package MJEleganza
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register WooCommerce related customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function mj_eleganza_woocommerce_customizer( $wp_customize ) {
    // Panel principal para productos.
    $wp_customize->add_panel(
        'mj_products_panel',
        array(
            'title'       => __( 'MJ Eleganza - Productos', 'mjeleganza' ),
            'description' => __( 'Personaliza la apariencia de los productos.', 'mjeleganza' ),
            'priority'    => 130,
        )
    );

    // ============================================
    // SECTION: Product Cards
    // ============================================
    $wp_customize->add_section(
        'mj_product_cards',
        array(
            'title'    => __( 'Tarjetas de Producto', 'mjeleganza' ),
            'panel'    => 'mj_products_panel',
            'priority' => 10,
        )
    );

    // Product Card controls configuration.
    $product_card_selects = array(
        'mj_product_card_style'   => array(
            'default' => 'default',
            'label'   => __( 'Estilo de tarjeta', 'mjeleganza' ),
            'choices' => array(
                'default'  => __( 'Predeterminado', 'mjeleganza' ),
                'minimal'  => __( 'Minimalista', 'mjeleganza' ),
                'boxed'    => __( 'Caja', 'mjeleganza' ),
                'elevated' => __( 'Elevada', 'mjeleganza' ),
            ),
        ),
        'mj_product_image_ratio'  => array(
            'default' => 'square',
            'label'   => __( 'Proporción de imagen', 'mjeleganza' ),
            'choices' => array(
                'square'    => __( 'Cuadrada', 'mjeleganza' ),
                'portrait'  => __( 'Vertical', 'mjeleganza' ),
                'landscape' => __( 'Horizontal', 'mjeleganza' ),
            ),
        ),
        'mj_product_hover_effect' => array(
            'default' => 'zoom',
            'label'   => __( 'Efecto hover de imagen', 'mjeleganza' ),
            'choices' => array(
                'zoom' => __( 'Zoom', 'mjeleganza' ),
                'fade' => __( 'Desvanecer', 'mjeleganza' ),
                'slide'=> __( 'Deslizar', 'mjeleganza' ),
            ),
        ),
        'mj_product_button_style' => array(
            'default' => 'filled',
            'label'   => __( 'Estilo de botón', 'mjeleganza' ),
            'choices' => array(
                'filled'  => __( 'Relleno', 'mjeleganza' ),
                'outline' => __( 'Contorno', 'mjeleganza' ),
                'text'    => __( 'Texto', 'mjeleganza' ),
            ),
        ),
        'mj_product_button_position' => array(
            'default' => 'bottom',
            'label'   => __( 'Posición del botón', 'mjeleganza' ),
            'choices' => array(
                'bottom'  => __( 'Inferior', 'mjeleganza' ),
                'overlay' => __( 'Superpuesta', 'mjeleganza' ),
                'hover'   => __( 'Solo al pasar', 'mjeleganza' ),
            ),
        ),
    );

    foreach ( $product_card_selects as $setting_key => $config ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => $config['default'],
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_select',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'   => $config['label'],
                'section' => 'mj_product_cards',
                'type'    => 'select',
                'choices' => $config['choices'],
            )
        );
    }

    $product_card_ranges = array(
        'mj_product_card_border_radius' => array(
            'default' => 5,
            'label'   => __( 'Radio de borde', 'mjeleganza' ),
            'min'     => 0,
            'max'     => 30,
        ),
        'mj_product_card_spacing'       => array(
            'default' => 20,
            'label'   => __( 'Espaciado entre tarjetas', 'mjeleganza' ),
            'min'     => 10,
            'max'     => 50,
        ),
        'mj_product_title_size'         => array(
            'default' => 16,
            'label'   => __( 'Tamaño de título', 'mjeleganza' ),
            'min'     => 14,
            'max'     => 24,
        ),
        'mj_product_price_size'         => array(
            'default' => 18,
            'label'   => __( 'Tamaño de precio', 'mjeleganza' ),
            'min'     => 14,
            'max'     => 28,
        ),
        'mj_product_button_radius'      => array(
            'default' => 5,
            'label'   => __( 'Radio de borde del botón', 'mjeleganza' ),
            'min'     => 0,
            'max'     => 30,
        ),
    );

    foreach ( $product_card_ranges as $setting_key => $config ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => $config['default'],
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_range',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'       => $config['label'],
                'section'     => 'mj_product_cards',
                'type'        => 'range',
                'input_attrs' => array(
                    'min'  => $config['min'],
                    'max'  => $config['max'],
                    'step' => 1,
                ),
            )
        );
    }

    $product_card_checks = array(
        'mj_product_card_shadow'     => __( 'Mostrar sombra', 'mjeleganza' ),
        'mj_product_image_hover'     => __( 'Activar efecto hover de imagen', 'mjeleganza' ),
        'mj_product_show_category'   => __( 'Mostrar categoría', 'mjeleganza' ),
        'mj_product_show_rating'     => __( 'Mostrar valoración', 'mjeleganza' ),
        'mj_product_show_description'=> __( 'Mostrar descripción corta', 'mjeleganza' ),
    );

    foreach ( $product_card_checks as $setting_key => $label ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_checkbox',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'   => $label,
                'section' => 'mj_product_cards',
                'type'    => 'checkbox',
            )
        );
    }

    $wp_customize->add_setting(
        'mj_product_button_text',
        array(
            'default'           => __( 'Añadir al carrito', 'mjeleganza' ),
            'transport'         => 'postMessage',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'mj_product_button_text',
        array(
            'label'       => __( 'Texto del botón', 'mjeleganza' ),
            'section'     => 'mj_product_cards',
            'type'        => 'text',
            'input_attrs' => array(
                'placeholder' => __( 'Añadir al carrito', 'mjeleganza' ),
            ),
        )
    );

    // ============================================
    // SECTION: Single Product
    // ============================================
    $wp_customize->add_section(
        'mj_single_product',
        array(
            'title'    => __( 'Producto Individual', 'mjeleganza' ),
            'panel'    => 'mj_products_panel',
            'priority' => 20,
        )
    );

    $single_product_selects = array(
        'mj_single_gallery_position' => array(
            'default' => 'left',
            'label'   => __( 'Posición de galería', 'mjeleganza' ),
            'choices' => array(
                'left'  => __( 'Izquierda', 'mjeleganza' ),
                'right' => __( 'Derecha', 'mjeleganza' ),
                'top'   => __( 'Superior', 'mjeleganza' ),
            ),
        ),
        'mj_single_gallery_style'    => array(
            'default' => 'default',
            'label'   => __( 'Estilo de galería', 'mjeleganza' ),
            'choices' => array(
                'default' => __( 'Predeterminado', 'mjeleganza' ),
                'slider'  => __( 'Slider', 'mjeleganza' ),
                'grid'    => __( 'Cuadrícula', 'mjeleganza' ),
            ),
        ),
        'mj_single_thumbnail_position' => array(
            'default' => 'bottom',
            'label'   => __( 'Posición de miniaturas', 'mjeleganza' ),
            'choices' => array(
                'bottom' => __( 'Inferior', 'mjeleganza' ),
                'left'   => __( 'Izquierda', 'mjeleganza' ),
                'right'  => __( 'Derecha', 'mjeleganza' ),
            ),
        ),
        'mj_single_meta_position'    => array(
            'default' => 'after-price',
            'label'   => __( 'Posición de meta', 'mjeleganza' ),
            'choices' => array(
                'after-title' => __( 'Después del título', 'mjeleganza' ),
                'after-price' => __( 'Después del precio', 'mjeleganza' ),
                'bottom'      => __( 'Al final', 'mjeleganza' ),
            ),
        ),
        'mj_single_button_width'     => array(
            'default' => 'full',
            'label'   => __( 'Ancho del botón', 'mjeleganza' ),
            'choices' => array(
                'auto'   => __( 'Automático', 'mjeleganza' ),
                'full'   => __( 'Completo', 'mjeleganza' ),
                'custom' => __( 'Personalizado', 'mjeleganza' ),
            ),
        ),
        'mj_single_tab_style'        => array(
            'default' => 'default',
            'label'   => __( 'Estilo de pestañas', 'mjeleganza' ),
            'choices' => array(
                'default'  => __( 'Predeterminado', 'mjeleganza' ),
                'vertical' => __( 'Vertical', 'mjeleganza' ),
                'accordion'=> __( 'Acordeón', 'mjeleganza' ),
            ),
        ),
        'mj_single_tab_position'     => array(
            'default' => 'below',
            'label'   => __( 'Posición de pestañas', 'mjeleganza' ),
            'choices' => array(
                'below' => __( 'Debajo del contenido', 'mjeleganza' ),
                'side'  => __( 'Lateral', 'mjeleganza' ),
            ),
        ),
    );

    foreach ( $single_product_selects as $setting_key => $config ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => $config['default'],
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_select',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'   => $config['label'],
                'section' => 'mj_single_product',
                'type'    => 'select',
                'choices' => $config['choices'],
            )
        );
    }

    $single_product_ranges = array(
        'mj_single_content_width'   => array(
            'default' => 80,
            'label'   => __( 'Anchura del contenido (%)', 'mjeleganza' ),
            'min'     => 50,
            'max'     => 100,
        ),
        'mj_single_gallery_radius'  => array(
            'default' => 6,
            'label'   => __( 'Radio de borde de galería', 'mjeleganza' ),
            'min'     => 0,
            'max'     => 20,
        ),
        'mj_single_title_size'      => array(
            'default' => 32,
            'label'   => __( 'Tamaño de título', 'mjeleganza' ),
            'min'     => 24,
            'max'     => 48,
        ),
        'mj_single_price_size'      => array(
            'default' => 24,
            'label'   => __( 'Tamaño de precio', 'mjeleganza' ),
            'min'     => 20,
            'max'     => 36,
        ),
        'mj_single_button_height'   => array(
            'default' => 50,
            'label'   => __( 'Altura del botón', 'mjeleganza' ),
            'min'     => 40,
            'max'     => 70,
        ),
        'mj_single_button_font_size'=> array(
            'default' => 16,
            'label'   => __( 'Tamaño de fuente del botón', 'mjeleganza' ),
            'min'     => 14,
            'max'     => 20,
        ),
        'mj_single_button_radius'   => array(
            'default' => 6,
            'label'   => __( 'Radio de borde del botón', 'mjeleganza' ),
            'min'     => 0,
            'max'     => 30,
        ),
    );

    foreach ( $single_product_ranges as $setting_key => $config ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => $config['default'],
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_range',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'       => $config['label'],
                'section'     => 'mj_single_product',
                'type'        => 'range',
                'input_attrs' => array(
                    'min'  => $config['min'],
                    'max'  => $config['max'],
                    'step' => 1,
                ),
            )
        );
    }

    $single_product_checks = array(
        'mj_single_sticky_summary'    => __( 'Fijar resumen al hacer scroll', 'mjeleganza' ),
        'mj_single_show_thumbnails'   => __( 'Mostrar miniaturas', 'mjeleganza' ),
        'mj_single_enable_lightbox'   => __( 'Activar lightbox', 'mjeleganza' ),
        'mj_single_show_sku'          => __( 'Mostrar SKU', 'mjeleganza' ),
        'mj_single_show_categories'   => __( 'Mostrar categorías', 'mjeleganza' ),
        'mj_single_show_tags'         => __( 'Mostrar etiquetas', 'mjeleganza' ),
        'mj_single_show_reviews_count'=> __( 'Mostrar contador de reseñas', 'mjeleganza' ),
    );

    foreach ( $single_product_checks as $setting_key => $label ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => true,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'mj_eleganza_sanitize_checkbox',
            )
        );

        $wp_customize->add_control(
            $setting_key,
            array(
                'label'   => $label,
                'section' => 'mj_single_product',
                'type'    => 'checkbox',
            )
        );
    }

    // ============================================
    // SECTION: Product Colors
    // ============================================
    $wp_customize->add_section(
        'mj_product_colors',
        array(
            'title'    => __( 'Colores de Productos', 'mjeleganza' ),
            'panel'    => 'mj_products_panel',
            'priority' => 30,
        )
    );

    $product_color_settings = array(
        'mj_product_card_bg'           => array( 'label' => __( 'Fondo de tarjeta', 'mjeleganza' ), 'default' => '#1E1E1E' ),
        'mj_product_card_bg_hover'     => array( 'label' => __( 'Fondo de tarjeta (hover)', 'mjeleganza' ), 'default' => '#252525' ),
        'mj_single_product_bg'         => array( 'label' => __( 'Fondo de producto individual', 'mjeleganza' ), 'default' => '#111111' ),
        'mj_product_title_color'       => array( 'label' => __( 'Color de título', 'mjeleganza' ), 'default' => '#FFFFFF' ),
        'mj_product_price_color'       => array( 'label' => __( 'Color de precio', 'mjeleganza' ), 'default' => '#FFFFFF' ),
        'mj_product_sale_price_color'  => array( 'label' => __( 'Color de precio en oferta', 'mjeleganza' ), 'default' => '#FF5A5F' ),
        'mj_product_desc_color'        => array( 'label' => __( 'Color de descripción', 'mjeleganza' ), 'default' => '#A0A0A0' ),
        'mj_product_button_bg'         => array( 'label' => __( 'Fondo de botón', 'mjeleganza' ), 'default' => '#007BFF' ),
        'mj_product_button_color'      => array( 'label' => __( 'Texto de botón', 'mjeleganza' ), 'default' => '#FFFFFF' ),
        'mj_product_button_hover_bg'   => array( 'label' => __( 'Fondo de botón (hover)', 'mjeleganza' ), 'default' => '#0056B3' ),
        'mj_product_button_hover_color'=> array( 'label' => __( 'Texto de botón (hover)', 'mjeleganza' ), 'default' => '#FFFFFF' ),
        'mj_product_sale_badge_bg'     => array( 'label' => __( 'Fondo de badge oferta', 'mjeleganza' ), 'default' => '#FF5A5F' ),
        'mj_product_sale_badge_color'  => array( 'label' => __( 'Texto de badge oferta', 'mjeleganza' ), 'default' => '#FFFFFF' ),
        'mj_product_rating_color'      => array( 'label' => __( 'Color de estrellas', 'mjeleganza' ), 'default' => '#FFC107' ),
        'mj_product_border_color'      => array( 'label' => __( 'Color de borde', 'mjeleganza' ), 'default' => '#333333' ),
    );

    foreach ( $product_color_settings as $setting_key => $config ) {
        $wp_customize->add_setting(
            $setting_key,
            array(
                'default'           => $config['default'],
                'transport'         => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize,
                $setting_key,
                array(
                    'label'   => $config['label'],
                    'section' => 'mj_product_colors',
                )
            )
        );
    }

    // ============================================
    // SECTION: Product Typography
    // ============================================
    $wp_customize->add_section(
        'mj_product_typography',
        array(
            'title'    => __( 'Tipografía de Productos', 'mjeleganza' ),
            'panel'    => 'mj_products_panel',
            'priority' => 40,
        )
    );

    $font_choices = array(
        'Montserrat' => 'Montserrat',
        'Roboto'      => 'Roboto',
        'Open Sans'   => 'Open Sans',
        'Lato'        => 'Lato',
        'Poppins'     => 'Poppins',
        'Playfair Display' => 'Playfair Display',
    );

    $wp_customize->add_setting(
        'mj_product_title_font',
        array(
            'default'           => 'Montserrat',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_font',
        )
    );

    $wp_customize->add_control(
        'mj_product_title_font',
        array(
            'label'   => __( 'Fuente del título', 'mjeleganza' ),
            'section' => 'mj_product_typography',
            'type'    => 'select',
            'choices' => $font_choices,
        )
    );

    $wp_customize->add_setting(
        'mj_product_title_weight',
        array(
            'default'           => 600,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_range',
        )
    );

    $wp_customize->add_control(
        'mj_product_title_weight',
        array(
            'label'       => __( 'Peso del título', 'mjeleganza' ),
            'section'     => 'mj_product_typography',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 300,
                'max'  => 900,
                'step' => 100,
            ),
        )
    );

    $wp_customize->add_setting(
        'mj_product_title_transform',
        array(
            'default'           => 'none',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_select',
        )
    );

    $wp_customize->add_control(
        'mj_product_title_transform',
        array(
            'label'   => __( 'Transformación de título', 'mjeleganza' ),
            'section' => 'mj_product_typography',
            'type'    => 'select',
            'choices' => array(
                'none'       => __( 'Ninguna', 'mjeleganza' ),
                'uppercase'  => __( 'Mayúsculas', 'mjeleganza' ),
                'capitalize' => __( 'Capitalizar', 'mjeleganza' ),
            ),
        )
    );

    $wp_customize->add_setting(
        'mj_product_price_font',
        array(
            'default'           => 'Montserrat',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_font',
        )
    );

    $wp_customize->add_control(
        'mj_product_price_font',
        array(
            'label'   => __( 'Fuente del precio', 'mjeleganza' ),
            'section' => 'mj_product_typography',
            'type'    => 'select',
            'choices' => $font_choices,
        )
    );

    $wp_customize->add_setting(
        'mj_product_price_weight',
        array(
            'default'           => 600,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_range',
        )
    );

    $wp_customize->add_control(
        'mj_product_price_weight',
        array(
            'label'       => __( 'Peso del precio', 'mjeleganza' ),
            'section'     => 'mj_product_typography',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 300,
                'max'  => 900,
                'step' => 100,
            ),
        )
    );

    $wp_customize->add_setting(
        'mj_product_desc_font',
        array(
            'default'           => 'Open Sans',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_font',
        )
    );

    $wp_customize->add_control(
        'mj_product_desc_font',
        array(
            'label'   => __( 'Fuente de descripción', 'mjeleganza' ),
            'section' => 'mj_product_typography',
            'type'    => 'select',
            'choices' => $font_choices,
        )
    );

    $wp_customize->add_setting(
        'mj_product_desc_size',
        array(
            'default'           => 15,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_range',
        )
    );

    $wp_customize->add_control(
        'mj_product_desc_size',
        array(
            'label'       => __( 'Tamaño de descripción', 'mjeleganza' ),
            'section'     => 'mj_product_typography',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 13,
                'max'  => 18,
                'step' => 1,
            ),
        )
    );

    $wp_customize->add_setting(
        'mj_product_desc_line_height',
        array(
            'default'           => 1.6,
            'transport'         => 'postMessage',
            'sanitize_callback' => 'mj_eleganza_sanitize_float',
        )
    );

    $wp_customize->add_control(
        'mj_product_desc_line_height',
        array(
            'label'       => __( 'Alto de línea de descripción', 'mjeleganza' ),
            'section'     => 'mj_product_typography',
            'type'        => 'range',
            'input_attrs' => array(
                'min'  => 1.2,
                'max'  => 2.0,
                'step' => 0.1,
            ),
        )
    );
}
add_action( 'customize_register', 'mj_eleganza_woocommerce_customizer' );

/**
 * Sanitize select fields.
 *
 * @param string               $input   Selected option.
 * @param WP_Customize_Setting $setting Setting instance.
 *
 * @return string
 */
function mj_eleganza_sanitize_select( $input, $setting ) {
    $control = $setting->manager->get_control( $setting->id );

    if ( ! $control || ! isset( $control->choices ) ) {
        return $setting->default;
    }

    return array_key_exists( $input, $control->choices ) ? $input : $setting->default;
}

/**
 * Sanitize checkbox.
 *
 * @param mixed $checked Checkbox value.
 *
 * @return bool
 */
function mj_eleganza_sanitize_checkbox( $checked ) {
    return (bool) $checked;
}

/**
 * Sanitize numeric ranges.
 *
 * @param mixed $value Range value.
 *
 * @return int
 */
function mj_eleganza_sanitize_range( $value ) {
    return is_numeric( $value ) ? absint( $value ) : 0;
}

/**
 * Sanitize float values.
 *
 * @param mixed $value Number value.
 *
 * @return float
 */
function mj_eleganza_sanitize_float( $value ) {
    return is_numeric( $value ) ? (float) $value : 1.0;
}

/**
 * Sanitize font choices.
 *
 * @param string $font Font name.
 *
 * @return string
 */
function mj_eleganza_sanitize_font( $font ) {
    $allowed_fonts = array(
        'Montserrat',
        'Roboto',
        'Open Sans',
        'Lato',
        'Poppins',
        'Playfair Display',
    );

    return in_array( $font, $allowed_fonts, true ) ? $font : 'Montserrat';
}

/**
 * Append helper classes to the body element based on saved settings.
 *
 * @param array $classes Body classes.
 *
 * @return array
 */
function mj_eleganza_product_body_classes( $classes ) {
    $map = array(
        'mj_product_card_style'     => 'mj-card-style-',
        'mj_product_button_style'   => 'mj-button-style-',
        'mj_product_button_position'=> 'mj-button-position-',
        'mj_product_image_ratio'    => 'mj-image-ratio-',
        'mj_single_gallery_position'=> 'mj-single-gallery-',
        'mj_single_gallery_style'   => 'mj-single-gallery-style-',
        'mj_single_tab_style'       => 'mj-single-tab-style-',
        'mj_single_tab_position'    => 'mj-single-tab-position-',
    );

    foreach ( $map as $setting => $prefix ) {
        $value = get_theme_mod( $setting, '' );
        if ( $value ) {
            $classes[] = sanitize_html_class( $prefix . $value );
        }
    }

    $image_hover = get_theme_mod( 'mj_product_image_hover', false );
    if ( $image_hover ) {
        $classes[] = 'mj-image-hover-enabled';

        $hover_effect = get_theme_mod( 'mj_product_hover_effect', 'zoom' );
        if ( $hover_effect ) {
            $classes[] = sanitize_html_class( 'mj-image-hover-effect-' . $hover_effect );
        }
    }

    if ( get_theme_mod( 'mj_single_sticky_summary', false ) ) {
        $classes[] = 'mj-single-sticky-summary';
    }

    if ( get_theme_mod( 'mj_single_show_thumbnails', true ) ) {
        $classes[] = 'mj-single-thumbs-enabled';
    }

    if ( get_theme_mod( 'mj_single_enable_lightbox', true ) ) {
        $classes[] = 'mj-single-lightbox-enabled';
    }

    return $classes;
}
add_filter( 'body_class', 'mj_eleganza_product_body_classes' );
