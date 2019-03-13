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

use WebDevStudios\CCForWoo\View\Admin\WooTab;
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
		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'add_newsletter_checkbox' ] );
	}

	/**
	 * Add the newsletter checkbox to the set of fields in the billing form.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 */
	public function add_newsletter_checkbox() {
		woocommerce_form_field( 'customer_newsletter_opt_in', [
			'type'  => 'checkbox',
			'class' => [ 'input-checkbox' ],
			'label' => __( 'I agree to receive marketing e-mails', 'cc-woo' ),
		], $this->get_default_checkbox_state() );
	}

	/**
	 * Get the default state of the newsletter opt-in checkbox.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_default_checkbox_state() : bool {
		return is_user_logged_in() ? $this->get_user_default_checkbox_setting() : $this->get_store_default_checkbox_setting();
	}

	/**
	 * Get the default checkbox state from a user's preferences.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_user_default_checkbox_setting() : bool {
		$user_preference = get_user_meta( get_current_user_id(), 'cc_woo_customer_agrees_to_marketing', true );

		return ! empty( $user_preference ) ? 'yes' === $user_preference : $this->get_store_default_checkbox_setting();
	}

	/**
	 * Get the store's default checkbox state.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_store_default_checkbox_setting() : bool {
		return 'yes' === get_option( 'cc_woo_customer_data_email_opt_in_default' );
	}
}
