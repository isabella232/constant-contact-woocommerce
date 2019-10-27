<?php
/**
 * REST API endpoint for getting JWT token for Abandoned Carts requests.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-24
 */

namespace WebDevStudios\CCForWoo\Rest\V1\Endpoints;

use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Controller;
use WP_Error;

use WebDevStudios\CCForWoo\Rest\V1\Registrar;

/**
 * Class GetToken
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-24
 */
class GetToken extends WP_REST_Controller {

	/**
	 * This endpoint's rest base.
	 *
	 * @since 2019-10-24
	 *
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Constructor.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 */
	public function __construct() {
		$this->rest_base = 'get-token';
	}

	/**
	 * Register the GetToken REST route.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 */
	public function register_routes() {
		register_rest_route(
			Registrar::$namespace, '/' . $this->rest_base,
			[
				[
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => [ $this, 'get_token' ],
				],
				'schema' => null,
			]
		);
	}

	/**
	 * Register the Abandoned Carts endpoint.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return array
	 */
	public function get_token( $request ) {
		if ( ! $this->get_secret_key() ) {
			return new WP_Error( 'cc-woo-rest-auth-config-error', esc_html__( 'Could not authenticate: Not configured properly.', 'cc-woo' ), [ 'status' => 403 ] );
		}

		$params = $request->get_json_param();
		$user   = wp_authenticate( $params['username'], $params['password'] );

		return new WP_REST_Response( $user, 200 );
	}

	/**
	 * Gets the secret key.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 *
	 * @return mixed String of the secret key if exists AND valid; bool false if not either.
	 */
	private function get_secret_key() {
		return false;
	}

}

