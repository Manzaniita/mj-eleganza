(function ($) {
    'use strict';

    if (typeof wp === 'undefined' || !wp.customize) {
        return;
    }

    const state = {};
    const styleId = 'mj-live-preview-styles';

    const ensureStyleTag = () => {
        let style = document.getElementById(styleId);
        if (!style) {
            style = document.createElement('style');
            style.id = styleId;
            document.head.appendChild(style);
        }
        return style;
    };

    const sanitizeColor = (value, fallback = '') => {
        if (typeof value !== 'string') {
            return fallback;
        }

        const color = value.trim();
        return /^#([0-9A-F]{3}){1,2}$/i.test(color) ? color : fallback;
    };

    const sanitizeFont = (value, fallback) => {
        if (typeof value !== 'string') {
            return fallback;
        }
        return value.replace(/[^a-zA-Z0-9\s-]/g, '') || fallback;
    };

    const numberOr = (value, fallback) => {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : fallback;
    };

    const flag = (value) => {
        return value === true || value === '1' || value === 1;
    };

    const updateButtonText = (text) => {
        $('.woocommerce ul.products li.product .button').each(function () {
            const $btn = $(this);
            const fallback = $btn.data('mjOriginalText') || $btn.text();
            if (!$btn.data('mjOriginalText')) {
                $btn.data('mjOriginalText', fallback);
            }
            $btn.text(text && text.length ? text : fallback);
        });
    };

    const render = () => {
        const cardStyle = state.mj_product_card_style || 'default';
        const cardRadius = numberOr(state.mj_product_card_border_radius, 5);
        const cardSpacing = numberOr(state.mj_product_card_spacing, 20);
        const cardShadow = flag(state.mj_product_card_shadow);
        const cardBg = sanitizeColor(state.mj_product_card_bg, '#1E1E1E');
        const cardBgHover = sanitizeColor(state.mj_product_card_bg_hover, '#252525');
        const borderColor = sanitizeColor(state.mj_product_border_color, '#333333');

        const titleSize = numberOr(state.mj_product_title_size, 16);
        const titleColor = sanitizeColor(state.mj_product_title_color, '#FFFFFF');
        const titleFont = sanitizeFont(state.mj_product_title_font, 'Montserrat');
        const titleWeight = numberOr(state.mj_product_title_weight, 600);
        const titleTransform = state.mj_product_title_transform || 'none';

        const priceSize = numberOr(state.mj_product_price_size, 18);
        const priceColor = sanitizeColor(state.mj_product_price_color, '#FFFFFF');
        const priceFont = sanitizeFont(state.mj_product_price_font, 'Montserrat');
        const priceWeight = numberOr(state.mj_product_price_weight, 600);

        const descColor = sanitizeColor(state.mj_product_desc_color, '#A0A0A0');
        const descFont = sanitizeFont(state.mj_product_desc_font, 'Open Sans');
        const descSize = numberOr(state.mj_product_desc_size, 15);
        const descLineHeight = numberOr(state.mj_product_desc_line_height, 1.6);

        const buttonBg = sanitizeColor(state.mj_product_button_bg, '#007BFF');
        const buttonColor = sanitizeColor(state.mj_product_button_color, '#FFFFFF');
        const buttonHoverBg = sanitizeColor(state.mj_product_button_hover_bg, '#0056B3');
        const buttonHoverColor = sanitizeColor(state.mj_product_button_hover_color, '#FFFFFF');
        const buttonRadius = numberOr(state.mj_product_button_radius, 5);
        const buttonStyle = state.mj_product_button_style || 'filled';
        const buttonPosition = state.mj_product_button_position || 'bottom';

        const imageRatio = state.mj_product_image_ratio || 'square';
        const imageHover = flag(state.mj_product_image_hover);
        const hoverEffect = state.mj_product_hover_effect || 'zoom';

        const showCategory = flag(state.mj_product_show_category);
        const showRating = flag(state.mj_product_show_rating);
        const showDesc = flag(state.mj_product_show_description);

        const salePriceColor = sanitizeColor(state.mj_product_sale_price_color, '#FF5A5F');
        const saleBadgeBg = sanitizeColor(state.mj_product_sale_badge_bg, '#FF5A5F');
        const saleBadgeColor = sanitizeColor(state.mj_product_sale_badge_color, '#FFFFFF');
        const ratingColor = sanitizeColor(state.mj_product_rating_color, '#FFC107');

        const singleBg = sanitizeColor(state.mj_single_product_bg, '#111111');
        const singleGalleryPosition = state.mj_single_gallery_position || 'left';
        const singleGalleryStyle = state.mj_single_gallery_style || 'default';
        const singleContentWidth = numberOr(state.mj_single_content_width, 80);
        const singleSticky = flag(state.mj_single_sticky_summary);
        const singleGalleryRadius = numberOr(state.mj_single_gallery_radius, 6);
        const singleShowThumbs = flag(state.mj_single_show_thumbnails);
        const singleThumbPosition = state.mj_single_thumbnail_position || 'bottom';
        const singleLightbox = flag(state.mj_single_enable_lightbox);
        const singleTitleSize = numberOr(state.mj_single_title_size, 32);
        const singlePriceSize = numberOr(state.mj_single_price_size, 24);
        const singleShowSku = flag(state.mj_single_show_sku);
        const singleShowCat = flag(state.mj_single_show_categories);
        const singleShowTags = flag(state.mj_single_show_tags);
        const singleMetaPosition = state.mj_single_meta_position || 'after-price';
        const singleButtonHeight = numberOr(state.mj_single_button_height, 50);
        const singleButtonFontSize = numberOr(state.mj_single_button_font_size, 16);
        const singleButtonWidth = state.mj_single_button_width || 'full';
        const singleButtonRadius = numberOr(state.mj_single_button_radius, 6);
        const singleTabStyle = state.mj_single_tab_style || 'default';
        const singleTabPosition = state.mj_single_tab_position || 'below';
        const singleReviewsCount = flag(state.mj_single_show_reviews_count);

        let css = '';

        css += `.woocommerce ul.products { gap: ${cardSpacing}px; }`;
        css += `.woocommerce ul.products li.product { border-radius: ${cardRadius}px; background-color: ${cardBg}; overflow: hidden; }`;

        if (cardStyle === 'minimal') {
            css += `.woocommerce ul.products li.product { background-color: transparent; border: none; box-shadow: none; }`;
        } else if (cardStyle === 'boxed') {
            css += `.woocommerce ul.products li.product { border: 1px solid ${borderColor}; box-shadow: none; }`;
        }

        if (cardShadow || cardStyle === 'elevated') {
            css += `.woocommerce ul.products li.product { box-shadow: 0 10px 30px rgba(0,0,0,0.2); }`;
        }

        css += `.woocommerce ul.products li.product:hover { background-color: ${cardBgHover}; }`;

        if (buttonPosition === 'hover') {
            css += `.woocommerce ul.products li.product .button { opacity: 0; transform: translateY(10px); transition: all 0.3s ease; }`;
            css += `.woocommerce ul.products li.product:hover .button { opacity: 1; transform: translateY(0); }`;
        } else if (buttonPosition === 'overlay') {
            css += `.woocommerce ul.products li.product { position: relative; }`;
            css += `.woocommerce ul.products li.product .button { position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%); width: calc(100% - 40px); }`;
        }

        if (imageRatio === 'portrait') {
            css += `.woocommerce ul.products li.product a img { aspect-ratio: 3 / 4; object-fit: cover; }`;
        } else if (imageRatio === 'landscape') {
            css += `.woocommerce ul.products li.product a img { aspect-ratio: 4 / 3; object-fit: cover; }`;
        } else {
            css += `.woocommerce ul.products li.product a img { aspect-ratio: 1 / 1; object-fit: cover; }`;
        }

        if (imageHover) {
            if (hoverEffect === 'fade') {
                css += `.woocommerce ul.products li.product a img { transition: opacity 0.4s ease; }`;
                css += `.woocommerce ul.products li.product:hover a img { opacity: 0.85; }`;
            } else if (hoverEffect === 'slide') {
                css += `.woocommerce ul.products li.product a img { transition: transform 0.4s ease; }`;
                css += `.woocommerce ul.products li.product:hover a img { transform: translateY(-6px); }`;
            } else {
                css += `.woocommerce ul.products li.product a img { transition: transform 0.4s ease; }`;
                css += `.woocommerce ul.products li.product:hover a img { transform: scale(1.05); }`;
            }
        }

        if (!showCategory) {
            css += `.woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .posted_in { display: none !important; }`;
        }

        if (!showRating) {
            css += `.woocommerce ul.products li.product .star-rating { display: none !important; }`;
        } else {
            css += `.woocommerce ul.products li.product .star-rating { color: ${ratingColor}; }`;
        }

        if (!showDesc) {
            css += `.woocommerce ul.products li.product .woocommerce-product-details__short-description { display: none !important; }`;
        }

        css += `.woocommerce ul.products li.product .woocommerce-loop-product__title { font-size: ${titleSize}px; color: ${titleColor}; font-family: '${titleFont}', sans-serif; font-weight: ${titleWeight}; text-transform: ${titleTransform}; }`;
        css += `.woocommerce ul.products li.product .price { font-size: ${priceSize}px; color: ${priceColor}; font-family: '${priceFont}', sans-serif; font-weight: ${priceWeight}; }`;
        css += `.woocommerce ul.products li.product .price ins { color: ${salePriceColor}; }`;
        css += `.woocommerce ul.products li.product .woocommerce-product-details__short-description { color: ${descColor}; font-family: '${descFont}', sans-serif; font-size: ${descSize}px; line-height: ${descLineHeight}; }`;

        css += `.woocommerce ul.products li.product .button { border-radius: ${buttonRadius}px; background-color: ${buttonBg}; color: ${buttonColor}; }`;

        if (buttonStyle === 'outline') {
            css += `.woocommerce ul.products li.product .button { background-color: transparent; border: 2px solid ${buttonBg}; color: ${buttonBg}; }`;
            css += `.woocommerce ul.products li.product .button:hover { color: ${buttonHoverColor}; background-color: ${buttonBg}; }`;
        } else if (buttonStyle === 'text') {
            css += `.woocommerce ul.products li.product .button { background-color: transparent; border: none; color: ${buttonBg}; padding-left: 0; padding-right: 0; }`;
            css += `.woocommerce ul.products li.product .button:hover { color: ${buttonHoverColor}; }`;
        } else {
            css += `.woocommerce ul.products li.product .button:hover { background-color: ${buttonHoverBg}; color: ${buttonHoverColor}; }`;
        }

        css += `.woocommerce ul.products li.product .onsale { background-color: ${saleBadgeBg}; color: ${saleBadgeColor}; }`;

    css += `body.single-product { background-color: ${singleBg}; }`;
    css += `body.single-product .single-product-container { background-color: ${singleBg}; }`;
        css += `.single-product div.product { max-width: ${singleContentWidth}%; margin-left: auto; margin-right: auto; }`;

        if (singleGalleryPosition === 'top') {
            css += `.single-product div.product { grid-template-columns: 1fr; }`;
        } else if (singleGalleryPosition === 'right') {
            css += `.single-product div.product { direction: rtl; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }`;
            css += `.single-product div.product > * { direction: ltr; }`;
        } else {
            css += `.single-product div.product { direction: ltr; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }`;
        }

        css += `.single-product div.product div.images { border-radius: ${singleGalleryRadius}px; }`;

        if (!singleShowThumbs) {
            css += `.single-product div.product div.images .flex-control-nav { display: none !important; }`;
        } else {
            if (singleThumbPosition === 'left') {
                css += `.single-product div.product div.images .flex-control-nav { flex-direction: column; }`;
            } else if (singleThumbPosition === 'right') {
                css += `.single-product div.product div.images .flex-control-nav { flex-direction: column; align-self: flex-end; }`;
            }
        }

        if (singleSticky) {
            css += `.single-product .summary.entry-summary { position: sticky; top: 120px; align-self: flex-start; }`;
        } else {
            css += `.single-product .summary.entry-summary { position: static; }`;
        }

        css += `.single-product .product_title { font-size: ${singleTitleSize}px; }`;
        css += `.single-product .summary .price { font-size: ${singlePriceSize}px; }`;

        if (!singleShowSku) {
            css += `.single-product .product_meta .sku_wrapper { display: none !important; }`;
        }
        if (!singleShowCat) {
            css += `.single-product .product_meta .posted_in { display: none !important; }`;
        }
        if (!singleShowTags) {
            css += `.single-product .product_meta .tagged_as { display: none !important; }`;
        }

        if (singleMetaPosition === 'after-title') {
            css += `.single-product .product_meta { order: 1; }`;
        } else if (singleMetaPosition === 'bottom') {
            css += `.single-product .product_meta { margin-top: 30px; }`;
        } else {
            css += `.single-product .summary .price + .product_meta { margin-top: 15px; }`;
        }

        css += `.single-product .cart .button { height: ${singleButtonHeight}px; font-size: ${singleButtonFontSize}px; border-radius: ${singleButtonRadius}px; background-color: ${buttonBg}; color: ${buttonColor}; }`;

        if (singleButtonWidth === 'full') {
            css += `.single-product .cart .button { width: 100%; }`;
        } else if (singleButtonWidth === 'auto') {
            css += `.single-product .cart .button { width: auto; padding-left: 40px; padding-right: 40px; }`;
        } else {
            css += `.single-product .cart .button { width: auto; }`;
        }

        css += `.single-product .cart .button:hover { background-color: ${buttonHoverBg}; color: ${buttonHoverColor}; }`;

        if (!singleReviewsCount) {
            css += `.single-product .woocommerce-review-link { display: none !important; }`;
        }

        if (singleTabStyle === 'vertical') {
            css += `.single-product .woocommerce-tabs { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }`;
            css += `.single-product .woocommerce-tabs ul.tabs { display: flex; flex-direction: column; border-right: 1px solid ${borderColor}; border-radius: 6px 0 0 6px; }`;
        } else if (singleTabStyle === 'accordion') {
            css += `.single-product .woocommerce-tabs ul.tabs { display: none; }`;
            css += `.single-product .woocommerce-tabs .panel { border-top: 1px solid ${borderColor}; padding-top: 15px; }`;
        }

        if (singleTabPosition === 'side') {
            css += `.single-product .woocommerce-tabs { display: grid; grid-template-columns: 280px 1fr; }`;
        }

        if (singleGalleryStyle === 'slider') {
            css += `.single-product div.product div.images { position: relative; }`;
        } else if (singleGalleryStyle === 'grid') {
            css += `.single-product div.product div.images { display: grid; gap: 15px; }`;
            css += `.single-product div.product div.images figure { margin: 0; }`;
        }

        if (!singleLightbox) {
            css += `.single-product div.product div.images a { pointer-events: none; }`;
        } else {
            css += `.single-product div.product div.images a { pointer-events: auto; }`;
        }

        const body = document.body;
        if (body) {
            const swapClass = (prefix, value, allowed) => {
                allowed.forEach((slug) => body.classList.remove(`${prefix}${slug}`));
                if (value && allowed.includes(value)) {
                    body.classList.add(`${prefix}${value}`);
                }
            };

            swapClass('mj-card-style-', cardStyle, ['default', 'minimal', 'boxed', 'elevated']);
            swapClass('mj-button-style-', buttonStyle, ['filled', 'outline', 'text']);
            swapClass('mj-button-position-', buttonPosition, ['bottom', 'overlay', 'hover']);
            swapClass('mj-image-ratio-', imageRatio, ['square', 'portrait', 'landscape']);
            swapClass('mj-image-hover-effect-', imageHover ? hoverEffect : '', ['zoom', 'fade', 'slide']);
            swapClass('mj-single-gallery-', singleGalleryPosition, ['left', 'right', 'top']);
            swapClass('mj-single-gallery-style-', singleGalleryStyle, ['default', 'slider', 'grid']);
            swapClass('mj-single-tab-style-', singleTabStyle, ['default', 'vertical', 'accordion']);
            swapClass('mj-single-tab-position-', singleTabPosition, ['below', 'side']);

            body.classList.toggle('mj-image-hover-enabled', imageHover);
            body.classList.toggle('mj-single-sticky-summary', singleSticky);
            body.classList.toggle('mj-single-thumbs-enabled', singleShowThumbs);
            body.classList.toggle('mj-single-lightbox-enabled', singleLightbox);
        }

        const style = ensureStyleTag();
        style.textContent = css;
    };

    const bindSetting = (id, callback) => {
        if (!wp.customize(id)) {
            return;
        }

        state[id] = wp.customize(id)();

        wp.customize(id, (value) => {
            value.bind((newval) => {
                state[id] = newval;
                render();
                if (typeof callback === 'function') {
                    callback(newval);
                }
            });
        });
    };

    const settings = [
        'mj_product_card_style',
        'mj_product_card_border_radius',
        'mj_product_card_spacing',
        'mj_product_card_shadow',
        'mj_product_card_bg',
        'mj_product_card_bg_hover',
        'mj_product_border_color',
        'mj_product_title_size',
        'mj_product_title_color',
        'mj_product_title_font',
        'mj_product_title_weight',
        'mj_product_title_transform',
        'mj_product_price_size',
        'mj_product_price_color',
        'mj_product_price_font',
        'mj_product_price_weight',
        'mj_product_desc_color',
        'mj_product_desc_font',
        'mj_product_desc_size',
        'mj_product_desc_line_height',
        'mj_product_button_bg',
        'mj_product_button_color',
        'mj_product_button_hover_bg',
        'mj_product_button_hover_color',
        'mj_product_button_radius',
        'mj_product_button_style',
        'mj_product_button_position',
        'mj_product_image_ratio',
        'mj_product_image_hover',
        'mj_product_hover_effect',
        'mj_product_show_category',
        'mj_product_show_rating',
        'mj_product_show_description',
        'mj_product_sale_price_color',
        'mj_product_sale_badge_bg',
        'mj_product_sale_badge_color',
        'mj_product_rating_color',
        'mj_single_product_bg',
        'mj_single_gallery_position',
        'mj_single_gallery_style',
        'mj_single_content_width',
        'mj_single_sticky_summary',
        'mj_single_gallery_radius',
        'mj_single_show_thumbnails',
        'mj_single_thumbnail_position',
        'mj_single_enable_lightbox',
        'mj_single_title_size',
        'mj_single_price_size',
        'mj_single_show_sku',
        'mj_single_show_categories',
        'mj_single_show_tags',
        'mj_single_meta_position',
        'mj_single_button_height',
        'mj_single_button_font_size',
        'mj_single_button_width',
        'mj_single_button_radius',
        'mj_single_tab_style',
        'mj_single_tab_position',
        'mj_single_show_reviews_count'
    ];

    settings.forEach((setting) => bindSetting(setting));

    bindSetting('mj_product_button_text', updateButtonText);

    // Initial render for current values.
    render();
    updateButtonText(state.mj_product_button_text || '');
})(jQuery);
