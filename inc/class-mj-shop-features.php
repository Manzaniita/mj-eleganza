<?php
/**
 * Core functionality powering the luxury WooCommerce shop experience.
 *
 * @package MJEleganza
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MJ_Eleganza_Shop_Features' ) ) {
    class MJ_Eleganza_Shop_Features {
        const NONCE_ACTION     = 'mj_shop_nonce';
        const VIEW_TRANSIENT   = 'mj_shop_view';

        /**
         * Bootstrap hooks.
         */
        public static function init() {
            add_action( 'init', array( __CLASS__, 'adjust_default_hooks' ), 20 );
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
            add_action( 'pre_get_posts', array( __CLASS__, 'handle_products_per_page' ) );
            add_filter( 'loop_shop_columns', array( __CLASS__, 'loop_columns' ) );

            add_action( 'wp_ajax_mj_filter_products', array( __CLASS__, 'ajax_filter_products' ) );
            add_action( 'wp_ajax_nopriv_mj_filter_products', array( __CLASS__, 'ajax_filter_products' ) );

            add_action( 'wp_ajax_mj_quick_view', array( __CLASS__, 'ajax_quick_view' ) );
            add_action( 'wp_ajax_nopriv_mj_quick_view', array( __CLASS__, 'ajax_quick_view' ) );

            add_filter( 'body_class', array( __CLASS__, 'body_classes' ) );
        }

        /**
         * Remove duplicated callbacks and keep catalogue ordering tidy.
         */
        public static function adjust_default_hooks() {
            if ( class_exists( 'WooCommerce' ) ) {
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
            }
        }

        /**
         * Determine if custom assets should be enqueued.
         */
        protected static function should_enqueue() {
            return ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() ) );
        }

        /**
         * Enqueue front-end assets and localize runtime data.
         */
        public static function enqueue_assets() {
            if ( ! self::should_enqueue() ) {
                return;
            }

            $theme_uri = get_template_directory_uri();
            $version   = defined( '_S_VERSION' ) ? _S_VERSION : wp_get_theme()->get( 'Version' );

            wp_enqueue_style(
                'mj-shop-style',
                $theme_uri . '/assets/css/shop.css',
                array(),
                $version
            );

            wp_register_script(
                'mj-shop-script',
                $theme_uri . '/assets/js/shop.js',
                array( 'jquery', 'wp-i18n' ),
                $version,
                true
            );

            wp_register_script(
                'mj-shop-filters',
                $theme_uri . '/assets/js/product-filters.js',
                array( 'jquery', 'mj-shop-script' ),
                $version,
                true
            );

            $localized = array(
                'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
                'nonce'          => wp_create_nonce( self::NONCE_ACTION ),
                'strings'        => array(
                    'loading'        => esc_html__( 'Cargando productos...', 'mjeleganza' ),
                    'noResults'      => esc_html__( 'No encontramos resultados con los filtros seleccionados.', 'mjeleganza' ),
                    'addedWishlist'  => esc_html__( 'Añadido a favoritos', 'mjeleganza' ),
                    'removedWishlist'=> esc_html__( 'Eliminado de favoritos', 'mjeleganza' ),
                    'addedCompare'   => esc_html__( 'Añadido a comparar', 'mjeleganza' ),
                    'removedCompare' => esc_html__( 'Eliminado de comparar', 'mjeleganza' ),
                    'quickViewError' => esc_html__( 'No fue posible cargar la vista rápida.', 'mjeleganza' ),
                ),
                'initialFilters' => self::get_initial_filters(),
            );

            wp_localize_script( 'mj-shop-script', 'MJEleganzaShop', $localized );

            wp_enqueue_script( 'mj-shop-script' );
            wp_enqueue_script( 'mj-shop-filters' );
            wp_enqueue_script( 'wc-add-to-cart' );
            wp_enqueue_script( 'wc-add-to-cart-variation' );
        }

        /**
         * Capture current filters from query string for initial UI setup.
         */
        protected static function get_initial_filters() {
            $filters = array(
                'categories'    => array(),
                'min_price'     => '',
                'max_price'     => '',
                'on_sale'       => false,
                'stock_status'  => array(),
                'rating_filter' => 0,
                'per_page'      => absint( get_option( 'posts_per_page', 12 ) ),
            );

            if ( isset( $_GET['product_cat'] ) ) {
                $filters['categories'] = array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) ) );
            }
            if ( isset( $_GET['min_price'] ) ) {
                $raw_min = trim( wp_unslash( $_GET['min_price'] ) );
                if ( '' !== $raw_min ) {
                    $filters['min_price'] = floatval( $raw_min );
                }
            }
            if ( isset( $_GET['max_price'] ) ) {
                $raw_max = trim( wp_unslash( $_GET['max_price'] ) );
                if ( '' !== $raw_max ) {
                    $filters['max_price'] = floatval( $raw_max );
                }
            }
            if ( isset( $_GET['on_sale'] ) ) {
                $filters['on_sale'] = ( '1' === wp_unslash( $_GET['on_sale'] ) );
            }
            if ( isset( $_GET['stock_status'] ) ) {
                $filters['stock_status'] = array_filter( array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['stock_status'] ) ) );
            }
            if ( isset( $_GET['rating_filter'] ) ) {
                $filters['rating_filter'] = absint( $_GET['rating_filter'] );
            }
            if ( isset( $_GET['per_page'] ) ) {
                $filters['per_page'] = absint( $_GET['per_page'] );
            }

            return $filters;
        }

        /**
         * Allow clients to adjust amount of products per page via query parameter.
         */
        public static function handle_products_per_page( $query ) {
            if ( is_admin() || ! $query->is_main_query() ) {
                return;
            }

            if ( ! function_exists( 'is_woocommerce' ) || ( ! $query->is_post_type_archive( 'product' ) && ! $query->is_tax( get_object_taxonomies( 'product' ) ) ) ) {
                return;
            }

            $per_page = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 0;
            if ( $per_page > 0 && $per_page <= 48 ) {
                $query->set( 'posts_per_page', $per_page );
            }
        }

        /**
         * Append helper classes to `<body>` when the new shop is active.
         */
        public static function body_classes( $classes ) {
            if ( self::should_enqueue() ) {
                $classes[] = 'mj-has-premium-shop';
            }
            return $classes;
        }

        /**
         * AJAX callback used to filter products without reloading the page.
         */
        public static function ajax_filter_products() {
            if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'La sesión de seguridad no es válida.', 'mjeleganza' ) ), 403 );
            }

            if ( ! class_exists( 'WooCommerce' ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'WooCommerce no está disponible.', 'mjeleganza' ) ), 400 );
            }

            $filters = isset( $_POST['filters'] ) ? (array) wp_unslash( $_POST['filters'] ) : array();
            $paged   = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
            $orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : ''; 
            $per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : absint( get_option( 'posts_per_page', 12 ) );

            $args = self::build_query_args( $filters, $paged, $per_page, $orderby );

            $query = new WP_Query( $args );

            $products_html  = '';
            $toolbar_html   = '';
            $pagination_html = '';

            wc_set_loop_prop( 'per_page', $per_page );
            wc_set_loop_prop( 'current_page', $paged );
            wc_set_loop_prop( 'is_shortcode', false );
            wc_set_loop_prop( 'total', $query->found_posts );
            wc_set_loop_prop( 'columns', self::loop_columns() );

            ob_start();
            if ( $query->have_posts() ) {
                woocommerce_product_loop_start();
                while ( $query->have_posts() ) {
                    $query->the_post();
                    do_action( 'woocommerce_shop_loop' );
                    wc_get_template_part( 'content', 'product' );
                }
                woocommerce_product_loop_end();
            }
            $products_html = ob_get_clean();

            ob_start();
            if ( $query->have_posts() ) {
                do_action( 'woocommerce_before_shop_loop' );
            }
            $toolbar_html = ob_get_clean();

            $prev_wp_query = $GLOBALS['wp_query'];
            $GLOBALS['wp_query'] = $query;

            ob_start();
            if ( $query->have_posts() ) {
                do_action( 'woocommerce_after_shop_loop' );
            }
            $pagination_html = ob_get_clean();

            $GLOBALS['wp_query'] = $prev_wp_query;

            wp_reset_postdata();
            wc_reset_loop();

            if ( '' === trim( $products_html ) ) {
                $products_html = '<div class="woocommerce-info">' . esc_html__( 'No se encontraron productos con los filtros actuales.', 'mjeleganza' ) . '</div>';
            }

            wp_send_json_success(
                array(
                    'products'       => $products_html,
                    'toolbar'        => $toolbar_html,
                    'pagination'     => $pagination_html,
                    'active_filters' => self::build_active_filters_markup( $filters ),
                    'found_posts'    => intval( $query->found_posts ),
                    'max_num_pages'  => intval( $query->max_num_pages ),
                )
            );
        }

        /**
         * Define the number of columns used on the shop grid.
         */
        public static function loop_columns( $columns = 0 ) {
            if ( ! self::should_enqueue() ) {
                return $columns ? $columns : 3;
            }

            return 3;
        }

        /**
         * Compose WP_Query arguments from filter payload.
         */
        protected static function build_query_args( $filters, $paged, $per_page, $orderby ) {
            $tax_query  = WC()->query->get_tax_query();
            $meta_query = WC()->query->get_meta_query();
            $post__in   = array();

            $categories = isset( $filters['categories'] ) ? array_filter( array_map( 'sanitize_title', (array) $filters['categories'] ) ) : array();
            if ( ! empty( $categories ) ) {
                $tax_query[] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $categories,
                );
            }

            $min_price = '';
            if ( isset( $filters['min_price'] ) && '' !== $filters['min_price'] ) {
                $min_price = floatval( $filters['min_price'] );
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => $min_price,
                    'compare' => '>=',
                    'type'    => 'DECIMAL',
                );
            }
            $max_price = '';
            if ( isset( $filters['max_price'] ) && '' !== $filters['max_price'] ) {
                $max_price = floatval( $filters['max_price'] );
                $meta_query[] = array(
                    'key'     => '_price',
                    'value'   => $max_price,
                    'compare' => '<=',
                    'type'    => 'DECIMAL',
                );
            }

            if ( ! empty( $filters['stock_status'] ) ) {
                $stock_status = array_map( 'sanitize_text_field', (array) $filters['stock_status'] );
                $meta_query[] = array(
                    'key'     => '_stock_status',
                    'value'   => $stock_status,
                    'compare' => 'IN',
                );
            }

            if ( ! empty( $filters['on_sale'] ) ) {
                $sale_ids = wc_get_product_ids_on_sale();
                if ( ! empty( $sale_ids ) ) {
                    $post__in = array_merge( array( 0 ), $sale_ids );
                } else {
                    // No sale items available, force empty result.
                    $post__in = array( 0 );
                }
            }

            if ( ! empty( $filters['rating_filter'] ) ) {
                $meta_query[] = array(
                    'key'     => '_wc_average_rating',
                    'value'   => floatval( $filters['rating_filter'] ),
                    'compare' => '>=',
                    'type'    => 'DECIMAL',
                );
            }

            $ordering = WC()->query->get_catalog_ordering_args( $orderby );

            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'paged'          => max( 1, $paged ),
                'posts_per_page' => $per_page,
                'meta_query'     => $meta_query,
                'tax_query'      => $tax_query,
            );

            if ( ! empty( $post__in ) ) {
                $args['post__in'] = $post__in;
            }

            if ( isset( $ordering['orderby'] ) ) {
                $args['orderby'] = $ordering['orderby'];
            }
            if ( isset( $ordering['order'] ) ) {
                $args['order'] = $ordering['order'];
            }
            if ( isset( $ordering['meta_key'] ) ) {
                $args['meta_key'] = $ordering['meta_key'];
            }

            return $args;
        }

        /**
         * Build markup for the active filters section.
         */
        protected static function build_active_filters_markup( $filters ) {
            $chips = array();

            if ( ! empty( $filters['categories'] ) ) {
                $terms = get_terms(
                    array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'slug'       => (array) $filters['categories'],
                    )
                );
                if ( ! is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $chips[] = self::build_chip_html( 'category', $term->slug, $term->name );
                    }
                }
            }

            if ( '' !== $filters['min_price'] || '' !== $filters['max_price'] ) {
                $min_label = '' !== $filters['min_price'] ? wc_price( $filters['min_price'] ) : esc_html__( 'Mín', 'mjeleganza' );
                $max_label = '' !== $filters['max_price'] ? wc_price( $filters['max_price'] ) : esc_html__( 'Máx', 'mjeleganza' );
                $label     = $min_label . ' – ' . $max_label;
                $chips[] = self::build_chip_html( 'price', 'range', wp_strip_all_tags( $label ) );
            }

            if ( ! empty( $filters['rating_filter'] ) ) {
                $chips[] = self::build_chip_html( 'rating_filter', (string) $filters['rating_filter'], sprintf( esc_html__( '%s★ o más', 'mjeleganza' ), number_format_i18n( $filters['rating_filter'] ) ) );
            }

            if ( ! empty( $filters['on_sale'] ) ) {
                $chips[] = self::build_chip_html( 'on_sale', '1', esc_html__( 'En promoción', 'mjeleganza' ) );
            }

            if ( ! empty( $filters['stock_status'] ) ) {
                $labels = array(
                    'instock'     => esc_html__( 'En stock', 'mjeleganza' ),
                    'onbackorder' => esc_html__( 'Pedido anticipado', 'mjeleganza' ),
                    'outofstock'  => esc_html__( 'Agotado', 'mjeleganza' ),
                );
                foreach ( (array) $filters['stock_status'] as $status ) {
                    if ( isset( $labels[ $status ] ) ) {
                        $chips[] = self::build_chip_html( 'stock_status', $status, $labels[ $status ] );
                    }
                }
            }

            if ( empty( $chips ) ) {
                return '';
            }

            return '<div class="mj-filter-chips">' . implode( '', $chips ) . '</div>';
        }

        /**
         * Helper to create a single filter chip HTML block.
         */
        protected static function build_chip_html( $type, $value, $label ) {
            return sprintf(
                '<button type="button" class="mj-filter-chip" data-filter-type="%1$s" data-filter-value="%2$s"><span>%3$s</span><span aria-hidden="true">&times;</span></button>',
                esc_attr( $type ),
                esc_attr( $value ),
                esc_html( $label )
            );
        }

        /**
         * Render Quick View modal content through AJAX.
         */
        public static function ajax_quick_view() {
            if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
                wp_send_json_error( array( 'message' => esc_html__( 'No se pudo verificar la seguridad.', 'mjeleganza' ) ), 403 );
            }

            $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
            if ( ! $product_id ) {
                wp_send_json_error( array( 'message' => esc_html__( 'Producto no válido.', 'mjeleganza' ) ), 400 );
            }

            $wc_product = wc_get_product( $product_id );
            if ( ! $wc_product ) {
                wp_send_json_error( array( 'message' => esc_html__( 'Producto no disponible.', 'mjeleganza' ) ), 404 );
            }

            global $post;
            global $product;

            $previous_global_post    = $post;
            $previous_global_product = $product;

            $post    = get_post( $product_id );
            $product = $wc_product;

            setup_postdata( $post );

            ob_start();
            self::render_quick_view_markup( $wc_product );
            $content = ob_get_clean();

            wp_reset_postdata();

            $post    = $previous_global_post;
            $product = $previous_global_product;

            wp_send_json_success( array( 'html' => $content ) );
        }

        /**
         * Output HTML for quick view modal body.
         */
    protected static function render_quick_view_markup( $product ) {
            $gallery_ids = $product->get_gallery_image_ids();
            ?>
            <div class="mj-quick-view__gallery">
                <figure class="mj-quick-view__image">
                    <?php echo wp_kses_post( $product->get_image( 'large' ) ); ?>
                </figure>
                <?php if ( ! empty( $gallery_ids ) ) : ?>
                    <div class="mj-quick-view__thumbnails" role="group" aria-label="<?php esc_attr_e( 'Miniaturas del producto', 'mjeleganza' ); ?>">
                        <?php foreach ( $gallery_ids as $attachment_id ) : ?>
                            <button type="button" class="mj-quick-view__thumb" data-full="<?php echo esc_url( wp_get_attachment_image_url( $attachment_id, 'large' ) ); ?>">
                                <?php echo wp_kses_post( wp_get_attachment_image( $attachment_id, 'thumbnail' ) ); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mj-quick-view__summary">
                <h2 id="mj-quick-view-title" class="mj-quick-view__title"><?php echo esc_html( $product->get_name() ); ?></h2>
                <div class="mj-quick-view__meta">
                    <?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span class="mj-quick-view__sku"><?php echo esc_html( $product->get_sku() ? $product->get_sku() : esc_html__( 'Sin SKU', 'mjeleganza' ) ); ?></span>
                </div>
                <div class="mj-quick-view__price">
                    <?php echo wp_kses_post( $product->get_price_html() ); ?>
                </div>
                <div class="mj-quick-view__excerpt">
                    <?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $product->get_short_description() ) ); ?>
                </div>
                <div class="mj-quick-view__buttons">
                    <?php woocommerce_template_single_add_to_cart(); ?>
                    <a class="mj-quick-view__more" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php esc_html_e( 'Ver detalles completos', 'mjeleganza' ); ?></a>
                </div>
            </div>
            <?php
        }
    }

    MJ_Eleganza_Shop_Features::init();
}
