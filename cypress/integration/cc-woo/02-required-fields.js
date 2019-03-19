/**
 * As a store owner
 * When I click on the Constant Contact tab in the WooCommerce Settings
 * I should be presented with a required text field for both first name and last name
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Phone Number
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Store Name
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Currency
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Country Code
 *
 * As a store owner
 * When I visit the settings page
 * I should be presented with a required text field for Contact Email Address
 */

function test_field( name, required ) {
	it('Sees a ' + ( required ? 'required' : '' ) + ' ' + name + ' field', function() {
		cy.get('@contactSettings').then((data) => {
			let element = '#' + data[name]
			cy.login(Cypress.env('cc_woo_url'))
			cy.get('body').should('contain', name)

			if ( required ) {
				cy.get( element ).should('have.attr', 'required')
			}
		})
	})
}

beforeEach(function(){
	cy.fixture('contact-settings.json').as('contactSettings')
})

describe('As a Store Owner on the CC Woo Settings Page', function(){
	test_field( 'First Name', true )
	test_field( 'Last Name', true )
	test_field( 'Phone Number', true )
	test_field( 'Store Name', true )
	test_field( 'Contact E-mail Address', true )
	test_field( 'Pre-select customer marketing sign-up at checkout', false )
})

