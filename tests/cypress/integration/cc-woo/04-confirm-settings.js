/**
 * When I visit the settings page
 * I should see a button that reads "Connect with Constant Contact"
 */

describe('As a Store Owner on the CC Woo Settings Page', function(){
	it( 'a button that reads "Connect with Constant Contact"', function(){
		cy.adminLogin(Cypress.env('cc_woo_url'))
		cy.get('body').should('contain', 'Connect with Constant Contact')
	} )
})

