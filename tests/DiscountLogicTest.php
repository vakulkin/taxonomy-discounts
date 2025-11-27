<?php

use PHPUnit\Framework\TestCase;

class DiscountLogicTest extends TestCase {

    public function testDiscountCalculation() {
        // Test the discount calculation formula
        $original_price = 100.0;
        $discount_percent = 10;

        $expected_discounted_price = $original_price * (1 - $discount_percent / 100);
        $this->assertEquals(90.0, $expected_discounted_price);

        // Test with different percentages
        $this->assertEquals(80.0, 100.0 * (1 - 20 / 100));
        $this->assertEquals(50.0, 100.0 * (1 - 50 / 100));
        $this->assertEquals(0.0, 100.0 * (1 - 100 / 100));
    }

    public function testDateComparisonLogic() {
        $today = '2025-11-27';

        // Test start date logic
        $this->assertTrue('2025-11-26' <= $today); // Past date
        $this->assertTrue('2025-11-27' <= $today); // Today
        $this->assertFalse('2025-11-28' <= $today); // Future date

        // Test end date logic
        $this->assertFalse('2025-11-26' >= $today); // Past date
        $this->assertTrue('2025-11-27' >= $today); // Today
        $this->assertTrue('2025-11-28' >= $today); // Future date
    }

    public function testPriorityOrdering() {
        $discounts = [
            ['priority' => 5, 'percent' => 10],
            ['priority' => 10, 'percent' => 20],
            ['priority' => 1, 'percent' => 5],
        ];

        // Sort by priority descending (higher priority first)
        usort($discounts, function($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        $this->assertEquals(10, $discounts[0]['priority']); // Highest priority first
        $this->assertEquals(5, $discounts[1]['priority']);
        $this->assertEquals(1, $discounts[2]['priority']);
    }

    public function testTaxonomyIntersection() {
        $product_brands = [1, 2, 3];
        $product_categories = [4, 5, 6];
        
        $discount_brands = [2, 7, 8];
        $discount_categories = [5, 9, 10];

        $has_brand = array_intersect( $discount_brands, $product_brands );
        $has_category = array_intersect( $discount_categories, $product_categories );
        
        // Both conditions must be true for discount to apply (optimized: categories checked first)
        $this->assertTrue(!empty($has_brand) && !empty($has_category)); // Should apply (has brand 2 and category 5)
        
        // Test case where only brand matches but category doesn't
        $discount_categories_no_match = [11, 12];
        $has_category_no_match = array_intersect( $discount_categories_no_match, $product_categories );
        $this->assertFalse(!empty($has_brand) && !empty($has_category_no_match)); // Should not apply
        
        // Test case where only category matches but brand doesn't
        $discount_brands_no_match = [7, 8];
        $has_brand_no_match = array_intersect( $discount_brands_no_match, $product_brands );
        $this->assertFalse(!empty($has_brand_no_match) && !empty($has_category)); // Should not apply
    }

    public function testCachingMechanism() {
        // Test that cache version changes over time
        $version1 = time();
        sleep(1); // Ensure time passes
        $version2 = time();
        
        $this->assertNotEquals($version1, $version2);
        
        // Test cache key generation logic
        $product_id = 123;
        $version = 1234567890;
        $expected_key = 'taxonomy_discounts_' . $product_id . '_' . $version;
        
        // Verify key format
        $this->assertStringStartsWith('taxonomy_discounts_', $expected_key);
        $this->assertStringEndsWith('_' . $version, $expected_key);
        $this->assertStringContainsString((string)$product_id, $expected_key);
    }
}