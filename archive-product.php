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
            <?php
            /**
             * Premium shop archive template.
             *
             * @package MJEleganza
             */

            defined( 'ABSPATH' ) || exit;

            get_header();

            $current_term = get_queried_object();
            $is_shop      = is_shop();
            ?>

            <div class="mj-shop-wrapper">
                <?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
                    <div class="mj-shop-breadcrumb">
                        <?php
                        woocommerce_breadcrumb(
                            array(
                                'delimiter'   => ' <span class="separator">/</span> ',
                                'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="breadcrumb">',
                                'wrap_after'  => '</nav>',
                            )
                        );
                        ?>
                    </div>
                <?php endif; ?>

                <header class="mj-shop-header">
                    <div class="mj-shop-header__content">
                        <?php if ( $is_shop ) : ?>
                            <h1 class="mj-shop-title"><?php woocommerce_page_title(); ?></h1>
                            <?php
                            $shop_page_id = wc_get_page_id( 'shop' );
                            if ( $shop_page_id && ( $description = get_post_field( 'post_content', $shop_page_id ) ) ) :
                                ?>
                                <div class="mj-shop-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
                            <?php endif; ?>
                        <?php else : ?>
                            <h1 class="mj-shop-title"><?php single_term_title(); ?></h1>
                            <?php if ( term_description() ) : ?>
                                <div class="mj-shop-description"><?php echo wp_kses_post( term_description() ); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <?php
                    if ( ! $is_shop && $current_term instanceof WP_Term ) {
                        $thumbnail_id = get_term_meta( $current_term->term_id, 'thumbnail_id', true );
                        if ( $thumbnail_id ) {
                            $image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
                            if ( $image ) {
                                echo '<div class="mj-shop-banner" style="background-image:url(' . esc_url( $image[0] ) . ');"></div>';
                            }
                        }
                    }
                    ?>
                </header>

                <div class="mj-shop-content">
                    <aside id="mj-shop-sidebar" class="mj-shop-sidebar" aria-label="Filtros de la tienda">
                        <div class="mj-shop-sidebar__inner">
                            <div class="mj-shop-sidebar__header mobile-only">
                                <h3><?php esc_html_e( 'Filtros', 'mjeleganza' ); ?></h3>
                                <button class="mj-close-sidebar" type="button" aria-label="<?php esc_attr_e( 'Cerrar filtros', 'mjeleganza' ); ?>">&times;</button>
                            </div>

                            <?php if ( is_active_sidebar( 'shop-sidebar' ) ) : ?>
                                <?php dynamic_sidebar( 'shop-sidebar' ); ?>
                            <?php else : ?>
                                <?php
                                // Fallback widgets when no widgets are defined.
                                the_widget( 'MJ_Shop_Price_Filter_Widget' );
                                the_widget( 'MJ_Shop_Category_Filter_Widget' );
                                the_widget( 'MJ_Shop_Rating_Filter_Widget' );
                                ?>
                            <?php endif; ?>

                            <div class="mj-active-filters" id="mj-active-filters" hidden>
                                <h4><?php esc_html_e( 'Filtros activos', 'mjeleganza' ); ?></h4>
                                <div class="mj-active-filters__tags"></div>
                            </div>

                            <div class="mj-filter-actions">
                                <button id="mj-apply-filters" class="btn btn-primary" type="button"><?php esc_html_e( 'Aplicar filtros', 'mjeleganza' ); ?></button>
                                <button id="mj-clear-filters" class="btn btn-secondary" type="button"><?php esc_html_e( 'Limpiar todo', 'mjeleganza' ); ?></button>
                            </div>
                        </div>
                    </aside>

                    <main id="mj-shop-main" class="mj-shop-main">
                        <?php if ( woocommerce_product_loop() ) : ?>
                            <div class="mj-shop-toolbar" data-view="grid">
                                <div class="mj-shop-toolbar__left">
                                    <?php do_action( 'woocommerce_before_shop_loop' ); ?>
                                </div>
                                <div class="mj-shop-toolbar__right">
                                    <div class="mj-view-toggle" role="group" aria-label="<?php esc_attr_e( 'Cambiar vista de productos', 'mjeleganza' ); ?>">
                                        <button class="mj-view-toggle__btn is-active" data-view="grid" type="button" aria-pressed="true" title="<?php esc_attr_e( 'Vista cuadrícula', 'mjeleganza' ); ?>">
                                            <span class="screen-reader-text"><?php esc_html_e( 'Vista cuadrícula', 'mjeleganza' ); ?></span>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                        </button>
                                        <button class="mj-view-toggle__btn" data-view="list" type="button" aria-pressed="false" title="<?php esc_attr_e( 'Vista lista', 'mjeleganza' ); ?>">
                                            <span class="screen-reader-text"><?php esc_html_e( 'Vista lista', 'mjeleganza' ); ?></span>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                        </button>
                                    </div>

                                    <div class="mj-shop-toolbar__orderby">
                                        <?php woocommerce_catalog_ordering(); ?>
                                    </div>

                                    <div class="mj-shop-toolbar__per-page">
                                        <label for="mj-products-per-page" class="screen-reader-text"><?php esc_html_e( 'Productos por página', 'mjeleganza' ); ?></label>
                                        <select id="mj-products-per-page" name="per_page" class="mj-select">
                                            <option value="12" <?php selected( 12, get_query_var( 'posts_per_page' ) ); ?>><?php esc_html_e( '12 por página', 'mjeleganza' ); ?></option>
                                            <option value="24" <?php selected( 24, get_query_var( 'posts_per_page' ) ); ?>><?php esc_html_e( '24 por página', 'mjeleganza' ); ?></option>
                                            <option value="36" <?php selected( 36, get_query_var( 'posts_per_page' ) ); ?>><?php esc_html_e( '36 por página', 'mjeleganza' ); ?></option>
                                            <option value="48" <?php selected( 48, get_query_var( 'posts_per_page' ) ); ?>><?php esc_html_e( '48 por página', 'mjeleganza' ); ?></option>
                                        </select>
                                    </div>

                                    <button class="mj-toggle-filters mobile-only" type="button">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                        <span><?php esc_html_e( 'Filtros', 'mjeleganza' ); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div id="mj-shop-loading" class="mj-shop-loading" hidden>
                                <div class="mj-spinner"></div>
                                <p><?php esc_html_e( 'Cargando productos...', 'mjeleganza' ); ?></p>
                            </div>

                            <div id="mj-products-wrapper" class="mj-products-grid">
                                <?php
                                woocommerce_product_loop_start();

                                if ( wc_get_loop_prop( 'total' ) ) {
                                    while ( have_posts() ) {
                                        the_post();
                                        do_action( 'woocommerce_shop_loop' );
                                        wc_get_template_part( 'content', 'product' );
                                    }
                                }

                                woocommerce_product_loop_end();
                                ?>
                            </div>

                            <?php do_action( 'woocommerce_after_shop_loop' ); ?>
                        <?php else : ?>
                            <?php do_action( 'woocommerce_no_products_found' ); ?>
                        <?php endif; ?>
                    </main>
                </div>
            </div>

            <div id="mj-quick-view-modal" class="mj-quick-view" hidden>
                <div class="mj-quick-view__overlay" data-role="close"></div>
                <div class="mj-quick-view__dialog" role="dialog" aria-modal="true" aria-labelledby="mj-quick-view-title">
                    <button class="mj-quick-view__close" type="button" data-role="close" aria-label="<?php esc_attr_e( 'Cerrar vista rápida', 'mjeleganza' ); ?>">&times;</button>
                    <div class="mj-quick-view__content">
                        <div class="mj-quick-view__loader">
                            <div class="mj-spinner"></div>
                        </div>
                        <div id="mj-quick-view-render"></div>
                    </div>
                </div>
            </div>

            <?php get_footer();
