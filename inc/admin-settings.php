<?php
/**
 * Admin settings page for MJ Eleganza theme.
 *
 * @package MJEleganza
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'mj_eleganza_get_font_options' ) ) {
    require_once get_template_directory() . '/inc/customizer-functions.php';
}

/**
 * Retrieve the available WooCommerce product categories as choices.
 *
 * @return array
 */
function mj_eleganza_get_category_choices() {
    $choices = array( '' => __( 'Selecciona una categoría', 'mjeleganza' ) );

    if ( ! taxonomy_exists( 'product_cat' ) ) {
        return $choices;
    }

    $terms = get_terms(
        array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );

    if ( is_wp_error( $terms ) ) {
        return $choices;
    }

    foreach ( $terms as $term ) {
        $choices[ $term->term_id ] = $term->name;
    }

    return $choices;
}

/**
 * Build the settings tabs and fields structure.
 *
 * @return array
 */
function mj_eleganza_get_settings_structure() {
    $font_options = array();
    foreach ( mj_eleganza_get_font_options() as $key => $data ) {
        $font_options[ $key ] = $data['label'];
    }

    $product_types = array(
        'best_selling' => __( 'Más vendidos', 'mjeleganza' ),
        'recent'       => __( 'Recientes', 'mjeleganza' ),
        'featured'     => __( 'Destacados', 'mjeleganza' ),
    );

    $current_year    = absint( date_i18n( 'Y' ) );
    $default_footer  = sprintf( '&copy; %s MJ Eleganza. Todos los derechos reservados.', $current_year );

    $structure = array(
        'general'   => array(
            'title'  => __( 'General', 'mjeleganza' ),
            'fields' => array(
                array(
                    'id'          => 'logo',
                    'type'        => 'media',
                    'label'       => __( 'Logo', 'mjeleganza' ),
                    'description' => __( 'Selecciona el logo personalizado que se mostrará en el encabezado.', 'mjeleganza' ),
                ),
                array(
                    'id'      => 'primary_color',
                    'type'    => 'color',
                    'label'   => __( 'Color primario', 'mjeleganza' ),
                    'default' => '#007BFF',
                ),
                array(
                    'id'      => 'secondary_color',
                    'type'    => 'color',
                    'label'   => __( 'Color secundario', 'mjeleganza' ),
                    'default' => '#1E1E1E',
                ),
                array(
                    'id'      => 'primary_font',
                    'type'    => 'select',
                    'label'   => __( 'Fuente principal', 'mjeleganza' ),
                    'options' => $font_options,
                    'default' => 'inherit',
                ),
            ),
        ),
        'header'    => array(
            'title'  => __( 'Header', 'mjeleganza' ),
            'fields' => array(
                array(
                    'id'      => 'sticky_header',
                    'type'    => 'checkbox',
                    'label'   => __( 'Activar sticky header', 'mjeleganza' ),
                    'default' => 0,
                ),
                array(
                    'id'          => 'header_height',
                    'type'        => 'number',
                    'label'       => __( 'Altura del header (px)', 'mjeleganza' ),
                    'default'     => 0,
                    'attributes'  => array(
                        'min'  => 0,
                        'step' => 1,
                    ),
                    'description' => __( 'Define una altura mínima para el encabezado. Usa 0 para mantener el valor predeterminado.', 'mjeleganza' ),
                ),
                array(
                    'id'          => 'header_padding',
                    'type'        => 'number',
                    'label'       => __( 'Espaciado vertical del header (px)', 'mjeleganza' ),
                    'default'     => 15,
                    'attributes'  => array(
                        'min'  => 0,
                        'step' => 1,
                    ),
                    'description' => __( 'Controla el padding superior e inferior del encabezado.', 'mjeleganza' ),
                ),
            ),
        ),
        'hero'      => array(
            'title'  => __( 'Hero Banner', 'mjeleganza' ),
            'fields' => array(
                array(
                    'id'          => 'hero_image',
                    'type'        => 'media',
                    'label'       => __( 'Imagen de fondo', 'mjeleganza' ),
                    'description' => __( 'Selecciona la imagen de fondo del banner principal.', 'mjeleganza' ),
                ),
                array(
                    'id'      => 'hero_title',
                    'type'    => 'text',
                    'label'   => __( 'Título', 'mjeleganza' ),
                    'default' => __( 'Tecnología que Define tu Estilo', 'mjeleganza' ),
                ),
                array(
                    'id'      => 'hero_subtitle',
                    'type'    => 'text',
                    'label'   => __( 'Subtítulo', 'mjeleganza' ),
                    'default' => __( 'Explora la Nueva Colección', 'mjeleganza' ),
                ),
                array(
                    'id'      => 'hero_button_text',
                    'type'    => 'text',
                    'label'   => __( 'Texto del botón', 'mjeleganza' ),
                    'default' => __( 'Ver Tienda', 'mjeleganza' ),
                ),
                array(
                    'id'          => 'hero_button_url',
                    'type'        => 'url',
                    'label'       => __( 'Enlace del botón', 'mjeleganza' ),
                    'description' => __( 'URL del botón principal del banner.', 'mjeleganza' ),
                ),
            ),
        ),
        'categories' => array(
            'title'  => __( 'Categorías destacadas', 'mjeleganza' ),
            'fields' => array(),
        ),
        'products' => array(
            'title'  => __( 'Productos destacados', 'mjeleganza' ),
            'fields' => array(
                array(
                    'id'      => 'products_type',
                    'type'    => 'select',
                    'label'   => __( 'Tipo de productos', 'mjeleganza' ),
                    'options' => $product_types,
                    'default' => 'best_selling',
                ),
                array(
                    'id'          => 'products_count',
                    'type'        => 'number',
                    'label'       => __( 'Cantidad a mostrar', 'mjeleganza' ),
                    'default'     => 4,
                    'attributes'  => array(
                        'min' => 1,
                        'max' => 12,
                    ),
                ),
            ),
        ),
        'footer'    => array(
            'title'  => __( 'Footer', 'mjeleganza' ),
            'fields' => array(
                array(
                    'id'          => 'footer_copyright',
                    'type'        => 'textarea',
                    'label'       => __( 'Texto de copyright', 'mjeleganza' ),
                    'default'     => $default_footer,
                    'description' => __( 'Este texto se mostrará en el pie de página.', 'mjeleganza' ),
                ),
            ),
        ),
    );

    $category_choices = mj_eleganza_get_category_choices();
    for ( $i = 1; $i <= 4; $i++ ) {
        $structure['categories']['fields'][] = array(
            'id'          => 'category_' . $i . '_id',
            'type'        => 'select',
            'label'       => sprintf( __( 'Categoría %d', 'mjeleganza' ), $i ),
            'options'     => $category_choices,
            'description' => __( 'Selecciona la categoría de WooCommerce que se mostrará.', 'mjeleganza' ),
        );
        $structure['categories']['fields'][] = array(
            'id'          => 'category_' . $i . '_image',
            'type'        => 'media',
            'label'       => sprintf( __( 'Imagen categoría %d', 'mjeleganza' ), $i ),
            'description' => __( 'Sube una imagen representativa para la categoría.', 'mjeleganza' ),
        );
    }

    return $structure;
}

/**
 * Sanitize each field based on its type.
 *
 * @param mixed $value Field value.
 * @param array $field Field configuration.
 *
 * @return mixed
 */
function mj_eleganza_sanitize_field( $value, $field ) {
    switch ( $field['type'] ) {
        case 'text':
            return sanitize_text_field( $value );
        case 'textarea':
            return sanitize_textarea_field( $value );
        case 'url':
            return esc_url_raw( $value );
        case 'color':
            $sanitized = sanitize_hex_color( $value );
            return $sanitized ? $sanitized : '';
        case 'number':
            return absint( $value );
        case 'select':
            $options = isset( $field['options'] ) ? (array) $field['options'] : array();
            if ( array_key_exists( $value, $options ) ) {
                return $value;
            }
            return isset( $field['default'] ) ? $field['default'] : '';
        case 'checkbox':
            return $value ? 1 : 0;
        case 'media':
            return absint( $value );
        default:
            return sanitize_text_field( $value );
    }
}

/**
 * Register settings for all fields defined in the structure.
 */
function mj_eleganza_register_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $structure = mj_eleganza_get_settings_structure();

    foreach ( $structure as $tab ) {
        foreach ( $tab['fields'] as $field ) {
            $option_name = 'mj_eleganza_' . $field['id'];

            register_setting(
                'mj_eleganza_settings',
                $option_name,
                array(
                    'sanitize_callback' => function ( $value ) use ( $field ) {
                        return mj_eleganza_sanitize_field( $value, $field );
                    },
                    'default'           => isset( $field['default'] ) ? $field['default'] : '',
                )
            );
        }
    }
}
add_action( 'admin_init', 'mj_eleganza_register_settings' );

/**
 * Add the MJ Customizer page to the WordPress admin menu.
 */
function mj_eleganza_add_admin_menu() {
    add_menu_page(
        __( 'MJ Customizer', 'mjeleganza' ),
        __( 'MJ Customizer', 'mjeleganza' ),
        'manage_options',
        'mj-customizer',
        'mj_eleganza_render_settings_page',
        'dashicons-admin-customizer',
        30
    );
}
add_action( 'admin_menu', 'mj_eleganza_add_admin_menu' );

/**
 * Enqueue admin assets for the settings page.
 *
 * @param string $hook Current admin page.
 */
function mj_eleganza_admin_assets( $hook ) {
    if ( 'toplevel_page_mj-customizer' !== $hook ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );

    wp_register_style( 'mj-eleganza-admin-style', false );
    wp_enqueue_style( 'mj-eleganza-admin-style' );

    $admin_css = '.mj-eleganza-admin .nav-tab-wrapper{margin-bottom:20px;}';
    $admin_css .= '.mj-eleganza-admin .nav-tab{cursor:pointer;}';
    $admin_css .= '.mj-eleganza-admin .mj-tab-panel{display:none;}';
    $admin_css .= '.mj-eleganza-admin .mj-tab-panel.is-active{display:block;}';
    $admin_css .= '.mj-eleganza-admin .mj-media-preview{margin-bottom:10px;max-width:220px;}';
    $admin_css .= '.mj-eleganza-admin .mj-media-preview img{max-width:220px;height:auto;display:block;}';
    $admin_css .= '.mj-eleganza-admin .mj-media-actions .button{margin-right:10px;}';
    $admin_css .= '.mj-eleganza-admin .mj-field-description{margin-top:8px;color:#555;}';
    $admin_css .= '.mj-eleganza-admin .form-table th{width:220px;}';

    wp_add_inline_style( 'mj-eleganza-admin-style', $admin_css );

    wp_register_script( 'mj-eleganza-admin-script', false, array( 'jquery', 'wp-color-picker' ), false, true );
    wp_enqueue_script( 'mj-eleganza-admin-script' );

        $admin_js = <<<'JS'
(function($){
    var storageKey = 'mjEleganzaActiveTab';
    var storageEnabled = false;
    try {
        localStorage.setItem(storageKey + 'Test', '1');
        localStorage.removeItem(storageKey + 'Test');
        storageEnabled = true;
    } catch (e) {}

    function activateTab(tab){
        var container = $('.mj-eleganza-admin');
        container.find('.nav-tab').removeClass('nav-tab-active');
        container.find('.nav-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');
        container.find('.mj-tab-panel').removeClass('is-active');
        container.find('.mj-tab-panel[data-tab="' + tab + '"]').addClass('is-active');
        if (storageEnabled) {
            localStorage.setItem(storageKey, tab);
        }
    }

    $('.mj-eleganza-admin .nav-tab').on('click', function(e){
        e.preventDefault();
        activateTab($(this).data('tab'));
    });

    if (storageEnabled) {
        var storedTab = localStorage.getItem(storageKey);
        if (storedTab && $('.mj-eleganza-admin .nav-tab[data-tab="' + storedTab + '"]').length){
            activateTab(storedTab);
        }
    }

    $('.mj-color-field').wpColorPicker();

    $('.mj-eleganza-admin').on('click', '.mj-media-upload', function(e){
        e.preventDefault();
        var button = $(this);
        var frame = wp.media({
            title: button.data('title') || button.text(),
            button: { text: button.data('button-text') || button.text() },
            multiple: false
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            var wrapper = button.closest('.mj-media-control');
            wrapper.find('input.mj-media-field').val(attachment.id);
            wrapper.find('.mj-media-preview').html('<img src="' + attachment.url + '" alt="" />');
            wrapper.addClass('has-image');
        });

        frame.open();
    });

    $('.mj-eleganza-admin').on('click', '.mj-media-remove', function(e){
        e.preventDefault();
        var wrapper = $(this).closest('.mj-media-control');
        wrapper.find('input.mj-media-field').val('');
        wrapper.find('.mj-media-preview').empty();
        wrapper.removeClass('has-image');
    });
})(jQuery);
JS;

    wp_add_inline_script( 'mj-eleganza-admin-script', $admin_js );
}
add_action( 'admin_enqueue_scripts', 'mj_eleganza_admin_assets' );

/**
 * Render each field based on type.
 *
 * @param array $field Field configuration.
 */
function mj_eleganza_render_field( $field ) {
    $option_name = 'mj_eleganza_' . $field['id'];
    $value       = get_option( $option_name, isset( $field['default'] ) ? $field['default'] : '' );

    switch ( $field['type'] ) {
        case 'textarea':
            printf(
                '<textarea id="%1$s" name="%1$s" rows="5" class="large-text">%2$s</textarea>',
                esc_attr( $option_name ),
                esc_textarea( $value )
            );
            break;
        case 'color':
            printf(
                '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text mj-color-field" data-default-color="%3$s" />',
                esc_attr( $option_name ),
                esc_attr( $value ),
                isset( $field['default'] ) ? esc_attr( $field['default'] ) : ''
            );
            break;
        case 'number':
            $attributes = '';
            if ( ! empty( $field['attributes'] ) ) {
                foreach ( $field['attributes'] as $attr_key => $attr_value ) {
                    $attributes .= sprintf( ' %s="%s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
                }
            }
            printf(
                '<input type="number" id="%1$s" name="%1$s" value="%2$s" class="small-text" %3$s />',
                esc_attr( $option_name ),
                esc_attr( $value ),
                $attributes
            );
            break;
        case 'select':
            echo '<select id="' . esc_attr( $option_name ) . '" name="' . esc_attr( $option_name ) . '">';
            $options = isset( $field['options'] ) ? (array) $field['options'] : array();
            foreach ( $options as $key => $label ) {
                printf(
                    '<option value="%1$s" %3$s>%2$s</option>',
                    esc_attr( $key ),
                    esc_html( $label ),
                    selected( $value, $key, false )
                );
            }
            echo '</select>';
            break;
        case 'checkbox':
            printf(
                '<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s /> %3$s</label>',
                esc_attr( $option_name ),
                checked( ! empty( $value ), true, false ),
                isset( $field['label'] ) ? esc_html( $field['label'] ) : ''
            );
            break;
        case 'media':
            $image_url = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
            echo '<div class="mj-media-control' . ( $image_url ? ' has-image' : '' ) . '">';
            echo '<div class="mj-media-preview">';
            if ( $image_url ) {
                printf( '<img src="%s" alt="" />', esc_url( $image_url ) );
            }
            echo '</div>';
            printf(
                '<input type="hidden" class="mj-media-field" id="%1$s" name="%1$s" value="%2$s" />',
                esc_attr( $option_name ),
                esc_attr( $value )
            );
            echo '<div class="mj-media-actions">';
            echo '<button type="button" class="button mj-media-upload">' . esc_html__( 'Seleccionar imagen', 'mjeleganza' ) . '</button>';
            echo '<button type="button" class="button mj-media-remove">' . esc_html__( 'Eliminar', 'mjeleganza' ) . '</button>';
            echo '</div>';
            echo '</div>';
            break;
        default:
            printf(
                '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
                esc_attr( $option_name ),
                esc_attr( $value )
            );
            break;
    }

    if ( ! empty( $field['description'] ) && 'checkbox' !== $field['type'] ) {
        printf( '<p class="description mj-field-description">%s</p>', esc_html( $field['description'] ) );
    }
}

/**
 * Render the MJ Customizer admin page.
 */
function mj_eleganza_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $structure = mj_eleganza_get_settings_structure();
    $tabs      = array_keys( $structure );
    $first_tab = reset( $tabs );
    ?>
    <div class="wrap mj-eleganza-admin">
        <h1><?php esc_html_e( 'MJ Customizer', 'mjeleganza' ); ?></h1>
        <?php settings_errors(); ?>
        <h2 class="nav-tab-wrapper">
            <?php foreach ( $structure as $slug => $tab ) : ?>
                <a href="#" class="nav-tab <?php echo $slug === $first_tab ? 'nav-tab-active' : ''; ?>" data-tab="<?php echo esc_attr( $slug ); ?>">
                    <?php echo esc_html( $tab['title'] ); ?>
                </a>
            <?php endforeach; ?>
        </h2>
        <form method="post" action="options.php">
            <?php settings_fields( 'mj_eleganza_settings' ); ?>
            <?php foreach ( $structure as $slug => $tab ) : ?>
                <div class="mj-tab-panel <?php echo $slug === $first_tab ? 'is-active' : ''; ?>" data-tab="<?php echo esc_attr( $slug ); ?>">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php foreach ( $tab['fields'] as $field ) : ?>
                                <tr>
                                    <th scope="row">
                                        <?php if ( 'checkbox' === $field['type'] ) : ?>
                                            &nbsp;
                                        <?php else : ?>
                                            <label for="<?php echo esc_attr( 'mj_eleganza_' . $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                        <?php endif; ?>
                                    </th>
                                    <td>
                                        <?php mj_eleganza_render_field( $field ); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// End of admin-settings.php