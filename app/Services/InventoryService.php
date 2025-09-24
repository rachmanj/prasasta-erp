<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockLayer;
use App\Models\StockMovement;
use App\Models\Accounting\Account;
use App\Services\Accounting\PostingService;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    protected $postingService;

    public function __construct(PostingService $postingService)
    {
        $this->postingService = $postingService;
    }
    /**
     * Add stock to inventory (Goods Receipt)
     */
    public function addStock(Item $item, float $quantity, float $unitCost, string $referenceType = null, int $referenceId = null, string $referenceNumber = null, string $notes = null, array $dimensions = []): void
    {
        // Services don't have inventory tracking
        if ($item->isService()) {
            return;
        }

        DB::transaction(function () use ($item, $quantity, $unitCost, $referenceType, $referenceId, $referenceNumber, $notes, $dimensions) {
            // Create FIFO stock layer
            StockLayer::create([
                'item_id' => $item->id,
                'purchase_date' => now()->toDateString(),
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'remaining_quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            // Create stock movement record
            $stockMovement = StockMovement::create([
                'item_id' => $item->id,
                'movement_date' => now()->toDateString(),
                'movement_type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            // Update item stock quantities and costs
            $this->updateItemStock($item, $quantity, $unitCost, 'in');

            // Create journal entry for inventory increase
            $this->createInventoryJournalEntry($item, $quantity, $unitCost, 'in', $stockMovement->id, $referenceNumber, $notes, $dimensions);
        });
    }

    /**
     * Remove stock from inventory (Sales Invoice)
     */
    public function removeStock(Item $item, float $quantity, string $referenceType = null, int $referenceId = null, string $referenceNumber = null, string $notes = null, array $dimensions = []): array
    {
        // Services don't have inventory tracking
        if ($item->isService()) {
            return [
                'total_cost' => 0,
                'average_unit_cost' => 0
            ];
        }

        return DB::transaction(function () use ($item, $quantity, $referenceType, $referenceId, $referenceNumber, $notes, $dimensions) {
            $totalCost = 0;
            $remainingQuantity = $quantity;

            // FIFO consumption - get oldest layers first
            $layers = StockLayer::forItem($item->id)
                ->available()
                ->oldestFirst()
                ->get();

            foreach ($layers as $layer) {
                if ($remainingQuantity <= 0) break;

                $consumeQuantity = min($remainingQuantity, $layer->remaining_quantity);
                $totalCost += $consumeQuantity * $layer->unit_cost;

                // Update layer remaining quantity
                $layer->update([
                    'remaining_quantity' => $layer->remaining_quantity - $consumeQuantity
                ]);

                $remainingQuantity -= $consumeQuantity;
            }

            if ($remainingQuantity > 0) {
                throw new \Exception("Insufficient stock for item {$item->name}. Available: " . $item->current_stock_quantity);
            }

            // Create stock movement record
            $stockMovement = StockMovement::create([
                'item_id' => $item->id,
                'movement_date' => now()->toDateString(),
                'movement_type' => 'out',
                'quantity' => $quantity,
                'unit_cost' => $totalCost / $quantity,
                'total_cost' => $totalCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            // Update item stock quantities
            $this->updateItemStock($item, $quantity, 0, 'out');

            // Create journal entry for inventory decrease
            $this->createInventoryJournalEntry($item, $quantity, $totalCost / $quantity, 'out', $stockMovement->id, $referenceNumber, $notes, $dimensions);

            return [
                'total_cost' => $totalCost,
                'average_unit_cost' => $totalCost / $quantity
            ];
        });
    }

    /**
     * Adjust stock quantity (Stock Adjustment)
     */
    public function adjustStock(Item $item, float $newQuantity, float $unitCost, string $referenceType = null, int $referenceId = null, string $referenceNumber = null, string $notes = null, array $dimensions = []): void
    {
        // Services don't have inventory tracking
        if ($item->isService()) {
            return;
        }

        DB::transaction(function () use ($item, $newQuantity, $unitCost, $referenceType, $referenceId, $referenceNumber, $notes, $dimensions) {
            $currentQuantity = $item->current_stock_quantity;
            $variance = $newQuantity - $currentQuantity;

            // Create stock movement record
            $stockMovement = StockMovement::create([
                'item_id' => $item->id,
                'movement_date' => now()->toDateString(),
                'movement_type' => 'adjustment',
                'quantity' => abs($variance),
                'unit_cost' => $unitCost,
                'total_cost' => abs($variance) * $unitCost,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            if ($variance > 0) {
                // Stock increase - create new layer
                StockLayer::create([
                    'item_id' => $item->id,
                    'purchase_date' => now()->toDateString(),
                    'quantity' => $variance,
                    'unit_cost' => $unitCost,
                    'remaining_quantity' => $variance,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                ]);
            } elseif ($variance < 0) {
                // Stock decrease - consume from existing layers FIFO
                $this->consumeFromLayers($item, abs($variance));
            }

            // Update item stock quantities
            $this->updateItemStock($item, $newQuantity, $unitCost, 'adjustment');

            // Create journal entry for stock adjustment
            $this->createInventoryJournalEntry($item, abs($variance), $unitCost, 'adjustment', $stockMovement->id, $referenceNumber, $notes, $dimensions);
        });
    }

    /**
     * Update item stock quantities and costs
     */
    private function updateItemStock(Item $item, float $quantity, float $unitCost, string $operation): void
    {
        $currentQuantity = $item->current_stock_quantity;
        $currentValue = $item->current_stock_value;

        switch ($operation) {
            case 'in':
                $newQuantity = $currentQuantity + $quantity;
                $newValue = $currentValue + ($quantity * $unitCost);
                $newAverageCost = $newQuantity > 0 ? $newValue / $newQuantity : 0;

                $item->update([
                    'current_stock_quantity' => $newQuantity,
                    'current_stock_value' => $newValue,
                    'last_cost_price' => $unitCost,
                    'average_cost_price' => $newAverageCost,
                ]);
                break;

            case 'out':
                $newQuantity = $currentQuantity - $quantity;
                $newValue = $newQuantity > 0 ? $newQuantity * $item->average_cost_price : 0;

                $item->update([
                    'current_stock_quantity' => $newQuantity,
                    'current_stock_value' => $newValue,
                ]);
                break;

            case 'adjustment':
                $newQuantity = $quantity;
                $newValue = $newQuantity * $unitCost;

                $item->update([
                    'current_stock_quantity' => $newQuantity,
                    'current_stock_value' => $newValue,
                    'last_cost_price' => $unitCost,
                    'average_cost_price' => $unitCost,
                ]);
                break;
        }
    }

    /**
     * Consume from existing stock layers (FIFO)
     */
    private function consumeFromLayers(Item $item, float $quantity): void
    {
        $remainingQuantity = $quantity;

        $layers = StockLayer::forItem($item->id)
            ->available()
            ->oldestFirst()
            ->get();

        foreach ($layers as $layer) {
            if ($remainingQuantity <= 0) break;

            $consumeQuantity = min($remainingQuantity, $layer->remaining_quantity);

            $layer->update([
                'remaining_quantity' => $layer->remaining_quantity - $consumeQuantity
            ]);

            $remainingQuantity -= $consumeQuantity;
        }
    }

    /**
     * Get available stock quantity for an item
     */
    public function getAvailableStock(Item $item): float
    {
        return $item->current_stock_quantity;
    }

    /**
     * Check if item has sufficient stock
     */
    public function hasSufficientStock(Item $item, float $requiredQuantity): bool
    {
        return $item->current_stock_quantity >= $requiredQuantity;
    }

    /**
     * Get items with low stock
     */
    public function getLowStockItems(): \Illuminate\Database\Eloquent\Collection
    {
        return Item::active()->lowStock()->with('category')->get();
    }

    /**
     * Process goods receipt - add stock to inventory
     */
    public function processGoodsReceipt(\App\Models\GoodsReceipt $goodsReceipt): void
    {
        DB::transaction(function () use ($goodsReceipt) {
            foreach ($goodsReceipt->lines as $line) {
                if ($line->item_id) {
                    $item = Item::find($line->item_id);
                    if ($item) {
                        $this->addStock(
                            $item,
                            $line->qty,
                            $line->unit_price,
                            'goods_receipt',
                            $goodsReceipt->id,
                            $goodsReceipt->grn_no,
                            $line->description
                        );
                    }
                }
            }
        });
    }

    /**
     * Process sales invoice - remove stock from inventory
     */
    public function processSalesInvoice(\App\Models\Accounting\SalesInvoice $salesInvoice): void
    {
        DB::transaction(function () use ($salesInvoice) {
            foreach ($salesInvoice->lines as $line) {
                if ($line->item_id) {
                    $item = Item::find($line->item_id);
                    if ($item) {
                        try {
                            $this->removeStock(
                                $item,
                                $line->qty,
                                'sales_invoice',
                                $salesInvoice->id,
                                $salesInvoice->invoice_no,
                                $line->description
                            );
                        } catch (\Exception $e) {
                            // Log error but don't fail the entire transaction
                            \Log::error("Stock removal failed for item {$item->id}: " . $e->getMessage());
                        }
                    }
                }
            }
        });
    }

    /**
     * Check if a sales invoice can be processed (sufficient stock available)
     */
    public function canProcessSalesInvoice(\App\Models\Accounting\SalesInvoice $salesInvoice): array
    {
        $issues = [];

        foreach ($salesInvoice->lines as $line) {
            if ($line->item_id) {
                $item = Item::find($line->item_id);
                if ($item && !$this->hasSufficientStock($item, $line->qty)) {
                    $availableStock = $this->getAvailableStock($item);
                    $issues[] = [
                        'item' => $item,
                        'required' => $line->qty,
                        'available' => $availableStock,
                        'shortage' => $line->qty - $availableStock
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Get inventory items for dropdown selection
     */
    public function getInventoryItemsForDropdown(): \Illuminate\Database\Eloquent\Collection
    {
        return Item::active()
            ->with('category')
            ->orderBy('name')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'code' => $item->code,
                    'name' => $item->name,
                    'category' => $item->category->name,
                    'current_stock' => $item->current_stock_quantity,
                    'unit_of_measure' => $item->unit_of_measure,
                    'last_cost_price' => $item->last_cost_price ?? 0,
                    'display_text' => $item->code . ' - ' . $item->name . ' (Stock: ' . $item->current_stock_quantity . ' ' . $item->unit_of_measure . ')'
                ];
            });
    }

    /**
     * Auto-assign inventory account based on item category
     */
    public function getInventoryAccountForItem(Item $item): ?int
    {
        // Map inventory categories to chart of accounts
        $categoryAccountMap = [
            'Office Supplies' => '1.1.12', // Inventory - Supplies & Consumables
            'Training Materials' => '1.1.11', // Inventory - Finished Goods
            'Equipment Parts' => '1.1.10', // Inventory - Work in Progress
            'Consumables' => '1.1.12', // Inventory - Supplies & Consumables
            'IT Supplies' => '1.1.12', // Inventory - Supplies & Consumables
        ];

        $accountCode = $categoryAccountMap[$item->category->name] ?? '1.1.12';

        return Account::where('code', $accountCode)->first()?->id;
    }

    /**
     * Get Cost of Goods Sold account
     */
    public function getCostOfGoodsSoldAccount(): ?int
    {
        return Account::where('code', '5.1.5')->first()?->id; // Cost of Goods Sold
    }

    /**
     * Auto-assign accounts to an item based on its category
     */
    public function assignAccountsToItem(Item $item): void
    {
        $inventoryAccountId = $this->getInventoryAccountForItem($item);
        $cogsAccountId = $this->getCostOfGoodsSoldAccount();

        $item->update([
            'inventory_account_id' => $inventoryAccountId,
            'cost_of_goods_sold_account_id' => $cogsAccountId,
        ]);
    }

    /**
     * Get all inventory-related accounts for dropdown selection
     */
    public function getInventoryAccountsForDropdown(): \Illuminate\Database\Eloquent\Collection
    {
        return Account::whereIn('code', [
            '1.1.9',  // Inventory - Raw Materials
            '1.1.10', // Inventory - Work in Progress
            '1.1.11', // Inventory - Finished Goods
            '1.1.12', // Inventory - Supplies & Consumables
        ])->orderBy('code')->get();
    }

    /**
     * Get Cost of Goods Sold accounts for dropdown selection
     */
    public function getCostOfGoodsSoldAccountsForDropdown(): \Illuminate\Database\Eloquent\Collection
    {
        return Account::where('code', '5.1.5') // Cost of Goods Sold
            ->orderBy('code')
            ->get();
    }

    /**
     * Create journal entry for inventory movements
     */
    private function createInventoryJournalEntry(Item $item, float $quantity, float $unitCost, string $movementType, int $stockMovementId, string $referenceNumber = null, string $notes = null, array $dimensions = []): void
    {
        $totalAmount = $quantity * $unitCost;
        $description = $this->getJournalDescription($movementType, $item, $referenceNumber, $notes);

        $lines = [];

        switch ($movementType) {
            case 'in':
                // Goods Receipt: Debit Inventory, Credit Accounts Payable/Cash
                $lines[] = [
                    'account_id' => $item->inventory_account_id,
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'project_id' => $dimensions['project_id'] ?? null,
                    'fund_id' => $dimensions['fund_id'] ?? null,
                    'dept_id' => $dimensions['dept_id'] ?? null,
                    'memo' => $notes,
                ];
                // Credit side will be handled by the calling process (e.g., Purchase Invoice)
                break;

            case 'out':
                // Sales Invoice: Debit Cost of Goods Sold, Credit Inventory
                $lines[] = [
                    'account_id' => $item->cost_of_goods_sold_account_id,
                    'debit' => $totalAmount,
                    'credit' => 0,
                    'project_id' => $dimensions['project_id'] ?? null,
                    'fund_id' => $dimensions['fund_id'] ?? null,
                    'dept_id' => $dimensions['dept_id'] ?? null,
                    'memo' => $notes,
                ];
                $lines[] = [
                    'account_id' => $item->inventory_account_id,
                    'debit' => 0,
                    'credit' => $totalAmount,
                    'project_id' => $dimensions['project_id'] ?? null,
                    'fund_id' => $dimensions['fund_id'] ?? null,
                    'dept_id' => $dimensions['dept_id'] ?? null,
                    'memo' => $notes,
                ];
                break;

            case 'adjustment':
                // Stock Adjustment: Debit/Credit Inventory, Credit/Debit Inventory Adjustment Account
                $adjustmentAccountId = Account::where('code', '5.1.6')->first()?->id; // Inventory Adjustment Account

                if ($quantity > 0) {
                    // Stock increase
                    $lines[] = [
                        'account_id' => $item->inventory_account_id,
                        'debit' => $totalAmount,
                        'credit' => 0,
                        'project_id' => $dimensions['project_id'] ?? null,
                        'fund_id' => $dimensions['fund_id'] ?? null,
                        'dept_id' => $dimensions['dept_id'] ?? null,
                        'memo' => $notes,
                    ];
                    $lines[] = [
                        'account_id' => $adjustmentAccountId,
                        'debit' => 0,
                        'credit' => $totalAmount,
                        'project_id' => $dimensions['project_id'] ?? null,
                        'fund_id' => $dimensions['fund_id'] ?? null,
                        'dept_id' => $dimensions['dept_id'] ?? null,
                        'memo' => $notes,
                    ];
                } else {
                    // Stock decrease
                    $lines[] = [
                        'account_id' => $adjustmentAccountId,
                        'debit' => $totalAmount,
                        'credit' => 0,
                        'project_id' => $dimensions['project_id'] ?? null,
                        'fund_id' => $dimensions['fund_id'] ?? null,
                        'dept_id' => $dimensions['dept_id'] ?? null,
                        'memo' => $notes,
                    ];
                    $lines[] = [
                        'account_id' => $item->inventory_account_id,
                        'debit' => 0,
                        'credit' => $totalAmount,
                        'project_id' => $dimensions['project_id'] ?? null,
                        'fund_id' => $dimensions['fund_id'] ?? null,
                        'dept_id' => $dimensions['dept_id'] ?? null,
                        'memo' => $notes,
                    ];
                }
                break;
        }

        if (!empty($lines)) {
            $this->postingService->postJournal([
                'date' => now()->toDateString(),
                'description' => $description,
                'source_type' => 'stock_movement',
                'source_id' => $stockMovementId,
                'status' => 'posted',
                'posted_by' => auth()->id(),
                'lines' => $lines,
            ]);
        }
    }

    /**
     * Generate journal description for inventory movements
     */
    private function getJournalDescription(string $movementType, Item $item, string $referenceNumber = null, string $notes = null): string
    {
        $descriptions = [
            'in' => 'Inventory Receipt',
            'out' => 'Inventory Issue',
            'adjustment' => 'Stock Adjustment',
        ];

        $baseDescription = $descriptions[$movementType] ?? 'Inventory Movement';
        $description = "{$baseDescription} - {$item->code} - {$item->name}";

        if ($referenceNumber) {
            $description .= " ({$referenceNumber})";
        }

        if ($notes) {
            $description .= " - {$notes}";
        }

        return $description;
    }
}
