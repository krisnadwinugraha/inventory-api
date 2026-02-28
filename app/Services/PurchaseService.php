<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            // 1. Create purchase header
            $purchase = Purchase::create([
                'vendor_id'      => $data['vendor_id'],
                'invoice_number' => $data['invoice_number'],
                'purchase_date'  => $data['purchase_date'],
                'notes'          => $data['notes'] ?? null,
                'total'          => 0,
            ]);

            $total = 0;

            // 2. Process each item
            foreach ($data['items'] as $item) {

                // Lock the product row to prevent race conditions
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                $subtotal   = $item['qty'] * $item['price'];
                $qtyBefore  = $product->current_stock;
                $qtyAfter   = $qtyBefore + $item['qty'];

                // 3. Create purchase item
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $product->id,
                    'qty'         => $item['qty'],
                    'price'       => $item['price'],
                    'subtotal'    => $subtotal,
                ]);

                // 4. Update product stock
                $product->update(['current_stock' => $qtyAfter]);

                // 5. Record stock movement
                StockMovement::create([
                    'product_id'     => $product->id,
                    'reference_type' => 'purchase',
                    'reference_id'   => $purchaseItem->id,
                    'qty_change'     => $item['qty'],
                    'qty_before'     => $qtyBefore,
                    'qty_after'      => $qtyAfter,
                    'notes'          => 'Stock in from purchase ' . $purchase->invoice_number,
                ]);

                $total += $subtotal;
            }

            // 6. Update purchase total
            $purchase->update(['total' => $total]);

            return $purchase->load('items.product', 'vendor');
        });
    }
}
