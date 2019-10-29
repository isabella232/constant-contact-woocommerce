<?php
/**
 * AbandonedCarts Rest API auth handler.
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-25
 */

namespace WebDevStudios\CCForWoo\Rest\V1;

use WebDevStudios\OopsWP\Structure\Service;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;

use WP_Error;
use WP_REST_Request;

/**
 * Class AuthHandler
 *
 * @author  George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Rest\V1
 * @since   2019-10-25
 */
class AuthHandler extends Service {

	/**
	 * Stores the fact of failed token validation.
	 *
	 * @since 2019-10-25
	 *
	 * @var null|WP_Error
	 */
	private $auth_error = null;

	/**
	 * Register hooks.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-25
	 */
	public function register_hooks() {
		add_filter( 'rest_pre_dispatch', [ $this, 'rest_pre_dispatch' ], 10, 2 );
		add_filter( 'determine_current_user', [ $this, 'determine_current_user' ], 10 );
	}

	/**
	 * Attempts to authenticate user based on the provided token.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-25
	 *
	 * @param WP_User $user A WP User.
	 * @return WP_User|int WP_User of the user by default, otherwise the int of token's User ID if verified.
	 */
	public function determine_current_user( $user ) {
		// Only require tokens on cc-woo/v1 API requests that aren't the actual token-retrieval endpoint.
		$is_cc_woo_endpoint       = strpos( $_SERVER['REQUEST_URI'], 'cc-woo/v1' );
		$is_cc_woo_token_endpoint = strpos( $_SERVER['REQUEST_URI'], 'cc-woo/v1/get-token' );

		if ( ! $is_cc_woo_endpoint || $is_cc_woo_token_endpoint ) {
			return $user;
		}

		$token = $this->validate_token();

		if ( is_wp_error( $token ) ) {
			$this->auth_error = $token;
			return $user;
		}

		return $token->data->user->id;
	}

	/**
	 * Validates requests by validating existience of auth headers, token, and that token is valid.
	 *
	 * Much of this taken from the JWT for WP REST API plugin by Enrique Chavez.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-25
	 *
	 * @return WP_User|object|array
	 */
	public function validate_token() {
		$auth = isset( $_SERVER['HTTP_AUTHORIZATION'] ) ? $_SERVER['HTTP_AUTHORIZATION'] : false;

		if ( ! $auth ) {
			$auth = isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
		}

		if ( ! $auth ) {
			return new WP_Error( 'cc-woo-rest-no-auth-header', esc_html__( 'Authorization header not found.', 'cc-woo' ), [ 'status' => 403 ] );
		}

		// The HTTP_AUTHORIZATION is present; now verify the format and presence of actual token.
		list( $token ) = sscanf( $auth, 'Bearer %s' );

		if ( ! $token ) {
			return new WP_Error( 'cc-woo-rest-malformed-auth-header', esc_html__( 'Authorization header malformed.', 'cc-woo' ), [ 'status' => 403 ] );
		}

		$secret_key = get_option( 'cc_woo_abandoned_carts_secret_key', false );

		if ( ! $secret_key ) {
			return new WP_Error( 'cc-woo-rest-auth-config-error', esc_html__( 'Could not authenticate: Not configured properly.', 'cc-woo' ), [ 'status' => 403 ] );
		}

		try {

			$token = JWT::decode( $token, $secret_key, [ 'HS256' ] );

			if ( get_bloginfo( 'url' ) !== $token->iss ) {
				return new WP_Error( 'cc-woo-rest-auth-bad-request', esc_html__( 'Could not validate token iss.', 'cc-woo' ), [ 'status' => 403 ] );
			}

			if ( ! isset( $token->data->user->id ) ) {
				return new WP_Error( 'cc-woo-rest-auth-bad-request', esc_html__( 'Could not validate token user ID.', 'cc-woo' ), [ 'status' => 403 ] );
			}

			return $token;

		} catch ( ExpiredException $e ) {
			// Handles if the token has expired.
			return new WP_Error( 'cc-woo-rest-auth-expired-token', $e->getMessage(), [ 'status' => 403 ] );
		} catch ( SignatureInvalidException $e ) {
			// Handles if the signing key changed since time token was issued.
			return new WP_Error( 'cc-woo-rest-auth-invalid-key', $e->getMessage(), [ 'status' => 403 ] );
		} catch ( Exception $e ) {
			return new WP_Error( 'cc-woo-rest-auth-invalid-token', $e->getMessage(), [ 'status' => 403 ] );
		}
	}

	/**
	 * Before request is rendered, verify auth. Invalid auth will be a WP_Error of some kind.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-25
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Request|WP_Error
	 */
	public function rest_pre_dispatch( $request ) {
		if ( is_wp_error( $this->auth_error ) ) {
			return $this->auth_error;
		}

		return $request;
	}

}

