<?php
/**
 * Customizer CSS output.
 *
 * @package MJEleganza
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate dynamic CSS based on customizer settings.
 */
function mj_eleganza_customizer_css() {
    $card_style         = get_theme_mod( 'mj_product_card_style', 'default' );
    $card_radius        = absint( get_theme_mod( 'mj_product_card_border_radius', 5 ) );
    $card_spacing       = absint( get_theme_mod( 'mj_product_card_spacing', 20 ) );
    $card_shadow        = (bool) get_theme_mod( 'mj_product_card_shadow', false );
    $card_bg            = sanitize_hex_color( get_theme_mod( 'mj_product_card_bg', '#1E1E1E' ) );
    $card_bg_hover      = sanitize_hex_color( get_theme_mod( 'mj_product_card_bg_hover', '#252525' ) );
    $border_color       = sanitize_hex_color( get_theme_mod( 'mj_product_border_color', '#333333' ) );

    $title_size         = absint( get_theme_mod( 'mj_product_title_size', 16 ) );
    $title_color        = sanitize_hex_color( get_theme_mod( 'mj_product_title_color', '#FFFFFF' ) );
    $title_font         = sanitize_text_field( get_theme_mod( 'mj_product_title_font', 'Montserrat' ) );
    $title_weight       = absint( get_theme_mod( 'mj_product_title_weight', 600 ) );
    $title_transform    = sanitize_text_field( get_theme_mod( 'mj_product_title_transform', 'none' ) );

    $price_size         = absint( get_theme_mod( 'mj_product_price_size', 18 ) );
    $price_color        = sanitize_hex_color( get_theme_mod( 'mj_product_price_color', '#FFFFFF' ) );
    $price_font         = sanitize_text_field( get_theme_mod( 'mj_product_price_font', 'Montserrat' ) );
    $price_weight       = absint( get_theme_mod( 'mj_product_price_weight', 600 ) );
    $sale_price_color   = sanitize_hex_color( get_theme_mod( 'mj_product_sale_price_color', '#FF5A5F' ) );

    $desc_color         = sanitize_hex_color( get_theme_mod( 'mj_product_desc_color', '#A0A0A0' ) );
    $desc_font          = sanitize_text_field( get_theme_mod( 'mj_product_desc_font', 'Open Sans' ) );
    $desc_size          = absint( get_theme_mod( 'mj_product_desc_size', 15 ) );
    $desc_line_height   = floatval( get_theme_mod( 'mj_product_desc_line_height', 1.6 ) );

    $button_bg          = sanitize_hex_color( get_theme_mod( 'mj_product_button_bg', '#007BFF' ) );
    $button_color       = sanitize_hex_color( get_theme_mod( 'mj_product_button_color', '#FFFFFF' ) );
    $button_hover_bg    = sanitize_hex_color( get_theme_mod( 'mj_product_button_hover_bg', '#0056B3' ) );
    $button_hover_color = sanitize_hex_color( get_theme_mod( 'mj_product_button_hover_color', '#FFFFFF' ) );
    $button_radius      = absint( get_theme_mod( 'mj_product_button_radius', 5 ) );
    $button_style       = sanitize_text_field( get_theme_mod( 'mj_product_button_style', 'filled' ) );
    $button_position    = sanitize_text_field( get_theme_mod( 'mj_product_button_position', 'bottom' ) );

    $image_ratio        = sanitize_text_field( get_theme_mod( 'mj_product_image_ratio', 'square' ) );
    $image_hover        = (bool) get_theme_mod( 'mj_product_image_hover', false );
    $hover_effect       = sanitize_text_field( get_theme_mod( 'mj_product_hover_effect', 'zoom' ) );

    $show_category      = (bool) get_theme_mod( 'mj_product_show_category', false );
    $show_rating        = (bool) get_theme_mod( 'mj_product_show_rating', false );
    $show_desc          = (bool) get_theme_mod( 'mj_product_show_description', false );

    $sale_badge_bg      = sanitize_hex_color( get_theme_mod( 'mj_product_sale_badge_bg', '#FF5A5F' ) );
    $sale_badge_color   = sanitize_hex_color( get_theme_mod( 'mj_product_sale_badge_color', '#FFFFFF' ) );
    $rating_color       = sanitize_hex_color( get_theme_mod( 'mj_product_rating_color', '#FFC107' ) );

    $single_bg          = sanitize_hex_color( get_theme_mod( 'mj_single_product_bg', '#111111' ) );
    $single_gallery_position = sanitize_text_field( get_theme_mod( 'mj_single_gallery_position', 'left' ) );
    $single_gallery_style    = sanitize_text_field( get_theme_mod( 'mj_single_gallery_style', 'default' ) );
    $single_content_width    = absint( get_theme_mod( 'mj_single_content_width', 80 ) );
    $single_sticky_summary   = (bool) get_theme_mod( 'mj_single_sticky_summary', false );
    $single_gallery_radius   = absint( get_theme_mod( 'mj_single_gallery_radius', 6 ) );
    $single_show_thumbs      = (bool) get_theme_mod( 'mj_single_show_thumbnails', true );
    $single_thumb_position   = sanitize_text_field( get_theme_mod( 'mj_single_thumbnail_position', 'bottom' ) );
    $single_lightbox         = (bool) get_theme_mod( 'mj_single_enable_lightbox', true );
    $single_title_size       = absint( get_theme_mod( 'mj_single_title_size', 32 ) );
    $single_price_size       = absint( get_theme_mod( 'mj_single_price_size', 24 ) );
    $single_show_sku         = (bool) get_theme_mod( 'mj_single_show_sku', true );
    $single_show_categories  = (bool) get_theme_mod( 'mj_single_show_categories', true );
    $single_show_tags        = (bool) get_theme_mod( 'mj_single_show_tags', true );
    $single_meta_position    = sanitize_text_field( get_theme_mod( 'mj_single_meta_position', 'after-price' ) );
    $single_button_height    = absint( get_theme_mod( 'mj_single_button_height', 50 ) );
    $single_button_font_size = absint( get_theme_mod( 'mj_single_button_font_size', 16 ) );
    $single_button_width     = sanitize_text_field( get_theme_mod( 'mj_single_button_width', 'full' ) );
    $single_button_radius    = absint( get_theme_mod( 'mj_single_button_radius', 6 ) );
    $single_tab_style        = sanitize_text_field( get_theme_mod( 'mj_single_tab_style', 'default' ) );
    $single_tab_position     = sanitize_text_field( get_theme_mod( 'mj_single_tab_position', 'below' ) );
    $single_reviews_count    = (bool) get_theme_mod( 'mj_single_show_reviews_count', true );

    $css  = "";

    // General helpers.
    $css .= ".woocommerce ul.products { gap: {$card_spacing}px; }\n";

    // Product cards.
    $css .= ".woocommerce ul.products li.product {";
    $css .= " border-radius: {$card_radius}px;";
    $css .= $card_bg ? " background-color: {$card_bg};" : '';
    $css .= ' overflow: hidden;';

    if ( 'minimal' === $card_style ) {
        $css .= ' background-color: transparent; border: none;';
    } elseif ( 'boxed' === $card_style ) {
        $css .= " border: 1px solid {$border_color};";
    }

    if ( $card_shadow || 'elevated' === $card_style ) {
        $css .= ' box-shadow: 0 10px 30px rgba(0,0,0,0.2);';
    }

    $css .= " }\n";

    if ( $card_bg_hover ) {
        $css .= ".woocommerce ul.products li.product:hover { background-color: {$card_bg_hover}; }\n";
    }

    if ( 'hover' === $button_position ) {
        $css .= ".woocommerce ul.products li.product .button { opacity: 0; transform: translateY(10px); transition: all 0.3s ease; }\n";
        $css .= ".woocommerce ul.products li.product:hover .button { opacity: 1; transform: translateY(0); }\n";
    } elseif ( 'overlay' === $button_position ) {
        $css .= ".woocommerce ul.products li.product { position: relative; }\n";
        $css .= ".woocommerce ul.products li.product .button { position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%); width: calc(100% - 40px); }\n";
    }

    // Image ratios.
    if ( 'portrait' === $image_ratio ) {
        $css .= ".woocommerce ul.products li.product a img { aspect-ratio: 3 / 4; object-fit: cover; }\n";
    } elseif ( 'landscape' === $image_ratio ) {
        $css .= ".woocommerce ul.products li.product a img { aspect-ratio: 4 / 3; object-fit: cover; }\n";
    } else {
        $css .= ".woocommerce ul.products li.product a img { aspect-ratio: 1 / 1; object-fit: cover; }\n";
    }

    if ( $image_hover ) {
        $transition = 'transform 0.4s ease';
        if ( 'fade' === $hover_effect ) {
            $css .= ".woocommerce ul.products li.product a img { transition: opacity 0.4s ease; }\n";
            $css .= ".woocommerce ul.products li.product:hover a img { opacity: 0.85; }\n";
        } elseif ( 'slide' === $hover_effect ) {
            $css .= ".woocommerce ul.products li.product a img { transition: transform 0.4s ease; }\n";
            $css .= ".woocommerce ul.products li.product:hover a img { transform: translateY(-6px); }\n";
        } else {
            $css .= ".woocommerce ul.products li.product a img { transition: {$transition}; }\n";
            $css .= ".woocommerce ul.products li.product:hover a img { transform: scale(1.05); }\n";
        }
    }

    // Card content toggles.
    if ( ! $show_category ) {
        $css .= ".woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .posted_in { display: none !important; }\n";
    }

    if ( ! $show_rating ) {
        $css .= ".woocommerce ul.products li.product .star-rating { display: none !important; }\n";
    } else {
        $css .= ".woocommerce ul.products li.product .star-rating { color: {$rating_color}; }\n";
    }

    if ( ! $show_desc ) {
        $css .= ".woocommerce ul.products li.product .woocommerce-product-details__short-description { display: none !important; }\n";
    }

    // Typography.
    $css .= ".woocommerce ul.products li.product .woocommerce-loop-product__title { font-size: {$title_size}px; color: {$title_color}; font-family: '{$title_font}', sans-serif; font-weight: {$title_weight}; text-transform: {$title_transform}; }\n";
    $css .= ".woocommerce ul.products li.product .price { font-size: {$price_size}px; color: {$price_color}; font-family: '{$price_font}', sans-serif; font-weight: {$price_weight}; }\n";
    $css .= ".woocommerce ul.products li.product .price ins { color: {$sale_price_color}; }\n";
    $css .= ".woocommerce ul.products li.product .woocommerce-product-details__short-description { color: {$desc_color}; font-family: '{$desc_font}', sans-serif; font-size: {$desc_size}px; line-height: {$desc_line_height}; }\n";

    // Button styling.
    $css .= ".woocommerce ul.products li.product .button { border-radius: {$button_radius}px; background-color: {$button_bg}; color: {$button_color}; }\n";

    if ( 'outline' === $button_style ) {
        $css .= ".woocommerce ul.products li.product .button { background-color: transparent; border: 2px solid {$button_bg}; color: {$button_bg}; }\n";
        $css .= ".woocommerce ul.products li.product .button:hover { color: {$button_hover_color}; background-color: {$button_bg}; }\n";
    } elseif ( 'text' === $button_style ) {
        $css .= ".woocommerce ul.products li.product .button { background-color: transparent; border: none; color: {$button_bg}; padding-left: 0; padding-right: 0; }\n";
        $css .= ".woocommerce ul.products li.product .button:hover { background-color: transparent; color: {$button_hover_color}; }\n";
    } else {
        $css .= ".woocommerce ul.products li.product .button:hover { background-color: {$button_hover_bg}; color: {$button_hover_color}; }\n";
    }

    // Sale badge.
    $css .= ".woocommerce ul.products li.product .onsale { background-color: {$sale_badge_bg}; color: {$sale_badge_color}; }\n";

    // Single product base.
    $css .= "body.single-product { background-color: {$single_bg}; }\n";
    $css .= "body.single-product .single-product-container { background-color: {$single_bg}; }\n";
    $css .= ".single-product div.product { max-width: {$single_content_width}%; margin-left: auto; margin-right: auto; }\n";

    if ( 'top' === $single_gallery_position ) {
        $css .= ".single-product div.product { grid-template-columns: 1fr; }\n";
    } elseif ( 'right' === $single_gallery_position ) {
        $css .= ".single-product div.product { direction: rtl; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }\n";
        $css .= ".single-product div.product > * { direction: ltr; }\n";
    } else {
        $css .= ".single-product div.product { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }\n";
    }
    $css .= ".single-product div.product div.images { border-radius: {$single_gallery_radius}px; }\n";

    if ( ! $single_show_thumbs ) {
        $css .= ".single-product div.product div.images .flex-control-nav { display: none !important; }\n";
    } else {
        if ( 'left' === $single_thumb_position ) {
            $css .= ".single-product div.product div.images .flex-control-nav { flex-direction: column; }\n";
        } elseif ( 'right' === $single_thumb_position ) {
            $css .= ".single-product div.product div.images .flex-control-nav { flex-direction: column; align-self: flex-end; }\n";
        }
    }

    if ( $single_sticky_summary ) {
        $css .= ".single-product .summary.entry-summary { position: sticky; top: 120px; align-self: flex-start; }\n";
    }

    // Single product typography.
    $css .= ".single-product .product_title { font-size: {$single_title_size}px; }\n";
    $css .= ".single-product .summary .price { font-size: {$single_price_size}px; }\n";

    // Single product meta toggles.
    if ( ! $single_show_sku ) {
        $css .= ".single-product .product_meta .sku_wrapper { display: none !important; }\n";
    }
    if ( ! $single_show_categories ) {
        $css .= ".single-product .product_meta .posted_in { display: none !important; }\n";
    }
    if ( ! $single_show_tags ) {
        $css .= ".single-product .product_meta .tagged_as { display: none !important; }\n";
    }

    if ( 'after-title' === $single_meta_position ) {
        $css .= ".single-product .product_meta { order: 1; }\n";
    } elseif ( 'after-price' === $single_meta_position ) {
        $css .= ".single-product .summary .price + .product_meta { margin-top: 15px; }\n";
    } else {
        $css .= ".single-product .product_meta { margin-top: 30px; }\n";
    }

    // Add to cart button.
    $css .= ".single-product .cart .button { height: {$single_button_height}px; font-size: {$single_button_font_size}px; border-radius: {$single_button_radius}px; }\n";

    if ( 'full' === $single_button_width ) {
        $css .= ".single-product .cart .button { width: 100%; }\n";
    } elseif ( 'auto' === $single_button_width ) {
        $css .= ".single-product .cart .button { width: auto; padding-left: 40px; padding-right: 40px; }\n";
    } else {
        $css .= ".single-product .cart .button { width: auto; }\n";
    }

    if ( ! $single_reviews_count ) {
        $css .= ".single-product .woocommerce-review-link { display: none !important; }\n";
    }

    // Tabs styling.
    if ( 'vertical' === $single_tab_style ) {
        $css .= ".single-product .woocommerce-tabs { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }\n";
        $css .= ".single-product .woocommerce-tabs ul.tabs { display: flex; flex-direction: column; border-right: 1px solid {$border_color}; border-radius: 6px 0 0 6px; }\n";
    } elseif ( 'accordion' === $single_tab_style ) {
        $css .= ".single-product .woocommerce-tabs ul.tabs { display: none; }\n";
        $css .= ".single-product .woocommerce-tabs .panel { border-top: 1px solid {$border_color}; padding-top: 15px; }\n";
    }

    if ( 'side' === $single_tab_position ) {
        $css .= ".single-product .woocommerce-tabs { display: grid; grid-template-columns: 280px 1fr; }\n";
    }

    // Gallery style helper classes.
    if ( 'slider' === $single_gallery_style ) {
        $css .= ".single-product div.product div.images { position: relative; }\n";
    } elseif ( 'grid' === $single_gallery_style ) {
        $css .= ".single-product div.product div.images { display: grid; gap: 15px; }\n";
        $css .= ".single-product div.product div.images figure { margin: 0; }\n";
    }

    if ( ! $single_lightbox ) {
        $css .= ".single-product div.product div.images a { pointer-events: none; }\n";
    }

    if ( $button_bg ) {
        $css .= ".single-product .cart .button { background-color: {$button_bg}; color: {$button_color}; }\n";
        $css .= ".single-product .cart .button:hover { background-color: {$button_hover_bg}; color: {$button_hover_color}; }\n";
    }

    if ( $css ) {
        wp_add_inline_style( 'mj-eleganza-style', $css );
    }
}
add_action( 'wp_enqueue_scripts', 'mj_eleganza_customizer_css', 30 );
