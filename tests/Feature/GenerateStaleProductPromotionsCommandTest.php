<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPromotion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the command generates a 20 percent promotion for products without sales in two weeks', function (): void {
    $recentProduct = Product::factory()->create(['sku' => 'RECENT-SALE-001']);
    $staleProduct = Product::factory()->create(['sku' => 'STALE-SALE-001']);

    $recentOrder = Order::create();
    $recentOrder->products()->attach($recentProduct->id, [
        'sold_at' => now()->subDays(3),
        'quantity' => 1,
    ]);

    $oldOrder = Order::create();
    $oldOrder->products()->attach($staleProduct->id, [
        'sold_at' => now()->subDays(20),
        'quantity' => 2,
    ]);

    $this->artisan('products:generate-stale-promotions')
        ->assertSuccessful();

    $this->assertDatabaseHas('product_promotions', [
        'product_id' => $staleProduct->id,
        'discount_percentage' => 20,
    ]);

    $this->assertDatabaseMissing('product_promotions', [
        'product_id' => $recentProduct->id,
        'discount_percentage' => 20,
    ]);
});

test('the command does not duplicate products with an active discount promotion', function (): void {
    $product = Product::factory()->create(['sku' => 'ALREADY-PROMOTED-001']);

    ProductPromotion::factory()
        ->for($product)
        ->create([
            'promotion_started_at' => now()->subDay(),
            'promotion_ends_at' => now()->addDay(),
            'discount_percentage' => 20,
        ]);

    $this->artisan('products:generate-stale-promotions')
        ->assertSuccessful();

    expect(ProductPromotion::query()->where('product_id', $product->id)->count())->toBe(1);
});
