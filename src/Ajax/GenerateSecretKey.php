<?php
/**
 * AJAX callback handler for generating new Secret Key in settings page; this key is used in Abandoned Carts REST endpoint.
 *
 * @package WebDevStudios\CCForWoo\Ajax
 * @since 2019-10-25
 */

namespace WebDevStudios\CCForWoo\Ajax;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class GenerateSecretKey
 *
 * @package WebDevStudios\CCForWoo\Ajax
 * @since 2019-10-11
 */
class GenerateSecretKey extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>>
	 * @since 2019-10-24
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_cc_woo_abandoned_carts_generate_secret_key', [ $this, 'generate_secret_key' ] );
	}

	/**
	 * Generates a new secret key.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>>
	 * @since 2019-10-24
	 */
	public function generate_secret_key() {
		$nonce = filter_input( INPUT_POST, 'nonce', FILTER_SANITIZE_STRING );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'cc-woo-abandoned-cart-generate-secret-key' ) ) {
			wp_send_json_error(
				[
					'key'     => null,
					'message' => esc_html__( 'Invalid nonce.', 'cc-woo' ),
				]
			);
		}

		$secret_key = wp_generate_password( 64, true, true );

		update_option( 'cc_woo_abandoned_carts_secret_key', $secret_key );

		wp_send_json_success(
			[
				'key'     => $secret_key,
				'message' => esc_html__( 'Success!', 'cc-woo' ),
			]
		);
	}

}
