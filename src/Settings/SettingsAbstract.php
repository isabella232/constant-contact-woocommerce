<?php
/**
 * Settings Abstract Class
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package wds-settings
 */

namespace WebDevStudios\CCForWoo\Settings;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Settings Abstract
 *
 * Implement and fill-out the missing methods to handle fields.
 *
 * @since 0.0.1
 */
abstract class SettingsAbstract implements SettingsInterface, Hookable {
	/**
	 * The current settings section ID.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $current_section = null;

	/**
	 * The settings configuration instance.
	 *
	 * @since 0.0.1
	 * @var \WebDevStudios\CCForWoo\Settings\SettingsConfig
	 */
	protected $config = null;

	/**
	 * Array of options to register.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	protected $registerable_settings = [];

	/**
	 * Main configuration method.
	 *
	 * This method will be hooked to `admin_init`, you should do your settings
	 * registration and sections/field setup here.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	abstract public function configure();

	/**
	 * Create a Settings class with a SettingsConfig.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param \WebDevStudios\CCForWoo\Settings\SettingsConfig $config The configuration object for the settings.
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	/**
	 * Hook into WordPress to set up our settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'configure' ] );
	}

	/**
	 * Unregisters defined settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function unregister_settings() {
		foreach ( $this->registerable_settings as $setting ) {
			$this->unregister( $setting );
		}
	}

	/**
	 * Register defined settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	protected function register_settings() {
		foreach ( $this->registerable_settings as $setting ) {
			$this->register( $setting );
		}
	}

	/**
	 * Add a settings section.
	 *
	 * See https://developer.wordpress.org/reference/functions/add_settings_section/
	 *
	 * This is a shorthand for WP's `add_settings_section` that will do some extra stuff:
	 * - Takes care of passing the `$page` paramter from the config object.
	 * - Saves the current settings section ID for use when registering fields.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string   $id The ID of the settings section.
	 * @param string   $title The title of the settings section, shown as the section heading.
	 * @param callable $callback The callback to display the section.
	 */
	protected function add_section( string $id, string $title, $callback ) {
		add_settings_section( $id, $title, $callback, $this->config->get_page() );
		$this->current_section = $id;
	}

	/**
	 * Add a settings field to the most recent section.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string   $field_id The ID of the field.
	 * @param string   $title The field title, used to create the field label.
	 * @param callable $callback The callback to display the field input(s).
	 */
	protected function add_field( string $field_id, string $title, $callback ) {
		add_settings_field(
			$field_id,
			$title,
			$callback,
			$this->config->get_page(),
			$this->current_section
		);
	}

	/**
	 * Registers a setting for this option group.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $field The field to register.
	 */
	private function register( string $field ) {
		register_setting( $this->config->get_option_group(), $field );
	}

	/**
	 * Unregister a field from the option group.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $field The field to unregister.
	 */
	private function unregister( $field ) {
		unregister_setting( $this->config->get_option_group(), $field );
	}
}
