<?php
/**
 * Custom widgets used within the luxury shop experience.
 *
 * @package MJEleganza
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MJ_Shop_Price_Filter_Widget' ) ) {
    /**
     * Advanced price filter widget with sale and stock toggles.
     */
    class MJ_Shop_Price_Filter_Widget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'mj_shop_price_filter',
                esc_html__( 'MJ · Filtro de precio', 'mjeleganza' ),
                array( 'description' => esc_html__( 'Permite filtrar productos por rango de precios, promociones y disponibilidad.', 'mjeleganza' ) )
            );
        }

        public function widget( $args, $instance ) {
            $min_price   = isset( $_GET['min_price'] ) ? floatval( wp_unslash( $_GET['min_price'] ) ) : '';
            $max_price   = isset( $_GET['max_price'] ) ? floatval( wp_unslash( $_GET['max_price'] ) ) : '';
            $on_sale     = isset( $_GET['on_sale'] ) ? ( '1' === $_GET['on_sale'] ) : false;
            $stock_input = isset( $_GET['stock_status'] ) ? (array) wp_unslash( $_GET['stock_status'] ) : array();
            $currency    = get_woocommerce_currency_symbol();

            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }
            ?>
            <div class="mj-filter-group mj-filter-group--price" data-filter-group="price">
                <div class="mj-filter-group__range">
                    <label>
                        <span><?php esc_html_e( 'Mínimo', 'mjeleganza' ); ?></span>
                        <span class="mj-currency"><?php echo esc_html( $currency ); ?></span>
                        <input type="number" min="0" step="1" name="min_price" value="<?php echo esc_attr( $min_price ); ?>" placeholder="0" />
                    </label>
                    <span class="mj-filter-group__separator">—</span>
                    <label>
                        <span><?php esc_html_e( 'Máximo', 'mjeleganza' ); ?></span>
                        <span class="mj-currency"><?php echo esc_html( $currency ); ?></span>
                        <input type="number" min="0" step="1" name="max_price" value="<?php echo esc_attr( $max_price ); ?>" placeholder="5000" />
                    </label>
                </div>

                <div class="mj-filter-group__toggles">
                    <label class="mj-toggle">
                        <input type="checkbox" name="on_sale" value="1" <?php checked( $on_sale ); ?> />
                        <span><?php esc_html_e( 'Solo artículos en promoción', 'mjeleganza' ); ?></span>
                    </label>
                </div>

                <div class="mj-filter-group__stock">
                    <span class="mj-filter-group__subtitle"><?php esc_html_e( 'Disponibilidad', 'mjeleganza' ); ?></span>
                    <label>
                        <input type="checkbox" name="stock_status[]" value="instock" <?php checked( in_array( 'instock', $stock_input, true ) ); ?> />
                        <span><?php esc_html_e( 'En stock', 'mjeleganza' ); ?></span>
                    </label>
                    <label>
                        <input type="checkbox" name="stock_status[]" value="onbackorder" <?php checked( in_array( 'onbackorder', $stock_input, true ) ); ?> />
                        <span><?php esc_html_e( 'En pedido anticipado', 'mjeleganza' ); ?></span>
                    </label>
                    <label>
                        <input type="checkbox" name="stock_status[]" value="outofstock" <?php checked( in_array( 'outofstock', $stock_input, true ) ); ?> />
                        <span><?php esc_html_e( 'Agotado', 'mjeleganza' ); ?></span>
                    </label>
                </div>
            </div>
            <?php
            echo $args['after_widget'];
        }

        public function form( $instance ) {
            $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Precio', 'mjeleganza' );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título:', 'mjeleganza' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php
        }

        public function update( $new_instance, $old_instance ) {
            $instance          = array();
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
            return $instance;
        }
    }
}

if ( ! class_exists( 'MJ_Shop_Category_Filter_Widget' ) ) {
    /**
     * Category checklist widget.
     */
    class MJ_Shop_Category_Filter_Widget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'mj_shop_category_filter',
                esc_html__( 'MJ · Categorías de producto', 'mjeleganza' ),
                array( 'description' => esc_html__( 'Muestra una lista de categorías con casillas de selección.', 'mjeleganza' ) )
            );
        }

        public function widget( $args, $instance ) {
            $selected = array();
            if ( isset( $_GET['product_cat'] ) ) {
                $selected = array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['product_cat'] ) ) );
            }

            $terms = get_terms(
                array(
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                )
            );

            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                return;
            }

            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }
            ?>
            <div class="mj-filter-group mj-filter-group--categories" data-filter-group="product_cat">
                <ul class="mj-filter-list">
                    <?php foreach ( $terms as $term ) : ?>
                        <li>
                            <label>
                                <input type="checkbox" name="product_cat[]" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( in_array( $term->slug, $selected, true ) ); ?> />
                                <span class="mj-filter-name"><?php echo esc_html( $term->name ); ?></span>
                                <span class="mj-filter-count"><?php echo intval( $term->count ); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
            echo $args['after_widget'];
        }

        public function form( $instance ) {
            $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Categorías', 'mjeleganza' );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título:', 'mjeleganza' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php
        }

        public function update( $new_instance, $old_instance ) {
            $instance          = array();
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
            return $instance;
        }
    }
}

if ( ! class_exists( 'MJ_Shop_Rating_Filter_Widget' ) ) {
    /**
     * Rating filter widget using radio inputs.
     */
    class MJ_Shop_Rating_Filter_Widget extends WP_Widget {
        public function __construct() {
            parent::__construct(
                'mj_shop_rating_filter',
                esc_html__( 'MJ · Valoraciones', 'mjeleganza' ),
                array( 'description' => esc_html__( 'Permite filtrar por valoración mínima.', 'mjeleganza' ) )
            );
        }

        public function widget( $args, $instance ) {
            $current_rating = isset( $_GET['rating_filter'] ) ? absint( $_GET['rating_filter'] ) : 0;

            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }
            ?>
            <div class="mj-filter-group mj-filter-group--rating" data-filter-group="rating_filter">
                <ul class="mj-filter-list">
                    <li>
                        <label>
                            <input type="radio" name="rating_filter" value="0" <?php checked( 0 === $current_rating ); ?> />
                            <span><?php esc_html_e( 'Cualquier valoración', 'mjeleganza' ); ?></span>
                        </label>
                    </li>
                    <?php for ( $rating = 5; $rating >= 1; $rating-- ) : ?>
                        <li>
                            <label>
                                <input type="radio" name="rating_filter" value="<?php echo esc_attr( $rating ); ?>" <?php checked( $current_rating === $rating ); ?> />
                                <span>
                                    <?php
                                    printf(
                                        /* translators: %s rating stars */
                                        esc_html__( '%s estrellas o más', 'mjeleganza' ),
                                        number_format_i18n( $rating )
                                    );
                                    ?>
                                </span>
                            </label>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
            <?php
            echo $args['after_widget'];
        }

        public function form( $instance ) {
            $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Valoraciones', 'mjeleganza' );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título:', 'mjeleganza' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <?php
        }

        public function update( $new_instance, $old_instance ) {
            $instance          = array();
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
            return $instance;
        }
    }
}

/**
 * Register custom shop widgets.
 */
function mj_register_shop_widgets() {
    register_widget( 'MJ_Shop_Price_Filter_Widget' );
    register_widget( 'MJ_Shop_Category_Filter_Widget' );
    register_widget( 'MJ_Shop_Rating_Filter_Widget' );
}
add_action( 'widgets_init', 'mj_register_shop_widgets' );
