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
	 * Main configuration method.
	 *
	 * This method will be hooked to `admin_init`, you should do your settings
	 * registration and sections/field setup here.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function configure();

	/**
	 * Unregisters defined settings.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function unregister_settings();
}
