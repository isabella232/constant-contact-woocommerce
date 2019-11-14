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
	 * Register hooks.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-11-13
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'init_rest_endpoints' ] );
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

}

