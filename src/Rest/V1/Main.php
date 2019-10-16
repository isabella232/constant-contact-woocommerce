<?php
/**
 * Main (entry point) class for doing the main setup of cc-woo REST API endpoints.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-13
 */

namespace WebDevStudios\CCForWoo\Rest\V1;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class Main
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-13
 */
class Main extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-10-13
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', [ $this, 'register_test_endpoint' ] );
	}

	/**
	 * Register test REST endpoint with WordPress.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-10-13
	 */
	public function register_test_endpoint() {
		register_rest_route( 'cc-woo/v1', '/abandoned-cart/(?P<id>\d+)', [
			'methods'  => 'GET',
			'callback' => [ __CLASS__, 'test_endpoint_response' ],
		] );
	}

	/**
	 * Register test REST endpoint response callback with WordPress.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  2019-10-13
	 *
	 * @param array $data Data from API request.
	 * @return mixed
	 */
	public static function test_endpoint_response( $data ) {
		return $data['id'];
	}

}
