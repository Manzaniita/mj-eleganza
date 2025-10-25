<?php
/**
 * Funciones y definiciones del tema MJ Eleganza.
 *
 * @package MJEleganza
 */

if ( ! defined( '_S_VERSION' ) ) {
    // Reemplaza con la versión del tema.
    define( '_S_VERSION', '1.0.0' );
}

// Incluir archivos de administración
require_once get_template_directory() . '/inc/customizer-functions.php';
require_once get_template_directory() . '/inc/admin-settings.php';
require_once get_template_directory() . '/inc/woocommerce-customizer.php';
require_once get_template_directory() . '/inc/customizer-css-output.php';
if ( class_exists( 'WooCommerce' ) ) {
    require_once get_template_directory() . '/inc/class-mj-shop-widgets.php';
    require_once get_template_directory() . '/inc/class-mj-shop-features.php';
}

/**
 * Configuración inicial del tema.
 */
function mj_eleganza_setup() {
    // Añade soporte para el título del documento.
    add_theme_support( 'title-tag' );

    // Habilita el soporte para Imágenes Destacadas en posts y páginas.
    add_theme_support( 'post-thumbnails' );

    // Habilita el soporte para WooCommerce.
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mj_eleganza_setup' );


/**
 * Encolar scripts y estilos.
 */
function mj_eleganza_scripts() {
    wp_enqueue_style( 'mj-eleganza-style', get_stylesheet_uri(), array(), _S_VERSION );
}
add_action( 'wp_enqueue_scripts', 'mj_eleganza_scripts' );

/**
 * Encolar scripts del customizer.
 */
function mj_eleganza_customizer_scripts() {
    wp_enqueue_script(
        'mj-customizer-controls',
        get_template_directory_uri() . '/assets/js/customizer-controls.js',
        array( 'jquery', 'customize-controls' ),
        _S_VERSION,
        true
    );
}
add_action( 'customize_controls_enqueue_scripts', 'mj_eleganza_customizer_scripts' );

/**
 * Scripts para vista previa del customizer.
 */
function mj_eleganza_customizer_preview_scripts() {
    wp_enqueue_script(
        'mj-customizer-preview',
        get_template_directory_uri() . '/assets/js/customizer-preview.js',
        array( 'jquery', 'customize-preview' ),
        _S_VERSION,
        true
    );
}
add_action( 'customize_preview_init', 'mj_eleganza_customizer_preview_scripts' );

/**
 * Registrar ubicaciones de menú.
 */
function mj_eleganza_register_menus() {
    register_nav_menus(
        array(
            'primary-menu' => esc_html__( 'Menú Principal', 'mjeleganza' ),
        )
    );
}
add_action( 'init', 'mj_eleganza_register_menus' );

/**
 * Añadir soporte para logo personalizado.
 */
add_theme_support(
    'custom-logo',
    array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    )
);

/**
 * Registrar áreas de widgets (sidebars).
 */
function mj_eleganza_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Shop Sidebar', 'mjeleganza' ),
            'id'            => 'shop-sidebar',
            'description'   => esc_html__( 'Añada widgets aquí para que aparezcan en la barra lateral de la tienda.', 'mjeleganza' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
}
add_action( 'widgets_init', 'mj_eleganza_widgets_init' );

/**
 * Registrar bloques personalizados del tema.
 */
function mj_eleganza_register_blocks() {
    register_block_type( __DIR__ . '/hero-banner/build' );
}
add_action( 'init', 'mj_eleganza_register_blocks' );
