describe('As WordPress', function() {
	/**
	 * As WordPress
	 * When the CC Woo plugin is activated and WooCommerce is activated
	 * I should hook into WooCommerce to add a new settings page tab labeled “Constant Contact”
	 */
	it('Ensures the plugin is active', function() {
		cy.login( '/wp-admin/plugins.php' )

		cy.get('body').then(($body) => {
			let found = $body.find('tr[data-plugin="constant-contact-woocommerce/plugin.php"] span.activate a');
			if ( found.length ) {
 				return 'tr[data-plugin="constant-contact-woocommerce/plugin.php"] span.activate a'
			}

			return '';
		})
		.then(($selector) => {
			if ( $selector ) {
				cy.get($selector).click()
			}
		})
	})
})

describe('As a Store Owner', function(){
	/**
	 * As a store owner
	 * When I go to the WooCommerce settings page
	 * I should see the Constant Contact tab
	 */
	it('Goes to the WooCommerce Settings and there is a Constant Contact tab', function() {
		cy.login( '/wp-admin/admin.php?page=wc-settings' )
		cy.get('.woo-nav-tab-wrapper').should('contain', 'Constant Contact')
	})
})
