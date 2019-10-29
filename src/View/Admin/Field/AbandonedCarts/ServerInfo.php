<?php
/**
 * Field for showing tips on server configuration for REST API to work.
 *
 * @since 2019-10-28
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */

namespace WebDevStudios\CCForWoo\View\Admin\Field\AbandonedCarts;

/**
 * AbandonedCarts\ServerInfo field class
 *
 * @since 2019-10-28
 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
 * @package cc-woo-view-admin-field
 */
class ServerInfo {

	/**
	 * Server Info field.
	 *
	 * @since 2019-10-28
	 *
	 * @var string
	 */
	const OPTION_FIELD_NAME = 'cc_woo_abandoned_carts_server_info';

	/**
	 * Returns the form field configuration.
	 *
	 * @since 2019-10-28
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return array.
	 */
	public function get_form_field() : array {
		return [
			'title'             => esc_html__( 'Server Information', 'cc-woo' ),
			'desc'              => $this->get_description(),
			'type'              => 'text',
			'id'                => self::OPTION_FIELD_NAME,
			'default'           => '',
			'custom_attributes' => [
				'readonly' => 'true',
			],
		];
	}

	/**
	 * Field description.
	 *
	 * @since 2019-10-28
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 *
	 * @return string
	 */
	private function get_description() : string {
		ob_start();
	?>
		<div>
			<p><?php esc_html_e( 'For the Abandoned Carts REST API endpoint to work, you need to make sure your web server allows HTTP authorization headers. If you\'re not familiar with checking or enabling this, please contact your web host for assistance.', 'cc-woo' ); ?></p>
			<p><?php esc_html_e( 'If you are comfortable tweaking files on your server, then in most cases one of the following methods will enable that functionality.', 'cc-woo' ); ?></p>
			<h3>.htaccess</h3>
			<p>
				<?php
					printf(
						/* Translators: Placeholder is a string of HTML referencing file names. */
						esc_html__( 'Add the following code to your server\'s %1$s file:', 'cc-woo' ),
						'<kbd>.htaccess</kbd>'
					);
				?>
			</p>
			<code>
				RewriteEngine on
				RewriteCond %{HTTP:Authorization} ^(.*)
				RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
			</code>
			<h3>apache.conf</h3>
			<p>
				<?php
					printf(
						/* Translators: Placeholders are strings of HTML referencing file names. */
						esc_html__( 'Add the following code to your server\'s %1$s or %2$s file:', 'cc-woo' ),
						'<kbd>apache.conf</kbd>',
						'<kbd>apache2.conf</kbd>'
					);
				?>
			</p>
			<code>
				SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
			</code>
		</div>
	<?php
		return ob_get_clean();
	}
}
