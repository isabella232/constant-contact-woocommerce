/**
 * Constant Contact WooCommerce Admin
 *
 * @package WebDevStudios\CCForWoo
 * @since   2019-10-24
 */
window.ccWooAdmin = {};

( function( app ) {

    /**
     * Init the admin functionality.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since   2019-10-24
     */
    app.init = function() {
        app.cacheEls();
        app.bindEvents();
    };

    /**
     * Cache DOM els.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since 2019-10-24
     */
    app.cacheEls = function() {
        app.els = {};

        app.els.input = document.getElementById( 'cc_woo_abandoned_carts_secret_key' );
        app.els.button = document.getElementById( 'cc_woo_abandoned_carts_generate_secret_key' );
    };

    /**
     * Bind callbacks to events.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since 2019-10-24
     */
    app.bindEvents = function() {

        // Generate new key.
        app.els.button.addEventListener( 'click', function( e ) {
            e.preventDefault();
            app.generateKey();
        } );
    };

    /**
     * Gets a new key.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since 2019-10-24
     */
    app.generateKey = function() {
        wp.ajax.send( 'cc_woo_abandoned_carts_generate_secret_key', {
            data: {
                nonce: app.els.button.getAttribute( 'data-wp-nonce' )
            },
            success: app.handleGenerateKeySuccess,
            error: app.handleGenerateKeyError
        } );
    };

    /**
     * Handle a successful generation of new secret key.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since 2019-10-24
     */
    app.handleGenerateKeySuccess = function( data ) {
        app.els.input.value = data.key;
    };

    /**
     * Handle a failed generation of new secret key.
     *
     * @author George Gecewicz <george.gecewicz@webdevstudios.com>
     * @since 2019-10-24
     */
    app.handleGenerateKeyError = function( data ) {

    };

    app.init();

} ( window.ccWooAdmin ) );