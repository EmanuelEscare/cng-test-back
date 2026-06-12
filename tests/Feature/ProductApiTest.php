<?php

use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('products can be listed with active promotion data', function (): void {
    $product = Product::factory()->create([
        'name' => 'Featured Coffee',
        'sku' => 'FEATURED-001',
    ]);

    ProductPromotion::factory()
        ->for($product)
        ->create([
            'promotion' => 'Launch discount',
            'promotion_started_at' => now()->subDay(),
            'promotion_ends_at' => now()->addDay(),
        ]);

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.0.sku', 'FEATURED-001')
        ->assertJsonPath('data.0.has_active_promotion', true)
        ->assertJsonPath('data.0.active_promotion.promotion', 'Launch discount');
});

test('products can be filtered by active supplier relationship', function (): void {
    $supplier = Supplier::factory()->create();

    $activeProduct = Product::factory()->create([
        'name' => 'Active Product',
        'sku' => 'ACTIVE-001',
    ]);

    $inactiveProduct = Product::factory()->create([
        'name' => 'Inactive Product',
        'sku' => 'INACTIVE-001',
    ]);

    $activeProduct->suppliers()->attach($supplier->id, ['is_active' => true]);
    $inactiveProduct->suppliers()->attach($supplier->id, ['is_active' => false]);

    $this->getJson("/api/products?supplier_id={$supplier->id}&is_active=true")
        ->assertOk()
        ->assertJsonPath('data.0.sku', 'ACTIVE-001')
        ->assertJsonPath('data.0.suppliers.0.id', $supplier->id)
        ->assertJsonPath('data.0.suppliers.0.is_active_for_product', true)
        ->assertJsonMissingPath('data.1');
});

test('products can be filtered by supplier', function (): void {
    $selectedSupplier = Supplier::factory()->create(['name' => 'Selected Supplier']);
    $otherSupplier = Supplier::factory()->create(['name' => 'Other Supplier']);

    $selectedProduct = Product::factory()->create(['sku' => 'SELECTED-SUPPLIER-001']);
    $otherProduct = Product::factory()->create(['sku' => 'OTHER-SUPPLIER-001']);

    $selectedProduct->suppliers()->attach($selectedSupplier->id, ['is_active' => true]);
    $otherProduct->suppliers()->attach($otherSupplier->id, ['is_active' => true]);

    $this->getJson("/api/products?supplier_id={$selectedSupplier->id}")
        ->assertOk()
        ->assertJsonPath('data.0.sku', 'SELECTED-SUPPLIER-001')
        ->assertJsonMissingPath('data.1');
});

test('suppliers can be listed for product filters', function (): void {
    $supplier = Supplier::factory()->create(['name' => 'Filter Supplier']);
    $product = Product::factory()->create();

    $product->suppliers()->attach($supplier->id, ['is_active' => true]);

    $this->getJson('/api/suppliers?has_products=true')
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.0.id', $supplier->id)
        ->assertJsonPath('data.0.name', 'Filter Supplier')
        ->assertJsonPath('data.0.products_count', 1)
        ->assertJsonPath('data.0.active_products_count', 1);
});

test('a product can be created', function (): void {
    $payload = [
        'name' => 'API Product',
        'sku' => 'api-001',
        'description' => 'Created from the API.',
        'price' => 199.99,
        'currency' => 'mxn',
    ];

    $this->postJson('/api/products', $payload)
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'API Product')
        ->assertJsonPath('data.sku', 'API-001')
        ->assertJsonPath('data.currency', 'MXN');

    $this->assertDatabaseHas('products', [
        'sku' => 'API-001',
        'name' => 'API Product',
    ]);
});

test('a product uses the default currency when it is not provided', function (): void {
    $this->postJson('/api/products', [
        'name' => 'Default Currency Product',
        'sku' => 'DEFAULT-CURRENCY-001',
        'price' => 99.99,
    ])
        ->assertCreated()
        ->assertJsonPath('data.currency', 'MXN');
});

test('product creation validation errors use the api response format', function (): void {
    $this->postJson('/api/products', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('data', null)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'sku',
                'price',
            ],
        ]);
});

test('a product can be updated', function (): void {
    $product = Product::factory()->create([
        'sku' => 'OLD-001',
        'price' => 100,
    ]);

    $this->patchJson("/api/products/{$product->id}", [
        'name' => 'Updated Product',
        'price' => 250.50,
    ])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Updated Product')
        ->assertJsonPath('data.price', '250.50');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
    ]);
});

test('a product can be soft deleted', function (): void {
    $product = Product::factory()->create();

    $this->deleteJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data', null);

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});
