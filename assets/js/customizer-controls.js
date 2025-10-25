(function (wp, $) {
    'use strict';

    if (!wp || !wp.customize) {
        return;
    }

    const toggleControl = (settingId, controlIds, predicate) => {
        const controlList = Array.isArray(controlIds) ? controlIds : [controlIds];
        const evaluate = (value) => {
            const shouldShow = typeof predicate === 'function' ? predicate(value) : value === predicate;
            controlList.forEach((controlId) => {
                const control = wp.customize.control(controlId);
                if (!control || !control.container) {
                    return;
                }

                control.container.toggle(shouldShow);
            });
        };

        const setting = wp.customize(settingId);
        if (!setting) {
            return;
        }

        evaluate(setting());
        setting.bind((newVal) => evaluate(newVal));
    };

    const bindRangePreview = (controlId) => {
        const control = wp.customize.control(controlId);
        if (!control || !control.container) {
            return;
        }

        const $input = control.container.find('input[type="range"]');
        if (!$input.length) {
            return;
        }

        const $badge = $('<span class="mj-range-value" />');
        $input.after($badge);

        const update = (value) => {
            $badge.text(`${value}${$input.data('unit') || ''}`);
        };

        update($input.val());
        $input.on('input change', function () {
            update($(this).val());
        });
    };

    const flag = (value) => value === true || value === '1' || value === 1;

    wp.customize.bind('ready', function () {
        // Hover effect depends on image hover flag.
        toggleControl('mj_product_image_hover', 'mj_product_hover_effect', (val) => flag(val));

        // Description slider valued badges.
        [
            'mj_product_card_border_radius',
            'mj_product_card_spacing',
            'mj_product_title_size',
            'mj_product_price_size',
            'mj_product_button_radius',
            'mj_single_content_width',
            'mj_single_gallery_radius',
            'mj_single_title_size',
            'mj_single_price_size',
            'mj_single_button_height',
            'mj_single_button_font_size',
            'mj_single_button_radius',
            'mj_product_title_weight',
            'mj_product_price_weight',
            'mj_product_desc_size',
            'mj_product_desc_line_height'
        ].forEach(bindRangePreview);

        // Thumbnail position only visible if thumbnails active.
        toggleControl('mj_single_show_thumbnails', 'mj_single_thumbnail_position', (val) => flag(val));

        // Lightbox toggle hidden when gallery style is grid (no modal gallery).
        toggleControl('mj_single_gallery_style', 'mj_single_enable_lightbox', (val) => val !== 'grid');

        // Button position overlay requires radius control visible but no extra toggles.
    toggleControl('mj_product_button_style', 'mj_product_button_hover_bg', (val) => val !== 'text');

        // Display rating color only when rating shown.
        toggleControl('mj_product_show_rating', 'mj_product_rating_color', (val) => flag(val));
    });
})(window.wp, jQuery);
