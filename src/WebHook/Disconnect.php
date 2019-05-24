<?php
/**
 * Disconnect WebHook
 *
 * @since 0.0.1
 * @author Constant Contact <https://www.constantcontact.com>
 * @package cc-woo
 * @link https://gist.github.com/jessepearson/66a0e72706b99c15b52dee7ce59e1d31
 */

namespace WebDevStudios\CCForWoo\WebHook;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Disconnect WebHook Class
 *
 * @since 2019-05-21
 */
class Disconnect extends Service {
	/**
	 * Hook into Woo.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_filter( 'woocommerce_webhook_topic_hooks', [ $this, 'add_new_topic_hooks' ] );
		add_filter( 'woocommerce_valid_webhook_events', [ $this, 'add_new_topic_events' ] );
		add_filter( 'woocommerce_webhook_topics', [ $this, 'add_new_webhook_topics' ] );
		add_action( 'woocommerce_checkout_order_processed', [ $this, 'my_order_item_check' ], 10, 3 );
	}

	/**
	 * Add a new topic hook for disconnecting.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $topic_hooks Array of hooks from Woo.
	 * @return array
	 */
	public function add_new_topic_hooks( $topic_hooks ) {
		$topic_hooks['constantcontact.woo_disconnect'] = [
			'cc_woo_disconnect',
		];

		return $topic_hooks;
	}

	/**
	 * Add new events for our topic.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $topic_events Existing valid events for resources.
	 * @return array
	 */
	public function add_new_topic_events( $topic_events ) {
		$topic_events[] = 'woo_disconnect';
		return $topic_events;
	}

	/**
	 * Adds the topic hook to the dropdown on Woo -> Settings -> Advanced -> WebHooks.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 * @param array $topics Array of topics with the i18n proper name.
	 * @return array
	 */
	public function add_new_webhook_topics( $topics ) {
		$topics['constantcontact.woo_disconnect'] = __( 'Constant Contact WooCommerce Disconnect', 'cc-woo' );
		return $topics;
	}
}
