<?php
/**
 * La plantilla para mostrar un único producto.
 *
 * @package MJEleganza
 */

get_header(); ?>

<div class="single-product-container">
    <main id="primary" class="site-main">
        <?php
        while ( have_posts() ) :
            the_post();

            /**
             * Hook: woocommerce_before_main_content.
             *
             * @hooked woocommerce_output_content_wrapper - 10 (we will remove this to use our own)
             * @hooked woocommerce_breadcrumb - 20
             */
            do_action( 'woocommerce_before_main_content' );
        ?>

        <?php wc_get_template_part( 'content', 'single-product' ); ?>

        <?php
        /**
         * Hook: woocommerce_after_main_content.
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (we will remove this)
         */
        do_action( 'woocommerce_after_main_content' );
        ?>
        <?php endwhile; // End of the loop. ?>
    </main>
</div>

<?php
get_footer();
