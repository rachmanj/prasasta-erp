<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ControlAccount;
use App\Models\SubsidiaryLedgerAccount;
use App\Models\Accounting\Account;
use App\Models\Master\Customer;
use App\Models\Master\Vendor;
use Illuminate\Support\Facades\DB;

class ControlAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->createControlAccounts();
            $this->createSubsidiaryAccounts();
        });
    }

    private function createControlAccounts(): void
    {
        $controlAccounts = [
            [
                'code' => '1.1.4',
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'control_type' => 'ar',
                'reconciliation_frequency' => 'monthly',
                'tolerance_amount' => 100.00,
                'description' => 'Control account for all customer receivables',
            ],
            [
                'code' => '2.1.1',
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'control_type' => 'ap',
                'reconciliation_frequency' => 'monthly',
                'tolerance_amount' => 100.00,
                'description' => 'Control account for all vendor payables',
            ],
            [
                'code' => '1.1.1',
                'name' => 'Cash and Bank',
                'type' => 'asset',
                'control_type' => 'cash',
                'reconciliation_frequency' => 'daily',
                'tolerance_amount' => 10.00,
                'description' => 'Control account for all cash and bank accounts',
            ],
            [
                'code' => '1.2.1',
                'name' => 'Inventory',
                'type' => 'asset',
                'control_type' => 'inventory',
                'reconciliation_frequency' => 'monthly',
                'tolerance_amount' => 500.00,
                'description' => 'Control account for all inventory items',
            ],
            [
                'code' => '1.3.1',
                'name' => 'Fixed Assets',
                'type' => 'asset',
                'control_type' => 'fixed_assets',
                'reconciliation_frequency' => 'monthly',
                'tolerance_amount' => 1000.00,
                'description' => 'Control account for all fixed assets',
            ],
        ];

        foreach ($controlAccounts as $accountData) {
            ControlAccount::updateOrCreate(
                ['code' => $accountData['code']],
                $accountData
            );
        }
    }

    private function createSubsidiaryAccounts(): void
    {
        // Create AR Subsidiary Accounts from existing customers
        $arControlAccount = ControlAccount::where('control_type', 'ar')->first();
        if ($arControlAccount) {
            $customers = Customer::all();
            foreach ($customers as $customer) {
                SubsidiaryLedgerAccount::updateOrCreate(
                    [
                        'control_account_id' => $arControlAccount->id,
                        'subsidiary_code' => 'CUST-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT),
                    ],
                    [
                        'name' => $customer->name,
                        'subsidiary_type' => 'customer',
                        'opening_balance' => 0.00,
                        'current_balance' => 0.00,
                        'is_active' => true,
                        'metadata' => [
                            'customer_id' => $customer->id,
                            'customer_code' => $customer->code,
                            'contact_person' => $customer->contact_person,
                            'phone' => $customer->phone,
                            'email' => $customer->email,
                        ],
                    ]
                );
            }
        }

        // Create AP Subsidiary Accounts from existing vendors
        $apControlAccount = ControlAccount::where('control_type', 'ap')->first();
        if ($apControlAccount) {
            $vendors = Vendor::all();
            foreach ($vendors as $vendor) {
                SubsidiaryLedgerAccount::updateOrCreate(
                    [
                        'control_account_id' => $apControlAccount->id,
                        'subsidiary_code' => 'VEND-' . str_pad($vendor->id, 4, '0', STR_PAD_LEFT),
                    ],
                    [
                        'name' => $vendor->name,
                        'subsidiary_type' => 'vendor',
                        'opening_balance' => 0.00,
                        'current_balance' => 0.00,
                        'is_active' => true,
                        'metadata' => [
                            'vendor_id' => $vendor->id,
                            'vendor_code' => $vendor->code,
                            'contact_person' => $vendor->contact_person,
                            'phone' => $vendor->phone,
                            'email' => $vendor->email,
                        ],
                    ]
                );
            }
        }

        // Create Cash Subsidiary Accounts from existing bank accounts
        $cashControlAccount = ControlAccount::where('control_type', 'cash')->first();
        if ($cashControlAccount) {
            $bankAccounts = DB::table('bank_accounts')->get();
            foreach ($bankAccounts as $bankAccount) {
                SubsidiaryLedgerAccount::updateOrCreate(
                    [
                        'control_account_id' => $cashControlAccount->id,
                        'subsidiary_code' => 'BANK-' . str_pad($bankAccount->id, 4, '0', STR_PAD_LEFT),
                    ],
                    [
                        'name' => $bankAccount->bank_name . ' - ' . $bankAccount->account_number,
                        'subsidiary_type' => 'other',
                        'opening_balance' => 0.00,
                        'current_balance' => 0.00,
                        'is_active' => true,
                        'metadata' => [
                            'bank_account_id' => $bankAccount->id,
                            'bank_name' => $bankAccount->bank_name,
                            'account_number' => $bankAccount->account_number,
                            'account_type' => $bankAccount->account_type,
                        ],
                    ]
                );
            }
        }

        // Create Inventory Subsidiary Accounts by category
        $inventoryControlAccount = ControlAccount::where('control_type', 'inventory')->first();
        if ($inventoryControlAccount) {
            $inventoryCategories = [
                'RAW' => 'Raw Materials',
                'WIP' => 'Work in Progress',
                'FG' => 'Finished Goods',
                'SP' => 'Spare Parts',
                'SUP' => 'Supplies',
            ];

            foreach ($inventoryCategories as $code => $name) {
                SubsidiaryLedgerAccount::updateOrCreate(
                    [
                        'control_account_id' => $inventoryControlAccount->id,
                        'subsidiary_code' => $code,
                    ],
                    [
                        'name' => $name,
                        'subsidiary_type' => 'category',
                        'opening_balance' => 0.00,
                        'current_balance' => 0.00,
                        'is_active' => true,
                        'metadata' => [
                            'category_type' => 'inventory',
                            'description' => 'Inventory category: ' . $name,
                        ],
                    ]
                );
            }
        }

        // Create Fixed Assets Subsidiary Accounts by category
        $fixedAssetsControlAccount = ControlAccount::where('control_type', 'fixed_assets')->first();
        if ($fixedAssetsControlAccount) {
            $assetCategories = DB::table('asset_categories')->get();
            foreach ($assetCategories as $category) {
                SubsidiaryLedgerAccount::updateOrCreate(
                    [
                        'control_account_id' => $fixedAssetsControlAccount->id,
                        'subsidiary_code' => 'AST-' . str_pad($category->id, 4, '0', STR_PAD_LEFT),
                    ],
                    [
                        'name' => $category->name,
                        'subsidiary_type' => 'category',
                        'opening_balance' => 0.00,
                        'current_balance' => 0.00,
                        'is_active' => true,
                        'metadata' => [
                            'asset_category_id' => $category->id,
                            'category_type' => 'fixed_assets',
                            'description' => 'Asset category: ' . $category->name,
                        ],
                    ]
                );
            }
        }
    }
}
