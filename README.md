# Constant Contact ❤️ WooCommerce

## Testing

### Cypress.io

This plugin uses [Cypress.io](https://cypress.io/) for end-to-end testing.

Cypress's files are located in `tests/cypress/`.

#### Configuration

##### Base Configuration

Before running Cypress, you'll need to copy `cypress.example.json` to `cypress.json`,
and modify it for your environment.

##### Variables

You will need to configure the following variables for your environment.

- `baseUrl` This is the base URL of your local WordPress instance.
- `admin_user` The WordPress admin user.
- `admin_pass` Password for the admin user.
- `customer_user` A WooCommerce customer account user.
- `customer_pass` The password for the customer account.
- `test_product_url` A frontend URL to a product to test with during checkout.

#### Fixtures

Cypress relies on fixtures to provide some additional information. The fixtures
for the form fields in the Admin screen are built using PHP reflection. If you 
modify the field names, you will need to regenerate the fixture data by running
the following `composer` command:

```sh
composer run-script generate-fixtures
```

#### Running Cypress

In your terminal:

```sh
npm i
$(npm bin)/cypress open
```
