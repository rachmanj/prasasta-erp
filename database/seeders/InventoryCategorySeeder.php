<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InventoryCategory;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'OFFICE',
                'name' => 'Office Supplies',
                'description' => 'Office supplies and stationery items',
                'type' => 'item',
                'is_active' => true,
            ],
            [
                'code' => 'TRAINING',
                'name' => 'Training Materials',
                'description' => 'Training materials and educational supplies',
                'type' => 'item',
                'is_active' => true,
            ],
            [
                'code' => 'EQUIPMENT',
                'name' => 'Equipment Parts',
                'description' => 'Equipment parts and maintenance supplies',
                'type' => 'item',
                'is_active' => true,
            ],
            [
                'code' => 'CONSUMABLES',
                'name' => 'Consumables',
                'description' => 'Consumable items and supplies',
                'type' => 'item',
                'is_active' => true,
            ],
            [
                'code' => 'IT_SUPPLIES',
                'name' => 'IT Supplies',
                'description' => 'IT equipment and supplies',
                'type' => 'item',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            InventoryCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
