<?php
/**
 * CCforWoo REST Registrar.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest
 * @since   2019-11-13
 */

namespace WebDevStudios\CCForWoo\Rest;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class Registrar
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest
 * @since   2019-11-13
 */
class Registrar extends Service {

    /**
	 * Namespace for the endpoints this registrar registers.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
    public static $namespace = 'wc/cc-woo';

	/**
	 * Register hooks.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-11-13
	 */
	public function register_hooks() {
        add_action( 'rest_api_init', [ $this, 'init_rest_endpoints' ] );
        add_filter( 'woocommerce_rest_is_request_to_rest_api', [ $this, 'register_endpoints_with_woo_auth_handler' ] );
    }

	/**
	 * Initialize REST endpoints.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-11-13
	 */
	public function init_rest_endpoints() {
		( new Endpoints\AbandonedCarts() )->register_routes();
    }

    /**
	 * Register REST endpoints with wc/cc-woo namespace with WooCommerce's REST auth handler.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-11-13
     *
     * @return bool
	 */
    public function register_endpoints_with_woo_auth_handler() {
        $request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

        if ( empty( $request_uri ) ) {
            return false;
        }

		$rest_prefix = trailingslashit( rest_get_url_prefix() );

        return false !== strpos( $request_uri, $rest_prefix . self::$namespace );
    }

}

