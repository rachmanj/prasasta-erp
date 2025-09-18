<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Master\Customer;
use App\Models\Master\Vendor;
use App\Models\Dimensions\Project;
use App\Models\Dimensions\Fund;
use App\Models\Dimensions\Department;
use App\Models\CourseCategory;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Trainer;
use App\Models\PaymentPlan;
use App\Models\AssetCategory;
use App\Models\Asset;
use Illuminate\Support\Facades\Hash;

class TrainingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createTrainingUsers();
        $this->createTrainingCustomers();
        $this->createTrainingVendors();
        $this->createTrainingProjects();
        $this->createTrainingFunds();
        $this->createTrainingDepartments();
        $this->createTrainingCourseCategories();
        $this->createTrainingCourses();
        $this->createTrainingCourseBatches();
        $this->createTrainingTrainers();
        $this->createTrainingPaymentPlans();
        // $this->createTrainingAssetCategories();
        // $this->createTrainingAssets();
    }

    private function createTrainingUsers()
    {
        $trainingUsers = [
            [
                'name' => 'Dewi Kartika',
                'username' => 'dewi.accountant',
                'email' => 'dewi@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'accountant',
            ],
            [
                'name' => 'Bambang Sutrisno',
                'username' => 'bambang.trainer',
                'email' => 'bambang@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'accountant',
            ],
            [
                'name' => 'Sari Indah',
                'username' => 'sari.cashier',
                'email' => 'sari@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ],
        ];

        foreach ($trainingUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }
    }

    private function createTrainingCustomers()
    {
        $customers = [
            [
                'code' => 'CUST-001',
                'name' => 'PT Maju Bersama',
                'email' => 'info@majubersama.com',
                'phone' => '021-12345678',
                'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'npwp' => '01.234.567.8-901.000',
                'student_status' => 'active',
                'enrollment_count' => 0,
                'total_paid' => 0,
            ],
            [
                'code' => 'CUST-002',
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@email.com',
                'phone' => '081234567890',
                'address' => 'Jl. Thamrin No. 456, Jakarta Pusat',
                'npwp' => null,
                'student_status' => 'active',
                'enrollment_count' => 0,
                'total_paid' => 0,
            ],
            [
                'code' => 'CUST-003',
                'name' => 'CV Teknologi Mandiri',
                'email' => 'contact@tekmandiri.com',
                'phone' => '021-87654321',
                'address' => 'Jl. Gatot Subroto No. 789, Jakarta Selatan',
                'npwp' => '02.345.678.9-012.000',
                'student_status' => 'active',
                'enrollment_count' => 0,
                'total_paid' => 0,
            ],
            [
                'code' => 'CUST-004',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'phone' => '081987654321',
                'address' => 'Jl. Kebon Jeruk No. 321, Jakarta Barat',
                'npwp' => null,
                'student_status' => 'active',
                'enrollment_count' => 0,
                'total_paid' => 0,
            ],
            [
                'code' => 'CUST-005',
                'name' => 'Yayasan Pendidikan Indonesia',
                'email' => 'info@ypi.or.id',
                'phone' => '021-55555555',
                'address' => 'Jl. Pendidikan No. 100, Jakarta Timur',
                'npwp' => '03.456.789.0-123.000',
                'student_status' => 'active',
                'enrollment_count' => 0,
                'total_paid' => 0,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['code' => $customerData['code']],
                $customerData
            );
        }
    }

    private function createTrainingVendors()
    {
        $vendors = [
            [
                'code' => 'VEND-001',
                'name' => 'PT Komputer Maju',
                'email' => 'sales@komputermaju.com',
                'phone' => '021-11111111',
            ],
            [
                'code' => 'VEND-002',
                'name' => 'PT Office Supplies',
                'email' => 'order@officesupplies.com',
                'phone' => '021-22222222',
            ],
            [
                'code' => 'VEND-003',
                'name' => 'Dr. Ahmad Wijaya',
                'email' => 'ahmad.wijaya@email.com',
                'phone' => '081333333333',
            ],
            [
                'code' => 'VEND-004',
                'name' => 'PT Cleaning Services',
                'email' => 'service@cleaning.com',
                'phone' => '021-44444444',
            ],
            [
                'code' => 'VEND-005',
                'name' => 'PT Internet Provider',
                'email' => 'support@internet.com',
                'phone' => '021-55555555',
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::updateOrCreate(
                ['code' => $vendorData['code']],
                $vendorData
            );
        }
    }

    private function createTrainingProjects()
    {
        $projects = [
            [
                'code' => 'PRJ-001',
                'name' => 'Digital Marketing Course Development',
            ],
            [
                'code' => 'PRJ-002',
                'name' => 'Data Analytics Training Program',
            ],
            [
                'code' => 'PRJ-003',
                'name' => 'IT Infrastructure Upgrade',
            ],
            [
                'code' => 'PRJ-004',
                'name' => 'Corporate Training Program',
            ],
            [
                'code' => 'PRJ-005',
                'name' => 'Scholarship Program',
            ],
        ];

        foreach ($projects as $projectData) {
            Project::updateOrCreate(
                ['code' => $projectData['code']],
                $projectData
            );
        }
    }

    private function createTrainingFunds()
    {
        $funds = [
            [
                'code' => 'FUND-001',
                'name' => 'General Operating Fund',
            ],
            [
                'code' => 'FUND-002',
                'name' => 'Scholarship Fund',
            ],
            [
                'code' => 'FUND-003',
                'name' => 'Equipment Fund',
            ],
            [
                'code' => 'FUND-004',
                'name' => 'Research & Development Fund',
            ],
            [
                'code' => 'FUND-005',
                'name' => 'Emergency Reserve Fund',
            ],
        ];

        foreach ($funds as $fundData) {
            Fund::updateOrCreate(
                ['code' => $fundData['code']],
                $fundData
            );
        }
    }

    private function createTrainingDepartments()
    {
        $departments = [
            [
                'code' => 'DEPT-001',
                'name' => 'IT Department',
            ],
            [
                'code' => 'DEPT-002',
                'name' => 'Finance Department',
            ],
            [
                'code' => 'DEPT-003',
                'name' => 'Training Department',
            ],
            [
                'code' => 'DEPT-004',
                'name' => 'Marketing Department',
            ],
            [
                'code' => 'DEPT-005',
                'name' => 'Administration Department',
            ],
        ];

        foreach ($departments as $departmentData) {
            Department::updateOrCreate(
                ['code' => $departmentData['code']],
                $departmentData
            );
        }
    }

    private function createTrainingCourseCategories()
    {
        $categories = [
            [
                'code' => 'CAT-001',
                'name' => 'Digital Marketing',
                'description' => 'Digital marketing and online advertising courses',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CAT-002',
                'name' => 'Data Analytics',
                'description' => 'Data analysis and business intelligence courses',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CAT-003',
                'name' => 'Project Management',
                'description' => 'Project management and leadership courses',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CAT-004',
                'name' => 'IT Fundamentals',
                'description' => 'Basic IT and computer skills courses',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'code' => 'CAT-005',
                'name' => 'Social Media Marketing',
                'description' => 'Social media marketing specialization',
                'parent_id' => 1, // Digital Marketing
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            CourseCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }
    }

    private function createTrainingCourses()
    {
        $courses = [
            [
                'code' => 'COURSE-001',
                'name' => 'Digital Marketing Fundamentals',
                'description' => 'Comprehensive digital marketing course covering all major platforms and strategies',
                'category_id' => 1, // Digital Marketing
                'duration_hours' => 40,
                'capacity' => 20,
                'base_price' => 8000000,
            ],
            [
                'code' => 'COURSE-002',
                'name' => 'Data Analytics with Python',
                'description' => 'Advanced data analytics using Python and machine learning',
                'category_id' => 2, // Data Analytics
                'duration_hours' => 60,
                'capacity' => 15,
                'base_price' => 12000000,
            ],
            [
                'code' => 'COURSE-003',
                'name' => 'Project Management Professional',
                'description' => 'PMP certification preparation course',
                'category_id' => 3, // Project Management
                'duration_hours' => 35,
                'capacity' => 25,
                'base_price' => 10000000,
            ],
            [
                'code' => 'COURSE-004',
                'name' => 'IT Fundamentals for Beginners',
                'description' => 'Basic IT skills and computer literacy',
                'category_id' => 4, // IT Fundamentals
                'duration_hours' => 30,
                'capacity' => 30,
                'base_price' => 5000000,
            ],
            [
                'code' => 'COURSE-005',
                'name' => 'Social Media Marketing Mastery',
                'description' => 'Advanced social media marketing strategies',
                'category_id' => 5, // Social Media Marketing
                'duration_hours' => 25,
                'capacity' => 18,
                'base_price' => 6000000,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::updateOrCreate(
                ['code' => $courseData['code']],
                $courseData
            );
        }
    }

    private function createTrainingCourseBatches()
    {
        $batches = [
            [
                'course_id' => 1, // Digital Marketing Fundamentals
                'batch_code' => 'DM-2025-01',
                'start_date' => '2025-02-01',
                'end_date' => '2025-02-28',
                'schedule' => ['Monday', 'Wednesday', 'Friday'],
                'location' => 'Jakarta Training Center',
                'trainer_id' => 1,
                'capacity' => 20,
            ],
            [
                'course_id' => 2, // Data Analytics with Python
                'batch_code' => 'DA-2025-01',
                'start_date' => '2025-03-01',
                'end_date' => '2025-03-31',
                'schedule' => ['Tuesday', 'Thursday'],
                'location' => 'Jakarta Training Center',
                'trainer_id' => 2,
                'capacity' => 15,
            ],
            [
                'course_id' => 3, // Project Management Professional
                'batch_code' => 'PMP-2025-01',
                'start_date' => '2025-04-01',
                'end_date' => '2025-04-30',
                'schedule' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'location' => 'Jakarta Training Center',
                'trainer_id' => 3,
                'capacity' => 25,
            ],
            [
                'course_id' => 4, // IT Fundamentals for Beginners
                'batch_code' => 'IT-2025-01',
                'start_date' => '2025-01-15',
                'end_date' => '2025-01-31',
                'schedule' => ['Saturday', 'Sunday'],
                'location' => 'Jakarta Training Center',
                'trainer_id' => 4,
                'capacity' => 30,
            ],
            [
                'course_id' => 5, // Social Media Marketing Mastery
                'batch_code' => 'SMM-2025-01',
                'start_date' => '2025-05-01',
                'end_date' => '2025-05-31',
                'schedule' => ['Wednesday', 'Friday'],
                'location' => 'Jakarta Training Center',
                'trainer_id' => 5,
                'capacity' => 18,
            ],
        ];

        foreach ($batches as $batchData) {
            CourseBatch::updateOrCreate(
                ['batch_code' => $batchData['batch_code']],
                $batchData
            );
        }
    }

    private function createTrainingTrainers()
    {
        $trainers = [
            [
                'code' => 'TRN-001',
                'name' => 'Dr. Ahmad Wijaya',
                'email' => 'ahmad.wijaya@email.com',
                'phone' => '081333333333',
                'qualifications' => 'PhD in Marketing, Certified Digital Marketing Professional',
                'specialization' => 'Digital Marketing, Social Media Marketing, Brand Management',
                'type' => 'external',
                'status' => 'active',
            ],
            [
                'code' => 'TRN-002',
                'name' => 'Bambang Sutrisno',
                'email' => 'bambang@prasasta.com',
                'phone' => '081444444444',
                'qualifications' => 'MSc in Data Science, Certified Data Analyst',
                'specialization' => 'Data Analytics, Machine Learning, Python Programming',
                'type' => 'internal',
                'status' => 'active',
            ],
            [
                'code' => 'TRN-003',
                'name' => 'Sari Indah',
                'email' => 'sari.indah@email.com',
                'phone' => '081555555555',
                'qualifications' => 'PMP Certified, MBA in Project Management',
                'specialization' => 'Project Management, Agile Methodology, Leadership',
                'type' => 'external',
                'status' => 'active',
            ],
            [
                'code' => 'TRN-004',
                'name' => 'Dewi Kartika',
                'email' => 'dewi.kartika@prasasta.com',
                'phone' => '081666666666',
                'qualifications' => 'BSc in Computer Science, Microsoft Certified Trainer',
                'specialization' => 'IT Fundamentals, Computer Literacy, Office Applications',
                'type' => 'internal',
                'status' => 'active',
            ],
            [
                'code' => 'TRN-005',
                'name' => 'Joko Widodo',
                'email' => 'joko.widodo@email.com',
                'phone' => '081777777777',
                'qualifications' => 'Certified Social Media Marketing Professional',
                'specialization' => 'Social Media Marketing, Content Creation, Influencer Marketing',
                'type' => 'external',
                'status' => 'active',
            ],
        ];

        foreach ($trainers as $trainerData) {
            Trainer::updateOrCreate(
                ['code' => $trainerData['code']],
                $trainerData
            );
        }
    }

    private function createTrainingPaymentPlans()
    {
        $paymentPlans = [
            [
                'code' => 'PLAN-001',
                'name' => 'Full Payment',
                'description' => 'Pay full amount upfront',
                'down_payment_percentage' => 100,
                'installment_count' => 1,
                'installment_interval_days' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'PLAN-002',
                'name' => '2 Installments',
                'description' => 'Pay in 2 equal installments',
                'down_payment_percentage' => 50,
                'installment_count' => 2,
                'installment_interval_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'PLAN-003',
                'name' => '3 Installments',
                'description' => 'Pay in 3 equal installments',
                'down_payment_percentage' => 33.33,
                'installment_count' => 3,
                'installment_interval_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'PLAN-004',
                'name' => '4 Installments',
                'description' => 'Pay in 4 equal installments',
                'down_payment_percentage' => 25,
                'installment_count' => 4,
                'installment_interval_days' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'PLAN-005',
                'name' => '6 Installments',
                'description' => 'Pay in 6 equal installments',
                'down_payment_percentage' => 16.67,
                'installment_count' => 6,
                'installment_interval_days' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($paymentPlans as $planData) {
            PaymentPlan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
        }
    }

    private function createTrainingAssetCategories()
    {
        $categories = [
            [
                'code' => 'IT-EQ',
                'name' => 'IT Equipment',
                'description' => 'Computers, laptops, servers, and IT infrastructure',
                'life_months_default' => 36,
                'method_default' => 'straight_line',
                'salvage_value_policy' => 10,
                'non_depreciable' => false,
                'is_active' => true,
            ],
            [
                'code' => 'OFF-FUR',
                'name' => 'Office Furniture',
                'description' => 'Desks, chairs, cabinets, and office furniture',
                'life_months_default' => 60,
                'method_default' => 'straight_line',
                'salvage_value_policy' => 5,
                'non_depreciable' => false,
                'is_active' => true,
            ],
            [
                'code' => 'VEHICLE',
                'name' => 'Vehicles',
                'description' => 'Company vehicles and transportation equipment',
                'life_months_default' => 60,
                'method_default' => 'straight_line',
                'salvage_value_policy' => 20,
                'non_depreciable' => false,
                'is_active' => true,
            ],
            [
                'code' => 'BUILDING',
                'name' => 'Buildings',
                'description' => 'Office buildings and training facilities',
                'life_months_default' => 240,
                'method_default' => 'straight_line',
                'salvage_value_policy' => 10,
                'non_depreciable' => false,
                'is_active' => true,
            ],
            [
                'code' => 'LAND',
                'name' => 'Land',
                'description' => 'Land and real estate',
                'life_months_default' => 0,
                'method_default' => 'straight_line',
                'salvage_value_policy' => 100,
                'non_depreciable' => true,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            AssetCategory::updateOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }
    }

    private function createTrainingAssets()
    {
        $assets = [
            [
                'code' => 'LAPTOP-001',
                'name' => 'Dell Laptop - Training Lab 1',
                'description' => 'Dell Inspiron 15 3000 for training purposes',
                'serial_number' => 'DL001234567',
                'category_id' => 1, // IT Equipment
                'acquisition_cost' => 8500000,
                'salvage_value' => 850000,
                'method' => 'straight_line',
                'life_months' => 36,
                'placed_in_service_date' => '2025-01-01',
                'status' => 'active',
                'fund_id' => 3, // Equipment Fund
                'project_id' => 3, // IT Infrastructure Upgrade
                'department_id' => 1, // IT Department
                'vendor_id' => 1, // PT Komputer Maju
            ],
            [
                'code' => 'LAPTOP-002',
                'name' => 'Dell Laptop - Training Lab 2',
                'description' => 'Dell Inspiron 15 3000 for training purposes',
                'serial_number' => 'DL001234568',
                'category_id' => 1, // IT Equipment
                'acquisition_cost' => 8500000,
                'salvage_value' => 850000,
                'method' => 'straight_line',
                'life_months' => 36,
                'placed_in_service_date' => '2025-01-01',
                'status' => 'active',
                'fund_id' => 3, // Equipment Fund
                'project_id' => 3, // IT Infrastructure Upgrade
                'department_id' => 1, // IT Department
                'vendor_id' => 1, // PT Komputer Maju
            ],
            [
                'code' => 'DESK-001',
                'name' => 'Office Desk - Executive',
                'description' => 'Executive office desk with drawers',
                'serial_number' => 'DESK001',
                'category_id' => 2, // Office Furniture
                'acquisition_cost' => 2500000,
                'salvage_value' => 125000,
                'method' => 'straight_line',
                'life_months' => 60,
                'placed_in_service_date' => '2025-01-15',
                'status' => 'active',
                'fund_id' => 1, // General Operating Fund
                'project_id' => null,
                'department_id' => 2, // Finance Department
                'vendor_id' => 2, // PT Office Supplies
            ],
            [
                'code' => 'CAR-001',
                'name' => 'Company Car - Toyota Avanza',
                'description' => 'Toyota Avanza for company transportation',
                'serial_number' => 'CAR001',
                'category_id' => 3, // Vehicles
                'acquisition_cost' => 150000000,
                'salvage_value' => 30000000,
                'method' => 'straight_line',
                'life_months' => 60,
                'placed_in_service_date' => '2025-01-01',
                'status' => 'active',
                'fund_id' => 1, // General Operating Fund
                'project_id' => null,
                'department_id' => 5, // Administration Department
                'vendor_id' => null,
            ],
            [
                'code' => 'PROJECTOR-001',
                'name' => 'Epson Projector - Training Room',
                'description' => 'Epson PowerLite 1781W for training presentations',
                'serial_number' => 'PROJ001',
                'category_id' => 1, // IT Equipment
                'acquisition_cost' => 3500000,
                'salvage_value' => 350000,
                'method' => 'straight_line',
                'life_months' => 36,
                'placed_in_service_date' => '2024-12-01',
                'status' => 'disposed',
                'fund_id' => 3, // Equipment Fund
                'project_id' => 3, // IT Infrastructure Upgrade
                'department_id' => 3, // Training Department
                'vendor_id' => 1, // PT Komputer Maju
            ],
        ];

        foreach ($assets as $assetData) {
            Asset::updateOrCreate(
                ['code' => $assetData['code']],
                $assetData
            );
        }
    }
}
