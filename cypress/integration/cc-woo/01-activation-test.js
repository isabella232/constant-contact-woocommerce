describe('As WordPress', function() {
	/**
	 * As WordPress
	 * When the CC Woo plugin is activated and WooCommerce is activated
	 * I should hook into WooCommerce to add a new settings page tab labeled “Constant Contact”
	 */
	it('Ensures the plugin is active', function() {
		cy.adminLogin( Cypress.env('plugins_url') )

		cy.get('body').then(($body) => {
			let activateEl = 'tr[data-plugin="constant-contact-woocommerce/plugin.php"] span.activate a';
			let found = $body.find(activateEl);
			if ( found.length ) {
 				return activateEl
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
		cy.adminLogin(Cypress.env('woosettings_url'))
		cy.get('.woo-nav-tab-wrapper').should('contain', 'Constant Contact')
	})
})
