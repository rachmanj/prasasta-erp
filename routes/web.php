<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Dev\PostingDemoController;
use App\Http\Controllers\Accounting\ManualJournalController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Accounting\PeriodController;
use App\Http\Controllers\Accounting\SalesInvoiceController;
use App\Http\Controllers\Accounting\PurchaseInvoiceController;
use App\Http\Controllers\Accounting\SalesReceiptController;
use App\Http\Controllers\Accounting\PurchasePaymentController;
use App\Http\Controllers\Accounting\AccountController;
use App\Http\Controllers\Accounting\CashExpenseController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseBatchController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\InstallmentPaymentController;
use App\Http\Controllers\RevenueRecognitionController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\RevenueReportController;
use App\Http\Controllers\CourseReportController;
use App\Http\Controllers\TrainerReportController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\FinancialDashboardController;
use App\Http\Controllers\OperationalDashboardController;
use App\Http\Controllers\PerformanceDashboardController;
use App\Http\Controllers\Dimensions\ProjectController as DimProjectController;
use App\Http\Controllers\Dimensions\FundController as DimFundController;
use App\Http\Controllers\Dimensions\DepartmentController as DimDepartmentController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\AssetDepreciationController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\AssetMovementController;
use App\Http\Controllers\AssetImportController;
use App\Http\Controllers\AssetDataQualityController;
use App\Http\Controllers\Banking\BankingDashboardController;
use App\Http\Controllers\Banking\CashOutController;
use App\Http\Controllers\Banking\CashInController;
use App\Http\Controllers\ControlAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    require __DIR__ . '/web/reports.php';

    Route::prefix('dev')->group(function () {
        Route::post('/post-journal', [PostingDemoController::class, 'store'])->name('dev.post-journal');
    });

    require __DIR__ . '/web/journals.php';

    require __DIR__ . '/web/orders.php';

    // Periods
    Route::prefix('periods')->group(function () {
        Route::get('/', [PeriodController::class, 'index'])->middleware('permission:periods.view')->name('periods.index');
        Route::post('/close', [PeriodController::class, 'close'])->middleware('permission:periods.close')->name('periods.close');
        Route::post('/open', [PeriodController::class, 'open'])->middleware('permission:periods.close')->name('periods.open');
    });

    require __DIR__ . '/web/orders.php';
    require __DIR__ . '/web/ar_ap.php';

    // Customers (Sales group)
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/data', [CustomerController::class, 'data'])->name('customers.data');
        Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::patch('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    });

    // Vendors (Purchase group)
    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendors.index');
        Route::get('/data', [VendorController::class, 'data'])->name('vendors.data');
        Route::get('/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('/', [VendorController::class, 'store'])->name('vendors.store');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
        Route::get('/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
        Route::patch('/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
        Route::get('/{vendor}/assets', [VendorController::class, 'assets'])->name('vendors.assets');
        Route::get('/{vendor}/purchase-orders', [VendorController::class, 'purchaseOrders'])->name('vendors.purchase-orders');
        Route::get('/{vendor}/asset-acquisition-history', [VendorController::class, 'assetAcquisitionHistory'])->name('vendors.asset-acquisition-history');
    });

    // Course Management
    Route::prefix('course-management')->group(function () {
        // Course Categories
        Route::prefix('course-categories')->group(function () {
            Route::get('/', [CourseCategoryController::class, 'index'])->name('course-categories.index');
            Route::get('/data', [CourseCategoryController::class, 'data'])->name('course-categories.data');
            Route::get('/create', [CourseCategoryController::class, 'create'])->name('course-categories.create');
            Route::post('/', [CourseCategoryController::class, 'store'])->name('course-categories.store');
            Route::get('/{courseCategory}/edit', [CourseCategoryController::class, 'edit'])->name('course-categories.edit');
            Route::patch('/{courseCategory}', [CourseCategoryController::class, 'update'])->name('course-categories.update');
        });

        // Courses
        Route::prefix('courses')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('courses.index');
            Route::get('/data', [CourseController::class, 'data'])->name('courses.data');
            Route::get('/create', [CourseController::class, 'create'])->name('courses.create');
            Route::post('/', [CourseController::class, 'store'])->name('courses.store');
            Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
            Route::patch('/{course}', [CourseController::class, 'update'])->name('courses.update');
            Route::delete('/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        });

        // Course Batches
        Route::prefix('course-batches')->group(function () {
            Route::get('/', [CourseBatchController::class, 'index'])->name('course-batches.index');
            Route::get('/data', [CourseBatchController::class, 'data'])->name('course-batches.data');
            Route::get('/create', [CourseBatchController::class, 'create'])->name('course-batches.create');
            Route::post('/', [CourseBatchController::class, 'store'])->name('course-batches.store');
            Route::get('/{courseBatch}/edit', [CourseBatchController::class, 'edit'])->name('course-batches.edit');
            Route::patch('/{courseBatch}', [CourseBatchController::class, 'update'])->name('course-batches.update');
            Route::delete('/{courseBatch}', [CourseBatchController::class, 'destroy'])->name('course-batches.destroy');
        });

        // Enrollments
        Route::prefix('enrollments')->group(function () {
            Route::get('/', [EnrollmentController::class, 'index'])->name('enrollments.index');
            Route::get('/data', [EnrollmentController::class, 'data'])->name('enrollments.data');
            Route::get('/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
            Route::post('/', [EnrollmentController::class, 'store'])->name('enrollments.store');
            Route::get('/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('enrollments.edit');
            Route::patch('/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
            Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        });

        // Trainers
        Route::prefix('trainers')->group(function () {
            Route::get('/', [TrainerController::class, 'index'])->name('trainers.index');
            Route::get('/data', [TrainerController::class, 'data'])->name('trainers.data');
            Route::get('/create', [TrainerController::class, 'create'])->name('trainers.create');
            Route::post('/', [TrainerController::class, 'store'])->name('trainers.store');
            Route::get('/{trainer}/edit', [TrainerController::class, 'edit'])->name('trainers.edit');
            Route::patch('/{trainer}', [TrainerController::class, 'update'])->name('trainers.update');
            Route::delete('/{trainer}', [TrainerController::class, 'destroy'])->name('trainers.destroy');
        });

        // Payment Plans
        Route::prefix('payment-plans')->group(function () {
            Route::get('/', [PaymentPlanController::class, 'index'])->name('payment-plans.index');
            Route::get('/data', [PaymentPlanController::class, 'data'])->name('payment-plans.data');
            Route::get('/create', [PaymentPlanController::class, 'create'])->name('payment-plans.create');
            Route::post('/', [PaymentPlanController::class, 'store'])->name('payment-plans.store');
            Route::get('/{paymentPlan}/edit', [PaymentPlanController::class, 'edit'])->name('payment-plans.edit');
            Route::patch('/{paymentPlan}', [PaymentPlanController::class, 'update'])->name('payment-plans.update');
            Route::delete('/{paymentPlan}', [PaymentPlanController::class, 'destroy'])->name('payment-plans.destroy');
        });

        // Installment Payments
        Route::prefix('installment-payments')->group(function () {
            Route::get('/', [InstallmentPaymentController::class, 'index'])->name('installment-payments.index');
            Route::get('/data', [InstallmentPaymentController::class, 'data'])->name('installment-payments.data');
            Route::get('/create', [InstallmentPaymentController::class, 'create'])->name('installment-payments.create');
            Route::post('/', [InstallmentPaymentController::class, 'store'])->name('installment-payments.store');
            Route::get('/{installmentPayment}/edit', [InstallmentPaymentController::class, 'edit'])->name('installment-payments.edit');
            Route::patch('/{installmentPayment}', [InstallmentPaymentController::class, 'update'])->name('installment-payments.update');
            Route::delete('/{installmentPayment}', [InstallmentPaymentController::class, 'destroy'])->name('installment-payments.destroy');
            Route::post('/{installmentPayment}/process-payment', [InstallmentPaymentController::class, 'processPayment'])->name('installment-payments.process-payment');
            Route::post('/enrollments/{enrollment}/generate-installments', [InstallmentPaymentController::class, 'generateInstallments'])->name('installment-payments.generate-installments');
            Route::post('/update-overdue', [InstallmentPaymentController::class, 'updateOverdueInstallments'])->name('installment-payments.update-overdue');
        });

        // Revenue Recognition
        Route::prefix('revenue-recognition')->group(function () {
            Route::get('/', [RevenueRecognitionController::class, 'index'])->name('revenue-recognition.index');
            Route::get('/data', [RevenueRecognitionController::class, 'data'])->name('revenue-recognition.data');
            Route::get('/create', [RevenueRecognitionController::class, 'create'])->name('revenue-recognition.create');
            Route::post('/', [RevenueRecognitionController::class, 'store'])->name('revenue-recognition.store');
            Route::get('/{revenueRecognition}/edit', [RevenueRecognitionController::class, 'edit'])->name('revenue-recognition.edit');
            Route::patch('/{revenueRecognition}', [RevenueRecognitionController::class, 'update'])->name('revenue-recognition.update');
            Route::delete('/{revenueRecognition}', [RevenueRecognitionController::class, 'destroy'])->name('revenue-recognition.destroy');
            Route::post('/{revenueRecognition}/recognize', [RevenueRecognitionController::class, 'recognize'])->name('revenue-recognition.recognize');
            Route::post('/{revenueRecognition}/reverse', [RevenueRecognitionController::class, 'reverse'])->name('revenue-recognition.reverse');
            Route::post('/batches/{batch}/recognize-batch-revenue', [RevenueRecognitionController::class, 'recognizeBatchRevenue'])->name('revenue-recognition.recognize-batch-revenue');
            Route::post('/enrollments/{enrollment}/generate-deferred-revenue', [RevenueRecognitionController::class, 'generateDeferredRevenue'])->name('revenue-recognition.generate-deferred-revenue');
        });
    });

    // Reports
    Route::prefix('reports')->group(function () {
        // Payment Reports
        Route::prefix('payment')->group(function () {
            Route::get('/', [PaymentReportController::class, 'index'])->name('reports.payment.index');
            Route::get('/aging', [PaymentReportController::class, 'agingReport'])->name('reports.payment.aging');
            Route::get('/collection', [PaymentReportController::class, 'collectionReport'])->name('reports.payment.collection');
            Route::get('/overdue', [PaymentReportController::class, 'overdueReport'])->name('reports.payment.overdue');
            Route::get('/aging/export', [PaymentReportController::class, 'exportAgingReport'])->name('reports.payment.aging.export');
            Route::get('/collection/export', [PaymentReportController::class, 'exportCollectionReport'])->name('reports.payment.collection.export');
            Route::get('/overdue/export', [PaymentReportController::class, 'exportOverdueReport'])->name('reports.payment.overdue.export');
        });

        // Revenue Reports
        Route::prefix('revenue')->group(function () {
            Route::get('/', [RevenueReportController::class, 'index'])->name('reports.revenue.index');
            Route::get('/recognition', [RevenueReportController::class, 'recognitionReport'])->name('reports.revenue.recognition');
            Route::get('/deferred', [RevenueReportController::class, 'deferredRevenueReport'])->name('reports.revenue.deferred');
            Route::get('/recognition/export', [RevenueReportController::class, 'exportRecognitionReport'])->name('reports.revenue.recognition.export');
            Route::get('/deferred/export', [RevenueReportController::class, 'exportDeferredRevenueReport'])->name('reports.revenue.deferred.export');
        });

        // Course Reports
        Route::prefix('course')->group(function () {
            Route::get('/', [CourseReportController::class, 'index'])->name('reports.course.index');
            Route::get('/performance', [CourseReportController::class, 'performanceReport'])->name('reports.course.performance');
            Route::get('/enrollment', [CourseReportController::class, 'enrollmentReport'])->name('reports.course.enrollment');
            Route::get('/capacity', [CourseReportController::class, 'capacityReport'])->name('reports.course.capacity');
            Route::get('/performance/export', [CourseReportController::class, 'exportPerformanceReport'])->name('reports.course.performance.export');
            Route::get('/enrollment/export', [CourseReportController::class, 'exportEnrollmentReport'])->name('reports.course.enrollment.export');
            Route::get('/capacity/export', [CourseReportController::class, 'exportCapacityReport'])->name('reports.course.capacity.export');
        });

        // Trainer Reports
        Route::prefix('trainer')->group(function () {
            Route::get('/', [TrainerReportController::class, 'index'])->name('reports.trainer.index');
            Route::get('/performance', [TrainerReportController::class, 'performanceReport'])->name('reports.trainer.performance');
            Route::get('/utilization', [TrainerReportController::class, 'utilizationReport'])->name('reports.trainer.utilization');
            Route::get('/revenue', [TrainerReportController::class, 'revenueReport'])->name('reports.trainer.revenue');
            Route::get('/performance/export', [TrainerReportController::class, 'exportPerformanceReport'])->name('reports.trainer.performance.export');
            Route::get('/utilization/export', [TrainerReportController::class, 'exportUtilizationReport'])->name('reports.trainer.utilization.export');
            Route::get('/revenue/export', [TrainerReportController::class, 'exportRevenueReport'])->name('reports.trainer.revenue.export');
        });
    });

    // Dashboards
    Route::prefix('dashboard')->group(function () {
        Route::get('/executive', [ExecutiveDashboardController::class, 'index'])->name('dashboard.executive.index');
        Route::get('/executive/data', [ExecutiveDashboardController::class, 'data'])->name('dashboard.executive.data');
        Route::get('/financial', [FinancialDashboardController::class, 'index'])->name('dashboard.financial.index');
        Route::get('/financial/data', [FinancialDashboardController::class, 'data'])->name('dashboard.financial.data');
        Route::get('/operational', [OperationalDashboardController::class, 'index'])->name('dashboard.operational.index');
        Route::get('/operational/data', [OperationalDashboardController::class, 'data'])->name('dashboard.operational.data');
        Route::get('/performance', [PerformanceDashboardController::class, 'index'])->name('dashboard.performance.index');
        Route::get('/performance/data', [PerformanceDashboardController::class, 'data'])->name('dashboard.performance.data');
    });

    // Accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('accounts.index');
        Route::get('/create', [AccountController::class, 'create'])->name('accounts.create');
        Route::post('/', [AccountController::class, 'store'])->name('accounts.store');
        Route::get('/{account}', [AccountController::class, 'show'])->middleware('permission:accounts.view_transactions')->name('accounts.show');
        Route::get('/{account}/transactions', [AccountController::class, 'transactionsData'])->middleware('permission:accounts.view_transactions')->name('accounts.transactions.data');
        Route::get('/{account}/transactions/export', [AccountController::class, 'transactionsExport'])->middleware('permission:accounts.view_transactions')->name('accounts.transactions.export');
        Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::patch('/{account}', [AccountController::class, 'update'])->name('accounts.update');
        Route::delete('/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    });

    // Control Accounts
    Route::prefix('control-accounts')->middleware(['permission:control_accounts.view'])->group(function () {
        Route::get('/', [ControlAccountController::class, 'index'])->name('control-accounts.index');
        Route::get('/data', [ControlAccountController::class, 'data'])->name('control-accounts.data');
        Route::get('/create', [ControlAccountController::class, 'create'])->name('control-accounts.create');
        Route::post('/', [ControlAccountController::class, 'store'])->name('control-accounts.store');
        Route::get('/{controlAccount}', [ControlAccountController::class, 'show'])->name('control-accounts.show');
        Route::get('/{controlAccount}/edit', [ControlAccountController::class, 'edit'])->name('control-accounts.edit');
        Route::patch('/{controlAccount}', [ControlAccountController::class, 'update'])->name('control-accounts.update');
        Route::delete('/{controlAccount}', [ControlAccountController::class, 'destroy'])->name('control-accounts.destroy');

        // Reconciliation
        Route::get('/{controlAccount}/reconciliation', [ControlAccountController::class, 'reconciliation'])->name('control-accounts.reconciliation');
        Route::post('/{controlAccount}/reconcile', [ControlAccountController::class, 'reconcile'])->name('control-accounts.reconcile');

        // Subsidiary Accounts
        // Subsidiary Accounts Management
        Route::get('/{controlAccount}/subsidiary-accounts', [ControlAccountController::class, 'subsidiaryAccounts'])->name('control-accounts.subsidiary-accounts');
        Route::get('/{controlAccount}/subsidiary-accounts/data', [ControlAccountController::class, 'subsidiaryAccountsData'])->name('control-accounts.subsidiary-accounts.data');
        Route::post('/{controlAccount}/subsidiary-accounts', [ControlAccountController::class, 'storeSubsidiaryAccount'])->name('control-accounts.subsidiary-accounts.store');
        Route::get('/{controlAccount}/subsidiary-accounts/{subsidiaryAccount}', [ControlAccountController::class, 'showSubsidiaryAccount'])->name('control-accounts.subsidiary-accounts.show');
        Route::put('/{controlAccount}/subsidiary-accounts/{subsidiaryAccount}', [ControlAccountController::class, 'updateSubsidiaryAccount'])->name('control-accounts.subsidiary-accounts.update');
        Route::delete('/{controlAccount}/subsidiary-accounts/{subsidiaryAccount}', [ControlAccountController::class, 'destroySubsidiaryAccount'])->name('control-accounts.subsidiary-accounts.destroy');

        // Balance History
        Route::get('/{controlAccount}/balances', [ControlAccountController::class, 'balances'])->name('control-accounts.balances');
        Route::get('/{controlAccount}/balances/data', [ControlAccountController::class, 'balancesData'])->name('control-accounts.balances.data');

        // Dashboard Data
        Route::get('/dashboard/data', [ControlAccountController::class, 'dashboardData'])->name('control-accounts.dashboard.data');
    });

    // Cash Expenses
    Route::prefix('cash-expenses')->group(function () {
        Route::get('/', [CashExpenseController::class, 'index'])->name('cash-expenses.index');
        Route::get('/data', [CashExpenseController::class, 'data'])->name('cash-expenses.data');
        Route::get('/create', [CashExpenseController::class, 'create'])->name('cash-expenses.create');
        Route::post('/', [CashExpenseController::class, 'store'])->name('cash-expenses.store');
        Route::get('/{cashExpense}/print', [CashExpenseController::class, 'print'])->name('cash-expenses.print');
    });

    // Banking Module
    Route::prefix('banking')->middleware(['permission:banking.view'])->group(function () {
        // Banking Dashboard
        Route::get('/dashboard', [BankingDashboardController::class, 'index'])->name('banking.dashboard.index');
        Route::get('/dashboard/data', [BankingDashboardController::class, 'data'])->name('banking.dashboard.data');

        // Cash Out
        Route::prefix('cash-out')->middleware(['permission:banking.cash_out'])->group(function () {
            Route::get('/', [CashOutController::class, 'index'])->name('banking.cash-out.index');
            Route::get('/data', [CashOutController::class, 'data'])->name('banking.cash-out.data');
            Route::get('/create', [CashOutController::class, 'create'])->name('banking.cash-out.create');
            Route::post('/', [CashOutController::class, 'store'])->name('banking.cash-out.store');
            Route::get('/{cashOut}/print', [CashOutController::class, 'print'])->name('banking.cash-out.print');
        });

        // Cash In
        Route::prefix('cash-in')->middleware(['permission:banking.cash_in'])->group(function () {
            Route::get('/', [CashInController::class, 'index'])->name('banking.cash-in.index');
            Route::get('/data', [CashInController::class, 'data'])->name('banking.cash-in.data');
            Route::get('/create', [CashInController::class, 'create'])->name('banking.cash-in.create');
            Route::post('/', [CashInController::class, 'store'])->name('banking.cash-in.store');
            Route::get('/{cashIn}/print', [CashInController::class, 'print'])->name('banking.cash-in.print');
        });
    });

    // Admin - Users, Roles, Permissions
    Route::prefix('admin')->middleware(['permission:view-admin'])->group(function () {
        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
        Route::get('/users/data', [AdminUserController::class, 'data'])->name('admin.users.data');
        Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
        Route::post('/users/{user}/roles', [AdminUserController::class, 'syncRoles'])->name('admin.users.syncRoles');

        // Roles
        Route::get('/roles', [AdminRoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('admin.roles.create');
        Route::get('/roles/{role}/edit', [AdminRoleController::class, 'edit'])->name('admin.roles.edit');
        Route::get('/roles/data', [AdminRoleController::class, 'data'])->name('admin.roles.data');
        Route::post('/roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
        Route::patch('/roles/{role}', [AdminRoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
        Route::post('/roles/{role}/permissions', [AdminRoleController::class, 'syncPermissions'])->name('admin.roles.syncPermissions');
        // Permissions
        Route::get('/permissions', [AdminPermissionController::class, 'index'])->name('admin.permissions.index');
        Route::get('/permissions/data', [AdminPermissionController::class, 'data'])->name('admin.permissions.data');
        Route::post('/permissions', [AdminPermissionController::class, 'store'])->name('admin.permissions.store');
        Route::patch('/permissions/{permission}', [AdminPermissionController::class, 'update'])->name('admin.permissions.update');
        Route::delete('/permissions/{permission}', [AdminPermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    });

    // Downloads
    Route::get('/downloads', [DownloadController::class, 'index'])->middleware('permission:reports.view')->name('downloads.index');

    // Dimensions Management
    Route::prefix('projects')->middleware(['permission:projects.view'])->group(function () {
        Route::get('/', [DimProjectController::class, 'index'])->name('projects.index');
        Route::get('/data', [DimProjectController::class, 'data'])->name('projects.data');
        Route::post('/', [DimProjectController::class, 'store'])->middleware('permission:projects.manage')->name('projects.store');
        Route::patch('/{id}', [DimProjectController::class, 'update'])->middleware('permission:projects.manage')->name('projects.update');
        Route::delete('/{id}', [DimProjectController::class, 'destroy'])->middleware('permission:projects.manage')->name('projects.destroy');
    });
    Route::prefix('funds')->middleware(['permission:funds.view'])->group(function () {
        Route::get('/', [DimFundController::class, 'index'])->name('funds.index');
        Route::get('/data', [DimFundController::class, 'data'])->name('funds.data');
        Route::post('/', [DimFundController::class, 'store'])->middleware('permission:funds.manage')->name('funds.store');
        Route::patch('/{id}', [DimFundController::class, 'update'])->middleware('permission:funds.manage')->name('funds.update');
        Route::delete('/{id}', [DimFundController::class, 'destroy'])->middleware('permission:funds.manage')->name('funds.destroy');
    });
    Route::prefix('departments')->middleware(['permission:departments.view'])->group(function () {
        Route::get('/', [DimDepartmentController::class, 'index'])->name('departments.index');
        Route::get('/data', [DimDepartmentController::class, 'data'])->name('departments.data');
        Route::post('/', [DimDepartmentController::class, 'store'])->middleware('permission:departments.manage')->name('departments.store');
        Route::patch('/{id}', [DimDepartmentController::class, 'update'])->middleware('permission:departments.manage')->name('departments.update');
        Route::delete('/{id}', [DimDepartmentController::class, 'destroy'])->middleware('permission:departments.manage')->name('departments.destroy');
    });

    // Fixed Assets Management
    Route::prefix('asset-categories')->middleware(['permission:asset_categories.view'])->group(function () {
        Route::get('/', [AssetCategoryController::class, 'index'])->name('asset-categories.index');
        Route::get('/data', [AssetCategoryController::class, 'data'])->name('asset-categories.data');
        Route::get('/accounts', [AssetCategoryController::class, 'getAccounts'])->name('asset-categories.accounts');
        Route::post('/', [AssetCategoryController::class, 'store'])->middleware('permission:asset_categories.manage')->name('asset-categories.store');
        Route::patch('/{assetCategory}', [AssetCategoryController::class, 'update'])->middleware('permission:asset_categories.manage')->name('asset-categories.update');
        Route::delete('/{assetCategory}', [AssetCategoryController::class, 'destroy'])->middleware('permission:asset_categories.manage')->name('asset-categories.destroy');
    });

    Route::prefix('assets')->middleware(['permission:assets.view'])->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/data', [AssetController::class, 'data'])->name('assets.data');
        Route::get('/create', [AssetController::class, 'create'])->middleware('permission:assets.create')->name('assets.create');
        Route::post('/', [AssetController::class, 'store'])->middleware('permission:assets.create')->name('assets.store');

        // Fixed Assets Depreciation - must be before /{asset} route
        Route::prefix('depreciation')->middleware(['permission:assets.depreciation.run'])->group(function () {
            Route::get('/', [AssetDepreciationController::class, 'index'])->name('assets.depreciation.index');
            Route::get('/data', [AssetDepreciationController::class, 'data'])->name('assets.depreciation.data');
            Route::get('/create', [AssetDepreciationController::class, 'create'])->name('assets.depreciation.create');
            Route::post('/', [AssetDepreciationController::class, 'store'])->name('assets.depreciation.store');
            Route::get('/{run}', [AssetDepreciationController::class, 'show'])->name('assets.depreciation.show');
            Route::get('/{run}/calculate', [AssetDepreciationController::class, 'calculate'])->name('assets.depreciation.calculate');
            Route::post('/{run}/entries', [AssetDepreciationController::class, 'createEntries'])->name('assets.depreciation.createEntries');
            Route::post('/{run}/post', [AssetDepreciationController::class, 'post'])->name('assets.depreciation.post');
            Route::post('/{run}/reverse', [AssetDepreciationController::class, 'reverse'])->name('assets.depreciation.reverse');
            Route::get('/{run}/entries', [AssetDepreciationController::class, 'entries'])->name('assets.depreciation.entries');
        });

        Route::get('/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->middleware('permission:assets.update')->name('assets.edit');
        Route::patch('/{asset}', [AssetController::class, 'update'])->middleware('permission:assets.update')->name('assets.update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->middleware('permission:assets.delete')->name('assets.destroy');
        Route::get('/categories', [AssetController::class, 'getCategories'])->name('assets.categories');
        Route::get('/funds', [AssetController::class, 'getFunds'])->name('assets.funds');
        Route::get('/projects', [AssetController::class, 'getProjects'])->name('assets.projects');
        Route::get('/departments', [AssetController::class, 'getDepartments'])->name('assets.departments');
        Route::get('/vendors', [AssetController::class, 'getVendors'])->name('assets.vendors');

        // Asset Import Routes
        Route::prefix('import')->middleware(['permission:assets.create'])->group(function () {
            Route::get('/', [AssetImportController::class, 'index'])->name('assets.import.index');
            Route::get('/template', [AssetImportController::class, 'template'])->name('assets.import.template');
            Route::post('/validate', [AssetImportController::class, 'validateImport'])->name('assets.import.validate');
            Route::post('/import', [AssetImportController::class, 'import'])->name('assets.import.import');
            Route::get('/reference-data', [AssetImportController::class, 'getReferenceData'])->name('assets.import.reference-data');
            Route::post('/bulk-update', [AssetImportController::class, 'bulkUpdate'])->middleware('permission:assets.update')->name('assets.import.bulk-update');
        });

        // Asset Data Quality Routes
        Route::prefix('data-quality')->middleware(['permission:assets.view'])->group(function () {
            Route::get('/', [AssetDataQualityController::class, 'index'])->name('assets.data-quality.index');
            Route::get('/duplicates', [AssetDataQualityController::class, 'duplicates'])->name('assets.data-quality.duplicates');
            Route::get('/incomplete', [AssetDataQualityController::class, 'incomplete'])->name('assets.data-quality.incomplete');
            Route::get('/consistency', [AssetDataQualityController::class, 'consistency'])->name('assets.data-quality.consistency');
            Route::get('/orphaned', [AssetDataQualityController::class, 'orphaned'])->name('assets.data-quality.orphaned');
            Route::get('/export', [AssetDataQualityController::class, 'exportReport'])->name('assets.data-quality.export');
            Route::get('/score', [AssetDataQualityController::class, 'getDataQualityScore'])->name('assets.data-quality.score');
            Route::post('/duplicate-details', [AssetDataQualityController::class, 'getDuplicateDetails'])->name('assets.data-quality.duplicate-details');
            Route::post('/assets-by-issue', [AssetDataQualityController::class, 'getAssetsByIssue'])->name('assets.data-quality.assets-by-issue');
        });

        // Asset Bulk Operations Routes
        Route::prefix('bulk-operations')->middleware(['permission:assets.update'])->group(function () {
            Route::get('/', [AssetController::class, 'bulkUpdateIndex'])->name('assets.bulk-operations.index');
            Route::get('/data', [AssetController::class, 'bulkUpdateData'])->name('assets.bulk-update.data');
            Route::post('/preview', [AssetController::class, 'bulkUpdatePreview'])->name('assets.bulk-update.preview');
            Route::post('/update', [AssetController::class, 'bulkUpdate'])->name('assets.bulk-update');
        });
    });


    // Asset depreciation schedule
    Route::get('/assets/{asset}/schedule', [AssetDepreciationController::class, 'schedule'])->middleware(['permission:assets.view'])->name('assets.schedule');

    // Asset Disposals
    Route::prefix('assets/disposals')->middleware(['permission:assets.disposal.view'])->group(function () {
        Route::get('/', [AssetDisposalController::class, 'index'])->name('assets.disposals.index');
        Route::get('/data', [AssetDisposalController::class, 'data'])->name('assets.disposals.data');
        Route::get('/create', [AssetDisposalController::class, 'create'])->middleware('permission:assets.disposal.create')->name('assets.disposals.create');
        Route::post('/', [AssetDisposalController::class, 'store'])->middleware('permission:assets.disposal.create')->name('assets.disposals.store');
        Route::get('/{disposal}', [AssetDisposalController::class, 'show'])->name('assets.disposals.show');
        Route::get('/{disposal}/edit', [AssetDisposalController::class, 'edit'])->middleware('permission:assets.disposal.update')->name('assets.disposals.edit');
        Route::patch('/{disposal}', [AssetDisposalController::class, 'update'])->middleware('permission:assets.disposal.update')->name('assets.disposals.update');
        Route::delete('/{disposal}', [AssetDisposalController::class, 'destroy'])->middleware('permission:assets.disposal.delete')->name('assets.disposals.destroy');
        Route::post('/{disposal}/post', [AssetDisposalController::class, 'post'])->middleware('permission:assets.disposal.post')->name('assets.disposals.post');
        Route::post('/{disposal}/reverse', [AssetDisposalController::class, 'reverse'])->middleware('permission:assets.disposal.reverse')->name('assets.disposals.reverse');
    });

    // Asset Movements
    Route::prefix('assets/movements')->middleware(['permission:assets.movement.view'])->group(function () {
        Route::get('/', [AssetMovementController::class, 'index'])->name('assets.movements.index');
        Route::get('/data', [AssetMovementController::class, 'data'])->name('assets.movements.data');
        Route::get('/create', [AssetMovementController::class, 'create'])->middleware('permission:assets.movement.create')->name('assets.movements.create');
        Route::post('/', [AssetMovementController::class, 'store'])->middleware('permission:assets.movement.create')->name('assets.movements.store');
        Route::get('/{movement}', [AssetMovementController::class, 'show'])->name('assets.movements.show');
        Route::get('/{movement}/edit', [AssetMovementController::class, 'edit'])->middleware('permission:assets.movement.update')->name('assets.movements.edit');
        Route::patch('/{movement}', [AssetMovementController::class, 'update'])->middleware('permission:assets.movement.update')->name('assets.movements.update');
        Route::delete('/{movement}', [AssetMovementController::class, 'destroy'])->middleware('permission:assets.movement.delete')->name('assets.movements.destroy');
        Route::post('/{movement}/approve', [AssetMovementController::class, 'approve'])->middleware('permission:assets.movement.approve')->name('assets.movements.approve');
        Route::post('/{movement}/complete', [AssetMovementController::class, 'complete'])->middleware('permission:assets.movement.update')->name('assets.movements.complete');
        Route::post('/{movement}/cancel', [AssetMovementController::class, 'cancel'])->middleware('permission:assets.movement.update')->name('assets.movements.cancel');
        Route::get('/asset/{asset}/history', [AssetMovementController::class, 'assetMovements'])->name('assets.movements.history');
    });
});

// Auth routes (AdminLTE views)
Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// GET logout route for testing purposes (development only)
Route::get('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
