beforeEach(function(){
	cy.fixture('consumer-settings').as('consumerSettings')
})
describe('As a store owner on the CC Woo Settings page Historical Import section', function(){
	/**
	 * As a store owner
	 * When I visit the settings page
	 * I should see a drop-down indicating my preference to import historical customer data
	 */
	it('Sees a drop-down indicating my preference to import historical customer data', function(){
		cy.adminLogin(Cypress.env('historical_url'))
		cy.get('@consumerSettings').then((data) => {
			cy.get('#' + data['Import historical customer data']).should('be.visible')
		})
	})

	/**
	 * As a store owner
	 * When I visit the settings page
	 * I should see a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”
	 */
	it('Sees a a checkbox that verifies I have permission to e-mail customers if the historical drop down is “Yes”', function(){
		cy.adminLogin(Cypress.env('historical_url'))
		cy.get('@consumerSettings').then((data) => {
			cy.get('#' + data['User information consent']).should('be.visible')
		})
	})
})
