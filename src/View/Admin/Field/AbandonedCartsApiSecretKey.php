<?php
/**
 * Field for getting the Secret Key for use with the Abandoned Carts REST endpoint.
 *
 * @since 2019-10-24
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */

namespace WebDevStudios\CCForWoo\View\Admin\Field;

/**
 * AbandonedCartsApiSecretKey clss
 *
 * @since 2019-10-24
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */
class AbandonedCartsApiSecretKey {

	/**
	 * Secret Key field.
	 *
	 * @since 2019-10-24
	 *
	 * @var string
	 */
	const OPTION_FIELD_NAME = 'cc_woo_abandoned_carts_secret_key';

	/**
	 * Returns the form field configuration.
	 *
	 * @since 2019-10-24
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return array.
	 */
	public function get_form_field() : array {
		return [
			'title'             => esc_html__( 'Secret Key', 'cc-woo' ),
			'desc'              => $this->get_description(),
			'type'              => 'text',
			'id'                => self::OPTION_FIELD_NAME,
			'default'           => '',
			'custom_attributes' => [
				'class'    => 'widefat',
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
		<div style="padding:1rem 0;">
			<button
				id="cc_woo_abandoned_carts_generate_secret_key"
				class="button button-secondary"
				data-wp-nonce="<?php echo esc_attr( wp_create_nonce( 'cc-woo-abandoned-cart-generate-secret-key' ) ); ?>"
			>
				<?php esc_html_e( 'Generate Key', 'cc-woo' ); ?>
			</button>
		</div>
	<?php
		return ob_get_clean();
	}
}
