<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseBatch;
use App\Models\Master\Customer;
use App\Models\Enrollment;
use App\Models\PaymentPlan;
use App\Models\InstallmentPayment;
use App\Models\RevenueRecognition;
use App\Models\Accounting\Journal;
use App\Models\Accounting\Account;
use Illuminate\Support\Facades\DB;

class CourseFinancialReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected CourseCategory $category;
    protected Course $course;
    protected CourseBatch $batch;
    protected Customer $student;
    protected PaymentPlan $paymentPlan;
    protected Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();

        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'course_financial_reports.view',
            'course_financial_reports.profitability',
            'course_financial_reports.receivables',
            'course_financial_reports.revenue_recognition',
            'course_financial_reports.payment_collection'
        ]);

        $this->actingAs($this->user);
        $this->setupTestData();
    }

    protected function setupTestData(): void
    {
        // Create course category
        $this->category = CourseCategory::create([
            'name' => 'Digital Marketing',
            'description' => 'Digital Marketing Courses'
        ]);

        // Create course
        $this->course = Course::create([
            'code' => 'DM-001',
            'name' => 'Digital Marketing Fundamentals',
            'description' => 'Basic digital marketing course',
            'category_id' => $this->category->id,
            'base_price' => 8000000,
            'status' => 'active'
        ]);

        // Create student
        $this->student = Customer::create([
            'name' => 'PT Maju Bersama',
            'email' => 'info@majubersama.com',
            'phone' => '081234567890',
            'company' => 'PT Maju Bersama'
        ]);

        // Create course batch
        $this->batch = CourseBatch::create([
            'course_id' => $this->course->id,
            'batch_code' => 'DM-BATCH-001',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(30),
            'capacity' => 20,
            'status' => 'scheduled'
        ]);

        // Create payment plan
        $this->paymentPlan = PaymentPlan::create([
            'name' => '4 Installments',
            'installment_count' => 4,
            'installment_amount' => 2000000
        ]);

        // Create enrollment
        $this->enrollment = Enrollment::create([
            'student_id' => $this->student->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        // Create installment payments
        InstallmentPayment::create([
            'enrollment_id' => $this->enrollment->id,
            'installment_number' => 1,
            'due_date' => now(),
            'amount' => 2000000,
            'status' => 'paid',
            'paid_at' => now()
        ]);

        InstallmentPayment::create([
            'enrollment_id' => $this->enrollment->id,
            'installment_number' => 2,
            'due_date' => now()->addDays(30),
            'amount' => 2000000,
            'status' => 'pending'
        ]);

        // Create revenue recognition
        RevenueRecognition::create([
            'enrollment_id' => $this->enrollment->id,
            'type' => 'deferred',
            'amount' => 7207207.21,
            'recognition_date' => now(),
            'status' => 'pending'
        ]);
    }

    public function test_course_financial_reports_dashboard_access(): void
    {
        $response = $this->get('/reports/course-financial');

        $response->assertOk();
        $response->assertViewIs('reports.course-financial.index');
        $response->assertSee('Course Financial Reports');
        $response->assertSee('Course Profitability');
        $response->assertSee('Outstanding Receivables');
        $response->assertSee('Revenue Recognition');
        $response->assertSee('Payment Collection');
    }

    public function test_course_financial_reports_dashboard_requires_permission(): void
    {
        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission);

        $response = $this->get('/reports/course-financial');

        $response->assertForbidden();
    }

    public function test_course_profitability_report_access(): void
    {
        $response = $this->get('/reports/course-financial/profitability');

        $response->assertOk();
        $response->assertViewIs('reports.course-financial.profitability');
        $response->assertSee('Course Profitability Report');
        $response->assertSee('Revenue & Cost Analysis');
    }

    public function test_course_profitability_report_data(): void
    {
        $response = $this->getJson('/reports/course-financial/profitability/data');

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('recordsTotal', $data);
        $this->assertArrayHasKey('recordsFiltered', $data);

        // Verify course data
        $courseData = $data['data'][0];
        $this->assertEquals('DM-001', $courseData['code']);
        $this->assertEquals('Digital Marketing Fundamentals', $courseData['name']);
        $this->assertEquals('Digital Marketing', $courseData['category_name']);
        $this->assertEquals(8000000, $courseData['base_price']);
        $this->assertEquals(1, $courseData['total_enrollments']);
        $this->assertEquals(8000000, $courseData['total_revenue']);
    }

    public function test_outstanding_receivables_report_access(): void
    {
        $response = $this->get('/reports/course-financial/outstanding-receivables');

        $response->assertOk();
        $response->assertViewIs('reports.course-financial.outstanding-receivables');
        $response->assertSee('Outstanding Receivables Report');
        $response->assertSee('Payment Tracking');
    }

    public function test_outstanding_receivables_report_data(): void
    {
        $response = $this->getJson('/reports/course-financial/outstanding-receivables/data');

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('recordsTotal', $data);
        $this->assertArrayHasKey('recordsFiltered', $data);

        // Verify receivables data
        $receivablesData = $data['data'][0];
        $this->assertEquals('PT Maju Bersama', $receivablesData['student_name']);
        $this->assertEquals('Digital Marketing Fundamentals', $receivablesData['course_name']);
        $this->assertEquals(8000000, $receivablesData['total_amount']);
        $this->assertEquals(2000000, $receivablesData['paid_amount']);
        $this->assertEquals(6000000, $receivablesData['outstanding_amount']);
    }

    public function test_revenue_recognition_report_access(): void
    {
        $response = $this->get('/reports/course-financial/revenue-recognition');

        $response->assertOk();
        $response->assertViewIs('reports.course-financial.revenue-recognition');
        $response->assertSee('Revenue Recognition Report');
        $response->assertSee('Deferred vs Recognized');
    }

    public function test_payment_collection_report_access(): void
    {
        $response = $this->get('/reports/course-financial/payment-collection');

        $response->assertOk();
        $response->assertViewIs('reports.course-financial.payment-collection');
        $response->assertSee('Payment Collection Report');
        $response->assertSee('Collection Performance');
    }

    public function test_course_profitability_report_with_filters(): void
    {
        // Test with date filter
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->getJson("/reports/course-financial/profitability/data?start_date={$startDate}&end_date={$endDate}");

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('data', $data);
    }

    public function test_course_profitability_report_with_category_filter(): void
    {
        // Test with category filter
        $response = $this->getJson("/reports/course-financial/profitability/data?category={$this->category->id}");

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('data', $data);

        // Verify all returned courses are from the selected category
        foreach ($data['data'] as $course) {
            $this->assertEquals('Digital Marketing', $course['category_name']);
        }
    }

    public function test_outstanding_receivables_report_with_filters(): void
    {
        // Test with date filter
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->getJson("/reports/course-financial/outstanding-receivables/data?start_date={$startDate}&end_date={$endDate}");

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('data', $data);
    }

    public function test_course_profitability_report_calculations(): void
    {
        $response = $this->getJson('/reports/course-financial/profitability/data');

        $response->assertOk();
        $data = $response->json();

        $courseData = $data['data'][0];

        // Test revenue per enrollment calculation
        $expectedRevenuePerEnrollment = 8000000 / 1; // total_revenue / total_enrollments
        $this->assertEquals($expectedRevenuePerEnrollment, $courseData['revenue_per_enrollment']);

        // Test utilization rate calculation
        $expectedUtilizationRate = (1 / 1) / 20 * 100; // (total_enrollments / total_batches) / avg_capacity * 100
        $this->assertEquals($expectedUtilizationRate, $courseData['utilization_rate']);

        // Test deferred revenue calculation
        $expectedDeferredRevenue = 8000000 - 0; // total_revenue - recognized_revenue
        $this->assertEquals($expectedDeferredRevenue, $courseData['deferred_revenue']);
    }

    public function test_outstanding_receivables_report_calculations(): void
    {
        $response = $this->getJson('/reports/course-financial/outstanding-receivables/data');

        $response->assertOk();
        $data = $response->json();

        $receivablesData = $data['data'][0];

        // Test payment progress calculation
        $expectedPaymentProgress = (2000000 / 8000000) * 100; // (paid_amount / total_amount) * 100
        $this->assertEquals($expectedPaymentProgress, $receivablesData['payment_progress']);

        // Test outstanding amount calculation
        $expectedOutstanding = 8000000 - 2000000; // total_amount - paid_amount
        $this->assertEquals($expectedOutstanding, $receivablesData['outstanding_amount']);
    }

    public function test_revenue_recognition_report_with_multiple_enrollments(): void
    {
        // Create additional enrollment for the same batch
        $student2 = Customer::create([
            'name' => 'CV Teknologi Jaya',
            'email' => 'info@teknologijaya.com',
            'phone' => '081234567891',
            'company' => 'CV Teknologi Jaya'
        ]);

        $enrollment2 = Enrollment::create([
            'student_id' => $student2->id,
            'batch_id' => $this->batch->id,
            'payment_plan_id' => $this->paymentPlan->id,
            'enrollment_date' => now(),
            'total_amount' => 8000000,
            'status' => 'active'
        ]);

        $response = $this->getJson('/reports/course-financial/revenue-recognition/data');

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('data', $data);

        // Verify batch data shows 2 enrollments
        $batchData = $data['data'][0];
        $this->assertEquals(2, $batchData['total_enrollments']);
        $this->assertEquals(16000000, $batchData['total_deferred']); // 2 * 8M
    }

    public function test_payment_collection_report_with_multiple_payments(): void
    {
        // Create additional payment
        InstallmentPayment::create([
            'enrollment_id' => $this->enrollment->id,
            'installment_number' => 2,
            'due_date' => now()->addDays(30),
            'amount' => 2000000,
            'status' => 'paid',
            'paid_at' => now()->addDays(15)
        ]);

        $response = $this->getJson('/reports/course-financial/payment-collection/data');

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('data', $data);

        // Verify payment data shows updated amounts
        $paymentData = $data['data'][0];
        $this->assertEquals(4000000, $paymentData['paid_amount']); // 2 payments
        $this->assertEquals(4000000, $paymentData['outstanding_amount']); // 8M - 4M
        $this->assertEquals(50, $paymentData['payment_progress']); // 4M / 8M * 100
    }

    public function test_course_profitability_report_export(): void
    {
        $response = $this->get('/reports/course-financial/profitability/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="Course_Profitability_Report_' . now()->format('Y-m-d') . '.xlsx"');
    }

    public function test_outstanding_receivables_report_export(): void
    {
        $response = $this->get('/reports/course-financial/outstanding-receivables/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="Outstanding_Receivables_Report_' . now()->format('Y-m-d') . '.xlsx"');
    }

    public function test_revenue_recognition_report_export(): void
    {
        $response = $this->get('/reports/course-financial/revenue-recognition/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="Revenue_Recognition_Report_' . now()->format('Y-m-d') . '.xlsx"');
    }

    public function test_payment_collection_report_export(): void
    {
        $response = $this->get('/reports/course-financial/payment-collection/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->assertHeader('Content-Disposition', 'attachment; filename="Payment_Collection_Report_' . now()->format('Y-m-d') . '.xlsx"');
    }

    public function test_course_financial_reports_with_no_data(): void
    {
        // Clear all test data
        Enrollment::truncate();
        CourseBatch::truncate();
        Course::truncate();
        CourseCategory::truncate();

        $response = $this->getJson('/reports/course-financial/profitability/data');

        $response->assertOk();
        $data = $response->json();

        $this->assertArrayHasKey('data', $data);
        $this->assertEmpty($data['data']);
        $this->assertEquals(0, $data['recordsTotal']);
        $this->assertEquals(0, $data['recordsFiltered']);
    }

    public function test_course_financial_reports_performance(): void
    {
        // Create multiple courses and enrollments for performance testing
        for ($i = 1; $i <= 10; $i++) {
            $course = Course::create([
                'code' => "DM-00{$i}",
                'name' => "Digital Marketing Course {$i}",
                'description' => "Course {$i} description",
                'category_id' => $this->category->id,
                'base_price' => 8000000,
                'status' => 'active'
            ]);

            $batch = CourseBatch::create([
                'course_id' => $course->id,
                'batch_code' => "DM-BATCH-00{$i}",
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(30),
                'capacity' => 20,
                'status' => 'scheduled'
            ]);

            Enrollment::create([
                'student_id' => $this->student->id,
                'batch_id' => $batch->id,
                'payment_plan_id' => $this->paymentPlan->id,
                'enrollment_date' => now(),
                'total_amount' => 8000000,
                'status' => 'active'
            ]);
        }

        $startTime = microtime(true);

        $response = $this->getJson('/reports/course-financial/profitability/data');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertOk();

        // Assert that the report loads within 2 seconds
        $this->assertLessThan(2.0, $executionTime, 'Report should load within 2 seconds');

        $data = $response->json();
        $this->assertGreaterThan(10, $data['recordsTotal']);
    }
}
