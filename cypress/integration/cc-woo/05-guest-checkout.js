describe('As a customer visiting the checkout page', function(){
	it( 'Should not have the opt-in default checked', function(){
		cy.login(Cypress.env('cc_woo_url')).then(() => {
			cy.fixture('contact-settings').then((data) => {
				let checkbox = '#' + data['Pre-select customer marketing sign-up at checkout'];
				cy.get( checkbox ).uncheck()
				cy.get( 'button[name="save"]' ).click().then(() => {
					cy.get('body').should('contain', 'Your settings have been saved')
					cy.get( checkbox ).should('not.have.attr', 'checked')
				})
			})
		})
	})

	it('Should checkout with a product and see that the checkbox for opt-in is not checked', function() {
		cy.visit(Cypress.env('test_product_url')).then(() => {
			cy.get('button[name="add-to-cart"]').click()
			cy.get('a[href*="/cart/"]').click()
			cy.get('a[href*="/checkout/"]').click()
			cy.get('#customer_newsletter_opt_in').should('not.have.attr', 'checked')
		})
	})

	it( 'Should have the opt-in default checked', function(){
		cy.login(Cypress.env('cc_woo_url')).then(() => {
			cy.fixture('contact-settings').then((data) => {
				let checkbox = '#' + data['Pre-select customer marketing sign-up at checkout'];
				cy.get( checkbox ).check()
				cy.get( 'button[name="save"]' ).click().then(() => {
					cy.get('body').should('contain', 'Your settings have been saved')
					cy.get( checkbox ).should('have.attr', 'checked')
				})
			})
		})
	})

	it('Should checkout with a product and see that the checkbox for opt-in is checked', function() {
		cy.visit(Cypress.env('test_product_url')).then(() => {
			cy.get('button[name="add-to-cart"]').click()
			cy.get('a[href*="/cart/"]').click()
			cy.get('a[href*="/checkout/"]').click()
			cy.get('#customer_newsletter_opt_in').should('have.attr', 'checked')
		})
	})
})
