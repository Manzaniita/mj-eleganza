<?php
/**
 * La plantilla para mostrar la página de inicio.
 *
 * @package MJEleganza
 */

get_header();

$default_hero_title       = __( 'Tecnología que Define tu Estilo', 'mjeleganza' );
$default_hero_subtitle    = __( 'Explora la Nueva Colección', 'mjeleganza' );
$default_hero_button_text = __( 'Ver Tienda', 'mjeleganza' );
$default_hero_image       = 'https://via.placeholder.com/1920x800.png/000000/FFFFFF?text=Imagen+Producto+Estrella';

$hero_title       = mj_get_option( 'hero_title', $default_hero_title );
$hero_subtitle    = mj_get_option( 'hero_subtitle', $default_hero_subtitle );
$hero_button_text = mj_get_option( 'hero_button_text', $default_hero_button_text );
$hero_image_id    = mj_get_option( 'hero_image', 0 );
$hero_image_url   = mj_eleganza_get_image_url( $hero_image_id );

if ( ! $hero_image_url ) {
    $hero_image_url = $default_hero_image;
}

$shop_page_url = '#';
if ( function_exists( 'wc_get_page_id' ) ) {
    $shop_page_id = wc_get_page_id( 'shop' );
    if ( $shop_page_id && $shop_page_id > 0 ) {
        $shop_page_url = get_permalink( $shop_page_id );
    }
}

$hero_button_url = mj_get_option( 'hero_button_url', '' );
if ( empty( $hero_button_url ) ) {
    $hero_button_url = $shop_page_url;
}

$selected_categories = array();
for ( $i = 1; $i <= 4; $i++ ) {
    $cat_id    = (int) mj_get_option( 'category_' . $i . '_id', 0 );
    $image_id  = mj_get_option( 'category_' . $i . '_image', 0 );
    $image_url = mj_eleganza_get_image_url( $image_id, 'medium' );

    if ( ! $cat_id ) {
        continue;
    }

    $term = get_term( $cat_id, 'product_cat' );
    if ( $term && ! is_wp_error( $term ) ) {
        $selected_categories[] = array(
            'term' => $term,
            'url'  => $image_url,
        );
    }
}

$product_type  = mj_get_option( 'products_type', 'best_selling' );
$products_count = mj_get_option( 'products_count', 4 );
$products_count = absint( $products_count );
if ( $products_count < 1 ) {
    $products_count = 4;
}
$product_shortcode = mj_eleganza_get_products_shortcode( $product_type, $products_count );

$product_headings = array(
    'best_selling' => __( 'Más Vendidos', 'mjeleganza' ),
    'recent'       => __( 'Novedades', 'mjeleganza' ),
    'featured'     => __( 'Productos Destacados', 'mjeleganza' ),
);
$products_heading = isset( $product_headings[ $product_type ] ) ? $product_headings[ $product_type ] : __( 'Productos Destacados', 'mjeleganza' );

$hero_style_attr = $hero_image_url ? ' style="background-image: url(' . esc_url( $hero_image_url ) . ');"' : '';
?>

<main id="primary" class="site-main">

    <!-- 1. Sección Hero Banner -->
    <section class="hero-banner"<?php echo $hero_style_attr; ?>>
        <div class="hero-content">
            <h2 class="hero-title"><?php echo esc_html( $hero_title ); ?></h2>
            <p class="hero-subtitle"><?php echo esc_html( $hero_subtitle ); ?></p>
            <?php if ( $hero_button_text ) : ?>
                <a href="<?php echo esc_url( $hero_button_url ); ?>" class="hero-button"><?php echo esc_html( $hero_button_text ); ?></a>
            <?php endif; ?>
        </div>
    </section>

    <!-- 2. Sección de Categorías Destacadas -->
    <section class="featured-categories">
        <div class="section-container">
            <h3 class="section-title">Categorías Principales</h3>
            <div class="categories-grid">
                <?php if ( ! empty( $selected_categories ) ) : ?>
                    <?php foreach ( $selected_categories as $category_item ) :
                        /** @var WP_Term $term */
                        $term      = $category_item['term'];
                        $image_url = $category_item['url'];
                        $term_link = get_term_link( $term );
                        if ( is_wp_error( $term_link ) ) {
                            $term_link = '#';
                        }
                        ?>
                        <a href="<?php echo esc_url( $term_link ); ?>" class="category-card">
                            <?php if ( $image_url ) : ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" />
                            <?php else : ?>
                                <div class="category-image-placeholder"></div>
                            <?php endif; ?>
                            <span class="category-name"><?php echo esc_html( $term->name ); ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php
                    $fallback_categories = array(
                        __( 'Smartphones', 'mjeleganza' ),
                        __( 'Audio', 'mjeleganza' ),
                        __( 'Consumibles', 'mjeleganza' ),
                        __( 'Accesorios', 'mjeleganza' ),
                    );
                    foreach ( $fallback_categories as $label ) :
                        ?>
                        <a href="#" class="category-card">
                            <div class="category-image-placeholder"></div>
                            <span class="category-name"><?php echo esc_html( $label ); ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 3. Sección de Productos Más Vendidos -->
    <section class="featured-products">
        <div class="section-container">
            <h3 class="section-title"><?php echo esc_html( $products_heading ); ?></h3>
            <?php
            if ( class_exists( 'WooCommerce' ) && $product_shortcode ) {
                echo do_shortcode( $product_shortcode );
            }
            ?>
        </div>
    </section>
    
    <!-- 4. Sección Propuesta de Valor -->
    <section class="value-proposition">
        <div class="section-container">
             <div class="values-grid">
                <div class="value-item">
                    <!-- Icono SVG: Camión -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    <h4>Envíos Rápidos y Seguros</h4>
                </div>
                <div class="value-item">
                    <!-- Icono SVG: Escudo -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <h4>Garantía de Calidad</h4>
                </div>
                <div class="value-item">
                    <!-- Icono SVG: Auriculares -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-headphones"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v3z"></path></svg>
                    <h4>Soporte Exclusivo</h4>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
?>
