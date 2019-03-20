/**
 * Check out the customer.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_checked Whether the opt-in checkbox should be checked.
 */
Cypress.Commands.add('doCustomerCheckout', ( opt_in_checked ) => {
	cy.get('button[name="add-to-cart"]').click()
	cy.get('a[href*="/cart/"]:first').click()
	cy.get('a[href*="/checkout/"]:first').click()
	cy.fixture('checkout-details').then((data) => {
		for ( var key in data.text_fields ) {
			cy.get( '#' + key ).clear().type( data.text_fields[ key ] )
		}

		for ( key in data.select_fields ) {
			cy.get( '#' + key ).select( data.select_fields[ key ], { "force": true })
		}
	})
	cy.get('#customer_newsletter_opt_in')[ opt_in_checked ? 'check' : 'uncheck' ]()
	cy.get('#payment_method_cod').check({"force": true})
	cy.get('#place_order').click({"force": true}).then(() => {
		cy.wait(500)
		cy.get('body').should('contain', 'Order received')
	})
})

/**
 * Verify the opt-in checked state.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_checked Whether the opt-in checkbox should be checked.
 */
Cypress.Commands.add('verifyOptInCheckState', ( opt_in_checked ) => {
	cy.get('button[name="add-to-cart"]').click()
	cy.get('a[href*="/cart/"]:first').click()
	cy.get('a[href*="/checkout/"]:first').click()
	cy.get('#customer_newsletter_opt_in').should( opt_in_checked ? 'have.attr' : 'not.have.attr', 'checked')
})

/**
 * Set the opt-in checked default in the admin area.
 *
 * @since 2019-03-20
 * @author Zach Owen <zach@webdevstudios.com>
 * @param {Boolean} opt_in_default_checked Whether the opt-in checkbox should be checked.
 */
Cypress.Commands.add('setAdminOptInCheckState', ( opt_in_default_checked ) => {
	cy.fixture('contact-settings').then((data) => {
		let checkbox = '#' + data['Pre-select customer marketing sign-up at checkout'];
		cy.get( checkbox )[ opt_in_default_checked ? 'check' : 'uncheck' ]()
		cy.get( 'button[name="save"]' ).click().then(() => {
			cy.get('body').should('contain', 'Your settings have been saved')
			cy.get( checkbox ).should( opt_in_default_checked ? 'have.attr' : 'not.have.attr', 'checked')
		})
	})
})
