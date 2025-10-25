(function ($, wp) {
    'use strict';

    if ( typeof MJEleganzaShop === 'undefined' ) {
        return;
    }

    const { __ } = wp && wp.i18n ? wp.i18n : { __: ( str ) => str };

    const storageKeys = {
        wishlist: 'mjEleganzaWishlist',
        compare: 'mjEleganzaCompare'
    };

    const Shop = {
        state: {
            view: localStorage.getItem( 'mjShopView' ) || 'grid',
            wishlist: new Set(),
            compare: new Set(),
            quickViewOpen: false
        },

        init() {
            this.bootstrapSets();
            this.cacheElements();
            this.bindEvents();
            this.applyInitialView();
            this.syncActionButtons();
            this.prefillFilters();
        },

        cacheElements() {
            this.$body = $( document.body );
            this.$wrapper = $( '.mj-shop-wrapper' );
            this.$productsWrapper = $( '#mj-products-wrapper' );
            this.$viewToggleButtons = $( '.mj-view-toggle__btn' );
            this.$perPageSelect = $( '#mj-products-per-page' );
            this.$filterSidebar = $( '#mj-shop-sidebar' );
            this.$toggleFilters = $( '.mj-toggle-filters' );
            this.$closeFilters = $( '.mj-close-sidebar' );
            this.$loading = $( '#mj-shop-loading' );
        },

        bindEvents() {
            const self = this;

            $( document ).on( 'click', '.mj-view-toggle__btn', function ( event ) {
                event.preventDefault();
                const view = $( this ).data( 'view' );
                self.toggleView( view );
            } );

            this.$perPageSelect.on( 'change', function () {
                $( document ).trigger( 'mj:filters:submit', [{ perPage: parseInt( this.value, 10 ) }] );
            } );

            this.$toggleFilters.on( 'click', () => {
                this.toggleSidebar( true );
            } );

            this.$closeFilters.on( 'click', () => {
                this.toggleSidebar( false );
            } );

            $( document ).on( 'keyup', ( event ) => {
                if ( event.key === 'Escape' ) {
                    this.toggleSidebar( false );
                    if ( this.state.quickViewOpen ) {
                        this.closeQuickView();
                    }
                }
            } );

            $( document ).on( 'click', '.mj-product-card__action[data-action="quick-view"]', ( event ) => {
                event.preventDefault();
                const productId = $( event.currentTarget ).data( 'product-id' );
                this.openQuickView( productId );
            } );

            $( document ).on( 'click', '.mj-product-card__action[data-action="wishlist"]', ( event ) => {
                event.preventDefault();
                const $button = $( event.currentTarget );
                this.toggleStorage( 'wishlist', $button.data( 'product-id' ) );
                this.syncActionButtons();
            } );

            $( document ).on( 'click', '.mj-product-card__action[data-action="compare"]', ( event ) => {
                event.preventDefault();
                const $button = $( event.currentTarget );
                this.toggleStorage( 'compare', $button.data( 'product-id' ) );
                this.syncActionButtons();
            } );

            $( document ).on( 'click', '#mj-quick-view-modal [data-role="close"]', ( event ) => {
                event.preventDefault();
                this.closeQuickView();
            } );

            $( document ).on( 'click', '#mj-quick-view-modal .mj-quick-view__thumb', ( event ) => {
                event.preventDefault();
                const $thumb = $( event.currentTarget );
                const fullImage = $thumb.data( 'full' );
                const $target = $( '#mj-quick-view-modal .mj-quick-view__image img' );
                if ( fullImage && $target.length ) {
                    $target.attr( 'src', fullImage );
                }
            } );

            $( document ).on( 'click', '#mj-quick-view-modal .mj-quick-view__overlay', ( event ) => {
                event.preventDefault();
                this.closeQuickView();
            } );

            $( document ).on( 'mj:filters:before', () => {
                this.$loading.removeAttr( 'hidden' );
            } );

            $( document ).on( 'mj:filters:after', () => {
                this.$loading.attr( 'hidden', 'hidden' );
                this.cacheElements();
                this.applyInitialView();
                this.syncActionButtons();
            } );
        },

        bootstrapSets() {
            this.state.wishlist = this.loadSet( storageKeys.wishlist );
            this.state.compare = this.loadSet( storageKeys.compare );
        },

        loadSet( key ) {
            try {
                const data = JSON.parse( localStorage.getItem( key ) || '[]' );
                return new Set( Array.isArray( data ) ? data : [] );
            } catch ( error ) {
                console.error( 'MJ Eleganza storage parse error', error );
                return new Set();
            }
        },

        persistSet( key, set ) {
            localStorage.setItem( key, JSON.stringify( Array.from( set ) ) );
        },

        toggleStorage( type, productId ) {
            if ( ! productId ) {
                return;
            }
            const setKey = type === 'compare' ? storageKeys.compare : storageKeys.wishlist;
            const bucket = type === 'compare' ? this.state.compare : this.state.wishlist;

            if ( bucket.has( productId ) ) {
                bucket.delete( productId );
            } else {
                bucket.add( productId );
            }

            this.persistSet( setKey, bucket );

            document.dispatchEvent( new CustomEvent( 'mj:' + type + ':updated', {
                detail: { items: Array.from( bucket ) }
            } ) );
        },

        toggleView( view ) {
            if ( ! view ) {
                return;
            }
            this.state.view = view;
            localStorage.setItem( 'mjShopView', view );
            this.updateViewUI();
        },

        applyInitialView() {
            this.updateViewUI();
        },

        updateViewUI() {
            const view = this.state.view;
            this.$viewToggleButtons.removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
            this.$viewToggleButtons.filter( `[data-view="${view}"]` ).addClass( 'is-active' ).attr( 'aria-pressed', 'true' );

            const $grid = $( '.mj-products-grid' );
            if ( view === 'list' ) {
                $grid.addClass( 'view-list' );
                $grid.find( '.mj-product-card' ).addClass( 'view-list' );
            } else {
                $grid.removeClass( 'view-list' );
                $grid.find( '.mj-product-card' ).removeClass( 'view-list' );
            }
        },

        toggleSidebar( show ) {
            if ( show ) {
                this.$filterSidebar.addClass( 'is-visible' );
                this.$body.addClass( 'mj-lock-scroll' );
            } else {
                this.$filterSidebar.removeClass( 'is-visible' );
                this.$body.removeClass( 'mj-lock-scroll' );
            }
        },

        syncActionButtons() {
            $( '.mj-product-card__action[data-action="wishlist"]' ).each( ( _index, element ) => {
                const $el = $( element );
                const productId = $el.data( 'product-id' );
                if ( this.state.wishlist.has( productId ) ) {
                    $el.addClass( 'is-active' ).attr( 'aria-pressed', 'true' );
                    $el.attr( 'title', __( 'Eliminar de favoritos', 'mjeleganza' ) );
                } else {
                    $el.removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
                    $el.attr( 'title', __( 'Añadir a favoritos', 'mjeleganza' ) );
                }
            } );

            $( '.mj-product-card__action[data-action="compare"]' ).each( ( _index, element ) => {
                const $el = $( element );
                const productId = $el.data( 'product-id' );
                if ( this.state.compare.has( productId ) ) {
                    $el.addClass( 'is-active' ).attr( 'aria-pressed', 'true' );
                    $el.attr( 'title', __( 'Eliminar de comparar', 'mjeleganza' ) );
                } else {
                    $el.removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
                    $el.attr( 'title', __( 'Añadir a comparar', 'mjeleganza' ) );
                }
            } );
        },

        openQuickView( productId ) {
            if ( ! productId ) {
                return;
            }

            const $modal = $( '#mj-quick-view-modal' );
            const $content = $( '#mj-quick-view-render' );
            const $loader = $modal.find( '.mj-quick-view__loader' );

            $modal.removeAttr( 'hidden' );
            this.state.quickViewOpen = true;
            $loader.show();
            $content.empty();

            $.post( MJEleganzaShop.ajaxUrl, {
                action: 'mj_quick_view',
                nonce: MJEleganzaShop.nonce,
                product_id: productId
            } )
                .done( ( response ) => {
                    if ( response && response.success && response.data && response.data.html ) {
                        $content.html( response.data.html );
                    } else {
                        $content.html( `<p class="woocommerce-info">${MJEleganzaShop.strings.quickViewError}</p>` );
                    }
                } )
                .fail( () => {
                    $content.html( `<p class="woocommerce-info">${MJEleganzaShop.strings.quickViewError}</p>` );
                } )
                .always( () => {
                    $loader.hide();
                } );
        },

        closeQuickView() {
            $( '#mj-quick-view-modal' ).attr( 'hidden', 'hidden' );
            $( '#mj-quick-view-render' ).empty();
            this.state.quickViewOpen = false;
        },

        prefillFilters() {
            const filters = MJEleganzaShop.initialFilters || {};
            const sidebar = document.getElementById( 'mj-shop-sidebar' );
            if ( ! sidebar ) {
                return;
            }

            if ( Array.isArray( filters.categories ) ) {
                filters.categories.forEach( ( slug ) => {
                    sidebar.querySelectorAll( `input[type="checkbox"][name="product_cat[]"][value="${slug}"]` ).forEach( ( input ) => {
                        input.checked = true;
                    } );
                } );
            }

            if ( typeof filters.min_price !== 'undefined' && filters.min_price !== '' ) {
                const input = sidebar.querySelector( 'input[name="min_price"]' );
                if ( input ) {
                    input.value = filters.min_price;
                }
            }

            if ( typeof filters.max_price !== 'undefined' && filters.max_price !== '' ) {
                const input = sidebar.querySelector( 'input[name="max_price"]' );
                if ( input ) {
                    input.value = filters.max_price;
                }
            }

            if ( filters.on_sale ) {
                const input = sidebar.querySelector( 'input[name="on_sale"]' );
                if ( input ) {
                    input.checked = true;
                }
            }

            if ( Array.isArray( filters.stock_status ) ) {
                filters.stock_status.forEach( ( status ) => {
                    sidebar.querySelectorAll( `input[name="stock_status[]"][value="${status}"]` ).forEach( ( input ) => {
                        input.checked = true;
                    } );
                } );
            }

            if ( typeof filters.rating_filter !== 'undefined' ) {
                const input = sidebar.querySelector( `input[name="rating_filter"][value="${filters.rating_filter}"]` );
                if ( input ) {
                    input.checked = true;
                }
            }

            if ( filters.per_page && this.$perPageSelect.length ) {
                this.$perPageSelect.val( filters.per_page );
            }
        }
    };

    $( () => Shop.init() );
})( jQuery, window.wp || {} );
