<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Sales Order form validation
     */
    public function test_sales_order_form_validation()
    {
        // Test required fields validation
        $response = $this->post('/sales-orders', []);

        $response->assertSessionHasErrors([
            'date' => 'The date field is required.',
            'customer_id' => 'The customer id field is required.',
            'lines' => 'The lines field is required.',
        ]);
    }

    public function test_sales_order_line_validation()
    {
        // Test line validation
        $response = $this->post('/sales-orders', [
            'date' => '2025-01-01',
            'customer_id' => 1,
            'lines' => [
                [
                    'line_type' => 'item',
                    'item_account_id' => 1,
                    'qty' => 1,
                    'unit_price' => 1000000,
                    'vat_amount' => 0,
                    'wtax_amount' => 0,
                    'amount' => 1000000,
                ]
            ]
        ]);

        // Should not have validation errors with proper hidden fields
        $response->assertSessionDoesNotHaveErrors(['lines.0.vat_amount', 'lines.0.wtax_amount']);
    }

    /**
     * Test Purchase Order form validation
     */
    public function test_purchase_order_form_validation()
    {
        // Test required fields validation
        $response = $this->post('/purchase-orders', []);

        $response->assertSessionHasErrors([
            'date' => 'The date field is required.',
            'vendor_id' => 'The vendor id field is required.',
            'lines' => 'The lines field is required.',
        ]);
    }

    public function test_purchase_order_line_validation()
    {
        // Test line validation with hidden fields
        $response = $this->post('/purchase-orders', [
            'date' => '2025-01-01',
            'vendor_id' => 1,
            'lines' => [
                [
                    'line_type' => 'item',
                    'item_account_id' => 1,
                    'qty' => 1,
                    'unit_price' => 1000000,
                    'vat_amount' => 0,
                    'wtax_amount' => 0,
                    'amount' => 1000000,
                ]
            ]
        ]);

        // Should not have validation errors with fixed hidden fields
        $response->assertSessionDoesNotHaveErrors(['lines.0.vat_amount', 'lines.0.wtax_amount']);
    }

    /**
     * Test Journal form validation
     */
    public function test_journal_form_validation()
    {
        // Test balanced journal validation
        $response = $this->post('/journals', [
            'date' => '2025-01-01',
            'description' => 'Test Journal',
            'lines' => [
                ['account_id' => 1, 'debit' => 100000, 'credit' => 0],
                ['account_id' => 1, 'debit' => 0, 'credit' => 100000]
            ]
        ]);

        $response->assertSessionDoesNotHaveErrors();
    }

    /**
     * Test Customer form validation
     */
    public function test_customer_form_validation()
    {
        $response = $this->post('/customers', []);

        $response->assertSessionHasErrors([
            'code' => 'The code field is required.',
            'name' => 'The name field is required.',
            'customer_type' => 'The customer type field is required.',
        ]);
    }

    /**
     * Test Vendor form validation
     */
    public function test_vendor_form_validation()
    {
        $response = $this->post('/vendors', []);

        $response->assertSessionHasErrors([
            'code' => 'The code field is required.',
            'name' => 'The name field is required.',
            'vendor_type' => 'The vendor type field is required.',
        ]);
    }

    /**
     * Test Project form validation
     */
    public function test_project_form_validation()
    {
        $response = $this->post('/projects', []);

        $response->assertSessionHasErrors([
            'code' => 'The code field is required.',
            'name' => 'The name field is required.',
        ]);
    }

    /**
     * Test Asset form validation
     */
    public function test_asset_form_validation()
    {
        $response = $this->post('/assets', []);

        $response->assertSessionHasErrors([
            'asset_number' => 'The asset number field is required.',
            'name' => 'The name field is required.',
            'category_id' => 'The category id field is required.',
            'purchase_date' => 'The purchase date field is required.',
            'purchase_cost' => 'The purchase cost field is required.',
        ]);
    }

    /**
     * Test Course form validation
     */
    public function test_course_form_validation()
    {
        $response = $this->post('/courses', []);

        $response->assertSessionHasErrors([
            'code' => 'The code field is required.',
            'name' => 'The name field is required.',
            'category_id' => 'The category id field is required.',
        ]);
    }

    /**
     * Test Enrollment form validation
     */
    public function test_enrollment_form_validation()
    {
        $response = $this->post('/enrollments', []);

        $response->assertSessionHasErrors([
            'student_number' => 'The student number field is required.',
            'course_batch_id' => 'The course batch id field is required.',
            'enrollment_date' => 'The enrollment date field is required.',
            'total_amount' => 'The total amount field is required.',
        ]);
    }

    /**
     * Test form hidden field generation
     */
    public function test_sales_order_hidden_fields_exist()
    {
        $response = $this->get('/sales-orders/create');

        $response->assertStatus(200);
        $response->assertSee('name="lines[0][vat_amount]"', false);
        $response->assertSee('name="lines[0][wtax_amount]"', false);
    }

    public function test_purchase_order_hidden_fields_exist()
    {
        $response = $this->get('/purchase-orders/create');

        $response->assertStatus(200);
        $response->assertSee('name="lines[0][vat_amount]"', false);
        $response->assertSee('name="lines[0][wtax_amount]"', false);
    }
}
