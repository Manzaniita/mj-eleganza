<?php
/**
 * El pie de página para nuestro tema.
 *
 * @package MJEleganza
 */
?>
    <?php
    $blog_name      = wp_strip_all_tags( get_bloginfo( 'name' ) );
    $footer_default = sprintf( '&copy; %s %s. Todos los derechos reservados.', date_i18n( 'Y' ), $blog_name );
    $footer_text    = mj_get_option( 'footer_copyright', $footer_default );
    ?>
    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <?php echo wp_kses_post( $footer_text ); ?>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
