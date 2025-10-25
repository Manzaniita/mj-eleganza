<?php
/**
 * Product loop template override for MJ Eleganza luxury layout.
 *
 * @package MJEleganza
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}

$product_id = $product->get_id();

if ( ! function_exists( 'mj_eleganza_product_is_new' ) ) {
    /**
     * Detect whether the product should display the "new" badge.
     */
    function mj_eleganza_product_is_new( $product ) {
        $newness_days = apply_filters( 'mj_eleganza_newness_days', 21 );
        $created      = $product->get_date_created();
        if ( ! $created ) {
            return false;
        }
        return ( time() - $created->getTimestamp() ) < ( absint( $newness_days ) * DAY_IN_SECONDS );
    }
}

$categories_output = array();
$categories        = get_the_terms( $product_id, 'product_cat' );
if ( $categories && ! is_wp_error( $categories ) ) {
    foreach ( $categories as $category ) {
        $categories_output[] = $category->name;
    }
}
?>
<li <?php wc_product_class( 'mj-product-card', $product ); ?> data-product-id="<?php echo esc_attr( $product_id ); ?>">
    <div class="mj-product-card__thumbnail">
        <a href="<?php the_permalink(); ?>" class="mj-product-card__link" aria-label="<?php the_title_attribute(); ?>">
            <?php woocommerce_template_loop_product_thumbnail(); ?>
        </a>

        <div class="mj-product-card__badges">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="mj-product-card__badge mj-product-card__badge--sale"><?php esc_html_e( 'Oferta', 'mjeleganza' ); ?></span>
            <?php endif; ?>

            <?php if ( ! $product->is_in_stock() ) : ?>
                <span class="mj-product-card__badge mj-product-card__badge--stock"><?php esc_html_e( 'Agotado', 'mjeleganza' ); ?></span>
            <?php endif; ?>

            <?php if ( $product->is_in_stock() && mj_eleganza_product_is_new( $product ) ) : ?>
                <span class="mj-product-card__badge mj-product-card__badge--new"><?php esc_html_e( 'Nuevo', 'mjeleganza' ); ?></span>
            <?php endif; ?>
        </div>

        <div class="mj-product-card__actions" aria-label="<?php esc_attr_e( 'Acciones rápidas del producto', 'mjeleganza' ); ?>">
            <button class="mj-product-card__action" type="button" data-action="quick-view" data-product-id="<?php echo esc_attr( $product_id ); ?>" title="<?php esc_attr_e( 'Vista rápida', 'mjeleganza' ); ?>" aria-label="<?php esc_attr_e( 'Vista rápida', 'mjeleganza' ); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M2.05 12a9.94 9.94 0 0 1 19.9 0a9.94 9.94 0 0 1 -19.9 0"></path></svg>
            </button>
            <button class="mj-product-card__action" type="button" data-action="wishlist" data-product-id="<?php echo esc_attr( $product_id ); ?>" title="<?php esc_attr_e( 'Añadir a favoritos', 'mjeleganza' ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Añadir a favoritos', 'mjeleganza' ); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0 -7.8 0l-1 1l-1 -1a5.5 5.5 0 0 0 -7.8 7.8l1 1l7.8 7.8l7.8 -7.8l1 -1a5.5 5.5 0 0 0 0 -7.8z"></path></svg>
            </button>
            <button class="mj-product-card__action" type="button" data-action="compare" data-product-id="<?php echo esc_attr( $product_id ); ?>" title="<?php esc_attr_e( 'Añadir a comparar', 'mjeleganza' ); ?>" aria-pressed="false" aria-label="<?php esc_attr_e( 'Añadir a comparar', 'mjeleganza' ); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M3 12h12"></path><path d="M3 18h6"></path></svg>
            </button>
        </div>
    </div>

    <div class="mj-product-card__body">
        <?php if ( ! empty( $categories_output ) ) : ?>
            <div class="mj-product-card__categories"><?php echo esc_html( implode( ', ', $categories_output ) ); ?></div>
        <?php endif; ?>

        <h3 class="mj-product-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="mj-product-card__meta">
            <div class="mj-product-card__price"><?php woocommerce_template_loop_price(); ?></div>
            <div class="mj-product-card__rating">
                <?php
                $rating_html = wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() );
                if ( $rating_html ) {
                    echo wp_kses_post( $rating_html );
                } else {
                    echo '<span class="mj-product-card__rating--empty">' . esc_html__( 'Sin valoraciones', 'mjeleganza' ) . '</span>';
                }
                ?>
            </div>
        </div>

        <div class="mj-product-card__cta">
            <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
    </div>
</li>
