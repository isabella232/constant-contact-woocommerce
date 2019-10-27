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
 * AbandonedCartApiSecretKey clss
 *
 * @since 2019-10-24
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */
class AbandonedCartApiSecretKey {

	/**
	 * Secret Key field.
	 *
	 * @since 2019-10-24
	 *
	 * @var string
	 */
	const OPTION_FIELD_NAME = 'cc_woo_abandoned_cart_secret_key';

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
		return '<button class="button button-secondary">Generate Key</button>';
	}
}
