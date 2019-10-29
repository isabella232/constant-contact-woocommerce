<?php
/**
 * AbandonedCarts Rest API registrar.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-16
 */

namespace WebDevStudios\CCForWoo\Rest\V1;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class Registrar
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-16
 */
class Registrar extends Service {

	/**
	 * Namespace for the endpoints this registrar registers.
	 *
	 * @since 2019-10-16
	 *
	 * @var string
	 */
	public static $namespace = 'cc-woo/v1';

	/**
	 * Register hooks.
	 *
	 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since   2019-10-16
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'init_rest_endpoints' ] );
	}

	/**
	 * Initialize REST endpoints.
	 *
	 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since   2019-10-16
	 */
	public function init_rest_endpoints() {
		( new Endpoints\GetToken() )->register_routes();
		( new Endpoints\AbandonedCarts() )->register_routes();
	}

}

