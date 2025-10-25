<?php
/**
 * La plantilla para mostrar los archivos de productos.
 *
 * @package MJEleganza
 */

get_header(); ?>

<div class="shop-container">
    <header class="woocommerce-products-header">
        <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
            <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_archive_description.
         *
         * @hooked woocommerce_taxonomy_archive_description - 10
         * @hooked woocommerce_product_archive_description - 10
         */
        do_action( 'woocommerce_archive_description' );
        ?>
    </header>

    <div class="shop-layout">
        <aside id="secondary" class="widget-area">
            <?php
            /**
             * Hook: woocommerce_sidebar.
             *
             * @hooked woocommerce_get_sidebar - 10
             */
            if ( is_active_sidebar( 'shop-sidebar' ) ) {
                dynamic_sidebar( 'shop-sidebar' );
            } else {
                // Mensaje si no hay widgets
                echo '<p>Añada widgets al área "Shop Sidebar" para mostrarlos aquí.</p>';
            }
            ?>
        </aside>

        <main id="primary" class="site-main">
            <?php
            if ( woocommerce_product_loop() ) {
                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked woocommerce_output_all_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action( 'woocommerce_before_shop_loop' );

                woocommerce_product_loop_start();

                if ( wc_get_loop_prop( 'total' ) ) {
                    while ( have_posts() ) {
                        the_post();
                        do_action( 'woocommerce_shop_loop' );
                        wc_get_template_part( 'content', 'product' );
                    }
                }

                woocommerce_product_loop_end();

                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action( 'woocommerce_after_shop_loop' );
            } else {
                do_action( 'woocommerce_no_products_found' );
            }
            ?>
        </main>
    </div>
</div>

<?php
get_footer();
