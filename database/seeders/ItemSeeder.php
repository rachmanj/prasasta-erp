<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\InventoryCategory;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Office Supplies
            [
                'code' => 'OFF-001',
                'name' => 'A4 Paper (500 sheets)',
                'description' => 'White A4 paper, 80gsm, 500 sheets per ream',
                'barcode' => '123456789001',
                'category_code' => 'OFFICE',
                'unit_of_measure' => 'ream',
                'min_stock_level' => 10,
                'max_stock_level' => 100,
                'is_active' => true,
            ],
            [
                'code' => 'OFF-002',
                'name' => 'Ballpoint Pen (Blue)',
                'description' => 'Blue ballpoint pen, pack of 12',
                'barcode' => '123456789002',
                'category_code' => 'OFFICE',
                'unit_of_measure' => 'pack',
                'min_stock_level' => 5,
                'max_stock_level' => 50,
                'is_active' => true,
            ],
            [
                'code' => 'OFF-003',
                'name' => 'Stapler',
                'description' => 'Standard office stapler',
                'barcode' => '123456789003',
                'category_code' => 'OFFICE',
                'unit_of_measure' => 'pcs',
                'min_stock_level' => 2,
                'max_stock_level' => 20,
                'is_active' => true,
            ],

            // Training Materials
            [
                'code' => 'TRN-001',
                'name' => 'Training Manual (Basic)',
                'description' => 'Basic training manual, 100 pages',
                'barcode' => '123456789004',
                'category_code' => 'TRAINING',
                'unit_of_measure' => 'pcs',
                'min_stock_level' => 20,
                'max_stock_level' => 200,
                'is_active' => true,
            ],
            [
                'code' => 'TRN-002',
                'name' => 'Whiteboard Marker Set',
                'description' => 'Set of 4 whiteboard markers (black, blue, red, green)',
                'barcode' => '123456789005',
                'category_code' => 'TRAINING',
                'unit_of_measure' => 'set',
                'min_stock_level' => 3,
                'max_stock_level' => 30,
                'is_active' => true,
            ],

            // IT Supplies
            [
                'code' => 'IT-001',
                'name' => 'USB Flash Drive 16GB',
                'description' => 'USB 3.0 flash drive, 16GB capacity',
                'barcode' => '123456789006',
                'category_code' => 'IT_SUPPLIES',
                'unit_of_measure' => 'pcs',
                'min_stock_level' => 5,
                'max_stock_level' => 50,
                'is_active' => true,
            ],
            [
                'code' => 'IT-002',
                'name' => 'Network Cable CAT6',
                'description' => 'CAT6 network cable, 5 meters',
                'barcode' => '123456789007',
                'category_code' => 'IT_SUPPLIES',
                'unit_of_measure' => 'pcs',
                'min_stock_level' => 10,
                'max_stock_level' => 100,
                'is_active' => true,
            ],

            // Equipment Parts
            [
                'code' => 'EQP-001',
                'name' => 'Printer Toner Cartridge',
                'description' => 'HP LaserJet toner cartridge, black',
                'barcode' => '123456789008',
                'category_code' => 'EQUIPMENT',
                'unit_of_measure' => 'pcs',
                'min_stock_level' => 2,
                'max_stock_level' => 20,
                'is_active' => true,
            ],

            // Consumables
            [
                'code' => 'CON-001',
                'name' => 'Cleaning Supplies Kit',
                'description' => 'Basic cleaning supplies kit',
                'barcode' => '123456789009',
                'category_code' => 'CONSUMABLES',
                'unit_of_measure' => 'kit',
                'min_stock_level' => 1,
                'max_stock_level' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($items as $itemData) {
            $category = InventoryCategory::where('code', $itemData['category_code'])->first();
            if ($category) {
                unset($itemData['category_code']);
                $itemData['category_id'] = $category->id;
                $itemData['type'] = 'item';
                $itemData['cost_method'] = 'fifo';

                Item::updateOrCreate(
                    ['code' => $itemData['code']],
                    $itemData
                );
            }
        }
    }
}
