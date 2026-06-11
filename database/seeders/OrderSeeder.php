<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Seed sample sales for products.
     */
    public function run(): void
    {
        $products = Product::query()
            ->whereIn('sku', [
                'CAF-CHI-500',
                'TER-INO-750',
                'EMP-BIO-MED',
                'CHO-ART-70',
                'KIT-GOU-001',
            ])
            ->get()
            ->keyBy('sku');

        $this->createOrder([
            ['sku' => 'CAF-CHI-500', 'sold_at' => now()->subDays(3), 'quantity' => 2],
            ['sku' => 'KIT-GOU-001', 'sold_at' => now()->subDays(5), 'quantity' => 1],
        ], $products);

        $this->createOrder([
            ['sku' => 'TER-INO-750', 'sold_at' => now()->subDays(20), 'quantity' => 1],
            ['sku' => 'CHO-ART-70', 'sold_at' => now()->subDays(25), 'quantity' => 4],
        ], $products);

        $this->createOrder([
            ['sku' => 'EMP-BIO-MED', 'sold_at' => now()->subDays(30), 'quantity' => 10],
        ], $products);
    }

    /**
     * Create an order and attach products with sale data.
     *
     * @param  array<int, array{sku: string, sold_at: \Illuminate\Support\Carbon, quantity: int}>  $items
     * @param  \Illuminate\Support\Collection<string, Product>  $products
     */
    private function createOrder(array $items, $products): void
    {
        $order = Order::create();
        $attach = [];

        foreach ($items as $item) {
            $product = $products->get($item['sku']);

            if (! $product instanceof Product) {
                continue;
            }

            $attach[$product->id] = [
                'sold_at' => $item['sold_at'],
                'quantity' => $item['quantity'],
            ];
        }

        $order->products()->syncWithoutDetaching($attach);
    }
}
