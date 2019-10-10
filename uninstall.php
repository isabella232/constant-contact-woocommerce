<?php
/**
 * Handle plugin uninstall processes.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-10
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

use WebDevStudios\CCForWoo\Database\AbandonedCartsTable;
AbandonedCartsTable::delete_table();
