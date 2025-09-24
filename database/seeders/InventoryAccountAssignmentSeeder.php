<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Services\InventoryService;

class InventoryAccountAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventoryService = new InventoryService();

        // Get all items that don't have accounts assigned
        $items = Item::whereNull('inventory_account_id')
            ->orWhereNull('cost_of_goods_sold_account_id')
            ->get();

        foreach ($items as $item) {
            // Assign inventory account if not set
            if (!$item->inventory_account_id) {
                $inventoryAccountId = $inventoryService->getInventoryAccountForItem($item);
                $item->update(['inventory_account_id' => $inventoryAccountId]);
            }

            // Assign COGS account if not set
            if (!$item->cost_of_goods_sold_account_id) {
                $cogsAccountId = $inventoryService->getCostOfGoodsSoldAccount();
                $item->update(['cost_of_goods_sold_account_id' => $cogsAccountId]);
            }
        }

        $this->command->info('Assigned accounts to ' . $items->count() . ' items.');
    }
}
