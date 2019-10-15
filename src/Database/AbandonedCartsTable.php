<?php
/**
 * Class to handle creation of abandoned carts table.
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
	 * Option name for abandoned carts db version.
	 */
	const CC_ABANDONED_CARTS_DB_VERSION_OPTION = 'cc_abandoned_carts_db_version';

	/**
	 * Abandoned carts table name.
	 */
	const CC_ABANDONED_CARTS_TABLE = 'cc_abandoned_carts';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-09
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', [ $this, 'update_db_check' ] );
	}

	/**
	 * Create abandoned carts table.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 */
	public function create_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . self::CC_ABANDONED_CARTS_TABLE;

		$sql = "CREATE TABLE {$table_name} (
			cart_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL DEFAULT 0,
			user_email varchar(200) NOT NULL default '',
			cart_contents longtext NOT NULL,
			cart_updated datetime NOT NULL default '0000-00-00 00:00:00',
			cart_updated_ts int(11) unsigned NOT NULL default 0,
			PRIMARY KEY (cart_id),
			UNIQUE KEY user (user_id, user_email)
		) {$wpdb->get_charset_collate()}";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( self::CC_ABANDONED_CARTS_DB_VERSION_OPTION, self::CC_ABANDONED_CARTS_DB_VERSION );
	}

	/**
	 * Check if table exists and is up-to-date.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  2019-10-10
	 */
	public function update_db_check() {
		if ( self::CC_ABANDONED_CARTS_DB_VERSION !== get_site_option( self::CC_ABANDONED_CARTS_DB_VERSION_OPTION ) ) {
			$this->create_table();
		}
	}
}
