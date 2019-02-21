<?php
/**
 * Main settings class.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo
 */

namespace ConstantContact\WooCommerce;

/**
 * Settings class for CC+Woo
 *
 * @since 0.0.1
 */
class Settings extends \WebDevStudios\Settings {
	/**
	 * Array of settings to un/register.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	protected $registerable_settings = [
		'ccwoo_owner_firstname',
		'ccwoo_owner_lastname',
		'ccwoo_store_name',
	];

	/**
	 * Configure the settings page.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function configure_settings() {
		$this->register_settings();
		$this->add_settings_section(
			'cc-account-info',
			'Constant Contact Store Information',
			[
				$this,
				'do_account_info_content',
			]
		);

		$this->add_settings_field( 'cc-first-name-field', __( 'First Name', 'cc-woo' ), [ $this, 'render_first_name_field' ] );
		$this->add_settings_field( 'cc-last-name-field', __( 'Last Name', 'cc-woo' ), [ $this, 'render_last_name_field' ] );
		$this->add_settings_field( 'cc-store-name-field', __( 'Store Name', 'cc-woo' ), [ $this, 'render_store_name_field' ] );
	}
}
