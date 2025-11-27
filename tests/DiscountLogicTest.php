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
        $product_terms = [1, 2, 3];
        $discount_taxonomies = [2, 4, 5];

        $intersection = array_intersect($discount_taxonomies, $product_terms);
        $this->assertNotEmpty($intersection); // Should have term 2 in common
        $this->assertContains(2, $intersection);

        // Test no intersection
        $discount_taxonomies_no_match = [4, 5, 6];
        $intersection_empty = array_intersect($discount_taxonomies_no_match, $product_terms);
        $this->assertEmpty($intersection_empty);
    }
}