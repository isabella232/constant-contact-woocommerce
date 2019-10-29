<?php
/**
 * Field for showing the REST Endpoints for the Abandoned Carts API.
 *
 * @since 2019-10-28
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */

namespace WebDevStudios\CCForWoo\View\Admin\Field\AbandonedCarts;

/**
 * RestEndpoints field class
 *
 * @since 2019-10-28
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */
class RestEndpoints {

	/**
	 * Rest Endpoints field.
	 *
	 * @since 2019-10-28
	 *
	 * @var string
	 */
	const OPTION_FIELD_NAME = 'cc_woo_abandoned_carts_rest_endpoints';

	/**
	 * Returns the form field configuration.
	 *
	 * @since 2019-10-28
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return array.
	 */
	public function get_form_field() : array {
		return [
			'title'             => esc_html__( 'REST Endpoints', 'cc-woo' ),
			'desc'              => $this->get_description(),
			'type'              => 'text',
			'id'                => self::OPTION_FIELD_NAME,
			'default'           => '',
			'custom_attributes' => [
				'readonly' => 'true',
			],
		];
	}

	/**
	 * Field description, where we actually just show the Generate Key button.
	 *
	 * @since 2019-10-24
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return string
	 */
	private function get_description() : string {
		ob_start();
	?>
		<div>
			<p>
				<strong><?php esc_html_e( 'REST Endpoint: Abandoned Carts', 'cc-woo' ); ?></strong><br />
				<?php echo esc_html( $this->get_abandoned_carts_endpoint_url() ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'REST Endpoint: Get Token', 'cc-woo' ); ?></strong><br />
				<?php echo esc_html( $this->get_get_token_endpoint_url() ); ?>
			</p>
		</div>
	<?php
		return ob_get_clean();
	}

	/**
	 * Get URL to abandoned cart endpoint.
	 *
	 * @since 2019-10-28
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return string
	 */
	private function get_abandoned_carts_endpoint_url() : string {
		return sprintf( '%1$s/wp-json/cc-woo/v1/abandoned-carts', get_home_url() );
	}

	/**
	 * Get URL to getToken endpoint.
	 *
	 * @since 2019-10-28
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return string
	 */
	private function get_get_token_endpoint_url() : string {
		return sprintf( '%1$s/wp-json/cc-woo/v1/get-token', get_home_url() );
	}

}
