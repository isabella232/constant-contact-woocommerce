<?php
/**
 * An interface for settings.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios>
 * @package cc-woo
 */

namespace WebDevStudios;

/**
 * Settings Interface
 *
 * @since 0.0.1
 */
interface SettingsInterface {
	/**
	 * Create a Settings class with a SettingsConfig.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param \WebDevStudios\SettingsConfig $config The configuration object for the settings.
	 */
	public function __construct( $config );

	/**
	 * Main configuration method.
	 *
	 * This method will be hooked to `admin_init`, you should do your settings
	 * registration and sections/field setup here.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function configure_settings();

	/**
	 * Unregisters defined settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function unregister_settings();

	/**
	 * Hook into WordPress to set up our settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks();
}
