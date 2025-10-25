<?php
/**
 * La cabecera para nuestro tema.
 *
 * @package MJEleganza
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <?php
    $header_classes = 'site-header';
    if ( mj_get_option( 'sticky_header', 0 ) ) {
        $header_classes .= ' sticky-header';
    }
    ?>
    <header id="masthead" class="<?php echo esc_attr( $header_classes ); ?>">
        <div class="header-container">
            <div class="site-branding">
                <?php
                $mj_logo_id  = mj_get_option( 'logo', 0 );
                $mj_logo_url = mj_eleganza_get_image_url( $mj_logo_id, 'full' );

                if ( $mj_logo_url ) {
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link">
                        <img src="<?php echo esc_url( $mj_logo_url ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
                    </a>
                    <?php
                } elseif ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    ?>
                    <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                    <?php
                }
                ?>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary-menu',
                        'menu_id'        => 'primary-menu',
                    )
                );
                ?>
            </nav>

            <div class="header-tools">
                <div class="product-search">
                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <?php get_product_search_form(); ?>
                    <?php endif; ?>
                </div>
                <div class="header-icons">
                    <a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" class="my-account-icon">
                        <!-- Aquí usaremos un SVG para el icono de usuario -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </a>
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-icon">
                        <!-- Aquí usaremos un SVG para el icono de carrito -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </header>
