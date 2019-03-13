<?php
/**
 * Class to handle filtering fields in the checkout billing form.
 *
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */

namespace WebDevStudios\CCForWoo\View\Checkout;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class BillingFieldFilter
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */
class BillingFieldsFilter implements Hookable {
	/**
	 * Register actions and filters with WordPress.
	 *
	 * @since 2019-03-13
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_billing_fields', [ $this, 'add_newsletter_checkbox' ] );
	}

	/**
	 * Add the newsletter checkbox to the set of fields in the billing form.
	 *
	 * @param array $fields Array of form fields from WooCommerce.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return array
	 */
	public function add_newsletter_checkbox( array $fields ) {
		return array_merge( $fields, $this->get_newsletter_checkbox_field( $fields ) );
	}

	/**
	 * Get the newsletter checkbox field registration values.
	 *
	 * @param array $fields WooCommerce billing form fields.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return array
	 */
	private function get_newsletter_checkbox_field( array $fields ) {
		// We want this checkbox displayed right after the billing email.
		$priority = $fields['billing_email']['priority'] ?? 110;

		return [
			'newsletter_opt_in' => [
				'label'        => __( 'I agree to receive marketing e-mails', 'cc-woo' ),
				'priority'     => $priority + 1,
				'type'         => 'checkbox',
			],
		];
	}
}
