(function ($) {
    'use strict';

    if ( typeof MJEleganzaShop === 'undefined' ) {
        return;
    }

    const Filters = {
        init() {
            this.$sidebar = $( '#mj-shop-sidebar' );
            this.$apply = $( '#mj-apply-filters' );
            this.$clear = $( '#mj-clear-filters' );
            this.$activeFilters = $( '#mj-active-filters' );
            this.$activeTags = this.$activeFilters.find( '.mj-active-filters__tags' );
            this.$toolbarLeft = $( '.mj-shop-toolbar__left' );
            this.$productsWrapper = $( '#mj-products-wrapper' );
            this.$shopMain = $( '#mj-shop-main' );

            this.bindEvents();
        },

        bindEvents() {
            this.$apply.on( 'click', ( event ) => {
                event.preventDefault();
                this.submit();
            } );

            this.$clear.on( 'click', ( event ) => {
                event.preventDefault();
                this.resetFilters();
                this.submit();
            } );

            $( document ).on( 'submit', '.woocommerce-ordering', ( event ) => {
                event.preventDefault();
            } );

            $( document ).on( 'change', '.woocommerce-ordering select', ( event ) => {
                event.preventDefault();
                this.submit({ orderby: event.currentTarget.value });
            } );

            $( document ).on( 'click', '#mj-shop-main .woocommerce-pagination a', ( event ) => {
                event.preventDefault();
                const href = event.currentTarget.getAttribute( 'href' );
                const paged = this.parsePageFromUrl( href );
                this.submit({ paged: paged });
            } );

            $( document ).on( 'click', '.mj-filter-chip', ( event ) => {
                event.preventDefault();
                const button = event.currentTarget;
                const type = button.getAttribute( 'data-filter-type' );
                const value = button.getAttribute( 'data-filter-value' );
                this.removeChipFilter( type, value );
                this.submit();
            } );

            $( document ).on( 'mj:filters:submit', ( _event, payload ) => {
                this.submit( payload || {} );
            } );
        },

        collectFilters() {
            const filters = {};
            const categories = [];
            this.$sidebar.find( 'input[name="product_cat[]"]:checked' ).each( ( _index, element ) => {
                categories.push( element.value );
            } );
            filters.categories = categories;

            const minPrice = this.$sidebar.find( 'input[name="min_price"]' ).val();
            const maxPrice = this.$sidebar.find( 'input[name="max_price"]' ).val();
            filters.min_price = minPrice ? parseFloat( minPrice ) : '';
            filters.max_price = maxPrice ? parseFloat( maxPrice ) : '';

            filters.on_sale = this.$sidebar.find( 'input[name="on_sale"]' ).is( ':checked' ) ? 1 : 0;

            const stockStatus = [];
            this.$sidebar.find( 'input[name="stock_status[]"]:checked' ).each( ( _index, element ) => {
                stockStatus.push( element.value );
            } );
            filters.stock_status = stockStatus;

            const rating = this.$sidebar.find( 'input[name="rating_filter"]:checked' ).val();
            filters.rating_filter = rating ? parseInt( rating, 10 ) : 0;

            return filters;
        },

        submit( overrides = {} ) {
            const filters = this.collectFilters();
            const orderby = overrides.orderby || this.getOrderby();
            const perPage = overrides.perPage || this.getPerPage();
            const paged = overrides.paged || 1;

            $( document ).trigger( 'mj:filters:before' );

            $.post( MJEleganzaShop.ajaxUrl, {
                action: 'mj_filter_products',
                nonce: MJEleganzaShop.nonce,
                filters: filters,
                orderby: orderby,
                per_page: perPage,
                paged: paged
            } )
                .done( ( response ) => {
                    if ( response && response.success && response.data ) {
                        this.renderResponse( response.data, filters, { orderby, perPage, paged } );
                    }
                } )
                .always( () => {
                    $( document ).trigger( 'mj:filters:after' );
                } );
        },

        renderResponse( data, filters, meta ) {
            if ( data.products ) {
                this.$productsWrapper.html( data.products );
            }

            if ( data.toolbar ) {
                this.$toolbarLeft.html( data.toolbar );
            }

            if ( meta && meta.perPage ) {
                const perPageSelect = document.getElementById( 'mj-products-per-page' );
                if ( perPageSelect ) {
                    perPageSelect.value = meta.perPage;
                }
            }

            if ( data.pagination ) {
                const $paginationTarget = this.$shopMain.find( '.woocommerce-pagination' );
                if ( $paginationTarget.length ) {
                    $paginationTarget.replaceWith( data.pagination );
                } else {
                    this.$shopMain.append( data.pagination );
                }
            }

            if ( data.active_filters ) {
                this.$activeFilters.removeAttr( 'hidden' );
                this.$activeTags.html( data.active_filters );
            } else {
                this.$activeFilters.attr( 'hidden', 'hidden' );
                this.$activeTags.empty();
            }

            this.updateHistory( filters, meta );
        },

        resetFilters() {
            this.$sidebar.find( 'input[type="checkbox"]' ).prop( 'checked', false );
            this.$sidebar.find( 'input[name="rating_filter"][value="0"]' ).prop( 'checked', true );
            this.$sidebar.find( 'input[type="number"]' ).val( '' );
        },

        removeChipFilter( type, value ) {
            switch ( type ) {
                case 'category':
                    this.$sidebar.find( `input[name="product_cat[]"][value="${value}"]` ).prop( 'checked', false );
                    break;
                case 'price':
                    this.$sidebar.find( 'input[name="min_price"], input[name="max_price"]' ).val( '' );
                    break;
                case 'rating_filter':
                    this.$sidebar.find( 'input[name="rating_filter"][value="0"]' ).prop( 'checked', true );
                    break;
                case 'on_sale':
                    this.$sidebar.find( 'input[name="on_sale"]' ).prop( 'checked', false );
                    break;
                case 'stock_status':
                    this.$sidebar.find( `input[name="stock_status[]"][value="${value}"]` ).prop( 'checked', false );
                    break;
                default:
                    break;
            }
        },

        parsePageFromUrl( url ) {
            if ( ! url ) {
                return 1;
            }
            try {
                const parsed = new URL( url, window.location.origin );
                const paged = parsed.searchParams.get( 'paged' ) || parsed.searchParams.get( 'page' );
                return paged ? parseInt( paged, 10 ) : 1;
            } catch ( error ) {
                return 1;
            }
        },

        getOrderby() {
            const ordering = document.querySelector( '.woocommerce-ordering select' );
            return ordering ? ordering.value : '';
        },

        getPerPage() {
            const select = document.querySelector( '#mj-products-per-page' );
            return select ? parseInt( select.value, 10 ) : 12;
        },

        updateHistory( filters, meta ) {
            const params = new URLSearchParams( window.location.search );

            params.delete( 'product_cat' );
            filters.categories.forEach( ( slug ) => {
                params.append( 'product_cat', slug );
            } );

            this.setOrDelete( params, 'min_price', filters.min_price );
            this.setOrDelete( params, 'max_price', filters.max_price );
            this.setOrDelete( params, 'on_sale', filters.on_sale ? 1 : '' );

            params.delete( 'stock_status' );
            filters.stock_status.forEach( ( status ) => {
                params.append( 'stock_status', status );
            } );

            this.setOrDelete( params, 'rating_filter', filters.rating_filter && filters.rating_filter > 0 ? filters.rating_filter : '' );
            this.setOrDelete( params, 'orderby', meta.orderby );
            this.setOrDelete( params, 'per_page', meta.perPage );
            this.setOrDelete( params, 'paged', meta.paged > 1 ? meta.paged : '' );

            const newQuery = params.toString();
            const url = newQuery ? `${window.location.pathname}?${newQuery}` : window.location.pathname;
            window.history.replaceState( {}, '', url );
        },

        setOrDelete( params, key, value ) {
            if ( value === '' || value === undefined || value === null ) {
                params.delete( key );
            } else {
                params.set( key, value );
            }
        }
    };

    $( () => Filters.init() );
})( jQuery );
