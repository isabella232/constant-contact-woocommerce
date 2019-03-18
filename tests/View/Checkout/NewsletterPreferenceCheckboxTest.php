<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test\View\Checkout
 * @since   2019-03-13
 */

namespace WebDevStudios\CCForWoo\Test\View\Checkout;

use WP_Mock\Tools\TestCase;
use WebDevStudios\CCForWoo\View\Checkout\NewsletterPreferenceCheckbox;

/**
 * Class NewsletterPreferenceCheckboxTest
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Test\View\Checkout
 * @since   2019-03-13
 */
class NewsletterPreferenceCheckboxTest extends TestCase {
	/**
	 * Given a logged-out user
	 * When they visit the checkout and see a checkbox to receive marketing e-mails
	 * And the store default is to pre-check that checkbox
	 * Then the checkbox should be checked
	 *
	 * @test
	 */
	public function checkbox_returns_store_default_state_when_user_is_logged_out() {
		$object         = new NewsletterPreferenceCheckbox();
		$default_method = new \ReflectionMethod( $object, 'get_default_checked_state' );
		$default_method->setAccessible( true );

		$store_method = new \ReflectionMethod( $object, 'get_store_default_checked_state' );
		$store_method->setAccessible( true );

		\WP_Mock::userFunction( 'is_user_logged_in', [
			'return' => false,
		] );

		\WP_Mock::userFunction( 'get_option', [
			'args'   => 'cc_woo_customer_data_email_opt_in_default',
			'return' => 'yes',
		] );

		$this->assertEquals( $store_method->invoke( $object ), $default_method->invoke( $object ) );
	}

	/**
	 * Given a logged in user that has not yet made a purchase
	 * When they visit the checkout
	 * And the store default is to pre-check the marketing checkbox
	 * Then that checkbox should be checked
	 *
	 * @test
	 */
	public function checkbox_displays_store_default_if_user_has_not_yet_made_a_purchase() {
		$object         = new NewsletterPreferenceCheckbox();
		$default_method = new \ReflectionMethod( $object, 'get_default_checked_state' );
		$default_method->setAccessible( true );

		$store_method = new \ReflectionMethod( $object, 'get_store_default_checked_state' );
		$store_method->setAccessible( true );

		\WP_Mock::userFunction( 'is_user_logged_in', [
			'return' => true,
		] );

		\WP_Mock::userFunction( 'get_current_user_id', [
			'return' => 1,
		] );

		\WP_Mock::userFunction( 'get_user_meta', [
			'return' => '',
		]);

		\WP_Mock::userFunction( 'get_option', [
			'args'   => 'cc_woo_customer_data_email_opt_in_default',
			'return' => 'yes',
		] );

		$this->assertTrue( $default_method->invoke( $object ) );
	}

	/**
	 * Give a logged in user who has already made a purchase and opted not to receive marketing e-mails
	 * When they visit the checkout page
	 * The marketing checkbox should not be pre-filled.
	 *
	 * @test
	 */
	public function checkbox_is_unchecked_when_returning_user_has_preference_set_to_no() {
		$object         = new NewsletterPreferenceCheckbox();
		$default_method = new \ReflectionMethod( $object, 'get_default_checked_state' );
		$default_method->setAccessible( true );

		\WP_Mock::userFunction( 'is_user_logged_in', [
			'return' => true,
		] );

		\WP_Mock::userFunction( 'get_current_user_id', [
			'return' => 1,
		] );

		\WP_Mock::userFunction( 'get_user_meta', [
			'return' => 'no',
		]);

		$this->assertFalse( $default_method->invoke( $object ) );
	}

	/**
	 * Given a logged-in user who has previously made a purchase and opted to receive marketing emails
	 * When they visit the checkout page
	 * The email marketing checkbox should be checked
	 *
	 * @test
	 */
	public function checkbox_is_checked_when_returning_user_has_preference_set_to_yes() {
		$object         = new NewsletterPreferenceCheckbox();
		$default_method = new \ReflectionMethod( $object, 'get_default_checked_state' );
		$default_method->setAccessible( true );

		\WP_Mock::userFunction( 'is_user_logged_in', [
			'return' => true,
		] );

		\WP_Mock::userFunction( 'get_current_user_id', [
			'return' => 1,
		] );

		\WP_Mock::userFunction( 'get_user_meta', [
			'return' => 'yes',
		]);

		$this->assertTrue( $default_method->invoke( $object ) );
	}
}
