<?php
/**
 * Class to handle creation and deletion of abandoned carts table.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-09
 */

namespace WebDevStudios\CCForWoo\Database;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class AbandonedCartsTable
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   2019-10-09
 */
class AbandonedCartsTable extends Service {

	/**
	 * Current version of abandoned carts table.
	 */
	const CC_ABANDONED_CARTS_DB_VERSION = '1.0';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-09
	 */
	public function register_hooks() {
	}

	/**
	 * Create abandoned carts table.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 */
	public function create_table() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'cc_abandoned_carts';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			cart_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			cart_added datetime NOT NULL default '0000-00-00 00:00:00',
			cart_added_ts
			user_id bigint(20) unsigned NOT NULL DEFAULT '0',
			user_email varchar(200) NOT NULL default '',
			cart_contents longtext NOT NULL,
			PRIMARY KEY (cart_id),
			KEY user_id (user_id)
		) $charset_collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'cc_abandoned_carts_db_version', self::CC_ABANDONED_CARTS_DB_VERSION );
	}
}
