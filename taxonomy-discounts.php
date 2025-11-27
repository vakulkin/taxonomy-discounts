<?php

/*
Plugin Name: Taxonomy Discounts for WooCommerce
Description: Allows setting taxonomy-specific percent discounts using ACF.
Version: 1.0
Author: Anton Vakulov
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TaxonomyDiscounts {

	public $product_taxonomy = 'product_brand';

	public function __construct() {
		add_action( 'init', array( $this, 'register_discounts_post_type' ) );
		add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
		add_filter( 'woocommerce_product_get_price', array( $this, 'apply_taxonomy_discounts' ), 10, 2 );
		add_filter( 'woocommerce_product_get_sale_price', array( $this, 'apply_taxonomy_discounts' ), 10, 2 );
	}

	// Register Custom Post Type
	public function register_discounts_post_type() {
		$args = array(
			'public' => false,
			'show_ui' => true,
			'label' => 'Discounts',
			'supports' => array( 'title' ),
			'menu_icon' => 'dashicons-money-alt',
		);
		register_post_type( 'discount', $args );
	}

	// Register ACF Fields
	public function register_acf_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) {
			acf_add_local_field_group( array(
				'key' => 'group_discount_fields',
				'title' => 'Discount Fields',
				'fields' => array(
					array(
						'key' => 'field_taxonomies',
						'label' => 'Taxonomies',
						'name' => 'taxonomies',
						'type' => 'taxonomy',
						'instructions' => 'Select the taxonomies to apply this discount to.',
						'taxonomy' => $this->product_taxonomy,
						'field_type' => 'multi_select',
						'required' => 1,
						'allow_null' => 0,
						'return_format' => 'id',
					),
					array(
						'key' => 'field_active',
						'label' => 'Active',
						'name' => 'active',
						'type' => 'true_false',
						'instructions' => 'Enable or disable this discount.',
						'default_value' => 1,
						'ui' => 1,
						'wrapper' => array( 'width' => '33.33%' ),
					),
					array(
						'key' => 'field_percent',
						'label' => 'Discount Percent',
						'name' => 'percent',
						'type' => 'number',
						'instructions' => 'Enter the discount percentage (e.g., 10 for 10%).',
						'required' => 1,
						'min' => 1,
						'max' => 100,
						'wrapper' => array( 'width' => '33.33%' ),
					),
					array(
						'key' => 'field_priority',
						'label' => 'Priority',
						'name' => 'priority',
						'type' => 'number',
						'instructions' => 'Higher priority discounts will be applied first. Default is 10.',
						'required' => 1,
						'default_value' => 100,
						'min' => 1,
						'wrapper' => array( 'width' => '33.33%' ),
					),
					array(
						'key' => 'field_start_date',
						'label' => 'Start Date',
						'name' => 'start_date',
						'type' => 'date_picker',
						'instructions' => 'Optional start date for the discount.',
						'return_format' => 'Y-m-d',
						'display_format' => 'd/m/Y',
						'wrapper' => array( 'width' => '33.33%' ),
					),
					array(
						'key' => 'field_end_date',
						'label' => 'End Date',
						'name' => 'end_date',
						'type' => 'date_picker',
						'instructions' => 'Optional end date for the discount.',
						'return_format' => 'Y-m-d',
						'display_format' => 'd/m/Y',
						'wrapper' => array( 'width' => '33.33%' ),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'discount',
						),
					),
				),
			) );
		}
	}

	// Get applicable discounts for a product
	public function get_applicable_discounts( $product ) {
		// Only apply to simple products
		if ( ! $product->is_type( 'simple' ) ) {
			return array();
		}

		$today = date( 'Y-m-d' );

		$discounts = get_posts( array(
			'post_type' => 'discount',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'active',
					'value' => '1',
					'compare' => '=',
				),
				array(
					'key' => 'percent',
					'value' => '1',
					'compare' => '>=',
					'type' => 'NUMERIC',
				),
				array(
					'key' => 'priority',
					'value' => '1',
					'compare' => '>=',
					'type' => 'NUMERIC',
				),
				array(
					'relation' => 'OR',
					array(
						'key' => 'start_date',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => 'start_date',
						'value' => '',
						'compare' => '=',
					),
					array(
						'key' => 'start_date',
						'value' => $today,
						'compare' => '<=',
						'type' => 'DATE',
					),
				),
				array(
					'relation' => 'OR',
					array(
						'key' => 'end_date',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => 'end_date',
						'value' => '',
						'compare' => '=',
					),
					array(
						'key' => 'end_date',
						'value' => $today,
						'compare' => '>=',
						'type' => 'DATE',
					),
				),
			),
			'orderby' => 'meta_value_num',
			'meta_key' => 'priority',
			'order' => 'DESC',
		) );

		$applicable_discounts = array();

		foreach ( $discounts as $discount ) {
			$taxonomies = get_field( 'taxonomies', $discount->ID );

			if ( $taxonomies ) {
				// Check if product has any of the selected taxonomies
				$product_terms = wp_get_post_terms( $product->get_id(), $this->product_taxonomy, array( 'fields' => 'ids' ) );
				$has_taxonomy = array_intersect( $taxonomies, $product_terms );

				if ( $has_taxonomy ) {
					$percent = get_field( 'percent', $discount->ID );
					$applicable_discounts[] = $percent;
				}
			}
		}

		return $applicable_discounts;
	}

	// Apply Discounts
	public function apply_taxonomy_discounts( $price, $product ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			return $price;
		}

		$applicable_discounts = $this->get_applicable_discounts( $product );

		if ( ! empty( $applicable_discounts ) ) {
			// Apply the highest priority discount
			$discount_percent = $applicable_discounts[0];
			$discounted_price = $product->get_regular_price() * ( 1 - $discount_percent * 0.01 );
			return $discounted_price;
		}

		return $price;
	}

}

// Initialize the plugin
new TaxonomyDiscounts();
