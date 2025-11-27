# Taxonomy Discounts Plugin - Tests

This directory contains unit tests for the Taxonomy Discounts for WooCommerce plugin.

## Setup

1. Install dependencies:
   ```bash
   composer install
   ```

2. Run tests:
   ```bash
   ./vendor/bin/phpunit
   ```

## Test Structure

- `bootstrap.php` - Test bootstrap file with mocks for WordPress/WooCommerce functions
- `TaxonomyDiscountsTest.php` - Tests for the main TaxonomyDiscounts class
- `DiscountLogicTest.php` - Tests for discount calculation and business logic

## Running Tests

### Run all tests:
```bash
./vendor/bin/phpunit
```

### Run specific test file:
```bash
./vendor/bin/phpunit tests/DiscountLogicTest.php
```

### Run with coverage:
```bash
./vendor/bin/phpunit --coverage-html coverage
```

### Run with verbose output:
```bash
./vendor/bin/phpunit --verbose
```

## Test Coverage

The tests cover:
- Discount price calculation logic
- Date comparison logic for discount validity
- Priority ordering of discounts
- Taxonomy intersection logic
- Basic class instantiation and property checks

## Mocking

Since this plugin depends on WordPress, WooCommerce, and ACF, the bootstrap file includes mocks for:
- WordPress functions (`get_posts`, `wp_get_post_terms`, `date`)
- ACF functions (`acf_add_local_field_group`, `get_field`)
- WooCommerce classes (`WC_Product`)

For full integration testing with WordPress, you would need to set up the WordPress test suite.