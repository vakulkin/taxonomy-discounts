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
- ✅ Discount price calculation formulas
- ✅ Date comparison logic for discount validity
- ✅ Priority ordering of discounts
- ✅ Taxonomy intersection logic for product matching (both brand AND category required)
- ✅ Caching mechanism and cache key generation

## Caching

The plugin uses WordPress transients for caching discount calculations:

- **Cache Key**: `taxonomy_discounts_{product_id}_{version}`
- **Cache Duration**: 1 hour
- **Cache Invalidation**: Automatic when discounts are created, updated, or deleted via:
  - `save_post` hook (standard WordPress post saving)
  - `delete_post` hook (post deletion)
- **Version Control**: Uses a global version number that changes when rules are modified

This significantly improves performance by avoiding repeated database queries for the same product.

For full integration testing with WordPress, you would need to set up the WordPress test suite.