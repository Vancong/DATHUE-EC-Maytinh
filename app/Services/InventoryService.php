<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockImport;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Add stock quantity directly to a product and log the import history
     */
    public function addStock(Product $product, int $quantity, int $supplierId, ?string $note = null, ?int $createdBy = null): Product
    {
        return DB::transaction(function () use ($product, $quantity, $supplierId, $note, $createdBy) {
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();
            $lockedProduct->increment('stock_quantity', max(1, $quantity));

            ProductStockImport::create([
                'product_id' => $lockedProduct->id,
                'supplier_id' => $supplierId,
                'quantity' => max(1, $quantity),
                'note' => $note,
                'created_by' => $createdBy,
            ]);

            return $lockedProduct->fresh();
        });
    }

    /**
     * Update product stock quantity and minimum threshold directly
     */
    public function updateStock(Product $product, int $newQuantity, ?int $minStock = null): Product
    {
        return DB::transaction(function () use ($product, $newQuantity, $minStock) {
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();

            $updateData = ['stock_quantity' => max(0, $newQuantity)];
            if ($minStock !== null) {
                $updateData['min_stock'] = max(0, $minStock);
            }

            $lockedProduct->update($updateData);

            return $lockedProduct->fresh();
        });
    }
}
